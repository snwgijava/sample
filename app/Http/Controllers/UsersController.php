<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Auth;
use Mail;

class UsersController extends Controller
{
    //
    /*
    __construct 是 PHP 的构造器方法，当一个类对象被创建之前该方法将会被调用。
    我们在 __construct 方法中调用了 middleware 方法，该方法接收两个参数，第一个为中间件的名称，第二个为要进行过滤的动作。
    我们通过 except 方法来设定 指定动作 不使用 Auth 中间件进行过滤，意为 —— 除了此处指定的动作以外，
    所有其他动作都必须登录用户才能访问，类似于黑名单的过滤机制。相反的还有 only 白名单方法，将只过滤指定动作。
    */
    public function __construct(){
        $this->middleware('auth',[
            'except' => ['show', 'create', 'store', 'index','confirmEmail']
        ]);
        //未登录用户只能访问注册页面
        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }

    public function index(){
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }
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
        //实现自动登录
        // Auth::login($user);
        //激活
        $this->sendEmailConfirmationTo($user);
        //可以使用 session() 方法来访问会话实例。而当我们想存入一条缓存的数据，让它只在下一次的请求内有效时，
        //则可以使用 flash 方法。flash 方法接收两个参数，第一个为会话的键，第二个为会话的值，我们可以通过下面这行代码的为会话赋值。

        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect()->route('users.show',[$user]);
    }

    public function edit(User $user){
        //利用了 Laravel 的『隐性路由模型绑定』功能，直接读取对应 ID 的用户实例 $user，未找到则报错；
        //将查找到的用户实例 $user 与编辑视图进行绑定；
        $this->authorize('update', $user);  //加载授权策略，authorize 方法接收两个参数，第一个为授权策略的名称，第二个为进行授权验证的数据
        return view('users.edit',compact('user'));
    }

    public function update(User $user,Request $request){
        $this->validate($request,[
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);
        $this->authorize('update', $user);
        /*
        将用户密码验证的 required 规则换成 nullable，这意味着当用户提供空白密码时也会通过验证，
        因此我们需要对传入的 password 进行判断，当其值不为空时才将其赋值给 data，避免将空白密码保存到数据库中。
        */
        $data = [];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success','个人资料更新成功');

        // $user->update([
        //     'name' => $request->name,
        //     'password' => bcrypt($request->password)
        // ]);

        return redirect()->route('users.show',$user->id);
    }

    public function destroy(User $user){
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success','成功删除用户！');
        return back();
    }

    protected function sendEmailConfirmationTo($user)
   {
       $view = 'emails.confirm';
       $data = compact('user');
       $from = 'aufree@yousails.com';
       $name = 'Aufree';
       $to = $user->email;
       $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

       Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
           $message->from($from, $name)->to($to)->subject($subject);
       });
   }

   public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

}
