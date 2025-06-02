<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $user
                    ? array_merge(
                        $user->only(['id', 'name', 'email', 'service_id', 'created_at', 'email', 'role']),
                        [
                            'kyc' => $user->kyc ?? null,
                        ]
                    )
                    : null,

                'notifications' => $user && $user->service_id
                    ? \App\Models\Notification::where('service_id', $user->service_id)
                        ->where('read', false)
                        ->get(['id', 'type', 'client_id', 'service_id', 'read', 'created_at'])
                    : [],
            ],

            'flash' => [
                'message' => fn() => $request->session()->get('message'),
            ],
        ];
    }
}
