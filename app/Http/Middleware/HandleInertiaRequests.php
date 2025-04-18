<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

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
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id'          => $request->user()->id,
                    'name'        => $request->user()->name,
                    // combine direct + via‑role permissions
                    'permissions' => $request->user()
                                           ->getAllPermissions()
                                           ->pluck('name')
                                           ->toArray(),
                    'roles'       => $request->user()
                                           ->getRoleNames()
                                           ->toArray(),
                ] : null,
                // if you want, you can still share these booleans too:
                'canManageProjects' => fn() => $request->user()?->can('manage client projects') ?? false,
                'isAdmin'           => fn() => $request->user()?->hasRole('admin') ?? false,
            ],
        ]);
    }

    
}
