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

// Busca usuários por nome (query param: name)
Route::get('/users/search', function (Request $request) {
    $name = $request->query('name', '');
    return response()->json(
        User::select('id', 'name', 'email')
            ->where('name', 'like', "%{$name}%")
            ->get()
    );
});

// Lista clientes vinculados a um usuário (id)
Route::get('/users/{id}/clients', function ($id) {
    $clients = Client::select('id', 'name')
        ->where('user_id', $id)
        ->get();
    return response()->json($clients);
});

// Lista documentos do cliente com status do arquivo (uploaded, approved, not uploaded)
Route::get('/clients/{id}/documents', function ($id) {
    // Busca o cliente com service e os documentos do serviço e arquivos enviados
    $client = Client::with(['service.documents', 'files'])->find($id);

    if (!$client) {
        return response()->json(['error' => 'Client not found'], 404);
    }

    // Para cada documento do serviço, tenta associar o arquivo enviado pelo cliente
    $documents = $client->service->documents->map(function ($doc) use ($client) {
        // Busca o arquivo do cliente para esse documento (pode ser null)
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
