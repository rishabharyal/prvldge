<?php

namespace App\Http\Controllers;

use App\Services\Detect\Detect;
use App\Services\Token;
use App\Traits\NormallyUsedMethods;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{

    use NormallyUsedMethods;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function checkPhone(Request $request) {
        $phone = $request->get('phone_number');
        $code = $request->get('prefix_code');

        if (!in_array($code, ['977'], false)) {
            return response()->json([
                'status' => 'INVALID_PREFIX_CODE',
                'success' => false
            ]);
        }

        if (strlen($phone) !== 10) {
            return response()->json([
                'status' => 'INVALID_PHONE_NUMBER',
                'success' => false
            ]);
        }

        $userWIthGivenPhoneNumber = User::where('phone_number', $phone)->first();

        if ($userWIthGivenPhoneNumber) {
            return response()->json([
                'status' => 'PHONE_NUMBER_ALREADY_EXISTS',
                'success' => false
            ]);
        }

        return response()->json([
            'status' => 'AVAILABLE',
            'success' => true
        ]);

    }

    public function register(Request $request) {

        if (!$this->isRequestAllowedToProceed($request->server('HTTP_USER_AGENT'))) {
            return response()->json([
                'status' => 'NOT_ALLOWED',
                'success' => false
            ]);
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

        if (!$this->isRequestAllowedToProceed($request->server('HTTP_USER_AGENT'))) {
            return response()->json([
                'status' => 'NOT_ALLOWED',
                'success' => false
            ]);
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
                'code' => 'INVALID_USERNAME',
                'success' => false
            ]);
        }

        $password = $user->password;

        if (!Hash::check($request->get('password'), $password)) {
            return response()->json([
                'status' => '404',
                'code' => 'INVALID_PASSWORD',
                'success' => false
            ]);
        }


        $token = $user->createToken();

        return response()->json([
            'access_token' => $token,
            'name' => $user->name,
            'username' => $user->username,
            'new' => $new,
            'success' => true
        ]);

    }
}
