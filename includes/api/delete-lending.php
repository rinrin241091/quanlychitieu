<?php
header('Content-Type: application/json');
http_response_code(410);
echo json_encode(['status' => 'error', 'message' => 'Lending feature has been removed']);
exit;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once('../database.php');
include_once('../auth_helper.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = requireAuthentication();
    
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid cho vay ID']);
        exit;
    }
    
    $checkQuery = mysqli_query($db, "SELECT id FROM cho vay WHERE id='$id' AND UserId='$userid'");
    if (mysqli_num_rows($checkQuery) === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Cho vay record not found']);
        exit;
    }
    
    $deleteQuery = mysqli_query($db, "DELETE FROM cho vay WHERE id='$id' AND UserId='$userid'");
    
    if ($deleteQuery) {
        echo json_encode(['status' => 'success', 'message' => 'Cho vay record deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete cho vay record']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>

