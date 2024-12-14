<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="form-container">
        <h1>Welcome Back</h1>
        <p>Login to access your account</p>
        <form id="loginForm" action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn">Login</button>
            <p class="register-link">Don't have an account? <a href="Registration.php">Register here</a></p>
        </form>
    </div>
</body>
</html>

<style>
  /* styles.css */
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
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
    color: #333;
}

h1 {
    text-align: center;
    margin-bottom: 5px;
    color: #6a11cb;
}

p {
    text-align: center;
    font-size: 14px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    font-size: 14px;
}

input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    outline: none;
    transition: 0.3s;
}

input:focus {
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
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

button.btn:hover {
    background: #2575fc;
}

@media (max-width: 500px) {
    .form-container {
        padding: 20px;
    }
}
/* styles.css */
.register-link {
    text-align: center;
    margin-top: 20px;
    font-size: 14px;
}

.register-link a {
    color: #6a11cb;
    text-decoration: none;
    font-weight: 600;
}

.register-link a:hover {
    text-decoration: underline;
}

</style>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "data00";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check login credentials
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM hospitals WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Start session and store user information
            session_start();
            $_SESSION['id'] = $user['id'];  // Assuming 'id' is the unique identifier for the user
            $_SESSION['hospital_id'] = $user['id'];  // Store hospital_id if needed

            // Redirect to the main page
            echo "<script>alert('Login successful! Welcome, {$user['name']}!'); window.location.href = 'main.php';</script>";
        } else {
            // Invalid password
            echo "<script>alert('Invalid password!'); window.location.href = 'login.php';</script>";
        }
    } else {
        // No user found
        echo "<script>alert('No account found with this email!'); window.location.href = 'login.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>

