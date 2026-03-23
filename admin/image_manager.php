<?php
require_once('../src/utils/pg_services.php');

$stockID = isset($_GET['stockID']) ? intval($_GET['stockID']) : 0;
if (!$stockID) {
  die("No valid stock ID provided.");
}

$imageDir = "../public/images/cars/$stockID/";
$webImagePath = "../public/images/cars/$stockID/";
$images = [];
  $success = 0;
  $fail = 0;


// Handle image upload
if (isset($_POST['upload']) && isset($_FILES['images'])) {

    // Ensure folders exist
    if (!is_dir($imageDir)) mkdir($imageDir, 0777, true);
    $thumbsDir = $imageDir . 'thumbs/';
    if (!is_dir($thumbsDir)) mkdir($thumbsDir, 0777, true);

    // Config (same as add_vehicle.php)
    $MAX_MAIN_SIZE = 1920;
    $THUMB_WIDTH = 400;
    $JPG_QUALITY = 80;
    $WEBP_QUALITY = 80;

    // Utility: load image
    function loadImageUpload($path, $mime) {
        switch ($mime) {
            case 'image/jpeg': return @imagecreatefromjpeg($path);
            case 'image/png':  return @imagecreatefrompng($path);
            case 'image/gif':  return @imagecreatefromgif($path);
            default: return false;
        }
    }

    // Utility: fix EXIF rotation
    function fixOrientationUpload($img, $path) {
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

    // Resize + save JPG
    function resizeAndSaveJPGUpload($srcPath, $destPath, $maxSize, $quality) {
        $info = @getimagesize($srcPath);
        if (!$info) return false;

        list($width, $height) = $info;
        $mime = $info['mime'];

        $src = loadImageUpload($srcPath, $mime);
        if (!$src) return false;

        $src = fixOrientationUpload($src, $srcPath);

        $ratio = $width / $height;

        if ($ratio > 1) {
            $newWidth = $maxSize;
            $newHeight = (int)($maxSize / $ratio);
        } else {
            $newHeight = $maxSize;
            $newWidth = (int)($maxSize * $ratio);
        }

        $dst = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagejpeg($dst, $destPath, $quality);

        imagedestroy($src);
        imagedestroy($dst);

        return true;
    }

    // Create thumbnail
    function createThumbnailUpload($srcPath, $thumbPath, $thumbWidth, $quality) {
        $info = @getimagesize($srcPath);
        if (!$info) return false;

        list($width, $height) = $info;
        $mime = $info['mime'];

        $src = loadImageUpload($srcPath, $mime);
        if (!$src) return false;

        $src = fixOrientationUpload($src, $srcPath);

        $ratio = $thumbWidth / $width;
        $thumbHeight = (int)($height * $ratio);

        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagecopyresampled($thumb, $src, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        imagejpeg($thumb, $thumbPath, $quality);

        imagedestroy($src);
        imagedestroy($thumb);

        return true;
    }

    // Create WebP
    function createWebPUpload($srcPath, $destPath, $quality) {
        $info = @getimagesize($srcPath);
        if (!$info) return false;

        $mime = $info['mime'];
        $src = loadImageUpload($srcPath, $mime);
        if (!$src) return false;

        imagewebp($src, $destPath, $quality);
        imagedestroy($src);

        return true;
    }

    // Loop through uploaded files
    foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {

        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
            $fail++;
            continue;
        }

        $origName = $_FILES['images']['name'][$i];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $fail++;
            continue;
        }

        // Find next available number
        $n = 1;
        while (file_exists($imageDir . $n . ".jpg")) {
            $n++;
        }

        $newName = $n . ".jpg";
        $dest = $imageDir . $newName;
        $thumbDest = $thumbsDir . $newName;

        // Convert uploaded file to JPG first
        resizeAndSaveJPGUpload($tmpName, $dest, $MAX_MAIN_SIZE, $JPG_QUALITY);

        // Create thumbnail
        createThumbnailUpload($dest, $thumbDest, $THUMB_WIDTH, $JPG_QUALITY);

        // Create WebP versions
        createWebPUpload($dest, $imageDir . $n . ".webp", $WEBP_QUALITY);
        createWebPUpload($thumbDest, $thumbsDir . $n . ".webp", $WEBP_QUALITY);

        @unlink($tmpName);

        $success++;
    }

    // Redirect with messages
    $params = [];
    if ($success) $params[] = "success=$success";
    if ($fail) $params[] = "fail=$fail";
    $query = implode('&', $params);

    header("Location: image_manager.php?stockID=$stockID&$query");
    exit;
}
?>
  

<?php
// Load image files (WEBP preferred, JPG fallback)
$images = [];
$seen = [];

