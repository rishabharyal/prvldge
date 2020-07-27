<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemoryReplySuggestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\MemoryReplySuggestion::class, 10)->create();
    }
}
