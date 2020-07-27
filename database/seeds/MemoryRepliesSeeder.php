<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemoryRepliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\MemoryReply::class,10)->create();
//        DB::table('memory_replies')
//            ->insert([
//                'user_id'=>1,
//                'memory_id'=>1,
//                'type'=> 'test',
//                'memory_reply_suggestion_id'=>1,
//                'comment'=> 'Hello this is comment'
//            ]);
    }
}
