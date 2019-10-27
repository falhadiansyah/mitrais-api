<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrRoles = [
            ['name' => 'Admin', 'description' => 'Administrator'], 
            ['name' => 'Web Developer', 'description' => 'Web Developer'],
            ['name' => 'User Developer', 'description' => 'User Developer'],
        ];

        foreach ($arrRoles as $key => $role) {
            $role['created_at'] = Carbon::now();
            $role['updated_at'] = Carbon::now();

            DB::table('roles')->insert($role);
        }
    }
}
