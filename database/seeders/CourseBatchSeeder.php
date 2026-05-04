<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Batch;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseBatchSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::where('slug', 'permaculture-main')->first();
        if (!$org) return;

        $courses = [
            [
                'title' => ['en' => 'Permaculture Design Course (PDC)', 'bn' => 'পারমাকালচার ডিজাইন কোর্স (পিডিসি)'],
                'description' => ['en' => 'Full PDC certification course.', 'bn' => 'সম্পূর্ণ পিডিসি সার্টিফিকেট কোর্স।'],
                'short_description' => ['en' => 'Intro to PDC.', 'bn' => 'পিডিসি পরিচিতি।'],
                'price' => 5000,
                'level' => 'Foundation',
                'delivery_mode' => 'online',
                'duration' => '72 Hours',
                'status' => 'published',
                'image' => 'https://images.unsplash.com/photo-1585314062340-f1a5a7c9328d?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'title' => ['en' => 'Kitchen Gardening Masterclass', 'bn' => 'কিচেন গার্ডেনিং মাস্টারক্লাস'],
                'description' => ['en' => 'Grow your own food at home.', 'bn' => 'বাড়িতে নিজের খাবার নিজেই ফলান।'],
                'short_description' => ['en' => 'Grow food.', 'bn' => 'খাবার ফলান।'],
                'price' => 0, // Free
                'level' => 'Foundation',
                'delivery_mode' => 'online',
                'duration' => '4 Hours',
                'status' => 'published',
                'image' => 'https://images.unsplash.com/photo-1592419044706-39796d40f98c?auto=format&fit=crop&q=80&w=800',
            ]
        ];

        foreach ($courses as $c) {
            $image = $c['image'];
            unset($c['image']);

            $course = Course::create(array_merge($c, [
                'organization_id' => $org->id,
                'slug' => Str::slug($c['title']['en']),
                'created_by' => 1,
                'is_active' => true,
            ]));
            
            // Note: Since we are using external URLs for now, we'll store them directly in the 'image' field
            // Normally we'd use Storage, but for seeder placeholders this works if the controller handles it.
            $course->update(['image' => $image]);

            // Create a batch for each course
            Batch::create([
                'organization_id' => $org->id,
                'course_id' => $course->id,
                'title' => ['en' => 'Batch #01 - Spring 2024', 'bn' => 'ব্যাচ #০১ - বসন্ত ২০২৪'],
                'start_date' => now()->addDays(7),
                'end_date' => now()->addMonths(3),
                'enrollment_deadline' => now()->addDays(6),
                'capacity' => 50,
                'price' => $course->price,
                'status' => 'upcoming',
                'is_enrollment_open' => true,
            ]);
        }
    }
}
