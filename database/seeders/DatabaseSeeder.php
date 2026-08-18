<?php

namespace Database\Seeders;

use App\Models\Utility;
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
        \Artisan::call('module:migrate LandingPage');
        \Artisan::call('module:seed LandingPage');

        $routeName = \Request::route() ? \Request::route()->getName() : null;
        if ($routeName != 'LaravelUpdater::database') {
            $this->call(UsersTableSeeder::class);
            $this->call(PlansTableSeeder::class);
        } else {
            Utility::languagecreate();
        }
    }
}
