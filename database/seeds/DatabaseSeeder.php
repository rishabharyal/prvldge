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
        // $this->call('MemoryReplySuggestionSeeder');
        // $this->call('MemoryRepliesSeeder');
        $this->call('UsersTableSeeder');
    }
}
