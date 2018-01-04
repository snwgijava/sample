<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['content'];
    //一条微博有一个用户   一对一关联
    public function user(){
        return $this->belongsTo(User::class);
    }
}
