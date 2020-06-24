<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PortalLogin extends Authenticatable
{
    protected $fillable = [
        'portal_user_id',
        'salesforce_token',
        'portal_user_details',
        'api_token',
        'expired_at',
        'last_login_at',
        'last_login_ip'
    ];

    protected $dates = [
        'last_login_at'
    ];
}
