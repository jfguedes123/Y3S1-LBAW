<div id="space{{ $space->id }}" data-space-id="{{ $space->id }}" class="space-card">
    <img src="{{ asset($user->media()) }}" class="profile-img"
        alt="profile media">
    @if ($user->id != 1)
        <div class="spaceauthor"><a href="/profile/{{ $user->id }}">{{ $user->username }}</a></div>
    @else
        <div class="spaceauthordeleted">Anonymous</div>
    @endif
    <div class="spacecontent">{{ $space->content }}</div>
    <div class="space-img">
        @if ($space->media())
            <img src="{{ asset($space->media()) }}" class="space-img" alt="profile media">
        @endif
    </div>
    @include('partials.likeSpace')
    @include ('partials.editSpace')
    @if (session('success'))
        <p class="success">
            {{ session('success') }}
        </p>
    @endif
</div>
