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
            'portal_settings' => [
                'contact_phone' => \App\Models\PortalSetting::getValue('contact_phone', ['en' => '+880 1234 567890', 'bn' => '+৮৮০ ১২৩৪ ৫৬৭৮৯০']),
                'contact_address' => \App\Models\PortalSetting::getValue('contact_address', ['en' => '123 Green Way, Eco City, Bangladesh', 'bn' => '১২৩ গ্রিন ওয়ে, ইকো সিটি, বাংলাদেশ']),
                'contact_email' => \App\Models\PortalSetting::getValue('contact_email', ['en' => 'support@regenerative.systems', 'bn' => 'support@regenerative.systems']),
                'contact_facebook' => \App\Models\PortalSetting::getValue('contact_facebook', ['en' => 'https://facebook.com/regenerativesystems', 'bn' => 'https://facebook.com/regenerativesystems']),
                'contact_twitter' => \App\Models\PortalSetting::getValue('contact_twitter', ['en' => 'https://twitter.com/regensys', 'bn' => 'https://twitter.com/regensys']),
                'contact_youtube' => \App\Models\PortalSetting::getValue('contact_youtube', ['en' => 'https://youtube.com/c/regenerativesystems', 'bn' => 'https://youtube.com/c/regenerativesystems']),
                'footer_description' => \App\Models\PortalSetting::getValue('footer_description', [
                    'en' => 'Rooted in Earth Care, People Care, and Fair Share. Our platform empowers local organic agriculture and dynamic ecological learning circles globally.',
                    'bn' => 'আর্থ কেয়ার, পিপল কেয়ার এবং ফেয়ার শেয়ার-এ নিহিত। আমাদের প্ল্যাটফর্ম স্থানীয় জৈব কৃষি এবং গতিশীল পরিবেশগত শিক্ষা বৃত্তকে বিশ্বব্যাপী ক্ষমতায়ন করে।'
                ]),
            ],
            'flash' => [
                'message' => $request->session()->get('message'),
                'error' => $request->session()->get('error'),
            ],
        ];
    }
}
