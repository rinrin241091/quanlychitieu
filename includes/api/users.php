<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once('../database.php');
include_once('../auth_helper.php');

$adminId = requireAdmin($db);
ensureSecondaryPasswordColumn($db);

function ensureAtLeastOneAdmin($db, $targetUserId, $nextIsAdmin) {
    if ($nextIsAdmin === 1) {
        return true;
    }

    $targetStmt = $db->prepare('SELECT is_admin FROM users WHERE id = ? LIMIT 1');
    $targetStmt->bind_param('i', $targetUserId);
    $targetStmt->execute();
    $targetResult = $targetStmt->get_result();
    $targetRow = $targetResult ? $targetResult->fetch_assoc() : null;
    $targetStmt->close();

    if (empty($targetRow)) {
        return false;
    }

    // If target user is already non-admin, this update cannot remove the last admin.
    if ((int)$targetRow['is_admin'] !== 1) {
        return true;
    }

    $countStmt = $db->prepare('SELECT COUNT(*) AS total_admins FROM users WHERE is_admin = 1 AND id <> ?');
    $countStmt->bind_param('i', $targetUserId);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $countData = $countResult ? $countResult->fetch_assoc() : ['total_admins' => 0];
    $countStmt->close();

    if ((int)$countData['total_admins'] < 1) {
        return false;
    }

    return true;
}

