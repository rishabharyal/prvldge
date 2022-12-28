<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Validation\Phone;

class ValidationController extends Controller
{
    public function checkPhoneAvailability(Request $request, Phone $validator) {
        return response()->json($validator->validate($request->only(['phone_number', 'prefix_code'])));
    }

    public function sendOTP(Request $request) {
    	
    }
}
