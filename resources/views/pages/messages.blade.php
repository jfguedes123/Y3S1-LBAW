@extends('layouts.app')

@section('content')
<main class="flex-container">
    @include('partials.sidebar')
    @include('partials.messagesCard')
    @include('partials.sideSearchbar')
</main>
@include('partials.footer')
@endsection