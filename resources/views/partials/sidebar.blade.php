@if (Auth::check())
    <div class="sidebar">
        <!-- Sidebar content -->
        @if (Auth::user()->isAdmin(Auth::user()))
            <a href="{{ url('/admin') }}" class="{{ Request::is('admin') ? 'active' : '' }}"><i class="fa-solid fa-user-tie"></i><span class="link-text"> Admin</span></a>
        @endif

        <a href="{{ url('/homepage') }}" class="{{ Request::is('homepage') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> <span class="link-text">Home</span></a>
        <a href="{{ url('/homepage/search') }}" class="{{ Request::is('homepage/search') ? 'active' : '' }}"><i class="fa-solid fa-magnifying-glass"></i><span class="link-text">Explore</span></a>
        <a href="{{ url('/profile/' . Auth::user()->id) }}" class="{{ Request::is('profile/*') ? 'active' : '' }}"><i class="fa-solid fa-user"></i> <span class="link-text">Profile</span></a>
        <a href="{{ url('/messages') }}" class="{{ Request::is('messages') ? 'active' : '' }}"><i class="fa-solid fa-envelope"></i> <span class="link-text">Messages</span></a>
        <a href="{{ url('/group') }}" class="{{ Request::is('group') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> <span class="link-text">Groups</span></a>
        <a href="{{ url('/about') }}" class="{{ Request::is('about') ? 'active' : '' }}"><i class="fa-solid fa-circle-info"></i> <span class="link-text">About Us</span></a>
        <a href="{{ url('/features') }}" class="{{ Request::is('features') ? 'active' : '' }}"><i class="fa-solid fa-star"></i> <span class="link-text">Features</span></a>
        <a href="#" class="{{ Request::is('settings') ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> <span class="link-text">Settings</span></a>
    </div>
@else
    <div class="sidebar">
        <!-- Sidebar content -->
        <a href="{{ url('/login') }}"><i class="fa-solid fa-house"></i> Home</a>
        <a href="{{ url('/login') }}"><i class="fa-solid fa-compass"></i> Explore</a>
        <a href="{{ url('/login') }}"><i class="fa-solid fa-user"></i> Profile</a>
        <a href="{{ url('/login') }}"><i class="fa-solid fa-envelope"></i> Messages</a>
        <a href="{{ url('/login') }}"><i class="fa-solid fa-users"></i> Groups</a>
        <a href="{{ url('/about') }}" class="{{ Request::is('about') ? 'active' : '' }}"><i class="fa-solid fa-info-circle"></i> About Us</a>
        <a href="{{ url('/login') }}"><i class="fa-solid fa-bell"></i> Notifications</a>
        <a href="{{ url('/login') }}"><i class="fa-solid fa-cog"></i> Settings</a>
        <a href="{{ url('/features') }}" class="{{ Request::is('features') ? 'active' : '' }}"><i class="fa-solid fa-star"></i> <span class="link-text">Features</span></a>

    </div>
@endif