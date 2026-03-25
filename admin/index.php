<?php include 'includes/_header.php'; ?>

<h1>Dashboard</h1>
<p class="text-muted">Welcome to the PG Services admin panel. Choose an action below.</p>

<div class="dashboard-grid">

    <a href="manage_stock.php" class="dashboard-card">
        <h2>Manage Stock</h2>
        <p>View, edit, and update all vehicles currently in stock.</p>
    </a>

    <a href="add_vehicle.php" class="dashboard-card">
        <h2>Add Vehicle</h2>
        <p>Add a new vehicle to the PG Services inventory.</p>
    </a>

    <a href="manage_users.php" class="dashboard-card">
        <h2>Manage Users</h2>
        <p>View, add, or remove admin users.</p>
    </a>

</div>

<?php include 'includes/_footer.php'; ?>
