<?php
// Database connection
$servername = "localhost"; // your server
$username = "root"; // your username
$password = ""; // your password
$dbname = "finals"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to count the total number of users
$sqlTotalUsers = "SELECT COUNT(*) AS total_users FROM users";
$resultTotalUsers = $conn->query($sqlTotalUsers);

if ($resultTotalUsers->num_rows > 0) {
    $row = $resultTotalUsers->fetch_assoc();
    $totalUsers = $row['total_users'];
} else {
    $totalUsers = 0;
}

// Query to count the number of passed users
$sqlPassed = "SELECT COUNT(*) AS passed_users FROM result WHERE status = 'pass'";
$resultPassed = $conn->query($sqlPassed);

if ($resultPassed->num_rows > 0) {
    $row = $resultPassed->fetch_assoc();
    $passedUsers = $row['passed_users'];
} else {
    $passedUsers = 0;
}

// Query to count the number of failed users
$sqlFailed = "SELECT COUNT(*) AS failed_users FROM result WHERE status = 'fail'";
$resultFailed = $conn->query($sqlFailed);

if ($resultFailed->num_rows > 0) {
    $row = $resultFailed->fetch_assoc();
    $failedUsers = $row['failed_users'];
} else {
    $failedUsers = 0;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=grading" />
    <title>Admin Dashboard</title>
</head>

<body>
    <nav>
        <div class="logo">
            <div class="logo-image">
                <img src="assets/logo.png" alt="">
            </div>
        </div>
        <div class="menu-items">
            <ul class="navLinks">
                <li class="navList">
                    <a href="#">
                        <ion-icon name="analytics-outline"></ion-icon>
                        <span class="links">Analytics</span>
                    </a>
                </li>
                <li class="navList">
                    <a href="#">
                        <ion-icon name="chatbubbles-outline"></ion-icon>
                        <span class="links">Comments</span>
                    </a>
                </li>
                <li class="navList">
                    <a href="admin.php">
                        <ion-icon name="chatbubbles-outline"></ion-icon>
                        <span class="links">Create Exam</span>
                    </a>
                </li>
            </ul>
            <ul class="bottom-link">
                <li>
                    <a href="#">
                        <ion-icon name="log-out-outline"></ion-icon>
                        <span class="links">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <section class="dashboard">
        <div class="container">
            <div class="overview">
                <div class="title">
                    <ion-icon name="speedometer"></ion-icon>
                    <span class="text">Dashboard</span>
                </div>
                <div class="boxes">
                    <div class="box box1">
                        <ion-icon name="eye-outline"></ion-icon>
                        <span class="text">Total Users</span>
                        <span class="number"><?php echo $totalUsers; ?></span>
                    </div>
                    <div class="box box2">
                        <ion-icon name="people-outline"></ion-icon>
                        <span class="text">Passed</span>
                        <span class="number"><?php echo $passedUsers; ?></span>
                    </div>
                    <div class="box box3">
                        <ion-icon name="chatbubbles-outline"></ion-icon>
                        <span class="text">Fail</span>
                        <span class="number"><?php echo $failedUsers; ?></span>
                    </div>
                    <div class="box box4">
                        <span class="material-symbols-outlined">grading</span>
                        <span class="text">Number of Subject</span>
                        <span class="number"></span>
                    </div>
                </div>
            </div>

            <!-- Additional content can go here -->
        </div>
    </section>

    <script src="assets/dashboard.js"></script>

    <!-- Sources for icons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>
