@extends('layouts.app')

@section('content')
<div class="flex-container">
    @include('partials.sidebar')
    <div class="user-following">
        <div class="search-container">
            <label for="searchc">Search:</label>
            <input type="text" id="searchc" oninput="filterUsers()">
        </div>

        @if (isset($follows) && count($follows) == 0)
        <h2>Nothing to see here</h2>
        @endif

        @foreach ($follows as $follow)
        @php
        $user = \App\Models\User::findOrFail($follow->user_id2);
        @endphp
        <div id="profile-card{{ $user->id }}" class="profile-card">
            <img src="{{ asset($user->media()) }}" class="profile-img" width="10%"
                style="border-radius: 50%; padding: 1em" alt="profile media">
            <h2><a href="/profile/{{ $user->id }}">{{ $user->username }}</a></h2>
            <button id="remove{{ $user->id }}" onclick="stopFollowing({{ $user->id }})"
                class="button-user">Unfollow</button>
        </div>
        @endforeach
    </div>
    @include('partials.sideSearchbar')
</div>
@include('partials.footer')
@endsection