<?php
/**
 * Simple build script to create minified frontend assets without Node.
 * Usage: php bin/build-assets.php
 */

$root = dirname(__DIR__);

$files = [
    // CSS: source => target
    $root . '/assets/css/frontend.css' => $root . '/assets/css/frontend.min.css',
    // JS
    $root . '/assets/js/frontend.js' => $root . '/assets/js/frontend.min.js',
    $root . '/assets/js/admin.js' => $root . '/assets/js/admin.min.js',
];

foreach ($files as $src => $dst) {
    if (!file_exists($src)) {
        echo "Skipping missing file: $src\n";
        continue;
    }

    $content = file_get_contents($src);

    if (substr($dst, -4) === '.css') {
        // Very small CSS minifier: remove comments and whitespace
        $min = preg_replace('!/\*.*?\*/!s', '', $content);
        $min = preg_replace('/\s+/', ' ', $min);
        $min = str_replace(["\n", "\r", "\t"], '', $min);
        $min = preg_replace('/\s*([{}:,;])\s*/', '$1', $min);
        $min = trim($min);
    } else {
        // Very small JS minifier: remove single-line and block comments and collapse whitespace
        // Note: This is intentionally minimal — for complex JS prefer a proper minifier.
        $min = preg_replace('!/\*.*?\*/!s', '', $content);
        $min = preg_replace('/(?<!http:)\/\/.*$/m', '', $min);
        $min = preg_replace('/\s+/', ' ', $min);
        $min = str_replace(["\n", "\r", "\t"], '', $min);
        $min = trim($min);
    }

    if (file_put_contents($dst, $min) === false) {
        echo "Failed to write: $dst\n";
    } else {
        echo "Wrote: $dst\n";
    }
}

echo "Build complete.\n";
