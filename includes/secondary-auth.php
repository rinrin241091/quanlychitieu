<?php include_once __DIR__ . '/i18n.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo t('brand'); ?> - <?php echo t('secondary_auth_title'); ?></title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.css" rel="stylesheet"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"></script>
<script src="js/auth.js"></script>
</head>
<body>
<div style="position:fixed; top:12px; right:16px; z-index:9999; font-weight:600;">
    <a href="<?php echo htmlspecialchars(switch_lang_url('vi'), ENT_QUOTES, 'UTF-8'); ?>">VI</a> |
    <a href="<?php echo htmlspecialchars(switch_lang_url('en'), ENT_QUOTES, 'UTF-8'); ?>">EN</a>
</div>

<section class="secondary-auth-screen">
    <div class="secondary-auth-shell">
        <div class="secondary-left-panel">
            <h6 class="mb-3"><?php echo t('enter_password'); ?></h6>
            <div class="shield-wrap">
                <i class="fa-solid fa-shield-halved"></i>
                <i class="fa-solid fa-lock lock-icon"></i>
            </div>
            <p class="text-muted small mb-0"><?php echo t('two_factor_protect'); ?></p>
        </div>

        <div class="secondary-right-panel" id="secondaryAuthPanel" tabindex="0" aria-live="polite">
            <h3 id="secondaryTitle" class="fw-bold mb-2"><?php echo t('enter_secondary_password'); ?></h3>
            <p id="secondaryDesc" class="text-muted mb-3"><?php echo t('must_verify_secondary'); ?></p>
            <p id="secondaryError" class="text-danger mb-3"></p>

            <button type="button" id="secondaryStartBtn" class="btn btn-outline-primary start-btn"><?php echo t('press_any_key_start'); ?></button>

            <div id="secondaryPad" class="d-none mt-2">
                <div id="secondaryDigits" class="secondary-digits mb-3"></div>
                <div id="secondaryKeypad" class="secondary-keypad mb-3"></div>
                <div class="d-flex gap-2">
                    <button type="button" id="secondaryBackBtn" class="btn btn-outline-secondary flex-fill"><?php echo t('back'); ?></button>
                    <button type="button" id="secondaryClearBtn" class="btn btn-light flex-fill"><?php echo t('delete'); ?></button>
                    <button type="button" id="secondarySubmitBtn" class="btn btn-primary flex-fill" disabled><?php echo t('confirm'); ?></button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function() {
    var REQUIRED_LENGTH = 4;
    var DIGIT_SOURCE = ['1', '2', '3', '4', '5', '6', '7', '8'];
    var I18N = <?php echo json_encode([
        'setup_secondary_password' => t('setup_secondary_password'),
        'setup_secondary_step_1' => t('setup_secondary_step_1'),
        'setup_secondary_step_2' => t('setup_secondary_step_2'),
        'enter_secondary_password' => t('enter_secondary_password'),
        'verify_secondary_hint' => t('verify_secondary_hint'),
        'secondary_mismatch' => t('secondary_mismatch'),
        'secondary_auth_failed' => t('secondary_auth_failed')
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    var pendingRaw = sessionStorage.getItem('secondary_auth_pending');
    if (!pendingRaw) {
        window.location.href = 'index.php';
        return;
    }

    var pending = null;
    try {
        pending = JSON.parse(pendingRaw);
    } catch (e) {
        sessionStorage.removeItem('secondary_auth_pending');
        window.location.href = 'index.php';
        return;
    }

    var mode = pending && pending.status === 'secondary_setup_required' ? 'setup' : 'verify';
    var challengeToken = pending ? String(pending.challenge_token || '') : '';

    if (!challengeToken) {
        sessionStorage.removeItem('secondary_auth_pending');
        window.location.href = 'index.php';
        return;
    }

    var enteredPin = '';
    var setupFirstPin = '';
    var showingPad = false;

    var panel = document.getElementById('secondaryAuthPanel');
    var titleEl = document.getElementById('secondaryTitle');
    var descEl = document.getElementById('secondaryDesc');
    var errorEl = document.getElementById('secondaryError');
    var startBtn = document.getElementById('secondaryStartBtn');
    var padEl = document.getElementById('secondaryPad');
    var digitsEl = document.getElementById('secondaryDigits');
    var keypadEl = document.getElementById('secondaryKeypad');
    var backBtn = document.getElementById('secondaryBackBtn');
    var clearBtn = document.getElementById('secondaryClearBtn');
    var submitBtn = document.getElementById('secondarySubmitBtn');

    function shuffleDigits() {
        var arr = DIGIT_SOURCE.slice();
        for (var i = arr.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var tmp = arr[i];
            arr[i] = arr[j];
            arr[j] = tmp;
        }
        return arr;
    }

    function updateHeaderText() {
        if (mode === 'setup') {
            titleEl.textContent = I18N.setup_secondary_password;
            descEl.textContent = setupFirstPin ? I18N.setup_secondary_step_2 : I18N.setup_secondary_step_1;
            return;
        }

        titleEl.textContent = I18N.enter_secondary_password;
        descEl.textContent = I18N.verify_secondary_hint;
    }

    function renderEnteredDigits() {
        var html = '';
        for (var i = 0; i < REQUIRED_LENGTH; i++) {
            var filled = i < enteredPin.length;
            html += '<span class="secondary-digit-box' + (filled ? ' filled' : '') + '">' + (filled ? '&#9679;' : '') + '</span>';
        }
        digitsEl.innerHTML = html;
        submitBtn.disabled = enteredPin.length !== REQUIRED_LENGTH;
    }

    function renderKeypad() {
        var digits = shuffleDigits();
        var html = '';
        for (var i = 0; i < digits.length; i++) {
            html += '<button type="button" class="btn btn-light secondary-key" data-digit="' + digits[i] + '">' + digits[i] + '</button>';
        }
        keypadEl.innerHTML = html;

        var keyButtons = keypadEl.querySelectorAll('.secondary-key');
        keyButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                appendDigit(btn.getAttribute('data-digit'));
            });
        });
    }

    function appendDigit(digit) {
        if (enteredPin.length >= REQUIRED_LENGTH) {
            return;
        }

        enteredPin += digit;
        renderEnteredDigits();
        renderKeypad();
    }

    function removeLastDigit() {
        if (enteredPin.length === 0) {
            return;
        }

        enteredPin = enteredPin.slice(0, -1);
        renderEnteredDigits();
        renderKeypad();
    }

    function clearPin() {
        enteredPin = '';
        renderEnteredDigits();
        renderKeypad();
    }

    function completeLogin(response) {
        sessionStorage.removeItem('secondary_auth_pending');
        localStorage.setItem('access_token', response.access_token);
        localStorage.setItem('user_data', JSON.stringify(response.user));

        if (response.user && parseInt(response.user.must_change_password || 0, 10) === 1) {
            window.location.href = 'force-change-password.php';
        } else {
            window.location.href = 'home.php';
        }
    }

    function showPad() {
        showingPad = true;
        startBtn.classList.add('d-none');
        padEl.classList.remove('d-none');
        updateHeaderText();
        clearPin();
        panel.focus();
    }

    function onAnyKeyStart(event) {
        if (showingPad) {
            return;
        }

        var target = event.target;
        if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA')) {
            return;
        }

        event.preventDefault();
        showPad();
    }

    function goBackLogin() {
        sessionStorage.removeItem('secondary_auth_pending');
        window.location.href = 'index.php';
    }

    function submitSecondary() {
        if (enteredPin.length !== REQUIRED_LENGTH) {
            return;
        }

        errorEl.textContent = '';
        submitBtn.disabled = true;

        if (mode === 'setup' && !setupFirstPin) {
            setupFirstPin = enteredPin;
            enteredPin = '';
            updateHeaderText();
            renderEnteredDigits();
            renderKeypad();
            return;
        }

        if (mode === 'setup' && setupFirstPin && setupFirstPin !== enteredPin) {
            errorEl.textContent = I18N.secondary_mismatch;
            setupFirstPin = '';
            enteredPin = '';
            updateHeaderText();
            renderEnteredDigits();
            renderKeypad();
            return;
        }

        var payload = {
            action: mode === 'setup' ? 'setup' : 'verify',
            challenge_token: challengeToken,
            secondary_password: mode === 'setup' ? setupFirstPin : enteredPin
        };

        if (mode === 'setup') {
            payload.confirm_secondary_password = enteredPin;
        }

        $.ajax({
            url: 'api/secondary-auth.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    completeLogin(response);
                } else {
                    errorEl.textContent = response.message || I18N.secondary_auth_failed;
                    submitBtn.disabled = false;
                }
            },
            error: function(xhr) {
                var message = I18N.secondary_auth_failed;
                try {
                    var response = JSON.parse(xhr.responseText);
                    message = response.message || message;
                } catch (e) {
                    // no-op
                }

                errorEl.textContent = message;
                submitBtn.disabled = false;

                if (mode === 'setup') {
                    setupFirstPin = '';
                    clearPin();
                    updateHeaderText();
                }
            }
        });
    }

    backBtn.addEventListener('click', goBackLogin);
    clearBtn.addEventListener('click', clearPin);
    submitBtn.addEventListener('click', submitSecondary);
    startBtn.addEventListener('click', showPad);

    panel.addEventListener('keydown', function(event) {
        if (!showingPad) {
            return;
        }

        if (event.key >= '1' && event.key <= '8') {
            event.preventDefault();
            appendDigit(event.key);
            return;
        }

        if (event.key === 'Backspace') {
            event.preventDefault();
            removeLastDigit();
            return;
        }

        if (event.key === 'Enter' && enteredPin.length === REQUIRED_LENGTH) {
            event.preventDefault();
            submitSecondary();
        }
    });

    document.addEventListener('keydown', onAnyKeyStart);

    updateHeaderText();
    panel.focus();
})();
</script>

