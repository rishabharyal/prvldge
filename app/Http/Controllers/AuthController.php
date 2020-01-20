<?php

namespace App\Http\Controllers;

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
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

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

        $token = (new Token($user->id))->generateToken();

        $user->access_token = $token;
        $user->save();

        return response()->json([
            'access_token' => $token,
            'name' => $user->name,
            'username' => $user->username,
            'new' => $new
        ]);

    }

}
