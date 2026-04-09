<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CryptoController;

Route::prefix('v1')->group(function () {
    Route::post('/encrypt', [CryptoController::class, 'encrypt']);
    Route::post('/decrypt', [CryptoController::class, 'decrypt']);
    Route::get('/me',       [CryptoController::class, 'me']);
});