<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Users\UserRequest;
use App\Http\Resources\Api\V1\Users\UserResource;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Post(
        path: "/api/v1/login",
        summary: "Inicio de sesión de usuario",
        description: "Retorna el token Bearer para autenticación",
        tags: ["Autenticación"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@admin.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "1234567890")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Login exitoso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Autenticación exitosa"),
                        new OA\Property(property: "access_token", type: "string", example: "1|3v9M..."),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                        new OA\Property(property: "user", type: "object", properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "name", type: "string", example: "Admin"),
                            new OA\Property(property: "email", type: "string", example: "admin@beos.com")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Credenciales inválidas"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function __invoke(UserRequest $request)
    {
        return DB::transaction(function () use ($request):JsonResponse {
            try {
                $user = User::where('email', $request->email)->first();
                if (!$user || !Hash::check($request->password, $user->password)) {
                    throw ValidationException::withMessages([
                        'email' => [__('auth.failed')],
                    ]);
                }

                $resource = new UserResource($user);
                return response()->json([
                    'message' => __('user.login.susses'),
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
