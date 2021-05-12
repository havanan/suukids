<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = "admin";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'account_id', 'email', 'password', 'avatar', 'sex','skype','prefecture','cmtnd','address',
        'birthday','phone','type'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isSuperAdmin() {
        return $this->type === 0;
    }
    public function isSale()
    {
        return false;
    }
    public function isOnlyMarketing()
    {
        return false;
    }
}
