<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (empty(User::where('name', '=', 'testuser')->first())) {
            $u = User::make([
                'name' => 'testuser',
                'email' => 'test@test.test',
                'password' => Hash::make('testpass'),
            ]);

            $u->save();
        } else {
            $this->command->info('Skipping');
        }
    }
}
