<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Topic::query()->delete();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        
        $courses = Course::all();

        foreach ($courses as $course) {
            $topics = [
                [
                    'title' => ['en' => 'Principles of Permaculture', 'bn' => 'পারমাকালচারের নীতিমালা'],
                    'content_body' => ['en' => '<p>Watch this video to understand the 12 principles of permaculture design.</p>', 'bn' => '<p>পারমাকালচার ডিজাইনের ১২টি নীতিমালা বুঝতে এই ভিডিওটি দেখুন।</p>'],
                    'topic_type' => 'video',
                    'video_url' => 'https://www.youtube.com/watch?v=YRSn-DZTGa4',
                    'order_index' => 1,
                    'estimated_duration' => '15 mins',
                    'is_published' => true,
                    'is_free_preview' => true,
                ],
                [
                    'title' => ['en' => 'Introduction to the Course', 'bn' => 'কোর্সের পরিচিতি'],
                    'content_body' => ['en' => '<p>Welcome to our course! In this lesson, we will cover the basics.</p>', 'bn' => '<p>আমাদের কোর্সে স্বাগতম! এই পাঠে আমরা মৌলিক বিষয়গুলো আলোচনা করব।</p>'],
                    'topic_type' => 'content',
                    'order_index' => 2,
                    'estimated_duration' => '10 mins',
                    'is_published' => true,
                ],
                [
                    'title' => ['en' => 'Design Guide PDF', 'bn' => 'ডিজাইন গাইড পিডিএফ'],
                    'topic_type' => 'pdf',
                    'pdf_file_en' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                    'order_index' => 3,
                    'estimated_duration' => '30 mins',
                    'is_published' => true,
                ],
                [
                    'title' => ['en' => 'Foundational Quiz', 'bn' => 'মৌলিক কুইজ'],
                    'topic_type' => 'quiz',
                    'quiz_data' => [
                        [
                            'question' => ['en' => 'What is Permaculture?', 'bn' => 'পারমাকালচার কি?'],
                            'type' => 'mcq',
                            'options' => [
                                ['en' => 'A way of gardening', 'bn' => 'বাগান করার একটি পদ্ধতি'],
                                ['en' => 'A design system for sustainable living', 'bn' => 'টেকসই জীবনের জন্য একটি ডিজাইন সিস্টেম'],
                                ['en' => 'A type of fertilizer', 'bn' => 'এক ধরণের সার'],
                            ],
                            'correct_answer' => 1,
                            'points' => 10,
                        ],
                        [
                            'question' => ['en' => 'Permaculture only applies to farms.', 'bn' => 'পারমাকালচার কেবল খামারে প্রযোজ্য।'],
                            'type' => 'true_false',
                            'correct_answer' => 1, // False
                            'points' => 5,
                        ]
                    ],
                    'order_index' => 4,
                    'estimated_duration' => '10 mins',
                    'is_published' => true,
                ],
                [
                    'title' => ['en' => 'Soil Health Audio Lecture', 'bn' => 'মাটির স্বাস্থ্য বিষয়ক অডিও লেকচার'],
                    'topic_type' => 'audio',
                    'order_index' => 5,
                    'estimated_duration' => '20 mins',
                    'is_published' => true,
                ],
                [
                    'title' => ['en' => 'Live Q&A Session', 'bn' => 'লাইভ প্রশ্নোত্তর সেশন'],
                    'topic_type' => 'online_class',
                    'order_index' => 6,
                    'estimated_duration' => '60 mins',
                    'is_published' => true,
                ],
                [
                    'title' => ['en' => 'Field Observation Assignment', 'bn' => 'মাঠ পর্যবেক্ষণ অ্যাসাইনমেন্ট'],
                    'topic_type' => 'assignment',
                    'content_body' => ['en' => '<p>Please observe your local environment and submit a report.</p>', 'bn' => '<p>দয়া করে আপনার স্থানীয় পরিবেশ পর্যবেক্ষণ করুন এবং একটি রিপোর্ট জমা দিন।</p>'],
                    'order_index' => 7,
                    'estimated_duration' => '2 hours',
                    'is_published' => true,
                ]
            ];

            foreach ($topics as $t) {
                Topic::create(array_merge($t, ['course_id' => $course->id]));
            }
        }
    }
}
