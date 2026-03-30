<?php
include_once __DIR__ . '/i18n.php';

$requestPath = isset($_SERVER['SCRIPT_NAME']) ? (string) $_SERVER['SCRIPT_NAME'] : '';
$assetPrefix = (strpos($requestPath, '/includes/') !== false) ? '' : 'includes/';
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8'); ?>">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo t('brand'); ?></title>

    <!-- ==== STYLE.CSS ==== -->
    <!-- <link href="css/style.css" rel="stylesheet" /> -->
   
    <!-- ====  REMIXICON CDN ==== -->
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css"
      rel="stylesheet"
    />
  
    <!-- ==== ANIMATE ON SCROLL CSS CDN  ==== -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo $assetPrefix; ?>css/style.css" />
    <link rel="icon" href="<?php echo $assetPrefix; ?>images/ex1.png" type="image/png" />
  </head>

  <!-- ==== ANIMATE ON SCROLL JS CDN -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- ==== GSAP CDN ==== -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.8.0/gsap.min.js"></script>
    <!-- ==== SCRIPT.JS ==== -->
    <script src="<?php echo $assetPrefix; ?>js/script.js" defer></script>
  </body>
</html>

<!-- script -->
<script
      src="https://kit.fontawesome.com/64d58efce2.js"
      crossorigin="anonymous"></script>


