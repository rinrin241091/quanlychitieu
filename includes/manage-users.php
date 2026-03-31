<?php
session_start();
error_reporting(0);
include('database.php');
include_once('i18n.php');
include_once('auth_helper.php');

if (empty($_SESSION['detsuid'])) {
    header('location:index.php');
    exit;
}

$currentUserId = (int)$_SESSION['detsuid'];
if (!userHasAdminPrivilege($db, $currentUserId)) {
    header('location:home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8'); ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('manage_users'); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/auth.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <style>
        .table td, .table th { vertical-align: middle; }
        .loading-spinner { text-align: center; padding: 30px; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="logo-details"><i class='bx bx-album'></i><span class="logo_name">Expenditure</span></div>
    <ul class="nav-links">
        <li><a href="<?php echo htmlspecialchars(with_lang('home.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class='bx bx-grid-alt'></i><span class="links_name">Dashboard</span></a></li>
        <li><a href="<?php echo htmlspecialchars(with_lang('add-expenses.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class='bx bx-box'></i><span class="links_name">Expenses</span></a></li>
        <li><a href="<?php echo htmlspecialchars(with_lang('add-income.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class='bx bx-box'></i><span class="links_name">Income</span></a></li>
        <li><a href="<?php echo htmlspecialchars(with_lang('manage-transaction.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class='bx bx-list-ul'></i><span class="links_name">Manage List</span></a></li>
        <li><a href="<?php echo htmlspecialchars(with_lang('analytics.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class='bx bx-pie-chart-alt-2'></i><span class="links_name">Analytics</span></a></li>
        <li><a href="<?php echo htmlspecialchars(with_lang('report.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bx bx-file"></i><span class="links_name">Report</span></a></li>
        <li class="admin-nav-item"><a href="<?php echo htmlspecialchars(with_lang('manage-users.php'), ENT_QUOTES, 'UTF-8'); ?>" class="active"><i class='bx bx-shield-quarter'></i><span class="links_name">Manage Users</span></a></li>
        <li><a href="<?php echo htmlspecialchars(with_lang('user_profile.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class='bx bx-cog'></i><span class="links_name">Setting</span></a></li>
        <li class="log_out"><a href="<?php echo htmlspecialchars(with_lang('logout.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class='bx bx-log-out'></i><span class="links_name">Log out</span></a></li>
    </ul>
</div>

<section class="home-section">
    <nav>
        <div class="sidebar-button"><i class='bx bx-menu sidebarBtn'></i><span class="dashboard"><?php echo t('manage_users'); ?></span></div>
        <div style="margin-left:auto; margin-right:12px; font-weight:600;">
            <a href="<?php echo htmlspecialchars(switch_lang_url('vi'), ENT_QUOTES, 'UTF-8'); ?>">VI</a> |
            <a href="<?php echo htmlspecialchars(switch_lang_url('en'), ENT_QUOTES, 'UTF-8'); ?>">EN</a>
        </div>
        <div class="profile-details">
            <img src="images/maex.png" alt="">
            <span class="admin_name" id="user-name">Admin</span>
            <i class='bx bx-chevron-down' id='profile-options-toggle'></i>
            <ul class="profile-options" id='profile-options'>
                <li><a href="<?php echo htmlspecialchars(with_lang('user_profile.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-user-circle"></i> User Profile</a></li>
                <li><a href="<?php echo htmlspecialchars(with_lang('logout.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="home-content">
        <div class="overview-boxes">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0"><?php echo t('user_permission_management'); ?></h4>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-user-modal"><i class="fas fa-user-plus"></i> <?php echo t('create_user'); ?></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo t('name'); ?></th>
                                        <th>Email</th>
                                        <th><?php echo t('phone'); ?></th>
                                        <th><?php echo t('created_at'); ?></th>
                                        <th><?php echo t('password_policy'); ?></th>
                                        <th><?php echo t('secondary_password'); ?></th>
                                        <th><?php echo t('role'); ?></th>
                                        <th><?php echo t('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="users-tbody">
                                    <tr><td colspan="9" class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> <?php echo t('loading'); ?></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="create-user-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="create-user-form">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo t('create_new_user'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo t('name'); ?></label>
                        <input type="text" class="form-control" id="create-name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="create-email" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo t('phone'); ?></label>
                        <input type="text" class="form-control" id="create-phone" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo t('temporary_password_help'); ?></label>
                        <input type="text" class="form-control" id="create-password" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo t('temporary_secondary_password'); ?></label>
                        <input type="text" class="form-control" id="create-secondary-password" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="create-is-admin">
                        <label class="form-check-label" for="create-is-admin"><?php echo t('grant_admin_role'); ?></label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo t('create'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-user-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="edit-user-form">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo t('edit_user'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-user-id">
                    <div class="form-group">
                        <label><?php echo t('name'); ?></label>
                        <input type="text" class="form-control" id="edit-name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="edit-email" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo t('phone'); ?></label>
                        <input type="text" class="form-control" id="edit-phone" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="edit-is-admin">
                        <label class="form-check-label" for="edit-is-admin"><?php echo t('admin_role'); ?></label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo t('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="reset-password-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="reset-password-form">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo t('reset_user_password'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="reset-user-id">
                    <div class="form-group">
                        <label><?php echo t('new_temporary_password'); ?></label>
                        <input type="text" class="form-control" id="reset-new-password" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo t('new_secondary_password_optional'); ?></label>
                        <input type="text" class="form-control" id="reset-new-secondary-password">
                        <small class="form-text text-muted"><?php echo t('secondary_password_rule'); ?></small>
                    </div>
                    <div class="alert alert-warning mb-0"><?php echo t('after_reset_must_change'); ?></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
                    <button type="submit" class="btn btn-danger"><?php echo t('reset_user_password'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var currentUserId = <?php echo (int)$currentUserId; ?>;
var cachedUsers = [];
var I18N = <?php echo json_encode([
    'failed_load_users' => t('failed_load_users'),
    'error_loading_users' => t('error_loading_users'),
    'no_users_found' => t('no_users_found'),
    'admin' => t('admin'),
    'user' => t('user'),
    'must_change_next_login' => t('must_change_next_login'),
    'normal' => t('normal'),
    'configured' => t('configured'),
    'not_set' => t('not_set'),
    'updated' => t('updated'),
    'created' => t('created'),
    'password_reset' => t('password_reset'),
    'failed_update_role' => t('failed_update_role'),
    'failed_create_user' => t('failed_create_user'),
    'failed_update_user' => t('failed_update_user'),
    'failed_reset_password' => t('failed_reset_password')
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

function loadUsers() {
    $.ajax({
        url: 'api/users.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                renderUsers(response.data.users || []);
            } else {
                $('#users-tbody').html('<tr><td colspan="9" class="text-center text-danger">' + (response.message || I18N.failed_load_users) + '</td></tr>');
            }
        },
        error: function(xhr) {
            if (xhr.status === 403) {
                window.location.href = 'home.php';
                return;
            }
            $('#users-tbody').html('<tr><td colspan="9" class="text-center text-danger">' + I18N.error_loading_users + '</td></tr>');
        }
    });
}

function renderUsers(users) {
    cachedUsers = users;

    if (!users.length) {
        $('#users-tbody').html('<tr><td colspan="9" class="text-center">' + I18N.no_users_found + '</td></tr>');
        return;
    }

    var html = '';
    users.forEach(function(user, idx) {
        var roleBadge = user.is_admin === 1
            ? '<span class="badge badge-success">' + I18N.admin + '</span>'
            : '<span class="badge badge-secondary">' + I18N.user + '</span>';

        var selectedAdmin = user.is_admin === 1 ? 'selected' : '';
        var selectedUser = user.is_admin === 0 ? 'selected' : '';
        var disabled = user.id === currentUserId ? 'disabled' : '';
        var passwordPolicy = user.must_change_password === 1
            ? '<span class="badge badge-warning">' + I18N.must_change_next_login + '</span>'
            : '<span class="badge badge-info">' + I18N.normal + '</span>';
        var secondPasswordState = parseInt(user.has_secondary_password || 0, 10) === 1
            ? '<span class="badge badge-success">' + I18N.configured + '</span>'
            : '<span class="badge badge-danger">' + I18N.not_set + '</span>';

        html += '<tr>' +
            '<td>' + (idx + 1) + '</td>' +
            '<td>' + (user.name || '-') + '</td>' +
            '<td>' + (user.email || '-') + '</td>' +
            '<td>' + (user.phone || '-') + '</td>' +
            '<td>' + (user.created_at || '-') + '</td>' +
            '<td>' + passwordPolicy + '</td>' +
            '<td>' + secondPasswordState + '</td>' +
            '<td>' + roleBadge + '</td>' +
            '<td>' +
                '<select class="form-control form-control-sm role-select" data-user-id="' + user.id + '" ' + disabled + '>' +
                    '<option value="1" ' + selectedAdmin + '>' + I18N.admin + '</option>' +
                    '<option value="0" ' + selectedUser + '>' + I18N.user + '</option>' +
                '</select> ' +
                '<button class="btn btn-sm btn-outline-primary mt-1 edit-user-btn" data-user-id="' + user.id + '"><i class="fas fa-edit"></i></button> ' +
                '<button class="btn btn-sm btn-outline-danger mt-1 reset-pass-btn" data-user-id="' + user.id + '"><i class="fas fa-key"></i></button>' +
            '</td>' +
        '</tr>';
    });

    $('#users-tbody').html(html);
}

$(document).ready(function() {
    var userData = localStorage.getItem('user_data');
    if (userData) {
        var user = JSON.parse(userData);
        $('#user-name').text(user.name || 'Admin');
    }

    loadUsers();

    $(document).on('change', '.role-select', function() {
        var userId = parseInt($(this).data('user-id'), 10);
        var role = parseInt($(this).val(), 10);

        $.ajax({
            url: 'api/users.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ action: 'set_role', user_id: userId, is_admin: role }),
            dataType: 'json',
            success: function(response) {
                alert(response.message || I18N.updated);
                loadUsers();
            },
            error: function(xhr) {
                var msg = I18N.failed_update_role;
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data && data.message) msg = data.message;
                } catch (e) {}
                alert(msg);
                loadUsers();
            }
        });
    });

    $('#create-user-form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'api/users.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                action: 'create',
                name: $('#create-name').val(),
                email: $('#create-email').val(),
                phone: $('#create-phone').val(),
                password: $('#create-password').val(),
                secondary_password: $('#create-secondary-password').val(),
                is_admin: $('#create-is-admin').is(':checked') ? 1 : 0
            }),
            dataType: 'json',
            success: function(response) {
                alert(response.message || I18N.created);
                $('#create-user-modal').modal('hide');
                $('#create-user-form')[0].reset();
                loadUsers();
            },
            error: function(xhr) {
                var msg = I18N.failed_create_user;
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data && data.message) msg = data.message;
                } catch (e) {}
                alert(msg);
            }
        });
    });

    $(document).on('click', '.edit-user-btn', function() {
        var userId = parseInt($(this).data('user-id'), 10);
        var user = cachedUsers.find(function(u) { return parseInt(u.id, 10) === userId; });
        if (!user) return;

        $('#edit-user-id').val(user.id);
        $('#edit-name').val(user.name || '');
        $('#edit-email').val(user.email || '');
        $('#edit-phone').val(user.phone || '');
        $('#edit-is-admin').prop('checked', parseInt(user.is_admin, 10) === 1);
        $('#edit-is-admin').prop('disabled', userId === currentUserId);
        $('#edit-user-modal').modal('show');
    });

    $('#edit-user-form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'api/users.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                action: 'update',
                user_id: parseInt($('#edit-user-id').val(), 10),
                name: $('#edit-name').val(),
                email: $('#edit-email').val(),
                phone: $('#edit-phone').val(),
                is_admin: $('#edit-is-admin').is(':checked') ? 1 : 0
            }),
            dataType: 'json',
            success: function(response) {
                alert(response.message || I18N.updated);
                $('#edit-user-modal').modal('hide');
                loadUsers();
            },
            error: function(xhr) {
                var msg = I18N.failed_update_user;
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data && data.message) msg = data.message;
                } catch (e) {}
                alert(msg);
            }
        });
    });

    $(document).on('click', '.reset-pass-btn', function() {
        var userId = parseInt($(this).data('user-id'), 10);
        $('#reset-user-id').val(userId);
        $('#reset-new-password').val('');
        $('#reset-new-secondary-password').val('');
        $('#reset-password-modal').modal('show');
    });

    $('#reset-password-form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'api/users.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                action: 'reset_password',
                user_id: parseInt($('#reset-user-id').val(), 10),
                new_password: $('#reset-new-password').val(),
                new_secondary_password: $('#reset-new-secondary-password').val()
            }),
            dataType: 'json',
            success: function(response) {
                alert(response.message || I18N.password_reset);
                $('#reset-password-modal').modal('hide');
                loadUsers();
            },
            error: function(xhr) {
                var msg = I18N.failed_reset_password;
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data && data.message) msg = data.message;
                } catch (e) {}
                alert(msg);
            }
        });
    });

    const toggleButton = document.getElementById('profile-options-toggle');
    const profileOptions = document.getElementById('profile-options');
    if (toggleButton && profileOptions) {
        toggleButton.addEventListener('click', function() {
            profileOptions.classList.toggle('show');
        });
    }
});

let sidebar = document.querySelector('.sidebar');
let sidebarBtn = document.querySelector('.sidebarBtn');
if (sidebarBtn) {
    sidebarBtn.onclick = function() {
        sidebar.classList.toggle('active');
        if (sidebar.classList.contains('active')) {
            sidebarBtn.classList.replace('bx-menu', 'bx-menu-alt-right');
        } else {
            sidebarBtn.classList.replace('bx-menu-alt-right', 'bx-menu');
        }
    };
}
</script>
</body>
</html>
