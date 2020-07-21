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
    		    'success' => false,
                'status' => 'MISSING_USER_ID_PARAM'
            ]);
        }

        $user = User::find($request->get('user_id'));

        if (!$user) {
            return response()->json([
                'success' => false,
                'status' => 'USER_NOT_FOUND'
            ]);
        }

        if (!Gate::allows('list-memories', $user)) {
            return response()->json([
                'success' => false,
                'status' => 'UNAUTHORIZED_ACTION'
            ]);
        }

        $memories = Memory::where('user_id', $user->id)->where('visibility', 1)->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $memories
        ]);

    }

    public function store(Request $request, File $file) {
        $this->validate($request, [
            'caption' => 'required|max:60',
            'photo' => 'required|image',
            'visibility' => 'required|boolean',
            'type' => 'required'
        ]);

        $memory = new Memory(); // need is_persisted column in memories table, because sometime data is saved but not attachment...
        $memory->user_id = Auth::id();
        $memory->caption = $request->get('caption');
        $memory->type = $request->get('type');
        $memory->visibility = $request->get('visibility') ?? 0;
        $memory->save();
        $fileInfo = $file->save($request->get('photo')); // This either returns StructFile or exception..

        MemoryAttachment::create([
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

        return response()->json($memory, 201);

    }

    public function show(int $id) {
        $memory = Memory::find($id);
        if (!$memory) {
            return response()->json([
                'success' => false,
                'status' => 'MEMORY_NOT_FOUND'
            ], 404);
        }

        if (!Gate::allows('see-memory', $memory)) {
            return response()->json([
                'success' => false,
                'status' => 'UNAUTHORIZED_ACTION'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $memory
        ], 200);
    }

    public function update(int $id, Request $request) {
        $memory = Memory::find($id);
        if (!$memory) {
            return response()->json([
                'success' => false,
                'status' => 'MEMORY_NOT_FOUND'
            ], 404);
        }

        if (!Gate::allows('update-memory', $memory)) {
            return response()->json([
                'success' => false,
                'status' => 'UNAUTHORIZED_ACTION'
            ], 404);
        }

        $this->validate($request, [
            'caption' => 'required|max:60'
        ]);

        $memory->caption = $request->get('caption');
        $memory->save();

        return response()->json(['success' => true]);

    }

    public function destroy(int $id) {
        $memory = Memory::find($id);

        if (!$memory) {
            return response()->json([
                'success' => false,
                'status' => 'MEMORY_NOT_FOUND'
            ], 404);
        }
        if (!Gate::allows('delete-memory', $memory)) {
            return response()->json([
                'success' => 'false',
                'code' => '401',
                'action_code' => 'UNAUTHORIZED_ACTION'
            ], 404);
        }

        $memory->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
