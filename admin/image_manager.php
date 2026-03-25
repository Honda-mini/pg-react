<?php
require_once('scripts/auth_session.php');

$stockID = intval($_GET['stockID'] ?? 0);
if (!$stockID) {
  die('Missing stock ID');
}

$dir = "../public/images/cars/{$stockID}/";
$publicDir = "../public/images/cars/{$stockID}/";

if (!is_dir($dir)) {
  mkdir($dir, 0775, true);
}

$orderFile = $dir . "order.json";

// Get list of main images (n.webp)
$images = [];
if (is_dir($dir)) {
  $files = scandir($dir);
  foreach ($files as $file) {
    if (preg_match('/^\d+\.webp$/', $file)) {
      $images[] = $file;
    }
  }
}

// Apply order.json if present
if (file_exists($orderFile)) {
  $order = json_decode(file_get_contents($orderFile), true);
  if (is_array($order)) {
    $ordered = [];
    foreach ($order as $fname) {
      if (in_array($fname, $images)) {
        $ordered[] = $fname;
      }
    }
    // Append any new images not yet in order.json
    $remaining = array_diff($images, $ordered);
    $images = array_merge($ordered, $remaining);
  }
}

// Primary image is first in array
$primary = $images[0] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Image Manager – Stock <?php echo htmlspecialchars($stockID); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="styles/boilerplate.css" rel="stylesheet">
  <style>
    body { background: #f5f5f7; }
    .image-card { cursor: grab; position: relative; }
    .image-card.dragging { opacity: 0.6; cursor: grabbing; }
    .primary-badge {
      position: absolute;
      top: 0.5rem;
      left: 0.5rem;
      z-index: 2;
      background-color: #0d6efd;
      color: #fff;
      padding: 0.15rem 0.5rem;
      border-radius: 999px;
      font-size: 0.7rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.25);
    }
    .image-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 1rem;
    }
    .image-card img {
      border-radius: 0.5rem 0.5rem 0 0;
      object-fit: cover;
      width: 100%;
      height: 160px;
    }
    .card-footer-btns .btn {
      font-size: 0.8rem;
    }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Images for stock #<?php echo htmlspecialchars($stockID); ?></h1>
    <a href="manage_stock.php" class="btn btn-outline-secondary btn-sm">Back to stock</a>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <strong>Upload images</strong>
    </div>
    <div class="card-body">
      <form id="uploadForm" action="scripts/upload_images.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="stockID" value="<?php echo htmlspecialchars($stockID); ?>">
        <div class="mb-3">
          <label class="form-label">Select images</label>
          <input type="file" name="images[]" class="form-control" multiple accept="image/*">
          <div class="form-text">
            iPhone photos are fine. Originals will be stored in <code>orig/</code> and WebP sizes generated automatically.
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Image order & primary</strong>
      <small class="text-muted">Drag to reorder. Click “Set as primary” to promote an image.</small>
    </div>
    <div class="card-body">
      <?php if (empty($images)): ?>
        <p class="text-muted mb-0">No images yet. Upload some above.</p>
      <?php else: ?>
        <div id="imageGrid" class="image-grid">
          <?php foreach ($images as $img): ?>
            <?php
              $base = pathinfo($img, PATHINFO_FILENAME);
              $src400 = "{$publicDir}{$base}_400.webp";
              $src800 = "{$publicDir}{$base}_800.webp";
              $src1920 = "{$publicDir}{$base}.webp";
            ?>
            <div class="card image-card" data-filename="<?php echo htmlspecialchars($img); ?>">
              <?php if ($img === $primary): ?>
                <div class="primary-badge">PRIMARY</div>
              <?php endif; ?>
              <img
                src="<?php echo htmlspecialchars($src400); ?>"
                srcset="<?php echo htmlspecialchars($src400); ?> 400w, <?php echo htmlspecialchars($src800); ?> 800w, <?php echo htmlspecialchars($src1920); ?> 1920w"
                sizes="(max-width: 576px) 50vw, (max-width: 992px) 33vw, 25vw"
                loading="lazy"
                alt=""
              >
              <div class="card-body py-2">
                <div class="small text-muted mb-1"><?php echo htmlspecialchars($img); ?></div>
              </div>
              <div class="card-footer card-footer-btns d-flex justify-content-between">
                <button type="button" class="btn btn-outline-primary btn-sm btn-primary-set" data-filename="<?php echo htmlspecialchars($img); ?>">
                  Set as primary
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm btn-delete" data-filename="<?php echo htmlspecialchars($img); ?>">
                  Delete
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
const stockID = <?php echo json_encode($stockID); ?>;

const grid = document.getElementById('imageGrid');
if (grid) {
  new Sortable(grid, {
    animation: 150,
    onEnd: saveOrder
  });
}

function getCurrentOrder() {
  const cards = document.querySelectorAll('#imageGrid .image-card');
  return Array.from(cards).map(card => card.dataset.filename);
}

function saveOrder() {
  const order = getCurrentOrder();
  fetch('scripts/save_image_order.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ stockID, order })
  });
}

document.addEventListener('click', async (e) => {
  const btnDelete = e.target.closest('.btn-delete');
  const btnPrimary = e.target.closest('.btn-primary-set');

  if (btnDelete) {
    const filename = btnDelete.dataset.filename;
    if (!confirm(`Delete ${filename}? This will remove all sizes and the original.`)) return;

    const formData = new FormData();
    formData.append('stockID', stockID);
    formData.append('filename', filename);

    const res = await fetch('scripts/delete_image.php', {
      method: 'POST',
      body: formData
    });
    const json = await res.json();
    if (json.status === 'success') {
      const card = btnDelete.closest('.image-card');
      card.remove();
      saveOrder();
    } else {
      alert(json.msg || 'Error deleting image');
    }
  }

  if (btnPrimary) {
    const filename = btnPrimary.dataset.filename;
    const res = await fetch('scripts/set_primary.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ stockID, filename })
    });
    const json = await res.json();
    if (json.status === 'success') {
      // Move card to top
      const card = btnPrimary.closest('.image-card');
      grid.prepend(card);
      // Update badges
      document.querySelectorAll('.primary-badge').forEach(b => b.remove());
      const badge = document.createElement('div');
      badge.className = 'primary-badge';
      badge.textContent = 'PRIMARY';
      card.appendChild(badge);
      saveOrder();
    } else {
      alert(json.msg || 'Error setting primary image');
    }
  }
});
</script>
</body>
</html>
