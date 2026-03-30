<?php

if (defined('APP_I18N_INCLUDED')) {
    return;
}
define('APP_I18N_INCLUDED', true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const APP_SUPPORTED_LANGS = ['en', 'vi'];

$GLOBALS['APP_TRANSLATIONS'] = [
    'en' => [
        'brand' => 'Expenditure.',
        'account' => 'Account',
        'about' => 'About',
        'service' => 'Service',
        'contact' => 'Contact',
        'welcome_title' => 'Welcome to',
        'welcome_subtitle' => 'Analyze your spending better.',
        'welcome_desc' => 'Start tracking your daily expenses from any corner of the world.',
        'start_now' => 'Start now',
        'sign_up' => 'Sign Up',
        'login' => 'Login',
        'email_address' => 'Email address',
        'password' => 'Password',
        'remember_me' => 'Remember me',
        'forgot_password' => 'Forgot password?',
        'dont_have_account' => "Don't have an account?",
        'create_account' => 'Create account',
        'login_help' => 'Please enter your login and password!',
        'dashboard' => 'Dashboard',
        'expenses' => 'Expenses',
        'income' => 'Income',
        'manage_list' => 'Manage List',
        'lending' => 'Lending',
        'manage_lending' => 'Manage Lending',
        'analytics' => 'Analytics',
        'report' => 'Report',
        'setting' => 'Setting',
        'logout' => 'Log out',
        'search' => 'Search...',
        'today_expense' => 'Today Expense',
        'yesterday_expense' => 'Yesterday Expense',
        'last_30_expense' => 'Last 30 day Expense',
        'total_expense' => 'Total Expense',
        'expense_chart' => 'Expense Chart',
        'category_table' => 'Category Table',
        'percentage' => 'Percentage',
        'category' => 'Category',
        'amount' => 'Amount',
        'total' => 'Total',
        'add_expense' => 'Add Expense',
        'user_profile' => 'User Profile',
        'up_from_today' => 'Up from Today',
        'up_from_yesterday' => 'Up from yesterday',
        'up_from_last_30' => 'Up from Last 30 day',
        'up_from_year' => 'Up from Year',
        'signup_title' => 'Sign up',
        'your_name' => 'Your Name',
        'your_email' => 'Your Email',
        'mobile_number' => 'Mobile Number',
        'confirm_password' => 'Confirm Password',
        'terms_agree' => 'I agree all statements in',
        'terms_service' => 'Terms of service',
        'have_account' => 'Have already an account?',
        'login_here' => 'Login here',
        'lang_vi' => 'VI',
        'lang_en' => 'EN',
        'income_tracker' => 'Income Tracker',
        'expense_tracker' => 'Expense Tracker',
        'add_income' => 'Add Income',
        'add_category' => 'Add Category',
        'add_income_category' => 'Add Income Category',
        'category_name' => 'Category Name',
        'cancel' => 'Cancel',
        'date_of_income' => 'Date of Income',
        'choose_category' => 'Choose Category',
        'amount_of_income' => 'Amount of Income',
        'description' => 'Description',
        'add' => 'Add',
        'add_expense_page' => 'Add Expense',
        'date_of_expense' => 'Date of Expense',
        'amount_of_expense' => 'Amount of Expense',
        'logout_title' => 'Logout',
    ],
    'vi' => [
        'brand' => 'Chi Tiêu.',
        'account' => 'Tài khoản',
        'about' => 'Giới thiệu',
        'service' => 'Dịch vụ',
        'contact' => 'Liên hệ',
        'welcome_title' => 'Chào mừng đến',
        'welcome_subtitle' => 'Phân tích chi tiêu thông minh hơn.',
        'welcome_desc' => 'Bắt đầu theo dõi chi tiêu hằng ngày ở mọi nơi.',
        'start_now' => 'Bắt đầu ngay',
        'sign_up' => 'Đăng ký',
        'login' => 'Đăng nhập',
        'email_address' => 'Địa chỉ email',
        'password' => 'Mật khẩu',
        'remember_me' => 'Ghi nhớ đăng nhập',
        'forgot_password' => 'Quên mật khẩu?',
        'dont_have_account' => 'Bạn chưa có tài khoản?',
        'create_account' => 'Tạo tài khoản',
        'login_help' => 'Vui lòng nhập email và mật khẩu!',
        'dashboard' => 'Tổng quan',
        'expenses' => 'Chi tiêu',
        'income' => 'Thu nhập',
        'manage_list' => 'Quản lý giao dịch',
        'lending' => 'Cho vay',
        'manage_lending' => 'Quản lý cho vay',
        'analytics' => 'Phân tích',
        'report' => 'Báo cáo',
        'setting' => 'Cài đặt',
        'logout' => 'Đăng xuất',
        'search' => 'Tìm kiếm...',
        'today_expense' => 'Chi tiêu hôm nay',
        'yesterday_expense' => 'Chi tiêu hôm qua',
        'last_30_expense' => 'Chi tiêu 30 ngày',
        'total_expense' => 'Tổng chi tiêu',
        'expense_chart' => 'Biểu đồ chi tiêu',
        'category_table' => 'Bảng danh mục',
        'percentage' => 'Tỷ lệ',
        'category' => 'Danh mục',
        'amount' => 'Số tiền',
        'total' => 'Tổng',
        'add_expense' => 'Thêm chi tiêu',
        'user_profile' => 'Hồ sơ người dùng',
        'up_from_today' => 'Tăng so với hôm nay',
        'up_from_yesterday' => 'Tăng so với hôm qua',
        'up_from_last_30' => 'Tăng so với 30 ngày',
        'up_from_year' => 'Tăng so với năm',
        'signup_title' => 'Đăng ký',
        'your_name' => 'Họ tên',
        'your_email' => 'Email',
        'mobile_number' => 'Số điện thoại',
        'confirm_password' => 'Xác nhận mật khẩu',
        'terms_agree' => 'Tôi đồng ý với',
        'terms_service' => 'Điều khoản dịch vụ',
        'have_account' => 'Bạn đã có tài khoản?',
        'login_here' => 'Đăng nhập tại đây',
        'lang_vi' => 'VI',
        'lang_en' => 'EN',
        'income_tracker' => 'Quản lý thu nhập',
        'expense_tracker' => 'Quản lý chi tiêu',
        'add_income' => 'Thêm thu nhập',
        'add_category' => 'Thêm danh mục',
        'add_income_category' => 'Thêm danh mục thu nhập',
        'category_name' => 'Tên danh mục',
        'cancel' => 'Hủy',
        'date_of_income' => 'Ngày thu nhập',
        'choose_category' => 'Chọn danh mục',
        'amount_of_income' => 'Số tiền thu',
        'description' => 'Mô tả',
        'add' => 'Thêm',
        'add_expense_page' => 'Thêm chi tiêu',
        'date_of_expense' => 'Ngày chi tiêu',
        'amount_of_expense' => 'Số tiền chi',
        'logout_title' => 'Đăng xuất',
    ],
];

function current_lang(): string
{
    static $resolved = null;
    if ($resolved !== null) {
        return $resolved;
    }

    $lang = null;
    if (isset($_GET['lang'])) {
        $lang = strtolower(trim((string) $_GET['lang']));
    } elseif (isset($_SESSION['lang'])) {
        $lang = strtolower(trim((string) $_SESSION['lang']));
    } elseif (isset($_COOKIE['lang'])) {
        $lang = strtolower(trim((string) $_COOKIE['lang']));
    }

    if (!in_array($lang, APP_SUPPORTED_LANGS, true)) {
        $lang = 'vi';
    }

    $_SESSION['lang'] = $lang;
    if (!headers_sent()) {
        setcookie('lang', $lang, time() + (86400 * 365), '/');
    }

    $resolved = $lang;
    return $resolved;
}

function t(string $key): string
{
    $lang = current_lang();
    $translations = $GLOBALS['APP_TRANSLATIONS'] ?? [];

    if (isset($translations[$lang][$key])) {
        return $translations[$lang][$key];
    }

    if (isset($translations['en'][$key])) {
        return $translations['en'][$key];
    }

    return $key;
}

function switch_lang_url(string $lang): string
{
    $lang = in_array($lang, APP_SUPPORTED_LANGS, true) ? $lang : 'vi';

    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = strtok($uri, '?');
    $query = [];
    parse_str(parse_url($uri, PHP_URL_QUERY) ?? '', $query);
    $query['lang'] = $lang;

    return $path . '?' . http_build_query($query);
}

function with_lang(string $url): string
{
    $parts = parse_url($url);
    $path = $parts['path'] ?? $url;
    $query = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
    }
    $query['lang'] = current_lang();
    return $path . '?' . http_build_query($query);
}
