<div class="group-sidebar">
    <div id="members" class="members-card">
        <h2>Members</h2>
        @if (
            (Auth::check() && Auth::user()->id == $group->user_id) ||
                (Auth::check() && Auth::user()->isAdmin(Auth::user())) ||
                $group->hasMember(Auth::user()))
            @foreach ($members as $member)
            <div class="member" id="member-{{ $member->user_id }}">                    
                @php
                        $user = \App\Models\User::findOrFail($member->user_id);
                    @endphp
                    <div class="member-container">
                        <p>{{ $user->username }}</p>
                        @if ((Auth::check() && Auth::user()->id == $group->user_id) || (Auth::check() && Auth::user()->isAdmin(Auth::user())))
                            @if ($member->user_id != $group->user_id)
                                <button onclick="deleteMember({{ $member->user_id }})" class="button-member-delete"><i
                                        class="fa-solid fa-trash"></i></button>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p>This group is private.</p>
        @endif
    </div>

    @if ((Auth::check() && Auth::user()->id == $group->user_id) || (Auth::check() && Auth::user()->isAdmin(Auth::user())))
        <div class="joinrequest-card">
            <h2>Join Requests</h2>
            @foreach ($joins as $join)
            <div class="join" id="join-{{ $join->user_id }}">
                    @php
                        $user = \App\Models\User::findOrFail($join->user_id);
                    @endphp
                    <p>{{ $user->username }}</p>
                    <!-- Add a cross icon next to each join request -->
                    @if (Auth::check() && (Auth::user()->id == $group->user_id || Auth::user()->isAdmin(Auth::user())))
                        <button onclick="declineJoin({{ $join->user_id }}, {{ $join->group_id }})"
                            class="button-join-delete"><i class="fa-solid fa-trash"></i>
                        </button>
                        <button onclick="acceptJoin({{ $join->user_id }}, {{ $join->group_id }})"
                            class="button-join-accept"> &#10003;
                            <div><i class="tick"></i></div>
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="invitegroup-container">
            <h2>Invite your friends!</h2>
            <form action="{{ url('/group/invite') }}" method="POST">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">
                <input type="email" name="email" placeholder="Enter email">
                <button type="submit">Invite</button>
            </form>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    @endif
</div>
