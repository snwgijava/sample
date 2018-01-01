<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    //消息通知相关功能，Authenticatable是授权相关功能
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *  过滤用户提交的字段，只有包含在该属性中的字段才能够被正常更新
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *对用户密码或其它敏感信息在用户实例通过数组或 JSON 显示时进行隐藏
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
