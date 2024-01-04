@extends('layouts.app')

@section('content')
    <div class="login-container">
        <div class="login-card">
            <form id="loginForm" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                <label for="email"><i class="fa-solid fa-square-envelope"></i> E-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @if ($errors->has('email'))
                    <span class="error">
                        {{ $errors->first('email') }}
                    </span>
                @endif

                <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                <input id="password" type="password" name="password" required>
                @if ($errors->has('password'))
                    <span class="error">
                        {{ $errors->first('password') }}
                    </span>
                @endif


                <button type="submit">
                    Login <i class="fa-solid fa-right-to-bracket"></i>
                </button>
                <a class="button" href="{{ route('register') }}">Register <i class="fa-solid fa-pen-to-square"></i></a>
                <a class="button" href="{{ route('resetPassword') }}">Reset Password <i class="fa-solid fa-pen-to-square"></i></a>

                @if (session('success'))
                    <p class="success">
                        {{ session('success') }}
                    </p>
                @endif
            </form>

        </div>
    </div>
    @include('partials.footer')
@endsection
