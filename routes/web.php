<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
// User
use App\Http\Controllers\KycController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
// Service
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\DocumentController;
// Admin
use App\Http\Controllers\Admin\UserController;
// File
use App\Http\Controllers\ClientFileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::get('/kyc/create', [KycController::class, 'create'])->name('kyc.create');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');

    // Clients
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');

    // File Upload
    Route::post('/clients/upload-documents', [ClientFileController::class, 'store'])->name('client.upload-documents');

});


Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // User Management
        Route::get('/subagents', [UserController::class, 'index'])->name('subagents.index');
        Route::get('/users', [UserController::class, 'admin'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

        // Clients
        Route::get('/users/{user}/clients', [ClientController::class, 'index'])->name('users.clients.index');
        Route::put('/files/{file}/status', [AdminClientController::class, 'updateDocumentStatus'])->name('update-document-status');

        // Serviços
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
        Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

        // Documentos
        Route::get('/services/{service}/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/services/{service}/documents/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/services/{service}/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
        Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

        // Aqui você pode adicionar rotas extras no futuro:
         Route::get('/clients', [AdminClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{client}', [AdminClientController::class, 'show'])->name('clients.show');
    });



