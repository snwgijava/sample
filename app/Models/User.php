<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;
use Auth;

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


/**
*为 gravatar 方法传递的参数 size 指定了默认值 100；
*通过 $this->attributes['email'] 获取到用户的邮箱；
*使用 trim 方法剔除邮箱的前后空白内容；
*用 strtolower 方法将邮箱转换为小写；
*将小写的邮箱使用 md5 方法进行转码；
*将转码后的邮箱与链接、尺寸拼接成完整的 URL 并返回；
*/
    public function gravatar($size = '100'){

        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    //一个用户可以有多条微博，一对多关联
    public function statuses(){
        return $this->hasMany(Status::class);
    }

    //微博排序
    public function feed(){
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        array_push($user_ids, Auth::user()->id);
        return Status::whereIn('user_id',$user_ids)->with('user')->orderBy('created_at','desc');
    }

    //粉丝列表,belongsToMany 方法的第三个参数 user_id 是定义在关联中的模型外键名，而第四个参数 follower_id 则是要合并的模型外键名。
    public function followers(){
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }
    //被关注人列表
    public function followings(){
        return $this->belongsToMany(User::class,'followers','follower_id','user_id');
    }

    //关注用户
    public function follow($user_ids){
        //is_array 用于判断参数是否为数组，如果已经是数组，则没有必要再使用 compact 方法
        if (!is_array($user_ids)){
            $user_ids = compact('user_ids');
        }
        //sync方法会自动获取数组中的 id
        $this->followings()->sync($user_ids,false);
    }
    //取消关注
    public function unfollow($user_ids){
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    //是否关注了当前用户
    public function isFollowing($user_id){
        return $this->followings->contains($user_id);
    }

}
