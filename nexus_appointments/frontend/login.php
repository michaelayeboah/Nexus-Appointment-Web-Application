<?php
session_start();
require_once '../backend/config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Search for the user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify user exists and password is correct
    if ($user && password_verify($password, $user['password_hash'])) {
        // Save user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Nexus Appointments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">Nexus Appointments</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="register.php" class="btn-primary">Sign Up</a>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content" style="max-width: 400px; margin: 0 auto; text-align: left;">
            <h2 style="text-align: center; margin-bottom: 20px;">Log In</h2>
            
            <?php if($error): ?>
                <p style="text-align: center; color: #dc2626; font-weight: bold;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST" action="login.php" style="display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <label>Email Address</label><br>
                    <input type="email" name="email" required style="width: 100%; padding: 10px; margin-top: 5px;">
                </div>
                <div>
                    <label>Password</label><br>
                    <input type="password" name="password" required style="width: 100%; padding: 10px; margin-top: 5px;">
                </div>
                <button type="submit" class="btn-primary" style="margin-top: 10px; width: 100%;">Log In</button>
            </form>
        </div>
    </section>
</body>
</html>