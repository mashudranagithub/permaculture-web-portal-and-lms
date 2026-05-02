<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'avatar_url' => $request->user()->avatar_url,
                    'roles' => $request->user()->roles->pluck('slug')->toArray(),
                    'permissions' => collect($request->user()->permissions->pluck('slug'))
                        ->merge($request->user()->roles->flatMap->permissions->pluck('slug'))
                        ->unique()
                        ->values()
                        ->toArray(),
                    'organization' => $request->user()->organization ? [
                        'id' => $request->user()->organization->id,
                        'name' => $request->user()->organization->name,
                        'logo_url' => $request->user()->organization->logo_url,
                    ] : null,
                ] : null,
            ],
            'translations' => function () {
                $locale = app()->getLocale();
                $file = lang_path("$locale.json");
                if (is_file($file)) {
                    $translations = json_decode(file_get_contents($file), true);
                    return empty($translations) ? (object) [] : $translations;
                }
                return (object) [];
            },
            'flash' => [
                'message' => $request->session()->get('message'),
                'error' => $request->session()->get('error'),
            ],
        ];
    }
}
