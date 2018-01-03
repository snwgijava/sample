<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;

class SessionsController extends Controller
{
    //
        //未登录用户只能访问登录页面
    public function __construct(){
        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }
    public function create(){
        return view('sessions.create');
    }

    public function store(Request $request){
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        //attempt 方法会接收一个数组来作为第一个参数，该参数提供的值将用于寻找数据库中的用户数据,第二个参数是添加记住我的功能
        if (Auth::attempt($credentials,$request->has('remember'))){
            //登录成功的相关操作
            if(Auth::user()->activated) {
               session()->flash('success', '欢迎回来！');
               return redirect()->intended(route('users.show', [Auth::user()]));
           } else {
               Auth::logout();
               session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
               return redirect('/');
           }
        }else{
            //登录失败的相关操作
            session()->flash('danger','很抱歉，您的邮箱和密码不匹配');
            return redirect()->back();
        }

    }

    public function destroy(){
        Auth::logout();
        session()->flash('success','您已成功退出！');
        return redirect('login');
    }
}
