<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: donor_login.php");
    exit;
}

$user = $_SESSION['user'];

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "data00";

// Create a database connection
$conn = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the user details from the database using the session email or ID (for example)
$userId = $user['id'];  // Assuming 'id' is stored in session to identify the user
$sql = "SELECT * FROM donors WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId); // Bind the user ID to the prepared statement
$stmt->execute();
$result = $stmt->get_result();
$currentUser = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #8B0000;
            --secondary-color: #DC143C;
            --background-color: #FFF0F5;
            --text-color: #333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .header {
            background-color: var(--primary-color);
            color: white;
            margin: -30px -30px 30px;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        h1 {
            font-size: 24px;
            position: relative;
            z-index: 1;
        }

        .blood-drop {
            position: absolute;
            top: 10px;
            left: 20px;
            width: 40px;
            height: 40px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M12 2c-5.33 4.55-8 8.48-8 11.8 0 4.98 3.8 8.2 8 8.2s8-3.22 8-8.2c0-3.32-2.67-7.25-8-11.8z"/></svg>') no-repeat center;
            background-size: contain;
            z-index: 0;
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--primary-color);
            grid-column: 1 / 3;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid var(--secondary-color);
            border-radius: 5px;
            transition: border-color 0.3s, box-shadow 0.3s;
            grid-column: span 1;
        }

        input:focus, select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 8px rgba(139, 0, 0, 0.2);
            outline: none;
        }

        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-weight: bold;
            grid-column: 1 / 3;
        }

        button:hover {
            background-color: var(--secondary-color);
        }

        .back-button {
            background-color: #f0f0f0;
            color: #8B0000;
            border: 1px solid #8B0000;
            grid-column: 1 / 3;
        }

        .back-button:hover {
            background-color: #DC143C;
            color: white;
        }

        @media (max-width: 600px) {
            form {
                grid-template-columns: 1fr;
            }

            input, select, button {
                grid-column: 1;
            }

            .container {
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="blood-drop"></div>
            <h1>Update Your Profile</h1>
        </div>

        <form action="donor_update.php" method="POST">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($currentUser['name']); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($currentUser['phone']); ?>" required>

            <label for="location">Location</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($currentUser['location']); ?>" required>

            <label for="age">Age</label>
            <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($currentUser['age']); ?>" min="18" max="65" required>

            <label for="blood_group">Blood Group</label>
            <select id="blood_group" name="blood_group" required>
                <option value="<?php echo htmlspecialchars($currentUser['blood_group']); ?>" selected><?php echo htmlspecialchars($currentUser['blood_group']); ?></option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>

            <button type="submit">Update Profile</button>
        </form>

        <!-- Back to Home Button -->
        <a href="donor_main.php">
            <button class="back-button">Back to Home</button>
        </a>
    </div>
</body>
</html>
