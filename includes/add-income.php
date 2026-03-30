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
    <html lang="<?php echo htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8'); ?>" dir="ltr">

    <head>
        <meta charset="UTF-8">
        <title><?php echo t('add_income'); ?></title>
        <link rel="stylesheet" href="css/style.css">
        <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="js/auth.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="js/scripts.js"></script>

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
        </style>
    </head>

    <body>
        <div class="sidebar">
            <div class="logo-details">
                <i class='bx bx-album'></i>
                <span class="logo_name"><?php echo t('income_tracker'); ?></span>
            </div>
            <ul class="nav-links">
                <li>
                    <a href="<?php echo htmlspecialchars(with_lang('home.php'), ENT_QUOTES, 'UTF-8'); ?>">
                        <i class='bx bx-grid-alt'></i>
                        <span class="links_name"><?php echo t('dashboard'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo htmlspecialchars(with_lang('add-expenses.php'), ENT_QUOTES, 'UTF-8'); ?>">
                        <i class='bx bx-box'></i>
                        <span class="links_name"><?php echo t('expenses'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo htmlspecialchars(with_lang('add-income.php'), ENT_QUOTES, 'UTF-8'); ?>" class="active">
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
                    <span class="dashboard"><?php echo t('income_tracker'); ?></span>
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

            <div class="home-content">
                <div class="overview-boxes">
                    <div class="col-md-12">
                        <br>
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="card-title"><?php echo t('add_income'); ?></h4>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <div class="ml-auto">
                                            <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#add-category-modal">
                                                <i class="fas fa-plus-circle"></i> <?php echo t('add_category'); ?>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="add-category-modal" tabindex="-1" role="dialog" aria-labelledby="add-category-modal-title" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <form id="add-category-form" method="post" action="add_category.php">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="add-category-modal-title"><?php echo t('add_income_category'); ?></h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="category-name"><?php echo t('category_name'); ?></label>
                                                            <input type="text" class="form-control" id="category-name" name="category-name" required>
                                                            <input type="hidden" name="mode" value="income">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
                                                        <button type="submit" class="btn btn-primary" name="add-category-submit"><?php echo t('add_category'); ?></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="incomeForm" role="form" class="needs-validation">
                                    <div class="form-group">
                                        <label for="incomeDate"><?php echo t('date_of_income'); ?></label>
                                        <input class="form-control" type="date" id="incomeDate" name="incomeDate" value="<?php echo date('Y-m-d'); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="category">Category</label>
                                        <select class="form-control" id="category" name="category" required>
                                            <option value="" selected disabled>Choose Category</option>
                                            <!-- Categories will be loaded via AJAX -->

                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="incomeAmount">Amount of Income</label>
                                        <input class="form-control" type="number" id="incomeAmount" name="incomeAmount" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary" name="submit">Add</button>
                                    </div>
                                </form>
                                <div id="success-message" class="alert alert-success" style="display:none;">
                                    Income added successfully.
                                </div>
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
            loadCategories();

            function loadCategories() {
                $.ajax({
                    url: 'api/get-categories.php',
                    type: 'GET',
                    data: { mode: 'income' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
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

            $('#incomeForm').on('submit', function(e) {
              e.preventDefault();
              $.ajax({
                url: 'api/add-income.php',
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>