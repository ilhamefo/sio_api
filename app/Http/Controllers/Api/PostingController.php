<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Posting;
use App\Models\PostingImage;

class PostingController extends Controller
{   

    public function AddLike(Request $request){
        
    }
    
    public function AddPostingImage(Request $request){

        $validator = Validator::make($request->all(), [
            'caption' => 'required|string',
            'photo_galery.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    "status"=> "fail",
                    "message"=> $validator->errors(),
                    "data" => "",
                ],400);
        }

        DB::beginTransaction();

        try{

            $postingan = new Posting;
            $postingan->user_id = auth()->user()->id;
            $postingan->caption = $request->caption;
            $postingan->save();

            foreach ($request->photo_galery as $file) {

                //create folder
                $path = public_path().'/photo_gallery/'.auth()->user()->id.'/';
                File::makeDirectory($path, 0711, true, true);

                // Upload Photo 
                $fileNewName = 'gallery_'.rand().'_'.auth()->user()->id.'.'.pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $file->move(public_path('photo_gallery/'.auth()->user()->id), $fileNewName);
                
                $postImage = new PostingImage;
                $postImage->post_id = $postingan->id;
                $postImage->image_path = asset('photo_gallery/'.auth()->user()->id.'/'.$fileNewName);
                $postImage->save();

            }

            DB::commit();//komit data

            return response()->json([
                    "status"=> "success",
                    "message"=> "Data store successfully",
                    "data" => $posts,
                ],200);
       
        } catch (\Exception $e) {

            Log::error('error note: ' . $e->getMessage());

            return response()->json([
                    "status"=> "fail",
                    "message"=> "Terjadi Kesalahan",
                    "data" => "",
                ],200);
        }
      
    }

    public function GetPosting(){

        $Postingans = Posting::with('PostingImages')->where('user_id', auth()->user()->id)->get();
     
        return response()->json(
            [
                "status"=> "success",
                "message"=> "Data retrieved successfully",
                "data" => $Postingans,
            ], 
        200);

    }

}
