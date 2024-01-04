<div id=likeSpace>
    @if (Auth::check())
        <button id="likeButton{{ $space->id }}"
            onclick="changeLikeState({{ $space->id }}, {{ Auth::check() && Auth::user()->likesSpace(Auth::user(), $space) ? 'true' : 'false' }}, {{ Auth::user()->id }},{{ $space->user_id }})">
            <i id="likeIcon{{ $space->id }}"
                class="fa {{ Auth::check() && Auth::user()->likesSpace(Auth::user(), $space) ? 'fa-heart' : 'fa-heart-o' }}"></i>
            <span id="countSpaceLikes{{ $space->id }}" class="like-count"> {{ $space->likes() }}</span>
        </button>
    @endif
    @if ((Auth::check() && $space->user_id == Auth::user()->id) || (Auth::check() && Auth::user()->isAdmin(Auth::user())))
        <button id="deleteSpace{{ $space->id }}" onclick="deleteSpace({{ $space->id }})"
            class="button-space-comment">&#10761;
            <span><i class="cross"></i></span>
        </button>
    @endif
</div>
