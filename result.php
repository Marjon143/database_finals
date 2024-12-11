<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "finals";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch results and dynamically calculate scores and statuses
$sql = "
    SELECT 
        r.id,
        r.name,
        r.subject,
        r.score,
        COUNT(q.id) AS total_questions,
        r.status
    FROM result r
    JOIN questions q ON r.subject = q.subject
    GROUP BY r.id, r.name, r.subject, r.score, r.status
    ORDER BY r.id ASC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .message {
            text-align: center;
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Exam Results</h1>
        <?php if (isset($_GET['message'])): ?>
            <p class="message"><?= htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Subject</th>
                    <th>Score</th>
                    <th>Total Questions</th>
                    <th>Percentage</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Calculate percentage score and determine status
                        $percentage = ($row['score'] / $row['total_questions']) * 100;
                        $status = ($percentage >= 75) ? 'Pass' : 'Fail';

                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['subject']}</td>
                                <td>{$row['score']}</td>
                                <td>{$row['total_questions']}</td>
                                <td>" . number_format($percentage, 2) . "%</td>
                                <td>{$status}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No results found.</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
