<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\UsersProfile;

class DetailProfileController extends Controller
{
    public function AddFollower(Request $request){
        $datas = [
            'phone_number' => $request->input('phone_number'),
        ];

        UsersProfile::updateOrInsert(['id_users' => $idUsers], $datas);
    }

    public function DetailProfile(Request $request){

        $data = DB::table('users AS u')
                ->select(
                    'u.id AS id',
                    'u.name AS name',
                    'u.email AS email',
                    'u.username AS username',
                    'up.id AS profile_id',
                    'up.phone_number AS profile_phone_number',
                    'up.first_name AS profile_first_name',
                    'up.last_name AS profile_last_name',
                    'up.date_of_birth AS profile_date_of_birth',
                    'up.image AS profile_image',
                    DB::raw('COUNT(DISTINCT f1.id) AS total_followers'),
                    DB::raw('COUNT(DISTINCT f2.id) AS total_following')
                )
                ->join('users_profile AS up', 'u.id', '=', 'up.id_users')
                ->leftJoin('followers AS f1', 'u.id', '=', 'f1.following_id')
                ->leftJoin('followers AS f2', 'u.id', '=', 'f2.follower_id')
                ->where('u.id', '=', auth()->user()->id)
                ->groupBy(
                    'u.id', 'u.name', 'u.email', 'u.username', 'up.id', 'up.phone_number',
                    'up.first_name', 'up.last_name', 'up.date_of_birth', 'up.image'
                )
                ->first();

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
                    "message"=> "Server error",
                    "data" => "",
                ], 500);
        }   

    }



    public function SearchUsers(Request $request){

        //Pencarian berdasarkan username
        $searchUser = $request->searchUser;

        try{
            $usersDetail = User::where('username', 'like', '%' . $searchUser . '%')
                ->with('UsersProfilee')
                ->get();

            return response()->json(["status"=> "success", "message"=> "Data retrieved successfully","data" => $usersDetail],200);
        } catch (\Exception $e) {

            Log::error('error note: ' . $e->getMessage());

            return response()->json([ "status"=> "fail","message"=> "Server error","data" => ""], 500);
        }      

    }

    public function FollowUnfollow(Request $request){

        try{

            if (auth()->user()->id == $request->id_user_target) {//tidak bisa folow dirisendiri
                return response()->json(["status"=> "success", "message"=> "Can't follow yourself","data" => ""],400);
            }

            $statusFollow = auth()->user()->toggleFollow($request->id_user_target);

            if ($statusFollow === 'followed') {
                auth()->user()->following()->detach($request->id_user_target);
                $fixStatus = 'Unfollow successfully';
            } elseif ($statusFollow === 'unfollowed') {
                auth()->user()->following()->attach($request->id_user_target);
                $fixStatus = 'Follow successfully';
            }

            return response()->json(["status"=> "success", "message"=> $fixStatus,"data" => ""],200);
        
        } catch (\Exception $e) {

            Log::error('error note: ' . $e->getMessage());
            return response()->json([
                    "status"=> "fail",
                    "message"=> "Server error",
                    "data" => "",
                ],500);
        }

    }


}
