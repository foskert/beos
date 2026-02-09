<?php

namespace App\Http\Controllers\Api\V1\Audits;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Audits\IndexRequest;
use App\Http\Resources\Api\V1\Audits\AuditResource;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class IndexController extends Controller
{
    #[OA\Get(
        path: "/api/v1/audits/products/{id}",
        summary: "Consultar historial de auditoría de un producto",
        tags: ["Productos"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID del producto (incluye eliminados)",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Historial de cambios recuperado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Auditoría consultada."),
                        new OA\Property(
                            property: "value",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/AuditResource")
                        )
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Producto no encontrado"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function __invoke(IndexRequest $request, int $id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            $audits = $product->audits()->with('user')->get();
            return response()->json([
                'message' => __('validation.audit.message'),
                'value'   => AuditResource::collection($audits),
            ]);
        }  catch (\Exception $ex) {
            Log::error( $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return response()->json([
                'message' =>  __('validation.audit.errors'),
                'value'   =>  $ex->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
