@forelse ($users as $user)
    <article class="search-page-card" id="user{{ $user->id }}">
        <a href="{{ $searchType === 'messages' ? '/messages/' : '/profile/' }}{{ $user->id }}">
            <p class="user-username search-page-card-user">{{ $user->name }}</p>
            <p class="search-user-card-username">&#64;{{ $user->username }}</p>
        </a>
    </article>
@empty
    <h2 class="no_results"></h2>
@endforelse