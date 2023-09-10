<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsersProfile;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DetailProfileController extends Controller
{
    public function AddFollower(Request $request){
        $datas = [
            'phone_number' => $request->input('phone_number'),
        ];

        UsersProfile::updateOrInsert(['id_users' => $idUsers], $datas);
    }

    public function DetailProfile(Request $request){

        $data = DB::table('users')
                ->select(
                    'users.id AS id',
                    'users.name AS name',
                    'users.email AS email',
                    'users.username AS username',
                    'users_profile.id AS profile_id',
                    'users_profile.phone_number AS profile_phone_number',
                    'users_profile.first_name AS profile_first_name',
                    'users_profile.last_name AS profile_last_name',
                    'users_profile.date_of_birth AS profile_date_of_birth',
                    'users_profile.image AS profile_image',
                    'total_follower',
                    'total_following'
                )
                ->leftJoinSub(
                    DB::table('followorfollowing')
                        ->select(
                            'id_users',
                            DB::raw('SUM(CASE WHEN kategori = "follower" THEN 1 ELSE 0 END) AS total_follower'),
                            DB::raw('SUM(CASE WHEN kategori = "following" THEN 1 ELSE 0 END) AS total_following')
                        )
                        ->where('id_users', 2)
                        ->groupBy('id_users'),

                    'follower_counts',
                    'follower_counts.id_users',
                    '=',
                    'users.id'
                )
                ->join('users_profile', 'users_profile.id_users', '=', 'users.id')
                ->where('users.id', auth()->user()->id)
                ->get();

        return response()->json(
            [
                "status"=> "success",
                "message"=> "Data retrieved successfully",
                "data" => $data,
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
            'image_profile' => 'required|max:2048|image|mimes:jpeg,png,jpg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    "status"=> "fail",
                    "message"=> $validator->errors(),
                    "data" => "",
                ],400);
        }

        try {

            //create folder
            $path = public_path().'/photo_profile/';
            Storage::makeDirectory($path, $mode = 0711, true, true);

            //Upload Photo Profile
            $fileNewName = 'profile_'.$request->input('first_name').'_'.rand().'_'.auth()->user()->id.'.'.pathinfo($request->file('image_profile')->getClientOriginalName(), PATHINFO_EXTENSION).''; 
            $image = $request->file('image_profile')->move(public_path('photo_profile'), $fileNewName);

            $datas = [
                'phone_number' => $request->input('phone_number'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'date_of_birth' => $request->input('date_of_birth'),
                'image' => asset('photo_profile/'.$fileNewName),
            ];

            UsersProfile::updateOrInsert(['id_users' => $idUsers], $datas);
            $userProfile = UsersProfile::where('id_users', $idUsers)->first();//ambil data
            
            return response()->json([
                    "status"=> "success",
                    "message"=> "Data store successfully",
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
