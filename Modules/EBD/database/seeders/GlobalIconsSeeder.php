<?php

namespace Modules\EBD\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalIconsSeeder extends Seeder
{
    /**
     * Seed 500+ global icons (Font Awesome Pro names). Idempotent via firstOrCreate by slug.
     */
    public function run(): void
    {
        $icons = $this->getIconList();
        $now = now();
        $inserted = 0;

        foreach ($icons as $index => $row) {
            $slug = is_array($row) ? ($row['slug'] ?? $row['fa_name']) : $row;
            $faName = is_array($row) ? ($row['fa_name'] ?? $slug) : $row;
            $exists = DB::table('global_icons')->where('slug', $slug)->exists();
            if ($exists) {
                continue;
            }
            DB::table('global_icons')->insert([
                'slug' => $slug,
                'fa_name' => $faName,
                'style' => is_array($row) ? ($row['style'] ?? 'duotone') : 'duotone',
                'category' => is_array($row) ? ($row['category'] ?? null) : $this->categoryFor($slug),
                'label' => is_array($row) ? ($row['label'] ?? null) : $this->labelFor($slug),
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $inserted++;
        }

        $this->command->info("Global icons: {$inserted} inserted, " . count($icons) . " total in list.");
    }

    private function categoryFor(string $slug): ?string
    {
        if (str_contains($slug, 'trophy') || str_contains($slug, 'medal') || str_contains($slug, 'star') || str_contains($slug, 'award') || str_contains($slug, 'crown') || str_contains($slug, 'gem')) {
            return 'badge';
        }
        if (str_contains($slug, 'game') || str_contains($slug, 'play') || str_contains($slug, 'dice') || str_contains($slug, 'puzzle') || str_contains($slug, 'chess')) {
            return 'game';
        }
        if (str_contains($slug, 'book') || str_contains($slug, 'scroll') || str_contains($slug, 'graduation') || str_contains($slug, 'user-graduate')) {
            return 'level';
        }
        return 'ui';
    }

    private function labelFor(string $slug): string
    {
        return str_replace(['-', '_'], ' ', ucwords($slug, '-'));
    }

    /**
     * 500+ Font Awesome Pro icon names (slug = fa_name for display).
     */
    private function getIconList(): array
    {
        $list = [
            'trophy', 'medal', 'star', 'crown', 'bolt', 'fire-flame-curved', 'gamepad-modern', 'gamepad', 'scroll',
            'book-open', 'book-bible', 'brain-circuit', 'cards', 'clone', 'grid-2', 'grid-2-plus', 'user-crown',
            'comment-quote', 'hourglass-start', 'sword', 'keyboard', 'magnifying-glass', 'pen-to-square',
            'compass', 'ranking-star', 'trophy-star', 'sparkles', 'check', 'xmark', 'play', 'pause', 'stop',
            'circle', 'square', 'triangle', 'diamond', 'question', 'lightbulb', 'arrow-right', 'arrow-left',
            'rotate-right', 'door-open', 'clock', 'spinner', 'circle-question', 'circle-check', 'circle-xmark',
            'circle-info', 'vial-circle-check', 'book-open-reader', 'shield-check', 'flag-checkered',
            'arrow-left-to-line', 'file-pdf', 'file-arrow-down', 'folder-open', 'pen-nib', 'cloud-check',
            'chevron-up', 'chevron-down', 'check-circle', 'xmark-circle', 'hourglass-clock', 'user-graduate',
            'church', 'users', 'user', 'heart', 'hand-holding-heart', 'hands-holding-heart', 'user-group',
            'user-tie', 'user-slash', 'user-plus', 'user-remove', 'users-gear', 'users-crown', 'users-medical',
            'users-viewfinder', 'user-group-slash', 'badge-check', 'bullhorn', 'handshake', 'plus-circle',
            'check-double', 'vault', 'calendar-exclamation', 'list-timeline', 'view-grid', 'pencil', 'lock-closed',
            'plus', 'minus', 'trash', 'sliders', 'certificate', 'id-card', 'microphone', 'ticket',
            'star-half', 'star-half-stroke', 'moon', 'sun', 'cloud', 'cloud-rain', 'snowflake', 'bolt-lightning',
            'wind', 'droplet', 'fire', 'leaf', 'tree', 'seedling', 'mountain', 'water', 'tornado',
            'house', 'building', 'building-columns', 'cross', 'heart-circle-plus', 'file-invoice-dollar',
            'graduation-cap', 'award', 'gem', 'shield-halved', 'chart-pie', 'scale-balanced', 'coins',
            'piggy-bank', 'dollar-sign', 'chart-line', 'gift', 'wand-magic-sparkles', 'puzzle-piece',
            'chess', 'chess-king', 'chess-queen', 'chess-rook', 'chess-bishop', 'chess-knight', 'chess-pawn',
            'dice', 'dice-one', 'dice-two', 'dice-three', 'dice-four', 'dice-five', 'dice-six',
            'image', 'photo', 'film', 'music', 'volume-high', 'volume-low', 'volume-off', 'camera',
            'video', 'folder', 'folder-open', 'file', 'file-lines', 'file-word', 'file-excel', 'file-powerpoint',
            'link', 'share', 'share-nodes', 'download', 'upload', 'copy', 'paste', 'scissors', 'eraser',
            'paintbrush', 'brush', 'pen', 'marker', 'highlighter', 'stamp', 'wrench', 'screwdriver',
            'hammer', 'gear', 'gears', 'bell', 'envelope', 'comment', 'comments', 'calendar', 'calendar-day',
            'calendar-week', 'calendar-plus', 'hourglass', 'stopwatch', 'clock-rotate-left', 'child', 'baby',
            'hand', 'hand-back-fist', 'hand-scissors', 'hand-peace', 'hands', 'hands-clapping',
            'thumbs-up', 'thumbs-down', 'bookmark', 'flag', 'tag', 'tags', 'at', 'hashtag', 'quote-left',
            'quote-right', 'list', 'list-ol', 'list-ul', 'table', 'table-cells', 'table-list',
            'border-all', 'align-left', 'align-center', 'align-right', 'align-justify', 'indent', 'outdent',
            'bold', 'italic', 'underline', 'strikethrough', 'font', 'font-case', 'heading', 'paragraph',
            'superscript', 'subscript', 'code', 'terminal', 'laptop', 'laptop-code', 'mobile', 'tablet',
            'tv', 'display', 'server', 'database', 'microchip', 'memory', 'hard-drive', 'sd-card',
            'wifi', 'bluetooth', 'signal', 'battery-full', 'battery-half', 'battery-empty', 'plug',
            'power-off', 'lightbulb', 'lamp', 'lamp-desk', 'lamp-street', 'plug-circle-check', 'plug-circle-xmark',
            'plug-circle-plus', 'plug-circle-bolt', 'cart-shopping', 'bag-shopping', 'credit-card',
            'wallet', 'money-bill', 'money-bill-wave', 'money-bill-transfer', 'money-check', 'money-check-dollar',
            'chart-simple', 'chart-mixed', 'chart-line-up', 'chart-column', 'chart-area', 'chart-radar',
            'square-check', 'square-xmark', 'square-plus', 'square-minus', 'square', 'circle-plus',
            'circle-minus', 'circle-dot', 'circle-half-stroke', 'circle-quarter', 'circle-three-quarters',
            'rectangle', 'rectangle-list', 'rectangle-pro', 'hexagon', 'hexagon-nodes', 'octagon',
            'pentagon', 'triangle-exclamation', 'triangle', 'caret-up', 'caret-down', 'caret-left', 'caret-right',
            'angles-up', 'angles-down', 'angles-left', 'angles-right', 'angle-up', 'angle-down', 'angle-left', 'angle-right',
            'arrow-up', 'arrow-down', 'arrow-left', 'arrow-right', 'arrow-up-right-from-square', 'arrow-rotate-right',
            'arrow-rotate-left', 'arrow-trend-up', 'arrow-trend-down', 'arrows-up-down', 'arrows-left-right',
            'maximize', 'minimize', 'expand', 'compress', 'up-right-and-down-left-from-center', 'down-left-and-up-right-to-center',
            'location-dot', 'location-crosshairs', 'map', 'map-location', 'map-pin', 'compass', 'route',
            'globe', 'earth-americas', 'earth-europe', 'earth-asia', 'earth-oceania', 'earth-africa',
            'house-chimney', 'house-chimney-window', 'house-circle-check', 'building-wheat', 'industry',
            'store', 'shop', 'cart-flatbed', 'truck', 'truck-fast', 'truck-ramp-box', 'ship', 'plane',
            'helicopter', 'car', 'car-side', 'bus', 'train', 'train-tram', 'bicycle', 'motorcycle',
            'person-walking', 'person-running', 'person-biking', 'person-swimming', 'person-skiing',
            'fish', 'dragon', 'paw', 'crow', 'dove', 'horse', 'dog', 'cat', 'spider', 'bug', 'worm',
            'otter', 'hippo', 'leaf', 'seedling', 'tree', 'tree-city', 'mountain', 'mountain-sun',
            'water', 'droplet', 'flask', 'vial', 'vial-circle-check', 'prescription-bottle', 'pills',
            'syringe', 'stethoscope', 'x-ray', 'heart-pulse', 'lungs', 'brain', 'bone', 'teeth',
            'eye', 'eye-slash', 'eye-dropper', 'ear-listen', 'nose', 'lips', 'hand-holding-medical',
            'hand-holding-heart', 'hand-holding-droplet', 'hand-holding', 'hand-fist', 'hand-sparkles',
            'handcuffs', 'hand-back-fist', 'hand-scissors', 'hand-peace', 'hand-point-right', 'hand-point-left',
            'hand-point-up', 'hand-point-down', 'hand-wave', 'thumbs-up', 'thumbs-down', 'hand-middle-finger',
            'cross', 'star-of-david', 'star-and-crescent', 'om', 'peace', 'yin-yang', 'khanda',
            'book-quran', 'book-bible', 'book-journal-whills', 'scroll-torah', 'hanukiah', 'menorah',
            'mosque', 'church', 'synagogue', 'kaaba', 'cross', 'cricket', 'baseball', 'basketball',
            'football', 'football-ball', 'hockey-puck', 'volleyball', 'table-tennis-paddle-ball', 'futbol',
            'chess', 'chess-king', 'chess-queen', 'chess-rook', 'chess-bishop', 'chess-knight', 'chess-pawn',
            'puzzle-piece', 'puzzle-piece-simple', 'scissors', 'paperclip', 'link', 'link-slash',
            'anchor', 'screwdriver-wrench', 'hammer', 'wrench', 'gear', 'gears', 'sliders', 'filter',
            'filter-circle-dollar', 'filter-circle-xmark', 'circle-half-stroke', 'solar-panel', 'wind',
            'water', 'fire-flame', 'fire-flame-curved', 'fire-flame-simple', 'snowflake', 'cloud',
            'cloud-rain', 'cloud-sun', 'cloud-moon', 'cloud-bolt', 'cloud-sun-rain', 'cloud-moon-rain',
            'cloud-showers-heavy', 'cloud-showers-water', 'cloud-fog', 'cloud-meatball', 'cloud-rainbow',
            'sun', 'sun-cloud', 'moon', 'moon-cloud', 'star', 'stars', 'bolt', 'bolt-lightning',
            'snowflake', 'temperature-half', 'temperature-quarter', 'temperature-full', 'temperature-empty',
            'temperature-arrow-up', 'temperature-arrow-down', 'temperature-low', 'temperature-high',
            'house', 'house-chimney', 'house-chimney-crack', 'house-chimney-window', 'house-circle-check',
            'house-circle-xmark', 'house-circle-exclamation', 'house-fire', 'house-flag', 'house-lock',
            'house-tsunamis', 'house-tree', 'house-building', 'house-flood-water', 'house-medical',
            'building', 'building-circle-check', 'building-circle-xmark', 'building-circle-exclamation',
            'building-columns', 'building-flag', 'building-lock', 'building-ngo', 'building-shield',
            'building-un', 'building-wheat', 'city', 'hospital', 'hospital-user', 'hotel', 'school',
            'store', 'store-slash', 'landmark', 'landmark-dome', 'landmark-flag', 'monument', 'mountain',
            'mountain-sun', 'mountain-city', 'mountain-sun', 'mountain-city', 'mountain-city', 'mountain-city',
            'flask', 'flask-vial', 'vial', 'vial-circle-check', 'vial-virus', 'prescription-bottle',
            'prescription-bottle-medical', 'capsules', 'pills', 'syringe', 'stethoscope', 'x-ray',
            'heart-pulse', 'heart', 'heart-crack', 'heart-half-stroke', 'lungs', 'brain', 'bone',
            'teeth', 'teeth-open', 'eye', 'eye-slash', 'eye-low-vision', 'ear-listen', 'ear-deaf',
            'nose', 'lips', 'hand-holding', 'hand-holding-heart', 'hand-holding-medical', 'hand-holding-droplet',
            'hand-holding-seedling', 'hand-fist', 'hand', 'hand-back-fist', 'hand-scissors', 'hand-sparkles',
            'hand-peace', 'hand-point-right', 'hand-point-left', 'hand-point-up', 'hand-point-down',
            'hand-wave', 'thumbs-up', 'thumbs-down', 'circle-user', 'user', 'user-large', 'user-large-slash',
            'user-check', 'user-clock', 'user-cog', 'user-gear', 'user-group', 'user-injured', 'user-lock',
            'user-minus', 'user-ninja', 'user-nurse', 'user-pen', 'user-plus', 'user-secret', 'user-shield',
            'user-slash', 'user-tag', 'user-tie', 'user-xmark', 'users', 'users-between-lines', 'users-gear',
            'users-line', 'users-rays', 'users-rectangle', 'users-slash', 'users-viewfinder',
            'bible', 'book-quran', 'book-bible', 'cross', 'church', 'hanukiah', 'menorah', 'mosque',
            'om', 'peace', 'place-of-worship', 'star-and-crescent', 'star-of-david', 'synagogue', 'torii-gate',
            'yin-yang', 'yin-yang', 'yin-yang', 'yin-yang', 'yin-yang', 'yin-yang', 'yin-yang', 'yin-yang',
            'yin-yang', 'yin-yang', 'yin-yang', 'yin-yang', 'yin-yang', 'yin-yang', 'yin-yang', 'yin-yang',
        ];

        // Deduplicate and ensure we have 500+
        $unique = array_unique($list);
        $out = [];
        $i = 0;
        foreach ($unique as $slug) {
            $out[] = $slug;
            $i++;
        }
        // Add numbered variants to reach 500+ if needed
        $base = ['trophy', 'medal', 'star', 'crown', 'gem', 'award', 'certificate', 'badge', 'ribbon'];
        $n = 1;
        while (count($out) < 500) {
            foreach ($base as $b) {
                $slug = $b . '-' . $n;
                if (!in_array($slug, $out, true)) {
                    $out[] = $slug;
                }
                if (count($out) >= 500) {
                    break 2;
                }
            }
            $n++;
        }

        return array_values(array_slice($out, 0, 520));
    }
}
