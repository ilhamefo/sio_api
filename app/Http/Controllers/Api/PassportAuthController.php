<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PassportAuthController extends Controller
{
    public function Register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users',
            'username' => 'required|string|unique:users',
            'password' => 'required|min:8',
        ]);
        
        //tidak boleh ada @ diusername
        $validator->after(function ($validator) use ($request) {
            if (strpos($request->username, '@') !== false) {
                $validator->errors()->add('username', 'Username should not contain the @ symbol.');
            }
        });
  
        if ($validator->fails()) {
            return response()->json([
                    "status"=> "fail",
                    "message"=> $validator->errors(),
                    "data" => "",
                ],400);
        }

        try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email, 
                'username' => $request->username, 
                'password' => bcrypt($request->password)
            ]);
      
            $token = $user->createToken('Sio-Token')->accessToken;

            return response()->json([
                        "status"=> "success",
                        "message"=> "Data retrieved successfully",
                        "data" => $token,
                    ], 200);
        
        } catch (\Exception $e) {
            Log::error('error note: ' . $e->getMessage());

            return response()->json([
                    "status"=> "fail",
                    "message"=> "Terjadi Kesalahan",
                    "data" => $e->getMessage(),
                ], 500);
        }

        
    }

    public function loginUser(Request $request){
        
        if (auth()->attempt($this->CheckUserOrEmail($request->usernameoremail, $request->password))) {
            $token = auth()->user()->createToken('Sio-Token')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function UserInfo(){
 
        $user = auth()->user();
        return response()->json(['user' => $user], 200);
 
    }

    protected function CheckUserOrEmail($useroremail, $password){

        $data = [ 'password' => $password ];

        if (strpos($useroremail, '@') !== false) {
            $data['email'] = $useroremail;
        } else {
            $data['username'] = $useroremail;
        }

       return $data;
    }
 

}
