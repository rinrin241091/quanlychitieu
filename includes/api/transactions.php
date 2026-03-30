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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    requireAdmin($db);
    
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $type = isset($_GET['type']) ? strtolower($_GET['type']) : 'all';
    $offset = ($page - 1) * $limit;
    
    $limit = min(max($limit, 1), 100);
    $page = max($page, 1);
    
    if ($type === 'expense') {
        $query = "
            SELECT e.ID, 'Expense' as Type, e.Category, e.ExpenseCost as Amount, e.Description, e.ExpenseDate as TransactionDate,
                   e.UserId, IFNULL(u.name, 'Unknown') AS Author
            FROM tblexpense e
            LEFT JOIN users u ON u.id = e.UserId
            ORDER BY e.ExpenseDate DESC, e.ID DESC
            LIMIT $limit OFFSET $offset
        ";
        $countQuery = "SELECT COUNT(*) as total FROM tblexpense";
    } elseif ($type === 'income') {
        $query = "
            SELECT i.ID, 'Income' as Type, i.Category, i.IncomeAmount as Amount, i.Description, i.IncomeDate as TransactionDate,
                   i.UserId, IFNULL(u.name, 'Unknown') AS Author
            FROM tblincome i
            LEFT JOIN users u ON u.id = i.UserId
            ORDER BY i.IncomeDate DESC, i.ID DESC
            LIMIT $limit OFFSET $offset
        ";
        $countQuery = "SELECT COUNT(*) as total FROM tblincome";
    } else {
        $query = "
            SELECT e.ID, 'Expense' as Type, e.Category, e.ExpenseCost as Amount, e.Description, e.ExpenseDate as TransactionDate,
                   e.UserId, IFNULL(u.name, 'Unknown') AS Author
            FROM tblexpense e
            LEFT JOIN users u ON u.id = e.UserId
            UNION ALL
            SELECT i.ID, 'Income' as Type, i.Category, i.IncomeAmount as Amount, i.Description, i.IncomeDate as TransactionDate,
                   i.UserId, IFNULL(u.name, 'Unknown') AS Author
            FROM tblincome i
            LEFT JOIN users u ON u.id = i.UserId
            ORDER BY TransactionDate DESC, ID DESC
            LIMIT $limit OFFSET $offset
        ";
        $countQuery = "
            SELECT COUNT(*) as total FROM (
                SELECT ID FROM tblexpense
                UNION ALL
                SELECT ID FROM tblincome
            ) as combined_table
        ";
    }
    
    $result = mysqli_query($db, $query);
    $transactions = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $transactions[] = [
                'id' => (int)$row['ID'],
                'type' => $row['Type'],
                'user_id' => (int)$row['UserId'],
                'author' => $row['Author'],
                'category' => $row['Category'],
                'amount' => (float)$row['Amount'],
                'description' => $row['Description'],
                'date' => $row['TransactionDate']
            ];
        }
    }
    
    $countResult = mysqli_query($db, $countQuery);
    $countData = mysqli_fetch_assoc($countResult);
    $total = (int)$countData['total'];
    $total_pages = ceil($total / $limit);
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'transactions' => $transactions,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total,
                'limit' => $limit
            ]
        ]
    ]);
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>
