<?php

namespace App\Http\Controllers;

use App\Services\Detect\Detect;
use App\Services\Token;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $request) {

        if (!$this->isRequestAllowedToProceed($request)) {
            return response()->json([
                'status' => 'NOT_ALLOWED'
            ], 401);
        }

        $this->validate($request, [
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'name' => 'required',
            'birthday' => 'date|required',
            'gender' => 'required|in:m,f,o'
        ]);

        $user = new User();
        $user->name = $request->get('name');
        $user->username = $request->get('username');
        $user->password = Hash::make($request->get('password'));
        $user->birthday = $request->get('birthday');
        $user->gender = $request->get('gender');
        $user->save();


        return $this->login($request, true);

    }

    public function login(Request $request, $new = false) {

        if (!$this->isRequestAllowedToProceed($request)) {
            return response()->json([
                'status' => 'NOT_ALLOWED'
            ], 401);
        }


        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        // We only allow user agent from two sources: iOS & Android for now

        $username = $request->get('username');

        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json([
                'status' => '404',
                'code' => 'INVALID_USERNAME'
            ]);
        }

        $password = $user->password;

        if (!Hash::check($request->get('password'), $password)) {
            return response()->json([
                'status' => '404',
                'code' => 'INVALID_PASSWORD'
            ]);
        }


        $token = $user->createToken();

        return response()->json([
            'access_token' => $token,
            'name' => $user->name,
            'username' => $user->username,
            'new' => $new
        ]);

    }

    private function isRequestAllowedToProceed(Request $request) {
        if ($request->server('HTTP_USER_AGENT') === 'MemoryTest') {
            return true;
        }

        return true;
    }

}
