<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PortalSettingsController extends Controller
{
    protected function checkAccess()
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('super-admin') && $user->organization_id !== null) {
            abort(403, 'Unauthorized action. Organization admins cannot edit portal settings.');
        }
    }

    /**
     * Helper to process background image upload for headers.
     */
    protected function handleHeaderUpdate(Request $request, $key, $fileInputName)
    {
        $headerData = $request->input($key);
        if ($request->hasFile($fileInputName)) {
            $path = $request->file($fileInputName)->store('portal', 'public');
            $headerData['bg_image'] = $path;
        } else {
            $existing = PortalSetting::getValue($key);
            $headerData['bg_image'] = $existing['bg_image'] ?? '';
            // Strip absolute URL prefix if it was previously resolved
            if (str_starts_with($headerData['bg_image'], asset('storage/'))) {
                $headerData['bg_image'] = str_replace(asset('storage/'), '', $headerData['bg_image']);
            }
        }
        PortalSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $headerData]
        );
    }

    /**
     * Show Homepage CMS editor.
     */
    public function editHomepage(): Response
    {
        $this->checkAccess();

        $defaultKeys = [
            'homepage_hero_title' => [
                'en' => 'Cultivating a Sustainable Future',
                'bn' => 'একটি টেকসই ভবিষ্যৎ গড়ে তোলা'
            ],
            'homepage_hero_description' => [
                'en' => 'Join thousands of changemakers worldwide in designing nature-based systems that regenerate our planet and communities.',
                'bn' => 'আমাদের গ্রহ এবং সম্প্রদায়গুলিকে পুনরুত্থিত করে এমন প্রকৃতি-ভিত্তিক সিস্টেম ডিজাইনে বিশ্বব্যাপী হাজার হাজার পরিবর্তনকারীদের সাথে যোগ দিন।'
            ],
            'counter_courses' => [
                'en' => '10+',
                'bn' => '১০+'
            ],
            'counter_teachers' => [
                'en' => '25+',
                'bn' => '২৫+'
            ],
            'counter_students' => [
                'en' => '500+',
                'bn' => '৫০০+'
            ],
            'counter_batches' => [
                'en' => '50+',
                'bn' => '৫০+'
            ],
            'homepage_slides' => [
                [
                    'title' => [
                        'en' => 'Cultivating a Sustainable Future',
                        'bn' => 'একটি টেকসই ভবিষ্যৎ গড়ে তোলা'
                    ],
                    'description' => [
                        'en' => 'Join thousands of changemakers worldwide in designing nature-based systems that regenerate our planet.',
                        'bn' => 'আমাদের গ্রহ পুনর্নির্মাণকারী প্রকৃতি-ভিত্তিক সিস্টেম ডিজাইনে বিশ্বব্যাপী হাজার হাজার পরিবর্তনকারীদের সাথে যোগ দিন।'
                    ],
                    'image' => 'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'
                ]
            ]
        ];

        $settings = [];
        foreach ($defaultKeys as $key => $defaultValue) {
            $setting = PortalSetting::firstOrCreate(
                ['key' => $key],
                ['value' => $defaultValue]
            );
            $settings[$key] = $setting->value;
        }

        return Inertia::render('Admin/CMS/Homepage', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update Homepage CMS settings.
     */
    public function updateHomepage(Request $request): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'homepage_hero_title' => 'required|array',
            'homepage_hero_title.en' => 'required|string',
            'homepage_hero_title.bn' => 'required|string',
            'homepage_hero_description' => 'required|array',
            'homepage_hero_description.en' => 'required|string',
            'homepage_hero_description.bn' => 'required|string',
            'counter_courses' => 'required|array',
            'counter_courses.en' => 'required|string',
            'counter_courses.bn' => 'required|string',
            'counter_teachers' => 'required|array',
            'counter_teachers.en' => 'required|string',
            'counter_teachers.bn' => 'required|string',
            'counter_students' => 'required|array',
            'counter_students.en' => 'required|string',
            'counter_students.bn' => 'required|string',
            'counter_batches' => 'required|array',
            'counter_batches.en' => 'required|string',
            'counter_batches.bn' => 'required|string',
            'homepage_slides' => 'nullable|array',
        ]);

        foreach ($request->only([
            'homepage_hero_title',
            'homepage_hero_description',
            'counter_courses',
            'counter_teachers',
            'counter_students',
            'counter_batches',
        ]) as $key => $value) {
            PortalSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $slides = $request->input('homepage_slides', []);
        foreach ($slides as $index => &$slide) {
            if ($request->hasFile("homepage_slides.{$index}.image_file")) {
                $file = $request->file("homepage_slides.{$index}.image_file");
                $path = $file->store('portal', 'public');
                $slide['image'] = asset('storage/' . $path);
            }
            unset($slide['image_file']);
            unset($slide['image_preview']);
        }

        PortalSetting::updateOrCreate(
            ['key' => 'homepage_slides'],
            ['value' => $slides]
        );

        return redirect()->back()->with('success', 'Homepage settings updated successfully.');
    }

    /**
     * Show About Us CMS editor.
     */
    public function editAbout(): Response
    {
        $this->checkAccess();

        $defaultKeys = [
            'about_mission' => [
                'en' => 'To accelerate the global transition to regenerative ecosystems through design education, community action, and ethical networking.',
                'bn' => 'ডিজাইন শিক্ষা, সম্প্রদায়গত পদক্ষেপ এবং নৈতিক নেটওয়ার্কিংয়ের মাধ্যমে পুনরুত্পাদনশীল ইকোসিস্টেমের দিকে বৈজ্ঞিক রূপান্তরকে ত্বরান্বিত করা।'
            ],
            'about_vision' => [
                'en' => 'To be the leading global knowledge commons for community-supported ecological restoration and permanent culture.',
                'bn' => 'কমিউনিটি-সমর্থিত পরিবেশগত পুনরুদ্ধার এবং স্থায়ী সংস্কৃতির জন্য শীর্ষস্থানীয় বিশ্বব্যাপী জ্ঞান সাধারণ হওয়া।'
            ],
            'about_lms' => [
                'en' => 'Our LMS provides structured learning pathways, peer discussions, and verified certificates to empower local regenerative initiatives.',
                'bn' => 'আমাদের এলএমএস স্থানীয় পুনরুত্পাদনশীল উদ্যোগগুলিকে শক্তিশালী করতে কাঠামোগত শিক্ষার পথ, সমকক্ষ আলোচনা এবং যাচাইকৃত সার্টিফিকেট প্রদান করে।'
            ],
            'about_ethics' => [
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
            ],
            'about_image' => [
                'en' => 'https://images.unsplash.com/photo-1592419044706-39796d40f98c?auto=format&fit=crop&q=80&w=800',
                'bn' => 'https://images.unsplash.com/photo-1592419044706-39796d40f98c?auto=format&fit=crop&q=80&w=800',
            ],
            'about_header' => [
                'title' => ['en' => 'Regenerative Systems', 'bn' => 'পুনরুত্পাদনশীল সিস্টেম'],
                'subtitle' => ['en' => 'Rooted in Earth Care, People Care, and Fair Share. Empowering ecological restoration and permanent culture globally.', 'bn' => 'আর্থ কেয়ার, পিপল কেয়ার এবং ফেয়ার শেয়ারে বিশ্বাসী। বিশ্বজুড়ে পরিবেশগত পুনরুদ্ধার এবং স্থায়ী সংস্কৃতিকে শক্তিশালী করা।'],
                'badge' => ['en' => 'About Our Initiative', 'bn' => 'আমাদের উদ্যোগ সম্পর্কে'],
                'bg_image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1920&q=80'
            ]
        ];

        $settings = [];
        foreach ($defaultKeys as $key => $defaultValue) {
            $setting = PortalSetting::firstOrCreate(
                ['key' => $key],
                ['value' => $defaultValue]
            );
            $settings[$key] = $setting->value;
        }

        if (isset($settings['about_image']['en']) && !str_starts_with($settings['about_image']['en'], 'http')) {
            $settings['about_image']['en'] = asset('storage/' . $settings['about_image']['en']);
        }
        if (isset($settings['about_image']['bn']) && !str_starts_with($settings['about_image']['bn'], 'http')) {
            $settings['about_image']['bn'] = asset('storage/' . $settings['about_image']['bn']);
        }

        if (isset($settings['about_header']['bg_image']) && !str_starts_with($settings['about_header']['bg_image'], 'http')) {
            $settings['about_header']['bg_image'] = asset('storage/' . $settings['about_header']['bg_image']);
        }

        return Inertia::render('Admin/CMS/About', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update About Us CMS settings.
     */
    public function updateAbout(Request $request): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'about_mission' => 'required|array',
            'about_mission.en' => 'required|string',
            'about_mission.bn' => 'required|string',
            'about_vision' => 'required|array',
            'about_vision.en' => 'required|string',
            'about_vision.bn' => 'required|string',
            'about_lms' => 'required|array',
            'about_lms.en' => 'required|string',
            'about_lms.bn' => 'required|string',
            'about_ethics' => 'required|array',
            'about_image' => 'nullable|image|max:2048',

            'about_header' => 'required|array',
            'about_header.title' => 'required|array',
            'about_header.title.en' => 'required|string',
            'about_header.title.bn' => 'required|string',
            'about_header.subtitle' => 'required|array',
            'about_header.subtitle.en' => 'required|string',
            'about_header.subtitle.bn' => 'required|string',
            'about_header.badge' => 'required|array',
            'about_header.badge.en' => 'required|string',
            'about_header.badge.bn' => 'required|string',
            'about_header_bg' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('about_image')) {
            $path = $request->file('about_image')->store('portal', 'public');
            PortalSetting::updateOrCreate(
                ['key' => 'about_image'],
                ['value' => [
                    'en' => $path,
                    'bn' => $path,
                ]]
            );
        }

        foreach ($request->only([
            'about_mission',
            'about_vision',
            'about_lms',
            'about_ethics',
        ]) as $key => $value) {
            PortalSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->handleHeaderUpdate($request, 'about_header', 'about_header_bg');

        return redirect()->back()->with('success', 'About Us settings updated successfully.');
    }

    /**
     * Show Terms & Conditions CMS editor.
     */
    public function editTerms(): Response
    {
        $this->checkAccess();

        $defaultTerms = [
            'en' => "Welcome to Regenerative Systems. Please read these terms carefully before using our learning platform and services.\n\n1. User Registration and Accounts\nTo access courses or register as partner organization, you must create a user account. You agree to provide accurate and updated information. Newly registered teacher and student accounts undergo verification before full activation.\n\n2. Course Fees and Payments\nPayments for premium course enrollments are processed securely through our partners (bKash and SSLCommerz). All transactions are subject to respective payment gateway policies. Refunds are handled in accordance with the specific hosting organization's terms.\n\n3. Certificates of Completion\nCertificates are awarded upon successful completion of curriculum criteria. Verification of certificates is publicly accessible through unique links on our secure domain.\n\n4. Platform Conduct and IP\nAll materials, designs, video contents, and curricula hosted on Regenerative Systems remain the intellectual property of their respective creators or organizations. Unauthorized sharing or distribution is strictly prohibited.",
            'bn' => "পুনরুত্পাদনশীল সিস্টেমে স্বাগতম। আমাদের লার্নিং প্ল্যাটফর্ম এবং পরিষেবাগুলি ব্যবহার করার আগে অনুগ্রহ করে এই শর্তাবলী মনোযোগ সহকারে পড়ুন।\n\n১. ব্যবহারকারী নিবন্ধন এবং অ্যাকাউন্ট\nকোর্স অ্যাক্সেস করতে বা অংশীদার সংস্থা হিসাবে নিবন্ধন করতে, আপনাকে অবশ্যই একটি ব্যবহারকারী অ্যাকাউন্ট তৈরি করতে হবে। আপনি সঠিক এবং আপডেট তথ্য প্রদান করতে সম্মত হন। নতুন নিবন্ধিত শিক্ষক এবং ছাত্র অ্যাকাউন্টগুলি সম্পূর্ণ সক্রিয়করণের আগে যাচাইকরণের মধ্য দিয়ে যায়।\n\n২. কোর্স ফি এবং পেমেন্ট\nপ্রিমিয়াম কোর্স এনরোলমেন্টের জন্য পেমেন্ট আমাদের পার্টনারদের (বিকাশ এবং এসএসএলকমার্স) মাধ্যমে নিরাপদে প্রসেস করা হয়। সমস্ত লেনদেন নিজ নিজ পেমেন্ট গেটওয়ে পলিসি সাপেক্ষে। ফেরত দেওয়ার প্রক্রিয়া নির্দিষ্ট হোস্টিং সংস্থার শর্তাবলী অনুসারে পরিচালিত হয়।\n\n৩. সমাপ্তির সার্টিফিকেট\nকারিকুলামের মানদণ্ড সফলভাবে সমাপ্ত করার পর সার্টিফিকেট প্রদান করা হয়। সার্টিফিকেটের সত্যতা যাচাই আমাদের নিরাপদ ডোমেনে অনন্য লিঙ্কের মাধ্যমে সর্বজনীনভাবে অ্যাক্সেসযোগ্য।\n\n৪. প্ল্যাটফর্ম আচরণ এবং আইপি\nপুনরুত্পাদনশীল সিস্টেমে হোস্ট করা সমস্ত উপাদান, ডিজাইন, ভিডিও সামগ্রী এবং পাঠ্যক্রম তাদের নিজ নিজ নির্মাতা বা সংস্থার মেধা সম্পত্তি হিসাবে থাকবে। অননুমোদিত শেয়ারিং বা বিতরণ কঠোরভাবে নিষিদ্ধ।"
        ];

        $setting = PortalSetting::firstOrCreate(
            ['key' => 'terms_content'],
            ['value' => $defaultTerms]
        );

        $headerSetting = PortalSetting::firstOrCreate(
            ['key' => 'terms_header'],
            ['value' => [
                'title' => ['en' => 'Terms & Conditions', 'bn' => 'শর্তাবলী এবং নিয়মনীতি'],
                'subtitle' => ['en' => 'Please review our terms of use, privacy declarations, and learning platform guidelines.', 'bn' => 'আমাদের ব্যবহারের শর্তাবলী, প্রাইভেসি ডিক্লারেশন এবং লার্নিং প্ল্যাটফর্মের নির্দেশিকা পর্যালোচনা করুন।'],
                'badge' => ['en' => 'Legal & Guidelines', 'bn' => 'আইনি ও গাইডলাইন'],
                'bg_image' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=1920&q=80'
            ]]
        );

        $headerVal = $headerSetting->value;
        if (isset($headerVal['bg_image']) && !str_starts_with($headerVal['bg_image'], 'http')) {
            $headerVal['bg_image'] = asset('storage/' . $headerVal['bg_image']);
        }

        return Inertia::render('Admin/CMS/Terms', [
            'settings' => [
                'terms_content' => $setting->value,
                'terms_header' => $headerVal,
            ]
        ]);
    }

    /**
     * Update Terms & Conditions CMS settings.
     */
    public function updateTerms(Request $request): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'terms_content' => 'required|array',
            'terms_content.en' => 'required|string',
            'terms_content.bn' => 'required|string',

            'terms_header' => 'required|array',
            'terms_header.title' => 'required|array',
            'terms_header.title.en' => 'required|string',
            'terms_header.title.bn' => 'required|string',
            'terms_header.subtitle' => 'required|array',
            'terms_header.subtitle.en' => 'required|string',
            'terms_header.subtitle.bn' => 'required|string',
            'terms_header.badge' => 'required|array',
            'terms_header.badge.en' => 'required|string',
            'terms_header.badge.bn' => 'required|string',
            'terms_header_bg' => 'nullable|image|max:2048',
        ]);

        PortalSetting::updateOrCreate(
            ['key' => 'terms_content'],
            ['value' => $request->terms_content]
        );

        $this->handleHeaderUpdate($request, 'terms_header', 'terms_header_bg');

        return redirect()->back()->with('success', 'Terms & Conditions updated successfully.');
    }

    /**
     * Show Privacy Policy CMS editor.
     */
    public function editPrivacy(): Response
    {
        $this->checkAccess();

        $defaultPrivacy = [
            'en' => "1. Information We Collect\nWe collect personal information such as your name, email address, phone number, and organization details when you register on our platform.\n\n2. How We Use Your Information\nWe use your data to manage user accounts, coordinate batch enrollments, verify professional certifications, and secure transactions.\n\n3. Data Storage and Retention\nYour personal details are stored securely on our database. We do not sell or lease user information to third-party advertisers.\n\n4. Dynamic Access Control\nAdministrators monitor and verify teacher and student status on the platform to maintain secure community interactions.",
            'bn' => "১. তথ্য সংগ্রহ\nআপনি যখন আমাদের প্ল্যাটফর্মে নিবন্ধন করেন তখন আমরা আপনার নাম, ইমেল ঠিকানা, ফোন নম্বর এবং সংস্থার বিবরণের মতো ব্যক্তিগত তথ্য সংগ্রহ করি।\n\n২. তথ্যের ব্যবহার\nআমরা আপনার অ্যাকাউন্ট পরিচালনা করতে, ব্যাচ এনরোলমেন্ট পরিচালনা করতে, শিক্ষকদের শংসাপত্র যাচাই করতে এবং লেনদেন নিরাপদ রাখতে আপনার ডেটা ব্যবহার করি।\n\n৩. ডেটা স্টোরেজ ও সুরক্ষা\nআপনার ব্যক্তিগত তথ্য আমাদের ডেটাবেজে নিরাপদে সংরক্ষিত থাকে। আমরা কোনো তৃতীয় পক্ষের কাছে তথ্য বিক্রি বা লিজ দিই না।\n\n৪. অ্যাক্সেস কন্ট্রোল\nনিরাপদ প্ল্যাটফর্ম ব্যবহার বজায় রাখতে অ্যাডমিনিস্ট্রেটররা শিক্ষক এবং শিক্ষার্থীদের অ্যাকাউন্ট تদারকি ও অনুমোদন করে থাকেন।"
        ];

        $setting = PortalSetting::firstOrCreate(
            ['key' => 'privacy_content'],
            ['value' => $defaultPrivacy]
        );

        $headerSetting = PortalSetting::firstOrCreate(
            ['key' => 'privacy_header'],
            ['value' => [
                'title' => ['en' => 'Privacy Policy', 'bn' => 'প্রাইভেসি পলিসি'],
                'subtitle' => ['en' => 'Your trust is essential to us. Read how we protect and manage your private data.', 'bn' => 'আপনার ট্রাস্ট আমাদের কাছে অত্যন্ত মূল্যবান। কীভাবে আমরা আপনার ব্যক্তিগত তথ্য সুরক্ষিত ও পরিচালনা করি তা জেনে নিন।'],
                'badge' => ['en' => 'Data & Safety', 'bn' => 'তথ্য ও নিরাপত্তা'],
                'bg_image' => 'https://images.unsplash.com/photo-1557683316-973673baf926?auto=format&fit=crop&w=1920&q=80'
            ]]
        );

        $headerVal = $headerSetting->value;
        if (isset($headerVal['bg_image']) && !str_starts_with($headerVal['bg_image'], 'http')) {
            $headerVal['bg_image'] = asset('storage/' . $headerVal['bg_image']);
        }

        return Inertia::render('Admin/CMS/Privacy', [
            'settings' => [
                'privacy_content' => $setting->value,
                'privacy_header' => $headerVal,
            ]
        ]);
    }

    /**
     * Update Privacy Policy CMS settings.
     */
    public function updatePrivacy(Request $request): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'privacy_content' => 'required|array',
            'privacy_content.en' => 'required|string',
            'privacy_content.bn' => 'required|string',

            'privacy_header' => 'required|array',
            'privacy_header.title' => 'required|array',
            'privacy_header.title.en' => 'required|string',
            'privacy_header.title.bn' => 'required|string',
            'privacy_header.subtitle' => 'required|array',
            'privacy_header.subtitle.en' => 'required|string',
            'privacy_header.subtitle.bn' => 'required|string',
            'privacy_header.badge' => 'required|array',
            'privacy_header.badge.en' => 'required|string',
            'privacy_header.badge.bn' => 'required|string',
            'privacy_header_bg' => 'nullable|image|max:2048',
        ]);

        PortalSetting::updateOrCreate(
            ['key' => 'privacy_content'],
            ['value' => $request->privacy_content]
        );

        $this->handleHeaderUpdate($request, 'privacy_header', 'privacy_header_bg');

        return redirect()->back()->with('success', 'Privacy Policy updated successfully.');
    }

    /**
     * Show Course Catalog Header CMS editor.
     */
    public function editCoursesHeader(): Response
    {
        $this->checkAccess();

        $setting = PortalSetting::firstOrCreate(
            ['key' => 'courses_header'],
            ['value' => [
                'title' => ['en' => 'Available Courses', 'bn' => 'প্রাপ্য কোর্সসমূহ'],
                'subtitle' => ['en' => 'Discover the perfect path for your regenerative journey.', 'bn' => 'আপনার পুনরুত্পাদনশীল যাত্রার জন্য নিখুঁত পথটি আবিষ্কার করুন।'],
                'badge' => ['en' => 'Interactive Learning', 'bn' => 'ইন্টারেক্টিভ লার্নিং'],
                'bg_image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1920&q=80'
            ]]
        );

        $headerVal = $setting->value;
        if (isset($headerVal['bg_image']) && !str_starts_with($headerVal['bg_image'], 'http')) {
            $headerVal['bg_image'] = asset('storage/' . $headerVal['bg_image']);
        }

        return Inertia::render('Admin/CMS/CoursesHeader', [
            'settings' => [
                'courses_header' => $headerVal,
            ]
        ]);
    }

    /**
     * Update Course Catalog Header CMS settings.
     */
    public function updateCoursesHeader(Request $request): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'courses_header' => 'required|array',
            'courses_header.title' => 'required|array',
            'courses_header.title.en' => 'required|string',
            'courses_header.title.bn' => 'required|string',
            'courses_header.subtitle' => 'required|array',
            'courses_header.subtitle.en' => 'required|string',
            'courses_header.subtitle.bn' => 'required|string',
            'courses_header.badge' => 'required|array',
            'courses_header.badge.en' => 'required|string',
            'courses_header.badge.bn' => 'required|string',
            'courses_header_bg' => 'nullable|image|max:2048',
        ]);

        $this->handleHeaderUpdate($request, 'courses_header', 'courses_header_bg');

        return redirect()->back()->with('success', 'Course Catalog Page Header updated successfully.');
    }

    /**
     * Show Partners Directory Header CMS editor.
     */
    public function editPartnersHeader(): Response
    {
        $this->checkAccess();

        $setting = PortalSetting::firstOrCreate(
            ['key' => 'partners_header'],
            ['value' => [
                'title' => ['en' => 'Our Collaborative Partners', 'bn' => 'আমাদের অংশীদার সংস্থাগুলি'],
                'subtitle' => ['en' => 'Explore the network of organic farms, training centers, and institutions pioneering permaculture.', 'bn' => 'পার্মাকালচার নিয়ে কাজ করা জৈব খামার, প্রশিক্ষণ কেন্দ্র এবং প্রতিষ্ঠানগুলির নেটওয়ার্ক অন্বেষণ করুন।'],
                'badge' => ['en' => 'Resilience Directory', 'bn' => 'রেজিলিয়েন্স ডিরেক্টরি'],
                'bg_image' => 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=1920&q=80'
            ]]
        );

        $headerVal = $setting->value;
        if (isset($headerVal['bg_image']) && !str_starts_with($headerVal['bg_image'], 'http')) {
            $headerVal['bg_image'] = asset('storage/' . $headerVal['bg_image']);
        }

        return Inertia::render('Admin/CMS/PartnersHeader', [
            'settings' => [
                'partners_header' => $headerVal,
            ]
        ]);
    }

    /**
     * Update Partners Directory Header CMS settings.
     */
    public function updatePartnersHeader(Request $request): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'partners_header' => 'required|array',
            'partners_header.title' => 'required|array',
            'partners_header.title.en' => 'required|string',
            'partners_header.title.bn' => 'required|string',
            'partners_header.subtitle' => 'required|array',
            'partners_header.subtitle.en' => 'required|string',
            'partners_header.subtitle.bn' => 'required|string',
            'partners_header.badge' => 'required|array',
            'partners_header.badge.en' => 'required|string',
            'partners_header.badge.bn' => 'required|string',
            'partners_header_bg' => 'nullable|image|max:2048',
        ]);

        $this->handleHeaderUpdate($request, 'partners_header', 'partners_header_bg');

        return redirect()->back()->with('success', 'Partners Directory Page Header updated successfully.');
    }

    /**
     * Show Contact CMS editor.
     */
    public function editContact(): Response
    {
        $this->checkAccess();

        $defaultKeys = [
            'contact_phone' => [
                'en' => '+880 1234 567890',
                'bn' => '+৮৮০ ১২৩৪ ৫৬৭৮৯০'
            ],
            'contact_address' => [
                'en' => '123 Green Way, Eco City, Bangladesh',
                'bn' => '১২৩ গ্রিন ওয়ে, ইকো সিটি, বাংলাদেশ'
            ],
            'contact_email' => [
                'en' => 'support@regenerative.systems',
                'bn' => 'support@regenerative.systems'
            ],
            'contact_facebook' => [
                'en' => 'https://facebook.com/regenerativesystems',
                'bn' => 'https://facebook.com/regenerativesystems'
            ],
            'contact_twitter' => [
                'en' => 'https://twitter.com/regensys',
                'bn' => 'https://twitter.com/regensys'
            ],
            'contact_youtube' => [
                'en' => 'https://youtube.com/c/regenerativesystems',
                'bn' => 'https://youtube.com/c/regenerativesystems'
            ],
            'contact_google_map' => [
                'en' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.9024424301385!2d90.39108011536295!3d23.75088939467473!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8beb29ba7d7%3A0xe54d32f7e44a3e2a!2sXAMPP!5e0!3m2!1sen!2sbd!4v1655712345678!5m2!1sen!2sbd',
                'bn' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.9024424301385!2d90.39108011536295!3d23.75088939467473!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8beb29ba7d7%3A0xe54d32f7e44a3e2a!2sXAMPP!5e0!3m2!1sen!2sbd!4v1655712345678!5m2!1sen!2sbd'
            ],
            'contact_header' => [
                'title' => ['en' => 'Get In Touch', 'bn' => 'যোগাযোগ করুন'],
                'subtitle' => ['en' => 'Have questions? We are here to help and build a resilient tomorrow together.', 'bn' => 'কোনো প্রশ্ন আছে? আমরা সাহায্য করতে এবং একসঙ্গে একটি শক্তিশালী আগামী তৈরি করতে প্রস্তুত।'],
                'badge' => ['en' => 'Contact Us', 'bn' => 'যোগাযোগ'],
                'bg_image' => 'https://images.unsplash.com/photo-1596524430615-b46475ddff6e?auto=format&fit=crop&w=1920&q=80'
            ],
            'footer_description' => [
                'en' => 'Rooted in Earth Care, People Care, and Fair Share. Our platform empowers local organic agriculture and dynamic ecological learning circles globally.',
                'bn' => 'আর্থ কেয়ার, পিপল কেয়ার এবং ফেয়ার শেয়ার-এ নিহিত। আমাদের প্ল্যাটফর্ম স্থানীয় জৈব কৃষি এবং গতিশীল পরিবেশগত শিক্ষা বৃত্তকে বিশ্বব্যাপী ক্ষমতায়ন করে।'
            ]
        ];

        $settings = [];
        foreach ($defaultKeys as $key => $defaultValue) {
            $setting = PortalSetting::firstOrCreate(
                ['key' => $key],
                ['value' => $defaultValue]
            );
            $settings[$key] = $setting->value;
        }

        if (isset($settings['contact_header']['bg_image']) && !str_starts_with($settings['contact_header']['bg_image'], 'http')) {
            $settings['contact_header']['bg_image'] = asset('storage/' . $settings['contact_header']['bg_image']);
        }

        return Inertia::render('Admin/CMS/Contact', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update Contact CMS settings.
     */
    public function updateContact(Request $request): RedirectResponse
    {
        $this->checkAccess();

        $request->validate([
            'contact_phone' => 'required|array',
            'contact_phone.en' => 'required|string',
            'contact_phone.bn' => 'required|string',
            'contact_address' => 'required|array',
            'contact_address.en' => 'required|string',
            'contact_address.bn' => 'required|string',
            'contact_email' => 'required|array',
            'contact_email.en' => 'required|string',
            'contact_email.bn' => 'required|string',
            'contact_facebook' => 'required|array',
            'contact_facebook.en' => 'required|string',
            'contact_facebook.bn' => 'required|string',
            'contact_twitter' => 'required|array',
            'contact_twitter.en' => 'required|string',
            'contact_twitter.bn' => 'required|string',
            'contact_youtube' => 'required|array',
            'contact_youtube.en' => 'required|string',
            'contact_youtube.bn' => 'required|string',
            'contact_google_map' => 'required|array',
            'contact_google_map.en' => 'required|string',
            'contact_google_map.bn' => 'required|string',
            'footer_description' => 'required|array',
            'footer_description.en' => 'required|string',
            'footer_description.bn' => 'required|string',

            'contact_header' => 'required|array',
            'contact_header.title' => 'required|array',
            'contact_header.title.en' => 'required|string',
            'contact_header.title.bn' => 'required|string',
            'contact_header.subtitle' => 'required|array',
            'contact_header.subtitle.en' => 'required|string',
            'contact_header.subtitle.bn' => 'required|string',
            'contact_header.badge' => 'required|array',
            'contact_header.badge.en' => 'required|string',
            'contact_header.badge.bn' => 'required|string',
            'contact_header_bg' => 'nullable|image|max:2048',
        ]);

        foreach ($request->only([
            'contact_phone',
            'contact_address',
            'contact_email',
            'contact_facebook',
            'contact_twitter',
            'contact_youtube',
            'contact_google_map',
            'footer_description',
        ]) as $key => $value) {
            PortalSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->handleHeaderUpdate($request, 'contact_header', 'contact_header_bg');

        return redirect()->back()->with('success', 'Contact settings updated successfully.');
    }
}
