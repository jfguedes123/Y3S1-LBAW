@forelse ($messages as $message)
    <article class="search-page-card" id="user{{ $message->id }}"> <!-- Change $user to $message -->
        <a href="../messages/{{ $message->id }}">
            <h2 class="user-username search-page-card-user"> {{ $message->name }}</h2> <!-- Change $user to $message -->
        </a>
        <h3 class="search-user-card-username">&#64;{{ $message->username }}</h3> <!-- Change $user to $message -->
    </article>
@empty
    <h2 class="no_results"></h2>
@endforelse
