<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplicationController;

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

Route::prefix('applications')->name('applications.')->group(function () {
    Route::get('/', [ApplicationController::class, 'index'])->name('index');
    Route::get('/create', [ApplicationController::class, 'create'])->name('create');
    Route::post('/', [ApplicationController::class, 'store'])->name('store');
    Route::get('/{application}', [ApplicationController::class, 'show'])->name('show');
    Route::delete('/{application}', [ApplicationController::class, 'destroy'])->name('destroy');
});





});

// Redirection page d'accueil
Route::get('/', function () {
    return redirect()->route('login');
});