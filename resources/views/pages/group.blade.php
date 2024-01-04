@extends('layouts.app')

@section('content')
<script type="text/javascript" src={{ url('js/groups.js') }} defer></script>
<div class="flex-container">
    @include('partials.sidebar')
    @include ('partials.groupCard')
    @if(Auth::user()->id == $group->user_id)
    @include ('partials.groupSideBar')
    @else
    @include ('partials.groupSideBar')
    @endif
</div>
@include('partials.footer')
@endsection