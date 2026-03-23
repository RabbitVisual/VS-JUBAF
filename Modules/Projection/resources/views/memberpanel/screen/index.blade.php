@php
    $viewerToken = request()->query('viewer_token');
    $useViewer = !empty($viewerToken);
    $projectionStateUrl = $useViewer ? url('/api/v1/projection/viewer/state') . '?viewer_token=' . urlencode($viewerToken) : url('/api/v1/projection/state');
    $projectionStateCredentials = $useViewer ? 'omit' : 'same-origin';
    $projectionCanPostState = !$useViewer;
@endphp
@include('projection::screen.canvas')
