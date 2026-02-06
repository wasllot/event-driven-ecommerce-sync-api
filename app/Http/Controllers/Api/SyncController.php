<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\DTOs\ProductData;
use App\Jobs\ProcessProductSync;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    /**
     * Handle incoming product sync webhook.
     *
     * @OA\Post(
     *      path="/api/sync/product",
     *      operationId="syncProduct",
     *      tags={"Product Sync"},
     *      summary="Queue product synchronization",
     *      description="Receives product data from Source PrestaShop and queues it for sync to Client.",
     *      security={{"api_key_security":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Product Data Payload",
     *          @OA\JsonContent(
     *              required={"id", "reference", "price"},
     *              @OA\Property(property="id", type="integer", example=101),
     *              @OA\Property(property="name", type="string", example="Camiseta Cotton"),
     *              @OA\Property(property="reference", type="string", example="TSHIRT-001"),
     *              @OA\Property(property="price", type="number", format="float", example=19.99),
     *              @OA\Property(property="stock", type="integer", example=50),
     *              @OA\Property(property="active", type="boolean", example=true)
     *          )
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Accepted",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product sync queued successfully."),
     *              @OA\Property(property="reference", type="string", example="TSHIRT-001")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Unprocessable Entity")
     * )
     */
    public function syncProduct(Request $request): JsonResponse
    {
        // Hydrate DTO from request data
        // In a real app, validation should happen before this (e.g., FormRequest)
        $productData = ProductData::fromArray($request->all());

        // Dispatch the job to the queue
        ProcessProductSync::dispatch($productData);

        return response()->json([
            'message' => 'Product sync queued successfully.',
            'reference' => $productData->reference,
        ], 202);
    }
}
