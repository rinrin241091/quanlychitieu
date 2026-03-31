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
        'manage_users' => 'Manage Users',
        'user_permission_management' => 'User Permission Management',
        'create_user' => 'Create User',
        'create_new_user' => 'Create New User',
        'edit_user' => 'Edit User',
        'reset_user_password' => 'Reset User Password',
        'new_temporary_password' => 'New Temporary Password',
        'new_secondary_password_optional' => 'New Secondary Password (optional)',
        'secondary_password_rule' => 'Secondary password must be exactly 4 digits, using numbers 1 to 8.',
        'after_reset_must_change' => 'After reset, user must change password on next login.',
        'role' => 'Role',
        'action' => 'Action',
        'name' => 'Name',
        'phone' => 'Phone',
        'created_at' => 'Created At',
        'password_policy' => 'Password Policy',
        'secondary_password' => 'Secondary Password',
        'configured' => 'Configured',
        'not_set' => 'Not set',
        'must_change_next_login' => 'Must change on next login',
        'normal' => 'Normal',
        'loading' => 'Loading...',
        'no_users_found' => 'No users found',
        'failed_load_users' => 'Failed to load users',
        'error_loading_users' => 'Error loading users',
        'grant_admin_role' => 'Grant admin role',
        'admin_role' => 'Admin role',
        'temporary_password_help' => 'Temporary Password (Admin gives this to user)',
        'temporary_secondary_password' => 'Temporary Secondary Password',
        'save' => 'Save',
        'create' => 'Create',
        'updated' => 'Updated',
        'created' => 'Created',
        'password_reset' => 'Password reset',
        'failed_update_role' => 'Failed to update role',
        'failed_create_user' => 'Failed to create user',
        'failed_update_user' => 'Failed to update user',
        'failed_reset_password' => 'Failed to reset password',
        'secondary_auth_title' => 'Secondary Authentication',
        'enter_password' => 'Enter password',
        'two_factor_protect' => 'Two-factor protection for your account.',
        'enter_secondary_password' => 'Enter secondary password',
        'must_verify_secondary' => 'You must complete secondary verification before accessing the system.',
        'press_any_key_start' => 'Press button or any key to start',
        'back' => 'Back',
        'confirm' => 'Confirm',
        'setup_secondary_password' => 'Create secondary password',
        'setup_secondary_step_1' => 'First login: create a 4-digit secondary password from numbers 1 to 8.',
        'setup_secondary_step_2' => 'Re-enter 4 digits to confirm.',
        'verify_secondary_hint' => 'Select the 4 digits of your secondary password to continue.',
        'secondary_mismatch' => 'The two entries do not match. Please create again.',
        'secondary_auth_failed' => 'Secondary verification failed.'
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
        'manage_users' => 'Quản lý người dùng',
        'user_permission_management' => 'Quản lý phân quyền người dùng',
        'create_user' => 'Tạo người dùng',
        'create_new_user' => 'Tạo người dùng mới',
        'edit_user' => 'Chỉnh sửa người dùng',
        'reset_user_password' => 'Đặt lại mật khẩu người dùng',
        'new_temporary_password' => 'Mật khẩu tạm mới',
        'new_secondary_password_optional' => 'Mật khẩu cấp 2 mới (tùy chọn)',
        'secondary_password_rule' => 'Mật khẩu cấp 2 phải đúng 4 số, dùng các số từ 1 đến 8.',
        'after_reset_must_change' => 'Sau khi reset, người dùng bắt buộc đổi mật khẩu ở lần đăng nhập kế tiếp.',
        'role' => 'Vai trò',
        'action' => 'Thao tác',
        'name' => 'Tên',
        'phone' => 'Số điện thoại',
        'created_at' => 'Ngày tạo',
        'password_policy' => 'Chính sách mật khẩu',
        'secondary_password' => 'Mật khẩu cấp 2',
        'configured' => 'Đã cấu hình',
        'not_set' => 'Chưa đặt',
        'must_change_next_login' => 'Phải đổi ở lần đăng nhập kế tiếp',
        'normal' => 'Bình thường',
        'loading' => 'Đang tải...',
        'no_users_found' => 'Không tìm thấy người dùng',
        'failed_load_users' => 'Không tải được danh sách người dùng',
        'error_loading_users' => 'Lỗi tải danh sách người dùng',
        'grant_admin_role' => 'Cấp quyền quản trị',
        'admin_role' => 'Quyền quản trị',
        'temporary_password_help' => 'Mật khẩu tạm (quản trị viên cấp cho người dùng)',
        'temporary_secondary_password' => 'Mật khẩu cấp 2 tạm',
        'save' => 'Lưu',
        'create' => 'Tạo',
        'updated' => 'Đã cập nhật',
        'created' => 'Đã tạo',
        'password_reset' => 'Đã đặt lại mật khẩu',
        'failed_update_role' => 'Cập nhật vai trò thất bại',
        'failed_create_user' => 'Tạo người dùng thất bại',
        'failed_update_user' => 'Cập nhật người dùng thất bại',
        'failed_reset_password' => 'Đặt lại mật khẩu thất bại',
        'secondary_auth_title' => 'Xác thực cấp 2',
        'enter_password' => 'Nhập mật khẩu',
        'two_factor_protect' => 'Bảo mật 2 lớp cho tài khoản của bạn.',
        'enter_secondary_password' => 'Nhập mật khẩu cấp 2',
        'must_verify_secondary' => 'Bạn phải xác thực cấp 2 trước khi truy cập hệ thống.',
        'press_any_key_start' => 'Bấm nút hoặc phím bất kỳ để bắt đầu',
        'back' => 'Lùi',
        'confirm' => 'Xác nhận',
        'setup_secondary_password' => 'Tạo mật khẩu cấp 2',
        'setup_secondary_step_1' => 'Lần đầu đăng nhập: hãy tạo mật khẩu cấp 2 gồm 4 số từ 1 đến 8.',
        'setup_secondary_step_2' => 'Nhập lại 4 số để xác nhận.',
        'verify_secondary_hint' => 'Hãy chọn 4 số của mật khẩu cấp 2 để vào hệ thống.',
        'secondary_mismatch' => 'Hai lần nhập không khớp, vui lòng tạo lại.',
        'secondary_auth_failed' => 'Xác thực cấp 2 thất bại.'
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
