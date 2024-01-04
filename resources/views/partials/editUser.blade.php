<div class="edit-user-container">
    <div class="edit-user-card">
        <button id="openEditUserModal" class="fas fa-edit">Edit User</button>
        <div id="editUserModal" class="modal">
        <div class="modal-content">
        <span class="close">&times;</span>
        <form method="POST" action="{{ url('profile/edit') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="edit-page-photo-options">
                <div class="image"><i class="fa-solid fa-image"></i> Choose a profile picture:</div>
                <img id="edit-profile-photo" class="edit-page-image" src="{{ Auth::user()->media() }}"
                    alt="Profile image">
                <input type="file" name="image" id="image">
            </div>
            <label for="password"><i class="fa-solid fa-lock"></i> Current Password</label>
        @if (Auth::user()->isAdmin(Auth::user()))
            <input id="oldPassword" type="password" name="oldPassword">
        @else
            <input id="oldPassword" type="password" name="oldPassword" >
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
            <input id="password" type="password" name="password" >
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
            <input type="hidden" name="id" value="{{ request()->route('id') }}">

            <div>
                <button type="submit">
                    Edit <i class="fa-solid fa-pen-to-square"></i>
                </button>
            </div>
        </form>
    </div>
    </div>
    </div>
</div>
