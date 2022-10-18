<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psy\Util\Json;
use \Illuminate\Support\Facades\Validator;

class userApiController extends Controller
{
    /**
     * userLogin method
     * @param $request->email, $request->password
     * @return Json [message, access_token]
     */
    public function userLogin(Request $request){
        if($request->isMethod('post')){
            $data=$request->all();

            // validation
            $rule=[
                'email'     => 'required|email|exists:users',
                'password'  => 'required',
            ];
            $customMessage=[
                'email.required'    => 'Email is required',
                'email.email'       => 'Email must be a valid mail',
                'email.exists'      => 'Email does not exist',
                'password.required' => 'Password is required',
            ];
            $validation= Validator::make($data,$rule,$customMessage);
            if($validation->fails()){
                return response()->json($validation->errors(),422);
            }

            $user=new User();
            $userDetails= User::where('email',$data['email'])->where('password',$data['password'])->first();
            if($userDetails){
                $access_token=$user->createToken($userDetails->email)->accessToken;
                User::where('email',$userDetails->email)->update(['access_token'=>$access_token]);
                $message="User Successfully Login";
                return response()->json(['message'=>$message,'access_token'=>$access_token],200);
            }
            else{
                $message="User login Fail!";
                return response()->json(['message'=>$message],422);
            }
        }
    }
}
