<?php
session_start();

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

// Assume hospital ID is stored in session (replace this with your logic)
if (!isset($_SESSION['hospital_id'])) {
    die("Error: Hospital not logged in.");
}

$hospitalId = $_SESSION['hospital_id']; // Example: Hardcode for testing if necessary

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['appointment_id'])) {
    $appointmentId = $_POST['appointment_id'];
    $action = $_POST['action']; // 'Confirmed' or 'Rejected'

    // Update the status of the appointment
    $status = ($action === 'Confirmed') ? 'Confirmed' : 'Rejected';
    $updateSql = "UPDATE appointments SET status = ? WHERE id = ? AND hospital_id = ?";
    $stmt = $conn->prepare($updateSql);
    if ($stmt) {
        $stmt->bind_param("sii", $status, $appointmentId, $hospitalId);
        if ($stmt->execute()) {
            echo "<p>Appointment status updated successfully to '$status'.</p>";
        } else {
            echo "<p>Error updating appointment status: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Error preparing statement: " . $conn->error . "</p>";
    }
}

// Fetch appointments for the hospital
$sql = "SELECT id, user_name, appointment_date, message, status 
        FROM appointments 
        WHERE hospital_id = ? 
        ORDER BY appointment_date ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $hospitalId); // Replace "i" with "s" if hospital_id is a string
$stmt->execute();
$result = $stmt->get_result();

// Display appointments
if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr>
            <th>Donor Name</th>
            <th>Appointment Date</th>
            <th>Message</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['appointment_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['message']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>
                <form method='POST'>
                    <input type='hidden' name='appointment_id' value='" . $row['id'] . "'>
                    <button type='submit' name='action' value='Confirmed'>Confirm</button>
                    <button type='submit' name='action' value='Rejected'>Reject</button>
                </form>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No appointments found for this hospital.</p>";
}

$stmt->close();
$conn->close();
?>
<style>
    /* General Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
    color: #333;
}

h1, h2 {
    color: #b30000;
}

table {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

table th, table td {
    padding: 10px 15px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background-color: #b30000;
    color: #fff;
    font-weight: bold;
}

table tr:nth-child(even) {
    background-color: #f2f2f2;
}

table tr:hover {
    background-color: #ffe6e6;
}

/* Buttons */
button {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    color: #fff;
    font-size: 14px;
}

button[name="action"][value="Confirmed"] {
    background-color: #4caf50;
}

button[name="action"][value="Rejected"] {
    background-color: #d32f2f;
}

button:hover {
    opacity: 0.9;
}

/* Messages */
p {
    text-align: center;
    font-size: 18px;
    margin: 15px;
}

p.success {
    color: #4caf50;
    font-weight: bold;
}

p.error {
    color: #d32f2f;
    font-weight: bold;
}

/* Page Header */
.header {
    background-color: #b30000;
    color: #fff;
    padding: 20px 0;
    text-align: center;
    font-size: 24px;
    font-weight: bold;
}

/* Footer */
.footer {
    text-align: center;
    padding: 10px;
    background-color: #b30000;
    color: #fff;
    position: fixed;
    bottom: 0;
    width: 100%;
}
</style>