<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Friend;

class FriendController extends Controller
{
    private Friend $friendService;

	public function __construct(Friend $friendService) {
		$this->friendService = $friendService;
	}

    public function add(Request $request) {
    	return response()->json($this->friendService->add($request->only(['username'])));
    }
}
