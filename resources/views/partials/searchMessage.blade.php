@forelse ($users as $user)
    <article class="search-page-card" id="user{{ $user->id }}">
    <a href="{{ $searchType === 'messages' ? '../messages/' . Auth::user()->id . '-' : '../profile/' }}{{ $user->id }}">
        <p class="user-username search-page-card-user">{{ $user->name }}</p>
        </a>
        <p class="search-user-card-username">&#64;{{ $user->username }}</p>
    </article>
@empty
    <p class="no_results"></p>
@endforelse