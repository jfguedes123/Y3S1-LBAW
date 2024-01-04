@extends('layouts.app')

@section('content')
<div class="recover-container">
    <div class="recover-card">
<form class="content" method="POST" action="/send">
    @csrf
    <label for="name">Your name</label>
    <input id="name" type="text" name="name" placeholder="Name" required>
    <label for="email">Your email</label>
    <input id="email" type="email" name="email" placeholder="Email" required>
    <button type="submit">Send</button>
</form>
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

@if (session('message'))
    <div class="alert alert-info">
        {{ session('message') }}
    </div>
@endif

@if (session('details'))
    <div class="alert alert-warning">
        Missing variables:
        <ul>
            @foreach (session('details') as $detail)
                <li>{{ $detail }}</li>
            @endforeach
        </ul>
    </div>
@endif
</div>
</div>
@include('partials.footer')
@endsection