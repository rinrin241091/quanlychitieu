<?php
session_start(); // added session_start() to start session
include('database.php');

if (empty($_SESSION['detsuid'])) {
    header('location:index.php');
    exit;
}

function hasAvatarColumn($db) {
    static $checked = false;
    static $exists = false;

    if ($checked) {
        return $exists;
    }

    $checked = true;
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'avatar'");
    $exists = $result && $result->num_rows > 0;
    return $exists;
}

if (isset($_POST['update_user'])) {

    $userid = (int)$_SESSION['detsuid'];
    $username = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($username === '' || $email === '' || $phone === '') {
        $message = "Name, email and phone are required";
        echo "<script type='text/javascript'>alert('" . addslashes($message) . "');</script>";
        echo " <script type='text/javascript'>window.location.href = 'user_profile.php';</script>";
        exit();
    }

    $hasAvatar = hasAvatarColumn($db);
    $newAvatarPath = '';

    if ($hasAvatar && isset($_FILES['avatar']) && is_array($_FILES['avatar']) && (int)$_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadError = (int)($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($uploadError !== UPLOAD_ERR_OK) {
            $message = 'Failed to upload avatar';
            echo "<script type='text/javascript'>alert('" . addslashes($message) . "');</script>";
            echo "<script type='text/javascript'>window.location.href = 'user_profile.php';</script>";
            exit();
        }

        $tmpFile = $_FILES['avatar']['tmp_name'] ?? '';
        $originalName = $_FILES['avatar']['name'] ?? '';
        $fileSize = (int)($_FILES['avatar']['size'] ?? 0);

        if ($tmpFile === '' || !is_uploaded_file($tmpFile)) {
            $message = 'Invalid avatar file';
            echo "<script type='text/javascript'>alert('" . addslashes($message) . "');</script>";
            echo "<script type='text/javascript'>window.location.href = 'user_profile.php';</script>";
            exit();
        }

        if ($fileSize > 5 * 1024 * 1024) {
            $message = 'Avatar must be smaller than 5MB';
            echo "<script type='text/javascript'>alert('" . addslashes($message) . "');</script>";
            echo "<script type='text/javascript'>window.location.href = 'user_profile.php';</script>";
            exit();
        }

        $ext = strtolower((string)pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            $message = 'Only JPG, PNG, GIF or WEBP avatar files are allowed';
            echo "<script type='text/javascript'>alert('" . addslashes($message) . "');</script>";
            echo "<script type='text/javascript'>window.location.href = 'user_profile.php';</script>";
            exit();
        }

        $avatarDirFs = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'avatars';
        if (!is_dir($avatarDirFs) && !mkdir($avatarDirFs, 0775, true)) {
            $message = 'Unable to prepare avatar storage';
            echo "<script type='text/javascript'>alert('" . addslashes($message) . "');</script>";
            echo "<script type='text/javascript'>window.location.href = 'user_profile.php';</script>";
            exit();
        }

        $newFilename = 'avatar_' . $userid . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        $targetFs = $avatarDirFs . DIRECTORY_SEPARATOR . $newFilename;
        if (!move_uploaded_file($tmpFile, $targetFs)) {
            $message = 'Unable to save avatar file';
            echo "<script type='text/javascript'>alert('" . addslashes($message) . "');</script>";
            echo "<script type='text/javascript'>window.location.href = 'user_profile.php';</script>";
            exit();
        }

        $newAvatarPath = 'images/avatars/' . $newFilename;

        $oldStmt = $db->prepare('SELECT avatar FROM users WHERE id = ? LIMIT 1');
        if ($oldStmt) {
            $oldStmt->bind_param('i', $userid);
            $oldStmt->execute();
            $oldRes = $oldStmt->get_result();
            $oldRow = $oldRes ? $oldRes->fetch_assoc() : null;
            $oldStmt->close();

            $oldAvatar = $oldRow['avatar'] ?? '';
            if ($oldAvatar !== '' && strpos($oldAvatar, 'images/avatars/') === 0) {
                $oldFs = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $oldAvatar);
                if (is_file($oldFs)) {
                    @unlink($oldFs);
                }
            }
        }
    }

    if ($hasAvatar && $newAvatarPath !== '') {
        $stmt = $db->prepare('UPDATE users SET name = ?, email = ?, phone = ?, avatar = ? WHERE id = ?');
        $stmt->bind_param('ssssi', $username, $email, $phone, $newAvatarPath, $userid);
    } else {
        $stmt = $db->prepare('UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?');
        $stmt->bind_param('sssi', $username, $email, $phone, $userid);
    }

    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        $message = "Profile updated successfully";
        $avatarJs = $newAvatarPath !== '' ? addslashes($newAvatarPath) : '';
        echo "<script type='text/javascript'>\n"
            . "try {\n"
            . "  var raw = localStorage.getItem('user_data') || '{}';\n"
            . "  var user = JSON.parse(raw);\n"
            . "  user.name = '" . addslashes($username) . "';\n"
            . "  user.email = '" . addslashes($email) . "';\n"
            . ($avatarJs !== '' ? "  user.avatar = '" . $avatarJs . "';\n" : "")
            . "  localStorage.setItem('user_data', JSON.stringify(user));\n"
            . "} catch (e) {}\n"
            . "alert('" . addslashes($message) . "');\n"
            . "window.location.href = 'user_profile.php';\n"
            . "</script>";
        exit();
    } else {
        // Handle the error case
        echo "Error updating user information: " . mysqli_error($db);
    }
}

?>