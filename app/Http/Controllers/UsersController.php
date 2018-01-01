<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    //
    public function create(){
        return view('users.create');
    }

    public function show(User $user){
        return view('users.show', compact('user'));
    }

    public function store(Request $request){
        //required 必填项，unique唯一性验证,confirmed对两次输入的密码进行验证
        $this->validate($request,[
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        //获取到页面传递的值
        // 如果需要获取用户输入的所有数据，可使用：
        // $data = $request->all();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        //可以使用 session() 方法来访问会话实例。而当我们想存入一条缓存的数据，让它只在下一次的请求内有效时，
        //则可以使用 flash 方法。flash 方法接收两个参数，第一个为会话的键，第二个为会话的值，我们可以通过下面这行代码的为会话赋值。
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show',[$user]);
    }

}
