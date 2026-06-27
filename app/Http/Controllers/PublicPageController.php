<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PortalSetting;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    /**
     * Localize and resolve dynamic headers.
     */
    protected function getTranslatedHeader($key, $defaults)
    {
        $header = PortalSetting::getValue($key, $defaults);
        $locale = app()->getLocale();

        $translated = [
            'title' => $header['title'][$locale] ?? $header['title']['en'] ?? '',
            'subtitle' => $header['subtitle'][$locale] ?? $header['subtitle']['en'] ?? '',
            'badge' => $header['badge'][$locale] ?? $header['badge']['en'] ?? '',
            'bg_image' => $header['bg_image'] ?? $defaults['bg_image'],
        ];

        if ($translated['bg_image'] && !str_starts_with($translated['bg_image'], 'http')) {
            $translated['bg_image'] = asset('storage/' . $translated['bg_image']);
        }

        return $translated;
    }

    /**
     * Render the about page.
     */
    public function about(): Response
    {
        $aboutImage = PortalSetting::getValue('about_image', 'https://images.unsplash.com/photo-1592419044706-39796d40f98c?auto=format&fit=crop&q=80&w=800');
        if ($aboutImage && !str_starts_with($aboutImage, 'http')) {
            $aboutImage = asset('storage/' . $aboutImage);
        }

        $header = $this->getTranslatedHeader('about_header', [
            'title' => ['en' => 'Regenerative Systems', 'bn' => 'পুনরুত্পাদনশীল সিস্টেম'],
            'subtitle' => [
                'en' => 'Rooted in Earth Care, People Care, and Fair Share. Empowering ecological restoration and permanent culture globally.',
                'bn' => 'আর্থ কেয়ার, পিপল কেয়ার এবং ফেয়ার শেয়ারে বিশ্বাসী। বিশ্বজুড়ে পরিবেশগত পুনরুদ্ধার এবং স্থায়ী সংস্কৃতিকে শক্তিশালী করা।'
            ],
            'badge' => ['en' => 'About Our Initiative', 'bn' => 'আমাদের উদ্যোগ সম্পর্কে'],
            'bg_image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1920&q=80'
        ]);

        return Inertia::render('Public/About', [
            'header' => $header,
            'about_image' => $aboutImage,
            'mission' => PortalSetting::getValue('about_mission', 'To accelerate the global transition to regenerative ecosystems through design education, community action, and ethical networking.'),
            'vision' => PortalSetting::getValue('about_vision', 'To be the leading global knowledge commons for community-supported ecological restoration and permanent culture.'),
            'about_lms' => PortalSetting::getValue('about_lms', 'Our LMS provides structured learning pathways, peer discussions, and verified certificates to empower local regenerative initiatives.'),
            'about_ethics' => PortalSetting::getValue('about_ethics', [
                [
                    'icon' => 'bi-globe2',
                    'title' => ['en' => 'Earth Care', 'bn' => 'আর্থ কেয়ার'],
                    'description' => [
                        'en' => 'Rebuilding natural capital, restoring soil biology, conserving clean water, and fostering diverse ecosystems.',
                        'bn' => 'প্রাকৃতিক মূলধন পুনর্গঠন, মাটির জীববিজ্ঞান পুনরুদ্ধার, বিশুদ্ধ পানি সংরক্ষণ এবং বৈচিত্র্যময় বাস্তুতন্ত্র গড়ে তোলা।'
                    ]
                ],
                [
                    'icon' => 'bi-people-fill',
                    'title' => ['en' => 'People Care', 'bn' => 'পিপল কেয়ার'],
                    'description' => [
                        'en' => 'Supporting self-reliance, community resilience, healthy food systems, and accessible lifelong learning.',
                        'bn' => 'আত্মনির্ভরশীলতা, সম্প্রদায়ের স্থিতিস্থাপকতা, স্বাস্থ্যকর খাদ্য ব্যবস্থা এবং অ্যাক্সেসযোগ্য আজীবন শিক্ষাকে সমর্থন করা।'
                    ]
                ],
                [
                    'icon' => 'bi-arrow-left-right',
                    'title' => ['en' => 'Fair Share', 'bn' => 'ফেয়ার শেয়ার'],
                    'description' => [
                        'en' => 'Redistributing surplus resources to care for the Earth and others, fostering a collaborative economic model.',
                        'bn' => 'পৃথিবী এবং অন্যদের যত্নের জন্য উদ্বৃত্ত সম্পদ পুনবণ্টন করা, একটি সহযোগিতামূলক অর্থনৈতিক মডেলকে উৎসাহিত করা।'
                    ]
                ]
            ]),
            'counter_courses' => PortalSetting::getValue('counter_courses', '10+'),
            'counter_teachers' => PortalSetting::getValue('counter_teachers', '25+'),
            'counter_students' => PortalSetting::getValue('counter_students', '500+'),
            'counter_batches' => PortalSetting::getValue('counter_batches', '50+'),
        ]);
    }

    /**
     * Render the terms and conditions page.
     */
    public function terms(): Response
    {
        $header = $this->getTranslatedHeader('terms_header', [
            'title' => ['en' => 'Terms & Conditions', 'bn' => 'শর্তাবলী এবং নিয়মনীতি'],
            'subtitle' => ['en' => 'Please review our terms of use, privacy declarations, and learning platform guidelines.', 'bn' => 'আমাদের ব্যবহারের শর্তাবলী, প্রাইভেসি ডিক্লারেশন এবং লার্নিং প্ল্যাটফর্মের নির্দেশিকা পর্যালোচনা করুন।'],
            'badge' => ['en' => 'Legal & Guidelines', 'bn' => 'আইনি ও গাইডলাইন'],
            'bg_image' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=1920&q=80'
        ]);

        return Inertia::render('Public/Terms', [
            'header' => $header,
            'terms_content' => PortalSetting::getValue('terms_content'),
        ]);
    }

    /**
     * Render the privacy policy page.
     */
    public function privacy(): Response
    {
        $header = $this->getTranslatedHeader('privacy_header', [
            'title' => ['en' => 'Privacy Policy', 'bn' => 'প্রাইভেসি পলিসি'],
            'subtitle' => ['en' => 'Your trust is essential to us. Read how we protect and manage your private data.', 'bn' => 'আপনার ট্রাস্ট আমাদের কাছে অত্যন্ত মূল্যবান। কীভাবে আমরা আপনার ব্যক্তিগত তথ্য সুরক্ষিত ও পরিচালনা করি তা জেনে নিন।'],
            'badge' => ['en' => 'Data & Safety', 'bn' => 'তথ্য ও নিরাপত্তা'],
            'bg_image' => 'https://images.unsplash.com/photo-1557683316-973673baf926?auto=format&fit=crop&w=1920&q=80'
        ]);

        return Inertia::render('Public/Privacy', [
            'header' => $header,
            'privacy_content' => PortalSetting::getValue('privacy_content', [
                'en' => "Our Privacy Policy outlines how we collect, use, and protect your personal information when you use our services...",
                'bn' => "আমাদের প্রাইভেসি পলিসি বর্ণনা করে যে কীভাবে আমরা আপনার ব্যক্তিগত তথ্য সংগ্রহ, ব্যবহার এবং সুরক্ষিত করি..."
            ]),
        ]);
    }

    /**
     * Render the contact page.
     */
    public function contact(): Response
    {
        $header = $this->getTranslatedHeader('contact_header', [
            'title' => ['en' => 'Get In Touch', 'bn' => 'যোগাযোগ করুন'],
            'subtitle' => ['en' => 'Have questions? We are here to help and build a resilient tomorrow together.', 'bn' => 'কোনো প্রশ্ন আছে? আমরা সাহায্য করতে এবং একসঙ্গে একটি শক্তিশালী আগামী তৈরি করতে প্রস্তুত।'],
            'badge' => ['en' => 'Contact Us', 'bn' => 'যোগাযোগ'],
            'bg_image' => 'https://images.unsplash.com/photo-1596524430615-b46475ddff6e?auto=format&fit=crop&w=1920&q=80'
        ]);

        return Inertia::render('Public/Contact', [
            'header' => $header,
            'phone' => PortalSetting::getValue('contact_phone', '+880 1234 567890'),
            'email' => PortalSetting::getValue('contact_email', 'support@regenerative.systems'),
            'address' => PortalSetting::getValue('contact_address', '123 Green Way, Eco City, Bangladesh'),
            'facebook' => PortalSetting::getValue('contact_facebook', 'https://facebook.com/regenerativesystems'),
            'twitter' => PortalSetting::getValue('contact_twitter', 'https://twitter.com/regensys'),
            'youtube' => PortalSetting::getValue('contact_youtube', 'https://youtube.com/c/regenerativesystems'),
            'google_map' => PortalSetting::getValue('contact_google_map', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.9024424301385!2d90.39108011536295!3d23.75088939467473!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8beb29ba7d7%3A0xe54d32f7e44a3e2a!2sXAMPP!5e0!3m2!1sen!2sbd!4v1655712345678!5m2!1sen!2sbd'),
        ]);
    }

    /**
     * List all active organizations / partners.
     */
    public function partners(): Response
    {
        $partners = Organization::where('status', 'active')
            ->withCount(['courses' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->map(function ($org) {
                return [
                    'id' => $org->id,
                    'name' => $org->name,
                    'slug' => $org->slug,
                    'logo_url' => $org->logo_url,
                    'description' => $org->description,
                    'courses_count' => $org->courses_count,
                    'website' => $org->website,
                ];
            });

        $header = $this->getTranslatedHeader('partners_header', [
            'title' => ['en' => 'Our Collaborative Partners', 'bn' => 'আমাদের অংশীদার সংস্থাগুলি'],
            'subtitle' => ['en' => 'Explore the network of organic farms, training centers, and institutions pioneering permaculture.', 'bn' => 'পার্মাকালচার নিয়ে কাজ করা জৈব খামার, প্রশিক্ষণ কেন্দ্র এবং প্রতিষ্ঠানগুলির নেটওয়ার্ক অন্বেষণ করুন।'],
            'badge' => ['en' => 'Resilience Directory', 'bn' => 'রেজিলিয়েন্স ডিরেক্টরি'],
            'bg_image' => 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=1920&q=80'
        ]);

        return Inertia::render('Public/PartnersList', [
            'header' => $header,
            'partners' => $partners,
        ]);
    }

    /**
     * Display a single partner's profile including courses and teachers.
     */
    public function partnerDetails(string $slug): Response
    {
        $partner = Organization::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $courses = Course::where('organization_id', $partner->id)
            ->active()
            ->get()
            ->map(function (Course $course) {
                return [
                    'id' => $course->id,
                    'title' => $course->translate('title'),
                    'short_description' => $course->translate('short_description'),
                    'duration' => $course->duration,
                    'level' => $course->level,
                    'delivery_mode' => $course->delivery_mode,
                    'image_url' => $course->image_url,
                ];
            });

        $teachers = User::where('organization_id', $partner->id)
            ->whereHas('roles', function ($q) {
                $q->where('slug', 'teacher');
            })
            ->where('is_approved', true)
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_url,
                ];
            });

        return Inertia::render('Public/PartnerDetails', [
            'partner' => [
                'id' => $partner->id,
                'name' => $partner->name,
                'slug' => $partner->slug,
                'logo_url' => $partner->logo_url,
                'description' => $partner->description,
                'email' => $partner->email,
                'phone' => $partner->phone,
                'address' => $partner->address,
                'website' => $partner->website,
                'cover_image' => $partner->cover_image ?? null
            ],
            'courses' => $courses,
            'teachers' => $teachers,
        ]);
    }
}
