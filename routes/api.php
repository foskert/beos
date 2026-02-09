<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Products;
use App\Http\Controllers\Api\V1\Audits;
use App\Http\Controllers\Api\V1\Users;
use Symfony\Component\HttpFoundation\Response;



Route::prefix('v1')->group(function () {
    Route::post('login', Users\UserController::class);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', function (Request $request) {
            $user = $request->user();

            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
                return response()->json(['message' => __('validation.token')], Response::HTTP_ACCEPTED);
            }else{
                return response()->json(['message' => __('validation.401')], Response::HTTP_UNAUTHORIZED);
            }
        });
    });
    Route::prefix('products')->group(function () {
        Route::get('/',          Products\IndexController::class);
        Route::post('/',         Products\StoreController::class);
        Route::get('/{id}',      Products\ShowController::class);
        Route::put('/{id}',      Products\UpdateController::class);
        Route::delete('/{id}',   Products\DestroyController::class);

        Route::get('/{id}/prices',  Products\Prices\IndexController::class);
        Route::post('/{id}/prices', Products\Prices\StoreController::class);
    });
    Route::prefix('audits')->group(function () {
        Route::get('/products/{id}', Audits\IndexController::class);
    });
    Route::fallback(function () {
        return response()->json(['message' => __('validation.404')], Response::HTTP_NOT_FOUND);
    });
});
