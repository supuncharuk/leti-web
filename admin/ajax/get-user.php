<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
checkLogin();

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $conn = getDbConnection();

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($course = $result->fetch_assoc()) {
        echo json_encode(['status' => 'success', 'data' => $course]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
}