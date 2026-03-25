<?php
$baseDir = __DIR__ . '/../public/images/cars/';

$folders = scandir($baseDir);

foreach ($folders as $folder) {
    if ($folder === '.' || $folder === '..') continue;

    $dir = $baseDir . $folder . '/';

    if (!is_dir($dir)) continue;

    $files = glob($dir . '*.webp');

    foreach ($files as $file) {
        $filename = basename($file);

        // Match "1 2.webp", "2 2.webp" etc
        if (preg_match('/^\d+\s2\.webp$/', $filename)) {
            unlink($file);
            echo "Deleted: {$folder}/{$filename}\n";
        }
    }
}

echo "Done.";