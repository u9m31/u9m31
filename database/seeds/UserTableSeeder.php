<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        DB::table('users')->insert(
        [
            [
                'name' => 'メイプル　管理者',
                'loginid' => 'maple_admin',
                'password' => Hash::make('password'),
                'role' => 5,
            ],
            [
                'name' => 'メイプル　ユーザ',
                'loginid' => 'maple_user',
                'password' => Hash::make('password'),
                'role' => 10,
            ],
        ]);
        
        factory(App\User::class, 100)->create();
    }
}
