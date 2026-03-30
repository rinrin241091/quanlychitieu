<?php include "head.php" ?>
<body>
    <!-- ==== HEADER ==== -->
    <header class="container header">
      <!-- ==== NAVBAR ==== -->
      <nav class="nav">
        <div class="logo">
          <h2><?php echo t('brand'); ?></h2>
        </div>

        <div class="nav_menu" id="nav_menu">
          <button class="close_btn" id="close_btn">
            <i class="ri-close-fill"></i>
          </button>

          <ul class="nav_menu_list">
            <li class="nav_menu_item">
              <a href="#" class="nav_menu_link"><?php echo t('account'); ?></a>
            </li>
            <li class="nav_menu_item">
              <a href="#" class="nav_menu_link"><?php echo t('about'); ?></a>
            </li>
            <li class="nav_menu_item">
              <a href="#" class="nav_menu_link"><?php echo t('service'); ?></a>
            </li>
            <li class="nav_menu_item">
              <a href="#" class="nav_menu_link"><?php echo t('contact'); ?></a>
            </li>
            <li class="nav_menu_item">
              <a href="<?php echo htmlspecialchars(switch_lang_url('vi'), ENT_QUOTES, 'UTF-8'); ?>" class="nav_menu_link"><?php echo t('lang_vi'); ?></a>
            </li>
            <li class="nav_menu_item">
              <a href="<?php echo htmlspecialchars(switch_lang_url('en'), ENT_QUOTES, 'UTF-8'); ?>" class="nav_menu_link"><?php echo t('lang_en'); ?></a>
            </li>
          </ul>
        </div>

        <button class="toggle_btn" id="toggle_btn">
          <i class="ri-menu-line"></i>
        </button>
      </nav>
    </header>