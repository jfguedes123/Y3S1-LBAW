@forelse ($groups as $group)
@php 
$user = App\Models\User::find($group->user_id);
@endphp
    <article class="search-page-card" id="group{{ $group->id }}">
        <a href="/group/{{ $group->id }}">
            <p class="group-content search-page-card-group">&#64;{{ $user->username }}</p>
            <p class="search-group-card-content">{{ $group->name }}</p>
        </a>
    </article>
@empty
    <h2 class="no_results"></h2>
@endforelse
