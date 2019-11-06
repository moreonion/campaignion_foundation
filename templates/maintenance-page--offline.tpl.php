<?php

/**
 * @file
 * Default theme implementation to display a single Drupal page while offline.
 *
 * All the available variables are mirrored in html.tpl.php and page.tpl.php.
 * Some may be blank but they are provided for consistency.
 *
 * @see template_preprocess()
 * @see template_preprocess_maintenance_page()
 *
 * @ingroup themeable
 */

  $head_title = 'Maintenance';
  $title = 'Sorry!';
  $logo = path_to_theme() . '/logo.png';
  // If your theme is set to display the site name, uncomment this line and
  // replace the value:
  // $site_name = 'Your Site Name';
  // If your theme is set to *not* display the site name, uncomment this line:
  unset($site_name);
  // If your theme is set to display the site slogan, uncomment this line and
  // replace the value:
  // $site_slogan = 'My Site Slogan';
  // If your theme is set to *not* display the site slogan, uncomment this line:
  unset($site_slogan);
  // Main message. Note HTML markup.
  $content = '<p>This site is currently under maintenance. We should be back shortly. Thank you for your patience.</p>';

?><!DOCTYPE html>
<html lang="<?php print $language->language; ?>" class="no-js">

<head>
  <?php print $head; ?>
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php print $head_title; ?></title>

  <?php print $styles; ?>
  <?php print $scripts; ?>
</head>

<body class="<?php print $classes; ?>" <?php print $attributes;?>>

  <div id="page">

    <section id="header" class="grid-container-fluid">
      <div class="top-bar">
        <?php if ($logo): ?>
        <div class="top-bar-left">
          <a class="logo" href="<?php print $front_page; ?>" rel="home">
            <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
          </a>
        </div>
        <?php endif; ?>
        <div class="top-bar-right">
          <?php print render($page['header']); ?>
        </div>
      </div>
    </section>

    <section id="main" class="grid-container-fluid">
      <div class="grid-x grid-padding-x grid-padding-y">

        <div class="cell medium-offset-1 medium-9 medium-offset-1">

          <?php if ($title): ?>
          <h1 class="title" id="page-title"><?php print $title; ?></h1>
          <?php endif; ?>

          <?php print $content; ?>

          <?php if ($messages): ?>
          <div id="messages">
            <div class="section clearfix"><?php print $messages; ?></div>
          </div>
          <?php endif; ?>

        </div>
      </div>
    </section>

  </div>

</body>
</html>
