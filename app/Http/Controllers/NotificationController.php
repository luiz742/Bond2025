<?php

namespace App\Http\Controllers;
use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $notifications = Notification::with(['client', 'user', 'service', 'document'])
            ->when($user->role === 'b2b', fn($query) => $query->where('user_id', $user->id))
            ->when($user->role === 'admin', fn($query) => $query->where('service_id', $user->service_id))
            ->latest()
            ->get();


        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }

    public function redirectAndMarkRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['read' => true]);

        // Redireciona para show do client
        return redirect()->route('admin.clients.show', $notification->client_id);
    }

}
