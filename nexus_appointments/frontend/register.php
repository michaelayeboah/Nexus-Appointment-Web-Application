<?php

require_once '../backend/config.php';

$message = ""; 

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)");
        
        // Execute the query
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password_hash' => $hashed_password
        ]);
        
        // AUTO-LOGIN: Fetch the newly created ID and start the session
        session_start();
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $name;
        
        // Instantly redirect to the dashboard
        header("Location: dashboard.php");
        exit();
    } catch(PDOException $e) {
        $message = "Error: Could not create account. Email might already exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Nexus Appointments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">Nexus Appointments</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="login.php" class="btn-outline">Log In</a>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content" style="max-width: 400px; margin: 0 auto; text-align: left;">
            <h2 style="text-align: center; margin-bottom: 20px;">Create an Account</h2>
            
            <?php if($message): ?>
                <p style="text-align: center; color: #2563eb; font-weight: bold;"><?php echo $message; ?></p>
            <?php endif; ?>

            <form method="POST" action="register.php" style="display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <label>Full Name</label><br>
                    <input type="text" name="name" required style="width: 100%; padding: 10px; margin-top: 5px;">
                </div>
                <div>
                    <label>Email Address</label><br>
                    <input type="email" name="email" required style="width: 100%; padding: 10px; margin-top: 5px;">
                </div>
                <div>
                    <label>Password</label><br>
                    <input type="password" name="password" required style="width: 100%; padding: 10px; margin-top: 5px;">
                </div>
                <button type="submit" class="btn-primary" style="margin-top: 10px; width: 100%;">Sign Up</button>
            </form>
        </div>
    </section>
</body>
</html>