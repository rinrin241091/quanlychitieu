var AuthManager = (function() {
    var TOKEN_KEY = 'access_token';
    var USER_KEY = 'user_data';
    var DISPLAY_PREFS_KEY = 'display_prefs';
    var systemThemeMedia = null;
    
    function getToken() {
        return localStorage.getItem(TOKEN_KEY);
    }
    
    function setToken(token) {
        localStorage.setItem(TOKEN_KEY, token);
    }
    
    function getUser() {
        var userData = localStorage.getItem(USER_KEY);
        return userData ? JSON.parse(userData) : null;
    }
    
    function setUser(user) {
        localStorage.setItem(USER_KEY, JSON.stringify(user));
    }
    
    function clearAuth() {
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(USER_KEY);
    }

    function normalizeDisplayPreferences(prefs) {
        var normalized = prefs && typeof prefs === 'object' ? prefs : {};
        var theme = String(normalized.theme || 'off').toLowerCase();
        if (theme !== 'off' && theme !== 'on' && theme !== 'auto') {
            theme = 'off';
        }

        return {
            theme: theme,
            compact: !!normalized.compact
        };
    }

    function getDisplayPreferences() {
        var raw = localStorage.getItem(DISPLAY_PREFS_KEY);
        if (!raw) {
            return normalizeDisplayPreferences(null);
        }

        try {
            return normalizeDisplayPreferences(JSON.parse(raw));
        } catch (e) {
            return normalizeDisplayPreferences(null);
        }
    }

    function setDisplayPreferences(prefs) {
        var normalized = normalizeDisplayPreferences(prefs);
        localStorage.setItem(DISPLAY_PREFS_KEY, JSON.stringify(normalized));
        return normalized;
    }

    function resolveEffectiveTheme(themeSetting) {
        if (themeSetting === 'on') {
            return 'dark';
        }
        if (themeSetting === 'auto') {
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            return prefersDark ? 'dark' : 'light';
        }
        return 'light';
    }

    function bindSystemThemeWatcher() {
        if (!window.matchMedia) {
            return;
        }

        if (!systemThemeMedia) {
            systemThemeMedia = window.matchMedia('(prefers-color-scheme: dark)');
        }

        if (systemThemeMedia.__bound) {
            return;
        }

        var onSystemThemeChanged = function() {
            var prefs = getDisplayPreferences();
            if (prefs.theme === 'auto') {
                applyDisplayPreferences(prefs);
            }
        };

        if (typeof systemThemeMedia.addEventListener === 'function') {
            systemThemeMedia.addEventListener('change', onSystemThemeChanged);
        } else if (typeof systemThemeMedia.addListener === 'function') {
            systemThemeMedia.addListener(onSystemThemeChanged);
        }

        systemThemeMedia.__bound = true;
    }

    function applyDisplayPreferences(prefs) {
        var normalized = normalizeDisplayPreferences(prefs || getDisplayPreferences());
        var effectiveTheme = resolveEffectiveTheme(normalized.theme);

        if (document && document.body) {
            document.body.classList.toggle('theme-dark', effectiveTheme === 'dark');
            document.body.classList.toggle('compact-ui', normalized.compact);
            document.body.setAttribute('data-theme-setting', normalized.theme);
        }

        bindSystemThemeWatcher();
        return normalized;
    }
    
    function isAuthenticated() {
        return !!getToken();
    }
    
    function logout() {
        clearAuth();
        window.location.href = 'index.php';
    }
    
    function setupAjaxDefaults() {
        if (typeof $ !== 'undefined' && $.ajaxSetup) {
            $.ajaxSetup({
                beforeSend: function(xhr) {
                    var token = getToken();
                    if (token) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 401) {
                        clearAuth();
                        alert('Session expired. Please login again.');
                        window.location.href = 'index.php';
                    }
                }
            });
        }
    }
    
    function login(email, password, successCallback, errorCallback) {
        $.ajax({
            url: 'api/login.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ email: email, password: password }),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    setToken(response.access_token);
                    setUser(response.user);
                }
                if (successCallback) successCallback(response);
            },
            error: function(xhr) {
                if (errorCallback) {
                    var response = xhr.responseJSON || { message: 'Login failed' };
                    errorCallback(response);
                }
            }
        });
    }
    
    function apiCall(url, method, data, successCallback, errorCallback) {
        var ajaxConfig = {
            url: url,
            type: method || 'GET',
            dataType: 'json',
            headers: {
                'Authorization': 'Bearer ' + getToken()
            },
            success: function(response) {
                if (successCallback) successCallback(response);
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    clearAuth();
                    window.location.href = 'index.php';
                    return;
                }
                if (errorCallback) {
                    var response = xhr.responseJSON || { message: 'Request failed' };
                    errorCallback(response);
                }
            }
        };
        
        if (data) {
            if (method === 'GET') {
                ajaxConfig.data = data;
            } else {
                ajaxConfig.contentType = 'application/json';
                ajaxConfig.data = JSON.stringify(data);
            }
        }
        
        $.ajax(ajaxConfig);
    }
    
    $(document).ready(function() {
        setupAjaxDefaults();
        applyDisplayPreferences(getDisplayPreferences());
    });
    
    return {
        getToken: getToken,
        setToken: setToken,
        getUser: getUser,
        setUser: setUser,
        clearAuth: clearAuth,
        isAuthenticated: isAuthenticated,
        logout: logout,
        login: login,
        apiCall: apiCall,
        setupAjaxDefaults: setupAjaxDefaults,
        getDisplayPreferences: getDisplayPreferences,
        setDisplayPreferences: setDisplayPreferences,
        applyDisplayPreferences: applyDisplayPreferences
    };
})();

