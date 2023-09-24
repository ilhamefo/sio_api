<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Posting;
use App\Models\Comments;

class BerandaPosting extends Controller
{
    public function BerandaPosting(){

         //keluanran postingan|comments|images|totallike|totalcomments|profile
        $Postingans = Posting::with(
            [
            'user' => function ($query) {
                $query->select('id','username');
            },
            'user.UsersProfilee' => function ($query) {
                $query->select('*');
            },
            'PostingImages' => function ($query) {
                $query->select('post_id', 'image_path', 'created_at');
            },
            'Commentss' => function ($query) {
                $query->select('post_id', 'user_id', 'comment_text','created_at');
            }

        ])->withCount(['Likess','Commentss'])->get();

        return response()->json(
            [
                "status"=> "success",
                "message"=> "Data retrieved successfully",
                "data" => $Postingans,
            ], 
        200);

    }
}
