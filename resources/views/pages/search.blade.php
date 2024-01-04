@extends('layouts.app')
@section('content')
<div class="flex-container">
    @include('partials.sidebar')
    @include('partials.searchCard')
    @include('partials.sideSearchPage')
</div>
@include('partials.footer')
@endsection