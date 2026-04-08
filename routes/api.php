<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EncryptionController;

Route::post('/encrypt', [EncryptionController::class, 'encrypt']);
Route::post('/decrypt', [EncryptionController::class, 'decrypt']);