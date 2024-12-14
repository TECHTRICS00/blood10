<?php
session_start();

// Database Configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "data00";

// Create a database connection
$conn = new mysqli($host, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$hospital_id = $_SESSION['hospital_id'];

// Handle updates to hospital details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_hospital'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $location = $_POST['location'];

    $sql = "UPDATE hospitals SET name = ?, email = ?, location = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $location, $hospital_id);

    if ($stmt->execute()) {
        $update_success = "Hospital details updated successfully!";
    } else {
        $update_error = "Failed to update hospital details. Please try again. Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch hospital details
$sql = "SELECT * FROM hospitals WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hospital_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $hospital = $result->fetch_assoc();
} else {
    die("No hospital found with this ID.");
}
$stmt->close();

// Fetch hardware data for the table
// Fetch hardware data for the table
if (isset($_GET['fetchTableData'])) {
    // SQL query to fetch the last 10 records for the current hospital_id
    $sql = "SELECT id, temperature, load_cell_1, load_cell_2, load_cell_3, load_cell_4 
            FROM sensordata 
            WHERE hospital_id = ? 
            ORDER BY id DESC 
            LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        // Convert temperature to Celsius and load cell values to kilograms
        $row['temperature'] = round($row['temperature'], 2) . ' °C';
        $row['load_cell_1'] = round($row['load_cell_1'], 2) . ' g';
        $row['load_cell_2'] = round($row['load_cell_2'], 2) . ' g';
        $row['load_cell_3'] = round($row['load_cell_3'], 2) . ' g';
        $row['load_cell_4'] = round($row['load_cell_4'], 2) . ' g';

        $data[] = $row;
    }

    echo json_encode($data);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- Navigation Bar -->
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="hospital_changepass.php">Change Password</a></li>
                <li><a href="hospital_request.php">Request</a></li>
                <li><a href="hospital_appoinment.php">Appointments</a></li>
                <li><button class="logout-btn" onclick="window.location.href='login.php'">Logout</button></li>
            </ul>
        </nav>

        <!-- Hospital Info Section -->
        <section class="user-info">
            <h1>Welcome, <?php echo htmlspecialchars($hospital['name']); ?>!</h1>
            <p>Email: <?php echo htmlspecialchars($hospital['email']); ?></p>
            <p>Location: <?php echo htmlspecialchars($hospital['location']); ?></p>
        </section>

        <!-- Update Details Section -->
        <section class="update-details">
            <h2>Update Your Details</h2>
            <?php if (isset($update_success)) echo "<p class='success'>$update_success</p>"; ?>
            <?php if (isset($update_error)) echo "<p class='error'>$update_error</p>"; ?>
            <form method="POST">
                <input type="text" name="name" placeholder="Hospital Name" value="<?php echo htmlspecialchars($hospital['name']); ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($hospital['email']); ?>" required>
                <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($hospital['location']); ?>" required>
                <button type="submit" name="update_hospital">Update</button>
            </form>
        </section>

        <!-- Hardware Data Section -->
        <section class="hardware-table">
            <h2>Hardware Data</h2>
            <table id="hardwareTable" border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Temperature (°C)</th>
                        <th>Load Cell 1 (kg)</th>
                        <th>Load Cell 2 (kg)</th>
                        <th>Load Cell 3 (kg)</th>
                        <th>Load Cell 4 (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated dynamically -->
                </tbody>
            </table>
        </section>
    </div>

    <script>
        async function fetchTableData() {
            try {
                const response = await fetch('?fetchTableData=1');
                const data = await response.json();

                const tableBody = document.querySelector('#hardwareTable tbody');
                tableBody.innerHTML = ''; // Clear existing table data

                data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.id}</td>
                        <td>${row.temperature}</td>
                        <td>${row.load_cell_1}</td>
                        <td>${row.load_cell_2}</td>
                        <td>${row.load_cell_3}</td>
                        <td>${row.load_cell_4}</td>
                    `;
                    tableBody.appendChild(tr);
                });
            } catch (error) {
                console.error('Error fetching table data:', error);
            }
        }

        // Fetch table data every 5 seconds
        setInterval(fetchTableData, 5000);
        fetchTableData(); // Initial fetch
    </script>
</body>
</html>

<style>
:root {
    --blood-primary: #8B0000; /* Deep red for blood-related theme */
    --blood-secondary: #DC143C; /* Crimson accent */
    --blood-background: #F8F0F0; /* Light pale pink background */
    --blood-text-dark: #2C3E50; /* Dark text for contrast */
    --blood-white: #FFFFFF;
    --blood-shadow: rgba(139, 0, 0, 0.2);
    --blood-hover: #C0392B;
}

/* General Styles */
body {
    font-family: 'Roboto', 'Arial', sans-serif;
    background-color: var(--blood-background);
    color: var(--blood-text-dark);
    margin: 0;
    padding: 0;
    line-height: 1.6;
    cursor: url('https://upload.wikimedia.org/wikipedia/commons/e/e4/Blood_drop_icon.svg'), auto; /* Blood droplet cursor */
}

/* Container Box */
.container {
    background-color: var(--blood-white);
    border-left: 8px solid var(--blood-primary);
    box-shadow: 0 6px 20px var(--blood-shadow);
    padding: 2.5rem;
    border-radius: 15px;
    max-width: 1200px;
    margin: 50px auto;
    text-align: center;
}

/* Navigation Bar */
.navbar {
    background-color: var(--blood-primary);
    padding: 1.2rem;
    border-radius: 12px;
    margin-bottom: 30px;
}

.navbar ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar li {
    display: inline-block;
    margin-right: 20px;
}

.navbar li:last-child {
    margin-right: 0;
}

.navbar a {
    text-decoration: none;
    color: var(--blood-white);
    font-weight: bold;
    transition: color 0.3s ease;
    font-size: 18px;
}

.navbar a:hover {
    color: var(--blood-hover);
}

button.logout-btn {
    background-color: var(--blood-hover);
    color: var(--blood-white);
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button.logout-btn:hover {
    background-color: var(--blood-secondary);
}

/* User Info Section */
.user-info {
    background-color: #FFF4F4;
    border-left: 5px solid var(--blood-secondary);
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 20px;
    text-align: left;
}

.user-info h1 {
    color: var(--blood-primary);
    font-size: 28px;
    margin-bottom: 10px;
}

.user-info p {
    font-size: 18px;
    margin: 5px 0;
}

/* Update Details Section */
.update-details h2 {
    color: var(--blood-secondary);
    font-size: 24px;
    margin-bottom: 20px;
}

.update-details form input {
    width: calc(100% - 20px);
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid var(--blood-primary);
    border-radius: 8px;
    font-size: 16px;
}

.update-details form button {
    background-color: var(--blood-primary);
    color: var(--blood-white);
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    transition: background-color 0.3s ease;
    cursor: pointer;
}

.update-details form button:hover {
    background-color: var(--blood-secondary);
}

.success {
    color: green;
    font-size: 16px;
}

.error {
    color: red;
    font-size: 16px;
}

/* Hardware Table */
.hardware-table h2 {
    font-size: 24px;
    color: var(--blood-secondary);
    margin-bottom: 15px;
}

.hardware-table table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    box-shadow: 0 4px 15px var(--blood-shadow);
}

.hardware-table th,
.hardware-table td {
    border: 1px solid var(--blood-primary);
    padding: 12px;
    text-align: center;
    font-size: 16px;
}

.hardware-table th {
    background-color: var(--blood-primary);
    color: var(--blood-white);
    text-transform: uppercase;
}

.hardware-table td {
    background-color: var(--blood-background);
    color: var(--blood-text-dark);
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    .container {
        padding: 1.5rem;
        margin: 20px;
    }

    .navbar ul {
        flex-direction: column;
        align-items: center;
    }

    .navbar li {
        margin-bottom: 10px;
    }

    .navbar li:last-child {
        margin-bottom: 0;
    }
}
