<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->where('role', 'b2b') // <- só usuários b2b
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'name' => "Subagent"
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
            return redirect()->route('admin.users.index')->with('success', 'User created.');
        }

        return redirect()->route('admin.subagents.index')->with('success', 'User created.');

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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,b2b'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'], // aqui
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'service_id' => $validated['service_id'] ?? null,
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    // View Clients of a User
    public function show(User $user)
    {
        $clients = $user->clients()->select('id', 'name', 'created_at')->latest()->paginate(10);

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'clients' => $clients,
        ]);
    }
}
