<?php

namespace App\Http\Controllers\Api\V1\Products\Prices;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Products\Prices\IndexRequest;
use App\Http\Resources\Api\V1\Products\Prices\PriceResource;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class IndexController extends Controller
{
    #[OA\Get(
        path: "/api/v1/products/{id}/prices",
        summary: "Listado de precios por moneda de un producto",
        tags: ["Productos"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID del producto",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Precios recuperados",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Precios consultados con éxito."),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/PriceIndexResponse")
                        ),
                        new OA\Property(property: "links", type: "object"),
                        new OA\Property(property: "meta", ref: "#/components/schemas/PaginationMeta")
                    ]
                )
            ),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function __invoke(IndexRequest $request, $id):JsonResponse
    {
         try {
            $products = Product::with( 'getCurrency', 'getPrices.currency')
                ->where('id', $id)
                ->get();

                $resource = PriceResource::collection($products);
            return response()->json([
                'message' => __('price.index.message'),
                'value'   => $resource->response()->getData(true),
            ], Response::HTTP_OK);
        } catch (\Exception $ex) {
            Log::error( $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return response()->json([
                'message' =>  __('price.index.errors'),
                'value'   =>  $ex->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
