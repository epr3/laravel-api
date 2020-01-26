<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Notifications\Notifiable;
use Dyrynda\Database\Support\GeneratesUuid;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use MustVerifyEmail, Notifiable, GeneratesUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'email',
        'password',
        'surname'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAvatarAttribute()
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return $this->attributes['avatar'] ?? "http://www.gravatar.com/avatar/$hash";
    }

    public function uuidColumn()
    {
        return 'id';
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }
}
