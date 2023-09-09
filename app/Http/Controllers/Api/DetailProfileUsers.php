<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsersProfile;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
 
class DetailProfileUsers extends Controller
{

    public function AddFollower(Request $request){
        $datas = [
            'phone_number' => $request->input('phone_number'),
        ];

        UsersProfile::updateOrInsert(['id_users' => $idUsers], $datas);
    }

    public function DetailProfile(Request $request){

        $datas = UsersProfile::with('user')->where('id_users',auth()->user()->id)->first();

        return response()->json(
            [
                "status"=> "success",
                "message"=> "Data retrieved successfully",
                "data" => $datas,
            ], 
        200);
    }

    public function AddDetailProfile(Request $request){
        
        $idUsers = auth()->user()->id;
        
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'image_profile' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    "status"=> "fail",
                    "message"=> $validator->errors(),
                    "data" => "",
                ],400);
        }

        try {
            $datas = [
                'phone_number' => $request->input('phone_number'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'date_of_birth' => $request->input('date_of_birth'),
                'image' => asset('storage/photo_profile/'.$request->input('image_profile')),
            ];

            UsersProfile::updateOrInsert(['id_users' => $idUsers], $datas);
            $userProfile = UsersProfile::where('id_users', $idUsers)->first();//ambil data
            
            return response()->json([
                    "status"=> "success",
                    "message"=> "Data retrieved successfully",
                    "data" => $userProfile,
                ], 200);

        } catch (\Exception $e) {

            Log::error('error note: ' . $e->getMessage());

            return response()->json([
                    "status"=> "fail",
                    "message"=> "Terjadi Kesalahan",
                    "data" => "",
                ], 500);
        }   

    }
}
