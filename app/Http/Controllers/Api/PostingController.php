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
use App\Models\Likes;
use App\Models\Comments;

class PostingController extends Controller
{   

    public function AddLike(Request $request, $postingId){
    
        // Cari postingan
        $post = Posting::find($postingId);

        if (!$post) {
            return response()->json([ "status"=> "fail", "message"=> "Post not found","data" => ""],404);
        }

        $alReady = Likes::where('user_id', auth()->user()->id)->where('post_id', $post->id)->first();

        if ($alReady) {
            $alReady->delete();//hapus jika sudah like
            return response()->json([ "status"=> "success", "message"=> "Delete successfully","data" => ""],200);
        } else { //like baru

            try{

                $like = new Likes();
                $like->user_id = auth()->user()->id;
                $like->post_id = $post->id;
                $like->save();
                return response()->json([ "status"=> "success", "message"=> "Like store successfully","data" => ""],200);
            
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
    
    public function AddPostingImage(Request $request){

        $validator = Validator::make($request->all(), [
            'caption' => 'required|string',
            'photo_galery.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([ "status"=> "fail","message"=> $validator->errors(),"data" => ""],400);
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

            return response()->json(["status"=> "success","message"=> "Data store successfully", "data" => ""],200);
       
        } catch (\Exception $e) {

            Log::error('error note: ' . $e->getMessage());

            return response()->json([
                    "status"=> "fail",
                    "message"=> "Server error",
                    "data" => "",
                ],500);
        }
      
    }

    public function Postcomments(Request $request, $postingId){

        // Cari postingan
        $post = Posting::find($postingId);

        if (!$post) {
            return response()->json(["status"=> "fail","message"=> "Post not found","data" => ""],404);
        }

        $validator = Validator::make($request->all(), [
            'comments' => 'required|string'
        ]);

        try{
            $comment = new Comments();
            $comment->post_id = $post->id;
            $comment->user_id = auth()->user()->id;
            $comment->comment_text = $request->comments;
            $comment->save();

            $lastComment = Comments::latest()->select('comment_text','post_id')->first();

            return response()->json(["status"=> "success","message"=> "Data store successfully","data" => $lastComment],200);
       
        } catch (\Exception $e) {

            Log::error('error note: ' . $e->getMessage());

            return response()->json(["status"=> "fail", "message"=> "Server error","data" => ""],500);
        }

    }

    public function GetPosting(){

        //keluanran postingan|comments|images|totallike|totalcomments
        $Postingans = Posting::with(
            ['PostingImages' => function ($query) {
                $query->select('post_id', 'image_path', 'created_at');
            },
            'Commentss' => function ($query) {
                $query->select('post_id', 'user_id', 'comment_text','created_at');
            }
        ])->withCount(['Likess','Commentss'])->where('user_id', auth()->user()->id)->get();

        return response()->json(
            [
                "status"=> "success",
                "message"=> "Data retrieved successfully",
                "data" => $Postingans,
            ], 
        200);

    }

}
