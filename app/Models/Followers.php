<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Followers extends Model
{
    use HasFactory;

    protected $table = 'follower';
    
    protected $fillable = [
        'id_follower',
        'id_followee'
    ];
}
