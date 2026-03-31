<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('../database.php');
include_once('../jwt.php');
include_once('../auth_helper.php');

ensureSecondaryPasswordColumn($db);

function respondError($code, $message) {
    http_response_code($code);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit;
}

function getUserForSecondaryAuth($db, $userId) {
    $stmt = $db->prepare('SELECT id, name, email, IFNULL(avatar, "") AS avatar, IFNULL(is_admin, 0) AS is_admin, IFNULL(must_change_password, 0) AS must_change_password, IFNULL(secondary_password, "") AS secondary_password FROM users WHERE id = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $user ?: null;
}

function isValidSecondaryPin($pin) {
    return is_string($pin) && preg_match('/^[1-8]{4}$/', $pin) === 1;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError(405, 'Method not allowed');
}

$input = json_decode(file_get_contents('php://input'), true);
$action = (string)($input['action'] ?? $_POST['action'] ?? '');
$challengeToken = (string)($input['challenge_token'] ?? $_POST['challenge_token'] ?? '');
$secondaryPassword = (string)($input['secondary_password'] ?? $_POST['secondary_password'] ?? '');
$confirmSecondaryPassword = (string)($input['confirm_secondary_password'] ?? $_POST['confirm_secondary_password'] ?? '');

if ($challengeToken === '') {
    respondError(400, 'Challenge token is required');
}

$challengePayload = JWT::decode($challengeToken);
if (!$challengePayload || ($challengePayload['purpose'] ?? '') !== 'secondary_auth' || empty($challengePayload['user_id'])) {
    respondError(401, 'Invalid or expired challenge token');
}

$userId = (int)$challengePayload['user_id'];
$user = getUserForSecondaryAuth($db, $userId);
if (!$user) {
    respondError(404, 'User not found');
}

$secondaryHash = (string)($user['secondary_password'] ?? '');

if ($action === 'setup') {
    if ($secondaryHash !== '') {
        respondError(409, 'Secondary password is already configured');
    }

    if (!isValidSecondaryPin($secondaryPassword) || !isValidSecondaryPin($confirmSecondaryPassword)) {
        respondError(400, 'Secondary password must be exactly 4 digits using numbers 1 to 8');
    }

    if (!hash_equals($secondaryPassword, $confirmSecondaryPassword)) {
        respondError(400, 'Secondary password confirmation does not match');
    }

    $hashedSecondary = password_hash($secondaryPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare('UPDATE users SET secondary_password = ? WHERE id = ?');
    if (!$stmt) {
        respondError(500, 'Failed to prepare secondary password update');
    }

    $stmt->bind_param('si', $hashedSecondary, $userId);
    if (!$stmt->execute()) {
        $stmt->close();
        respondError(500, 'Failed to save secondary password');
    }
    $stmt->close();

    $secondaryHash = $hashedSecondary;
} elseif ($action === 'verify') {
    if ($secondaryHash === '') {
        respondError(400, 'Secondary password is not configured');
    }

    if (!isValidSecondaryPin($secondaryPassword)) {
        respondError(400, 'Secondary password must be exactly 4 digits using numbers 1 to 8');
    }

    if (!password_verify($secondaryPassword, $secondaryHash)) {
        respondError(401, 'Invalid secondary password');
    }
} else {
    respondError(400, 'Invalid action. Use setup or verify');
}

$_SESSION['detsuid'] = $userId;

$accessTokenPayload = [
    'user_id' => $userId,
    'email' => $user['email'],
    'name' => $user['name'],
    'is_admin' => (int)$user['is_admin'],
    'must_change_password' => (int)$user['must_change_password']
];

$accessToken = JWT::encode($accessTokenPayload);

echo json_encode([
    'status' => 'success',
    'message' => 'Secondary authentication successful',
    'access_token' => $accessToken,
    'user' => [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'avatar' => $user['avatar'],
        'is_admin' => (int)$user['is_admin'],
        'must_change_password' => (int)$user['must_change_password']
    ]
]);
?>