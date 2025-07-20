<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Client;

// Route::get('/users', function (Request $request) {
//     $name = $request->query('name', '');
//     $query = User::select('id', 'name', 'email');
//     if ($name) {
//         $query->where('name', 'like', "%{$name}%");
//     }
//     return response()->json($query->get());
// });

// Route::get('/users/{id}/clients', function ($id) {
//     $clients = Client::select('id', 'name')
//         ->where('user_id', $id)
//         ->get();
//     return response()->json($clients);
// });

// // Lista todos os clientes, com filtro opcional por nome
// Route::get('/clients', function (Request $request) {
//     $name = $request->query('name', '');
//     $query = Client::select('id', 'name', 'user_id');
//     if ($name) {
//         $query->where('name', 'like', "%{$name}%");
//     }
//     return response()->json($query->get());
// });

// Route::get('/clients/{id}/family', function ($id) {
//     $client = Client::with('familyMembers')->find($id);

//     if (!$client) {
//         return response()->json([], 200);
//     }

//     return response()->json(
//         $client->familyMembers->map(function ($m) {
//             return [
//                 'id' => $m->id,
//                 'name' => $m->name,
//                 'type' => $m->type,
//             ];
//         })
//     );
// });

// Route::get('/clients/{id}/documents', function ($id) {
//     $client = Client::with(['service.documents', 'files', 'familyMembers'])->find($id);

//     if (!$client) {
//         return response()->json(['error' => 'Client not found'], 404);
//     }

//     $familyMembers = $client->familyMembers->map(function ($member) {
//         return [
//             'id' => $member->id,
//             'name' => $member->name,
//             'type' => $member->type,
//         ];
//     });

//     $documents = $client->service->documents->map(function ($doc) use ($client, $familyMembers) {
//         $ownerName = $client->name;
//         if ($doc->client_type && $doc->client_type !== 'main') {
//             $member = $familyMembers->firstWhere('type', $doc->client_type);
//             if ($member) {
//                 $ownerName = $member->name;
//             } else {
//                 return null;
//             }
//         }

//         $file = $client->files->firstWhere('document_id', $doc->id);

//         return [
//             'document_id' => $doc->id,
//             'document_name' => $doc->name,
//             'owner_name' => $ownerName,
//             'file_status' => $file ? $file->status : 'not uploaded',
//             'file_path' => $file ? $file->path : null,
//         ];
//     })->filter();

//     return response()->json([
//         'client_id' => $client->id,
//         'client_name' => $client->name,
//         'family_members' => $familyMembers,
//         'documents' => $documents->values(),
//     ]);
// });
