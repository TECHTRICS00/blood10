<?php
$servername = "localhost";
$username = "your_database_username";
$password = "your_database_password";
$dbname = "your_database_name";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get temperature value
if (isset($_GET['temp'])) {
    $temperature = floatval($_GET['temp']);
    
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO temperature_logs (temperature, timestamp) VALUES (?, NOW())");
    $stmt->bind_param("d", $temperature);
    
    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
?>