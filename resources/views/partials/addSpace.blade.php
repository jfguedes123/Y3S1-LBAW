<form method="POST" action="{{ url('space/add') }}" enctype="multipart/form-data" class="addSpace">
    {{ csrf_field() }}
    <label for="content" class="label-color">Create a new Space</label>
    <input id="content" type="text" name="content" placeholder="What are you thinking ..." style="color: white;"
        required autofocus>
    @if ($errors->has('content'))
        <span class="error">
            {{ $errors->first('content') }}
        </span>
    @endif
    <section class="edit-page-photo-options">
        <label for="image">
            Choose a picture: <i class="fa-solid fa-image"></i>
        </label>
        <input type="file" name="image" id="image" hidden>
    </section>
    <label>
        Private Space? <input type="checkbox" name="public" checked>
    </label>
    <button type="submit">
        Post <i class="fa-solid fa-paper-plane"></i>
    </button>
</form>
