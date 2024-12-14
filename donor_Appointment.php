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

// Handle appointment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hospital_id'])) {
    $hospitalId = $_POST['hospital_id'];
    $hospitalName = $_POST['hospital_name'];
    $userName = $_POST['user_name'];
    $appointmentDate = $_POST['appointment_date'];
    $message = $_POST['message'];

    // Insert appointment into the appointments table
    $sql = "INSERT INTO appointments (hospital_name, user_name, appointment_date, message, status, hospital_id) 
            VALUES (?, ?, ?, ?, 'Pending', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $hospitalName, $userName, $appointmentDate, $message, $hospitalId);

    if ($stmt->execute()) {
        echo "Appointment sent successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch hospital data
$sql = "SELECT id, name FROM hospitals";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Appointments</title>
</head>
<body>
<h1>Send Appointments to Hospitals</h1>

<?php if ($result->num_rows > 0): ?>
    <table border="1">
        <thead>
        <tr>
            <th>Hospital ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="hospital_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="hospital_name" value="<?php echo htmlspecialchars($row['name']); ?>">
                        <label for="user_name">Donor Name:</label>
                        <input type="text" name="user_name" required>
                        <label for="appointment_date">Date:</label>
                        <input type="date" name="appointment_date" required>
                        <label for="message">Message:</label>
                        <input type="text" name="message">
                        <button type="submit">Send Appointment</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No hospitals available.</p>
<?php endif; ?>
</body>
</html>

<style>
/* General Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
    color: #333;
    transition: background-color 0.5s ease;
}

h1 {
    text-align: center;
    color: #b30000;
    margin-top: 20px;
    animation: fadeIn 1.5s ease;
}

/* Table Styling */
table {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
}

table:hover {
    transform: scale(1.02);
}

table th, table td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
    transition: background-color 0.3s ease, color 0.3s ease;
}

th {
    background-color: #b30000;
    color: white;
    font-weight: bold;
}

table tr:nth-child(even) {
    background-color: #f2f2f2;
}

table tr:hover {
    background-color: #ffe6e6;
}

/* Form Styling */
form {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    animation: fadeInUp 1s ease;
}

form label {
    font-weight: bold;
    color: #b30000;
}

form input[type="text"], form input[type="date"] {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 100%;
    max-width: 200px;
    transition: box-shadow 0.3s ease;
}

form input[type="text"]:focus, form input[type="date"]:focus {
    box-shadow: 0 0 5px #b30000;
}

form button {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    background-color: #4caf50;
    color: white;
    cursor: pointer;
    font-size: 14px;
    margin-top: 10px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

form button:hover {
    background-color: #45a049;
    transform: scale(1.05);
}

/* Messages */
p {
    text-align: center;
    font-size: 18px;
    margin: 15px;
    animation: fadeIn 1s ease;
}

p.success {
    color: #4caf50;
    font-weight: bold;
}

p.error {
    color: #d32f2f;
    font-weight: bold;
}

/* Page Footer */
.footer {
    text-align: center;
    padding: 10px;
    background-color: #b30000;
    color: #fff;
    position: fixed;
    bottom: 0;
    width: 100%;
    animation: slideUp 1s ease;
}

/* Keyframe Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUp {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}


</style>
