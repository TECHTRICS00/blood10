<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: donor_login.php");
    exit;
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "data00";
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = $_SESSION['user'];

// Handle password change request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validate new passwords match
    if ($newPassword !== $confirmPassword) {
        $error = "New passwords do not match.";
    } else {
        // Verify current password
        $sql = "SELECT password FROM donors WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();

        if (password_verify($currentPassword, $userData['password'])) {
            // Hash new password
            $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password in database
            $updateSql = "UPDATE donors SET password = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("si", $newPasswordHashed, $user['id']);
            
            if ($updateStmt->execute()) {
                $successMessage = "Password updated successfully!";
            } else {
                $error = "Error updating password.";
            }
        } else {
            $error = "Current password is incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
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
            max-width: 500px;
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
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--primary-color);
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid var(--secondary-color);
            border-radius: 5px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input:focus {
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
        }

        button:hover {
            background-color: var(--secondary-color);
        }

        .success, .error {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-footer {
            margin-top: 20px;
            text-align: center;
        }

        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .form-footer a:hover {
            color: var(--secondary-color);
        }

        @media (max-width: 500px) {
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
            <h1>Change Password</h1>
        </div>

        <?php if (isset($error)) { ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>
        <?php if (isset($successMessage)) { ?>
            <div class="success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php } ?>

        <form method="POST" action="">
            <label for="currentPassword">Current Password</label>
            <input type="password" id="currentPassword" name="currentPassword" required>

            <label for="newPassword">New Password</label>
            <input type="password" id="newPassword" name="newPassword" required>

            <label for="confirmPassword">Confirm New Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>

            <button type="submit">Update Password</button>
        </form>

        <div class="form-footer">
            <p><a href="donor_main.php">Back to Profile</a></p>
        </div>
    </div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>