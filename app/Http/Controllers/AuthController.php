<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Authentication;

class AuthController extends Controller
{
    private Authentication $authenticationService;

	public function __construct(Authentication $authenticationService) {
		$this->authenticationService = $authenticationService;
	}

    public function register(Request $request) {
    	return response()->json($this->authenticationService->register($request->only(['username', 'password', 'name', 'birthday', 'gender'])));
    }

    public function login(Request $request) {
    	return response()->json($this->authenticationService->login($request->only(['username', 'password'])));
    }

    public function user() {
        return response()->json($this->authenticationService->getUser());
    }
}
