<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once('../database.php');
include_once('../auth_helper.php');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$adminId = requireAdmin($db);

$sql = "
    SELECT
        u.id,
        u.name,
        COALESCE(SUM(CAST(e.ExpenseCost AS DECIMAL(15,2))), 0) AS total_expense
    FROM users u
    LEFT JOIN tblexpense e
        ON e.UserId = u.id
        AND e.ExpenseDate BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()
    GROUP BY u.id, u.name
    ORDER BY total_expense DESC, u.name ASC
";
$stmt = $db->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query']);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'user_id' => (int)$row['id'],
        'name' => $row['name'],
        'total_expense' => (float)$row['total_expense']
    ];
}

$stmt->close();

echo json_encode([
    'status' => 'success',
    'data' => $data
]);
