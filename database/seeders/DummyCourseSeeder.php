<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\Course;
use App\Models\Batch;
use Illuminate\Support\Str;

class DummyCourseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Dummy Organization
        $org = Organization::updateOrCreate(
            ['slug' => 'eco-village-academy'],
            [
                'name' => 'Eco-Village Academy',
                'email' => 'hello@ecovillage.academy',
                'status' => 'active',
                'approved_at' => now(),
            ]
        );

        // 2. Create Course 1
        $course1 = Course::updateOrCreate(
            ['slug' => 'intro-to-permaculture'],
            [
                'organization_id' => $org->id,
                'title' => [
                    'en' => 'Introduction to Regenerative Systems Design',
                    'bn' => 'রিজেনারেটিভ সিস্টেমস ডিজাইনের পরিচিতি'
                ],
                'description' => [
                    'en' => 'Learn the core principles of regenerative systems design and how to apply them to your own land or community.',
                    'bn' => 'রিজেনারেটিভ সিস্টেমস ডিজাইনের মূল নীতিগুলো এবং কীভাবে আপনার নিজের জমি বা সম্প্রদায়ে সেগুলো প্রয়োগ করবেন তা শিখুন।'
                ],
                'short_description' => [
                    'en' => 'A comprehensive guide to regenerative living and ecological design.',
                    'bn' => 'পুনর্জন্মশীল জীবনযাপন এবং পরিবেশগত ডিজাইনের একটি বিস্তৃত নির্দেশিকা।'
                ],
                'price' => 5000,
                'level' => 'Foundation',
                'delivery_mode' => 'online',
                'duration' => '4 Weeks',
                'is_online' => true,
                'is_active' => true,
                'status' => 'published',
                'image' => null, // Placeholder or existing image
                'created_by' => 1,
            ]
        );

        // Create Batch for Course 1
        Batch::updateOrCreate(
            ['course_id' => $course1->id, 'title->en' => 'Summer Intake - Batch 01'],
            [
                'organization_id' => $org->id,
                'title' => [
                    'en' => 'Summer Intake - Batch 01',
                    'bn' => 'গ্রীষ্মকালীন ভর্তি - ব্যাচ ০১'
                ],
                'price' => 4500,
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(37),
                'capacity' => 50,
                'is_enrollment_open' => true,
                'status' => 'upcoming',
            ]
        );

        // 3. Create Course 2
        $course2 = Course::updateOrCreate(
            ['slug' => 'advanced-organic-farming'],
            [
                'organization_id' => $org->id,
                'title' => [
                    'en' => 'Advanced Organic Farming Techniques',
                    'bn' => 'উন্নত জৈব চাষ পদ্ধতি'
                ],
                'description' => [
                    'en' => 'Master advanced soil health management, natural pest control, and high-yield organic farming.',
                    'bn' => 'উন্নত মাটির স্বাস্থ্য ব্যবস্থাপনা, প্রাকৃতিক পোকা দমন এবং উচ্চ-ফলনশীল জৈব চাষে দক্ষতা অর্জন করুন।'
                ],
                'short_description' => [
                    'en' => 'Professional techniques for sustainable and profitable organic agriculture.',
                    'bn' => 'টেকসই এবং লাভজনক জৈব কৃষির জন্য পেশাদার কৌশল।'
                ],
                'price' => 8000,
                'level' => 'Advanced',
                'delivery_mode' => 'hybrid',
                'duration' => '8 Weeks',
                'is_online' => true,
                'is_active' => true,
                'status' => 'published',
                'image' => null,
                'created_by' => 1,
            ]
        );

        // Create Batch for Course 2
        Batch::updateOrCreate(
            ['course_id' => $course2->id, 'title->en' => 'Autumn Cohort - Batch 01'],
            [
                'organization_id' => $org->id,
                'title' => [
                    'en' => 'Autumn Cohort - Batch 01',
                    'bn' => 'শরৎকালীন কোহর্ট - ব্যাচ ০১'
                ],
                'price' => 7500,
                'start_date' => now()->addDays(14),
                'end_date' => now()->addDays(74),
                'capacity' => 30,
                'is_enrollment_open' => true,
                'status' => 'upcoming',
            ]
        );
    }
}
