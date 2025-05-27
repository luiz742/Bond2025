<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\File;
use App\Models\User;
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

    public function show(Client $client, $id)
    {
        $user = auth()->user();
        $client = Client::where('id', $id)
            ->with(['service.documents', 'files', 'user'])
            ->firstOrFail();

        return Inertia::render('Admin/Clients/Show', [
            'client' => $client,
            'user' => $user,
        ]);
    }
    public function create()
    {
        $user = auth()->user();

        // Cria uma coleção vazia por padrão
        $services = collect();

        // Só busca serviços se o usuário tiver um service_id
        if ($user->service_id) {
            $services = Service::where('id', $user->service_id)
                ->with('documents')
                ->get();
        }

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
            ->banner('Client created successfully.');
    }

    public function clientuser(Request $request, User $user)
    {

        $services = Service::
            with('documents')
            ->get();
        return Inertia::render('Admin/Users/ClientUsers', [
            'services' => $services,
            'user' => $user,
        ]);
    }

    public function clientuserstore(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'nullable|exists:services,id',
        ]);

        // Define o user_id para o user da rota (não o usuário logado)
        $validated['user_id'] = $user->id;

        Client::create($validated);

        return redirect()->route('admin.users.show', ['user' => $user->id])
            ->banner('Client created successfully.');
    }


    public function updateDocumentStatus(Request $request, File $file)
    {
        // dd("Chegou no update status para o arquivo: {$file->id}, status novo: {$request->status}");

        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $file->status = $request->status;
        $file->save();

        return redirect()->back()->banner('Document status updated.');
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

    public function mydestroy(Client $client)
    {
        // Verifica se o usuário autenticado é o dono do cliente
        if (auth()->user()->id !== $client->user_id) {
            return redirect()->back()->dangerBanner('error', "You don't have permission to delete this client.");
        }

        $client->delete();
        return redirect()->back()
            ->banner('Client deleted successfully.');

    }

    public function mycreate(Request $request)
    {
        $user = auth()->user();
        $services = Service::where('id', $user->service_id)
            ->with('documents')
            ->get();
        return Inertia::render('Admin/Clients/MyCreate', [
            'services' => $services,
            'user' => $user,
        ]);
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
            ->banner('Client created successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('admin.clients.index')
            ->banner('Client deleted successfully.');
    }
}

