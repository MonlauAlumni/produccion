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

    $settings = $user ? $user->settings()->first() : [];

    $locale = app()->getLocale();
    $translations = [];
    
    if (file_exists(resource_path("lang/{$locale}.json"))) {
        $translations = json_decode(file_get_contents(resource_path("lang/{$locale}.json")), true);
    }
    return array_merge(parent::share($request), [
        'auth' => [
            'user' => $user,
            'user_notifications' => $user ? $user->notifications()->where('is_read', 0)->count() : 0,
            'user_settings' => $settings,
        ],
        'locale' => $locale,
        'translations' => $translations,
    ]);
}

}