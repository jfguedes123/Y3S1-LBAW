<script type="text/javascript" src={{ url('js/groups.js') }} defer></script>
<button id="openFormButton" class="fas fa-plus"></button>

<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form method="POST" action="{{ url('/group/add') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <label for="name" class="Title-color">Create a new Group</label>
            <input id="name" type="text" name="name" placeholder="Write The title of the Group"
                style="color: white;" required autofocus>
            @if ($errors->has('name'))
                <span class="error">
                    {{ $errors->first('name') }}
                </span>
            @endif
            <label for="description">Description</label>
            <input id="description" type="text" name="description" placeholder="Write a description of the Group"
                style="color: white;" required autofocus>
            @if ($errors->has('description'))
                <span class="error">
                    {{ $errors->first('description') }}
                </span>
            @endif
            <label>
                Public Group? <input type="checkbox" name="public" checked>
            </label>
            <button type="submit">
                Create Group
            </button>
        </form>
    </div>
</div>
