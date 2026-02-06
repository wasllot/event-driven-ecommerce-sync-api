<?php

namespace App\Services\PrestaShop;

use App\Interfaces\ECommerceProviderInterface;
use Exception;

class PrestaShopFactory
{
    public function make(string $connectionName): ECommerceProviderInterface
    {
        $config = config("services.prestashop.{$connectionName}");

        if (!$config) {
            throw new Exception("PrestaShop connection [{$connectionName}] not configured.");
        }

        if (empty($config['url']) || empty($config['key'])) {
            throw new Exception("PrestaShop credentials missing for [{$connectionName}].");
        }

        return new PrestaShopService($config['url'], $config['key']);
    }
}
