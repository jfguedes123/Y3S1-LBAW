@extends('layouts.app')

@section('content')
    <div class="register-container">
        <div class="register-card">
            <form method="POST" action="{{ route('register') }}">
                {{ csrf_field() }}

                <label for="name"><i class="fa-solid fa-user-pen"></i> Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                @if ($errors->has('name'))
                    <span class="error">
                        {{ $errors->first('name') }}
                    </span>
                @endif

                <label for="username"><i class="fa-solid fa-circle-user"></i> Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
                @if ($errors->has('username'))
                    <span class="error">
                        {{ $errors->first('username') }}
                    </span>
                @endif

                <label for="email"><i class="fa-solid fa-square-envelope"></i> E-Mail Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
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

                <label for="password-confirm"><i class="fa-solid fa-circle-check"></i> Confirm Password</label>
                <input id="password-confirm" type="password" name="password_confirmation" required>

                <button type="submit">
                    Register <i class="fa-solid fa-pen-to-square"></i>
                </button>
                
            </form>
        </div>
    </div>
    @include('partials.footer')
@endsection
