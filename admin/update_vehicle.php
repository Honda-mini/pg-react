<?php include("includes/_header.php"); ?>
<?php
require_once('../src/utils/pg_services.php');
require_once('scripts/auth_session.php');

$success = false;
$error = '';
$stockID = isset($_GET['stockID']) ? (int)$_GET['stockID'] : 0;
$vehicle = [];

// Connect
$mysqli = new mysqli($hostname_pg_services, $username_pg_services, $password_pg_services, $database_pg_services);
if ($mysqli->connect_error) {
    die("DB connection failed: " . $mysqli->connect_error);
}

// Fetch current data
if ($stockID) {
    $stmt = $mysqli->prepare("SELECT * FROM stock WHERE stockID = ?");
    $stmt->bind_param("i",  $stockID);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();
    $stmt->close();
    if (!$vehicle) {
        $error = "✖ Vehicle not found or invalid stock ID.";
    }
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['MM_update']) && $_POST['MM_update'] === 'form1') {

    $make         = $_POST['make'];
    $model        = $_POST['model'];
    $trim         = $_POST['trim'];
    $additional   = $_POST['additional'];
    $yearPlate    = $_POST['yearPlate'];
    $regNumber    = $_POST['regNumber'];
    $fuelType     = $_POST['fuelType'];
    $engineSize   = (int)$_POST['engineSize'];
    $mileage      = (int)$_POST['mileage'];
    $transmission = $_POST['transmission'];
    $bodyType     = $_POST['bodyType'];
    $powerBhp     = (int)$_POST['powerBhp'];
    $doorsNo      = (int)$_POST['doorsNo'];
    $colour       = $_POST['colour'];
    $description  = $_POST['description'];
    $price        = $_POST['price'];

    $sql = "UPDATE stock SET make=?, model=?, trim=?, additional=?, yearPlate=?, regNumber=?, fuelType=?, engineSize=?, mileage=?, transmission=?, bodyType=?, powerBhp=?, doorsNo=?, colour=?, description=?, price=?, featured=?, reserved=?, sold=? WHERE stockID=?";
    $stmt = $mysqli->prepare($sql);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $reserved = isset($_POST['reserved']) ? 1 : 0;
    $sold     = isset($_POST['sold']) ? 1 : 0;

// enforce exclusivity
    if ($reserved && $sold) {
        $reserved = 0;
}


    if (!$stmt) {
        $error = "Prepare failed: " . $mysqli->error;
    } else {
        $stmt->bind_param(
    "sssssssiissiisssiiii",
    $make, $model, $trim, $additional, $yearPlate,
    $regNumber, $fuelType, $engineSize, $mileage,
    $transmission, $bodyType, $powerBhp, $doorsNo,
    $colour, $description, $price,
    $featured, $reserved, $sold,
    $stockID
);


        if ($stmt->execute()) {
            $_SESSION['uploadMessage'] = "✔ Vehicle #$stockID successfully updated!";
            $_SESSION['uploadMessageType'] = "success";
            header("Location: manage_stock.php");
            exit;
        } else {
            $error = "Update failed: " . $stmt->error;
        }
        $stmt->close();
    }
}

