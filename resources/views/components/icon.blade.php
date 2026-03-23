@props([
    'name',
    'style' => 'duotone',
    'class' => '',
])

@php
    $styleMap = [
        'duotone' => 'fa-duotone',
        'solid' => 'fa-solid',
        'regular' => 'fa-regular',
        'light' => 'fa-light',
        'thin' => 'fa-thin',
        'brands' => 'fa-brands',
    ];

    $faStyle = $styleMap[$style] ?? $styleMap['duotone'];

    // Normalize: if value is "fa-solid fa-water" or similar, use only short name for fa-{name}
    if (str_contains((string) $name, ' ') || str_starts_with((string) $name, 'fa-')) {
        $parts = preg_split('/\s+/', trim((string) $name));
        foreach ($parts as $p) {
            if (str_starts_with($p, 'fa-')) {
                $name = substr($p, 3);
            }
        }
    }
@endphp

<i {{ $attributes->merge(['class' => "$faStyle fa-$name $class"]) }} aria-hidden="true"></i>
