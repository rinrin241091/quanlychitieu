<?php include_once __DIR__ . '/i18n.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.css" rel="stylesheet"/>
<link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/auth.js"></script>
</head>
<body style="background:#f5f7fb;">
<div class="container" style="max-width:520px; margin-top:80px;">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h4 class="text-center mb-3">Doi mat khau lan dau</h4>
      <p class="text-muted text-center mb-4">Ban can doi mat khau moi de tiep tuc su dung he thong.</p>

      <p id="error-msg" class="text-danger text-center"></p>
      <p id="success-msg" class="text-success text-center"></p>

      <form id="firstChangeForm">
        <div class="mb-3">
          <label class="form-label">Mat khau moi</label>
          <input type="password" id="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Nhap lai mat khau moi</label>
          <input type="password" id="confirm_password" class="form-control" required>
        </div>

        <button type="submit" id="submitBtn" class="btn btn-primary w-100">
          <span id="submitText">Cap nhat mat khau</span>
          <span id="submitSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
        </button>
      </form>

      <button id="logoutBtn" class="btn btn-link w-100 mt-2">Dang xuat</button>
    </div>
  </div>
</div>

<script>
(function() {
    var userData = null;
    try {
        userData = JSON.parse(localStorage.getItem('user_data') || 'null');
    } catch (e) {
        userData = null;
    }

    if (!userData) {
        window.location.href = 'index.php';
        return;
    }

    if (parseInt(userData.must_change_password || 0, 10) !== 1) {
        window.location.href = 'home.php';
        return;
    }
})();

document.getElementById('logoutBtn').addEventListener('click', function() {
    if (typeof AuthManager !== 'undefined') {
        AuthManager.logout();
    } else {
        localStorage.removeItem('access_token');
        localStorage.removeItem('user_data');
        window.location.href = 'index.php';
    }
});

document.getElementById('firstChangeForm').addEventListener('submit', function(e) {
    e.preventDefault();

    var newPassword = document.getElementById('new_password').value;
    var confirmPassword = document.getElementById('confirm_password').value;

    var errorMsg = document.getElementById('error-msg');
    var successMsg = document.getElementById('success-msg');
    var submitBtn = document.getElementById('submitBtn');
    var submitText = document.getElementById('submitText');
    var submitSpinner = document.getElementById('submitSpinner');

    errorMsg.textContent = '';
    successMsg.textContent = '';

    submitBtn.disabled = true;
    submitText.style.display = 'none';
    submitSpinner.style.display = 'inline-block';

    $.ajax({
        url: 'api/change-password-first-login.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            new_password: newPassword,
            confirm_password: confirmPassword
        }),
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                var userData = null;
                try {
                    userData = JSON.parse(localStorage.getItem('user_data') || '{}');
                } catch (e) {
                    userData = {};
                }
                userData.must_change_password = 0;
                localStorage.setItem('user_data', JSON.stringify(userData));

                successMsg.textContent = response.message || 'Updated';
                setTimeout(function() {
                    window.location.href = 'home.php';
                }, 700);
            } else {
                errorMsg.textContent = response.message || 'Failed to change password';
            }
        },
        error: function(xhr) {
            var msg = 'Failed to change password';
            try {
                var data = JSON.parse(xhr.responseText);
                if (data && data.message) msg = data.message;
            } catch (e) {}
            errorMsg.textContent = msg;
        },
        complete: function() {
            submitBtn.disabled = false;
            submitText.style.display = 'inline';
            submitSpinner.style.display = 'none';
        }
    });
});
</script>
</body>
</html>
