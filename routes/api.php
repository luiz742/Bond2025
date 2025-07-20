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

// Lista todos os usuários
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

// Lista clientes de um usuário (por ID)
Route::get('/users/{id}/clients', function ($id) {
    $clients = Client::select('id', 'name')
        ->where('user_id', $id)
        ->get();
    return response()->json($clients);
});

// Lista todos os clientes (para buscar clientId pelo nome)
Route::get('/clients', function () {
    return response()->json(Client::select('id', 'name', 'user_id')->get());
});

// Lista familiares de um cliente (spouse, filhos, etc.)
Route::get('/clients/{id}/family', function ($id) {
    $client = Client::with('familyMembers')->find($id);

    if (!$client) {
        return response()->json([], 200);
    }

    return response()->json(
        $client->familyMembers->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'type' => $m->type, // spouse, child_1, etc.
            ];
        })
    );
});

// Lista documentos do cliente com status dos arquivos
Route::get('/clients/{id}/documents', function ($id) {
    $client = Client::with(['service.documents', 'files'])->find($id);

    if (!$client) {
        return response()->json(['error' => 'Client not found'], 404);
    }

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
