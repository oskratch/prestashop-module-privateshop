<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class PrivateShopCustomer {
    private $customer_id;
    private $is_approved;
    private $approved_at;

    public function __construct($customer_id) {
        $this->customer_id = $customer_id;
        $this->loadCustomerData();
    }
        
    public static function sendCustomerNotification($customer, $context) {
        $templateVars = [
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{email}' => $customer->email,
            '{id_customer}' => $customer->id,
            //'{link}' => $context->link->getPageLink('my-account', true)
        ];
            
        Mail::Send(
            (int)(Configuration::get('PS_LANG_DEFAULT')),
            'customer_registration', 
            'Registro pendiente de validación',
            $templateVars,
            $customer->email,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            _PS_MODULE_DIR_ . 'privateshop/mails'
        );
    }

    private function loadCustomerData() {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'privateshop_customers` WHERE `customer_id` = ' . (int)$this->customer_id;
        $result = Db::getInstance()->getRow($sql);

        if ($result) {
            $this->is_approved = (int)$result['is_approved'];
            $this->approved_at = $result['approved_at'] ? $result['approved_at'] : null;
        } else {
            // Si no existeix el client a la taula, inicialitzem com a no aprovat
            $this->is_approved = 0;
            $this->approved_at = null;
        }
    }
        
    public static function sendApprovalNotification($customer, $context) {
        $templateVars = [
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{email}' => $customer->email,
            '{id_customer}' => $customer->id,
            '{link}' => $context->link->getPageLink('authentication', true)
        ];
            
        Mail::Send(
            (int)(Configuration::get('PS_LANG_DEFAULT')),
            'customer_approved', 
            'Bienvenido a nuestra tienda, tu cuenta ha sido aprobada',
            $templateVars,
            $customer->email,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            _PS_MODULE_DIR_ . 'privateshop/mails'
        );
    }

    // Getter per obtenir l'estat d'aprovació
    public function getIsApproved() {
        return $this->is_approved;
    }

    // Getter per obtenir la data d'aprovació
    public function getApprovedAt() {
        return $this->approved_at;
    }
}
