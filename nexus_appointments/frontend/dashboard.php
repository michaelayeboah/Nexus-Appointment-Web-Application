<?php
session_start();
require_once '../backend/config.php';

// 1. PROTECTED ROUTE: If they are not logged in, kick them back to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// 2. HANDLE CRUD ACTIONS (Create & Update/Cancel)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Action: Book a new service (CREATE)
    if (isset($_POST['book_service'])) {
        // We capture the dynamically generated service name here
        $service_name = $_POST['service_name']; 
        $booking_date = $_POST['booking_date'];

        $stmt = $pdo->prepare("INSERT INTO appointments (user_id, service_name, booking_date) VALUES (:user_id, :service_name, :booking_date)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':service_name' => $service_name,
            ':booking_date' => $booking_date
        ]);
        $message = "Appointment booked successfully!";
    } 
    // Action: Cancel an appointment (UPDATE / SOFT DELETE)
    elseif (isset($_POST['cancel_booking'])) {
        $appointment_id = $_POST['appointment_id'];
        
        // We update the status instead of deleting the row permanently
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ':id' => $appointment_id,
            ':user_id' => $user_id
        ]);
        $message = "Appointment cancelled.";
    }
}

// 3. FETCH APPOINTMENTS (READ)
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE user_id = :user_id ORDER BY booking_date DESC");
$stmt->execute([':user_id' => $user_id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Nexus Appointments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <header class="navbar">
        <div class="logo">Nexus <span style="color: #2563eb;">Appointments</span></div>
        <nav>
            <span style="font-weight: bold; margin-right: 20px; color: #4b5563;">
                Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
            </span>
            <a href="logout.php" class="btn-outline">Log Out</a>
        </nav>
    </header>

    <section class="services">
        <h2>Your Dashboard</h2>
        
        <!-- Toast Notification Pop-up -->
        <?php if($message): ?>
            <div id="toast" class="toast">
                <?php echo $message; ?>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var toast = document.getElementById("toast");
                    toast.classList.add("show");
                    setTimeout(function() {
                        toast.classList.remove("show");
                    }, 3000);
                });
            </script>
        <?php endif; ?>

        <!-- Create Panel with Dynamic Dropdowns -->
        <div style="background: #ffffff; padding: 25px; border-radius: 8px; max-width: 500px; margin: 0 auto 40px auto; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 15px; color: #111827;">Book a New Service</h3>
            
            <form method="POST" action="dashboard.php" style="display: flex; flex-direction: column; gap: 15px; text-align: left;">
                <input type="hidden" name="book_service" value="1">
                
                <!-- Doctor Type Dropdown -->
                <div>
                    <label style="font-weight: bold;">Type of Specialist</label><br>
                    <select id="doctor_type" name="doctor_type" required onchange="updateServices()" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">-- Select a Specialist --</option>
                        <option value="General Practitioner">General Practitioner</option>
                        <option value="Dentist">Dentist</option>
                        <option value="Optometrist">Optometrist</option>
                        <option value="Pediatrician">Pediatrician</option>
                    </select>
                </div>

                <!-- Dynamic Services Dropdown -->
                <div>
                    <label style="font-weight: bold;">Select Service</label><br>
                    <select id="service_name" name="service_name" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">-- Please select a specialist first --</option>
                    </select>
                </div>

                <div>
                    <label style="font-weight: bold;">Date & Time</label><br>
                    <input type="datetime-local" name="booking_date" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Confirm Booking</button>
            </form>

           <script src="app.js"></script>
        </div>

        <!-- Read & Update Panel -->
        <h3 style="color: #111827;">Your Appointments</h3>
        <div style="max-width: 600px; margin: 20px auto; text-align: left;">
            <?php if (count($appointments) > 0): ?>
                <?php foreach ($appointments as $appt): ?>
                    <div style="background: #ffffff; padding: 15px 20px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <div>
                            <strong style="color: #2563eb; font-size: 18px;"><?php echo htmlspecialchars($appt['service_name']); ?></strong><br>
                            <span style="color: #4b5563; font-size: 14px;"><?php echo date('F j, Y, g:i a', strtotime($appt['booking_date'])); ?></span>
                            <br>
                            <span style="font-size: 13px; font-weight: bold; padding: 3px 8px; border-radius: 12px; background: #f3f4f6; color: <?php echo $appt['status'] == 'cancelled' ? '#dc2626' : '#10b981'; ?>;">
                                <?php echo ucfirst(htmlspecialchars($appt['status'])); ?>
                            </span>
                        </div>
                        
                        <?php if ($appt['status'] !== 'cancelled'): ?>
                            <form method="POST" action="dashboard.php">
                                <input type="hidden" name="cancel_booking" value="1">
                                <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                <button type="submit" class="btn-outline" style="border-color: #dc2626; color: #dc2626; padding: 6px 12px; font-size: 14px;">Cancel</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #6b7280; margin-top: 20px;">You have no appointments yet.</p>
            <?php endif; ?>
        </div>
            </section>
  
    <footer class="footer" style="text-align: center; padding: 20px; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb; margin-top: 40px;">
        <div style="margin-bottom: 10px;">
            <p><strong>Contact Us:</strong> mahenkanyeboah@gmail.com | (+233) 554616514</p>
            <p>Accra</p>
        </div>
        <p>&copy; 2026 Nexus Appointments. All rights reserved.</p>
    </footer>

</body>
</html>
</body>
</html>