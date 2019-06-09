<?php

use Illuminate\Database\Seeder;
use App\Player;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Player::firstOrCreate(['name' => 'Computer']);
    }
}
