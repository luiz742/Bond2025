<?php

// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Client;

// Lista todos os usuários
Route::get('/users', function () {
    return response()->json(User::select('id', 'name', 'email')->get());
});

Route::get('/clients', function () {
    return response()->json(Client::select('id', 'name', 'user_id')->get());
});

Route::get('/clients/{id}/documents', function ($id) {
    $client = Client::with(['service.documents', 'files'])->find($id);

    if (!$client) {
        return response()->json(['error' => 'Client not found'], 404);
    }

    // Mapeia documentos com status do arquivo (ou 'not uploaded' se não existir)
    $documents = $client->service->documents->map(function ($doc) use ($client) {
        $file = $client->files->firstWhere('document_id', $doc->id);
        return [
            'document_id' => $doc->id,
            'document_name' => $doc->name,
            'client_type' => $doc->client_type,
            'file_status' => $file ? $file->status : 'not uploaded',
            'file_path' => $file ? $file->path : null,
        ];
    });

    return response()->json([
        'client_id' => $client->id,
        'client_name' => $client->name,
        'documents' => $documents,
    ]);
});

// Busca usuários por nome
Route::get('/users/search', function (Request $request) {
    $name = $request->query('name', '');
    return response()->json(
        User::select('id', 'name', 'email')
            ->where('name', 'like', "%{$name}%")
            ->get()
    );
});

// Lista clientes de um usuário (por ID)
Route::get('/users/{id}/clients', function ($id) {
    $clients = Client::select('id', 'name')
        ->where('user_id', $id)
        ->get();
    return response()->json($clients);
});
