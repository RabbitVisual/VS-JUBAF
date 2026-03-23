<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fontes para preload (first paint)
    |--------------------------------------------------------------------------
    |
    | Lista de fontes (basename com ou sem .woff2) que serão preloaded via
    | @preloadFonts. Mantenha apenas as usadas no first paint para evitar
    | o aviso "preloaded but not used within a few seconds".
    |
    | Após `npm run build`, confira public/build/manifest.json: se houver
    | chaves terminando em .woff2, use o basename aqui (ex.: inter-v20-latin-regular.woff2
    | ou inter-v20-latin-regular). Se o manifest não listar fontes, o preload
    | não será injetado (comportamento esperado com Laravel Vite).
    |
    | Refs: AGENTS.md, system_default.md (fontes 100% locais).
    |
    */

    'only' => [
        'inter-v20-latin-regular',
        'inter-v20-latin-600',
    ],

];
