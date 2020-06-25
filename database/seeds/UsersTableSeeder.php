<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@coaching.com'],
            [
                'name' => 'Admin',
                'password' => 'SCP@SSw0D2020',
                'email_verified_at' => now()
            ]);
    }
}
