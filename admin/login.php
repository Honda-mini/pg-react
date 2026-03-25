<?php

require_once('../src/utils/pg_services.php');
session_start();

// Check DB connection
if ($pg_services->connect_error) {
    die("Database connection failed: " . $pg_services->connect_error);
}

// Redirect target
$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
    $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

// Handle login
// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginUsername = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pg_services->prepare("SELECT userName, password FROM users WHERE userName = ?");
    $stmt->bind_param("s", $loginUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($fetchedUser, $hashedPassword);
        $stmt->fetch();

       if (is_string($hashedPassword) && password_verify($password, $hashedPassword)) {

            $_SESSION['username'] = $fetchedUser;
            $_SESSION['MM_Username'] = $fetchedUser;
            $_SESSION['MM_UserGroup'] = "";
            $_SESSION['admin_logged_in'] = true; // this is the key flag

            $redirect = $_SESSION['PrevUrl'] ?? "index.php";
            header("Location: $redirect");
            exit;
        }
    }

    header("Location: login.php?error=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login – PG Services</title>
    <link rel="stylesheet" href="includes/admin.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <h1>Admin Login</h1>

        <?php if (isset($_GET['error'])): ?>
            <p style="color:#d9534f; text-align:center; margin-bottom:15px;">
                Invalid username or password.
            </p>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($loginFormAction); ?>" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit" class="button">Log In</button>
        </form>

        <p style="text-align:center; margin-top:20px; color:#666; font-size:0.85rem;">
            Authorized users only.
        </p>
    </div>
</div>

</body>
</html>
