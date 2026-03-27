<?php
require_once('../Connections/pg_services.php');

// Optional success message
$uploadMessage = '';
if (!empty($_SESSION['success'])) {
    $uploadMessage = $_SESSION['success'];
    unset($_SESSION['success']);
}

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
$query = "SELECT stockID, make, model, trim, regNumber, price, featured, reserved, sold
          FROM stock 
          $whereSQL
          ORDER BY updated DESC";

$result = $pg_services->query($query);
?>

<?php include("includes/_header.php"); ?>


    <div class="admin-header-row">
        <h1 class="page-title">Manage Vehicle Stock</h1>
        <a href="add_vehicle.php" class="admin-btn primary">
            + Add Vehicle
        </a>
    </div>

    <?php if ($uploadMessage): ?>
        <div class="admin-alert success">
            <?= htmlspecialchars($uploadMessage) ?>
        </div>
    <?php endif; ?>

    <!-- Search + Filters -->
    <form method="GET" class="admin-filter-bar">
        <input type="text" 
               name="q" 
               placeholder="Search make, model, reg, ID…" 
               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
               class="admin-input">

        <select name="make" class="admin-select">
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

        <select name="hasImages" class="admin-select">
            <option value="">All Vehicles</option>
            <option value="1" <?= (($_GET['hasImages'] ?? '') === '1') ? 'selected' : '' ?>>Has Images</option>
            <option value="0" <?= (($_GET['hasImages'] ?? '') === '0') ? 'selected' : '' ?>>No Images</option>
        </select>

        <button type="submit" class="admin-btn secondary">Filter</button>
    </form>

    <!-- Vehicle Cards -->
    <div class="vehicle-grid">

    <?php while ($row = $result->fetch_assoc()) { ?>

        <?php
        // Image logic preserved
        $imageDir = __DIR__ . "/..//images/cars/" . $row['stockID'] . "/";
        $imageCount = 0;
        $primaryThumb = null;

        if (is_dir($imageDir)) {
            foreach (scandir($imageDir) as $file) {
                if (preg_match('/^[0-9]+\.webp$/', $file)) {
                    $imageCount++;
                }
            }

            $orderFile = $imageDir . "order.json";
            if (file_exists($orderFile)) {
                $order = json_decode(file_get_contents($orderFile), true);

                if (is_array($order) && count($order) > 0) {
                    $base = pathinfo($order[0], PATHINFO_FILENAME);

                    $thumb400 = "../images/cars/{$row['stockID']}/{$base}_400.webp";
                    $thumb800 = "../images/cars/{$row['stockID']}/{$base}_800.webp";
                    $thumbFull = "../images/cars/{$row['stockID']}/{$order[0]}";

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

        $make  = trim($row['make']  ?? '');
        $model = trim($row['model'] ?? '');
        $trim  = trim($row['trim']  ?? '');
        $fullName = trim("$make $model $trim");

        // Price formatting (admin-side, simple UK format)
        $price = $row['price'] ?? null;
        $priceDisplay = $price !== null && $price !== ''
            ? '£' . number_format((float)$price, 0, '.', ',')
            : 'POA';
        ?>

        <div class="vehicle-card">

    <div class="vehicle-image-wrapper">
        <?php if ($primaryThumb): ?>
            <img src="<?= $primaryThumb ?>" class="vehicle-thumb" alt="Vehicle image">
        <?php else: ?>
            <div class="vehicle-thumb placeholder">No Image</div>
        <?php endif; ?>

        <div class="status-badge-group">
            <?php if ($row['featured']): ?>
                <div class="status-badge status-featured">FEATURED</div>
            <?php endif; ?>

            <?php if ($row['reserved']): ?>
                <div class="status-badge status-reserved">RESERVED</div>
            <?php endif; ?>

            <?php if ($row['sold']): ?>
                <div class="status-badge status-sold">SOLD</div>
            <?php endif; ?>
        </div>
    </div>

            <div class="vehicle-card-body">
               <div class="vehicle-card-header">
    <span class="vehicle-id">ID: <?= htmlspecialchars($row['stockID']); ?></span>

    <div class="vehicle-header-right">
<?php if (!empty($row['price']) && $row['price'] > 0): ?>
    <span class="vehicle-price-badge"><?= htmlspecialchars($priceDisplay); ?></span>
<?php endif; ?>
       
    </div>
</div>



                <div class="vehicle-info">
                    <div class="vehicle-title">
                        <?= htmlspecialchars($fullName ?: 'Unknown Vehicle'); ?>
                    </div>
                    <div class="vehicle-reg">
                        Reg: <?= htmlspecialchars($row['regNumber'] ?? ''); ?>
                    </div>
                </div>

                <div class="vehicle-actions">
                    <a href="update_vehicle.php?stockID=<?= $row['stockID']; ?>" class="admin-btn small">
                        Edit
                    </a>
                    <a href="scripts/delete_vehicle.php?stockID=<?= $row['stockID']; ?>" 
                       class="admin-btn small danger"
                       onclick="return confirm('Are you sure you want to delete this vehicle?');">
                        Delete
                    </a>
                    <a href="image_manager.php?stockID=<?= $row['stockID'] ?>" class="admin-btn small">
                        <?= $imageCount > 0 ? "Edit Images ($imageCount)" : "Add Images" ?>
                    </a>
                </div>
                
            </div>

        </div>

    <?php } ?>

    </div>

    <!-- Floating Add Vehicle button (mobile-friendly) -->
    <a href="add_vehicle.php" class="fab-add-vehicle">
        +
    </a>

</div>

<?php include("includes/_footer.php"); ?>

<?php
$result->free();
$pg_services->close();
?>
