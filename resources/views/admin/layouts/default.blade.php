<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{asset('admin/plugins/fontawesome-free/css/all.min.css')}}">
    <script src="https://kit.fontawesome.com/c9c45b0eac.js" crossorigin="anonymous"></script>

    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('admin/dist/css/adminlte.min.css')}}">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.10/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    @yield('page_css')
</head>
<body class="hold-transition sidebar-mini">
@if(auth()->check())
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{route('HomePage')}}" class="nav-link">Home</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Navbar Search -->
{{--            <li class="nav-item">--}}
{{--                <a class="nav-link" data-widget="navbar-search" href="#" role="button">--}}
{{--                    <i class="fas fa-search"></i>--}}
{{--                </a>--}}
{{--                <div class="navbar-search-block">--}}
{{--                    <form class="form-inline">--}}
{{--                        <div class="input-group input-group-sm">--}}
{{--                            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">--}}
{{--                            <div class="input-group-append">--}}
{{--                                <button class="btn btn-navbar" type="submit">--}}
{{--                                    <i class="fas fa-search"></i>--}}
{{--                                </button>--}}
{{--                                <button class="btn btn-navbar" type="button" data-widget="navbar-search">--}}
{{--                                    <i class="fas fa-times"></i>--}}
{{--                                </button>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </li>--}}

            <!-- Messages Dropdown Menu -->
                        <div class="btn-group" bis_skin_checked="1">
                            <button type="button" class="btn btn-default">{{auth()->user()->name}}</button>
                            <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                                <span class="sr-only">{{auth()->user()->name}}</span>
                            </button>
                            <div class="dropdown-menu" role="menu" bis_skin_checked="1" style="">
                                <a class="dropdown-item" href="{{route('settingView')}}">Акаунт</a>
                                <a class="dropdown-item" href="{{route('logoutAdmin')}}">Выход</a>
                            </div>
                        </div>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{route('HomePage')}}" class="brand-link">
            <img src="{{asset('admin/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Driver Connect</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
{{--            <div class="user-panel mt-3 pb-3 mb-3 d-flex">--}}
{{--                <div class="image">--}}
{{--                    <img src="{{asset('admin/dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">--}}
{{--                </div>--}}
{{--                <div class="info">--}}
{{--                    <a href="#" class="d-block">{{auth()->user()->name}}</a>--}}
{{--                </div>--}}
{{--            </div>--}}

            <!-- SidebarSearch Form -->
{{--            <div class="form-inline">--}}
{{--                <div class="input-group" data-widget="sidebar-search">--}}
{{--                    <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">--}}
{{--                    <div class="input-group-append">--}}
{{--                        <button class="btn btn-sidebar">--}}
{{--                            <i class="fas fa-search fa-fw"></i>--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->
                    {{--https://fontawesome.com/v4/icon/key--}}
         @php
                 $routeName = \Illuminate\Support\Facades\Route::currentRouteName();
         @endphp

                    <li class="nav-item">
                        <a href="{{route('HomePage')}}" class="nav-link @if($routeName == 'HomePage' ) active @endif">
                            <i class="nav-icon fa-solid fa-chart-line"></i>
                            <p>
                                Сводка
                            </p>
                        </a>
                    </li>
                    <li class="nav-item  @if($routeName== 'new_users'  || $routeName == 'all_users' || $routeName == 'single_page_user')  menu-is-opening menu-open @endif ">
                        <a href="#" class="nav-link ">
                            <i class="nav-icon fa fa-users"></i>
                            <p>
                               Пользватели
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview ">

                            <li class="nav-item">
                                <a href="{{route('new_users')}}" class="nav-link  @if($routeName == 'new_users' ) active @endif">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Заявки</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('all_users')}}" class="nav-link  @if($routeName == 'all_users' ) active @endif">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Зарегистрированные</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        @php
                            $routes = ['all_jobs','single_page_job'];
                        @endphp


                        <a href="{{route('all_jobs')}}" class="nav-link {{ in_array($routeName, $routes) ? 'active' : '' }}">
                            <i class="nav-icon fa-solid fa-route"></i>
                            <p>
                                Поездки
                                {{--                                                                <span class="right badge badge-danger">New</span>--}}
                            </p>
                        </a>
                    </li>
{{--                    <li class="nav-item">--}}
{{--                                                        <a href="{{route('get_all_regions')}}" class="nav-link @if($routeName == 'get_all_regions' || $routeName == 'single_page_region') active @endif">--}}
{{--                                                            <i class="nav-icon fas fa-th"></i>--}}
{{--                                                            <p>--}}
{{--                                                                Регионы--}}
{{--                                                            </p>--}}
{{--                                                        </a>--}}
{{--                                                    </li>--}}

{{--                    <li class="nav-item">--}}
{{--                        <a href="{{route('all_news_letters')}}" class="nav-link @if($routeName == 'get_all_regions' || $routeName == 'single_page_region') active @endif">--}}
{{--                            <i class="nav-icon  fa-solid fa-envelopes-bulk"></i>--}}
{{--                            <p>--}}
{{--                                Рассылки--}}
{{--                            </p>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                                                    <li class="nav-item">--}}
{{--                                                        <a href="{{route('get_all_key')}}" class="nav-link @if($routeName == 'get_all_key' || $routeName == 'create_key_page' || $routeName =='single_page_key') active @endif">--}}
{{--                                                            <i class="nav-icon fa fa-key"></i>--}}
{{--                                                            <p>--}}
{{--                                                                Ключи--}}
{{--                                                                <span class="right badge badge-danger">New</span>--}}
{{--                                                            </p>--}}
{{--                                                        </a>--}}
{{--                                                    </li>--}}
                    <li class="nav-item">
                        @php
                            $routes = ['all_faqs', 'create_faq_page', 'single_page_faq'];
                        @endphp
                        <a href="{{route('all_faqs')}}" class="nav-link {{ in_array($routeName, $routes) ? 'active' : '' }}">
                            <i class="nav-icon fa fa-question-circle"></i>
                            <p>
                                FAQ
                                {{--                                                                <span class="right badge badge-danger">New</span>--}}
                            </p>
                        </a>
                    </li>

                    @php
                    $routes = [ 'settings_page','single_page_letters'];
                    @endphp
                                                    <li class="nav-item">
                                                        <a href="{{route('settings_page')}}" class="nav-link {{ in_array($routeName, $routes) ? 'active' : '' }}">
                                                            <i class="nav-icon fa fa-cogs"></i>
                                                            <p>
                                                                Настройки
{{--                                                                <span class="right badge badge-danger">New</span>--}}
                                                            </p>
                                                        </a>
                                                    </li>


                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- /.content-header -->

        <!-- Main content -->

        <div class="content">

            <div class="container-fluid">
                <br>
                @yield('content')


                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
        <div class="p-3">
            <h5>Title</h5>
            <p>Sidebar content</p>
        </div>
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
@endif

<script src="{{asset('admin/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{ asset('admin/plugins/select2/js/select2.full.min.js') }}"></script>

<!-- Bootstrap 4 -->
<script src="{{asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{asset('admin/plugins/chart.js/Chart.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('admin/dist/js/adminlte.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
{{--<script src="{{asset('admin/dist/js/demo.js')}}"></script>--}}
<script>

    $(document).ready(function(){
        // Сохранение активного таба при его клике
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

            var activeTab = $(e.target).attr('href'); // Получаем href активного таба
            $.ajax({
                url: "{{ route('saveActiveTab') }}", // Маршрут для сохранения активного таба
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}", // Токен CSRF для безопасности
                    activeTab: activeTab
                }
            });
        });
    });
</script>
@yield('page_scripts')
</body>
</html>

