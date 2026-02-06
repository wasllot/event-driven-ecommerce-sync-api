<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Event-Driven E-commerce Sync API",
 *      description="API for synchronizing products and orders between PrestaShop instances via Laravel Horizon.",
 *      @OA\Contact(
 *          email="admin@ecommerce-sync.local"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      ),
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="api_key_security",
 *     type="apiKey",
 *     in="header",
 *     name="X-API-KEY",
 *     description="Enter your API Key."
 * )
 */
class Controller
{
    //
}
