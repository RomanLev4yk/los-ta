<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CompanyModel;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $company = CompanyModel::factory()->create([
            'timezone' => 'Europe/Amsterdam',
        ]);

         User::factory()->create([
             'company_id' => $company->id,
             'name' => 'Solar User',
             'email' => 'solar@user.com',
         ]);
    }
}
