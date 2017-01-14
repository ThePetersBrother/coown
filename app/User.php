<?php

namespace App;

use SammyK\LaravelFacebookSdk\SyncableGraphNodeTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SyncableGraphNodeTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected static $graph_node_field_aliases = [
        'id'   => 'facebook_user_id',
        'name' => 'full_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'access_token',
    ];
}
