<?php

class AdminEcommerceSyncMigrationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        $apiUrl = Configuration::get('ECOMMERCE_SYNC_API_URL');
        $apiToken = Configuration::get('ECOMMERCE_SYNC_API_TOKEN');

        if (!$apiUrl || !$apiToken) {
            $this->errors[] = $this->l('API URL and Token must be configured in the module settings.');
            return;
        }

        $this->context->smarty->assign([
            'api_url' => $apiUrl,
            'api_token' => $apiToken,
        ]);

        $this->setTemplate('migration.tpl');
    }
}
