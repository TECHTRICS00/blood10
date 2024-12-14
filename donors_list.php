<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "data00";
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM donors"; // Assuming you have a 'donors' table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Start the table
    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<caption>Donors List</caption>"; // Added caption for the table
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Blood Group</th></tr>";

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['blood_group'] . "</td>";
        echo "</tr>";
    }

    // End the table
    echo "</table>";
} else {
    echo "No donors found.";
}

$conn->close();
?>

<!-- Back to Home Button -->
<div class="back-button-container">
    <a href="index.php" class="back-button">Back to Home</a>
</div>

<style>
    /* General Table Styling */
    table {
        width: 80%;
        margin: 30px auto;
        border-collapse: collapse;
        background-color: #f9f9f9;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
        animation: fadeIn 1s ease-out;
    }

    /* Table Caption Styling */
    caption {
        font-size: 1.5em;
        font-weight: bold;
        margin-bottom: 20px;
        color: #b30000; /* Dark red for blood theme */
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Table Header Styling */
    th {
        background-color: #b30000; /* Blood red */
        color: white;
        font-size: 1.1em;
        padding: 12px 15px;
        text-align: left;
        letter-spacing: 1px;
        border-bottom: 2px solid #a30000;
        transition: background-color 0.3s ease;
    }

    /* Table Data Styling */
    td {
        padding: 12px 15px;
        text-align: left;
        font-size: 1em;
        color: #333;
        border-bottom: 1px solid #ddd;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    /* Hover Effects */
    tr:hover {
        background-color: #ffcccc; /* Light red hover effect */
    }

    th:hover {
        background-color: #d20000; /* Darker red on hover */
    }

    td:hover {
        background-color: #fce6e6; /* Very light red when hovered */
        transform: scale(1.05);
    }

    /* Animation for Table Appearance */
    @keyframes fadeIn {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }

    /* Alternate Row Coloring */
    tr:nth-child(even) {
        background-color: #fff7f7; /* Lightest red background */
    }

    tr:nth-child(odd) {
        background-color: #ffe6e6; /* Soft pink background */
    }

    /* Responsive Design for Table */
    @media (max-width: 768px) {
        table {
            width: 95%;
        }

        th, td {
            font-size: 0.9em;
            padding: 10px;
        }
    }

    /* Fade-in animation for the entire page */
    body {
        animation: fadeIn 2s ease-out;
    }

    /* Back Button Styling */
    .back-button-container {
        text-align: center; /* Center align the button container */
        margin-top: 20px; /* Space above the button */
    }

    .back-button {
        display: inline-block; /* Make it an inline block for padding */
        padding: 10px 20px; /* Padding for button size */
        background-color: #D32F2F; /* Blood red color for the button */
        color: white; /* White text color */
        text-decoration: none; /* Remove underline from link */
        border-radius: 5px; /* Rounded corners for button */
        transition: background-color 0.3s ease, transform 0.3s ease; /* Transition effects */
      }
    
      .back-button:hover {
          background-color: #b30000; /* Darker red on hover */
          transform: scale(1.05); /* Scale effect on hover */
      }
</style>