<style>
body {
    margin: 0;
    min-height: 100vh;
    font-family: 'Roboto', sans-serif;
    background: radial-gradient(circle at 18% 20%, #17284f 0%, #0f1d3a 28%, #0b1325 58%, #060b16 100%);
    color: #1f2a37;
}

.secondary-auth-screen {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
}

.secondary-auth-shell {
    width: min(980px, 100%);
    background: #f7f8fb;
    border: 1px solid #d9e0ea;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.28);
    display: grid;
    grid-template-columns: 290px 1fr;
}

.secondary-left-panel {
    padding: 28px 22px;
    border-right: 1px solid #d9e0ea;
    background: linear-gradient(180deg, #f2f5fb 0%, #edf1f8 100%);
}

.shield-wrap {
    width: 120px;
    height: 120px;
    margin-bottom: 14px;
    border-radius: 24px;
    background: #d7dde9;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    color: #7f8ea4;
    font-size: 56px;
}

.lock-icon {
    position: absolute;
    font-size: 30px;
    color: #f7f8fb;
    top: 46px;
}

.secondary-right-panel {
    padding: 32px 28px;
    outline: none;
}

.start-btn {
    min-height: 46px;
    font-weight: 600;
}

.secondary-digits {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
}

.secondary-digit-box {
    height: 52px;
    border-radius: 8px;
    border: 1px solid #cad5e3;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #0d6efd;
}

.secondary-digit-box.filled {
    border-color: #0d6efd;
    box-shadow: inset 0 0 0 1px #0d6efd;
}

.secondary-keypad {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
}

.secondary-key {
    min-height: 52px;
    font-size: 22px;
    font-weight: 700;
    border: 1px solid #d8deea;
    background: #fff;
}

.secondary-key:hover {
    background: #f3f7ff;
}

@media (max-width: 900px) {
    .secondary-auth-shell {
        grid-template-columns: 1fr;
    }

    .secondary-left-panel {
        border-right: none;
        border-bottom: 1px solid #d9e0ea;
    }
}

@media (max-width: 520px) {
    .secondary-auth-screen {
        padding: 10px;
    }

    .secondary-right-panel,
    .secondary-left-panel {
        padding: 16px;
    }

    .secondary-key,
    .secondary-digit-box {
        min-height: 44px;
        font-size: 18px;
    }
}
</style>
</body>
</html>
