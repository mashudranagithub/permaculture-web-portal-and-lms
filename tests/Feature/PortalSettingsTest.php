<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Organization;
use App\Models\PortalSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        Role::create(['name' => 'Admin', 'slug' => 'admin']);
        Role::create(['name' => 'Organization Admin', 'slug' => 'org-admin']);
    }

    public function test_public_pages_render_successfully(): void
    {
        $this->get('/about')->assertOk();
        $this->get('/terms')->assertOk();
        $this->get('/partners')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/')->assertOk();
    }

    public function test_super_admin_can_access_portal_settings_pages(): void
    {
        $superAdmin = User::factory()->create([
            'is_approved' => true,
            'organization_id' => null,
        ]);
        $superAdmin->roles()->attach(Role::where('slug', 'super-admin')->first()->id);

        $this->actingAs($superAdmin)->get('/admin/cms/homepage')->assertOk();
        $this->actingAs($superAdmin)->get('/admin/cms/about')->assertOk();
        $this->actingAs($superAdmin)->get('/admin/cms/contact')->assertOk();
    }

    public function test_super_admin_with_organization_can_access_portal_settings_pages(): void
    {
        $org = Organization::create([
            'name' => 'Test Org',
            'slug' => 'test-org',
            'email' => 'org@example.com',
            'status' => 'active',
        ]);

        $superAdmin = User::factory()->create([
            'is_approved' => true,
            'organization_id' => $org->id,
        ]);
        $superAdmin->roles()->attach(Role::where('slug', 'super-admin')->first()->id);

        $this->actingAs($superAdmin)->get('/admin/cms/homepage')->assertOk();
        $this->actingAs($superAdmin)->get('/admin/cms/about')->assertOk();
        $this->actingAs($superAdmin)->get('/admin/cms/contact')->assertOk();
    }

    public function test_lms_admin_can_access_portal_settings_pages(): void
    {
        $lmsAdmin = User::factory()->create([
            'is_approved' => true,
            'organization_id' => null,
        ]);
        $lmsAdmin->roles()->attach(Role::where('slug', 'admin')->first()->id);

        $this->actingAs($lmsAdmin)->get('/admin/cms/homepage')->assertOk();
        $this->actingAs($lmsAdmin)->get('/admin/cms/about')->assertOk();
        $this->actingAs($lmsAdmin)->get('/admin/cms/contact')->assertOk();
    }

    public function test_organization_admin_is_denied_access_to_portal_settings(): void
    {
        $org = Organization::create([
            'name' => 'Test Org',
            'slug' => 'test-org',
            'email' => 'org@example.com',
            'status' => 'active',
        ]);

        $orgAdmin = User::factory()->create([
            'is_approved' => true,
            'organization_id' => $org->id,
        ]);
        $orgAdmin->roles()->attach(Role::where('slug', 'admin')->first()->id);

        $this->actingAs($orgAdmin)->get('/admin/cms/homepage')->assertStatus(403);
        $this->actingAs($orgAdmin)->post('/admin/cms/homepage', [])->assertStatus(403);

        $this->actingAs($orgAdmin)->get('/admin/cms/about')->assertStatus(403);
        $this->actingAs($orgAdmin)->post('/admin/cms/about', [])->assertStatus(403);

        $this->actingAs($orgAdmin)->get('/admin/cms/contact')->assertStatus(403);
        $this->actingAs($orgAdmin)->post('/admin/cms/contact', [])->assertStatus(403);
    }

    public function test_super_admin_can_update_settings(): void
    {
        $superAdmin = User::factory()->create([
            'is_approved' => true,
            'organization_id' => null,
        ]);
        $superAdmin->roles()->attach(Role::where('slug', 'super-admin')->first()->id);

        // Update Homepage Settings
        $homePayload = [
            'homepage_hero_title' => ['en' => 'New Title', 'bn' => 'নতুন শিরোনাম'],
            'homepage_hero_description' => ['en' => 'New Desc', 'bn' => 'নতুন বর্ণনা'],
            'counter_courses' => ['en' => '10+', 'bn' => '১০+'],
            'counter_teachers' => ['en' => '25+', 'bn' => '২৫+'],
            'counter_students' => ['en' => '500+', 'bn' => '৫০০+'],
            'counter_batches' => ['en' => '50+', 'bn' => '৫০+'],
        ];
        $this->actingAs($superAdmin)
            ->from('/admin/cms/homepage')
            ->post('/admin/cms/homepage', $homePayload)
            ->assertRedirect('/admin/cms/homepage');

        $this->assertEquals('New Title', PortalSetting::getValue('homepage_hero_title'));

        // Update About Settings
        $aboutPayload = [
            'about_mission' => ['en' => 'New Mission', 'bn' => 'নতুন মিশন'],
            'about_vision' => ['en' => 'New Vision', 'bn' => 'নতুন ভিশন'],
            'about_lms' => ['en' => 'New LMS', 'bn' => 'নতুন এলএমএস'],
            'about_ethics' => [
                [
                    'icon' => 'bi-award',
                    'title' => ['en' => 'Award', 'bn' => 'পুরস্কার'],
                    'description' => ['en' => 'Award text', 'bn' => 'পুরস্কার লেখা']
                ]
            ],
            'terms_content' => ['en' => 'New Terms', 'bn' => 'নতুন শর্তাবলী'],
        ];
        $this->actingAs($superAdmin)
            ->from('/admin/cms/about')
            ->post('/admin/cms/about', $aboutPayload)
            ->assertRedirect('/admin/cms/about');

        $this->assertEquals('New Mission', PortalSetting::getValue('about_mission'));
        $this->assertEquals('bi-award', PortalSetting::getValue('about_ethics')[0]['icon']);
    }
}
