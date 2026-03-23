<?php

return [
    'name' => 'Projection',

    /*
    | Optional viewer token for public screen display (e.g. projector in another room).
    | When set, GET /api/v1/projection/state?viewer_token=YOUR_TOKEN returns state without auth.
    | Leave null to require login for state.
    */
    'viewer_token' => env('PROJECTION_VIEWER_TOKEN'),
];
