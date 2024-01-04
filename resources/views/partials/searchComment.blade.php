@forelse ($comments as $comment)
@php 
$user = App\Models\User::find($comment->author_id);
$space = App\Models\Space::find($comment->space_id);
@endphp
    <article class="search-page-card" id="comment{{ $comment->id }}">
        <a href="/space/{{ $space->id }}">
            <p class="comment-content search-page-card-comment">&#64;{{ $user->username }}</p>
            <p class="search-comment-card-content">{!! $comment->content !!}</p>
        </a>
    </article>
@empty
    <h2 class="no_results"></h2>
@endforelse
