<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Products\StoreRequest;
use App\Http\Resources\Api\V1\Products\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class StoreController extends Controller
{
    #[OA\Post(
        path: "/api/v1/products",
        summary: "Crear un nuevo producto",
        tags: ["Productos"],
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "price", "tax_cost", "currency_id", "manufacturing_cost"],
                properties: [
                    new OA\Property(property: "name", type: "string", maxLength: 255, example: "Producto Premium"),
                    new OA\Property(property: "price", type: "number", format: "float", minimum: 0, example: 150.50),
                    new OA\Property(property: "tax_cost", type: "number", format: "float", minimum: 0, example: 15.00),
                    new OA\Property(property: "currency_id", type: "integer", description: "ID de la moneda existente", example: 1),
                    new OA\Property(property: "manufacturing_cost", type: "number", format: "float", minimum: 0, example: 80.00),
                    new OA\Property(property: "description", type: "string", nullable: true, example: "Descripción detallada del producto")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Producto creado con éxito",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Producto guardado correctamente."),
                        new OA\Property(property: "value", type: "object", properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "name", type: "string", example: "Producto Premium"),
                            new OA\Property(property: "description", type: "string", example: "Descripción..."),
                            new OA\Property(property: "price", type: "string", example: "150.50"),
                            new OA\Property(property: "currency", type: "object", properties: [
                                new OA\Property(property: "name", type: "string", example: "Dólar"),
                                new OA\Property(property: "symbol", type: "string", example: "USD"),
                                new OA\Property(property: "exchange_rate", type: "string", example: "1.00")
                            ]),
                            new OA\Property(property: "manufacturing_cost", type: "string", example: "80.00"),
                            new OA\Property(property: "tax_cost", type: "string", example: "15.00"),
                            new OA\Property(property: "created_at", type: "string", example: "2024-03-20"),
                            new OA\Property(property: "updated_at", type: "string", example: "2024-03-20")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación (Ej. Nombre duplicado o campos faltantes)"
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Hubo un error al procesar la solicitud."),
                        new OA\Property(property: "value", type: "string", example: "Mensaje de la excepción...")
                    ]
                )
            )
        ]
    )]
    public function __invoke(StoreRequest $request):JsonResponse
    {
        return DB::transaction(function () use ($request):JsonResponse {
            try {
                $product = Product::create($request->validated());
                $product->load('getCurrency');
                $resource = new ProductResource($product);
                return response()->json([
                    'message' => __('product.store.message'),
                    'value'   => $resource,
                ], Response::HTTP_CREATED);
            }  catch (\Exception $ex) {
                Log::error("Error en StoreController: " . $ex->getMessage(), [
                    'request' => $request->all(),
                    'trace'   => $ex->getTraceAsString()
                ]);
                return response()->json([
                    'message' =>  __('product.store.errors'),
                    'value'   =>  $ex->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    }
}
