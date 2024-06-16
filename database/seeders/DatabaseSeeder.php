<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Company;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        $this->call([
//            RoleSeeder::class,
//            ActivitySeeder::class,
//        ]);
//
//        $company = Company::factory()->create(['id' => 100, 'name' => 'Vodafone']);
//
//        User::factory()->admin()->create([
//            'email' => 'admin@gmail.com',
//            'company_id' => $company->id,
//        ]);
//        User::factory()->companyOwner()->create([
//            'email' => 'owner@gmail.com',
//            'company_id' => $company->id,
//        ]);
//        User::factory()->guide()->create([
//            'email' => 'guide@gmail.com',
//            'company_id' => $company->id,
//        ]);

        Activity::factory(30)->create();

    }
}