if (is_dir($imageDir)) {
    $files = array_diff(scandir($imageDir), ['.', '..', 'thumbs', '.DS_Store', 'order.json']);

    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $name = pathinfo($file, PATHINFO_FILENAME);

        // Only consider image formats we use
        if (!in_array($ext, ['jpg', 'jpeg', 'webp'])) {
            continue;
        }

        // Skip JPG if WEBP exists
        if (($ext === 'jpg' || $ext === 'jpeg') && file_exists($imageDir . $name . '.webp')) {
            continue;
        }

        // Avoid duplicates
        if (isset($seen[$name])) {
            continue;
        }

        $seen[$name] = true;
        $images[] = $file;
    }
}
// Show upload messages from GET after redirect
if (isset($_GET['success'])) {
    echo "<div class='alert alert-success'>Uploaded " . intval($_GET['success']) . " image(s).</div>";
}
if (isset($_GET['fail'])) {
    echo "<div class='alert alert-danger'>Some files were not valid images.</div>";
}
?>
<script>
  setTimeout(function() {
    var alertBox = document.querySelector('.alert-success, .alert-danger');
    if(alertBox) alertBox.style.transition = "opacity 0.5s";
    if(alertBox) alertBox.style.opacity = 0;
    setTimeout(function(){ if(alertBox) alertBox.remove(); }, 600);
  }, 4000);
</script>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8">
  <title>Image Manager</title>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
  <link href="styles/pgLayout.css?v=<?=filemtime('styles/pgLayout.css')?>" rel="stylesheet">
<style>
  .card img {
    object-fit: cover;
    height: 180px;
  }
  @media (max-width: 600px) {
    .card img {
      height: 90px;
    }
    .row.g-4 {
      gap: 0.5rem !important;
    }
    .card {
      font-size: 0.95em;
    }
  }
</style></head>
<body>
  
  <div class="container py-4">
    <div id="responseMessage" class="alert d-none" role="alert"></div>

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

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
  <a href="manage_stock.php" class="btn btn-secondary mb-2 mb-md-0">← Back to Stock</a>
  <h2 class="mb-0 flex-grow-1 text-center text-md-start">Images for Vehicle ID <?= $stockID ?></h2>
</div>
<div class="alert alert-info mb-4">
  <strong>Tip:</strong> Drag and drop images to change their order. The first image will be used as the main thumbnail. Click <b>Save Order</b> to apply your changes.
</div>

<div class="row g-4" id="imageGrid">
      <?php 
      $orderFile = $imageDir . 'order.json';
if (file_exists($orderFile)) {
  $ordered = json_decode(file_get_contents($orderFile), true);
  // Only include images that still exist
  $images = array_values(array_filter($ordered, function($img) use ($imageDir) {
    return is_file($imageDir . $img);
  }));
}
foreach ($images as $image):
        $filenameNoExt = pathinfo($image, PATHINFO_FILENAME); ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card h-100 shadow-sm">
            <a href="<?= $webImagePath . $image ?>" data-lightbox="car-<?= $stockID ?>">
              <img src="<?= $webImagePath . $image ?>" class="card-img-top" alt="Vehicle image">
            </a>
            <div class="card-body">
              <form class="delete-form" method="POST" action="scripts/delete_image.php">
                <input type="hidden" name="stockID" value="<?= $stockID ?>">
                <input type="hidden" name="filename" value="<?= $image ?>">
                <button class="btn btn-outline-danger btn-sm w-100">Delete</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <button id="saveOrderBtn" class="btn btn-success mt-3">Save Order</button>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
<script>
function showMessage(type, msg) {
  const $box = $('#responseMessage');
  $box
    .removeClass('d-none alert-success alert-danger')
    .addClass(type === 'success' ? 'alert-success' : 'alert-danger')
    .text(msg)
    .fadeIn();

  setTimeout(() => $box.fadeOut(), 5000);
}


$(document).on('submit', '.delete-form', function(e) {
  e.preventDefault();
  if (!confirm('Delete this image?')) return;

  const $form = $(this);
  const $card = $form.closest('.col-sm-6, .col-md-4');

  $.post($form.attr('action'), $form.serialize(), function(response) {
    if (response.status === 'success') {
      showMessage('success', response.msg);
      $card.fadeOut(300, function() { $(this).remove(); });
    } else {
      showMessage('error', response.msg);
    }
  }, 'json').fail(() => {
    showMessage('error', 'Delete request failed.');
  });
});
</script>
<script>
  // Make the grid sortable
  const sortable = new Sortable(document.getElementById('imageGrid'), {
    animation: 150,
    handle: '.card-img-top', // drag by image
    draggable: '.col-sm-6, .col-md-4, .col-lg-3'
  });

// Save order
document.getElementById('saveOrderBtn').addEventListener('click', function() {
  const order = [];
  document.querySelectorAll('#imageGrid .card').forEach(card => {
    const input = card.querySelector('input[name="filename"]');
    if (input) order.push(input.value);
  });
  fetch('scripts/save_image_order.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({stockID: <?= $stockID ?>, order: order})
  })
  .then(res => res.json())
  .then(data => alert(data.msg || 'Order saved!'));
});</script>
</body>
</html>
