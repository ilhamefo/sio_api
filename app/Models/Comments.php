<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;

    protected $table = 'comments';
    
    protected $fillable = [
        'post_id',
        'user_id',
        'comments_text',
    ];

    public function posting()
    {
        return $this->belongsTo(Posting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
