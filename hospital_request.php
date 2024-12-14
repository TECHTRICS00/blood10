<?php
// Start the session
session_start();

// Database Configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "data00";

// Create a database connection
$conn = new mysqli($host, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Initialize variables
$blood_group_filter = '';
$location_filter = '';
$request_success = false;
$confirmation_success = false;

// Handle Blood Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['request_blood'])) {
        // Get donor ID and hospital ID
        $donor_id = $_POST['donor_id'];
        $hospital_id = $_SESSION['hospital_id'];

        // Fetch the hospital's name
        $sql = "SELECT name FROM hospitals WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $hospital_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $hospital = $result->fetch_assoc();
            $hospital_name = $hospital['name'];

            // Update donor's request status
            $update_sql = "UPDATE donors SET request_status = 'Requested' WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $donor_id);
            $update_stmt->execute();

            // Insert notification
            $notification_message = "YOU HAVE BLOOD REQUEST FROM $hospital_name";
            $insert_sql = "INSERT INTO notifications (donor_id, message, status) VALUES (?, ?, 'unread')";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("is", $donor_id, $notification_message);

            if ($insert_stmt->execute()) {
                $request_success = true; // Indicate success
            }

            $insert_stmt->close();
            $update_stmt->close();
        }

        $stmt->close();

        // Redirect back to the same page to avoid resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Handle Hospital Confirmation
    if (isset($_POST['confirm_request'])) {
        $donor_id = $_POST['donor_id'];

        // Update donor's request status to "Confirmed"
        $update_sql = "UPDATE donors SET request_status = 'Confirmed' WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $donor_id);

        if ($stmt->execute()) {
            // Insert confirmation notification
            $notification_message = "YOUR BLOOD REQUEST HAS BEEN CONFIRMED BY THE HOSPITAL.";
            $insert_sql = "INSERT INTO notifications (donor_id, message, status) VALUES (?, ?, 'unread')";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("is", $donor_id, $notification_message);
            $insert_stmt->execute();
            $confirmation_success = true;
        }

        $stmt->close();
    }

    // Handle filtering
    if (isset($_POST['filter'])) {
        $blood_group_filter = $_POST['blood_group'];
        $location_filter = $_POST['location'];
    }
}

// Fetch donor data with filters
$sql = "SELECT id, name, age, blood_group, location, email, request_status FROM donors WHERE 1";
$params = [];
$types = '';

if (!empty($blood_group_filter)) {
    $sql .= " AND blood_group = ?";
    $params[] = $blood_group_filter;
    $types .= 's';
}

if (!empty($location_filter)) {
    $sql .= " AND location = ?";
    $params[] = $location_filter;
    $types .= 's';
}

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Details</title>
    <style>
        .status-default {
            background-color: transparent;
        }
        .status-requested {
            background-color: #90EE90; /* Light Green */
        }
        .status-confirmed {
            background-color: #ADD8E6; /* Light Blue */
        }
        .request-btn-disabled, .confirm-btn-disabled {
            background-color: #CCCCCC;
            cursor: not-allowed;
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Donor Details</h1>
        
        <!-- Filter Form -->
        <form method="POST" class="filter-form">
            <label for="blood_group">Blood Group:</label>
            <input type="text" name="blood_group" id="blood_group" value="<?php echo htmlspecialchars($blood_group_filter); ?>" placeholder="e.g., A+">

            <label for="location">Location:</label>
            <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($location_filter); ?>" placeholder="e.g., New York">

            <button type="submit" name="filter" class="filter-btn">Filter</button>
        </form>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Blood Group</th>
                        <th>Location</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="<?php 
                            echo ($row['request_status'] == 'Requested') ? 'status-requested' : 
                                 (($row['request_status'] == 'Confirmed') ? 'status-confirmed' : 'status-default'); ?>">
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['blood_group']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['request_status']); ?></td>
                            <td>
                                <?php if ($row['request_status'] == 'Requested'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="donor_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="confirm_request" class="confirm-btn">Confirm</button>
                                    </form>
                                <?php elseif ($row['request_status'] != 'Confirmed'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="donor_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="request_blood" class="request-btn">Request Blood</button>
                                    </form>
                                <?php else: ?>
                                    <button class="confirm-btn confirm-btn-disabled" disabled>Confirmed</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No donors found in the database.</p>
        <?php endif; ?>
        <div class="back-home">
            <a href="main.php">Back to Home</a>
        </div>
    </div>
</body>
</html>

 
<style>
/* Blood-themed styles for the page */
:root {
    --blood-primary: #8B0000; /* Dark red */
    --blood-secondary: #C21807; /* Crimson red */
    --blood-hover: #FF6347; /* Lighter blood red */
    --blood-white: #FFFFFF;
    --blood-shadow: rgba(139, 0, 0, 0.3);
}

/* Body Styling */
body {
    font-family: 'Arial', sans-serif;
    background-color: #F8F9FA; /* Light gray background for contrast */
    color: var(--blood-primary);
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

/* Container Styling */
.container {
    background-color: var(--blood-white);
    border-left: 6px solid var(--blood-primary);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 8px 20px var(--blood-shadow);
    width: 90%;
    max-width: 1200px;
    animation: fadeIn 1s ease;
}

/* Header */
.container h1 {
    text-align: center;
    color: var(--blood-primary);
    margin-bottom: 20px;
    text-transform: uppercase;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table th,
table td {
    border: 1px solid var(--blood-secondary);
    padding: 12px;
    text-align: left;
}

table th {
    background-color: var(--blood-primary);
    color: var(--blood-white);
}

table tr:nth-child(even) { 
   background-color: #FFE8E8; 
}

table tr:hover { 
   background-color: #FFCCCC; 
   transition: background-color 0.3s ease; 
}

/* Status Styles */
.status-default { background-color: transparent; }
.status-requested { background-color: #90EE90; } /* Light Green */
.status-confirmed { background-color: #ADD8E6; } /* Light Blue */

/* Button Styles */
.request-btn,
.confirm-btn {
    background-color: var(--blood-secondary);
    color: var(--blood-white);
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.request-btn:hover,
.confirm-btn:hover { 
   background-color: var(--blood-hover); 
   transform: scale(1.05); 
}

.request-btn-disabled,
.confirm-btn-disabled {
   background-color: #CCCCCC; 
   cursor:not-allowed; 
   opacity:.6; 
}

/* Back to Home Button */
.back-home { text-align:center; }

.back-home a { 
   color: var(--blood-white); 
   background-color: var(--blood-primary); 
   text-decoration:none; 
   padding:10px 20px; 
   border-radius:10px; 
   font-weight:bold; 
   transition:.3s ease; 
}

.back-home a:hover { 
   background-color: var(--blood-hover); 
   transform:.95s ease; 
}

/* Animations */
@keyframes fadeIn {
   from { opacity:.5; transform translateY(20px); }
   to { opacity:.9; transform translateY(0); }
}

</style>
