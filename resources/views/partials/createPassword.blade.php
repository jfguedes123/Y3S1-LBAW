@extends('layouts.app')

@section('content')
<div class="resetPassword-container">
    <div class="resetPassword-card">
        <form method="POST" action="{{ url('/login/createPassword') }}">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="PUT">

                <label for="email"><i class="fa-solid fa-square-envelope"></i> E-Mail Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                @if ($errors->has('email'))
                    <span class="error">
                        {{ $errors->first('email') }}
                    </span>
                @endif
                <label for="token"><i class="fa-solid fa-key"></i> Token</label>
                <input id="token" type="text" name="token" required autofocus>
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
    Recover <i class="fa-solid fa-pen-to-square"></i>
</button>
</form>
</div>
</div>
@endsection