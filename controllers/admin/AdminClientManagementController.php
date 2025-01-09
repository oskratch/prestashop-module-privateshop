<?php

require_once(_PS_MODULE_DIR_.'privateshop/classes/PrivateShopCustomer.php');

class AdminClientManagementController extends ModuleAdminController {

    public function __construct() {
        $this->table = 'privateshop_customers';
        $this->className = 'PrivateshopCustomer';
        $this->lang = false;
        $this->identifier = 'customer_id';
        $this->list_no_link = true;
        $this->bootstrap = true;

        parent::__construct();
    }

    public function initContent() {
        parent::initContent();

        $this->getClientManagement();
    }

    public function postProcess() {

        if (Tools::isSubmit('approve') && Tools::getValue('id_customer')) {
            $id_customer = (int)Tools::getValue('id_customer');
    
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'privateshop_customers 
                    SET is_approved = 1 
                    WHERE customer_id = ' . $id_customer;
    
            Db::getInstance()->execute($sql);
    
            $customer = new Customer($id_customer);
    
            $privateShopCustomer = new PrivateShopCustomer($id_customer);
    
            $privateShopCustomer->sendApprovalNotification($customer, $this->context);

            die(json_encode(['success' => true]));
        }
    
        if (Tools::isSubmit('delete') && Tools::getValue('id_customer')) {
            $id_customer = (int)Tools::getValue('id_customer');
    
            $deleted = Db::getInstance()->delete('customer', 'id_customer = ' . $id_customer);
    
            if ($deleted) {
                die(json_encode(['success' => true]));
            } else {
                die(json_encode(['success' => false]));
            }
        }      
     
        if (Tools::isSubmit('employecode') && Tools::getValue('id_customer') && Tools::getValue('employee_code') !== null) {
            $idCustomer = (int)Tools::getValue('id_customer');
            $employeeCode = Tools::getValue('employee_code');
        
            if (!$idCustomer || !Validate::isUnsignedId($idCustomer)) {
                $response = array('success' => false, 'message' => 'ID de cliente no válido.');
                echo json_encode($response);
                exit;
            }
        
            $result = Db::getInstance()->update(
                'privateshop_customers',
                array('employee_code' => $employeeCode),
                'customer_id = ' . (int)$idCustomer
            );
        
            if ($result) {
                $response = array('success' => true);
            } else {
                $response = array('success' => false, 'message' => 'Error al actualizar la base de datos.');
            }
        
            echo json_encode($response);
            exit;
        }
     
        if (Tools::isSubmit('shipping') && Tools::getValue('id_customer') && Tools::getValue('shipping_restriction') !== null) {
            $idCustomer = (int)Tools::getValue('id_customer');
            $shippingRestriction = (int)Tools::getValue('shipping_restriction');
        
            if (!$idCustomer || !Validate::isUnsignedId($idCustomer)) {
                $response = array('success' => false, 'message' => 'ID de cliente no válido.');
                echo json_encode($response);
                exit;
            }
        
            if ($shippingRestriction !== 0 && $shippingRestriction !== 1) {
                $response = array('success' => false, 'message' => 'Valor de restricción de envío no válido.');
                echo json_encode($response);
                exit;
            }
        
            $result = Db::getInstance()->update(
                'privateshop_customers',
                array('shipping_restriction' => $shippingRestriction),
                'customer_id = ' . (int)$idCustomer
            );
        
            if ($result) {
                $response = array('success' => true);
            } else {
                $response = array('success' => false, 'message' => 'Error al actualizar la base de datos.');
            }
        
            echo json_encode($response);
            exit;
        }
    }    

    private function getClientManagement() {
        $sql = 'SELECT p.customer_id, c.firstname, c.lastname, c.email, p.is_approved, p.approved_at
                FROM ' . _DB_PREFIX_ . 'privateshop_customers p
                JOIN ' . _DB_PREFIX_ . 'customer c ON p.customer_id = c.id_customer
                WHERE p.is_approved = 0';

        $customers = Db::getInstance()->executeS($sql);

        $sql_all_customers = 'SELECT p.customer_id, c.firstname, c.lastname, c.email, p.is_approved, p.approved_at, p.shipping_restriction, p.employee_code
                FROM ' . _DB_PREFIX_ . 'privateshop_customers p
                JOIN ' . _DB_PREFIX_ . 'customer c ON p.customer_id = c.id_customer
                WHERE p.is_approved = 1';

        $all_customers = Db::getInstance()->executeS($sql_all_customers);

        $this->context->smarty->assign(array(
            'customers' => $customers,
            'all_customers' => $all_customers,
            'module_dir' => $this->module->getPathUri(),
            '_token' => Tools::getAdminTokenLite('AdminCustomers')
        ));
        
        $this->setTemplate('client_management.tpl');
    }

}