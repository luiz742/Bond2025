<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Client;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Lista todos os usuários (id, name, email)
Route::get('/users', function () {
    return response()->json(User::select('id', 'name', 'email')->get());
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

// Lista todos os clientes (para busca geral)
Route::get('/clients', function () {
    return response()->json(Client::select('id', 'name', 'user_id')->get());
});

// Busca cliente por nome (novo)
Route::get('/clients/search', function (Request $request) {
    $name = $request->query('name', '');
    return response()->json(
        Client::select('id', 'name', 'user_id')
            ->where('name', 'like', "%{$name}%")
            ->get()
    );
});

// Lista clientes de um usuário (por user_id)
Route::get('/users/{id}/clients', function ($id) {
    $clients = Client::select('id', 'name')
        ->where('user_id', $id)
        ->get();
    return response()->json($clients);
});

// Lista documentos do cliente
Route::get('/clients/{id}/documents', function ($id) {
    $client = Client::with(['service.documents', 'files'])->find($id);

    if (!$client) {
        return response()->json(['error' => 'Client not found'], 404);
    }

    // Retorna lista vazia se não houver service/documents
    $documents = collect();
    if ($client->service && $client->service->documents) {
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
    }

    return response()->json([
        'client_id' => $client->id,
        'client_name' => $client->name,
        'documents' => $documents,
    ]);
});
