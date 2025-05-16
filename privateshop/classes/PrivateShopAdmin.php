<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class PrivateShopAdmin {    

    public static function sendAdminNotification($customer, $adminEmails, $context) {

        foreach ($adminEmails as $adminEmail) {
            if($adminEmail != ""){
                $templateVars = [
                    '{firstname}' => $customer->firstname,
                    '{lastname}' => $customer->lastname,
                    '{email}' => $customer->email,
                    '{id_customer}' => $customer->id,
                    //'{link}' => $context->link->getBaseLink() . 'admin/index.php?controller=AdminClientManagement&token=' . Tools::getAdminTokenLite('AdminClientManagement'. (int)Tab::getIdFromClassName('AdminClientManagement') . (int)$context->employee->id)
                ];

                Mail::Send(
                    (int)(Configuration::get('PS_LANG_DEFAULT')),
                    'admin_customer_registration',
                    'Nuevo registro de cliente',
                    $templateVars,
                    $adminEmail,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    _PS_MODULE_DIR_ . 'privateshop/mails'
                );
            }
        }
    }
    
}