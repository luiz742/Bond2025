<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use App\Models\FamilyMember;
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

        // Busca os membros da família direto no controller
        $familyMembers = $client->familyMembers()->get(['id', 'label']); // Ajuste os campos conforme tabela

        $client->load(['service.documents', 'files']); // Mantém só os dados necessários para client

        return Inertia::render('Clients/Show', [
            'client' => $client,
            'user' => $user,
            'familyMembers' => $familyMembers,
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

        // valida apenas os campos que o usuário vai enviar
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id', // opcional, só se quiser registrar para outro usuário
        ]);

        // força sempre o service_id do usuário logado
        $validated['service_id'] = $user->service_id;

        // se user_id não foi enviado, pega o próprio usuário
        $validated['user_id'] = $validated['user_id'] ?? $user->id;

        $validated['code_reference'] = Client::generateUniqueCodeReference();

        $client = Client::create($validated);
        $client->notifyClientCreated();

        if ($request->wantsJson()) {
            return response()->json([
                'client' => $client,
                'message' => 'Client created successfully.',
            ]);
        }

        return redirect()->route('admin.clients.index')
            ->banner('Client created successfully.');
    }



    public function edit(Client $client)
    {
        $user = auth()->user();

        if ($client->user_id !== $user->id) {
            abort(403);
        }

        $services = Service::all();

        // Busca direta dos membros da família
        $familyMembers = FamilyMember::where('client_id', $client->id)->get();
        // dd($familyMembers);
        return Inertia::render('Clients/Edit', [
            'client' => $client,
            'services' => $services,
            'user' => $user,
            'familyMembers' => $familyMembers, // envia separadamente
        ]);
    }


    public function update(Request $request, Client $client)
    {
        $user = auth()->user();

        if ($client->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
        ]);

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->banner('Client updated successfully.');
    }

    public function updateFamilyMember(Request $request, FamilyMember $familyMember)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $familyMember->update([
            'name' => $validated['name'],
        ]);

        return redirect()->back()->banner('Family member updated successfully.');
    }
}
