<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posting extends Model
{
    use HasFactory;

    protected $table = 'posts';
    
    protected $fillable = [
        'caption',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function PostingImages()
    {
        return $this->hasMany(PostingImage::class, 'post_id');
    }

    public function Likess()
    {
        return $this->hasMany(Likes::class, 'post_id');
    }

    public function Commentss()
    {
        return $this->hasMany(Comments::class, 'post_id');
    }

}
