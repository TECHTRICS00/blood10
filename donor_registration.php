<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <title>Donor Registration</title>
</head>
<body>
    <div class="form-container">
        <h1>Donor Registration</h1>
        <p>Register now to become a donor and save lives!</p>
        <form id="donorRegistrationForm" action="donor_registration.php" method="POST" onsubmit="return validateDonorForm()">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
            </div>
            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" placeholder="Enter your age" min="18" max="65" required>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="Enter your location">
            </div>
            <div class="form-group">
                <label for="blood_group">Blood Group</label>
                <select id="blood_group" name="blood_group" required>
                    <option value="" disabled selected>Select your blood group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" placeholder="Re-enter your password" required>
            </div>
            <button type="submit" class="btn">Register</button>
            <div class="form-footer">
                <p>Already registered? <a href="donor_login.php">Log in here</a>.</p>
            </div>
        </form>
    </div>
</body>
</html>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        color: #fff;
    }

    .form-container {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 380px;
        color: #333;
        height: auto;
    }

    h1 {
        text-align: center;
        margin-bottom: 10px;
        font-size: 24px;
        color: #6a11cb;
    }

    p {
        text-align: center;
        font-size: 12px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 10px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        font-size: 12px;
    }

    input, select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 12px;
        outline: none;
        transition: 0.3s;
    }

    input:focus, select:focus {
        border-color: #6a11cb;
        box-shadow: 0 0 5px rgba(106, 17, 203, 0.3);
    }

    button.btn {
        width: 100%;
        padding: 10px;
        background: #6a11cb;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    button.btn:hover {
        background: #2575fc;
    }

    .form-footer {
        margin-top: 15px;
        text-align: center;
        font-size: 12px;
    }

    .form-footer a {
        color: #6a11cb;
        text-decoration: none;
        font-weight: 600;
    }

    .form-footer a:hover {
        text-decoration: underline;
    }

    @media (max-width: 500px) {
        .form-container {
            padding: 15px;
        }
    }
</style>

<script>
function validateDonorForm() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return false;
    }

    const phone = document.getElementById('phone').value;
    const phonePattern = /^[0-9]{10}$/;
    if (!phonePattern.test(phone)) {
        alert("Enter a valid 10-digit phone number.");
        return false;
    }

    const age = document.getElementById('age').value;
    if (age < 18 || age > 65) {
        alert("Age must be between 18 and 65.");
        return false;
    }

    return true;
}
</script>

<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "data00";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $age = $conn->real_escape_string($_POST['age']);
    $location = $conn->real_escape_string($_POST['location']);
    $blood_group = $conn->real_escape_string($_POST['blood_group']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Prepare SQL statement
    $sql = "INSERT INTO donors (name, email, phone, age, location, blood_group, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare and bind
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("sssssss", $name, $email, $phone, $age, $location, $blood_group, $password);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href = 'donor_login.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
        // Log the specific error
        error_log("MySQL Error: " . $stmt->error);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>