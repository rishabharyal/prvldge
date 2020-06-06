<?php

namespace App\Http\Controllers;

use App\Contracts\File;
use App\FeedDates;
use App\Memory;
use App\MemoryAttachment;
use App\Structures\StructFile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class MemoryController extends Controller
{
    public function index(Request $request) {
        if (!$request->has('user_id')) {
            return response()->json([
                'status' => 'MISSING_USER_ID_PARAM'
            ], 404);
        }

        $user = User::find($request->get('user_id'));

        if (!$user) {
            return response()->json([
                'status' => 'USER_NOT_FOUND'
            ], 403);
        }

        if (!Gate::allows('list-memories', $user)) {
            return response()->json([
                '401' => 'UNAUTHORIZED_ACTION'
            ], 404);
        }

        $memories = Memory::where('user_id', $user->id)->where('visibility', 1)->paginate(12);

        return response()->json($memories);

    }

    public function store(Request $request, File $file) {
        $this->validate($request, [
            'caption' => 'required|max:60',
            'photo' => 'require|image',
            'visibility' => 'required',
            'date' => 'required|date'
        ]);

        $memory = new Memory(); // need is_persisted column in memories table, because sometime data is saved but not attachment...
        $memory->user_id = Auth::id();
        $memory->caption = $request->get('caption');
        $memory->visibility = $request->get('visibility') ?? 0;
        $memory->memory_at = $request->get('date');
        $memory->type = $request->get('image') ?? 'image';
        $memory->save();
        $fileInfo = $file->save($request->get('image')); // This either returns StructFile or exception..

        MemoryAttachment::create([
            'memory_id' => $memory->id,
            'file_url' => $fileInfo->url,
            'type' => $fileInfo->type,
            'storage' => $fileInfo->storage
        ]);

        // Add to FeeDDates table..
        $postId = ",{$memory->id}";

        $latestFeedDate = Auth::user()->postDates()->latest()->first();
        if (!$latestFeedDate) {
            $latestFeedDate = new FeedDates();
            $latestFeedDate->user_id = Auth::id();
            $postId = $memory->id;
        }

        $latestFeedDate->post_dates .= $postId;
        $latestFeedDate->save();

        return response()->json($memory, 201);

    }

    public function show(Memory $memory) {

        if (!$memory) {
            return response()->json([
                'status' => 'MEMORY_NOT_FOUND'
            ], 404);
        }

        if (!Gate::allows('see-memory', $memory)) {
            return response()->json([
                '401' => 'UNAUTHORIZED_ACTION'
            ], 404);
        }


        return response()->json($memory, 201);

    }

    public function update(Memory $memory, Request $request) {
        if (!$memory) {
            return response()->json([
                'status' => 'MEMORY_NOT_FOUND'
            ], 404);
        }

        if (!Gate::allows('see-memory', $memory)) {
            return response()->json([
                '401' => 'UNAUTHORIZED_ACTION'
            ], 404);
        }

        $memory->memory_at = $request->get('date');
        $memory->caption = $request->get('caption');
        $memory->save();

        return response()->json('success');

    }

    public function destroy(Memory $memory) {
        if (!$memory) {
            return response()->json([
                'status' => 'MEMORY_NOT_FOUND'
            ], 404);
        }

        if (!Gate::allows('delete-memory', $memory)) {
            return response()->json([
                '401' => 'UNAUTHORIZED_ACTION'
            ], 404);
        }

        $memory->delete();
        return response()->json('success');
    }
}
