<?php
/**
 * Event-Driven Ecommerce Sync Connector
 *
 * @author    Senior Developer
 * @copyright 2026 Ecommerce Sync Inc
 * @license   Commercial
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Ecommerce_sync_connector extends Module
{
    public function __construct()
    {
        $this->name = 'ecommerce_sync_connector';
        $this->tab = 'market_place';
        $this->version = '1.0.0';
        $this->author = 'Senior Developer';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Event-Driven Ecommerce Sync Connector');
        $this->description = $this->l('Syncs products and orders with Laravel Middleware via Webhooks.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('actionProductSave') &&
            $this->registerHook('actionUpdateQuantity') &&
            $this->registerHook('actionValidateOrder');
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            Configuration::deleteByName('ECOMMERCE_SYNC_API_URL') &&
            Configuration::deleteByName('ECOMMERCE_SYNC_API_TOKEN') &&
            Configuration::deleteByName('ECOMMERCE_SYNC_ROLE');
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $apiUrl = strval(Tools::getValue('ECOMMERCE_SYNC_API_URL'));
            $apiToken = strval(Tools::getValue('ECOMMERCE_SYNC_API_TOKEN'));
            $role = strval(Tools::getValue('ECOMMERCE_SYNC_ROLE'));

            if (
                !$apiUrl ||
                empty($apiUrl) ||
                !Validate::isUrl($apiUrl)
            ) {
                $output .= $this->displayError($this->l('Invalid API URL'));
            } else {
                Configuration::updateValue('ECOMMERCE_SYNC_API_URL', $apiUrl);
                Configuration::updateValue('ECOMMERCE_SYNC_API_TOKEN', $apiToken);
                Configuration::updateValue('ECOMMERCE_SYNC_ROLE', $role);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Middleware API URL'),
                    'name' => 'ECOMMERCE_SYNC_API_URL',
                    'size' => 20,
                    'required' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('API Token'),
                    'name' => 'ECOMMERCE_SYNC_API_TOKEN',
                    'size' => 20,
                    'required' => true
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Shop Role'),
                    'name' => 'ECOMMERCE_SYNC_ROLE',
                    'required' => true,
                    'options' => [
                        'query' => [
                            ['id' => 'SOURCE', 'name' => $this->l('Wholesaler (Source)')],
                            ['id' => 'CLIENT', 'name' => $this->l('Retailer (Client)')],
                        ],
                        'id' => 'id',
                        'name' => 'name'
                    ]
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;

        // Load current values
        $helper->fields_value['ECOMMERCE_SYNC_API_URL'] = Configuration::get('ECOMMERCE_SYNC_API_URL');
        $helper->fields_value['ECOMMERCE_SYNC_API_TOKEN'] = Configuration::get('ECOMMERCE_SYNC_API_TOKEN');
        $helper->fields_value['ECOMMERCE_SYNC_ROLE'] = Configuration::get('ECOMMERCE_SYNC_ROLE');

        return $helper->generateForm($fieldsForm);
    }

    /**
     * HOOK: Product Saved or Updated
     * Triggered only if Role is SOURCE
     */
    public function hookActionProductSave($params)
    {
        if (Configuration::get('ECOMMERCE_SYNC_ROLE') !== 'SOURCE') {
            return;
        }

        // Avoid recursion if needed, or check if it's a real update
        $id_product = (int) $params['id_product'];
        $product = new Product($id_product, false, $this->context->language->id);

        if (!Validate::isLoadedObject($product)) {
            return;
        }

        $this->sendWebhook('/sync/product', [
            'id' => $product->id,
            'reference' => $product->reference,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => Product::getQuantity($product->id),
            'active' => $product->active
        ]);
    }

    /**
     * HOOK: Quantity Updated
     * Triggered only if Role is SOURCE
     */
    public function hookActionUpdateQuantity($params)
    {
        if (Configuration::get('ECOMMERCE_SYNC_ROLE') !== 'SOURCE') {
            return;
        }

        $id_product = (int) $params['id_product'];
        $product = new Product($id_product, false, $this->context->language->id);

        if (!Validate::isLoadedObject($product)) {
            return;
        }

        $this->sendWebhook('/sync/product', [
            'id' => $product->id,
            'reference' => $product->reference,
            'name' => $product->name, // Potentially cached or unnecessary if only syncing stock
            'price' => $product->price,
            'stock' => (int) $params['quantity'],
            'active' => $product->active
        ]);
    }

    /**
     * HOOK: New Order Created
     * Triggered only if Role is CLIENT
     */
    public function hookActionValidateOrder($params)
    {
        if (Configuration::get('ECOMMERCE_SYNC_ROLE') !== 'CLIENT') {
            return;
        }

        $order = $params['order'];
        $customer = $params['customer'];

        // Prepare items
        $products = $order->getProducts();
        $items = [];
        foreach ($products as $prod) {
            $items[] = [
                'reference' => $prod['product_reference'],
                'quantity' => (int) $prod['product_quantity'],
                'price' => (float) $prod['product_price']
            ];
        }

        $this->sendWebhook('/sync/order', [
            'id' => $order->id,
            'reference' => $order->reference,
            'customer_email' => $customer->email,
            'total' => (float) $order->total_paid,
            'status' => 'paid', // Or map current state
            'items' => $items
        ]);
    }

    /**
     * Helper to send POST request
     */
    protected function sendWebhook($endpoint, $data)
    {
        $url = Configuration::get('ECOMMERCE_SYNC_API_URL') . '/api' . $endpoint;
        $token = Configuration::get('ECOMMERCE_SYNC_API_TOKEN');

        // Use cURL for compatibility
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-API-KEY: ' . $token
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Timeout is crucial to avoid blocking PrestaShop
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

        // Async trick: verify SSL off for dev local
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);

        // Log errors if meaningful logging is implemented
        if (curl_errno($ch)) {
            PrestaShopLogger::addLog('Ecommerce Sync Connector Error: ' . curl_error($ch), 3);
        }

        curl_close($ch);
    }
}
