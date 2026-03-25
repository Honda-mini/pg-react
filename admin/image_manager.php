<?php include("includes/_header.php"); ?>
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
        $remaining = array_diff($images, $ordered);
        $images = array_merge($ordered, $remaining);
    }
}

// Primary image is first in array
$primary = $images[0] ?? null;
?>



    <h1 class="admin-title">Stock #<?= htmlspecialchars($stockID) ?> – Image Manager</h1>

    <!-- Upload Card -->
    <div class="admin-card mb-2">
        <h2 class="admin-card-title">Upload Images</h2>

        <form id="uploadForm" action="scripts/upload_images.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="stockID" value="<?= htmlspecialchars($stockID) ?>">

            <div class="form-group">
                <label>Select images</label>
                <input type="file" name="images[]" multiple accept="image/*">
                <p class="form-hint">
                    iPhone photos are fine. Originals go to <code>orig/</code> and WebP sizes are generated automatically.
                </p>
            </div>

            <button type="submit" class="admin-btn primary small">Upload</button>
        </form>
    </div>

    <!-- Image Grid -->
    <div class="admin-card">
        <h2 class="admin-card-title">Image Order & Primary</h2>
        <p class="admin-card-subtitle">Drag to reorder. Click “Set as primary” to promote an image.</p>

        <?php if (empty($images)): ?>
            <p class="text-muted">No images yet. Upload some above.</p>
        <?php else: ?>
            <div id="imageGrid" class="image-grid">
                <?php foreach ($images as $img): ?>
                    <?php
                        $base = pathinfo($img, PATHINFO_FILENAME);
                        $src400 = "{$publicDir}{$base}_400.webp";
                        $src800 = "{$publicDir}{$base}_800.webp";
                        $src1920 = "{$publicDir}{$base}.webp";
                    ?>
                    <div class="image-card" data-filename="<?= htmlspecialchars($img) ?>">

                        <?php if ($img === $primary): ?>
                            <div class="primary-badge">PRIMARY</div>
                        <?php endif; ?>

                        <img
                            src="<?= htmlspecialchars($src400) ?>"
                            srcset="<?= htmlspecialchars($src400) ?> 400w, <?= htmlspecialchars($src800) ?> 800w, <?= htmlspecialchars($src1920) ?> 1920w"
                            sizes="(max-width: 576px) 50vw, (max-width: 992px) 33vw, 25vw"
                            loading="lazy"
                            alt=""
                        >

                        <div class="card-body">
                            <div class="filename"><?= htmlspecialchars($img) ?></div>
                        </div>

                        <div class="card-footer-btns">
                            <button type="button" class="admin-btn small btn-primary-set" data-filename="<?= htmlspecialchars($img) ?>">
                                Set as primary
                            </button>
                            <button type="button" class="admin-btn danger small btn-delete" data-filename="<?= htmlspecialchars($img) ?>">
                                Delete
                            </button>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
const stockID = <?= json_encode($stockID) ?>;

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
            btnDelete.closest('.image-card').remove();
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
            const card = btnPrimary.closest('.image-card');
            grid.prepend(card);

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

<?php include("includes/_footer.php"); ?>
