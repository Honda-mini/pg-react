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
// Fetch stock data
$query = "SELECT stockID, make, model, trim, regNumber, price FROM stock ORDER BY updated DESC";
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


<div class="vehicle-list">
<?php while ($row = $result->fetch_assoc()) { ?>
<?php
$imageDir = __DIR__ . "/../images/cars/" . $row['stockID'] . "/";
$imageCount = 0;
if (is_dir($imageDir)) {
    $files = array_diff(scandir($imageDir), ['.', '..', 'thumbs', '.DS_Store', 'order.json']);
    foreach ($files as $file) {
        if (is_file($imageDir . $file) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
            $imageCount++;
        }
    }
}
?>
  <div class="vehicle-card">
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

        $('#imageModal .modal-content').load('views/image_manager.php?stockID=' + stockID, function (response, status, xhr) {
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
