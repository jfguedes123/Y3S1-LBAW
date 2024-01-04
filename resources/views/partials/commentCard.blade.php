<div class="comment-card">
    <h3><a href="javascript:void(0);" onclick="editSpace({{ $space->id }})"></a></h3>
    <h4>Comments</h4>

    {{-- Add a form for submitting comments --}}
    @if (Auth::check() &&
            !Auth::user()->isAdmin(Auth::user()) &&
            ($user->is_public == false || Auth::user()->isFollowing($user)))
        <form method="POST" action="/comment/create">
            @csrf
            <input type="hidden" name="space_id" value="{{ $space->id }}">
            <textarea name="content" required></textarea>
            <button type="submit">Submit</button>
        </form>
    @endif
    {{-- Display existing comments --}}
    @if ($space->comments)
        @if ($errors->has('profile'))
            <span class="error">
                {{ $errors->first('profile') }}
            </span>
        @endif
        @foreach ($space->comments as $comment)
            <div id="comment{{ $comment->id }}" class="comment">
                <div class="comment-user">
                    @php
                        $real = \App\Models\User::findOrFail($comment->author_id);
                    @endphp
                    @if ($real->id != 1)
                        <p><a href="/profile/{{ $comment->author_id }}">{{ $comment->username }}</a></p>
                    @else
                        <p>Anonymous</p>
                    @endif
                </div>
                <div class="content">{!! $comment->content !!}</div>
                @if (Auth::check())
                    <button id="likeButton{{ $comment->id }}"
                        onclick="changeLikeStateC({{ $comment->id }}, {{ Auth::check() && Auth::user()->likesComment(Auth::user(), $comment) ? 'true' : 'false' }})">
                        <i id="likeIcon{{ $comment->id }}"
                            class="fa {{ Auth::check() && Auth::user()->likesComment(Auth::user(), $comment) ? 'fa-heart' : 'fa-heart-o' }}"></i>
                        <span id="countCommentLikes{{ $comment->id }}" class="like-count">
                            {{ $comment->likes() }}</span>
                    </button>
                @endif

                {{-- Add delete and edit options for comments if needed --}}
                @if (
                                (Auth::check() && $comment->author_id == Auth::user()->id) ||
                                    (Auth::check() && Auth::user()->isAdmin(Auth::user())))
                                <button id="editComment{{ $comment->id }}" onclick="editComment({{ $comment->id }})"
                                    class="button-comment">&#9998;
                                    <span id="text-config"><i id="text-icon" class="pencil"></i></span>
                                </button>
                                <button id="deleteComment{{ $comment->id }}" onclick="deleteComment({{ $comment->id }})"
                                    class="button-comment">&#10761;
                                    <span><i class="cross"></i></span>
                                </button>
                                <button id="cancelEditComment{{ $comment->id }}"
                                    onclick="cancelEditComment({{ $comment->id }})" style="visibility:hidden;"
                                    class="button-comment">&#10761;
                                    <span><i class="cross"></i></span>
                                </button>
                @endif

            </div>
        @endforeach
    @endif
</div>
