<?php
/**
 * One-off script: Tailwind 4 alignment replacements across blade/vue files.
 * Run: php scripts/tailwind-replace.php
 */
$base = dirname(__DIR__);
$iter = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS)
);
$replacements = [
    'bg-gradient-to-r' => 'bg-linear-to-r',
    'bg-gradient-to-br' => 'bg-linear-to-br',
    'bg-gradient-to-t' => 'bg-linear-to-t',
    'bg-gradient-to-b' => 'bg-linear-to-b',
    'bg-gradient-to-l' => 'bg-linear-to-l',
    'rounded-[1.5rem]' => 'rounded-3xl',
    'rounded-[2rem]' => 'rounded-4xl',
    'tracking-[0.1em]' => 'tracking-widest',
    'bg-white/[0.02]' => 'bg-white/2',
    'dark:bg-white/[0.02]' => 'dark:bg-white/2',
    'bg-white/[0.01]' => 'bg-white/1',
    'bg-white/[0.03]' => 'bg-white/3',
    'z-[100]' => 'z-100',
];
$skip = ['node_modules', 'vendor', 'storage', '.git'];
$count = 0;
foreach ($iter as $file) {
    $path = $file->getPathname();
    $name = $file->getFilename();
    $ok = str_ends_with($name, '.blade.php') || str_ends_with($name, '.vue');
    if (!$ok) continue;
    foreach ($skip as $s) {
        if (strpos($path, $s) !== false) continue 2;
    }
    $content = @file_get_contents($path);
    if ($content === false) continue;
    $orig = $content;
    foreach ($replacements as $from => $to) {
        $content = str_replace($from, $to, $content);
    }
    if ($content !== $orig) {
        file_put_contents($path, $content);
        $count++;
        echo "Updated: " . str_replace($base . DIRECTORY_SEPARATOR, '', $path) . "\n";
    }
}
echo "Done. Files updated: $count\n";
