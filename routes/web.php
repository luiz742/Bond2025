<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Client;
use App\Models\User;
use App\Models\Invoice;

use App\Http\Controllers\DashboardController;

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
// Invoices
use App\Http\Controllers\InvoiceController;
// Notifications
use App\Http\Controllers\NotificationController;
// Family Members
use App\Http\Controllers\FamilyMemberController;
// PDF Vanuatu Documents Download
use App\Http\Controllers\PdfDownloadController;


Route::get('/', function () {
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    if ($user->role === 'admin') {
        return redirect()->route('admin.myclients');
    }

    if ($user->role === 'b2b') {
        return redirect()->route('clients.index');
    }

    if ($user->role === 'super_admin') {
        return redirect()->route('admin.users.index');
    }

    // default
    return redirect()->route('profile.show');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
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
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');

    // Family Members
    Route::post('/family-members', [FamilyMemberController::class, 'store'])->name('family-members.store');


    // File Upload
    Route::post('/clients/upload-documents', [ClientFileController::class, 'store'])->name('client.upload-documents');

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{id}/printable', [InvoiceController::class, 'printable'])->name('invoices.printable');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::post('/invoice-services', [InvoiceController::class, 'storeInvoiceService'])->name('invoice-services.store');
    Route::put('/invoice-services/{id}', [InvoiceController::class, 'updateInvoiceService'])->name('invoice-services.update');


    // PDF Downloads
    Route::get('/pdfs', [PdfDownloadController::class, 'index'])->name('pdfs.index');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/redirect', [NotificationController::class, 'redirectAndMarkRead'])->name('notifications.redirect');

    Route::post('/files/reupload/{id}', [ClientFileController::class, 'reupload'])->name('files.reupload');
    // Route::put('/clients/{client}/documents/{file}', [ClientFileController::class, 'update'])->name('client.update-document');
    Route::match(['put', 'post'], '/clients/{client}/documents/{id}', [ClientFileController::class, 'update'])->name('client.update-document');
    Route::put('/admin/family-members/{familyMember}', [ClientController::class, 'updateFamilyMember'])
        ->name('family-members.update');


});


Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // User Management
        Route::get('/subagents', [UserController::class, 'index'])->name('subagents.index');
        Route::get('/users', [UserController::class, 'admin'])->name('users.index');
        Route::post('/users/{user}/upload-agreement', [UserController::class, 'uploadAgreement'])->name('users.uploadAgreement');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::get('/subagents/create', [UserController::class, 'create'])->name('subagents.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::delete('/users/client/{client}', [UserController::class, 'clientdestroy'])->name('users.client.destroy');


        // Clients
        Route::get('/users/{user}/clients', [ClientController::class, 'index'])->name('users.clients.index');
        // Route::put('/files/{file}/status', [AdminClientController::class, 'updateDocumentStatus'])->name('update-document-status');
        Route::post('files/{id}/status', [AdminClientController::class, 'status'])->name('files.status.update');

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
        Route::put('/services/{service}/documents/{name}/{type}', [DocumentController::class, 'updateGrouped'])->name('documents.updateGrouped');


        // Aqui você pode adicionar rotas extras no futuro:
        Route::get('/clients', [AdminClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{clients}', [AdminClientController::class, 'show'])->name('clients.show');
        Route::get('/clients/create', [AdminClientController::class, 'create'])->name('clients.create');
        Route::get('/clients/new/create', [AdminClientController::class, 'admincreate'])->name('clients.admincreate');
        Route::post('clients/new/store', [AdminClientController::class, 'adminstore'])->name('adminclients.store');

        Route::get('/clients/user/{user}/create', [AdminClientController::class, 'clientuser'])->name('clients.user.create');
        Route::post('/clients/user/{user}', [AdminClientController::class, 'clientuserstore'])->name('clients.user.store');
        Route::post('/clients', [AdminClientController::class, 'store'])->name('clients.store');
        Route::delete('/clients/{client}', [AdminClientController::class, 'destroy'])->name('clients.destroy');
        Route::get('/clients/{client}/edit', [AdminClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [AdminClientController::class, 'update'])->name('clients.update');

        // My Clients
        Route::get('/my/clients', [AdminClientController::class, 'myclients'])->name('myclients');
        Route::get('/my/clients/create', [AdminClientController::class, 'mycreate'])->name('myclients.create');
        Route::post('/myclients', [AdminClientController::class, 'mystore'])->name('myclients.store');
        Route::delete('/clients/{client}', [AdminClientController::class, 'mydestroy'])->name('clients.mydestroy');

        Route::put('/admin/family-members/{familyMember}', [AdminClientController::class, 'updateFamilyMember'])
            ->name('family-members.update');
    });



