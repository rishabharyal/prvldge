<?php


namespace App\Http\Controllers;


use App\MemoryReply;
use App\User;
use http\Env\Request;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\Facades\Validator;

class MemoryRepliesController extends Controller
{

    public function index(Request $request)
    {
        $userId = $request->get('user_id');
        if(!$userId){
            return [
                'success' => false,
                'status' => 'MISSING_USER_ID_PARAM'
            ];
        }
        $replies = MemoryReply::where('user_id', $userId);
        return response()->json([
            'success'=> true,
            'data' => $replies
        ]);
    }

    public function store(Request $request)
    {
        $userId = $request->get('user_id');
        if(!$userId){
            return [
                'success' => false,
                'status' => 'MISSING_USER_ID_PARAM'
            ];
        }
        $validator = Validator::make($request->only(['memory_id','type','comment']),[
            'memory_id'=>'required',
            'type' => 'required',
            'comment'=>'required'
        ]);
        if($validator->fails())
        {
            return [
                'success'=>false,
                'VALIDATION_FAILED',
                'data' => $validator->errors()
            ];
        }
        $replies = new MemoryReply();
        $replies->user_id = $userId;
        $replies->memory_id = $request->get('memory_id');
        $replies->type = $request->get('type');
        $replies->comment = $request->get('comment');
        $replies->save();

        return response()->json([
            'success'=> true,
            'data' => $replies
        ]);
    }


}
