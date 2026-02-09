<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Products\DestroyRequest;
use App\Http\Resources\Api\V1\Products\ProductResource;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

class DestroyController extends Controller
{
    #[OA\Delete(
        path: "/api/v1/products/{id}",
        summary: "Eliminar un producto",
        tags: ["Productos"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID del producto a eliminar",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Producto eliminado correctamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Producto eliminado con éxito."),
                        new OA\Property(property: "value", ref: "#/components/schemas/ProductResource")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Producto no encontrado o ya eliminado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "No se encontró el producto para eliminar."),
                        new OA\Property(property: "value", type: "null")
                    ]
                )
            ),
            new OA\Response(response: 500, description: "Error interno del servidor")
        ]
    )]
    public function __invoke(DestroyRequest $request, $id):JsonResponse
    {
        return DB::transaction(function () use ($request, $id):JsonResponse {

            try {
                $product = Product::with('getCurrency')->findOrFail($id);
                $resource = new ProductResource($product);
                $product->destroy($id);
                return response()->json([
                    'message' => __('product.destroy.message'),
                    'value'   => $resource,
                ], Response::HTTP_OK);
            }  catch (\Exception $ex) {
                Log::error( $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
                return response()->json([
                    'message' =>  __('product.destroy.errors'),
                    'value'   =>  $ex->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    }
}
