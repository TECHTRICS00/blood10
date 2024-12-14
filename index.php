<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation Network</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-red: #D32F2F;
            --secondary-red: #FF5252;
            --background-light: #F5F5F5;
            --text-dark: #333333;
            --white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
            line-height: 1.6;
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="40" height="48" viewport="0 0 100 100" style="fill:red;opacity:0.7;"><path d="M19.4 27.5c-1.5-1.5-2.4-3.5-2.4-5.6 0-4.4 3.6-8 8-8s8 3.6 8 8c0 2.1-0.9 4.1-2.4 5.6L20 40l-0.6-0.6z"/></svg>') 16 0, auto;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Header Styles */
        header {
            background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
            color: var(--white);
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s infinite alternate;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            100% { transform: scale(1.05); }
        }

        header h1 {
            font-size: 2.5em;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }

        /* Navigation Styles */
        .main-nav {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .nav-section {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            width: 250px;
        }

        .nav-section:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .nav-header {
            background-color: var(--primary-red);
            color: var(--white);
            padding: 15px;
            text-align: center;
            font-size: 1.2em;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-header i {
            margin-right: 10px;
        }

        .nav-content {
            padding: 20px;
            text-align: center;
        }

        .nav-button {
            display: block;
            background-color: var(--secondary-red);
            color: var(--white);
            text-decoration: none;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .nav-button:hover {
            background-color: var(--primary-red);
            transform: scale(1.05);
        }

        /* Info Section */
        .info-section {
            background-color: var(--white);
            padding: 40px 0;
            text-align: center;
            box-shadow: 0 -4px 6px rgba(0,0,0,0.05);
        }

        .info-section h2 {
            color: var(--primary-red);
            margin-bottom: 20px;
        }

        /* Footer */
        footer {
            background-color: var(--primary-red);
            color: var(--white);
            padding: 30px 0;
            text-align: center;
        }

        .social-icons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .social-icons a {
            color: var(--white);
            font-size: 1.5em;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: rgba(255,255,255,0.7);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-nav {
                flex-direction: column;
                align-items: center;
            }

            .nav-section {
                width: 100%;
                max-width: 350px;
            }
        }

        /* Blood Drop Animation for Background */
        @keyframes float-blood-drop {
            0% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0); }
        }

        body::before {
            content: '';
            position: fixed;
            top: -50px;
            right: 50px;
            width: 50px;
            height: 70px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="rgba(211,47,47,0.2)"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>') no-repeat;
            background-size: contain;
            opacity: 0.3;
            animation: float-blood-drop 3s infinite ease-in-out;
            z-index: -1;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Blood Donation Network</h1>
            <p>Saving Lives, One Drop at a Time</p>
        </div>
    </header>

    <div class="container main-nav">
        <div class="nav-section">
            <div class="nav-header">
                <i class="fas fa-hospital"></i> Hospital
            </div>
            <div class="nav-content">
                <a href="login.php" class="nav-button">Login</a>
                <a href="Registration.php" class="nav-button">Register</a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-header">
                <i class="fas fa-user-friends"></i> Donor
            </div>
            <div class="nav-content">
                <a href="donor_login.php" class="nav-button">Login</a>
                <a href="donor_registration.php" class="nav-button">Register</a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-header">
                <i class="fas fa-address-book"></i> Directory
            </div>
            <div class="nav-content">
                <a href="donors_list.php" class="nav-button">Donors List</a>
                <a href="hospital_list.php" class="nav-button">Hospital List</a>
            </div>
        </div>
    </div>

    <section class="info-section container">
        <h2>Why Donate Blood?</h2>
        <p>
            Every two seconds, someone needs blood. Your single donation can save up to three lives. 
            Be a hero - donate blood and make a difference in someone's life today.
        </p>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 Blood Donation Network. All Rights Reserved.</p>
            <div class="social-icons">
                <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook"></i></a>
                <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
            <p>Follow us for updates on donation events and health information.</p>
        </div>
    </footer>
</body>
</html>