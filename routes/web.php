<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\EncryptionController;   // ← Ajout important
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\MasterKeyController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\KeyController;
use App\Http\Controllers\LogController;

// Routes publiques
Route::get('/', function () {
    return redirect()->route('login');
});

// Inscription
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Connexion
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);



// Déconnexion
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Routes protégées
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');

    // ==================== ROUTES API ====================
    Route::prefix('api')->group(function () {
        Route::post('/encrypt', [EncryptionController::class, 'encrypt']);
        Route::post('/decrypt', [EncryptionController::class, 'decrypt']);
        Route::post('/decrypt/plain', [EncryptionController::class, 'decryptPlain']);
    });



    // Applications
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
    Route::delete('/applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::post('/applications/{application}/suspend', [ApplicationController::class, 'suspend'])->name('applications.suspend');
    Route::post('/applications/{application}/activate', [ApplicationController::class, 'activate'])->name('applications.activate');

    // API Keys
    Route::get('/keys', [KeyController::class, 'index'])->name('keys.index');
    Route::post('/keys', [KeyController::class, 'store'])->name('keys.store');
    Route::post('/keys/{apiKey}/revoke', [KeyController::class, 'revoke'])->name('keys.revoke');

    // Logs
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');



});

Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',         [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/keys',     [MasterKeyController::class, 'index'])->name('keys.index');
    Route::post('/keys/rotate', [MasterKeyController::class, 'rotate'])->name('keys.rotate');
    Route::get('/keys/status',  [MasterKeyController::class, 'status'])->name('keys.status');
    Route::get('/tenants',          [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
    Route::post('/tenants/{tenant}/suspend',  [TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::post('/tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
    Route::get('/plans',              [PlanController::class, 'index'])->name('plans.index');
    Route::put('/plans/{plan}',       [PlanController::class, 'update'])->name('plans.update');
});