<div class="search-card">
    <h1><i class="fa-solid fa-magnifying-glass"></i> Explore</h1>
    <form action="{{ url('homepage/search') }}" method="get">
        @csrf
        <div class="searchbar">
            <input type="text" id="search" name="search" placeholder="Search..." style="color: white;"
                pattern="[a-zA-Z0-9\s]+" onclick="showResultsContainer()" onblur="hideResultsContainer()"
                autocomplete="off" required>
            <div class="results-container" id="resultsContainer">
                <div id="results-users"></div>
                <div id="results-spaces"></div>
                <div id="results-groups"></div>
                <div id="results-comments"></div>
            </div>
        </div>
        <div id="filters" style="display: none;">
            <input type="date" id="date" name="date">
            <input type="radio" id="publicRadio" name="profileType" value="anyone"><span
                class="radio-label">Anyone</span>
            <input type="radio" id="privateRadio" name="profileType" value="follow"><span
                class="radio-label">Following</span>
        </div>
        <button type="button" class="filters" onclick="toggleFilters()">Filters <i
                class="fa-solid fa-filter"></i></button>
        <button type="submit">Search <i class="fa-solid fa-magnifying-glass"></i></button>
    </form>

    @if (isset($users) && !empty($users) && $users != '[]')
        <h2>Users</h2>
        <div id="users" class="search-page-results" style="overflow-y: auto;">
            @foreach ($users as $user)
                <p><a href="/profile/{{ $user->id }}">{{ $user->username }}</a></p>
            @endforeach
        </div>
    @endif

    @if (isset($spaces) && !empty($spaces) && $spaces != '[]')
        <h2>Spaces</h2>

        <div id="spaces" class="search-page-results">
            @foreach ($spaces as $space)
                @php
                    $user = App\Models\User::find($space->user_id);
                @endphp
                <div class='space-card'>
                    <img src="{{ asset($user->media()) }}" class="profile-img" width=5%
                        style="border-radius: 50%; padding: 1em" alt="profile media">
                    <div class="spaceauthor"><a href="/profile/{{ $user->id }}">{{ $user->username }}</a></div>
                    <p><a href="/space/{{ $space->id }}">{{ $space->content }}</a></p>
                </div>
            @endforeach
        </div>
    @endif

    @if (isset($comments) && !empty($comments) && $comments != '[]')
        <h2>Comments</h2>
        <div id="comments" class="search-page-results">
            @foreach ($comments as $comment)
                <p><a href="/space/{{ $comment->space_id }}">{!! strip_tags($comment->content) !!}</a></p>
            @endforeach
        </div>
    @endif

    @if (isset($groups) && !empty($groups) && $groups != '[]')
        <h2>Groups</h2>
        <div id="groups" class="search-page-results">
            @foreach ($groups as $group)
                <p><a href="/group/{{ $group->id }}">{{ $group->name }}</a></p>
            @endforeach
        </div>
    @endif
</div>
