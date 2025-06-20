<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\File;
use App\Models\User;
use App\Models\FamilyMember;
use App\Models\Service;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'super_admin') {
            $clients = Client::orderBy('created_at', 'desc')
                ->with(['service', 'files', 'user'])
                ->paginate(10);
            return Inertia::render('Admin/Clients/Index', [
                'clients' => $clients,
            ]);
        } else {

            $clients = Client::where('service_id', $user->service_id)
                ->orderBy('created_at', 'desc')
                ->with(['service', 'files', 'user'])
                ->paginate(10);
        }

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients,
        ]);
    }

    public function edit(Client $client)
    {
        $familyMembers = FamilyMember::where('client_id', $client->id)->get();

        return Inertia::render('Admin/Clients/Edit', [
            'client' => $client,
            'services' => Service::all(),
            'familyMembers' => $familyMembers,
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code_reference' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
        ]);

        $client->update($validated);

        return redirect()->back()->banner('Client updated successfully.');
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

    public function show(Client $client, $id)
    {
        $user = auth()->user();

        $client = Client::where('id', $id)
            ->with(['service.documents', 'files', 'user'])
            ->firstOrFail();

        $familyMembers = $client->familyMembers()->get(['id', 'label']);

        return Inertia::render('Admin/Clients/Show', [
            'client' => $client,
            'user' => $user,
            'familyMembers' => $familyMembers,
        ]);
    }

    public function admincreate()
    {
        $authUser = auth()->user();
        $users = User::with('clients')->get();

        $services = collect();

        if ($authUser->service_id) {
            $services = Service::where('id', $authUser->service_id)
                ->with('documents')
                ->get();
        }

        return Inertia::render('Admin/Clients/AdminCreate', [
            'services' => $services,
            'users' => $users,
        ]);
    }

    public function adminstore(Request $request)
    {
        $user = User::find($request->user_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'service_id' => 'nullable|exists:services,id',
        ]);
        $service_id = Auth::user()->service_id ?? null;
        $client = Client::create([
            'name' => $request->name,
            'user_id' => $request->user_id,
            'service_id' => $service_id,
            'code_reference' => Client::generateUniqueCodeReference(),
        ]);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client created successfully.');
    }


    public function create()
    {
        $user = auth()->user();

        $services = collect();

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
        $validated['code_reference'] = Client::generateUniqueCodeReference();

        Client::create($validated);

        return redirect()->route('admin.clients.index')
            ->banner('Client created successfully.');
    }

    public function clientuser(Request $request, User $user)
    {
        $services = Service::with('documents')->get();

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

        $validated['user_id'] = $user->id;
        $validated['code_reference'] = Client::generateUniqueCodeReference();

        Client::create($validated);

        return redirect()->route('admin.users.show', ['user' => $user->id])
            ->banner('Client created successfully.');
    }

    public function status(Request $request, File $file)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'id' => 'required',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $file = File::findOrFail($request->id);
        $file->status = $request->status;

        if ($request->status === 'rejected') {
            $file->rejection_reason = $request->rejection_reason;
        } else {
            $file->rejection_reason = null;
        }

        $file->save();
    }

    public function updateDocumentStatus(Request $request, File $file)
    {
        $status = $request->input('status');

        // dd($status);
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $file->status = $request->input('status');
        $file->save();

        return response()->json([
            'message' => 'Document status updated.',
            'file' => $file,
        ]);
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
        $validated['code_reference'] = Client::generateUniqueCodeReference();

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
