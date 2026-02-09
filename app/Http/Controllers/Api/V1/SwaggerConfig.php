<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "BEOS API",
    version: "1.0.0",
    description: "Documentación oficial de la API BEOS"
)]
#[OA\Server(url: "http://localhost:8000", description: "Servidor Local")]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Pega el token directamente (ej: 4|lGMHH...). Swagger añadirá el prefijo Bearer por ti."
)]

#[OA\Schema(
    schema: "ProductResource",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Producto A"),
        new OA\Property(property: "price", type: "string", example: "100.00")
    ]
)]
#[OA\Schema(
    schema: "PriceIndexResponse",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Producto X"),
        new OA\Property(property: "description", type: "string", nullable: true),
        new OA\Property(property: "price", type: "string", example: "120.50"),
        new OA\Property(property: "currency", type: "object", nullable: true, properties: [
            new OA\Property(property: "name", type: "string"),
            new OA\Property(property: "symbol", type: "string"),
            new OA\Property(property: "exchange_rate", type: "string")
        ]),
        new OA\Property(
            property: "prices",
            type: "array",
            items: new OA\Items(
                properties: [
                    new OA\Property(property: "price", type: "string"),
                    new OA\Property(property: "currency", type: "object", nullable: true)
                ]
            )
        ),
        new OA\Property(property: "manufacturing_cost", type: "string"),
        new OA\Property(property: "tax_cost", type: "string"),
        new OA\Property(property: "created_at", type: "string"),
        new OA\Property(property: "updated_at", type: "string")
    ]
)]
#[OA\Schema(
    schema: "ProductListResponse",
    properties: [
        new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/ProductResource")),
        new OA\Property(property: "links", type: "object"),
        new OA\Property(property: "meta", ref: "#/components/schemas/PaginationMeta")
    ]
)]
#[OA\Schema(
    schema: "PaginationMeta",
    properties: [
        new OA\Property(property: "current_page", type: "integer"),
        new OA\Property(property: "last_page", type: "integer"),
        new OA\Property(property: "total", type: "integer")
    ]
)]

#[OA\Schema(schema: "AuditResource", properties: [ new OA\Property(property: "id", type: "integer") ])]

class SwaggerConfig {}
