<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Products\ShowRequest;
use App\Http\Resources\Api\V1\Products\ProductResource;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShowController extends Controller
{
    #[OA\Get(
        path: "/api/v1/products/{id}",
        summary: "Obtener detalles de un producto",
        tags: ["Productos"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID del producto a consultar",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detalles del producto",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Producto consultado correctamente."),
                        new OA\Property(property: "value", ref: "#/components/schemas/ProductResource")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Producto no encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "El producto solicitado no existe."),
                        new OA\Property(property: "value", type: "null")
                    ]
                )
            ),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function __invoke(ShowRequest $request, $id): JsonResponse
    {
        try {
            $products = Product::with('getCurrency')->findOrFail($id);
            $resource = new ProductResource($products);
            return response()->json([
                'message' => __('product.show.message'),
                'value'   => $resource,
            ], Response::HTTP_OK);
        } catch (\Exception $ex) {
            Log::error( $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return response()->json([
                'message' =>  __('product.show.errors'),
                'value'   =>  $ex->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