(function() {
    var LANG_KEY = 'app_lang';
    var SUPPORTED = ['en', 'vi'];

    function getUrlLang() {
        try {
            var url = new URL(window.location.href);
            var lang = (url.searchParams.get('lang') || '').toLowerCase();
            return SUPPORTED.indexOf(lang) >= 0 ? lang : null;
        } catch (e) {
            return null;
        }
    }

    function getCookie(name) {
        var key = name + '=';
        var parts = document.cookie ? document.cookie.split(';') : [];
        for (var i = 0; i < parts.length; i++) {
            var c = parts[i].trim();
            if (c.indexOf(key) === 0) {
                return decodeURIComponent(c.substring(key.length));
            }
        }
        return null;
    }

    function resolveLang() {
        var fromUrl = getUrlLang();
        if (fromUrl) return fromUrl;

        var fromStorage = (localStorage.getItem(LANG_KEY) || '').toLowerCase();
        if (SUPPORTED.indexOf(fromStorage) >= 0) return fromStorage;

        var fromCookie = (getCookie('lang') || '').toLowerCase();
        if (SUPPORTED.indexOf(fromCookie) >= 0) return fromCookie;

        return 'vi';
    }

    function persistLang(lang) {
        localStorage.setItem(LANG_KEY, lang);
        document.cookie = 'lang=' + encodeURIComponent(lang) + '; path=/; max-age=31536000';
    }

    function ensureUrlHasLang(lang) {
        try {
            var url = new URL(window.location.href);
            if (url.searchParams.get('lang') !== lang) {
                url.searchParams.set('lang', lang);
                history.replaceState({}, '', url.toString());
            }
        } catch (e) {
            // no-op
        }
    }

    function patchInternalLinks(lang) {
        var links = document.querySelectorAll('a[href]');
        for (var i = 0; i < links.length; i++) {
            var href = links[i].getAttribute('href');
            if (!href || href[0] === '#' || href.indexOf('javascript:') === 0 || href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) {
                continue;
            }
            try {
                var abs = new URL(href, window.location.href);
                if (abs.origin !== window.location.origin) {
                    continue;
                }
                abs.searchParams.set('lang', lang);
                links[i].setAttribute('href', abs.pathname + abs.search + abs.hash);
            } catch (e) {
                // ignore malformed URLs
            }
        }
    }

    function upsertSwitcher(lang) {
        var nav = document.querySelector('nav');
        var existingGlobals = document.querySelectorAll('.lang-switcher-global');
        for (var g = 0; g < existingGlobals.length; g++) {
            existingGlobals[g].remove();
        }

        // Remove legacy VI/EN blocks inside nav so we keep a single consistent switcher.
        if (nav) {
            var legacyBlocks = nav.querySelectorAll('div,span,p');
            for (var i = 0; i < legacyBlocks.length; i++) {
                var block = legacyBlocks[i];
                var text = (block.textContent || '').replace(/\s+/g, '').toUpperCase();
                var hasViEnText = text === 'VI|EN' || text === 'VI/EN' || text === 'VI|EN|';
                var hasLangAnchors = !!(block.querySelector('a[href*="lang=vi"]') || block.querySelector('a[href*="lang=en"]'));
                if (hasViEnText || hasLangAnchors) {
                    if (!block.classList.contains('profile-details')) {
                        block.remove();
                    }
                }
            }
        }

        var host = nav || document.body;
        var el = document.createElement('div');
        el.className = 'lang-switcher-global';
        if (nav) {
            el.style.cssText = 'display:inline-flex;align-items:center;background:#ffffff;border:1px solid #d9d9d9;border-radius:10px;padding:6px 10px;font-weight:700;font-size:14px;line-height:1;margin-left:auto;margin-right:12px;';
        } else {
            el.style.cssText = 'position:fixed;top:10px;right:14px;z-index:10000;background:#ffffff;border:1px solid #d9d9d9;border-radius:10px;padding:6px 10px;font-weight:700;font-size:14px;line-height:1;';
        }

        var vi = document.createElement('a');
        vi.textContent = 'VI';
        vi.href = '#';
        vi.style.marginRight = '8px';
        vi.style.fontSize = 'inherit';
        vi.style.lineHeight = 'inherit';
        vi.style.textDecoration = 'none';
        vi.style.color = lang === 'vi' ? '#0b4db8' : '#333';

        var en = document.createElement('a');
        en.textContent = 'EN';
        en.href = '#';
        en.style.fontSize = 'inherit';
        en.style.lineHeight = 'inherit';
        en.style.textDecoration = 'none';
        en.style.color = lang === 'en' ? '#0b4db8' : '#333';

        function onSwitch(nextLang) {
            return function(evt) {
                evt.preventDefault();
                persistLang(nextLang);
                var url = new URL(window.location.href);
                url.searchParams.set('lang', nextLang);
                window.location.href = url.toString();
            };
        }

        vi.addEventListener('click', onSwitch('vi'));
        en.addEventListener('click', onSwitch('en'));

        el.appendChild(vi);
        el.appendChild(document.createTextNode('| '));
        el.appendChild(en);
        if (nav) {
            var profileBlock = nav.querySelector('.profile-details');
            if (profileBlock) {
                nav.insertBefore(el, profileBlock);
            } else {
                nav.appendChild(el);
            }
        } else {
            host.appendChild(el);
        }
    }

    function buildPhraseMap(lang) {
        var entries = [
            { en: 'Expenditure', vi: 'Chi tiêu', aliases: ['Chi tieu', 'Chi Tieu'] },
            { en: 'Dashboard', vi: 'Tổng quan', aliases: ['Tong quan'] },
            { en: 'Expenses', vi: 'Chi tiêu', aliases: ['Chi tieu'] },
            { en: 'Income', vi: 'Thu nhập', aliases: ['Thu nhap'] },
            { en: 'Manage List', vi: 'Quản lý giao dịch', aliases: ['Quan ly giao dich'] },
            { en: 'Manage Transactions', vi: 'Quản lý giao dịch', aliases: ['Quan ly giao dich'] },
            { en: 'Manage Income', vi: 'Quản lý thu nhập', aliases: ['Quan ly thu nhap'] },
            { en: 'Analytics', vi: 'Phân tích', aliases: ['Phan tich'] },
            { en: 'Report', vi: 'Báo cáo', aliases: ['Bao cao'] },
            { en: 'Generate Report', vi: 'Tạo báo cáo', aliases: ['Tao bao cao'] },
            { en: 'Manage Users', vi: 'Quản lý người dùng', aliases: ['Quan ly nguoi dung'] },
            { en: 'User Permission Management', vi: 'Quản lý phân quyền người dùng', aliases: ['Quan ly phan quyen nguoi dung'] },
            { en: 'Role', vi: 'Vai trò', aliases: ['Vai tro'] },
            { en: 'User', vi: 'Người dùng', aliases: ['Nguoi dung'] },
            { en: 'Admin', vi: 'Quản trị viên', aliases: ['Quan tri vien'] },
            { en: 'Report Results', vi: 'Kết quả báo cáo', aliases: ['Ket qua bao cao'] },
            { en: 'Report Type', vi: 'Loại báo cáo', aliases: ['Loai bao cao'] },
            { en: 'Select a report type', vi: 'Chọn loại báo cáo', aliases: ['Chon loai bao cao'] },
            { en: 'Expense Report', vi: 'Báo cáo chi tiêu', aliases: ['Bao cao chi tieu'] },
            { en: 'Income Report', vi: 'Báo cáo thu nhập', aliases: ['Bao cao thu nhap'] },
            { en: 'Total Records', vi: 'Tổng bản ghi', aliases: ['Tong ban ghi'] },
            { en: 'Total Amount', vi: 'Tổng số tiền', aliases: ['Tong so tien'] },
            { en: 'No data found for the selected criteria', vi: 'Không có dữ liệu phù hợp với tiêu chí đã chọn', aliases: ['Khong co du lieu phu hop voi tieu chi da chon'] },
            { en: 'Setting', vi: 'Cài đặt', aliases: ['Cai dat'] },
            { en: 'Settings', vi: 'Cài đặt', aliases: ['Cai dat'] },
            { en: 'Account', vi: 'Tài khoản', aliases: ['Tai khoan'] },
            { en: 'Password', vi: 'Mật khẩu', aliases: ['Mat khau'] },
            { en: 'Account Settings', vi: 'Cài đặt tài khoản', aliases: ['Cai dat tai khoan'] },
            { en: 'Password Settings', vi: 'Cài đặt mật khẩu', aliases: ['Cai dat mat khau'] },
            { en: 'Display & Accessibility', vi: 'Màn hình và trợ năng', aliases: ['Man hinh va tro nang'] },
            { en: 'Dark mode', vi: 'Chế độ tối', aliases: ['Che do toi'] },
            { en: 'Compact mode', vi: 'Chế độ thu gọn', aliases: ['Che do thu gon'] },
            { en: 'Adjust UI to reduce glare and make viewing more comfortable.', vi: 'Điều chỉnh giao diện để giảm độ chói và dễ nhìn hơn.', aliases: ['Dieu chinh giao dien de giam do choi va de nhin hon.'] },
            { en: 'Reduce font size and spacing to fit more content.', vi: 'Giảm cỡ chữ và khoảng cách để hiển thị nhiều nội dung hơn.', aliases: ['Giam co chu va khoang cach de hien thi nhieu noi dung hon.'] },
            { en: 'Off', vi: 'Tắt', aliases: [] },
            { en: 'On', vi: 'Bật', aliases: [] },
            { en: 'Auto', vi: 'Tự động', aliases: ['Tu dong'] },
            { en: 'Save display settings', vi: 'Lưu cài đặt hiển thị', aliases: ['Luu cai dat hien thi'] },
            { en: 'Display settings updated successfully', vi: 'Đã cập nhật cài đặt hiển thị thành công', aliases: ['Da cap nhat cai dat hien thi thanh cong'] },
            { en: 'Input Mode', vi: 'Chế độ nhập', aliases: ['Che do nhap'] },
            { en: 'Manual Entry', vi: 'Nhập thủ công', aliases: ['Nhap thu cong'] },
            { en: 'Auto from Receipt Image', vi: 'Tự động từ ảnh hóa đơn', aliases: ['Tu dong tu anh hoa don'] },
            { en: 'Receipt Image', vi: 'Ảnh hóa đơn', aliases: ['Anh hoa don'] },
            { en: 'Scan Receipt and Auto Fill', vi: 'Quét hóa đơn và tự điền', aliases: ['Quet hoa don va tu dien'] },
            { en: 'Please choose a receipt image first.', vi: 'Vui lòng chọn ảnh hóa đơn trước.', aliases: ['Vui long chon anh hoa don truoc.'] },
            { en: 'Scanning...', vi: 'Đang quét...', aliases: ['Dang quet...'] },
            { en: 'Scan completed. Fields auto-filled where possible.', vi: 'Quét xong. Hệ thống đã tự điền các trường có thể nhận diện.', aliases: ['Quet xong. He thong da tu dien cac truong co the nhan dien.'] },
            { en: 'Could not read text from this image. Try a clearer photo.', vi: 'Không đọc được nội dung từ ảnh này. Hãy thử ảnh rõ hơn.', aliases: ['Khong doc duoc noi dung tu anh nay. Hay thu anh ro hon.'] },
            { en: 'Receipt scan failed. Please try another image.', vi: 'Quét hóa đơn thất bại. Vui lòng thử ảnh khác.', aliases: ['Quet hoa don that bai. Vui long thu anh khac.'] },
            { en: 'Username', vi: 'Biệt danh', aliases: ['Biet danh'] },
            { en: 'Registered Date', vi: 'Ngày đăng ký', aliases: ['Ngay dang ky'] },
            { en: 'Phone Number', vi: 'Số điện thoại', aliases: ['So dien thoai'] },
            { en: 'Old password', vi: 'Mật khẩu cũ', aliases: ['Mat khau cu'] },
            { en: 'New password', vi: 'Mật khẩu mới', aliases: ['Mat khau moi'] },
            { en: 'Confirm new password', vi: 'Xác nhận mật khẩu mới', aliases: ['Xac nhan mat khau moi'] },
            { en: 'Update', vi: 'Cập nhật', aliases: ['Cap nhat'] },
            { en: 'Log out', vi: 'Đăng xuất', aliases: ['Dang xuat'] },
            { en: 'Logout', vi: 'Đăng xuất', aliases: ['Dang xuat'] },
            { en: 'User Profile', vi: 'Hồ sơ người dùng', aliases: ['Ho so nguoi dung'] },
            { en: 'Search...', vi: 'Tìm kiếm...', aliases: ['Tim kiem...'] },
            { en: 'Show', vi: 'Hiển thị', aliases: ['Hien thi'] },
            { en: 'entries', vi: 'mục', aliases: ['muc'] },
            { en: 'All', vi: 'Tất cả', aliases: ['Tat ca'] },
            { en: 'Type', vi: 'Loại', aliases: ['Loai'] },
            { en: 'Category', vi: 'Danh mục', aliases: ['Danh muc'] },
            { en: 'Amount', vi: 'Số tiền', aliases: ['So tien'] },
            { en: 'Description', vi: 'Mô tả', aliases: ['Mo ta'] },
            { en: 'Date', vi: 'Ngày', aliases: ['Ngay'] },
            { en: 'Action', vi: 'Thao tác', aliases: ['Thao tac'] },
            { en: 'Loading...', vi: 'Đang tải...', aliases: ['Dang tai...'] },
            { en: 'No transactions found', vi: 'Không tìm thấy giao dịch', aliases: ['Khong tim thay giao dich'] },
            { en: 'No income records found', vi: 'Không tìm thấy bản ghi thu nhập', aliases: ['Khong tim thay ban ghi thu nhap'] },
            { en: 'Delete', vi: 'Xóa', aliases: ['Xoa'] },
            { en: 'Previous', vi: 'Trước', aliases: ['Truoc'] },
            { en: 'Next', vi: 'Sau', aliases: [] },
            { en: 'Export CSV', vi: 'Xuất CSV', aliases: ['Xuat CSV'] },
            { en: 'Import CSV', vi: 'Nhập CSV', aliases: ['Nhap CSV'] },
            { en: 'CSV Format:', vi: 'Định dạng CSV:', aliases: ['Dinh dang CSV:'] },
            { en: 'Select CSV File', vi: 'Chọn tệp CSV', aliases: ['Chon tep CSV'] },
            { en: 'Cancel', vi: 'Hủy', aliases: ['Huy'] },
            { en: 'Import', vi: 'Nhập', aliases: ['Nhap'] },
            { en: 'Please fill in all fields', vi: 'Vui lòng điền đầy đủ thông tin', aliases: ['Vui long dien day du thong tin'] },
            { en: 'Error generating report', vi: 'Lỗi tạo báo cáo', aliases: ['Loi tao bao cao'] },
            { en: 'Error generating report. Please try again.', vi: 'Lỗi tạo báo cáo. Vui lòng thử lại.', aliases: ['Loi tao bao cao. Vui long thu lai.'] },
            { en: 'Error loading transactions', vi: 'Lỗi tải danh sách giao dịch', aliases: ['Loi tai danh sach giao dich'] },
            { en: 'Error loading data', vi: 'Lỗi tải dữ liệu', aliases: ['Loi tai du lieu'] },
            { en: 'An error occurred while deleting.', vi: 'Đã xảy ra lỗi khi xóa.', aliases: ['Da xay ra loi khi xoa.'] },
            { en: 'An error occurred while importing the CSV file.', vi: 'Đã xảy ra lỗi khi nhập tệp CSV.', aliases: ['Da xay ra loi khi nhap tep CSV.'] },
            { en: 'Session expired. Please login again.', vi: 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', aliases: ['Phien dang nhap da het han. Vui long dang nhap lai.'] },
            { en: 'Create User', vi: 'Tạo người dùng', aliases: [] },
            { en: 'Create New User', vi: 'Tạo người dùng mới', aliases: [] },
            { en: 'Edit User', vi: 'Chỉnh sửa người dùng', aliases: [] },
            { en: 'Reset User Password', vi: 'Đặt lại mật khẩu người dùng', aliases: [] },
            { en: 'New Temporary Password', vi: 'Mật khẩu tạm mới', aliases: [] },
            { en: 'New Secondary Password (optional)', vi: 'Mật khẩu cấp 2 mới (tùy chọn)', aliases: [] },
            { en: 'Secondary password must be exactly 4 digits, using numbers 1 to 8.', vi: 'Mật khẩu cấp 2 phải đúng 4 số, dùng các số từ 1 đến 8.', aliases: [] },
            { en: 'After reset, user must change password on next login.', vi: 'Sau khi reset, người dùng bắt buộc đổi mật khẩu ở lần đăng nhập kế tiếp.', aliases: [] },
            { en: 'No users found', vi: 'Không tìm thấy người dùng', aliases: ['Khong tim thay nguoi dung'] },
            { en: 'Failed to load users', vi: 'Không tải được danh sách người dùng', aliases: ['Khong tai duoc danh sach nguoi dung'] },
            { en: 'Error loading users', vi: 'Lỗi tải danh sách người dùng', aliases: ['Loi tai danh sach nguoi dung'] },
            { en: 'Failed to update role', vi: 'Cập nhật vai trò thất bại', aliases: ['Cap nhat vai tro that bai'] },
            { en: 'Failed to create user', vi: 'Tạo người dùng thất bại', aliases: ['Tao nguoi dung that bai'] },
            { en: 'Failed to update user', vi: 'Cập nhật người dùng thất bại', aliases: ['Cap nhat nguoi dung that bai'] },
            { en: 'Failed to reset password', vi: 'Đặt lại mật khẩu thất bại', aliases: ['Dat lai mat khau that bai'] },
            { en: 'Password reset', vi: 'Đã đặt lại mật khẩu', aliases: ['Da dat lai mat khau'] },
            { en: 'Manage User Permissions', vi: 'Quản lý người dùng', aliases: ['Quan ly nguoi dung'] },
            { en: 'Secondary Authentication', vi: 'Xác thực cấp 2', aliases: ['Xac thuc cap 2'] },
            { en: 'Enter password', vi: 'Nhập mật khẩu', aliases: ['Nhap mat khau'] },
            { en: 'Two-factor protection for your account.', vi: 'Bảo mật 2 lớp cho tài khoản của bạn.', aliases: ['Bao mat 2 lop cho tai khoan cua ban.'] },
            { en: 'Enter secondary password', vi: 'Nhập mật khẩu cấp 2', aliases: ['Nhap mat khau cap 2'] },
            { en: 'You must complete secondary verification before accessing the system.', vi: 'Bạn phải xác thực cấp 2 trước khi truy cập hệ thống.', aliases: ['Ban phai xac thuc cap 2 truoc khi truy cap vao he thong.'] },
            { en: 'Press button or any key to start', vi: 'Bấm nút hoặc phím bất kỳ để bắt đầu', aliases: ['Bam nut hoac phim bat ky de bat dau'] },
            { en: 'Back', vi: 'Lùi', aliases: ['Lui'] },
            { en: 'Confirm', vi: 'Xác nhận', aliases: ['Xac nhan'] },
            { en: 'Create secondary password', vi: 'Tạo mật khẩu cấp 2', aliases: ['Tao mat khau cap 2'] },
            { en: 'First login: create a 4-digit secondary password from numbers 1 to 8.', vi: 'Lần đầu đăng nhập: hãy tạo mật khẩu cấp 2 gồm 4 số từ 1 đến 8.', aliases: ['Lan dau dang nhap: hay tao mat khau cap 2 gom 4 so tu 1 den 8.'] },
            { en: 'Re-enter 4 digits to confirm.', vi: 'Nhập lại 4 số để xác nhận.', aliases: ['Nhap lai 4 so de xac nhan.'] },
            { en: 'Select the 4 digits of your secondary password to continue.', vi: 'Hãy chọn 4 số của mật khẩu cấp 2 để vào hệ thống.', aliases: ['Hay chon 4 so cua mat khau cap 2 de vao he thong.'] },
            { en: 'The two entries do not match. Please create again.', vi: 'Hai lần nhập không khớp, vui lòng tạo lại.', aliases: ['Hai lan nhap khong khop, vui long tao lai.'] },
            { en: 'Secondary verification failed.', vi: 'Xác thực cấp 2 thất bại.', aliases: ['Xac thuc cap 2 that bai.'] }
        ];

        var map = {};
        for (var i = 0; i < entries.length; i++) {
            var entry = entries[i];
            var viValue = entry.vi;
            var target = lang === 'vi' ? viValue : entry.en;

            map[entry.en] = target;
            map[viValue] = lang === 'vi' ? viValue : entry.en;

            if (entry.aliases && entry.aliases.length) {
                for (var j = 0; j < entry.aliases.length; j++) {
                    var alias = entry.aliases[j];
                    map[alias] = target;
                }
            }
        }

        return map;
    }

    function escapeRegExp(text) {
        return text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function replaceInString(raw, map) {
        if (!raw) return raw;
        var result = raw;
        var keys = Object.keys(map).sort(function(a, b) { return b.length - a.length; });
        for (var i = 0; i < keys.length; i++) {
            var key = keys[i];
            if (!key) continue;
            if (result.indexOf(key) < 0) continue;

            // Avoid broken mixed strings like "Người dùngname" by replacing whole words only.
            var escapedKey = escapeRegExp(key);
            var isWordLike = /^[A-Za-z0-9][A-Za-z0-9\s._-]*[A-Za-z0-9]$/.test(key);

            if (isWordLike) {
                var re = new RegExp('(^|[^A-Za-z0-9_])(' + escapedKey + ')(?=[^A-Za-z0-9_]|$)', 'g');
                result = result.replace(re, function(_, prefix) {
                    return prefix + map[key];
                });
            } else {
                result = result.split(key).join(map[key]);
            }
        }
        return result;
    }

    function replaceTrimmed(raw, map) {
        if (!raw) return raw;
        var trimmed = raw.trim();
        if (!trimmed) return raw;
        if (!Object.prototype.hasOwnProperty.call(map, trimmed)) {
            return raw;
        }
        return raw.replace(trimmed, map[trimmed]);
    }

    function translateUi(lang) {
        var map = buildPhraseMap(lang);
        var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
        var nodes = [];
        while (walker.nextNode()) {
            nodes.push(walker.currentNode);
        }

        for (var i = 0; i < nodes.length; i++) {
            var node = nodes[i];
            if (!node.parentElement) continue;
            var tag = node.parentElement.tagName;
            if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'NOSCRIPT') continue;
            node.nodeValue = replaceInString(node.nodeValue, map);
        }

        var inputs = document.querySelectorAll('input[placeholder], button[title], input[type="submit"], input[type="button"]');
        for (var j = 0; j < inputs.length; j++) {
            var input = inputs[j];
            if (input.hasAttribute('placeholder')) {
                input.setAttribute('placeholder', replaceInString(input.getAttribute('placeholder'), map));
            }
            if (input.hasAttribute('title')) {
                input.setAttribute('title', replaceInString(input.getAttribute('title'), map));
            }
            if (input.hasAttribute('value')) {
                input.setAttribute('value', replaceInString(input.getAttribute('value'), map));
            }
        }

        var title = document.title || '';
        document.title = replaceInString(title, map);

        if (!window.__i18nAlertPatched) {
            var originalAlert = window.alert;
            var originalConfirm = window.confirm;
            window.alert = function(message) {
                return originalAlert(replaceInString(String(message), map));
            };
            window.confirm = function(message) {
                return originalConfirm(replaceInString(String(message), map));
            };
            window.__i18nAlertPatched = true;
        }
    }

    function setupDynamicTranslation() {
        if (window.__i18nDynamicBound) {
            return;
        }
        window.__i18nDynamicBound = true;

        var rerenderTimer = null;
        function scheduleTranslate() {
            if (rerenderTimer) {
                clearTimeout(rerenderTimer);
            }
            rerenderTimer = setTimeout(function() {
                translateUi(resolveLang());
            }, 50);
        }

        if (typeof $ !== 'undefined' && $.fn && $.fn.jquery) {
            $(document).ajaxComplete(function() {
                scheduleTranslate();
            });
        }

        if (typeof MutationObserver !== 'undefined' && document.body) {
            var observer = new MutationObserver(function(mutations) {
                for (var i = 0; i < mutations.length; i++) {
                    if (mutations[i].addedNodes && mutations[i].addedNodes.length) {
                        scheduleTranslate();
                        return;
                    }
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    function upsertAdminNav(lang) {
        var user = null;
        try {
            user = JSON.parse(localStorage.getItem('user_data') || 'null');
        } catch (e) {
            user = null;
        }

        var sidebars = document.querySelectorAll('.nav-links');
        if (!sidebars.length) {
            return;
        }

        for (var i = 0; i < sidebars.length; i++) {
            var nav = sidebars[i];
            var existing = nav.querySelector('.admin-nav-item') || nav.querySelector("a[href*='manage-users.php']");

            if (!user || parseInt(user.is_admin, 10) !== 1) {
                if (existing) {
                    if (existing.tagName && existing.tagName.toLowerCase() === 'a') {
                        if (existing.parentElement) {
                            existing.parentElement.remove();
                        }
                    } else {
                        existing.remove();
                    }
                }
                continue;
            }

            var targetUrl = 'manage-users.php';
            if (lang) {
                targetUrl += '?lang=' + encodeURIComponent(lang);
            }

            if (!existing) {
                var li = document.createElement('li');
                li.className = 'admin-nav-item';

                var a = document.createElement('a');
                if (window.location.pathname.toLowerCase().indexOf('manage-users.php') >= 0) {
                    a.className = 'active';
                }
                a.href = targetUrl;
                a.innerHTML = "<i class='bx bx-shield-quarter'></i><span class='links_name'>Manage Users</span>";

                li.appendChild(a);

                var settingsItem = nav.querySelector("a[href*='user_profile.php']");
                if (settingsItem && settingsItem.parentElement) {
                    nav.insertBefore(li, settingsItem.parentElement);
                } else {
                    nav.appendChild(li);
                }
            } else {
                var link = existing.tagName && existing.tagName.toLowerCase() === 'a' ? existing : existing.querySelector('a');
                if (link) {
                    link.href = targetUrl;
                    if (window.location.pathname.toLowerCase().indexOf('manage-users.php') >= 0) {
                        link.classList.add('active');
                    }
                }

                if (existing.tagName && existing.tagName.toLowerCase() === 'a' && existing.parentElement) {
                    existing.parentElement.classList.add('admin-nav-item');
                }
            }
        }
    }

    function enforceRoleNavigation() {
        var user = null;
        try {
            user = JSON.parse(localStorage.getItem('user_data') || 'null');
        } catch (e) {
            user = null;
        }

        if (!user || parseInt(user.is_admin, 10) === 1) {
            return;
        }

        var blockedPatterns = [
            'manage-transaction.php',
            'manage-income.php',
            'manage-users.php'
        ];

        var links = document.querySelectorAll('.nav-links a[href]');
        for (var i = 0; i < links.length; i++) {
            var link = links[i];
            var href = (link.getAttribute('href') || '').toLowerCase();
            for (var j = 0; j < blockedPatterns.length; j++) {
                if (href.indexOf(blockedPatterns[j]) >= 0) {
                    if (link.parentElement) {
                        link.parentElement.remove();
                    }
                    break;
                }
            }
        }
    }

    function enforcePasswordChangeFlow() {
        var user = null;
        try {
            user = JSON.parse(localStorage.getItem('user_data') || 'null');
        } catch (e) {
            user = null;
        }

        if (!user || parseInt(user.must_change_password || 0, 10) !== 1) {
            return;
        }

        var path = (window.location.pathname || '').toLowerCase();
        var allowed = [
            'force-change-password.php',
            'logout.php',
            'index.php'
        ];

        for (var i = 0; i < allowed.length; i++) {
            if (path.indexOf(allowed[i]) >= 0) {
                return;
            }
        }

        var target = 'force-change-password.php';
        var lang = resolveLang();
        if (lang) {
            target += '?lang=' + encodeURIComponent(lang);
        }
        window.location.href = target;
    }

    function applyStoredUserAvatar() {
        var user = null;
        try {
            user = JSON.parse(localStorage.getItem('user_data') || 'null');
        } catch (e) {
            user = null;
        }

        if (!user || !user.avatar) {
            return;
        }

        var avatar = String(user.avatar).trim();
        if (!avatar) {
            return;
        }

        var candidates = document.querySelectorAll('.profile-details img, #profile-image-preview');
        for (var i = 0; i < candidates.length; i++) {
            candidates[i].src = avatar;
        }
    }

    function enhanceProfileMenus() {
        var user = null;
        try {
            user = JSON.parse(localStorage.getItem('user_data') || 'null');
        } catch (e) {
            user = null;
        }

        var profileName = user && user.name ? String(user.name) : 'User';
        var avatarFromUser = user && user.avatar ? String(user.avatar).trim() : '';
        var profileImageNode = document.querySelector('.profile-details img');
        var fallbackAvatar = profileImageNode && profileImageNode.getAttribute('src') ? profileImageNode.getAttribute('src') : 'images/maex.png';
        var profileAvatar = avatarFromUser || fallbackAvatar;

        var menus = document.querySelectorAll('nav .profile-options');
        for (var i = 0; i < menus.length; i++) {
            var menu = menus[i];
            menu.classList.add('profile-options-fb');

            var header = menu.querySelector('.profile-menu-header');
            if (!header) {
                header = document.createElement('li');
                header.className = 'profile-menu-header';

                var userWrap = document.createElement('div');
                userWrap.className = 'profile-menu-user';

                var avatarEl = document.createElement('img');
                avatarEl.className = 'profile-menu-avatar';
                avatarEl.setAttribute('src', profileAvatar);
                avatarEl.setAttribute('alt', 'avatar');

                var nameEl = document.createElement('div');
                nameEl.className = 'profile-menu-name';
                nameEl.textContent = profileName;

                userWrap.appendChild(avatarEl);
                userWrap.appendChild(nameEl);
                header.appendChild(userWrap);
                menu.insertBefore(header, menu.firstChild);
            } else {
                var avatarEl = header.querySelector('.profile-menu-avatar');
                var nameEl = header.querySelector('.profile-menu-name');
                if (avatarEl) avatarEl.setAttribute('src', profileAvatar);
                if (nameEl) nameEl.textContent = profileName;
            }

            var links = menu.querySelectorAll('li a');
            for (var j = 0; j < links.length; j++) {
                links[j].classList.add('profile-menu-link');
            }
        }
    }

    function enableAvatarDropdownToggle() {
        var profileBlocks = document.querySelectorAll('nav .profile-details');
        for (var i = 0; i < profileBlocks.length; i++) {
            (function(block) {
                if (block.__avatarToggleBound) {
                    return;
                }

                var menu = block.querySelector('.profile-options');
                if (!menu) {
                    return;
                }

                block.__avatarToggleBound = true;
                block.style.cursor = 'pointer';

                block.addEventListener('click', function(evt) {
                    if (evt.target && evt.target.closest('.profile-options')) {
                        return;
                    }
                    menu.classList.toggle('show');
                });
            })(profileBlocks[i]);
        }

        if (!document.__avatarDropdownCloseBound) {
            document.__avatarDropdownCloseBound = true;
            document.addEventListener('click', function(evt) {
                if (evt.target && evt.target.closest('nav .profile-details')) {
                    return;
                }

                var menus = document.querySelectorAll('nav .profile-options.show');
                for (var i = 0; i < menus.length; i++) {
                    menus[i].classList.remove('show');
                }
            });
        }
    }

    function removeSidebarLogoutItem() {
        var logoutItems = document.querySelectorAll('.nav-links .log_out');
        for (var i = 0; i < logoutItems.length; i++) {
            logoutItems[i].remove();
        }
    }

    function bootLang() {
        var lang = resolveLang();
        persistLang(lang);
        ensureUrlHasLang(lang);
        patchInternalLinks(lang);
        applyStoredUserAvatar();
        enhanceProfileMenus();
        enableAvatarDropdownToggle();
        enforcePasswordChangeFlow();
        upsertAdminNav(lang);
        enforceRoleNavigation();
        removeSidebarLogoutItem();
        upsertSwitcher(lang);
        translateUi(lang);
        setupDynamicTranslation();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootLang);
    } else {
        bootLang();
    }
})();
