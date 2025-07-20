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
