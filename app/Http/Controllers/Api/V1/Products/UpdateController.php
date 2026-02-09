<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Products\UpdateRequest;
use App\Http\Resources\Api\V1\Products\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class UpdateController extends Controller
{
    #[OA\Put(
        path: "/api/v1/products/{id}",
        summary: "Actualizar un producto existente",
        tags: ["Productos"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID del producto a actualizar",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", maxLength: 255, example: "Producto Editado"),
                    new OA\Property(property: "price", type: "number", format: "float", example: 199.99),
                    new OA\Property(property: "tax_cost", type: "number", format: "float", example: 20.00),
                    new OA\Property(property: "currency_id", type: "integer", example: 1),
                    new OA\Property(property: "manufacturing_cost", type: "number", format: "float", example: 100.00),
                    new OA\Property(property: "description", type: "string", nullable: true, example: "Nueva descripción")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Producto actualizado con éxito",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Producto actualizado correctamente."),
                        new OA\Property(property: "value", type: "object", properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "name", type: "string", example: "Producto Editado"),
                            new OA\Property(property: "description", type: "string", example: "Nueva descripción"),
                            new OA\Property(property: "price", type: "string", example: "199.99"),
                            new OA\Property(property: "currency", type: "object", properties: [
                                new OA\Property(property: "name", type: "string", example: "Dólar"),
                                new OA\Property(property: "symbol", type: "string", example: "USD"),
                                new OA\Property(property: "exchange_rate", type: "string", example: "1.00")
                            ]),
                            new OA\Property(property: "manufacturing_cost", type: "string", example: "100.00"),
                            new OA\Property(property: "tax_cost", type: "string", example: "20.00"),
                            new OA\Property(property: "created_at", type: "string", example: "2024-03-20"),
                            new OA\Property(property: "updated_at", type: "string", example: "2024-03-20")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Producto no encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "El producto no existe."),
                        new OA\Property(property: "value", type: "null")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Datos de validación inválidos"),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function __invoke(UpdateRequest $request, $id)  :JsonResponse
    {
        return DB::transaction(function () use ($request, $id):JsonResponse {
            try {
                $product = Product::with('getCurrency')->find($id);
                if (!$product) {
                    return response()->json([
                        'message' => __('product.update.not_found'),
                        'value'   => $product
                    ], Response::HTTP_NOT_FOUND);
                }

                $product->update($request->validated());
                return response()->json([
                    'message' => __('product.update.message'),
                    'value'   => new ProductResource($product),
                ], Response::HTTP_OK);

            } catch (\Throwable $ex) {
                Log::error( $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
                return response()->json([
                    'message' =>  __('product.update.errors'),
                    'value'   =>  $ex->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    }
}
