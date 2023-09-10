<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function Posting()
    {
        return $this->hasMany(Posting::class,'user_id');
    }

    public function Commentss()
    {
        return $this->hasMany(Comments::class,'user_id');
    }

    public function UsersProfilee()
    {
        return $this->hasOne(UsersProfile::class, 'id_users');
    }

    public function Followorfollowingg()
    {
        return $this->belongsToMany(Followorfollowing::class, 'id_users');
    }

    public function following() {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id');
    }

    // users that follow this user
    public function followers() {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id');
    }

    public function toggleFollow($idTargets)
    {
        $user = $this->following()->find($idTargets);
        if ($user) {
            return 'followed';
        } else {
            return 'unfollowed';
        }
    }

}
