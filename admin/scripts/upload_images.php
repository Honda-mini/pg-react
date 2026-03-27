<?php


// scripts/upload_images.php
header('Content-Type: text/html; charset=utf-8');

$stockID = isset($_POST['stockID']) ? intval($_POST['stockID']) : 0;
if (!$stockID) {
    die("No valid stock ID provided.");
}

$baseDir = $_SERVER['DOCUMENT_ROOT'] . "/reactPg/images/cars/{$stockID}/";
$origDir   = $baseDir . "orig/";
$orderFile = $baseDir . "order.json";

if (!is_dir($baseDir)) {
    mkdir($baseDir, 0775, true);
}
if (!is_dir($origDir)) {
    mkdir($origDir, 0775, true);
}

// Config
$MAX_MAIN_SIZE = 1920;
$SIZE_800      = 800;
$SIZE_400      = 400;
$WEBP_QUALITY  = 80;

$success = 0;
$fail    = 0;

// Load existing order.json
$order = [];
if (file_exists($orderFile)) {
    $decoded = json_decode(file_get_contents($orderFile), true);
    if (is_array($decoded)) {
        $order = $decoded;
    }
}

// Utility: load image from upload
function loadImageFromPath($path, $mime) {
    switch ($mime) {
        case 'image/jpeg': return @imagecreatefromjpeg($path);
        case 'image/png':  return @imagecreatefrompng($path);
        case 'image/gif':  return @imagecreatefromgif($path);
        case 'image/webp': return @imagecreatefromwebp($path);
        default: return false;
    }
}

// Utility: fix EXIF rotation
function fixOrientation($img, $path) {
    if (!function_exists('exif_read_data')) return $img;
    $exif = @exif_read_data($path);
    if (!$exif || !isset($exif['Orientation'])) return $img;

    switch ($exif['Orientation']) {
        case 3: return imagerotate($img, 180, 0);
        case 6: return imagerotate($img, -90, 0);
        case 8: return imagerotate($img, 90, 0);
        default: return $img;
    }
}

// Resize to max dimension
function resizeToMax($srcPath, $maxSize) {
    $info = @getimagesize($srcPath);
    if (!$info) return false;

    list($width, $height) = $info;
    $mime = $info['mime'];

    $src = loadImageFromPath($srcPath, $mime);
    if (!$src) return false;

    $src = fixOrientation($src, $srcPath);

    $ratio = $width / $height;

    if ($ratio > 1) {
        $newWidth  = $maxSize;
        $newHeight = (int)($maxSize / $ratio);
    } else {
        $newHeight = $maxSize;
        $newWidth  = (int)($maxSize * $ratio);
    }

    $dst = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    imagedestroy($src);
    return $dst;
}

// Resize to fixed width (for 800 / 400)
function resizeToWidth($srcPath, $targetWidth) {
    $info = @getimagesize($srcPath);
    if (!$info) return false;

    list($width, $height) = $info;
    $mime = $info['mime'];

    $src = loadImageFromPath($srcPath, $mime);
    if (!$src) return false;

    $src = fixOrientation($src, $srcPath);

    $ratio       = $targetWidth / $width;
    $targetHeight = (int)($height * $ratio);

    $dst = imagecreatetruecolor($targetWidth, $targetHeight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

    imagedestroy($src);
    return $dst;
}

// Save GD image as WebP
function saveAsWebP($gdImage, $destPath, $quality) {
    imagewebp($gdImage, $destPath, $quality);
    imagedestroy($gdImage);
}

// Find next available numeric index
function getNextIndex($baseDir) {
    $max = 0;
    $files = scandir($baseDir);
    foreach ($files as $file) {
        if (preg_match('/^(\d+)\.webp$/', $file, $m)) {
            $n = intval($m[1]);
            if ($n > $max) $max = $n;
        }
    }
    return $max + 1;
}

// Loop through uploaded files
if (isset($_FILES['images'])) {
    foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {

        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
            $fail++;
            continue;
        }

        $origName = $_FILES['images']['name'][$i];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif'])) {
            $fail++;
            continue;
        }

        // We’ll still use the tmp file as the source for resizing
        $info = @getimagesize($tmpName);
        if (!$info) {
            $fail++;
            continue;
        }

        $n = getNextIndex($baseDir);
        $baseName = (string)$n;

        // Save original as orig/{n}_orig.<ext>
        $origExt  = $ext ?: 'jpg';
        $origPath = $origDir . "{$baseName}_orig." . $origExt;
        @move_uploaded_file($tmpName, $origPath);

        // If move_uploaded_file failed (e.g. already moved), copy instead
        if (!file_exists($origPath) && file_exists($tmpName)) {
            @copy($tmpName, $origPath);
            @unlink($tmpName);
        }

        if (!file_exists($origPath)) {
            $fail++;
            continue;
        }

        // MAIN 1920
        $mainImg = resizeToMax($origPath, $MAX_MAIN_SIZE);
        if ($mainImg) {
            $mainPath = $baseDir . "{$baseName}.webp";
            saveAsWebP($mainImg, $mainPath, $WEBP_QUALITY);
        } else {
            $fail++;
            continue;
        }

        // 800
        $img800 = resizeToWidth($origPath, $SIZE_800);
        if ($img800) {
            $path800 = $baseDir . "{$baseName}_800.webp";
            saveAsWebP($img800, $path800, $WEBP_QUALITY);
        }

        // 400
        $img400 = resizeToWidth($origPath, $SIZE_400);
        if ($img400) {
            $path400 = $baseDir . "{$baseName}_400.webp";
            saveAsWebP($img400, $path400, $WEBP_QUALITY);
        }

        // Add to order.json
        $order[] = "{$baseName}.webp";

        $success++;
    }
}

// Ensure order.json exists and has a primary image
$order = array_values(array_unique($order));

// If this is the first upload and order.json didn't exist before:
if (!file_exists($orderFile)) {
    // Sort numerically so 1.webp is always first
    usort($order, function($a, $b) {
        return intval($a) - intval($b);
    });
}

// Save order.json
file_put_contents($orderFile, json_encode($order, JSON_PRETTY_PRINT));

// Redirect back to image_manager with messages
$params = [];
if ($success) $params[] = "success={$success}";
if ($fail)    $params[] = "fail={$fail}";
$query = $params ? ('&' . implode('&', $params)) : '';

header("Location: /reactPg/admin/image_manager.php?stockID={$stockID}{$query}");
exit;
