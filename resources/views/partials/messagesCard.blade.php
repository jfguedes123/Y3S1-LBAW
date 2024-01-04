<div class="messages-card">
    <h3>Messages <i class="fa-solid fa-message"></i></h3>

    <div class="searchbar">
        <input type="text" id="search" placeholder="Search..." style="color: white;" pattern="[a-zA-Z0-9\s]+">
        <div id="results-chats"></div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <th>NEW</th>

            <th>CONTINUE</th>
            @foreach ($users as $user)
                @php
                    $real = App\Models\User::findOrFail($user->emits_id);
                    $firstMessage = App\Models\Message::where(function ($query) use ($user) {
                        $query->where('emits_id', $user->emits_id)->where('received_id', Auth::user()->id);
                    })
                        ->orWhere(function ($query) use ($user) {
                            $query->where('emits_id', Auth::user()->id)->where('received_id', $user->emits_id);
                        })
                        ->orderBy('id')
                        ->first();
                @endphp
                @if ($real)
                    <tr>
                        <td class="username">{{ $real->username }}</td>
                        <td>
                            <a href="/messages/{{ $firstMessage->emits_id }}-{{ $firstMessage->received_id }}">Continue
                                Conversation <i class="fa-solid fa-comments"></i></a>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
