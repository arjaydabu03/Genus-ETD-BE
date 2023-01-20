<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

// use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_code',
        'account_name',
        'location_code',
        'location',
        'department_code',
        'department',
        'company_code',
        'company',
        'scope_id',
        'type',
        'mobile_no',
        'username',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',

    ];

    public function scope()
    {
        return $this->belongsTo(ScopeController::class)->select('id','name');
    }

 
}
