@php
    $user = \App\Models\User::findOrFail($group->user_id);
@endphp
<div id="group{{ $group->id }}" data-group-id="{{ $group->id }}" class="group-card">
    <div class="group-header">
        <img src="{{ asset($user->media()) }}" class="profile-img" width="10%" style="border-radius: 50%; padding: 1em"
            alt="profile media">
        <div class="groupauthor"><a href="/profile/{{ $user->id }}">{{ $user->username }}</a></div>
    </div>
    <div class="groupcontent-card">
        <div class="groupname" data-original-name="{{ $group->name }}">{{ $group->name }}</div>
        <div class="groupcontent" data-original-description="{{ $group->description }}">
            {{ $group->description }}</div>
    </div>
    <div class="button-container-group">
        @if ((Auth::check() && $group->user_id == Auth::user()->id) || (Auth::check() && Auth::user()->isAdmin(Auth::user())))
            <button id="deleteGroup{{ $group->id }}" onclick="deleteGroup({{ $group->id }})"
                class="button-group-comment">
                <i class="fa-solid fa-trash"></i>
            </button>
        @endif

        @if ((Auth::check() && $group->user_id == Auth::user()->id) || (Auth::check() && Auth::user()->isAdmin(Auth::user())))
            <button id="editGroup{{ $group->id }}" onclick="editGroup({{ $group->id }})"
                class="button-group-comment">&#9998;
                <div id="text-config"><i id="text-icon" class="pencil"></i></div>
            </button>
            <button id="cancelEditGroup{{ $group->id }}" onclick="cancelEditGroup({{ $group->id }})"
                style="visibility:hidden;" class="button-group-comment">&#10761;
                <div><i class="cross"></i> </div>
            </button>
        @endif

        <section id="buttons" class="buttons">
            @if (Auth::check() && Auth::user()->id != $group->owner_id)
                <button id="groupState{{ $group->id }}" class="group-interaction-button"
                    onclick="changeGroupState({{ $group->id }},{{ Auth::user()->id }},{{ $group->is_public }})"
                    data-status="{{ $group->hasMember(Auth::user()) ? 'member' : 'non-member' }}">
                    @if ($group->hasMember(Auth::user()))
                        <i id="text-icon" aria-hidden="true"></i> Leave Group
                        <button id="fav{{ $group->id }}"
                            onclick="isFavorite({{ Auth::user()->id }}, {{ $group->id }})"
                            class="{{ Auth::user()->isFavorite(Auth::user(), $group) ? 'group-interaction-button fa fa-star' : 'group-interaction-button fa fa-star-o' }}"></button>
                    @else
                        @if (Auth::user()->hasSentJoinRequest($group->id))
                            <i id="text-icon" aria-hidden="true"></i> Pending
                        @else
                            <i id="text-icon" aria-hidden="true"></i> Join Group
                        @endif
                    @endif
                </button>
            @endif
        </section>
    </div>
    @if (Auth::check() && $group->hasMember(Auth::user()))


        @if (session('success'))
            <p class="success">
                {{ session('success') }}
            </p>
        @endif

        <div class="group-chat">

            @if ($spaces)
                @foreach ($spaces as $space)
                    @php
                        $spaceAuthor = \App\Models\Space::findOrFail($space->id);
                        $username = \App\Models\User::findOrFail($space->user_id);
                    @endphp
                    <div class="space-card">
                        <div class="spaceauthor"><a href="/space/{{ $space->id }}">{{ $username->username }}</a>
                        </div>
                        <div class="spacecontent">{{ $space->content }}</div>
                    </div>
                @endforeach
            @endif
        </div>

        <h3><a href="javascript:void(0);" onclick="editGroup({{ $group->id }})"></a></h3>
        <i id="text-icon{{ $group->id }}" aria-hidden="true"></i>
        <div class="add-space-group-card">
            <form method="POST" action="{{ url('space/add') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">

                <div class="space-input-container">
                    <input id="content" type="text" name="content" placeholder="Enter space content"
                        style="color: white;" required autofocus>
                    <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                </div>
            </form>
        </div>
    @endif
</div>
