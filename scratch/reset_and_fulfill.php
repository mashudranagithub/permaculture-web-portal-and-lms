<?php

use App\Models\Topic;
use App\Models\TopicProgress;

// 1. Reset Progress
$topicIds = Topic::where('course_id', 2)->pluck('id');
TopicProgress::whereIn('topic_id', $topicIds)->delete();
echo "Progress reset for Course 2.\n";

// 2. Fulfill Content
// Topic 64: Video
$t64 = Topic::find(64);
if ($t64) {
    $t64->update([
        'title' => ['en' => 'Introduction to Kitchen Gardening', 'bn' => 'কিচেন গার্ডেনিং পরিচিতি'],
        'topic_type' => 'video',
        'video_url' => 'https://www.youtube.com/watch?v=R9Z8X6yG2_k',
        'content_body' => ['en' => '<p>Welcome to the Kitchen Gardening Masterclass. In this video, we will cover the basics.</p>', 'bn' => '<p>কিচেন গার্ডেনিং মাস্টারক্লাসে আপনাকে স্বাগতম। এই ভিডিওতে আমরা বেসিক বিষয়গুলো আলোচনা করব।</p>'],
        'estimated_duration' => '10m',
        'is_published' => true
    ]);
}

// Topic 68: Audio
$t68 = Topic::find(68);
if ($t68) {
    $t68->update([
        'title' => ['en' => 'The Secrets of Organic Soil', 'bn' => 'জৈব মাটির রহস্য'],
        'topic_type' => 'audio',
        'audio_file_en' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',
        'content_body' => ['en' => '<p>Listen to this exclusive lecture on soil preparation.</p>', 'bn' => '<p>মাটি তৈরির ওপর এই এক্সক্লুসিভ লেকচারটি শুনুন।</p>'],
        'estimated_duration' => '15m',
        'is_published' => true
    ]);
}

// Topic 66: PDF
$t66 = Topic::find(66);
if ($t66) {
    $t66->update([
        'title' => ['en' => 'Seasonal Planting Guide PDF', 'bn' => 'ঋতুভিত্তিক রোপণ গাইড'],
        'topic_type' => 'pdf',
        'pdf_file_en' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
        'content_body' => ['en' => '<p>Download this guide for your reference.</p>', 'bn' => '<p>আপনার রেফারেন্সের জন্য এই গাইডটি ডাউনলোড করুন।</p>'],
        'estimated_duration' => '20m',
        'is_published' => true
    ]);
}

// Topic 67: Quiz
$t67 = Topic::find(67);
if ($t67) {
    $t67->update([
        'title' => ['en' => 'Soil & Water Quiz', 'bn' => 'মাটি ও পানি কুইজ'],
        'topic_type' => 'quiz',
        'quiz_data' => [
            [
                'question' => ['en' => 'What is the best time to water your kitchen garden?', 'bn' => 'কিচেন গার্ডেনে পানি দেওয়ার সেরা সময় কোনটি?'],
                'options' => [
                    ['en' => 'Early Morning', 'bn' => 'ভোরবেলা'],
                    ['en' => 'High Noon', 'bn' => 'দুপুরবেলা'],
                    ['en' => 'Midnight', 'bn' => 'মধ্যরাত']
                ],
                'correct_option' => 0,
                'points' => 10
            ],
            [
                'question' => ['en' => 'Which component is vital for organic gardening?', 'bn' => 'জৈব বাগানের জন্য কোন উপাদানটি অত্যাবশ্যক?'],
                'options' => [
                    ['en' => 'Chemical Fertilizer', 'bn' => 'রাসায়নিক সার'],
                    ['en' => 'Compost', 'bn' => 'কম্পোস্ট'],
                    ['en' => 'Plastic Mulch', 'bn' => 'প্লাস্টিক মালচ']
                ],
                'correct_option' => 1,
                'points' => 10
            ]
        ],
        'estimated_duration' => '5m',
        'is_published' => true
    ]);
}

// Topic 65: Content
$t65 = Topic::find(65);
if ($t65) {
    $t65->update([
        'title' => ['en' => 'Essential Tools for Gardening', 'bn' => 'বাগানের জন্য প্রয়োজনীয় যন্ত্রপাতি'],
        'topic_type' => 'content',
        'content_body' => ['en' => '<h3>Tools List</h3><ul><li>Hand Trowel</li><li>Pruning Shears</li><li>Watering Can</li></ul>', 'bn' => '<h3>যন্ত্রপাতির তালিকা</h3><ul><li>হ্যান্ড ট্রাওয়েল</li><li>ছাঁটাই কাঁচি</li><li>ঝারি</li></ul>'],
        'estimated_duration' => '12m',
        'is_published' => true
    ]);
}

// Topic 69: Online Class
$t69 = Topic::find(69);
if ($t69) {
    $t69->update([
        'title' => ['en' => 'Live Q&A Session', 'bn' => 'লাইভ প্রশ্নোত্তর সেশন'],
        'topic_type' => 'online_class',
        'content_body' => ['en' => '<p>Join our live session every Friday at 4 PM.</p>', 'bn' => '<p>প্রতি শুক্রবার বিকেল ৪টায় আমাদের লাইভ সেশনে যোগ দিন।</p>'],
        'estimated_duration' => '1h',
        'is_published' => true
    ]);
}

// Topic 70: Assignment
$t70 = Topic::find(70);
if ($t70) {
    $t70->update([
        'title' => ['en' => 'My First Seedling Assignment', 'bn' => 'আমার প্রথম চারা অ্যাসাইনমেন্ট'],
        'topic_type' => 'assignment',
        'content_body' => ['en' => '<p>Upload a photo of your first planted seed.</p>', 'bn' => '<p>আপনার প্রথম রোপণ করা বীজের একটি ছবি আপলোড করুন।</p>'],
        'estimated_duration' => '30m',
        'is_published' => true
    ]);
}

echo "All topics fulfilled for Course 2.\n";
