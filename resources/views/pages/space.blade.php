@extends('layouts.app')

@section('content')
<script type="text/javascript" src={{ url('js/space.js') }} defer></script>
<script type="text/javascript" src={{ url('js/comment.js') }} defer></script>
@php
$user = \App\Models\User::findOrFail($space->user_id);

@endphp
<div class="flex-container">
    @include('partials.sidebar')
    <div class="content">
        <script>
            window.spaceUserId = "{{ $space->user_id }}";
        </script>
        @include ('partials.spaceCard')

        @include ('partials.commentCard')
    </div>
    @include('partials.sideSearchbar')
</div>
@include('partials.footer')
@endsection