<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'privateshop/classes/PrivateShopAdmin.php');
require_once(_PS_MODULE_DIR_.'privateshop/classes/PrivateShopCustomer.php');

class PrivateShop extends Module {

    public $tabs = [
        [
            'name' => 'Private Shop',
            'class_name' => 'AdminClientManagement',
            'parent_class_name' => 'AdminParentCustomer',
        ],
    ];

    public function __construct() {
        $this->name = 'privateshop';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Oscar Periche';
        $this->need_instance = 0;
        
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Private Shop');
        $this->description = $this->l('Controla el acceso a la tienda para permitir únicamente la navegación y compra de clientes previamente aprobados por el administrador. Además, ofrece opciones adicionales de restricción, como la posibilidad de habilitar o deshabilitar la opción de envío a domicilio para cada cliente, según las necesidades específicas de la tienda.');
    }
    
    public function install() {
        if (!parent::install() || !$this->installTab()) {
            return false;
        }
        
        if (!Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'privateshop_customers` (
                `customer_id` INT UNSIGNED NOT NULL,
                `dni` CHAR(20) NULL,
                `is_approved` TINYINT(1) NOT NULL DEFAULT 0,
                `approved_at` DATETIME NULL,
                `shipping_restriction` TINYINT(1) NOT NULL DEFAULT 0,
                `employee_code` CHAR(60) NULL,
                PRIMARY KEY (`customer_id`),
                CONSTRAINT `fk_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `' . _DB_PREFIX_ . 'customer`(`id_customer`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ')) {
            $this->_errors[] = $this->l('Error creating privateshop_customers table.');
            return false;
        }

        $adminEmail1 = Configuration::get('PS_SHOP_EMAIL');
        Configuration::updateValue('PRIVATE_SHOP_ADMIN_EMAIL_1', $adminEmail1);

        Configuration::updateValue('PS_CUSTOMER_CREATION_EMAIL', 0);

        Configuration::updateValue('PRIVATE_SHOP_CARRIER_ID', 0);

        if (!$this->registerHook('actionCustomerAccountAdd')) {
            return false;
        }

        if (!$this->registerHook('actionObjectCustomerDeleteAfter')) {
            return false;
        }

        if (!$this->registerHook('actionFrontControllerSetMedia')) {
            return false;
        }
        
        if (!$this->registerHook('displayCustomerLoginFormAfter')) {
            return false;
        }

        if (!$this->registerHook('displayBeforeCarrier')) {
            return false;
        }

        if (!$this->registerHook('createAccountForm')) {
            return false;
        }

        return true;
    }
    
    public function uninstall() {
        if (!parent::uninstall() || !$this->uninstallTab()) {
            return false;
        }

        Configuration::updateValue('PS_CUSTOMER_CREATION_EMAIL', 1);
        
        $themeModulesDir = _PS_THEME_DIR_ . 'modules/privateshop';

        return true;
    } 

    public function enable($force_all = false) {
        return parent::enable($force_all)
            && $this->installTab()
        ;
    }

    public function disable($force_all = false) {
        return parent::disable($force_all)
            && $this->uninstallTab()
        ;
    }

    public function installTab() {
        $tabId = (int) Tab::getIdFromClassName('AdminClientManagementController');
    
        if (!$tabId) {
            $tab = new Tab();
        } else {
            $tab = new Tab($tabId);
        }

        $tab->active = 1;
        $tab->class_name = 'AdminClientManagementController'; 
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('Private Shop', array(), 'Modules.PrivateShop.Admin', $lang['locale']);
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentCustomers'); 
        $tab->module = $this->name;

        return $tab->save();

    }  

    public function uninstallTab() {
        $tabId = (int) Tab::getIdFromClassName('AdminClientManagementController');

        if ($tabId) {
            $tab = new Tab($tabId);
            $tab->delete();
        }

        return true;

    }

    public function getContent() {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $adminEmail1 = (string)Tools::getValue('PRIVATE_SHOP_ADMIN_EMAIL_1');
            $adminEmail2 = (string)Tools::getValue('PRIVATE_SHOP_ADMIN_EMAIL_2');
            $adminEmail3 = (string)Tools::getValue('PRIVATE_SHOP_ADMIN_EMAIL_3');
    
            if (empty($adminEmail1) && empty($adminEmail2) && empty($adminEmail3)) {
                $adminEmail1 = Configuration::get('PS_SHOP_EMAIL');
                Configuration::updateValue('PRIVATE_SHOP_ADMIN_EMAIL_1', $adminEmail1);
                Configuration::updateValue('PRIVATE_SHOP_ADMIN_EMAIL_2', '');
                Configuration::updateValue('PRIVATE_SHOP_ADMIN_EMAIL_3', '');
            } else {
                Configuration::updateValue('PRIVATE_SHOP_ADMIN_EMAIL_1', $adminEmail1);
                Configuration::updateValue('PRIVATE_SHOP_ADMIN_EMAIL_2', $adminEmail2);
                Configuration::updateValue('PRIVATE_SHOP_ADMIN_EMAIL_3', $adminEmail3);
                $output = $this->displayConfirmation($this->l('Los correos electrónicos de notificación se han actualizado correctamente.'));
            }
        }        

        if (Tools::isSubmit('PRIVATE_SHOP_CARRIER_ID') && Tools::getValue('PRIVATE_SHOP_CARRIER_ID') !== null) {
            $carrierId = (int)Tools::getValue('PRIVATE_SHOP_CARRIER_ID');
            Configuration::updateValue('PRIVATE_SHOP_CARRIER_ID', $carrierId);
            $output = $this->displayConfirmation($this->l('El transportista se ha actualizado correctamente.'));
        }
    
        $output .= $this->displayForm();
        $output .= $this->displayCarrierRestrictionForm();
        return $output;
    }
    
    public function displayForm() {
        $adminEmail1 = Configuration::get('PRIVATE_SHOP_ADMIN_EMAIL_1');
        $adminEmail2 = Configuration::get('PRIVATE_SHOP_ADMIN_EMAIL_2');
        $adminEmail3 = Configuration::get('PRIVATE_SHOP_ADMIN_EMAIL_3');
        
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Notificaciones de nuevos usuarios pendientes de validar'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Correo 1'),
                        'name' => 'PRIVATE_SHOP_ADMIN_EMAIL_1',
                        'desc' => $this->l('Correo 1'),
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Correo 2'),
                        'name' => 'PRIVATE_SHOP_ADMIN_EMAIL_2',
                        'desc' => $this->l('Correo 2'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Correo 3'),
                        'name' => 'PRIVATE_SHOP_ADMIN_EMAIL_3',
                        'desc' => $this->l('Correo 3'),
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Guardar'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];
    
        $helper = new HelperForm();
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;
    
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        $helper->fields_value['PRIVATE_SHOP_ADMIN_EMAIL_1'] = Tools::getValue('PRIVATE_SHOP_ADMIN_EMAIL_1', Configuration::get('PRIVATE_SHOP_ADMIN_EMAIL_1'));
        $helper->fields_value['PRIVATE_SHOP_ADMIN_EMAIL_2'] = Tools::getValue('PRIVATE_SHOP_ADMIN_EMAIL_2', Configuration::get('PRIVATE_SHOP_ADMIN_EMAIL_2'));
        $helper->fields_value['PRIVATE_SHOP_ADMIN_EMAIL_3'] = Tools::getValue('PRIVATE_SHOP_ADMIN_EMAIL_3', Configuration::get('PRIVATE_SHOP_ADMIN_EMAIL_3'));

        return $helper->generateForm([$form]);
    }     
    
    public function displayCarrierRestrictionForm() {
        $carrierId = Configuration::get('PRIVATE_SHOP_CARRIER_ID');
        
        $carriers = Carrier::getCarriers(
            (int)Configuration::get('PS_LANG_DEFAULT'),
            true,
            false,
            false,
            null,
            Carrier::ALL_CARRIERS
        );

        $carrierOptions = [];
        $carrierOptions[] = [
            'id_option' => 0,
            'name' => "Seleccionar Transportista..."
        ];
        foreach ($carriers as $carrier) {
            $carrierOptions[] = [
                'id_option' => $carrier['id_carrier'],
                'name' => $carrier['name']
            ];
        }

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Seleccionar Transportista para Envío a Domicilio'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->l('Este transportista no se mostrará si el cliente no tiene habilitada la opción de envío a domicilio.'),
                        'name' => 'PRIVATE_SHOP_CARRIER_ID',
                        'options' => [
                            'query' => $carrierOptions,
                            'id' => 'id_option',
                            'name' => 'name'
                        ],
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Guardar'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        $helper->fields_value['PRIVATE_SHOP_CARRIER_ID'] = Tools::getValue('PRIVATE_SHOP_CARRIER_ID', $carrierId);

        return $helper->generateForm([$form]);
    }
    
    /*
    // Hooks
    */

    public function hookActionCustomerAccountAdd($params) {
        // Honeypot validation
        $timestamp = Tools::getValue('ts');
        $textField = Tools::getValue('website');
        $checkbox = Tools::getValue('agree');

        // Validate: if the text field or checkbox is filled, or if it's too fast (< 2 seconds)
        if (!empty($textField) || !empty($checkbox) || (time() - $timestamp) < 2) {
            // Delete the customer since it's already created
            $customer = $params['newCustomer'];
            Db::getInstance()->delete('customer', 'id_customer = ' . (int)$customer->id);
            $this->context->controller->errors[] = $this->l('Se ha producido un error inesperado. Por favor, inténtalo de nuevo más tarde.');
            return false; // Bloquejar
        }

        $customer = $params['newCustomer'];
        $dni = Tools::getValue('dni');
        
        Db::getInstance()->insert('privateshop_customers', [
            'customer_id' => (int)$customer->id,
            'dni' => pSQL($dni),
            'is_approved' => 0,
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        $adminEmails = array_filter([
            Configuration::get('PRIVATE_SHOP_ADMIN_EMAIL_1'),
            Configuration::get('PRIVATE_SHOP_ADMIN_EMAIL_2'),
            Configuration::get('PRIVATE_SHOP_ADMIN_EMAIL_3'),
        ]);
    
        if (!empty($adminEmails)) {
            PrivateShopAdmin::sendAdminNotification($customer, $adminEmails, $this->context);
        }

        PrivateShopCustomer::sendCustomerNotification($customer, $this->context);
    }

    public function hookActionObjectCustomerDeleteAfter($params) {
        $customer = $params['object'];

        if (isset($customer->id)) {
            Db::getInstance()->delete('privateshop_customers', 'customer_id = ' . (int)$customer->id);
        }
    }
    
    public function hookActionFrontControllerSetMedia($hookParams) {
        $controller = Tools::getValue('controller');
    
        if (in_array($controller, ['authentication', 'registration', 'password', 'cms', 'order-confirmation', 'validation', 'redsys', 'securepayment', 'processpayment', 'processpaymentref', 'securepaymentv2'])) {
            return;
        }
    
        if ($this->context->customer->isLogged()) {
            $customer = new PrivateShopCustomer($this->context->customer->id);
            if (!$customer->getIsApproved()) {
                $this->context->customer->logout();
                Tools::redirect('index.php?controller=authentication&approval_needed=1');
            }
        } else {
            Tools::redirect('index.php?controller=authentication');
        }
    }
        
    public function hookDisplayCustomerLoginFormAfter($params) {
        if (Tools::getValue('approval_needed') == 1) {
            return '
                <div class="alert alert-warning not_approved">
                    Hemos recibido tu solicitud de registro y pronto será revisada. Para acceder a tu cuenta, necesitas la validación de un administrador. Una vez aprobada, recibirás un correo electrónico de confirmación.
                </div>
                <style>
                    .page-authentication #content {
                        background-color: transparent !important;
                        margin-left: 0 !important;
                        padding:0 !important;
                    }

                    .not_approved {
                        font-size: 1rem;
                        line-height: 1.5em;
                    }
                </style>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var loginForm = document.querySelector(".login-form");
                        var noAccount = document.querySelector(".no-account");
                        var hrElement = document.querySelector("#content hr");
                        var topMenu = document.querySelector(".top-menu");
                        var searchBar = document.querySelector("#search_widget");

                        if (loginForm) loginForm.style.display = "none";
                        if (noAccount) noAccount.style.display = "none";
                        if (hrElement) hrElement.style.display = "none";
                        if (topMenu) topMenu.style.display = "none";
                        if (searchBar) searchBar.style.display = "none";
                    });
                </script>
            ';
        }
        if (!$this->context->customer->isLogged()) {
            return '
                <script>
                    var topMenu = document.querySelector(".top-menu");
                    var searchBar = document.querySelector("#search_widget");
                    if (topMenu) topMenu.style.display = "none";
                    if (searchBar) searchBar.style.display = "none";
                </script>
            ';
        }
        return '';
    }

    public function hookCreateAccountForm($params) {
        $timestamp = time();
        $honeypotHtml = '
            <input type="text" name="website" style="display:none !important;" tabindex="-1" autocomplete="off">
            <input type="checkbox" name="agree" style="display:none !important;" tabindex="-1">
            <input type="hidden" name="ts" value="' . $timestamp . '">
        ';
        return '
            <div class="form-group row">
                <label class="form-control-label col-md-3 required" for="dni">' . $this->l('DNI') . '</label>
                <div class="col-md-6 js-input-column">
                    <input type="text" class="form-control" name="dni" id="dni" value="' . Tools::getValue('dni') . '" required>
                    <span class="form-control-comment">
                        Por favor, asegúrate de que el DNI sea correcto, ya que será utilizado para validar tu registro .
                    </span>
                </div>
                <div class="col-md-3 form-control-comment"></div>
            </div>
            ' . $honeypotHtml . '
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                var dniField = document.querySelector(".form-group input[name=\'dni\']").closest(".form-group");
                var genderField = document.querySelector(".form-group input[name=\'id_gender\']").closest(".form-group");
                
                if (dniField && genderField) {
                genderField.parentNode.insertBefore(dniField, genderField.nextSibling);
                }
            });
            </script>
        ';
    }
}
