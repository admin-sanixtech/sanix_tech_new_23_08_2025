<?php
// get_question_details.php
session_start();
require_once(__DIR__ . '/../../config/db_connection.php');


// Set JSON header
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

// Check if it's a POST request with the correct action
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'get_question_details') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit();
}

// Check if question_id is provided
if (!isset($_POST['question_id']) || !is_numeric($_POST['question_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Question ID is required and must be numeric']);
    exit();
}

$question_id = intval($_POST['question_id']);

try {
    // Fetch complete question details with category and creator information - Updated for correct table
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
        qp.reviewed_by,
        qp.reviewed_at,
        qp.review_notes,
        c.category_name,
        sc.subcategory_name,
        u.username as creator_name,
        u.email as creator_email
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
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Question not found or not pending approval']);
        $stmt->close();
        $conn->close();
        exit();
    }

    $question = $result->fetch_assoc();
    $stmt->close();

    // For this table structure, we don't need separate approval history since it's built-in
    $question['approval_history'] = [];

    // Clean up and format data - Updated for new structure
    $question['question_text'] = htmlspecialchars($question['question_text']);
    $question['question_content'] = htmlspecialchars($question['question_content'] ?? '');
    $question['description'] = htmlspecialchars($question['description'] ?? '');
    $question['answer_content'] = htmlspecialchars($question['answer_content'] ?? '');
    $question['code_snippet'] = htmlspecialchars($question['code_snippet'] ?? '');
    
    // Handle the new option structure
    $question['option_a'] = htmlspecialchars($question['option_a'] ?? '');
    $question['option_a_content'] = htmlspecialchars($question['option_a_content'] ?? '');
    $question['option_b'] = htmlspecialchars($question['option_b'] ?? '');
    $question['option_b_content'] = htmlspecialchars($question['option_b_content'] ?? '');
    $question['option_c'] = htmlspecialchars($question['option_c'] ?? '');
    $question['option_c_content'] = htmlspecialchars($question['option_c_content'] ?? '');
    $question['option_d'] = htmlspecialchars($question['option_d'] ?? '');
    $question['option_d_content'] = htmlspecialchars($question['option_d_content'] ?? '');
    
    $question['correct_answer'] = htmlspecialchars($question['correct_answer']);
    $question['creator_name'] = htmlspecialchars($question['creator_name'] ?? 'Unknown');
    $question['category_name'] = htmlspecialchars($question['category_name'] ?? '');
    $question['subcategory_name'] = htmlspecialchars($question['subcategory_name'] ?? '');

    // Return success response
    echo json_encode([
        'success' => true,
        'question' => $question
    ]);']);
        $stmt->close();
        $conn->close();
        exit();
    }

    $question = $result->fetch_assoc();
    $stmt->close();

    // For this table structure, we don't need separate approval history since it's built-in
    $question['approval_history'] = [];

    // Clean up and format data - Updated for new structure
    $question['question_text'] = htmlspecialchars($question['question_text']);
    $question['question_content'] = htmlspecialchars($question['question_content'] ?? '');
    $question['description'] = htmlspecialchars($question['description'] ?? '');
    $question['answer_content'] = htmlspecialchars($question['answer_content'] ?? '');
    $question['code_snippet'] = htmlspecialchars($question['code_snippet'] ?? '');
    
    // Handle the new option structure
    $question['option_a'] = htmlspecialchars($question['option_a'] ?? '');
    $question['option_a_content'] = htmlspecialchars($question['option_a_content'] ?? '');
    $question['option_b'] = htmlspecialchars($question['option_b'] ?? '');
    $question['option_b_content'] = htmlspecialchars($question['option_b_content'] ?? '');
    $question['option_c'] = htmlspecialchars($question['option_c'] ?? '');
    $question['option_c_content'] = htmlspecialchars($question['option_c_content'] ?? '');
    $question['option_d'] = htmlspecialchars($question['option_d'] ?? '');
    $question['option_d_content'] = htmlspecialchars($question['option_d_content'] ?? '');
    
    $question['correct_answer'] = htmlspecialchars($question['correct_answer']);
    $question['creator_name'] = htmlspecialchars($question['creator_name'] ?? 'Unknown');
    $question['category_name'] = htmlspecialchars($question['category_name'] ?? '');
    $question['subcategory_name'] = htmlspecialchars($question['subcategory_name'] ?? '');

    // Return success response
    echo json_encode([
        'success' => true,
        'question' => $question
    ]);']);
        $stmt->close();
        $conn->close();
        exit();
    }

    $question = $result->fetch_assoc();
    $stmt->close();

    // Get approval history if any (for context)
    $history_sql = "SELECT 
        qa.action,
        qa.action_date,
        qa.notes,
        u.username as admin_name
    FROM quiz_question_approvals qa
    LEFT JOIN users u ON qa.admin_id = u.user_id
    WHERE qa.question_id = ?
    ORDER BY qa.action_date DESC";
    
    $history_stmt = $conn->prepare($history_sql);
    $history = [];
    
    if ($history_stmt) {
        $history_stmt->bind_param("i", $question_id);
        if ($history_stmt->execute()) {
            $history_result = $history_stmt->get_result();
            while ($row = $history_result->fetch_assoc()) {
                $history[] = $row;
            }
        }
        $history_stmt->close();
    }

    // Add history to question data
    $question['approval_history'] = $history;

    // Clean up and format data
    $question['question_text'] = htmlspecialchars($question['question_text']);
    $question['description'] = htmlspecialchars($question['description'] ?? '');
    $question['code_snippet'] = htmlspecialchars($question['code_snippet'] ?? '');
    $question['option_a'] = htmlspecialchars($question['option_a'] ?? '');
    $question['option_b'] = htmlspecialchars($question['option_b'] ?? '');
    $question['option_c'] = htmlspecialchars($question['option_c'] ?? '');
    $question['option_d'] = htmlspecialchars($question['option_d'] ?? '');
    $question['correct_answer'] = htmlspecialchars($question['correct_answer']);
    $question['creator_name'] = htmlspecialchars($question['creator_name'] ?? 'Unknown');
    $question['category_name'] = htmlspecialchars($question['category_name'] ?? '');
    $question['subcategory_name'] = htmlspecialchars($question['subcategory_name'] ?? '');

    // Return success response
    echo json_encode([
        'success' => true,
        'question' => $question
    ]);

} catch (Exception $e) {
    error_log("Get question details error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred while fetching question details'
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>