<?php

require_once('scripts/auth_session.php');
require_once('../src/utils/pg_services.php');

$uploadMessage = '';

if (!empty($_SESSION['success'])) {
    $uploadMessage = $_SESSION['success'];
    unset($_SESSION['success']); // Only show it once
}
?>


<?php
// Build dynamic WHERE conditions
$where = [];
$q = $_GET['q'] ?? '';
$makeFilter = $_GET['make'] ?? '';
$hasImages = $_GET['hasImages'] ?? '';

// Search text
if ($q !== '') {
    $qEsc = $pg_services->real_escape_string($q);
    $where[] = "(stockID LIKE '%$qEsc%' 
                 OR make LIKE '%$qEsc%' 
                 OR model LIKE '%$qEsc%' 
                 OR trim LIKE '%$qEsc%' 
                 OR regNumber LIKE '%$qEsc%')";
}

// Filter by make
if ($makeFilter !== '') {
    if ($makeFilter === '__NONE__') {
        $where[] = "(make IS NULL OR make = '')";
    } else {
        $makeEsc = $pg_services->real_escape_string($makeFilter);
        $where[] = "make = '$makeEsc'";
    }
}

// Build WHERE SQL
$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// Final query
$query = "SELECT stockID, make, model, trim, regNumber, price 
          FROM stock 
          $whereSQL
          ORDER BY updated DESC";

$result = $pg_services->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Manage Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles/boilerplate.css" rel="stylesheet">
    <link href="styles/pgLayout.css?v=<?=filemtime('styles/pgLayout.css')?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="adminGridContainer clearfix">

<div id="header">
    <?php include("includes/header2.txt"); ?>
    <div id="admin" align="right">ADMIN AREA</div>
    
    <?php if (!empty($_SESSION['uploadMessage'])): ?>
        <div id="uploadMessage" style="
            background: transparent;
            border: 2px solid <?= ($_SESSION['uploadMessageType'] == 'success') ? '#4CAF50' : '#F44336'; ?>;
            color: <?= ($_SESSION['uploadMessageType'] == 'success') ? '#4CAF50' : '#F44336'; ?>;
            padding: 12px 16px;
            margin: 15px auto;
            width: 90%;
            max-width: 800px;
            font-size: 1.1em;
            font-weight: bold;
            border-radius: 6px;
            text-align: center;
            transition: opacity 1s ease, transform 1s ease;
        ">
            <?= htmlspecialchars($_SESSION['uploadMessage']) ?>
        </div>
        <script>
            setTimeout(function() {
                const el = document.getElementById('uploadMessage');
                if (el) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-20px)';
                    setTimeout(() => el.style.display = 'none', 1000);
                }
            }, 15000);
        </script>
        <?php unset($_SESSION['uploadMessage'], $_SESSION['uploadMessageType']); ?>
    <?php endif; ?>
</div>
<div id="nav">      
    <a href="index.php" class="btn btn-secondary nav-back">← Back to Admin Menu</a>

    <button id="nav-toggle" aria-label="Open navigation">
  <span class="hamburger"></span>
  <span class="hamburger"></span>
  <span class="hamburger"></span>
</button>
<?php include("includes/nav2.txt"); ?>
</div>

    <div id="admin-content">
        <h1>Manage Vehicle Stock</h1>

