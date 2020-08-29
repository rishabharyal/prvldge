<?php

namespace App\Services\OTP;

use App\OTP as OTPModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class OTP
{

	private OTPModel $otpModel;

	public function __construct(OTPModel $otpModel) {
		$this->otpModel = $otpModel;
	}


	public function send($params) {
		$validator = Validator::make($params, [
            'prefix_code' => 'required|string|min:3|max:3',
            'phone_number' => 'required|string|min:10|max:10',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'status' => 'VALIDATION_FAILED',
                'data' => $validator->errors()
            ];
        }

		$otp = $this->otpModel->where('user_id', Auth::id())->first();
		$code = '666666';
		if (!$otp) {
			$otp = new OTPModel();
			$otp->user_id = Auth::id();
			$otp->otp_hash = Hash::make($code);
			$otp->prefix_code = $params['prefix_code'];
			$otp->phone_number = $params['phone_number'];
			$otp->sent_times = 1;
			$otp->last_sent_at = Carbon::now();
			$otp->save();

			return [
				'success' => true,
				'code' => 'OTP_SENT'
			];
		}

		$otp->otp_hash = Hash::make($code);
		$otp->sent_times = $otp->sent_times + 1;
		$otp->last_sent_at = Carbon::now();
		$otp->save();

		return [
			'success' => true,
			'code' => 'OTP_RESENT'
		];
	}

	public function verify($params) {
		$validator = Validator::make($params, [
            'otp_code' => 'required|string|min:6|max:6',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'status' => 'VALIDATION_FAILED',
                'data' => $validator->errors()
            ];
        }

		$otp = $this->otpModel->where('user_id', Auth::id())->first();
		if (!$otp) {
			return [
				'success' => false,
				'code' => 'OTP_NOT_SENT_YET'
			];
		}
		$otp->verified_times = $otp->verified_times +1;
		$otp->save();

		if (!Hash::check($params['otp_code'], $otp->otp_hash)) {
			return [
				'success' => false,
				'code' => 'INVALID_OTP'
			];
		}

		$user = Auth::user();
		$user->is_phone_verified = 1;
		$user->phone_number = "{$otp->prefix_code} {$otp->phone_number}";
		$user->save();

		return [
			'success' => true,
			'code' => 'OTP_VERIFICATION_SUCCESSFUL'
		];
	}
}