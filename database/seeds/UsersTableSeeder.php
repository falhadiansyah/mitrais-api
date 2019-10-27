<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrUsers = [
            ['first_name' => 'Admin', 'last_name' => 'user', 'phone' => '081234567890', 'email' => 'admin@mailinator.com', 'role_id' => 1, 'password' => 'secret@123', 'birth_date' => '2000-01-01'],
            ['first_name' => 'Web', 'last_name' => 'developer', 'phone' => '081234567891', 'email' => 'webdev@mailinator.com', 'role_id' => 2, 'password' => 'secret@123', 'birth_date' => '2000-01-01'],
        ];

        foreach ($arrUsers as $key => $user) {
            $user['password'] = bcrypt($user['password']);
            $user['confirmation_code'] = str_random(20);
            $user['confirmed_at'] = Carbon::now();
            $user['created_at'] = Carbon::now();
            $user['updated_at'] = Carbon::now();

            DB::table('users')->insert($user);
        }
    }
}
