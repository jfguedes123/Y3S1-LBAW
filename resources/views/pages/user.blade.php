@extends('layouts.app')

@section('title', 'user')
@section('content')
<script type="text/javascript" src={{ url('js/user.js') }} defer></script>

<div class="flex-container">
    @include('partials.sidebar')
    @include('partials.userCard')
    @include('partials.sideSearchbar')

</div>
@include('partials.footer')
@endsection