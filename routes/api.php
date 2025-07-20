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

// Busca usuários por nome (parâmetro ?name=)
Route::get('/users/search', function (Request $request) {
    $name = $request->query('name', '');
    return response()->json(
        User::select('id', 'name', 'email')
            ->where('name', 'like', "%{$name}%")
            ->get()
    );
});

// Lista todos os clientes (com user_id para vinculação)
Route::get('/clients', function () {
    return response()->json(Client::select('id', 'name', 'user_id')->get());
});

// Lista clientes de um usuário por ID
Route::get('/users/{id}/clients', function ($id) {
    $clients = Client::select('id', 'name', 'user_id')
        ->where('user_id', $id)
        ->get();
    return response()->json($clients);
});

// Lista membros da família de um cliente
Route::get('/clients/{id}/family', function ($id) {
    $client = Client::with('familyMembers')->find($id);
    if (!$client) {
        return response()->json([]);
    }
    return response()->json(
        $client->familyMembers->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'type' => $m->type, // spouse, child, etc.
            ];
        })
    );
});

// Lista documentos do cliente e familiares (com dono real)
Route::get('/clients/{id}/documents', function ($id) {
    $client = Client::with(['service.documents', 'files', 'familyMembers'])->find($id);

    if (!$client) {
        return response()->json(['error' => 'Client not found'], 404);
    }

    $familyMembers = $client->familyMembers->map(function ($member) {
        return [
            'id' => $member->id,
            'name' => $member->name,
            'type' => $member->type,
        ];
    });

    $documents = $client->service->documents->map(function ($doc) use ($client, $familyMembers) {
        $ownerName = $client->name;

        if ($doc->client_type && $doc->client_type !== 'main') {
            $member = $familyMembers->firstWhere('type', $doc->client_type);
            if ($member) {
                $ownerName = $member->name;
            } else {
                return null;
            }
        }

        $file = $client->files->firstWhere('document_id', $doc->id);

        return [
            'document_id' => $doc->id,
            'document_name' => $doc->name,
            'owner_name' => $ownerName,
            'file_status' => $file ? $file->status : 'not uploaded',
            'file_path' => $file ? $file->path : null,
        ];
    })->filter();

    return response()->json([
        'client_id' => $client->id,
        'client_name' => $client->name,
        'family_members' => $familyMembers,
        'documents' => $documents->values(),
    ]);
});
