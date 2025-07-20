<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Client;

Route::get('/users', function (Request $request) {
    $name = $request->query('name', '');
    $query = User::select('id', 'name', 'email');
    if ($name) {
        $query->where('name', 'like', "%{$name}%");
    }
    return response()->json($query->get());
});

// Lista clientes de um usuário (busca por nome do usuário ou id)
Route::get('/users/{identifier}/clients', function ($identifier) {
    // Se identifier for numérico, assume id, senão tenta buscar usuário pelo nome
    if (is_numeric($identifier)) {
        $user = User::find($identifier);
    } else {
        $user = User::where('name', 'like', "%{$identifier}%")->first();
    }
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    $clients = Client::select('id', 'name')
        ->where('user_id', $user->id)
        ->get();

    return response()->json([
        'user_id' => $user->id,
        'user_name' => $user->name,
        'clients' => $clients,
    ]);
});

// Lista todos os clientes, com filtro opcional por nome (?name=)
Route::get('/clients', function (Request $request) {
    $name = $request->query('name', '');
    $query = Client::select('id', 'name', 'user_id');
    if ($name) {
        $query->where('name', 'like', "%{$name}%");
    }
    return response()->json($query->get());
});

// Lista familiares de um cliente (por id)
Route::get('/clients/{id}/family', function ($id) {
    $client = Client::with('familyMembers')->find($id);
    if (!$client) {
        return response()->json([], 200);
    }
    return response()->json(
        $client->familyMembers->map(fn($m) => [
            'id' => $m->id,
            'name' => $m->name,
            'type' => $m->type,
        ])
    );
});

// Lista documentos do cliente, incluindo familiares
Route::get('/clients/{id}/documents', function ($id) {
    $client = Client::with(['service.documents', 'files', 'familyMembers'])->find($id);

    if (!$client) {
        return response()->json(['error' => 'Client not found'], 404);
    }

    $familyMembers = $client->familyMembers->map(fn($m) => [
        'id' => $m->id,
        'name' => $m->name,
        'type' => $m->type,
    ]);

    $documents = $client->service->documents->map(function ($doc) use ($client, $familyMembers) {
        $ownerName = $client->name;
        if ($doc->client_type && $doc->client_type !== 'main') {
            $member = $familyMembers->firstWhere('type', $doc->client_type);
            if ($member) {
                $ownerName = $member->name;
            } else {
                return null; // Ignorar documentos de membros inexistentes
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
