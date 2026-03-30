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

$userId = getAuthenticatedUserId();
if (!$userId) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if (!isPasswordChangeRequired($db, $userId)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password change is not required']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$newPassword = $input['new_password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? '';

if ($newPassword === '' || $confirmPassword === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'New password and confirmation are required']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
    exit;
}

if (strlen($newPassword) < 8) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters']);
    exit;
}

$stmt = $db->prepare('SELECT id FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$user) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

$newHash = password_hash($newPassword, PASSWORD_DEFAULT);
$update = $db->prepare('UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?');
$update->bind_param('si', $newHash, $userId);
$ok = $update->execute();
$update->close();

if (!$ok) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => 'Password changed successfully. You can now use the system.'
]);
