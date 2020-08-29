<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OTP\OTP;

class OTPController extends Controller
{
	private OTP $otpService;

	public function __construct(OTP $otpService) {
		$this->otpService = $otpService;
	}

    public function send(Request $request) {
        return response()->json($this->otpService->send($request->only(['phone_number', 'prefix_code'])));
    }

    public function verify(Request $request) {
    	return response()->json($this->otpService->verify($request->only(['otp_code'])));
    }
}
