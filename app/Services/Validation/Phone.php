<?php
namespace App\Services\Validation;

use App\Contracts\Validation;
use App\User;

class Phone implements Validation {

	public function validate($values) {
		$phone = $values['phone_number'] ?? '';
        $code = $values['prefix_code'] ?? '';

		if (!in_array($code, ['977'], false)) {
            return [
                'status' => 'INVALID_PREFIX_CODE',
                'success' => false
            ];
        }

        if (strlen($phone) !== 10) {
            return [
                'status' => 'INVALID_PHONE_NUMBER',
                'success' => false
            ];
        }

        $userWithGivenPhoneNumber = User::where('phone_number', "{$code} {$phone}")->first();

        if ($userWithGivenPhoneNumber) {
            return [
                'status' => 'PHONE_NUMBER_ALREADY_EXISTS',
                'success' => false
            ];
        }

        return [
            'success' => true,
            'status' => 'AVAILABLE',
            'data' => [
                'message' => 'The phone number is available, and can be used.',
            ]
        ];

	}
}