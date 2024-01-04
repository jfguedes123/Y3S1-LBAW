<form method="POST" action="{{ url('/messages/send') }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <label for="content" class="label-color">Create a new Message</label>
    <input id="content" type="text" name="content" placeholder="Write message..." style="color: white;" required autofocus>

    @if ($errors->has('content'))
        <span class="error">
            {{ $errors->first('content') }}
        </span>
    @endif

    <button type="submit">
        Create Message
    </button>
</form>