$mysqli->close();
?>



    <h1 class="admin-title">Update Vehicle #<?= htmlspecialchars($stockID) ?></h1>

    <?php if ($error): ?>
        <div class="admin-alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($vehicle): ?>

    <div class="admin-card">
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?stockID=' . $stockID ?>" method="POST" class="admin-form">

            <div class="form-grid">

                <div class="form-group">
                    <label>Make</label>
                    <input type="text" name="make" value="<?= htmlspecialchars($vehicle['make']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Model</label>
                    <input type="text" name="model" value="<?= htmlspecialchars($vehicle['model']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Trim</label>
                    <input type="text" name="trim" value="<?= htmlspecialchars($vehicle['trim']) ?>">
                </div>

                <div class="form-group full">
                    <label>Extra Info</label>
                    <textarea name="additional" rows="3"><?= htmlspecialchars($vehicle['additional']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Year / Plate</label>
                    <input type="text" name="yearPlate" value="<?= htmlspecialchars($vehicle['yearPlate']) ?>">
                </div>

                <div class="form-group">
                    <label>Registration Number</label>
                    <input type="text" name="regNumber" value="<?= htmlspecialchars($vehicle['regNumber']) ?>">
                </div>

                <div class="form-group">
                    <label>Fuel Type</label>
                    <input type="text" name="fuelType" value="<?= htmlspecialchars($vehicle['fuelType']) ?>">
                </div>

                <div class="form-group">
                    <label>Engine Size</label>
                    <input type="text" name="engineSize" value="<?= htmlspecialchars($vehicle['engineSize']) ?>">
                </div>

                <div class="form-group">
                    <label>Mileage</label>
                    <input type="text" name="mileage" value="<?= htmlspecialchars($vehicle['mileage']) ?>">
                </div>

                <div class="form-group">
                    <label>Transmission</label>
                    <input type="text" name="transmission" value="<?= htmlspecialchars($vehicle['transmission']) ?>">
                </div>

                <div class="form-group">
                    <label>Body Type</label>
                    <input type="text" name="bodyType" value="<?= htmlspecialchars($vehicle['bodyType']) ?>">
                </div>

                <div class="form-group">
                    <label>Power (BHP)</label>
                    <input type="text" name="powerBhp" value="<?= htmlspecialchars($vehicle['powerBhp']) ?>">
                </div>

                <div class="form-group">
                    <label>Doors</label>
                    <input type="text" name="doorsNo" value="<?= htmlspecialchars($vehicle['doorsNo']) ?>">
                </div>

                <div class="form-group">
                    <label>Colour</label>
                    <input type="text" name="colour" value="<?= htmlspecialchars($vehicle['colour']) ?>">
                </div>

                <div class="form-group full">
                    <label>Description</label>
                    <textarea name="description" rows="5"><?= htmlspecialchars($vehicle['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Price (£)</label>
                    <input type="text" name="price" value="<?= htmlspecialchars($vehicle['price']) ?>">
                </div>

            </div>
        <div class="status-form">
        <!-- Featured toggle -->
        <label class="toggle">
            <input type="checkbox" name="featured" value="1" <?= $vehicle['featured'] ? 'checked' : '' ?>>
            <span class="slider"></span>
            <span class="toggle-label">Featured</span>
        </label>

        <!-- Reserved toggle -->
        <label class="toggle">
            <input type="checkbox" class="reserved-toggle" name="reserved" value="1" <?= $vehicle['reserved'] ? 'checked' : '' ?>>
            <span class="slider"></span>
            <span class="toggle-label">Reserved</span>
        </label>

        <!-- Sold toggle -->
        <label class="toggle">
            <input type="checkbox" class="sold-toggle" name="sold" value="1" <?= $vehicle['sold'] ? 'checked' : '' ?>>
            <span class="slider"></span>
            <span class="toggle-label">Sold</span>
        </label>

        </div>
</div>

            <input type="hidden" name="MM_update" value="form1">

            <div class="form-actions">
                <button type="submit" class="admin-btn primary">Update Vehicle</button>
                <a href="manage_stock.php" class="admin-btn">Cancel</a>
            </div>

        </form>
    </div>

    <?php endif; ?>

</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const reserved = document.querySelectorAll(".reserved-toggle");
    const sold = document.querySelectorAll(".sold-toggle");

    reserved.forEach(r => {
        r.addEventListener("change", () => {
            if (r.checked) {
                // turn off sold
                r.closest(".status-form").querySelector(".sold-toggle").checked = false;
            }
        });
    });

    sold.forEach(s => {
        s.addEventListener("change", () => {
            if (s.checked) {
                // turn off reserved
                s.closest(".status-form").querySelector(".reserved-toggle").checked = false;
            }
        });
    });
});
</script>

<?php include("includes/_footer.php"); ?>
