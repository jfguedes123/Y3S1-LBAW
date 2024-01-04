@extends('layouts.app')
@section('content')
<script type="text/javascript" src={{ url('js/message.js') }} defer></script>
<div class="flex-container">
    @include('partials.sidebar')
    @include('partials.messageCard')
    @include('partials.sideSearchbar')
</div>

@include('partials.footer')
@endsection