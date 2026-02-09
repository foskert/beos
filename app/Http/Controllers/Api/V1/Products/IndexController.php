<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Products\IndexRequest;
use App\Http\Resources\Api\V1\Products\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;


class IndexController extends Controller
{
    #[OA\Get(
        path: "/api/v1/products",
        summary: "Listado paginado de productos con filtros",
        tags: ["Productos"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "search", in: "query", description: "Buscar por nombre", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "currency_id", in: "query", description: "Filtrar por ID de moneda", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "min_price", in: "query", schema: new OA\Schema(type: "number")),
            new OA\Parameter(name: "max_price", in: "query", schema: new OA\Schema(type: "number")),
            new OA\Parameter(name: "paginate", in: "query", description: "Resultados por página", schema: new OA\Schema(type: "integer", default: 15)),
            new OA\Parameter(name: "sort_by", in: "query", description: "Campo de ordenación", schema: new OA\Schema(type: "string", enum: ["id", "name", "price", "created_at"])),
            new OA\Parameter(name: "order_by", in: "query", description: "Dirección", schema: new OA\Schema(type: "string", enum: ["asc", "desc"]))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Listado de productos recuperado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Productos listados con éxito."),
                        new OA\Property(property: "value", ref: "#/components/schemas/ProductListResponse")
                    ]
                )
            ),
            new OA\Response(response: 500, description: "Error en el servidor")
        ]
    )]
    public function __invoke(IndexRequest $request): JsonResponse
    {
        try {
            $products = Product::filter($request)
                ->with(
                    'getCurrency'
                )
                ->orderBy(
                    (! empty($request->sortBy)?$request->sortBy:config('api.defaults.sort_by')),
                 (! empty($request->orderBy)?$request->orderBy:config('api.defaults.order_by'))
                 )
                ->paginate($request->filled('paginate') ? $request->paginate : config('api.defaults.paginate'));
            $resource = ProductResource::collection($products);
            return response()->json([
                'message' => __('product.index.message'),
                'value'   => $resource->response()->getData(true),
            ], Response::HTTP_OK);
        } catch (\Exception $ex) {
            Log::error( $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return response()->json([
                'message' =>  __('product.index.errors'),
                'value'   =>  $ex->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
