<?php

namespace App\Services\OTP;

use App\OTP as OTPModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OTP
{

	private OTPModel $otpModel;

	public function __construct(OTPModel $otpModel) {
		$this->otpModel = $otpModel;
	}


	public function send($params) {
		$otp = $this->otpModel->where('user_id', Auth::id())->first();
		$code = '666666';
		if (!$otp) {
			$otp = new OTPModel();
			$otp->user_id = Auth::id();
			$otp->otp_hash = Hash::make($code);
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

		return [
			'success' => true,
			'code' => 'OTP_VERIFICATION_SUCCESSFUL'
		];
	}
}