<?php
include_once(__DIR__ . '/jwt.php');

function getAuthenticatedUserId() {
    $jwtUserId = JWT::getUserId();
    if ($jwtUserId) {
        return $jwtUserId;
    }
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!empty($_SESSION['detsuid'])) {
        return $_SESSION['detsuid'];
    }
    
    return null;
}

function requireAuthentication() {
    $userId = getAuthenticatedUserId();
    
    if (!$userId) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized - Please login']);
        exit;
    }
    
    if (isset($GLOBALS['db'])) {
        enforcePasswordChangeForApi($GLOBALS['db'], $userId);
    }

    return $userId;
}

function hasAdminColumn($db) {
    static $checked = false;
    static $exists = false;

    if ($checked) {
        return $exists;
    }

    $checked = true;
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
    $exists = $result && $result->num_rows > 0;

    return $exists;
}

function hasMustChangePasswordColumn($db) {
    static $checked = false;
    static $exists = false;

    if ($checked) {
        return $exists;
    }

    $checked = true;
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'must_change_password'");
    $exists = $result && $result->num_rows > 0;

    return $exists;
}

function hasSecondaryPasswordColumn($db) {
    static $checked = false;
    static $exists = false;

    if ($checked) {
        return $exists;
    }

    $checked = true;
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'secondary_password'");
    $exists = $result && $result->num_rows > 0;

    return $exists;
}

function ensureSecondaryPasswordColumn($db) {
    if (hasSecondaryPasswordColumn($db)) {
        return;
    }

    $db->query("ALTER TABLE users ADD COLUMN secondary_password VARCHAR(255) NULL AFTER password");
}

function isPasswordChangeRequired($db, $userId) {
    if (!hasMustChangePasswordColumn($db)) {
        return false;
    }

    $stmt = $db->prepare('SELECT must_change_password FROM users WHERE id = ? LIMIT 1');
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return !empty($row) && (int)$row['must_change_password'] === 1;
}

function canBypassPasswordChangeForCurrentApi() {
    $script = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $allowed = [
        'login.php',
        'logout.php',
        'change-password-first-login.php'
    ];
    return in_array($script, $allowed, true);
}

function enforcePasswordChangeForApi($db, $userId) {
    if (canBypassPasswordChangeForCurrentApi()) {
        return;
    }

    if (!isPasswordChangeRequired($db, $userId)) {
        return;
    }

    header('Content-Type: application/json');
    http_response_code(428);
    echo json_encode([
        'status' => 'error',
        'code' => 'PASSWORD_CHANGE_REQUIRED',
        'message' => 'Password change required before using the system'
    ]);
    exit;
}

function userHasAdminPrivilege($db, $userId) {
    if (!hasAdminColumn($db)) {
        return false;
    }

    $stmt = $db->prepare('SELECT is_admin FROM users WHERE id = ? LIMIT 1');
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return !empty($user) && (int)$user['is_admin'] === 1;
}

function requireAdmin($db) {
    $userId = requireAuthentication();

    if (!userHasAdminPrivilege($db, $userId)) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Forbidden - Admin access required']);
        exit;
    }

    return $userId;
}
?>
