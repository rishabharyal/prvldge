<?php

namespace App\Http\Controllers;

use App\Contracts\File;
use App\FeedDates;
use App\Memory;
use App\MemoryAttachment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\Memory as MemoryService;
use Illuminate\Support\Facades\Validator;

class MemoryController extends Controller
{
    private MemoryService $memory;

    public function __construct(MemoryService $memory)
    {
        $this->memory = $memory;
    }

    public function index(Request $request) {
        return response()->json($this->memory->get($request->get('user_id')));
    }

    public function store(Request $request, File $file) {
        $validator = Validator::make($request->only(['caption', 'photo', 'visibility', 'type']), [
            'caption' => 'required|max:60',
            'photo' => 'required',
            'visibility' => 'required|boolean',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'VALIDATION_FAILED',
                'data' => $validator->errors()
            ]);
        }

        return response()->json($this->memory->create(
            $request->get('caption'),
            $request->get('type'),
            $request->get('visibility') ?? 0,
            $request->get('photo'),
            $file
        ));

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

        $validator = Validator::make($request->only(['caption']), [
            'caption' => 'required|max:60',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'status' => 'VALIDATION_FAILED',
                'data' => $validator->errors()
            ];
        }

        return response()->json(
            $this->memory->update($id, $request->get('caption'))
        );
    }

    public function destroy(int $id) {
        return response()->json(
            $this->memory->delete($id)
        );
    }
}
