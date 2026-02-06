<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\DTOs\OrderData;
use App\Jobs\ProcessOrderReplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Handle incoming order replication webhook.
     *
     * @OA\Post(
     *      path="/api/sync/order",
     *      operationId="replicateOrder",
     *      tags={"Order Replication"},
     *      summary="Queue order replication",
     *      description="Receives order data from Client PrestaShop and queues it for replication to Source.",
     *      security={{"api_key_security":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Order Data Payload",
     *          @OA\JsonContent(
     *              required={"id", "reference", "customer_email", "total"},
     *              @OA\Property(property="id", type="integer", example=500),
     *              @OA\Property(property="reference", type="string", example="ORD-XJ90"),
     *              @OA\Property(property="customer_email", type="string", format="email", example="customer@example.com"),
     *              @OA\Property(property="total", type="number", format="float", example=150.50),
     *              @OA\Property(property="status", type="string", example="paid"),
     *              @OA\Property(property="shipping_address", type="object",
     *                  @OA\Property(property="firstname", type="string"),
     *                  @OA\Property(property="lastname", type="string"),
     *                  @OA\Property(property="address1", type="string"),
     *                  @OA\Property(property="city", type="string"),
     *                  @OA\Property(property="postcode", type="string"),
     *                  @OA\Property(property="country", type="string"),
     *                  @OA\Property(property="phone", type="string")
     *              ),
     *              @OA\Property(property="billing_address", type="object", ref="#/components/schemas/Address"),
     *              @OA\Property(property="carrier_id", type="integer", example=2),
     *              @OA\Property(property="module", type="string", example="bankwire"),
     *              @OA\Property(property="currency", type="string", example="EUR"),
     *              @OA\Property(
     *                  property="items", 
     *                  type="array", 
     *                  @OA\Items(type="string", example="Item object")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Accepted",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Order replication queued successfully."),
     *              @OA\Property(property="reference", type="string", example="ORD-XJ90")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Unprocessable Entity")
     * )
     */
    public function replicateOrder(Request $request): JsonResponse
    {
        // Hydrate DTO from request data
        $orderData = OrderData::fromArray($request->all());

        // Dispatch the job to the queue
        ProcessOrderReplication::dispatch($orderData);

        return response()->json([
            'message' => 'Order replication queued successfully.',
            'reference' => $orderData->reference,
        ], 202);
    }
}
