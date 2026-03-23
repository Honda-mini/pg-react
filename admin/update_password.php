<?php
require_once('../Connections/pg_services.php');
session_start();

// Fetch users from DB for dropdown
$users = [];
$sql = "SELECT userName FROM users ORDER BY userName";
$result = $pg_services->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row['userName'];
    }
}

// Handle form submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedUser = $_POST['user'] ?? '';
    $newPassword = $_POST['password'] ?? '';

    if (!empty($selectedUser) && !empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pg_services->prepare("UPDATE users SET password = ? WHERE userName = ?");
        $stmt->bind_param("ss", $hashedPassword, $selectedUser);

        if ($stmt->execute()) {
            $message = "Password updated for user: <strong>{$selectedUser}</strong>";
        } else {
            $message = "Error updating password.";
        }
        $stmt->close();
    } else {
        $message = "Please select a user and enter a password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update User Password</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        label { display: block; margin-top: 15px; }
        input[type="password"], select { padding: 5px; width: 250px; }
        .message { margin-top: 20px; color: green; }
    </style>
</head>
<body>
    <h1>Update User Password</h1>

    <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="user">Select User:</label>
        <select name="user" id="user" required>
            <option value="">-- Choose a user --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user) ?>"><?= htmlspecialchars($user) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="password">New Password:</label>
        <input type="password" name="password" id="password" required />

        <br><br>
        <input type="submit" value="Update Password">
    </form>
</body>
</html>
