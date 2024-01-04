@forelse ($spaces as $space)
@php 
$user = App\Models\User::find($space->user_id);
@endphp
    <article class="search-page-card" id="space{{ $space->id }}">
        <a href="/space/{{ $space->id }}">
            <p class="space-content search-page-card-space">&#64;{{ $user->username }}</p>
            <p class="search-space-card-content">{{ $space->content }}</p>
        </a>
    </article>
@empty
    <h2 class="no_results"></h2>
@endforelse
