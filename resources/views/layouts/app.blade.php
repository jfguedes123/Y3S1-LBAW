<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <script src="https://kit.fontawesome.com/b10add5646.js" crossorigin="anonymous"></script>
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">

    <link href="{{ asset('css/milligram.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ url('css/user.css') }}" rel="stylesheet">
    <link href="{{ url('css/space.page.css') }}" rel="stylesheet">
    <link href="{{ url('css/register-login.css') }}" rel="stylesheet">
    <link href="{{ url('css/about.css') }}" rel="stylesheet">
    <link href="{{ url('css/admin.css') }}" rel="stylesheet">
    <link href="{{ url('css/partials.css') }}" rel="stylesheet">
    <link href="{{ url('css/groups.css') }}" rel="stylesheet">
    <link href="{{ url('css/search.css') }}" rel="stylesheet">
    <link href="{{ url('css/message.css') }}" rel="stylesheet">
    <link href="{{ url('css/notifications.css') }}" rel="stylesheet">
    <link href="{{ url('css/features.css') }}" rel="stylesheet">
    <script src="https://js.pusher.com/7.0/pusher.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript"></script>
    <script type="text/javascript" src={{ url('js/app.js') }} defer></script>
    <script type="text/javascript" src={{ url('js/config.js') }} defer></script>
    <script type="text/javascript" src={{ url('js/space.js') }} defer></script>
    <script type="text/javascript" src={{ url('js/user.js') }} defer></script>
    <script type="text/javascript" src={{ url('js/admin.js') }} defer></script>
    <script type="text/javascript" src={{ url('js/comment.js') }} defer></script>
    <script type="text/javascript" src={{ url('js/notification.js') }} defer></script>


</head>

<body>
    <main>
        <header>
            <h1>
                <a
                    href="{{ Auth::check() && Auth::user()->isAdmin(Auth::user()) ? url('/admin') : url('/homepage') }}">
                    <img class="logo" src="{{ url('images/sporthub-icon.jpg') }}" alt="Logo">
                    <mark class="sport">Sport</mark><mark class="hub">HUB</mark>
                </a>
            </h1>
            @if (Auth::check())
                
                <div class="button-container">
                    <a class="button" href="{{ url('/profile/' . Auth::user()->id) }}">
                        <span>{{ Auth::user()->name }}</span>
                        <i class="fa-solid fa-user"></i>
                    </a>
                    <a id="notificationsButton" class="button" onclick="showNotifications({{ Auth::user()->id }})">
                        Notifications <i class="fa-solid fa-bell"></i> 
                    </a>
                    <a class="button" href="{{ url('/logout') }}"> Logout <i
                        class="fa-solid fa-right-from-bracket"></i></a>
                </div>
                <div id="notificationsContainer"></div>
            @else
                <div class="button-container">
                <a class="button" href="{{ url('/login') }}"> Login <i class="fa-solid fa-right-to-bracket"></i></a>
                <a class="button" href="{{ url('/register') }}"> Register <i class="fa-solid fa-pen-to-square"></i></a>
                </div>
            @endif
        </header>
        <section id="content">
            @yield('content')
        </section>
    </main>
</body>

</html>
