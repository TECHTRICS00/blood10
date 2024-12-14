<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file for tracking
$log_file = 'sensor_data_log.txt';

// Function to log errors and messages
function logMessage($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Database credentials
$servername = "sql201.infinityfree.com";
$username = "if0_37888203";
$password = "Bloodiot10";
$dbname = "if0_37888203_data00";

// Log incoming data
logMessage("Received POST Data: " . print_r($_POST, true));

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    logMessage("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data with error checking
$hospital_id = isset($_POST['hospital_id']) ? $conn->real_escape_string($_POST['hospital_id']) : '';
$load_cell_1 = isset($_POST['load_cell_1']) ? $conn->real_escape_string($_POST['load_cell_1']) : 0;
$load_cell_2 = isset($_POST['load_cell_2']) ? $conn->real_escape_string($_POST['load_cell_2']) : 0;
$load_cell_3 = isset($_POST['load_cell_3']) ? $conn->real_escape_string($_POST['load_cell_3']) : 0;
$load_cell_4 = isset($_POST['load_cell_4']) ? $conn->real_escape_string($_POST['load_cell_4']) : 0;
$temperature = isset($_POST['temperature']) ? $conn->real_escape_string($_POST['temperature']) : 0;

// Get current timestamp
$timestamp = date('Y-m-d H:i:s');

// Prepare SQL query
$sql = "INSERT INTO sensordata (
    hospital_id, 
    load_cell_1, 
    load_cell_2, 
    load_cell_3, 
    load_cell_4, 
    temperature, 
    timestamp
) VALUES (
    '$hospital_id', 
    '$load_cell_1', 
    '$load_cell_2', 
    '$load_cell_3', 
    '$load_cell_4', 
    '$temperature', 
    '$timestamp'
)";

// Execute query
if ($conn->query($sql) === TRUE) {
    logMessage("Data inserted successfully");
    echo "Data inserted successfully";
} else {
    logMessage("Error: " . $sql . " || " . $conn->error);
    echo "Error: " . $conn->error;
}

// Close the connection
$conn->close();
?>