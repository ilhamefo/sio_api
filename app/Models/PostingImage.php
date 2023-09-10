<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostingImage extends Model
{
    use HasFactory;

    protected $table = 'post_images';
    
    protected $fillable = [
        'id',
        'post_id',
        'image_path',
    ];

    public function posting()
    {
        return $this->belongsTo(Posting::class, 'post_id');
    }

}