<!-- Search and filter form -->
        <form method="GET" class="stock-search" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
    
    <input type="text" 
           name="q" 
           placeholder="Search make, model, reg, ID…" 
           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
           class="form-control"
           style="max-width: 250px;">

    <select name="make" class="form-select" style="max-width: 180px;">
    <option value="">All Makes</option>

    <?php
    $makes = $pg_services->query("
        SELECT DISTINCT 
            CASE 
                WHEN make IS NULL OR make = '' THEN '__NONE__'
                ELSE make
            END AS makeValue
        FROM stock
        ORDER BY makeValue ASC
    ");

    while ($m = $makes->fetch_assoc()):
        $value = $m['makeValue'];
        $label = ($value === '__NONE__') ? 'No Make' : $value;
        $selected = (($_GET['make'] ?? '') === $value) ? 'selected' : '';
    ?>
        <option value="<?= htmlspecialchars($value) ?>" <?= $selected ?>>
            <?= htmlspecialchars($label) ?>
        </option>
    <?php endwhile; ?>
</select>


    <select name="hasImages" class="form-select" style="max-width: 180px;">
        <option value="">All Vehicles</option>
        <option value="1" <?= (($_GET['hasImages'] ?? '') === '1') ? 'selected' : '' ?>>Has Images</option>
        <option value="0" <?= (($_GET['hasImages'] ?? '') === '0') ? 'selected' : '' ?>>No Images</option>
    </select>

    <button type="submit" class="btn btn-primary">Filter</button>
</form>

<!-- Vehicle list -->
<div class="vehicle-list">
<?php while ($row = $result->fetch_assoc()) { ?>

<!-- Image handling logic: Count main images (1.webp, 2.webp…) and load primary thumbnail from order.json if it exists. -->
<?php
$imageDir = __DIR__ . "/../public/images/cars/" . $row['stockID'] . "/";
$imageCount = 0;
$primaryThumb = null;

if (is_dir($imageDir)) {

    // Count ONLY main images (1.webp, 2.webp…)
    foreach (scandir($imageDir) as $file) {
        if (preg_match('/^[0-9]+\.webp$/', $file)) {
            $imageCount++;
        }
    }

    // Load primary image from order.json
    $orderFile = $imageDir . "order.json";
    if (file_exists($orderFile)) {
        $order = json_decode(file_get_contents($orderFile), true);

        if (is_array($order) && count($order) > 0) {

            // Extract base name (e.g. "1" from "1.webp")
            $base = pathinfo($order[0], PATHINFO_FILENAME);

            $thumb400 = "../public/images/cars/{$row['stockID']}/{$base}_400.webp";
            $thumb800 = "../public/images/cars/{$row['stockID']}/{$base}_800.webp";
            $thumbFull = "../public/images/cars/{$row['stockID']}/{$order[0]}";

            if (file_exists($imageDir . "{$base}_400.webp")) {
                $primaryThumb = $thumb400;
            } elseif (file_exists($imageDir . "{$base}_800.webp")) {
                $primaryThumb = $thumb800;
            } elseif (file_exists($imageDir . $order[0])) {
                $primaryThumb = $thumbFull;
            }
        }
    }
}
?>

  <div class="vehicle-card">

<!-- Display primary thumbnail if available, otherwise show placeholder -->
  <?php if ($primaryThumb): ?>
    <img src="<?= $primaryThumb ?>" class="vehicle-thumb">
<?php else: ?>
    <div class="vehicle-thumb placeholder">No Image</div>
<?php endif; ?>

    <div class="vehicle-id"><strong>ID:</strong> <?php echo htmlspecialchars($row['stockID']); ?></div>
    
    <?php
$make  = trim($row['make']  ?? '');
$model = trim($row['model'] ?? '');
$trim  = trim($row['trim']  ?? '');

$fullName = trim("$make $model $trim");
?>
<div class="vehicle-make">
    <strong>Make & Model:</strong> <?php echo htmlspecialchars($fullName); ?>
</div>

    <div class="vehicle-reg"><strong>Reg Number:</strong> <?php echo htmlspecialchars($row['regNumber'] ?? ''); ?></div>
<div class="vehicle-actions">
  <a href="update_vehicle.php?stockID=<?php echo $row['stockID']; ?>" class="action-btn edit">Edit</a>
  <a href="scripts/delete_vehicle.php?stockID=<?php echo $row['stockID']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this vehicle?');">Delete</a>
<a href="image_manager.php?stockID=<?= $row['stockID'] ?>" class="action-btn images">
    <?= $imageCount > 0 ? "Edit Images ($imageCount)" : "Add Images" ?>
</a>

</div>  </div>
<?php } ?>
</div></div>

    </div>

    <div id="footer1">
        <p>VIEWING BY APPOINTMENT, ALL VEHICLES VALETED TO A VERY HIGH STANDARD</p>
        <p>Phone: 01736 369940 • Mobile: 07887 653155</p>
    </div>

    <div id="footer2">
        <p>
            <a href="../scripts/logout.php" class="btn btn-secondary">Log out</a>
        </p>
        <p style="font-size: 0.6em">
            &copy;2025 Honda-Mini Designs <a href="http://www.honda-mini.co.uk">Site</a> • 
            <a href="mailto:martyn@honda-mini.co.uk">Contact</a>
        </p>
    </div>

</div>
<!-- old modal code
 <div id="imageModal" class="modal" style="display: none;">
  <span class="modal-close" onclick="document.getElementById('imageModal').style.display='none'">✖</span>
  <div class="modal-content">Loading...</div>
</div>
<script>
$(document).ready(function () {
    // Open image manager modal
    $('.open-image-manager').on('click', function (e) {
        e.preventDefault();
        const stockID = $(this).data('stockid');

        $('#imageModal .modal-content').load('image_manager.php?stockID=' + stockID, function (response, status, xhr) {
            if (status === 'error') {
                $('#imageModal .modal-content').html('<p>Error loading image manager.</p>');
                console.error('AJAX error:', xhr.statusText);
            }
        });

        $('#imageModal').fadeIn();
    });

    // Close modal when clicking outside content
    $(document).on('click', function (e) {
        if ($(e.target).is('#imageModal')) {
            $('#imageModal').fadeOut();
        }
    });

    // DELETE form handler
    $(document).on('submit', '.delete-form', function (e) {
        e.preventDefault();
        if (!confirm('Delete this image?')) return;

        const $form = $(this);
        const formData = $form.serialize();

        $.post($form.attr('action'), formData, function (response) {
            if (response.status === 'success') {
                $('#image-manager-content').html(response.updatedContent);
            }

            showMessage(response.status, response.msg);
        }, 'json').fail(function () {
            alert('There was a problem processing the request.');
        });
    });

    // RENAME form handler
    $(document).on('submit', '.rename-form', function (e) {
        e.preventDefault();

        const $form = $(this);
        const formData = $form.serialize();

        $.post($form.attr('action'), formData, function (response) {
            if (response.status === 'success') {
                $form.closest('.image-card').replaceWith(response.updatedContent);
            }

            showMessage(response.status, response.msg);
        }, 'json').fail(function () {
            alert('There was a problem processing the request.');
        });
    });

    // Message helper
    function showMessage(status, msg) {
        const msgClass = status === 'success' ? 'status-success' : 'status-error';
        const message = `<div class="status-message ${msgClass}">${msg}</div>`;
        $('.status-message').remove();
        $('#image-manager-content').before(message);

        setTimeout(function () {
            $('.status-message').fadeTo(1000, 0).slideUp(500, function () {
                $(this).remove();
            });
        }, 5000);
    }
});
</script> -->

<script>
document.getElementById('nav-toggle').addEventListener('click', function() {
  var navList = document.querySelector('#navbar');
  navList.classList.toggle('open');
});
</script>

</body>
</html>

<?php
$result->free();
$pg_services->close();
?>
