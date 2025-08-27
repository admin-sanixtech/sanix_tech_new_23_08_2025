<?php
require 'db_connection.php';

header('Content-Type: application/json');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$post_id = isset($input['post_id']) ? intval($input['post_id']) : 0;

if ($post_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit;
}

// Update likes count
$update_query = "UPDATE posts SET likes = likes + 1 WHERE post_id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("i", $post_id);

if ($stmt->execute()) {
    // Get updated like count
    $select_query = "SELECT likes FROM posts WHERE post_id = ?";
    $stmt = $conn->prepare($select_query);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true, 
        'likes' => $post['likes'],
        'message' => 'Post liked successfully'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Error updating likes'
    ]);
}
?>