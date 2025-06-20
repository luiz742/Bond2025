<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->where('role', 'b2b') // <- só usuários b2b
            ->latest()
            ->paginate(10);

        $services = Service::all();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'name' => "Subagent",
        ]);
    }

    public function admin()
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->whereIn('role', ['admin', 'super_admin'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'name' => "User"
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users' . ($user->id ?? '')],
            'password' => $request->isMethod('post') ? ['required', Rules\Password::defaults()] : ['nullable', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,b2b'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'], // aqui
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'service_id' => $validated['service_id'] ?? null,
        ]);

        if (in_array($request->role, ['admin', 'super_admin'])) {
            return redirect()->route('admin.users.index')->banner('User created.');
        }

        return redirect()->route('admin.subagents.index')->banner('User created.');

    }

    public function edit(User $user)
    {
        $services = Service::all();
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'services' => $services,
        ]);
    }

    public function update(Request $request, User $user)
    {
        dd($request->all());
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,b2b,super_admin'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'], // aqui
            'service_agreement' => ['nullable'],
            'trade_license_number' => ['nullable', 'string', 'max:255'],
            'tax_registration_number' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'service_id' => $validated['service_id'] ?? null,
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
            'tax_registration_number' => $validated['tax_registration_number'] ?? null,
            'trade_license_number' => $validated['trade_license_number'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        return redirect()->back()->banner('User updated.');

    }

    public function uploadAgreement(Request $request, User $user)
    {
        // dd($request->all());
        $validated = $request->validate([
            'service_agreement' => ['required', 'file', 'mimes:pdf', 'max:2048'],
        ]);

        if ($request->hasFile('service_agreement')) {
            $path = $request->file('service_agreement')->store('agreements', 'public');
            $user->service_agreement = $path;
            $user->save();
        }

        return redirect()->back()->banner('Service Agreement uploaded successfully.');
    }

    // View Clients of a User
    public function show(User $user)
    {
        $services = Service::all();
        $clients = $user->clients()->select('id', 'code_reference', 'name', 'created_at')->latest()->paginate(10);

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'clients' => $clients,
            'services' => $services,
        ]);
    }

    public function destroy(User $user)
    {
        // Opcional: impedir que um super admin exclua a si mesmo
        if (auth()->id() === $user->id) {
            return redirect()->back()->dangerBanner('Você não pode excluir a si mesmo.');
        }

        // Apaga o usuário
        $user->delete();

        return redirect()->back()->banner('User deleted successfully.');
    }

    public function clientdestroy(Client $client)
    {
        // Opcional: impedir que um super admin exclua a si mesmo
        if (auth()->id() === $client->id) {
            return redirect()->back()->dangerBanner('error', 'Você não pode excluir a si mesmo.');
        }

        // Apaga o usuário
        $client->delete();

        return redirect()->back()->banner('User deleted successfully.');
    }

}
