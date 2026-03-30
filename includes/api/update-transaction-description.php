<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once('../database.php');
include_once('../auth_helper.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

requireAdmin($db);

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$type = strtolower(trim((string)($_POST['type'] ?? '')));
$description = trim((string)($_POST['description'] ?? ''));

if ($id <= 0 || ($type !== 'expense' && $type !== 'income')) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request payload']);
    exit;
}

if ($type === 'expense') {
    $stmt = $db->prepare('UPDATE tblexpense SET Description = ? WHERE ID = ?');
} else {
    $stmt = $db->prepare('UPDATE tblincome SET Description = ? WHERE ID = ?');
}

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Unable to prepare update query']);
    exit;
}

$stmt->bind_param('si', $description, $id);
$ok = $stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if (!$ok) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to update description']);
    exit;
}

if ($affected < 0) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Description update failed']);
    exit;
}

echo json_encode(['status' => 'success', 'message' => 'Description updated successfully']);
