<?php

namespace App\Models;

use Auth;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Passport\HasApiTokens;

class CUser extends \App\Models\Base\User implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    // Always remember to put necessary traits inside class after defining them below namespace
    // These traits are used by default for user login authentication
    use LaratrustUserTrait {
        LaratrustUserTrait::can insteadof Authorizable;
        Authorizable::can as authorizableCan;
    }
    use Authenticatable,
    Authorizable,
    CanResetPassword,
    Notifiable,
        HasApiTokens;

    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'image',
        'role',
        'editor',
    ];

    // Function get user image from database
    function adminlte_image()
    {

        $getImage = User::find(Auth::user()->id);
        $image = asset('uploads/' . $getImage->image);

        return $image;
    }

    function adminlte_desc()
    {
        return 'Hi, Welcome!';
    }

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d',
        'role' => 'integer',
    ];
}
