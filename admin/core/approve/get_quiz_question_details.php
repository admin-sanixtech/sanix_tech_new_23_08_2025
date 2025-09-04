<?php
// get_quiz_question_details.php
session_start();
require_once(__DIR__ . '/../../config/db_connection.php');

// Set content type to JSON
header('Content-Type: application/json');

// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

// Check if this is a POST request with the required data
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['question_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request. Question ID is required.']);
    exit();
}

$question_id = intval($_POST['question_id']);

if ($question_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid question ID']);
    exit();
}

try {
    // Fetch question details
    $sql = "SELECT 
        qp.pending_id,
        qp.question_text,
        qp.question_content,
        qp.question_type,
        qp.correct_answer,
        qp.option_a,
        qp.option_a_content,
        qp.option_b,
        qp.option_b_content,
        qp.option_c,
        qp.option_c_content,
        qp.option_d,
        qp.option_d_content,
        qp.code_snippet,
        qp.difficulty_level,
        qp.description,
        qp.answer_content,
        qp.created_by,
        qp.created_at,
        qp.status,
        qp.category_id,
        qp.subcategory_id,
        COALESCE(c.category_name, 'Uncategorized') as category_name,
        COALESCE(sc.subcategory_name, '') as subcategory_name,
        COALESCE(u.name, 'Unknown User') as creator_name
    FROM quiz_questions_pending qp
    LEFT JOIN categories c ON qp.category_id = c.category_id
    LEFT JOIN subcategories sc ON qp.subcategory_id = sc.subcategory_id
    LEFT JOIN users u ON qp.created_by = u.user_id
    WHERE qp.pending_id = ? AND qp.status = 'pending'";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database prepare error: " . $conn->error);
    }
    
    $stmt->bind_param("i", $question_id);
    if (!$stmt->execute()) {
        throw new Exception("Database execute error: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Question not found or not pending approval']);
        exit();
    }
    
    $question = $result->fetch_assoc();
    $stmt->close();
    
    // Clean up and prepare the data
    $question_data = [
        'pending_id' => intval($question['pending_id']),
        'question_text' => $question['question_text'] ?? '',
        'question_content' => $question['question_content'] ?? '',
        'question_type' => $question['question_type'] ?? '',
        'correct_answer' => $question['correct_answer'] ?? '',
        'option_a' => $question['option_a'] ?? '',
        'option_a_content' => $question['option_a_content'] ?? '',
        'option_b' => $question['option_b'] ?? '',
        'option_b_content' => $question['option_b_content'] ?? '',
        'option_c' => $question['option_c'] ?? '',
        'option_c_content' => $question['option_c_content'] ?? '',
        'option_d' => $question['option_d'] ?? '',
        'option_d_content' => $question['option_d_content'] ?? '',
        'code_snippet' => $question['code_snippet'] ?? '',
        'difficulty_level' => $question['difficulty_level'] ?? 'Unknown',
        'description' => $question['description'] ?? '',
        'answer_content' => $question['answer_content'] ?? '',
        'category_name' => $question['category_name'] ?? 'Uncategorized',
        'subcategory_name' => $question['subcategory_name'] ?? '',
        'creator_name' => $question['creator_name'] ?? 'Unknown User',
        'created_at' => $question['created_at'] ?? ''
    ];
    
    // Log successful fetch for debugging
    error_log("Successfully fetched question details for ID: $question_id");
    
    echo json_encode(['success' => true, 'question' => $question_data]);
    
} catch (Exception $e) {
    error_log("Error in get_quiz_question_details.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>