<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\File;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $clients = Client::where('service_id', $user->service_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients,
        ]);
    }

    public function show(Client $client)
    {
        $user = Auth::user();

        // Verifica se o usuário tem permissão para acessar o client pelo serviço permitido
        if ($user->service_allowed && $client->service_id !== $user->service_allowed) {
            abort(403, 'Access denied');
        }

        // Carrega os relacionamentos necessários para o show
        $client->load(['service.documents', 'files']);

        return Inertia::render('Admin/Clients/Show', [
            'client' => $client,
            'user' => $user, // pode ser útil para controle na view admin
        ]);
    }

    public function updateDocumentStatus(Request $request, File $file)
    {
        // dd("Chegou no update status para o arquivo: {$file->id}, status novo: {$request->status}");

        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $file->status = $request->status;
        $file->save();

        return redirect()->back()->with('success', 'Document status updated.');
    }
}
