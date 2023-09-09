<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UsersProfile extends Model
{
    use HasFactory;

    protected $table = 'users_profile';
    
    protected $fillable = [
        'id_users',
        'phone_number',
        'first_name',
        'last_name',
        'date_of_birth',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users');
    }

}
