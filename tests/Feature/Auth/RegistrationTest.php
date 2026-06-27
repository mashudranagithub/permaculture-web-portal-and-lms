<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Organization;
use App\Models\Course;
use App\Models\Batch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function createBatch()
    {
        $user = User::factory()->create([
            'is_approved' => true,
        ]);

        $org = Organization::create([
            'name' => 'Test Org',
            'slug' => 'test-org',
            'email' => 'org@example.com',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $course = Course::create([
            'organization_id' => $org->id,
            'slug' => 'test-course',
            'title' => ['en' => 'Test Course', 'bn' => 'Test Course'],
            'description' => ['en' => 'Desc', 'bn' => 'Desc'],
            'short_description' => ['en' => 'Short', 'bn' => 'Short'],
            'price' => 1000,
            'level' => 'Foundation',
            'delivery_mode' => 'online',
            'duration' => '4 Weeks',
            'status' => 'published',
            'created_by' => $user->id,
        ]);

        return Batch::create([
            'organization_id' => $org->id,
            'course_id' => $course->id,
            'title' => ['en' => 'Batch #1', 'bn' => 'Batch #1'],
            'start_date' => now()->addDays(7),
            'end_date' => now()->addMonths(3),
            'enrollment_deadline' => now()->addDays(6),
            'capacity' => 50,
            'price' => 1000,
            'status' => 'upcoming',
            'is_enrollment_open' => true,
        ]);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $batch = $this->createBatch();
        $response = $this->get('/register?batch_id=' . $batch->id);

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $batch = $this->createBatch();
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'batch_id' => $batch->id,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('enrollments.store', ['batch_id' => $batch->id]));
    }
}
