<?php
// Start output buffering and session at the very top of the file
ob_start();
session_start(); 

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Redirect to login page if not logged in
    header("Location: donor_login.php");
    exit;
}

$user = $_SESSION['user'];  // Retrieve logged-in user's identifier
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation Form</title>
    <style>
    /* General Styling */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f7fa;
    color: #333;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Header Styling */
h1 {
    text-align: center;
    color: #b71c1c;
    margin: 40px 0;
    font-size: 36px;
}

/* Form Container Styling */
form {
    background-color: #fff;
    max-width: 700px;
    margin: 20px auto;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1);
    border: 1px solid #e0e0e0;
    transition: transform 0.3s ease-in-out;
}

/* Form Input Styling */
label {
    display: block;
    font-weight: bold;
    color: #b71c1c;
    margin-bottom: 8px;
    font-size: 16px;
}

input[type="text"],
input[type="date"],
textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
}

input[type="text"]:focus,
input[type="date"]:focus,
textarea:focus {
    border-color: #b71c1c;
    outline: none;
    box-shadow: 0 0 8px rgba(183, 28, 28, 0.3);
}

textarea {
    resize: vertical;
    min-height: 120px;
}

input[type="text"],
input[type="date"] {
    background-color: #f9f9f9;
}

/* Submit Button Styling */
button {
    background-color: #b71c1c;
    color: white;
    padding: 14px 22px;
    font-size: 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #d32f2f;
}

/* Small Screens */
@media (max-width: 600px) {
    h1 {
        font-size: 28px;
    }
    
    form {
        padding: 20px;
    }

    input[type="text"],
    input[type="date"],
    textarea {
        font-size: 14px;
        padding: 10px;
    }

    button {
        font-size: 16px;
        padding: 12px 18px;
    }
}

/* Visual Feedback */
form:focus-within {
    transform: scale(1.02);
}

    </style>
</head>
<body>
    <h1>Blood Donation Form</h1>
    <form action="donor_form.php" method="POST">
        <!-- Form Fields -->
        <label for="donated_blood">Have you donated blood before?</label>
        <input type="text" id="donated_blood" name="donated_blood"><br><br>

        <label for="eaten_last_4_hours">Have you eaten in the last 4 hours?</label>
        <input type="text" id="eaten_last_4_hours" name="eaten_last_4_hours" required><br><br>

        <label for="given_blood_last_16_weeks">Have you given blood in the last 16 weeks?</label>
        <input type="text" id="given_blood_last_16_weeks" name="given_blood_last_16_weeks" required><br><br>

        <label for="pregnant_breastfeeding">Are you pregnant or breastfeeding?</label>
        <input type="text" id="pregnant_breastfeeding" name="pregnant_breastfeeding" required><br><br>

        <label for="cough_cold">Do you have a cough or cold?</label>
        <input type="text" id="cough_cold" name="cough_cold" required><br><br>

        <label for="medications">Are you taking any medications?</label>
        <input type="text" id="medications" name="medications" required><br><br>

        <label for="vaccine">Have you received any vaccine recently?</label>
        <input type="text" id="vaccine" name="vaccine" required><br><br>

        <label for="allergy">Do you have any allergies?</label>
        <input type="text" id="allergy" name="allergy" required><br><br>

        <label for="dental">Have you undergone any dental treatment recently?</label>
        <input type="text" id="dental" name="dental" required><br><br>

        <label for="rabies">Have you received a rabies shot?</label>
        <input type="text" id="rabies" name="rabies" required><br><br>

        <label for="surgery">Have you had any surgeries recently?</label>
        <input type="text" id="surgery" name="surgery" required><br><br>

        <label for="last_vaccinated_date">Date of last vaccination:</label>
        <input type="date" id="last_vaccinated_date" name="last_vaccinated_date" required><br><br>

        <label for="next_appointment_date">Next appointment date:</label>
        <input type="date" id="next_appointment_date" name="next_appointment_date" required><br><br>

        <label for="queries">Additional queries or comments:</label><br>
        <textarea id="queries" name="queries" rows="4" cols="50"></textarea><br><br>

        <button type="submit">Submit</button>
    </form>

    <?php
    // Debugging: print the session data
   

    // Check if the user is logged in
    if (!isset($_SESSION['user'])) {
        echo "User session is not set!";
        header("Location: donor_login.php");
        exit;
    }

    $user = $_SESSION['user'];  // Proceed with the session data

    // Database connection
    $host = "localhost";
$username = "root";
$password = "";
$database = "data00";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check POST data
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $donated_blood = $_POST['donated_blood'] ?? '';
        $eaten_last_4_hours = $_POST['eaten_last_4_hours'] ?? '';
        $given_blood_last_16_weeks = $_POST['given_blood_last_16_weeks'] ?? '';
        $pregnant_breastfeeding = $_POST['pregnant_breastfeeding'] ?? '';
        $cough_cold = $_POST['cough_cold'] ?? '';
        $medications = $_POST['medications'] ?? '';
        $vaccine = $_POST['vaccine'] ?? '';
        $allergy = $_POST['allergy'] ?? '';
        $dental = $_POST['dental'] ?? '';
        $rabies = $_POST['rabies'] ?? '';
        $surgery = $_POST['surgery'] ?? '';
        $last_vaccinated_date = $_POST['last_vaccinated_date'] ?? '';
        $next_appointment_date = $_POST['next_appointment_date'] ?? '';
        $queries = $_POST['queries'] ?? '';

        // Validate required fields
        if (empty($eaten_last_4_hours) || empty($given_blood_last_16_weeks)) {
            echo "Please fill in the required fields!";
            exit;
        }

        // Insert into the database using prepared statements
        $sql = "INSERT INTO user_data (
                    user_id,
                    donated_blood, 
                    eaten_last_4_hours, 
                    given_blood_last_16_weeks, 
                    pregnant_breastfeeding, 
                    cough_cold, 
                    medications, 
                    vaccine, 
                    allergy, 
                    dental, 
                    rabies, 
                    surgery, 
                    last_vaccinated_date, 
                    next_appointment_date, 
                    queries
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Error preparing the statement: ' . $conn->error);
        }

        $stmt->bind_param(
            "issssssssssssss",
            $user['id'], // Using user ID from session
            $donated_blood,
            $eaten_last_4_hours,
            $given_blood_last_16_weeks,
            $pregnant_breastfeeding,
            $cough_cold,
            $medications,
            $vaccine,
            $allergy,
            $dental,
            $rabies,
            $surgery,
            $last_vaccinated_date,
            $next_appointment_date,
            $queries
        );

        // Execute the query
        if ($stmt->execute()) {
            echo "Donor information submitted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
    ?>

</body>
</html>
