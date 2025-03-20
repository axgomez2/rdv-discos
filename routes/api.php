<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// MercadoPago Webhook
Route::post('/mercadopago/webhook', function (Request $request) {
    Log::info('MercadoPago Webhook recebido', $request->all());
    
    $mercadoPagoService = app(App\Services\MercadoPagoService::class);
    $result = $mercadoPagoService->processNotification($request->all());
    
    return response()->json(['success' => $result]);
})->middleware('api');
