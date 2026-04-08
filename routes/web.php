<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\MasterKeyController;
use App\Http\Controllers\ActivityController;


// Routes publiques
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Routes protégées
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Applications
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [ApplicationController::class, 'index'])->name('index');
        Route::get('/create', [ApplicationController::class, 'create'])->name('create');
        Route::post('/', [ApplicationController::class, 'store'])->name('store');
        Route::get('/{application}', [ApplicationController::class, 'show'])->name('show');
        Route::delete('/{application}', [ApplicationController::class, 'destroy'])->name('destroy');
    });

    // API Keys
    Route::prefix('api-keys')->name('apikeys.')->group(function () {
        Route::post('/generate/{application}', [ApiKeyController::class, 'generate'])->name('generate');
        Route::patch('/revoke/{apiKey}', [ApiKeyController::class, 'revoke'])->name('revoke');
    });

    Route::prefix('master-keys')->name('masterkeys.')->group(function () {
    Route::get('/', [MasterKeyController::class, 'index'])->name('index');
    Route::post('/rotate', [MasterKeyController::class, 'rotate'])->name('rotate');
});


Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');

});

// Redirection page d'accueil
Route::get('/', function () {
    return redirect()->route('login');
});