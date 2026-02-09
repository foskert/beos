<?php

namespace App\Http\Controllers\Api\V1\Products\Prices;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Products\Prices\StoreRequest;
use App\Http\Resources\Api\V1\Products\Prices\PriceResource;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class StoreController extends Controller
{
    #[OA\Post(
        path: "/api/v1/products/{id}/prices",
        summary: "Asignar un nuevo precio a un producto",
        tags: ["Productos"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID del producto al que se le añade el precio",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["price", "product_id", "currency_id"],
                properties: [
                    new OA\Property(property: "price", type: "number", format: "float", example: 1250.00),
                    new OA\Property(property: "product_id", type: "integer", example: 1),
                    new OA\Property(property: "currency_id", type: "integer", example: 2)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Precio creado correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Precio asignado con éxito."),
                        new OA\Property(property: "value", ref: "#/components/schemas/PriceIndexResponse")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Error de validación"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function __invoke(StoreRequest $request, $id):JsonResponse
    {
        return DB::transaction(function () use ($request, $id):JsonResponse {

            try {
                $price   = ProductPrice::create($request->validated());
                $product = Product::with( 'getCurrency', 'getPrices.currency')
                    ->where('id', $id)
                    ->first();
                $resource = new PriceResource($product);
                return response()->json([
                    'message' => __('price.store.message'),
                    'value'   => $resource,
                ], Response::HTTP_CREATED);
            }  catch (\Exception $ex) {
                Log::error( $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
                return response()->json([
                    'message' =>  __('price.store.errors'),
                    'value'   =>  $ex->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    }

}
