<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class userTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users=[
            ['name'=>'Anisul', 'email'=>'anis@gmail.com', 'password'=>'12345'],
            ['name'=>'Karim', 'email'=>'karim@gmail.com', 'password'=>'12345'],
            ['name'=>'Hasan', 'email'=>'hasan@gmail.com', 'password'=>'12345']
        ];
        # insert users table
        User::insert($users);
    }
}
