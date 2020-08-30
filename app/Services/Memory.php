<?php

namespace App\Services;

use App\Contracts\File;
use App\FeedDates;
use App\MemoryAttachment;
use App\Traits\NormallyUsedMethods;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Memory {

	use NormallyUsedMethods;

    public function get($userId) {
        if (!$userId) {
            return [
                'success' => false,
                'status' => 'MISSING_USER_ID_PARAM'
            ];
        }

        $user = User::find($userId);

        if (!$user) {
            return [
                'success' => false,
                'status' => 'USER_NOT_FOUND'
            ];
        }

        if (!Gate::allows('list-memories', $user)) {
            return [
                'success' => false,
                'status' => 'UNAUTHORIZED_ACTION'
            ];
        }

        $memories = \App\Memory::where('user_id', $user->id)->where('visibility', 1)->paginate(12);

        return [
            'success' => true,
            'data' => $memories
        ];
    }

    public function create($caption, $type, $visibility, $photo, File $file) {
        $memory = new \App\Memory(); // need is_persisted column in memories table, because sometime data is saved but not attachment...
        $memory->user_id = Auth::id();
        $memory->caption = $caption;
        $memory->type = $type;
        $memory->visibility = $visibility;
        $memory->save();
        $fileInfo = $file->save($photo); // This either returns StructFile or exception..

        $attachment = MemoryAttachment::create([
            'memory_id' => $memory->id,
            'file_url' => $fileInfo->url,
            'type' => $fileInfo->mime,
            'storage' => $fileInfo->storage
        ]);

        // Add to FeedDates table..
        $postId = ",{$memory->id}";

        $latestFeedDate = Auth::user()->postDates()->latest()->first();
        if (!$latestFeedDate) {
            $latestFeedDate = new FeedDates();
            $latestFeedDate->user_id = Auth::id();
            $postId = $memory->id;
        }

        $latestFeedDate->post_dates .= $postId;
        $latestFeedDate->save();

        return [
            'id' => $memory->id,
            'caption' => $memory->caption,
            'type' => $memory->type,
            'visibility' => $memory->visibility,
            'attachment_url' => $fileInfo->url
        ];
    }

    public function update($id, $caption) {
        $memory = \App\Memory::find($id);
        if (!$memory) {
            return [
                'success' => false,
                'status' => 'MEMORY_NOT_FOUND'
            ];
        }

        if (!Gate::allows('update-memory', $memory)) {
            return [
                'success' => false,
                'status' => 'UNAUTHORIZED_ACTION'
            ];
        }

        $memory->caption = $caption;
        $memory->save();

        return [
            'success' => true
        ];
    }

    public function delete($id) {
        $memory = \App\Memory::find($id);

        if (!$memory) {
            return [
                'success' => false,
                'status' => 'MEMORY_NOT_FOUND'
            ];
        }

        if (!Gate::allows('delete-memory', $memory)) {
            return [
                'success' => 'false',
                'code' => '401',
                'action_code' => 'UNAUTHORIZED_ACTION'
            ];
        }

        $memory->delete();
        return [
            'success' => true
        ];
    }


}
