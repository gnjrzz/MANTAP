<?php
session_start();
require_once __DIR__ . '/../../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_history') {
    $user_id = $_SESSION['user_id'];
    $calories = $_POST['calories'];
    $protein = $_POST['protein'];
    $fat = $_POST['fat'];
    $carbs = $_POST['carbs'];

    $stmt = $conn->prepare("INSERT INTO calculation_history (user_id, calories, protein, fat, carbs) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("idddd", $user_id, $calories, $protein, $fat, $carbs);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
    exit;
}
?>