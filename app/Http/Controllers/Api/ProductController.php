<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Actions\Product\ListProductsAction;
use App\Actions\Product\MigrateProductAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * List products available for migration from Source.
     *
     * @OA\Get(
     *      path="/api/products",
     *      operationId="listProducts",
     *      tags={"Product Migration"},
     *      summary="List products from Source",
     *      security={{"api_key_security":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="List of products",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ProductData"))
     *      )
     * )
     */
    public function index(Request $request, ListProductsAction $listAction): JsonResponse
    {
        $products = $listAction->execute($request->all());
        return response()->json($products);
    }

    /**
     * Trigger migration for specific products.
     *
     * @OA\Post(
     *      path="/api/products/migrate",
     *      operationId="migrateProducts",
     *      tags={"Product Migration"},
     *      summary="Migrate selected products",
     *      security={{"api_key_security":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"ids"},
     *              @OA\Property(property="ids", type="array", @OA\Items(type="integer"))
     *          )
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Migration queued",
     *          @OA\JsonContent(@OA\Property(property="message", type="string"))
     *      )
     * )
     */
    public function migrate(Request $request, MigrateProductAction $migrateAction): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $migrateAction->execute($request->input('ids'));

        return response()->json(['message' => 'Product migration queued successfully.'], 202);
    }
}