function emailExists($db, $email, $excludeId = 0) {
    if ($excludeId > 0) {
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1');
        $stmt->bind_param('si', $email, $excludeId);
    } else {
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();
    return !empty($row);
}

function nameExists($db, $name, $excludeId = 0) {
    if ($excludeId > 0) {
        $stmt = $db->prepare('SELECT id FROM users WHERE name = ? AND id <> ? LIMIT 1');
        $stmt->bind_param('si', $name, $excludeId);
    } else {
        $stmt = $db->prepare('SELECT id FROM users WHERE name = ? LIMIT 1');
        $stmt->bind_param('s', $name);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();
    return !empty($row);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $db->query("SELECT id, name, email, phone, created_at, is_admin, IFNULL(must_change_password, 0) AS must_change_password, CASE WHEN IFNULL(secondary_password, '') = '' THEN 0 ELSE 1 END AS has_secondary_password FROM users ORDER BY created_at DESC");
    if (!$result) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch users']);
        exit;
    }

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'created_at' => $row['created_at'],
            'is_admin' => (int)$row['is_admin'],
            'must_change_password' => (int)$row['must_change_password'],
            'has_secondary_password' => (int)$row['has_secondary_password']
        ];
    }

    echo json_encode([
        'status' => 'success',
        'data' => [
            'users' => $users
        ]
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $action = strtolower(trim((string)($input['action'] ?? 'set_role')));

    if ($action === 'create') {
        $name = trim((string)($input['name'] ?? ''));
        $email = trim((string)($input['email'] ?? ''));
        $phone = trim((string)($input['phone'] ?? ''));
        $password = (string)($input['password'] ?? '');
        $secondaryPassword = (string)($input['secondary_password'] ?? '');
        $isAdmin = ((int)($input['is_admin'] ?? 0) === 1) ? 1 : 0;

        if ($name === '' || $email === '' || $phone === '' || $password === '' || $secondaryPassword === '') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid email address']);
            exit;
        }

        if (strlen($password) < 8) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters']);
            exit;
        }

        if (strlen($secondaryPassword) < 4) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Secondary password must be at least 4 characters']);
            exit;
        }

        if (emailExists($db, $email)) {
            http_response_code(409);
            echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
            exit;
        }

        if (nameExists($db, $name)) {
            http_response_code(409);
            echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
            exit;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $secondaryHash = password_hash($secondaryPassword, PASSWORD_DEFAULT);
        $verificationCode = md5((string)mt_rand());

        $stmt = $db->prepare('INSERT INTO users (name, email, phone, password, secondary_password, verification_code, created_at, is_admin, must_change_password) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, 1)');
        $stmt->bind_param('ssssssi', $name, $email, $phone, $passwordHash, $secondaryHash, $verificationCode, $isAdmin);
        $ok = $stmt->execute();
        $stmt->close();

        if (!$ok) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to create user']);
            exit;
        }

        echo json_encode(['status' => 'success', 'message' => 'User created successfully']);
        exit;
    }

    if ($action === 'update') {
        $userId = (int)($input['user_id'] ?? 0);
        $name = trim((string)($input['name'] ?? ''));
        $email = trim((string)($input['email'] ?? ''));
        $phone = trim((string)($input['phone'] ?? ''));
        $isAdmin = ((int)($input['is_admin'] ?? 0) === 1) ? 1 : 0;

        if ($userId <= 0 || $name === '' || $email === '' || $phone === '') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid update payload']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid email address']);
            exit;
        }

        if ($userId === (int)$adminId && $isAdmin === 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'You cannot remove your own admin permission']);
            exit;
        }

        if (!ensureAtLeastOneAdmin($db, $userId, $isAdmin)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'System must have at least one admin']);
            exit;
        }

        if (emailExists($db, $email, $userId)) {
            http_response_code(409);
            echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
            exit;
        }

        if (nameExists($db, $name, $userId)) {
            http_response_code(409);
            echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
            exit;
        }

        $stmt = $db->prepare('UPDATE users SET name = ?, email = ?, phone = ?, is_admin = ? WHERE id = ?');
        $stmt->bind_param('sssii', $name, $email, $phone, $isAdmin, $userId);
        $ok = $stmt->execute();
        $stmt->close();

        if (!$ok) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update user']);
            exit;
        }

        echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
        exit;
    }

    if ($action === 'reset_password') {
        $userId = (int)($input['user_id'] ?? 0);
        $newPassword = (string)($input['new_password'] ?? '');
        $newSecondaryPassword = (string)($input['new_secondary_password'] ?? '');

        if ($userId <= 0 || $newPassword === '') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid reset payload']);
            exit;
        }

        if (strlen($newPassword) < 8) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters']);
            exit;
        }

        if ($newSecondaryPassword !== '' && strlen($newSecondaryPassword) < 4) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Secondary password must be at least 4 characters']);
            exit;
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($newSecondaryPassword !== '') {
            $newSecondaryHash = password_hash($newSecondaryPassword, PASSWORD_DEFAULT);
            $stmt = $db->prepare('UPDATE users SET password = ?, secondary_password = ?, must_change_password = 1 WHERE id = ?');
            $stmt->bind_param('ssi', $newHash, $newSecondaryHash, $userId);
        } else {
            $stmt = $db->prepare('UPDATE users SET password = ?, must_change_password = 1 WHERE id = ?');
            $stmt->bind_param('si', $newHash, $userId);
        }
        $ok = $stmt->execute();
        $stmt->close();

        if (!$ok) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to reset password']);
            exit;
        }

        echo json_encode(['status' => 'success', 'message' => 'Password reset successfully. User must change password on next login.']);
        exit;
    }

    // Backward compatible role update flow.
    $userId = isset($input['user_id']) ? (int)$input['user_id'] : 0;
    $isAdmin = isset($input['is_admin']) ? (int)$input['is_admin'] : 0;
    $isAdmin = $isAdmin === 1 ? 1 : 0;

    if ($userId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid user id']);
        exit;
    }

    if ($userId === (int)$adminId && $isAdmin === 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'You cannot remove your own admin permission']);
        exit;
    }

    if (!ensureAtLeastOneAdmin($db, $userId, $isAdmin)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'System must have at least one admin']);
        exit;
    }

    $stmt = $db->prepare('UPDATE users SET is_admin = ? WHERE id = ?');
    $stmt->bind_param('ii', $isAdmin, $userId);
    $ok = $stmt->execute();
    $stmt->close();

    if (!$ok) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to update user role']);
        exit;
    }

    echo json_encode(['status' => 'success', 'message' => 'User role updated successfully']);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
