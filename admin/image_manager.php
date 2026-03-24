<?php
require_once('../src/utils/pg_services.php');

$stockID = isset($_GET['stockID']) ? intval($_GET['stockID']) : 0;
if (!$stockID) die("No valid stock ID provided.");

$imageDir = "../public/images/cars/$stockID/";
$webImagePath = "../public/images/cars/$stockID/";

$success = 0;
$fail = 0;

// -------------------------
// IMAGE UPLOAD
// -------------------------
if (isset($_POST['upload']) && isset($_FILES['images'])) {

    if (!is_dir($imageDir)) mkdir($imageDir, 0777, true);

    function createResizedWebP($srcPath, $destPath, $maxSize, $quality) {
        $info = getimagesize($srcPath);
        if (!$info) return false;

        list($width, $height) = $info;

        $src = imagecreatefromstring(file_get_contents($srcPath));
        if (!$src) return false;

        $ratio = $width / $height;

        if ($ratio > 1) {
            $newWidth = $maxSize;
            $newHeight = $maxSize / $ratio;
        } else {
            $newHeight = $maxSize;
            $newWidth = $maxSize * $ratio;
        }

        $dst = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagewebp($dst, $destPath, $quality);

        imagedestroy($src);
        imagedestroy($dst);

        return true;
    }

    foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {

        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
            $fail++;
            continue;
        }

        $n = 1;
        while (file_exists($imageDir . $n . ".webp")) {
            $n++;
        }

        // MAIN
        createResizedWebP($tmpName, $imageDir . "{$n}.webp", 1920, 80);

        // GALLERY
        createResizedWebP($tmpName, $imageDir . "{$n}_800.webp", 800, 80);

        // THUMB
        createResizedWebP($tmpName, $imageDir . "{$n}_400.webp", 400, 80);

        @unlink($tmpName);
        $success++;
    }

    $params = [];
    if ($success) $params[] = "success=$success";
    if ($fail) $params[] = "fail=$fail";

    header("Location: image_manager.php?stockID=$stockID&" . implode('&', $params));
    exit;
}

// -------------------------
// LOAD IMAGES (MAIN ONLY)
// -------------------------
$images = [];

if (is_dir($imageDir)) {
    $files = array_diff(scandir($imageDir), ['.', '..', '.DS_Store', 'order.json']);

    foreach ($files as $file) {
        if (preg_match('/^\d+\.webp$/', $file)) {
            $images[] = $file;
        }
    }

    natsort($images);
    $images = array_values($images);
}

// -------------------------
// APPLY ORDER.JSON
// -------------------------
$orderFile = $imageDir . 'order.json';

if (file_exists($orderFile)) {
    $ordered = json_decode(file_get_contents($orderFile), true);

    $images = array_values(array_filter($ordered, function($img) use ($imageDir) {
        return preg_match('/^\d+\.webp$/', $img) && file_exists($imageDir . $img);
    }));
}

// Messages
if (isset($_GET['success'])) {
    echo "<div class='alert alert-success'>Uploaded " . intval($_GET['success']) . " image(s).</div>";
}
if (isset($_GET['fail'])) {
    echo "<div class='alert alert-danger'>Some files failed.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Image Manager</title>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="styles/pgLayout.css?v=<?=filemtime('styles/pgLayout.css')?>" rel="stylesheet" type="text/css">
<style>
.card img {
  object-fit: cover;
  height: 180px;
}
</style>
</head>

<body>

<div class="container py-4">
  
<!-- HEADER -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
  <a href="manage_stock.php" class="btn btn-secondary mb-2 mb-md-0">← Back to Stock</a>
  <h2 class="mb-0 flex-grow-1 text-center text-md-start">Images for Vehicle ID <?= $stockID ?></h2>
</div>
 <div id="responseMessage" class="alert d-none" role="alert"></div>

<!-- UPLOAD FORM -->
 <div class="card mb-4 shadow-sm">
  <div class="card-body">
    <form method="post" enctype="multipart/form-data" class="row g-2 align-items-center">
      <div class="col-12 col-md-8">
        <label for="images" class="form-label mb-1 fw-semibold">Add Images</label>
        <input type="file" id="images" name="images[]" multiple accept="image/*" class="form-control" required>
        <div class="form-text">You can select multiple images (jpg, png, gif).</div>
      </div>
      <div class="col-12 col-md-4 d-flex align-items-end">
        <button type="submit" name="upload" class="btn btn-primary w-100 mt-2 mt-md-0">
          <i class="bi bi-upload"></i> Upload Images
        </button>
      </div>
    </form>
  </div>
</div>
<div class="alert alert-info mb-4">
  <strong>Tip:</strong> Drag and drop images to change their order. The first image will be used as the main thumbnail. Click <b>Save Order</b> to apply your changes.
</div>

<div class="row g-4" id="imageGrid">

<?php foreach ($images as $image): ?>
<div class="col-sm-6 col-md-4 col-lg-3">
  <div class="card shadow-sm">

    <img src="<?= $webImagePath . $image ?>" class="card-img-top">

    <div class="card-body">
      <form method="POST" action="scripts/delete_image.php">
        <input type="hidden" name="stockID" value="<?= $stockID ?>">
        <input type="hidden" name="filename" value="<?= $image ?>">
        <button class="btn btn-danger btn-sm w-100">Delete</button>
      </form>
    </div>

  </div>
</div>
<?php endforeach; ?>

</div>

<button id="saveOrderBtn" class="btn btn-success mt-3">Save Order</button>

</div>

<script>
// Drag + drop
new Sortable(document.getElementById('imageGrid'), {
  animation: 150,
  draggable: '.col-sm-6'
});

// Save order
document.getElementById('saveOrderBtn').addEventListener('click', function() {

  const order = [];

  document.querySelectorAll('#imageGrid .col-sm-6').forEach(col => {
    const input = col.querySelector('input[name="filename"]');
    if (input) order.push(input.value);
  });

  fetch('scripts/save_image_order.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      stockID: <?= $stockID ?>,
      order: order
    })
  })
  .then(res => res.json())
  .then(data => alert(data.msg || 'Order saved!'));
});
</script>

</body>
</html>