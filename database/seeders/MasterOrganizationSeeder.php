<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\Course;
use App\Models\Batch;
use App\Models\User;

class MasterOrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create the Master Organization
        $masterOrg = Organization::firstOrCreate(
            ['slug' => 'permaculture-main'],
            [
                'name' => 'Permaculture Main',
                'email' => 'admin@permaculture.test',
                'status' => 'active',
                'approved_at' => now(),
            ]
        );

        // 2. Backfill Courses
        Course::withoutGlobalScopes()
            ->whereNull('organization_id')
            ->update(['organization_id' => $masterOrg->id]);

        // 3. Backfill Batches
        Batch::withoutGlobalScopes()
            ->whereNull('organization_id')
            ->update(['organization_id' => $masterOrg->id]);

        // 4. Backfill All Users to the Master Organization
        User::withoutGlobalScopes()
            ->whereNull('organization_id')
            ->update(['organization_id' => $masterOrg->id]);

        $this->command->info('Master organization created and existing data backfilled successfully.');
    }
}
