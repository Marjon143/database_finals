<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "finals";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle exam submission and result calculation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answers'])) {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        echo "Please log in to take the exam.";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $answers = $_POST['answers'];

    $score = 0;
    $totalQuestions = 0;
    $user_name = "";

    // Query to get the user's name
    $sql_user = "SELECT name FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    if ($result_user->num_rows > 0) {
        $row_user = $result_user->fetch_assoc();
        $user_name = $row_user['name'];
    }
    $stmt_user->close();

    // Iterate through the answers and calculate the score
    foreach ($answers as $questionId => $answer) {
        $exam_id = $_POST['exam_ids'][$questionId];
        $sql = "SELECT correct_answer, subject FROM questions WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $correct_answer = $row['correct_answer'];
            $subject = $row['subject'];

            // Save the answer in the response table
            $stmt_response = $conn->prepare("INSERT INTO response (exam_id, subject, answer, name) VALUES (?, ?, ?, ?)");
            $stmt_response->bind_param("isss", $exam_id, $subject, $answer, $user_name);
            $stmt_response->execute();
            $stmt_response->close();

            // Check if the answer is correct
            if ($answer === $correct_answer) {
                $score++;
            }
            $totalQuestions++;
        }
        $stmt->close();
    }

    // Determine the status (Pass or Fail)
    $status = ($score >= ($totalQuestions / 2)) ? 'Pass' : 'Fail';

    // Insert result into the database
    $stmt_result = $conn->prepare("INSERT INTO result (name, subject, score, status) VALUES (?, ?, ?, ?)");
    $stmt_result->bind_param("ssis", $user_name, $subject, $score, $status);
    $stmt_result->execute();
    $stmt_result->close();

    // Redirect to results page
    header("Location: result.php?message=Exam submitted successfully! Your score: $score/$totalQuestions. Status: $status");
    exit;
}

// Fetch subjects for dropdown
if (isset($_GET['action']) && $_GET['action'] == 'getSubjects') {
    $sql = "SELECT DISTINCT subject FROM questions";
    $result = $conn->query($sql);
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row['subject'];
    }
    echo json_encode($subjects);
    exit;
}

// Fetch questions based on subject
if (isset($_GET['subject'])) {
    $subject = $_GET['subject'];
    $sql = "SELECT id, question, answer, exam_id FROM questions WHERE subject = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $subject);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
    echo json_encode($questions);
    $stmt->close();
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/examinee.css">
    <title>Take Exam</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .exam-container { max-width: 600px; margin: auto; }
        .question { margin-bottom: 20px; }
        .question label { font-weight: bold; }
        .submit-btn { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="exam-container">
        <h1>Take Your Exam</h1>
        
        <!-- Subject Dropdown -->
        <div>
            <label for="subject">Choose a Subject:</label>
            <select id="subject" name="subject">
                <option value="">Select Subject</option>
            </select>
        </div>
        
        <!-- Questions will be displayed here -->
        <form id="exam-form" method="POST">
            <div id="questions"></div>
            
            <!-- Submit Button -->
            <button type="submit" id="submit-btn" class="submit-btn" style="display:none;">Submit Exam</button>
            <a href="index.php" class="button-div">Home</a>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Fetch and populate the subject dropdown
            $.ajax({
                url: 'examinee.php',
                type: 'GET',
                data: { action: 'getSubjects' },
                success: function(data) {
                    var subjects = JSON.parse(data);
                    var subjectSelect = $('#subject');
                    subjects.forEach(function(subject) {
                        subjectSelect.append('<option value="' + subject + '">' + subject + '</option>');
                    });
                }
            });

            // When a subject is selected, fetch and display the questions
            $('#subject').change(function() {
                var subject = $(this).val();
                if (subject) {
                    $.ajax({
                        url: 'examinee.php',
                        type: 'GET',
                        data: { subject: subject },
                        success: function(data) {
                            var questions = JSON.parse(data);
                            var questionsHtml = '';
                            questions.forEach(function(question) {
                                var answerOptions = question.answer.split(',');
                                questionsHtml += `  
                                    <div class="question">
                                        <label>${question.question}</label><br>
                                        ${answerOptions.map(function(option, i) {
                                            return ` 
                                                <input type="radio" name="answers[${question.id}]" value="${option}" required> 
                                                <label>${option}</label><br>
                                            `;
                                        }).join('')}
                                        <input type="hidden" name="exam_ids[${question.id}]" value="${question.exam_id}">
                                    </div>
                                `;
                            });
                            $('#questions').html(questionsHtml);
                            $('#submit-btn').show();
                        }
                    });
                } else {
                    $('#questions').empty();
                    $('#submit-btn').hide();
                }
            });
        });
    </script>
</body>
</html>
