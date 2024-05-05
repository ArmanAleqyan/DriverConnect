@extends('admin.layouts.default')
<link rel="shortcut icon" href="{{asset('Лого.png')}}"/>
@section('title')
    Логин
@endsection

<style>
    input{
        color: white !important;
    }

</style>


{{--    <div class="container-scroller">--}}
{{--        <div class="container-fluid page-body-wrapper full-page-wrapper">--}}
{{--            <div class="row w-100 m-0">--}}
{{--                <div style="background-image: url({{asset('doroga-more-traktor.jpg')}})" class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">--}}
{{--                    <div class="card col-lg-4 mx-auto">--}}
{{--                        <div class="card-body px-5 py-5">--}}
{{--                            <h3 class="card-title text-left mb-3">Вход</h3>--}}
{{--                            <form method="post" action="{{route('logined')}}" >--}}
{{--                                @csrf--}}
{{--                                <div class="form-group">--}}
{{--                                    <label>Эл.почта *</label>--}}
{{--                                    <input  name="email" type="text" class="form-control p_input"  value="{{old('login')}}" required>--}}
{{--                                    @if(session('login'))--}}
{{--                                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>--}}
{{--                                        <script>--}}
{{--                                            $(document).ready(function () {--}}

{{--                                                setTimeout(function(){--}}
{{--                                                    document.getElementById('emailerror').style.display = 'none';--}}
{{--                                                }, 10000);--}}
{{--                                            });--}}
{{--                                        </script>--}}
{{--                                        <p id="emailerror" style=" color: #fe8765;">Неверная эл.почта</p>--}}
{{--                                    @endif--}}
{{--                                </div>--}}

{{--                                <div class="form-group">--}}
{{--                                    <label>Пароль *</label>--}}
{{--                                    <input name="password" type="password" class="form-control p_input" required>--}}
{{--                                    @if(session('password'))--}}
{{--                                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>--}}
{{--                                        <script>--}}
{{--                                            $(document).ready(function () {--}}

{{--                                                setTimeout(function(){--}}
{{--                                                    document.getElementById('passworderror').style.display = 'none';--}}
{{--                                                }, 10000);--}}
{{--                                            });--}}
{{--                                        </script>--}}
{{--                                        <p id="passworderror" style=" color: #fe8765;">Неверный пароль</p>--}}
{{--                                    @endif--}}
{{--                                </div>--}}


{{--                                <div class="text-center">--}}
{{--                                    <button type="submit" style="    background: #fa806b;" class="btn  btn-block enter-btn">Войти</button>--}}
{{--                                </div>--}}


{{--                            </form>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <!-- content-wrapper ends -->--}}
{{--            </div>--}}
{{--            <!-- row ends -->--}}
{{--        </div>--}}
{{--        <!-- page-body-wrapper ends -->--}}
{{--    </div>--}}
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Driver</b>Connect</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form action="{{route('logined')}}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input style="    color: black !important;" type="email" name="email" class="form-control" placeholder="Email">

                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>

                </div>
                @if(session('login'))
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                    <script>
                        $(document).ready(function () {

                            setTimeout(function(){
                                document.getElementById('emailerror').style.display = 'none';
                            }, 10000);
                        });
                    </script>
                    <p id="emailerror" style=" color: #fe8765;">Неверная эл.почта</p>
                @endif

                <div class="input-group mb-3">
                    <input style="    color: black !important;" type="password" class="form-control" name="password" placeholder="Password">

                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>

                </div>
                @if(session('password'))
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                    <script>
                        $(document).ready(function () {

                            setTimeout(function(){
                                document.getElementById('passworderror').style.display = 'none';
                            }, 10000);
                        });
                    </script>
                    <p id="passworderror" style=" color: #fe8765;">Неверный пароль</p>
                @endif
                <div class="row">
{{--                    <div class="col-8">--}}
{{--                        <div class="icheck-primary">--}}
{{--                            <input type="checkbox" id="remember">--}}
{{--                            <label for="remember">--}}
{{--                                Remember Me--}}
{{--                            </label>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <!-- /.social-auth-links -->

{{--            <p class="mb-1">--}}
{{--                <a href="forgot-password.html">I forgot my password</a>--}}
{{--            </p>--}}
{{--            <p class="mb-0">--}}
{{--                <a href="register.html" class="text-center">Register a new membership</a>--}}
{{--            </p>--}}
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{asset('admin/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('admin/dist/js/adminlte.min.js')}}"></script>
</body>
