<header class="navbar navbar-fixed-top navbar-inverse">
  <div class="container">
    <div class="col-md-offset-1 col-md-10">
      <a href="{{ route('home') }}" id="logo">Sample App</a>
      <nav>
        <ul class="nav navbar-nav navbar-right">
            @if(Auth::check())
            <li><a href="#">用户列表</a></li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      {{ Auth::user()->name }}<b class="caret"></b>
                  </a>
                   <ul class="dropdown-menu">
                       <li>
                           <a href="{{ route('users.show',Auth::user()->id) }}">个人中心</a>
                       </li>
                       <li><a href="#">编辑资料</a></li>
                <li class="divider"></li>
                <li>
                  <a id="logout" href="#">
                      <form action="{{ route('logout') }}" method="POST">
                          {{ csrf_field() }}
                          <!-- 退出操作需要使用 DELETE 请求来发送给服务器。由于浏览器不支持发送 DELETE 请求，因此我们需要使用一个隐藏域来伪造 DELETE 请求。
                          在 Blade 模板中，我们可以使用 method_field 方法来创建隐藏域。 -->
                          {{ method_field('DELETE') }}
                          <button class="btn btn-block btn-danger" type="submit" name="button">退出</button>
                      </form>
                  </a>
           </li>
         </ul>
       </li>
       @else
       <li>
           <a href="{{ route('help') }}">帮助</a>
       </li>
       <li>
           <a href="{{ route('login') }}">登录</a>
       </li>
       @endif
        </ul>
      </nav>
    </div>
  </div>
</header>
