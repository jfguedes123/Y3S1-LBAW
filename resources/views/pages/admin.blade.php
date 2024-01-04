@extends('layouts.app')
@section('content')
<script src="{{ asset('js/admin.js') }}" defer></script>
<div class="admin-container">
    @include('partials.sidebar')
    <div class="adminsearch">
        @if (session('success'))
        <p class="success">
            {{ session('success') }}
        </p>
        @endif

        <div class="main-menu admin-options">
            <ul>

                <li><button id="usersButton" onclick="UsersDropDown()"><i class="fa-solid fa-user"></i> Users</button>
                </li>
                <li><button id="spacesButton" onclick="SpacesDropDown()"><i class="fa-solid fa-comment"></i>
                        Spaces</button></li>
                <li><button id="groupsButton" onclick="GroupsDropDown()"><i class="fa-solid fa-users"></i>
                        Groups</button></li>
            </ul>
        </div>
        <div id="adminUsersSearch" class="search-container" style="display: none;">
            <input type="text" id="userSearch" placeholder="User Search...">
            <div id="results-users"></div>
            <div id="createUser" class="admincreate">
                <button onclick="location.href='{{ url('/register') }}'" class="btn btn-primary">Create User</button>
            </div>
        </div>
        <div id="adminSpacesSearch" class="search-container" style="display: none;">
            <input type="text" id="spacesSearch" placeholder="Spaces Search...">
            <div id="results-spaces"></div>
        </div>
        <div id="adminGroupsSearch" class="search-container" style="display: none;">
            <input type="text" id="groupsSearch" placeholder="Groups Search...">
            <div id="results-groups"></div>
        </div>
    </div>
</div>
</div>
@include('partials.footer')
@endsection