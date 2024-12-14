<section class="change-password">
    <h2>Change Password</h2>
    <?php if (isset($password_success)) echo "<p class='success'>$password_success</p>"; ?>
    <?php if (isset($password_error)) echo "<p class='error'>$password_error</p>"; ?>
    <form method="POST">
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <button type="submit" name="change_password">Change Password</button>
    </form>
    <div class="back-home">
        <a href="main.php">Back to Home</a>
    </div>
</section>

<?php
// Change Password Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if new password matches confirmation
    if ($new_password !== $confirm_password) {
        $password_error = "New password and confirmation do not match.";
    } else {
        // Fetch the current password hash from the database
        $sql = "SELECT password FROM hospitals WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $hospital_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

            // Verify the current password
            if (password_verify($current_password, $hashed_password)) {
                // Hash the new password
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password in the database
                $update_sql = "UPDATE hospitals SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $new_hashed_password, $hospital_id);

                if ($update_stmt->execute()) {
                    $password_success = "Password updated successfully.";
                } else {
                    $password_error = "Failed to update password. Please try again.";
                }
                $update_stmt->close();
            } else {
                $password_error = "Current password is incorrect.";
            }
        } else {
            $password_error = "User not found.";
        }
        $stmt->close();
    }
}
?>
<style>
/* Root Variables for Blood Theme */
:root {
    --blood-primary: #8B0000; /* Dark red */
    --blood-secondary: #C21807; /* Crimson red */
    --blood-hover: #FF6347; /* Blood hover effect */
    --blood-white: #FFFFFF;
    --blood-shadow: rgba(139, 0, 0, 0.2);
}

/* Section Styling */
.change-password {
    background-color: #FFE8E8;
    border-left: 6px solid var(--blood-primary);
    padding: 2.5rem;
    border-radius: 15px;
    margin: 20px auto;
    width: 50%;
    box-shadow: 0 8px 20px var(--blood-shadow);
    animation: fadeIn 1.5s ease, pulseGlow 5s infinite ease-in-out;
}

/* Title Styling */
.change-password h2 {
    color: var(--blood-primary);
    font-size: 28px;
    text-align: center;
    margin-bottom: 20px;
    text-transform: uppercase;
    animation: slideInFromLeft 1.5s ease;
}

/* Input Fields */
.change-password form input {
    width: 90%;
    padding: 15px;
    margin-bottom: 20px;
    border: 2px solid var(--blood-secondary);
    border-radius: 10px;
    font-size: 16px;
    transition: all 0.3s ease;
    box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
}

.change-password form input:focus {
    border-color: var(--blood-primary);
    background: #FFF5F5;
    outline: none;
    transform: scale(1.02);
}

/* Button Styling */
.change-password form button {
    background-color: var(--blood-primary);
    color: var(--blood-white);
    border: none;
    padding: 12px 25px;
    border-radius: 12px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px var(--blood-shadow);
    text-transform: uppercase;
}

.change-password form button:hover {
    background-color: var(--blood-hover);
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 6px 12px var(--blood-shadow);
}

/* Back to Home Link */
.back-home {
    margin-top: 15px;
    text-align: center;
}

.back-home a {
    color: var(--blood-primary);
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    padding: 10px 15px;
    border-radius: 8px;
    background-color: var(--blood-secondary);
    transition: background-color 0.3s ease, transform 0.3s ease;
    display: inline-block;
}

.back-home a:hover {
    background-color: var(--blood-hover);
    color: var(--blood-white);
    transform: scale(1.1);
}

/* Success and Error Messages */
.success, .error {
    font-size: 16px;
    margin: 10px 0;
    padding: 10px;
    border-radius: 8px;
    width: 90%;
    text-align: center;
    display: block;
    animation: fadeIn 1s ease;
}

.success {
    color: #155724;
    background-color: #D4EDDA;
    border: 1px solid #C3E6CB;
}

.error {
    color: #721C24;
    background-color: #F8D7DA;
    border: 1px solid #F5C6CB;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInFromLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulseGlow {
    0%, 100% {
        box-shadow: 0 0 10px var(--blood-primary);
    }
    50% {
        box-shadow: 0 0 20px var(--blood-secondary);
    }
}

</style>