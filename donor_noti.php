<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: donor_login.php");
    exit;
}
$user = $_SESSION['user'];

$donorId = $_SESSION['donor_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data00";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle marking notification as read
if (isset($_POST['mark_as_read'])) {
    $notificationId = $_POST['notification_id'];
    $updateSql = "UPDATE notifications SET status = 'read' WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch notifications 
$notificationSql = "SELECT id, message, status, created_at FROM notifications 
                    WHERE donor_id = ? 
                    ORDER BY created_at DESC";
$notificationStmt = $conn->prepare($notificationSql);
$notificationStmt->bind_param("i", $donorId);
$notificationStmt->execute();
$notificationResult = $notificationStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Notifications</title>
   
</head>
<body>
    <h1>Appointment Notifications</h1>

    <?php if ($notificationResult->num_rows > 0): ?>
        <div class="notifications-container">
            <?php while ($row = $notificationResult->fetch_assoc()): ?>
                <?php 
                // Determine notification type for styling
                $notificationType = strpos(strtolower($row['message']), 'confirmed') !== false ? 'confirmed' : 
                                    (strpos(strtolower($row['message']), 'rejected') !== false ? 'rejected' : '');
                ?>
                <div class="notification <?php echo $row['status'] == 'unread' ? 'unread' : ''; ?> <?php echo $notificationType; ?>">
                    <div class="notification-details">
                        <p><?php echo htmlspecialchars($row['message']); ?></p>
                        <small>Received: <?php echo htmlspecialchars($row['created_at']); ?></small>
                    </div>
                    
                    <?php if ($row['status'] == 'unread'): ?>
                        <div class="notification-actions">
                            <form method="POST">
                                <input type="hidden" name="notification_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="mark_as_read">Mark as Read</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No appointment notifications found.</p>
    <?php endif; ?>
</body>
</html>
<?php
// When confirming or rejecting an appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_appointment'])) {
        $appointmentId = $_POST['appointment_id'];
        $status = 'Confirmed';
        
        // Fetch appointment details
        $appointmentSql = "SELECT donor_id, hospital_name, appointment_date FROM appointments WHERE id = ?";
        $appointmentStmt = $conn->prepare($appointmentSql);
        $appointmentStmt->bind_param("i", $appointmentId);
        $appointmentStmt->execute();
        $appointmentResult = $appointmentStmt->get_result();
        $appointmentDetails = $appointmentResult->fetch_assoc();
        
        $message = "Your appointment at " . $appointmentDetails['hospital_name'] . 
                   " on " . $appointmentDetails['appointment_date'] . " has been confirmed.";
        $donorId = $appointmentDetails['donor_id'];
    } 
    elseif (isset($_POST['reject_appointment'])) {
        $appointmentId = $_POST['appointment_id'];
        $status = 'Rejected';
        
        // Fetch appointment details
        $appointmentSql = "SELECT donor_id, hospital_name, appointment_date FROM appointments WHERE id = ?";
        $appointmentStmt = $conn->prepare($appointmentSql);
        $appointmentStmt->bind_param("i", $appointmentId);
        $appointmentStmt->execute();
        $appointmentResult = $appointmentStmt->get_result();
        $appointmentDetails = $appointmentResult->fetch_assoc();
        
        $message = "Your appointment at " . $appointmentDetails['hospital_name'] . 
                   " on " . $appointmentDetails['appointment_date'] . " has been rejected.";
        $donorId = $appointmentDetails['donor_id'];
    }

    // Update appointment status
    $updateSql = "UPDATE appointments SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $status, $appointmentId);
    
    if ($stmt->execute()) {
        // Insert notification
        $notificationSql = "INSERT INTO notifications (donor_id, message, status) VALUES (?, ?, 'unread')";
        $notificationStmt = $conn->prepare($notificationSql);
        $notificationStmt->bind_param("is", $donorId, $message);
        $notificationStmt->execute();
    }
}?>
<style>
    /* General Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
    color: #333;
    transition: background-color 0.5s ease;
}

h1 {
    text-align: center;
    color: #d63031;
    margin-top: 20px;
    animation: fadeIn 1.5s ease;
}

/* Notifications Styling */
.notifications-container {
    width: 90%;
    margin: 20px auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.notification {
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.notification.unread {
    background-color: #ffe6e6;
    border-left: 5px solid #d63031;
}

.notification.confirmed {
    background-color: #e0f7fa;
    border-left: 5px solid #00b894;
}

.notification.rejected {
    background-color: #fbe9e7;
    border-left: 5px solid #d63031;
}

.notification:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.notification-details p {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.notification-details small {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #555;
}

.notification-actions button {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    background-color: #0984e3;
    color: white;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.notification-actions button:hover {
    background-color: #74b9ff;
    transform: scale(1.05);
}

/* Form Styling */
form {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    animation: fadeInUp 1s ease;
}

form label {
    font-weight: bold;
    color: #d63031;
}

form input[type="text"], form input[type="date"] {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 100%;
    max-width: 200px;
    transition: box-shadow 0.3s ease;
}

form input[type="text"]:focus, form input[type="date"]:focus {
    box-shadow: 0 0 5px #d63031;
}

form button {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    background-color: #4caf50;
    color: white;
    cursor: pointer;
    font-size: 14px;
    margin-top: 10px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

form button:hover {
    background-color: #45a049;
    transform: scale(1.05);
}

/* Messages */
p {
    text-align: center;
    font-size: 18px;
    margin: 15px;
    animation: fadeIn 1s ease;
}

p.success {
    color: #4caf50;
    font-weight: bold;
}

p.error {
    color: #d32f2f;
    font-weight: bold;
}

/* Page Footer */
.footer {
    text-align: center;
    padding: 10px;
    background-color: #d63031;
    color: #fff;
    position: fixed;
    bottom: 0;
    width: 100%;
    animation: slideUp 1s ease;
}

/* Keyframe Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUp {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}</style>