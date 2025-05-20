<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $clients = $user->clients()->with('service')->paginate(15);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'user' => $user,
        ]);
    }

    public function show(Client $client)
    {
        $user = auth()->user();

        if ($client->user_id !== $user->id) {
            abort(403);
        }

        $client->load(['service.documents', 'files']); // carrega documentos e arquivos enviados

        return Inertia::render('Clients/Show', [
            'client' => $client,
            'user' => $user,
        ]);
    }


    public function create()
    {
        $services = Service::all();
        $user = auth()->user();

        return Inertia::render('Clients/Create', [
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

        return redirect()->route('clients.index')
            ->with('success', 'Client created successfully.');
    }
}
