<?php
session_start();
error_reporting(0);
include('database.php');
include_once('i18n.php');

if (empty($_SESSION['detsuid'])) {
  header('location:logout.php');
} else {
?>





  <!DOCTYPE html>
  <!-- Designined by CodingLab | www.youtube.com/codinglabyt -->
  <html lang="<?php echo htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8'); ?>" dir="ltr">

  <head>
    <meta charset="UTF-8">
    <!--<title> Responsiive Admin Dashboard | CodingLab </title>-->
    <link rel="stylesheet" href="css/style.css">
    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/auth.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>


    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <style>
      .container {
        background-color: #f2f2f2;
        border-radius: 5px;
        box-shadow: 0px 0px 10px #aaa;
        padding: 20px;
        margin-top: 20px;
      }

      .form-group label {
        font-weight: bold;
      }

      .form-control {
        border-radius: 3px;
        border: 1px solid #ccc;
      }

      .invalid-feedback {
        color: red;
        font-size: 12px;
      }

      .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
      }

      .btn-primary:hover {
        background-color: #0069d9;
        border-color: #0062cc;
      }

      .entry-mode-group .btn {
        min-width: 160px;
      }

      .receipt-panel {
        border: 1px dashed #b8c2cc;
        border-radius: 8px;
        padding: 14px;
        background: #f8fbff;
        margin-bottom: 16px;
      }

      .receipt-preview {
        max-width: 220px;
        max-height: 220px;
        border-radius: 6px;
        border: 1px solid #dce3ea;
        margin-top: 10px;
      }

      .receipt-gallery {
        margin-top: 12px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
      }

      .receipt-thumb {
        position: relative;
        border: 1px solid #dce3ea;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
      }

      .receipt-thumb img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        display: block;
      }

      .receipt-thumb .file-name {
        font-size: 11px;
        color: #4a5568;
        padding: 6px 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .remove-receipt-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 22px;
        height: 22px;
        border: none;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.95);
        color: #fff;
        font-size: 13px;
        line-height: 22px;
        text-align: center;
        cursor: pointer;
      }

      .ocr-status {
        margin-top: 10px;
        font-size: 13px;
      }

      .ocr-result-box {
        margin-top: 10px;
        font-size: 13px;
        background: #fff;
        border: 1px solid #dce3ea;
        border-radius: 6px;
        padding: 10px;
      }
    </style>
  </head>

  <body>
    <div class="sidebar">
      <div class="logo-details">
        <i class='bx bx-album'></i>
        <span class="logo_name"><?php echo t('expense_tracker'); ?></span>
      </div>
      <ul class="nav-links">
        <li>
          <a href="<?php echo htmlspecialchars(with_lang('home.php'), ENT_QUOTES, 'UTF-8'); ?>">
            <i class='bx bx-grid-alt'></i>
            <span class="links_name"><?php echo t('dashboard'); ?></span>
          </a>
        </li>
        <li>
          <a href="<?php echo htmlspecialchars(with_lang('add-expenses.php'), ENT_QUOTES, 'UTF-8'); ?>" class="active">
            <i class='bx bx-box'></i>
            <span class="links_name"><?php echo t('expenses'); ?></span>
          </a>
        </li>
        <li>
          <a href="<?php echo htmlspecialchars(with_lang('add-income.php'), ENT_QUOTES, 'UTF-8'); ?>">
            <i class='bx bx-box'></i>
            <span class="links_name"><?php echo t('income'); ?></span>
          </a>
        </li>
        <li>
          <a href="<?php echo htmlspecialchars(with_lang('manage-transaction.php'), ENT_QUOTES, 'UTF-8'); ?>">
            <i class='bx bx-list-ul'></i>
            <span class="links_name"><?php echo t('manage_list'); ?></span>
          </a>
        </li>
        <li>
          <a href="<?php echo htmlspecialchars(with_lang('analytics.php'), ENT_QUOTES, 'UTF-8'); ?>">
            <i class='bx bx-pie-chart-alt-2'></i>
            <span class="links_name"><?php echo t('analytics'); ?></span>
          </a>
        </li>
        <li>
          <a href="<?php echo htmlspecialchars(with_lang('report.php'), ENT_QUOTES, 'UTF-8'); ?>">
            <i class="bx bx-file"></i>
            <span class="links_name"><?php echo t('report'); ?></span>
          </a>
        </li>
        <li>
          <a href="<?php echo htmlspecialchars(with_lang('user_profile.php'), ENT_QUOTES, 'UTF-8'); ?>">
            <i class='bx bx-cog'></i>
            <span class="links_name"><?php echo t('setting'); ?></span>
          </a>
        </li>
        <li class="log_out">
          <a href="<?php echo htmlspecialchars(with_lang('logout.php'), ENT_QUOTES, 'UTF-8'); ?>">
            <i class='bx bx-log-out'></i>
            <span class="links_name"><?php echo t('logout'); ?></span>
          </a>
        </li>
      </ul>
    </div>
    <section class="home-section">
      <nav>
        <div class="sidebar-button">
          <i class='bx bx-menu sidebarBtn'></i>
          <span class="dashboard"><?php echo t('expense_tracker'); ?></span>
        </div>
        <div style="margin-left:auto; margin-right:12px; font-weight:600;">
          <a href="<?php echo htmlspecialchars(switch_lang_url('vi'), ENT_QUOTES, 'UTF-8'); ?>">VI</a> |
          <a href="<?php echo htmlspecialchars(switch_lang_url('en'), ENT_QUOTES, 'UTF-8'); ?>">EN</a>
        </div>

        <?php
        $uid = $_SESSION['detsuid'];
        $ret = mysqli_query($db, "select name  from users where id='$uid'");
        $row = mysqli_fetch_array($ret);
        $name = $row['name'];

        ?>

        <div class="profile-details">
          <img src="images/maex.png" alt="">
          <span class="admin_name"><?php echo $name; ?></span>
          <i class='bx bx-chevron-down' id='profile-options-toggle'></i>
          <ul class="profile-options" id='profile-options'>
            <li><a href="<?php echo htmlspecialchars(with_lang('user_profile.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-user-circle"></i> <?php echo t('user_profile'); ?></a></li>
            <!-- <li><a href="#"><i class="fas fa-cog"></i> Account Settings</a></li> -->
            <li><a href="<?php echo htmlspecialchars(with_lang('logout.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-sign-out-alt"></i> <?php echo t('logout_title'); ?></a></li>
          </ul>
        </div>
        <script>
          const toggleButton = document.getElementById('profile-options-toggle');
          const profileOptions = document.getElementById('profile-options');

          toggleButton.addEventListener('click', () => {
            profileOptions.classList.toggle('show');
          });
        </script>

      </nav>


      <?php
      $uid = $_SESSION['detsuid'];
      $ret = mysqli_query($db, "select name  from users where id='$uid'");
      $row = mysqli_fetch_array($ret);
      $name = $row['name'];

      ?>

      <div class="home-content">
        <div class="overview-boxes">


          <div class="col-md-12">
            <br>

            <div class="card">
              <div class="card-header">
                <div class="row">
                  <div class="col-md-6">
                    <h4 class="card-title">Add Expense</h4>
                  </div>
                  <div class="col-md-6 text-right">
                    <div class="ml-auto">
                      <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#add-category-modal">
                        <i class="fas fa-plus-circle"></i> Add Category
                      </button>
                    </div>
                  </div>


                  <div class="modal fade" id="add-category-modal" tabindex="-1" role="dialog" aria-labelledby="add-category-modal-title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <form id="add-category-form">
                          <div class="modal-header">
                            <h5 class="modal-title" id="add-category-modal-title">Add Category</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <div class="form-group">
                              <label for="category-name">Category Name</label>
                              <input type="text" class="form-control" id="category-name" name="category-name" required>
                              <input type="hidden" name="mode" value="expense">

                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Category</button>

                          </div>
                        </form>
                      </div>
                    </div>
                  </div>



                </div>
              </div>
              <div class="card-body">
                <form id="expenseForm" role="form" class="needs-validation">
                  <div class="form-group entry-mode-group">
                    <label>Input Mode</label>
                    <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                      <label class="btn btn-outline-primary active">
                        <input type="radio" name="entry-mode" value="manual" autocomplete="off" checked> Manual Entry
                      </label>
                      <label class="btn btn-outline-primary">
                        <input type="radio" name="entry-mode" value="auto" autocomplete="off"> Auto from Receipt Image
                      </label>
                    </div>
                  </div>

                  <div id="auto-receipt-panel" class="receipt-panel" style="display:none;">
                    <div class="form-group mb-2">
                      <label for="receipt-image">Receipt Images (select one or many)</label>
                      <input class="form-control-file" type="file" id="receipt-image" accept="image/*" multiple>
                      <small class="form-text text-muted">You can select many bills and remove each image using the X button.</small>
                    </div>
                    <button type="button" class="btn btn-info btn-sm" id="scan-receipt-btn">
                      <i class="fas fa-camera"></i> Scan Selected Bills and Auto Fill
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-receipt-btn">
                      <i class="fas fa-times"></i> Remove All Images
                    </button>
                    <div id="ocr-status" class="ocr-status text-muted"></div>
                    <div id="receipt-gallery" class="receipt-gallery"></div>
                    <div id="ocr-result" class="ocr-result-box" style="display:none;"></div>
                  </div>

                  <div class="form-group">
                    <label for="dateexpense">Date of Expense</label>
                    <input class="form-control" type="date" id="dateexpense" name="dateexpense" value="<?php echo date('Y-m-d'); ?>">
                  </div>


                  <div class="form-group">
                    <label for="category">Category</label>
                    <select class="form-control" id="category" name="category" required>
                      <option value="" selected disabled>Choose Category</option>
                      <!-- Categories will be loaded via AJAX -->

                    </select>
                  </div>

                  <div class="form-group">
                    <label for="costitem">Cost of Item</label>
                    <input class="form-control" type="number" id="costitem" name="costitem" required>
                  </div>

                  <div class="form-group">
                    <label for="category-description">Description</label>
                    <textarea class="form-control" id="category-description" name="category-description" required></textarea>
                  </div>


                  <div class="form-group">
                    <button type="submit" class="btn btn-primary" name="submit">Add</button>
                  </div>
                </form>
                <div id="success-message" class="alert alert-success" style="display:none;">
                  Expense added successfully.
                </div>
              </div>
            </div>





          </div>
        </div>

    </section>





    <script>
      let sidebar = document.querySelector(".sidebar");
      let sidebarBtn = document.querySelector(".sidebarBtn");
      sidebarBtn.onclick = function() {
        sidebar.classList.toggle("active");
        if (sidebar.classList.contains("active")) {
          sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
        } else
          sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
      }
    </script>
    <script>
      $(document).ready(function() {
        var categoriesCache = [];
        var selectedReceiptFiles = [];
        var receiptIdSeed = 1;

        loadCategories();

        function loadCategories() {
            $.ajax({
                url: 'api/get-categories.php',
                type: 'GET',
                data: { mode: 'expense' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                      categoriesCache = response.data || [];
                        var options = '<option value="" selected disabled>Choose Category</option>';
                        $.each(response.data, function(index, category) {
                            options += '<option value="' + category.categoryid + '">' + category.categoryname + '</option>';
                        });
                        $('#category').html(options);
                    } else {
                        console.error('Failed to load categories');
                    }
                },
                error: function() {
                    console.error('Error loading categories');
                }
            });
        }

        function normalizeText(value) {
          return String(value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .trim();
        }

        function escapeHtml(value) {
          return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
        }

        function parseMoneyToken(token) {
          var cleaned = String(token || '').replace(/\s+/g, '');
          if (!cleaned) return 0;

          if (cleaned.indexOf(',') >= 0 && cleaned.indexOf('.') >= 0) {
            if (cleaned.lastIndexOf(',') > cleaned.lastIndexOf('.')) {
              cleaned = cleaned.replace(/\./g, '').replace(',', '.');
            } else {
              cleaned = cleaned.replace(/,/g, '');
            }
          } else if (cleaned.indexOf(',') >= 0) {
            var commaParts = cleaned.split(',');
            cleaned = commaParts[commaParts.length - 1].length === 3 ? cleaned.replace(/,/g, '') : cleaned.replace(',', '.');
          } else if (cleaned.indexOf('.') >= 0) {
            var dotParts = cleaned.split('.');
            if (dotParts[dotParts.length - 1].length === 3) {
              cleaned = cleaned.replace(/\./g, '');
            }
          }

          var amount = parseFloat(cleaned);
          return Number.isFinite(amount) ? amount : 0;
        }

        function extractDate(text) {
          var lines = String(text || '').split(/\r?\n/);
          var dateRegexes = [
            /(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{2,4})/,
            /(\d{4})[\/\-.](\d{1,2})[\/\-.](\d{1,2})/
          ];

          for (var i = 0; i < lines.length; i++) {
            var line = lines[i];
            for (var r = 0; r < dateRegexes.length; r++) {
              var m = line.match(dateRegexes[r]);
              if (!m) continue;

              var y, mo, d;
              if (r === 0) {
                d = parseInt(m[1], 10);
                mo = parseInt(m[2], 10);
                y = parseInt(m[3], 10);
                if (y < 100) y += 2000;
              } else {
                y = parseInt(m[1], 10);
                mo = parseInt(m[2], 10);
                d = parseInt(m[3], 10);
              }

              if (y >= 2000 && mo >= 1 && mo <= 12 && d >= 1 && d <= 31) {
                var mm = mo < 10 ? '0' + mo : String(mo);
                var dd = d < 10 ? '0' + d : String(d);
                return y + '-' + mm + '-' + dd;
              }
            }
          }

          return '';
        }

        function extractAmount(text) {
          var lines = String(text || '').split(/\r?\n/).filter(function(x) { return x.trim() !== ''; });
          var priorityKeys = ['tong tien', 'tong cong', 'thanh tien', 'can thanh toan', 'tong thanh toan', 'so tien', 'total', 'amount due', 'grand total', 'pay'];
          var referenceKeys = ['ma gd', 'magd', 'ma giao dich', 'giao dich', 'reference', 'ref', 'trace', 'order', 'invoice no', 'stk', 'tai khoan', 'account', 'so tk', 'phone', 'dien thoai'];
          var currencyKeys = ['vnd', 'vnđ', 'd', 'dong', '$'];
          var maxReasonableAmount = 200000000; // 200M to avoid selecting account/reference numbers

          var best = { score: -9999, value: 0 };
          var fallback = 0;

          function containsAny(normalized, keys) {
            for (var i = 0; i < keys.length; i++) {
              if (normalized.indexOf(keys[i]) >= 0) {
                return true;
              }
            }
            return false;
          }

          lines.forEach(function(line) {
            var normalized = normalizeText(line);
            var isReferenceLine = containsAny(normalized, referenceKeys);
            var hasPriorityKeyword = containsAny(normalized, priorityKeys);
            var hasCurrencyKeyword = containsAny(normalized, currencyKeys);

            var matches = line.match(/(?:\d{1,3}(?:[.,\s]\d{3})+|\d{3,})(?:[.,]\d{1,2})?/g) || [];
            matches.forEach(function(token) {
              var compactToken = String(token || '').replace(/\s+/g, '');
              var plainDigits = compactToken.replace(/\D/g, '');
              var value = parseMoneyToken(token);

              if (!Number.isFinite(value) || value <= 0 || value > maxReasonableAmount) {
                return;
              }

              // Long plain numbers are usually account numbers / reference numbers.
              if (plainDigits.length >= 10 && compactToken.indexOf('.') === -1 && compactToken.indexOf(',') === -1) {
                return;
              }

              if (!isReferenceLine && value >= 1000 && value > fallback) {
                fallback = value;
              }

              var score = 0;
              if (hasPriorityKeyword) score += 80;
              if (hasCurrencyKeyword) score += 25;
              if (!isReferenceLine) score += 10;
              if (plainDigits.length >= 4 && plainDigits.length <= 8) score += 8;
              if (isReferenceLine) score -= 100;

              if (score > best.score) {
                best = { score: score, value: value };
              }
            });
          });

          if (best.value > 0 && best.score >= 20) {
            return best.value;
          }

          return fallback;
        }

        function extractMerchantLine(text) {
          var lines = String(text || '').split(/\r?\n/);
          for (var i = 0; i < lines.length; i++) {
            var candidate = lines[i].replace(/\s+/g, ' ').trim();
            if (!candidate) continue;

            var normalized = normalizeText(candidate);
            if (normalized.length < 4) continue;
            if (/\d{3,}/.test(candidate)) continue;
            if (normalized.indexOf('mst') >= 0 || normalized.indexOf('tax') >= 0) continue;

            return candidate;
          }
          return '';
        }

        function suggestCategoryId(text, categories) {
          if (!Array.isArray(categories) || !categories.length) return '';

          var normalizedText = normalizeText(text);
          for (var i = 0; i < categories.length; i++) {
            var cname = normalizeText(categories[i].categoryname || '');
            if (cname.length >= 3 && normalizedText.indexOf(cname) >= 0) {
              return categories[i].categoryid;
            }
          }

          var groups = [
            { keys: ['food', 'an uong', 'do an', 'quan', 'bun', 'pho', 'com', 'cafe', 'coffee', 'tra sua'], picks: ['food', 'an', 'uong', 'cafe', 'coffee', 'eat'] },
            { keys: ['xang', 'petrol', 'fuel'], picks: ['xang', 'petrol', 'fuel'] },
            { keys: ['dien', 'electric', 'water', 'nuoc'], picks: ['dien', 'electric', 'water', 'nuoc'] },
            { keys: ['sieu thi', 'market', 'mart', 'shop'], picks: ['market', 'mart', 'shop', 'mua sam'] },
            { keys: ['rent', 'thue', 'nha'], picks: ['rent', 'thue', 'nha'] }
          ];

          for (var g = 0; g < groups.length; g++) {
            var hasGroupKeyword = groups[g].keys.some(function(k) {
              return normalizedText.indexOf(k) >= 0;
            });
            if (!hasGroupKeyword) continue;

            for (var c = 0; c < categories.length; c++) {
              var catNorm = normalizeText(categories[c].categoryname || '');
              for (var p = 0; p < groups[g].picks.length; p++) {
                if (catNorm.indexOf(groups[g].picks[p]) >= 0) {
                  return categories[c].categoryid;
                }
              }
            }
          }

          return '';
        }

        function parseReceipt(rawText) {
          var extractedDate = extractDate(rawText);
          var extractedAmount = extractAmount(rawText);
          var merchant = extractMerchantLine(rawText);
          var categoryId = suggestCategoryId(rawText, categoriesCache);

          return {
            date: extractedDate,
            amount: extractedAmount,
            merchant: merchant,
            categoryId: categoryId
          };
        }

        function applyReceiptToForm(parsed) {
          var extractedDate = parsed.date || '';
          var extractedAmount = parsed.amount || 0;
          var merchant = parsed.merchant || '';
          var categoryId = parsed.categoryId || '';

          if (extractedDate) {
            $('#dateexpense').val(extractedDate);
          }

          if (extractedAmount > 0) {
            $('#costitem').val(Math.round(extractedAmount));
          }

          if (merchant) {
            var existingDesc = ($('#category-description').val() || '').trim();
            if (!existingDesc) {
              $('#category-description').val('Receipt: ' + merchant);
            }
          }

          if (categoryId) {
            $('#category').val(String(categoryId));
          }
        }

        function renderOcrSummary(results) {
          if (!results.length) {
            $('#ocr-result').show().html('<strong>OCR Result</strong><br>No readable bill data found.');
            return;
          }

          var html = '<strong>OCR Result</strong><br>';
          for (var i = 0; i < results.length; i++) {
            var item = results[i];
            html += (i + 1) + '. ' + item.name + ' - Date: ' + (item.date || 'Not found') + ', Amount: ' + (item.amount > 0 ? Math.round(item.amount) : 'Not found') + ', Category: ' + (item.category || 'Not selected') + '<br>';
          }
          html += '<em>The first readable bill was used to auto-fill the form. You can edit manually before saving.</em>';

          $('#ocr-result').show().html(html);
        }

        function clearAllSelectedReceipts() {
          selectedReceiptFiles.forEach(function(item) {
            if (item.previewUrl) {
              URL.revokeObjectURL(item.previewUrl);
            }
          });
          selectedReceiptFiles = [];
          $('#receipt-image').val('');
          $('#receipt-gallery').empty();
          $('#ocr-status').text('');
          $('#ocr-result').hide().html('');
        }

        function renderReceiptGallery() {
          if (!selectedReceiptFiles.length) {
            $('#receipt-gallery').empty();
            return;
          }

          var html = '';
          selectedReceiptFiles.forEach(function(item) {
            var safeName = escapeHtml(item.file.name || 'receipt-image');
            html += '<div class="receipt-thumb">' +
              '<button type="button" class="remove-receipt-btn" data-id="' + item.id + '" title="Remove this image">&times;</button>' +
              '<img src="' + item.previewUrl + '" alt="Receipt preview">' +
              '<div class="file-name" title="' + safeName + '">' + safeName + '</div>' +
            '</div>';
          });

          $('#receipt-gallery').html(html);
        }

        async function scanSelectedReceipts() {
          if (!selectedReceiptFiles.length) {
            alert('Please choose at least one receipt image first.');
            return;
          }

          var $btn = $('#scan-receipt-btn');
          $btn.prop('disabled', true).text('Scanning...');
          $('#ocr-result').hide().html('');

          var scanSummaries = [];
          var formFilled = false;

          for (var i = 0; i < selectedReceiptFiles.length; i++) {
            var entry = selectedReceiptFiles[i];
            try {
              var recognized = await Tesseract.recognize(entry.file, 'eng+vie', {
                logger: function(message) {
                  if (message && message.status) {
                    var percent = typeof message.progress === 'number' ? Math.round(message.progress * 100) : null;
                    var prefix = 'Bill ' + (i + 1) + '/' + selectedReceiptFiles.length + ': ';
                    $('#ocr-status').text(prefix + (percent !== null ? (message.status + ' ' + percent + '%') : message.status));
                  }
                }
              });

              var text = (recognized && recognized.data && recognized.data.text) ? recognized.data.text : '';
              if (!text.trim()) {
                scanSummaries.push({ name: entry.file.name, date: '', amount: 0, category: '' });
                continue;
              }

              var parsed = parseReceipt(text);
              if (!formFilled && (parsed.date || parsed.amount > 0 || parsed.merchant || parsed.categoryId)) {
                applyReceiptToForm(parsed);
                formFilled = true;
              }

              scanSummaries.push({
                name: entry.file.name,
                date: parsed.date,
                amount: parsed.amount,
                category: parsed.categoryId ? ($('#category option[value="' + parsed.categoryId + '"]').text() || '') : ''
              });
            } catch (e) {
              scanSummaries.push({ name: entry.file.name, date: '', amount: 0, category: '' });
            }
          }

          if (formFilled) {
            $('#ocr-status').text('Scan completed. Form auto-filled from the first readable bill.');
          } else {
            $('#ocr-status').text('Scan completed but no clear amount/date was detected.');
          }

          renderOcrSummary(scanSummaries);
          $btn.prop('disabled', false).text('Scan Selected Bills and Auto Fill');

          $('#ocr-result').show().html(
            '<strong>OCR Result</strong><br>' +
            'Date: ' + (extractedDate || 'Not found') + '<br>' +
            'Amount: ' + (extractedAmount > 0 ? Math.round(extractedAmount) : 'Not found') + '<br>' +
            'Category: ' + ($('#category option:selected').text() || 'Not selected') + '<br>' +
            'Description: ' + (($('#category-description').val() || '').trim() || 'Not filled')
          );
        }

        function getSelectedEntryMode() {
          var checked = $('input[name="entry-mode"]:checked').val();
          if (checked === 'auto' || checked === 'manual') {
            return checked;
          }

          // Fallback in case Bootstrap active class is out of sync with the checked input.
          var activeLabel = $('.entry-mode-group .btn.active input[name="entry-mode"]');
          var activeValue = activeLabel.length ? activeLabel.val() : '';
          return activeValue === 'auto' ? 'auto' : 'manual';
        }

        function syncEntryModeUI() {
          var mode = getSelectedEntryMode();
          if (mode === 'auto') {
            $('#auto-receipt-panel').stop(true, true).slideDown(180);
          } else {
            $('#auto-receipt-panel').stop(true, true).slideUp(180);
          }
        }

        $('input[name="entry-mode"]').on('change', syncEntryModeUI);
        $('.entry-mode-group .btn').on('click', function() {
          var $input = $(this).find('input[name="entry-mode"]');
          if ($input.length) {
            $input.prop('checked', true).trigger('change');
          }
        });
        syncEntryModeUI();

        $('#receipt-image').on('change', function() {
          var files = this.files ? Array.prototype.slice.call(this.files) : [];
          if (!files.length) {
            return;
          }

          for (var i = 0; i < files.length; i++) {
            var file = files[i];
            if (!file.type || file.type.indexOf('image/') !== 0) {
              continue;
            }

            selectedReceiptFiles.push({
              id: receiptIdSeed++,
              file: file,
              previewUrl: URL.createObjectURL(file)
            });
          }

          $(this).val('');
          renderReceiptGallery();
        });

        $('#clear-receipt-btn').on('click', function() {
          clearAllSelectedReceipts();
        });

        $(document).on('click', '.remove-receipt-btn', function() {
          var id = parseInt($(this).data('id'), 10);
          selectedReceiptFiles = selectedReceiptFiles.filter(function(item) {
            if (item.id === id) {
              if (item.previewUrl) {
                URL.revokeObjectURL(item.previewUrl);
              }
              return false;
            }
            return true;
          });
          renderReceiptGallery();
        });

        $('#scan-receipt-btn').on('click', function() {
          scanSelectedReceipts();
        });

        $('#expenseForm').on('submit', function(e) {
          e.preventDefault();
          $.ajax({
            url: 'api/add-expense.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                alert(response.message);
                window.location.href = 'manage-transaction.php';
              } else {
                alert(response.message);
              }
            },
            error: function() {
              alert('An error occurred while processing your request.');
            }
          });
        });

        // Add Category AJAX handler
        $('#add-category-form').on('submit', function(e) {
          e.preventDefault();
          $.ajax({
            url: 'api/add-category.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                alert(response.message);
                $('#add-category-modal').modal('hide');
                $('#add-category-form')[0].reset();
                loadCategories(); // Refresh categories without reload
              } else {
                alert(response.message);
              }
            },
            error: function() {
              alert('An error occurred while adding the category.');
            }
          });
        });
      });
    </script>

  <?php } ?>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.3/umd/popper.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>

  <!-- Bootstrap Validation Plugin -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>