<?php
include 'db.php'; // Ensure your database connection is correctly included

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Handle the create action
    if ($action === 'create') {
        $subject = $_POST['subject'];
        $exam_type = $_POST['exam_type'];
        $question = $_POST['question'];
        $answer = $_POST['answer'];
        $correct_answer = $_POST['correct_answer'];

        // Prepare the SQL query to insert the new question
        $query = "INSERT INTO questions (subject, exam_type, question, answer, correct_answer)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssss', $subject, $exam_type, $question, $answer, $correct_answer);

        // Execute the query and redirect
        if ($stmt->execute()) {
            header('Location: admin.php'); // Redirect to the page after creation
            exit;
        } else {
            echo 'Error: ' . $stmt->error;
        }
    }

    // Handle the update action
    if ($action === 'update') {
        $id = $_POST['id'];
        $subject = $_POST['subject'];
        $exam_type = $_POST['exam_type'];
        $question = $_POST['question'];
        $answer = $_POST['answer'];
        $correct_answer = $_POST['correct_answer'];

        // Prepare the SQL query to update the question
        $query = "UPDATE questions SET subject = ?, exam_type = ?, question = ?, answer = ?, correct_answer = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssssi', $subject, $exam_type, $question, $answer, $correct_answer, $id);

        // Execute the query and redirect
        if ($stmt->execute()) {
            header('Location: admin.php'); // Redirect after update
            exit;
        } else {
            echo 'Error: ' . $stmt->error;
        }
    }

    // Handle the delete action
    if ($action === 'delete') {
        $id = $_POST['id'];

        // Prepare the SQL query to delete the question
        $query = "DELETE FROM questions WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);

        // Execute the delete query and redirect back to the same page (admin.php)
        if ($stmt->execute()) {
            header('Location: admin.php'); // Redirect after delete
            exit;
        } else {
            echo 'Error: ' . $stmt->error;
        }
    }
}

// Function to fetch all questions
function getQuestions()
{
    global $conn;
    $result = $conn->query("SELECT * FROM questions ORDER BY id DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to fetch a specific question by ID for editing
function getQuestionById($id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM questions WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>
