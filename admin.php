<?php
include 'crud.php'; // Include the crud.php file to handle form submissions and deletions

// Fetch the question to edit if the 'edit' parameter is set
if (isset($_GET['edit'])) {
    $question_to_edit = getQuestionById($_GET['edit']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Question Management</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="container">
        <h1><?php echo isset($question_to_edit) ? 'Edit Exam Question' : 'Create Exam'; ?></h1>

        <!-- Form for Adding or Editing a Question -->
        <form id="exam-form" method="POST" action="crud.php">
            <input type="hidden" name="action" value="<?php echo isset($question_to_edit) ? 'update' : 'create'; ?>">
            <input type="hidden" name="id" value="<?php echo isset($question_to_edit) ? $question_to_edit['id'] : ''; ?>">

            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" value="<?php echo isset($question_to_edit) ? $question_to_edit['subject'] : ''; ?>" required>

            <label for="exam-type">Exam Type:</label>
            <select id="exam-type" name="exam_type" required>
                <option value="multiple-choice" <?php echo isset($question_to_edit) && $question_to_edit['exam_type'] == 'multiple-choice' ? 'selected' : ''; ?>>Multiple Choice</option>
                <option value="form-type" <?php echo isset($question_to_edit) && $question_to_edit['exam_type'] == 'form-type' ? 'selected' : ''; ?>>Form Type</option>
            </select>

            <label for="question">Question:</label>
            <textarea id="question" name="question" rows="4" required><?php echo isset($question_to_edit) ? $question_to_edit['question'] : ''; ?></textarea>

            <label for="answer">Answer Options (comma-separated):</label>
            <input type="text" id="answer" name="answer" value="<?php echo isset($question_to_edit) ? $question_to_edit['answer'] : ''; ?>" required>

            <label for="correct-answer">Correct Answer:</label>
            <input type="text" id="correct-answer" name="correct_answer" value="<?php echo isset($question_to_edit) ? $question_to_edit['correct_answer'] : ''; ?>" required>

            <button type="submit"><?php echo isset($question_to_edit) ? 'Update Question' : 'Add Question'; ?></button>
        </form>

        <h2>Questions List</h2>
        <table id="questions-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Exam Type</th>
                    <th>Question</th>
                    <th>Answer Options</th>
                    <th>Correct Answer</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch all questions to display
                $questions = getQuestions(); 
                foreach ($questions as $question) {
                    echo "<tr>
                        <td>{$question['subject']}</td>
                        <td>{$question['exam_type']}</td>
                        <td>{$question['question']}</td>
                        <td>{$question['answer']}</td>
                        <td>{$question['correct_answer']}</td>
                        <td>
                            <!-- Edit Button -->
                            <a href='?edit={$question['id']}'>Edit</a>
                            <!-- Delete Button -->
                            <form method='POST' action='crud.php' style='display:inline;'>
                                <input type='hidden' name='action' value='delete'>
                                <input type='hidden' name='id' value='{$question['id']}'>
                                <button type='submit'>Delete</button>
                            </form>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="button-div">Home</a>
    </div>
</body>
</html>
