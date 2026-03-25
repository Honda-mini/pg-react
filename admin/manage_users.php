<?php include("includes/_header.php"); ?>

<?php
require_once('../src/utils/pg_services.php');
require_once('scripts/auth_session.php');

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $newUsername = trim($_POST['new_username']);
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $pg_services->prepare("INSERT INTO users (userName, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $newUsername, $newPassword);
    $stmt->execute();
    $stmt->close();

    $_SESSION['uploadMessage'] = "✔ User '$newUsername' created successfully.";
    $_SESSION['uploadMessageType'] = "success";

    header("Location: manage_users.php");
    exit;
}

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];

    // Prevent deleting yourself
    if ($deleteId == $_SESSION['user_id']) {
        $_SESSION['uploadMessage'] = "✖ You cannot delete your own account.";
        $_SESSION['uploadMessageType'] = "error";
        header("Location: manage_users.php");
        exit;
    }

    $stmt = $pg_services->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['uploadMessage'] = "✔ User deleted.";
    $_SESSION['uploadMessageType'] = "success";

    header("Location: manage_users.php");
    exit;
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $updateId = (int)$_POST['update_id'];
    $updateUsername = trim($_POST['update_username']);
    $updatePassword = $_POST['update_password'];

    if (!empty($updatePassword)) {
        $hashed = password_hash($updatePassword, PASSWORD_DEFAULT);
        $stmt = $pg_services->prepare("UPDATE users SET userName = ?, password = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $updateUsername, $hashed, $updateId);
    } else {
        $stmt = $pg_services->prepare("UPDATE users SET userName = ? WHERE user_id = ?");
        $stmt->bind_param("si", $updateUsername, $updateId);
    }

    $stmt->execute();
    $stmt->close();

    $_SESSION['uploadMessage'] = "✔ User updated.";
    $_SESSION['uploadMessageType'] = "success";

    header("Location: manage_users.php");
    exit;
}

// Fetch all users
$result = $pg_services->query("SELECT user_id, userName FROM users ORDER BY user_id ASC");
?>


    <h1 class="admin-title">Manage Users</h1>

    <?php if (isset($_SESSION['uploadMessage'])): ?>
        <div class="admin-alert <?= $_SESSION['uploadMessageType'] ?>">
            <?= $_SESSION['uploadMessage'] ?>
        </div>
        <?php unset($_SESSION['uploadMessage'], $_SESSION['uploadMessageType']); ?>
    <?php endif; ?>

    <!-- Create User Card -->
    <div class="admin-card mb-2">
        <h2 class="admin-card-title">Create New User</h2>

        <form method="post" class="admin-form">
            <input type="hidden" name="create_user" value="1">

            <div class="form-grid">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="new_username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="new_password" required>
                </div>
            </div>

            <p class="form-hint">Passwords are securely hashed and cannot be retrieved later.</p>

            <div class="form-actions">
                <button type="submit" class="admin-btn primary">Create User</button>
            </div>
        </form>
    </div>

    <!-- Existing Users -->
    <div class="admin-card">
        <h2 class="admin-card-title">Existing Users</h2>

        <div class="image-grid"><!-- reused grid layout for cards -->
            <?php while ($row = $result->fetch_assoc()): ?>
                <form method="post" class="image-card user-card">

                    <input type="hidden" name="update_user" value="1">
                    <input type="hidden" name="update_id" value="<?= $row['user_id'] ?>">

                    <div class="card-body">
                        <div class="filename"><strong>ID:</strong> <?= $row['user_id'] ?></div>

                        <div class="form-group mt-1">
                            <label>Username</label>
                            <input type="text" name="update_username" value="<?= htmlspecialchars($row['userName']) ?>" required>
                        </div>

                        <div class="form-group mt-1">
                            <label>New Password</label>
                            <input type="password" name="update_password" placeholder="Leave blank to keep existing">
                        </div>
                    </div>

                    <div class="card-footer-btns">
                        <button type="submit" class="admin-btn small">Update</button>
                        <a href="manage_users.php?delete=<?= $row['user_id'] ?>"
                           class="admin-btn danger small"
                           onclick="return confirm('Delete this user?');">
                            Delete
                        </a>
                    </div>

                </form>
            <?php endwhile; ?>
        </div>
    </div>

</div>

<?php include("includes/_footer.php"); ?>
