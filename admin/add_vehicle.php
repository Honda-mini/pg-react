<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../Connections/pg_services.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['MM_insert']) && $_POST['MM_insert'] === 'form1') {

    // Collect form fields
    $make         = $_POST['make'];
    $model        = $_POST['model'];
    $trim         = $_POST['trim'];
    $additional   = $_POST['additional'];
    $yearPlate    = $_POST['yearPlate'];
    $regNumber    = $_POST['regNumber'];
    $fuelType     = $_POST['fuelType'];
    $engineSize   = $_POST['engineSize'];
    $mileage      = $_POST['mileage'];
    $transmission = $_POST['transmission'];
    $bodyType     = $_POST['bodyType'];
    $powerBhp     = $_POST['powerBhp'];
    $doorsNo      = $_POST['doorsNo'];
    $colour       = $_POST['colour'];
    $description  = $_POST['description'];
    $price        = $_POST['price'];

    // Featured toggle (tinyint)
    $featured = isset($_POST['featured']) ? 1 : 0;

    // Prepare SQL
    $stmt = $pg_services->prepare("
        INSERT INTO stock (
            make, model, trim, additional, yearPlate, regNumber, fuelType,
            engineSize, mileage, transmission, bodyType, powerBhp, doorsNo,
            colour, description, price, featured
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die('Database error: ' . $pg_services->error);
    }

    $stmt->bind_param(
        "ssssssssssssssssi",
        $make, $model, $trim, $additional, $yearPlate, $regNumber, $fuelType,
        $engineSize, $mileage, $transmission, $bodyType, $powerBhp, $doorsNo,
        $colour, $description, $price, $featured
    );

    $stmt->execute();

    if ($stmt->error) {
        die("Insert failed: " . $stmt->error);
    }

    $stockID = $stmt->insert_id;
    $stmt->close();

    // Handle image upload
    if (!empty($_FILES['pix']['name'][0])) {
        $_FILES['images'] = $_FILES['pix'];
        $_POST['stockID'] = $stockID;
        include("scripts/upload_images.php");
        exit;
    }
}
?>

<?php include("includes/_header.php"); ?>


    <h1 class="admin-title">Add Vehicle</h1>

    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data" class="admin-form">

        <div class="form-grid">

    <div class="form-group">
        <label>Make</label>
        <input type="text" name="make" placeholder="e.g. Ford" required>
    </div>

    <div class="form-group">
        <label>Model</label>
        <input type="text" name="model" placeholder="e.g. Fiesta" required>
    </div>

    <div class="form-group">
        <label>Trim</label>
        <input type="text" name="trim" placeholder="e.g. Titanium X">
    </div>

    <div class="form-group full">
        <label>Extra Info</label>
        <textarea name="additional" rows="3" placeholder="Optional notes… These form part of the title, along with make, model and trim."></textarea>
    </div>

    <div class="form-group">
        <label>Year / Plate</label>
        <input type="text" name="yearPlate" placeholder="e.g. 2015 / 65">
    </div>

    <div class="form-group">
        <label>Registration Number</label>
        <input type="text" name="regNumber" placeholder="e.g. AB12 CDE">
    </div>

    <div class="form-group">
        <label>Fuel Type</label>
        <input type="text" name="fuelType" placeholder="e.g. Petrol">
    </div>

    <div class="form-group">
        <label>Engine Size</label>
        <input type="text" name="engineSize" placeholder="e.g. 1998">
    </div>

    <div class="form-group">
        <label>Mileage</label>
        <input type="text" name="mileage" placeholder="e.g. 123000">
    </div>

    <div class="form-group">
        <label>Transmission</label>
        <input type="text" name="transmission" placeholder="e.g. Manual">
    </div>

    <div class="form-group">
        <label>Body Type</label>
        <input type="text" name="bodyType" placeholder="e.g. Hatchback">
    </div>

    <div class="form-group">
        <label>Power (BHP)</label>
        <input type="text" name="powerBhp" placeholder="e.g. 150">
    </div>

    <div class="form-group">
        <label>Doors</label>
        <input type="text" name="doorsNo" placeholder="e.g. 5">
    </div>

    <div class="form-group">
        <label>Colour</label>
        <input type="text" name="colour" placeholder="e.g. Black">
    </div>

    <div class="form-group full">
        <label>Description</label>
        <textarea name="description" rows="5" placeholder="Full vehicle description…"></textarea>
    </div>

    <div class="form-group">
        <label>Price (£)</label>
        <input type="text" name="price" placeholder="e.g. 7995">
    </div>

    <div class="form-group full">
        <label>Upload Images</label>
        <input type="file" name="pix[]" multiple accept="image/*">
        <div id="previewContainer" class="image-preview"></div>
    </div>

</div>


        <!-- Featured toggle -->
        <div class="form-group toggle-group">
            <label class="toggle">
                <input type="checkbox" name="featured" value="1">
                <span class="slider"></span>
                <span class="toggle-label">Featured</span>
            </label>
        </div>

        <input type="hidden" name="MM_insert" value="form1">

        <div class="form-actions">
            <button type="submit" class="admin-btn primary">Add Vehicle</button>
            <a href="manage_stock.php" class="admin-btn">Cancel</a>
        </div>

    </form>

</div>

<script>
document.querySelector('input[name="pix[]"]').addEventListener('change', function(e) {
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    Array.from(e.target.files).forEach(file => {
        if (!file.type.startsWith('image/')) return;
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.className = 'preview-thumb';
        container.appendChild(img);
    });
});
</script>

<?php include("includes/_footer.php"); ?>
