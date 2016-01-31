<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserTableSeeder::class);
        factory(App\Project::class, 20)->create();
        factory(App\UserPledge::class, 20)->create();

        $user = App\User::create([
            'name' => 'Someguy123',
            'email' => 'info@someguy123.com',
            'password' => bcrypt('test')
        ]);
        $user->group = 10;
        $user->save();
    }
}
