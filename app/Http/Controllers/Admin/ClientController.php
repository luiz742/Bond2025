<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\File;
use App\Models\Service;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $clients = Client::where('service_id', $user->service_id)
            ->orderBy('created_at', 'desc')
            ->with(['service', 'files', 'user'])
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

    public function create()
    {
        $user = auth()->user();
        $services = Service::where('id', $user->service_id)
            ->with('documents')
            ->get();
        return Inertia::render('Admin/Clients/Create', [
            'services' => $services,
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
        ]);

        $validated['user_id'] = $user->id;

        Client::create($validated);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client created successfully.');
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

    // My Clients
    public function myclients()
    {
        $user = Auth::user();

        $clients = Client::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->with(['service', 'files', 'user'])
            ->paginate(10);

        return Inertia::render('Admin/Clients/MyClients', [
            'clients' => $clients,
        ]);
    }

    public function mycreate(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
        ]);

        $validated['user_id'] = $user->id;

        Client::create($validated);

        return redirect()->route('admin.myclients')
            ->with('success', 'Client created successfully.');
    }


    public function mystore(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
        ]);

        $validated['user_id'] = $user->id;

        Client::create($validated);

        return redirect()->route('admin.myclients')
            ->with('success', 'Client created successfully.');
    }
}

