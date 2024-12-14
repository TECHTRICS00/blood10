<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: donor_login.php");
    exit;
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Profile</title>
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
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-top: 30px;
        }

        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            position: relative;
        }

        .logout-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: white;
            color: var(--primary-color);
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: var(--background-color);
        }

        nav {
            background-color: var(--secondary-color);
            padding: 10px 0;
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        nav ul li a:hover {
            color: var(--background-color);
        }

        .profile-section {
            padding: 20px;
        }

        .profile-section h2 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .profile-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .profile-info p {
            background-color: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
        }

        .profile-info strong {
            color: var(--primary-color);
            display: block;
            margin-bottom: 5px;
        }

        @media (max-width: 600px) {
            .profile-info {
                grid-template-columns: 1fr;
            }

            nav ul {
                flex-direction: column;
                align-items: center;
            }

            nav ul li {
                margin: 10px 0;
            }
        }

        /* Blood drop icon for additional theming */
        .blood-drop {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M12 2c-5.33 4.55-8 8.48-8 11.8 0 4.98 3.8 8.2 8 8.2s8-3.22 8-8.2c0-3.32-2.67-7.25-8-11.8z"/></svg>') no-repeat center;
            background-size: contain;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="blood-drop"></div>
            <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?></h1>
            <a href="donor_login.php" class="logout-btn">Logout</a>
        </div>

        <nav>
            <ul>
                <li><a href="donor_main.php">Home</a></li>
                <li><a href="donor_changepass.php">Change Password</a></li>
                <li><a href="donor_noti.php">Notifications</a></li>
                <li><a href="donor_form.php">Form</a></li>
                <li><a href="donor_Appointment.php">Appointment</a></li>
                <li><a href="donor_update.php">Update Profile</a></li>
            </ul>
        </nav>

        <div class="profile-section">
            <h2>Your Profile</h2>
            <div class="profile-info">
                <p><strong>Name</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Email</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Phone</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                <p><strong>Location</strong> <?php echo htmlspecialchars($user['location']); ?></p>
                <p><strong>Age</strong> <?php echo htmlspecialchars($user['age']); ?></p>
                <p><strong>Blood Group</strong> <?php echo htmlspecialchars($user['blood_group']); ?></p>
            </div>
        </div>
    </div>
</body>
</html>