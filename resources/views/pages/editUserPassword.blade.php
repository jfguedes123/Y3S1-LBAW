@include('layouts.app')

<div class="edit-user-container-password">
    <form method="POST" action="{{ url('profile/editUser/password') }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <label for="password"><i class="fa-solid fa-lock"></i> Current Password</label>
        @if (Auth::user()->isAdmin(Auth::user()))
        <input id="oldPassword" type="password" name="oldPassword">
        @else
        <input id="oldPassword" type="password" name="oldPassword" required>
        @endif
        @if ($errors->has('password'))
        <span class="error">
            {{ $errors->first('password') }}
        </span>
        @endif
        <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
        @if (Auth::user()->isAdmin(Auth::user()))
        <input id="password" type="password" name="password">
        @else
        <input id="password" type="password" name="password" required>
        @endif
        @if ($errors->has('password'))
        <span class="error">
            {{ $errors->first('password') }}
        </span>
        @endif

        <label for="password-confirm"><i class="fa-solid fa-square-check"></i> Confirm Password</label>
        @if (Auth::user()->isAdmin(Auth::user()))
        <input id="password-confirm" type="password" name="password_confirmation">
        @else
        <input id="password-confirm" type="password" name="password_confirmation">
        @endif
        <button type="submit">
            Edit <i class="fa-solid fa-pen-to-square"></i>
        </button>
    </form>
</div>