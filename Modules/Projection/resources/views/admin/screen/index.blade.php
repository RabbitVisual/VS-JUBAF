@php
    $projectionStateUrl = url('/api/v1/projection/state');
    $projectionStateCredentials = 'same-origin';
    $projectionCanPostState = true;
@endphp
@include('projection::screen.canvas')
