<div class="userinfo" data-id="{{ $user->id }}">

    <div class="profile-container">
        <img src="{{ asset($user->media()) }}" class="profile-img"
            alt="profile media">

        <div class="user-card-container">
            <div class="user-card">
                <div class="user" id="user{{$user->id}}">
                <p><a href="/profile/{{ $user->id }}">{!! '@' . $user->username !!}</a></p>
                    <div class="name">{{ $user->name }}</div>
                        <div class="email">{{ $user->email }}</div>

                </div>
            </div>
                @if(Auth::check())    
                <div class="user-card" >
                    <div class="user-header">
                            <div class="user-extras">
                                <span>Following</span>
                                <a href="/profile/{{$user->id}}/following" style="color: white;">{{ $countFollows }}</a>
                            </div>
                            <div class="user-extras">
                                <span>Followers</span>
                                <a href="/profile/{{$user->id}}/followers" style="color: white;">{{ $countFollowers }}</a>
                            </div>
                            <div class="user-extras">
                            <span>Spaces</span>
                                <span>{{ count($spaces) }}</span>
                            </div>
                    </div>
                </div>
                @endif
            </div>
        @if (Auth::check() && ($user->id == Auth::user()->id || Auth::user()->isAdmin(Auth::user())))
            <div class="button-container-user">
                <button id="editUser{{ $user->id }}" onclick="editUser({{ $user->id }},{{$user->is_public}})" class="button-user-comment">
                    &#9998;
                    <span id="text-config"><i id="text-icon" class="pencil"></i></span>
                </button>

                <button id="cancelEditUser{{ $user->id }}" style="visibility: hidden;"
                    onclick="cancelEditUser({{ $user->id }})">Cancel</button>
                @include ('partials.editUser')
                <button id="deleteProfile{{ $user->id }}" onclick="deleteProfile({{ $user->id }})" class="button-user">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
            @if (Auth::user()->isAdmin(Auth::user()))
            @if(!$isBlocked)
                <form method="POST" action="/profile/block/{{ $user->id }}">
                    @csrf
                    <button type="submit">Block</button>
                </form>
            @else
                <form method="POST" action="/profile/unblock/{{ $user->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Unblock</button>
                </form>
            @endif
            @endif
        @elseif (Auth::check() && Auth::user()->id != $user->id && $user->id != 1)
            <button id="profileState{{ $user->id }}" class="profile-interaction-button"
                onclick="changeProfileState({{ $user->id }},{{ Auth::user()->id }},{{ $user->is_public }})">
                @if (Auth::user()->isFollowing($user))
                    <i id="text-icon" aria-hidden="true"></i> Unfollow
                @elseif(Auth::user()->hasSentFollowRequest($user))
                    <i id="text-icon" aria-hidden="true"></i> Pending
                @else
                    <i id="text-icon" aria-hidden="true"></i> Follow
                @endif
            </button>
        @endif
    </div>
    <div class="card-body">
        <ul class="card-list">
            @if ((Auth::check() && (Auth::user()->isFollowing($user) || $user->is_public == 0 || Auth::user()->id ==
            $user->id)) || Auth::check() && Auth::user()->isAdmin(Auth::user()))
            @foreach ($spaces as $space)
            <li>
                <div class="card">
                    <ul>
                        @php
                        $user = App\Models\User::find($space->user_id);
                        @endphp
                        <li><img src="{{ asset($user->media()) }}" class="profile-img" alt="profile media">
                            <a href="/profile/{{ $space->user_id }}">{{ $user->username }}</a>
                        </li>
                        <li>
                        <span id="space-home-content">
                            <a href="/space/{{ $space->id }}">{{ $space->content }}</a>
                        </span>
                        </li>
                        <li>
                            @if ($space->media())
                            <img src="{{ asset($space->media()) }}" class="space-img"
                                alt="space media">
                            @endif
                        </li>
                        <li>
                        @include('partials.likeSpace')
                        </li>
                    </ul>
                </div>
            </li>
            @endforeach
            @endif

        </ul>
    </div>
</div>

