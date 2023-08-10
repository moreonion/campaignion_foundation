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
 */

  $foundation_assets_css = theme_get_setting('foundation_assets_css') . '?' . variable_get('css_js_query_string', '0');

?><!DOCTYPE html>
<html lang="<?php print $language->language; ?>" class="no-js">

<head>
  <?php print $head; ?>
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php print $head_title; ?></title>

  <?php print $styles; ?>
  <?php print $scripts; ?>
  <link type="text/css" rel="stylesheet" href="<?php print $foundation_assets_css; ?>" media="all">
</head>

<body class="<?php print $classes; ?>" <?php print $attributes;?>>

  <div id="page">

    <section id="header" role="banner">
      <div class="top-bar grid-container">
        <?php if ($logo): ?>
        <div class="top-bar-left">
          <a class="logo" href="<?php print $front_page; ?>" rel="home">
            <img src="<?php print $logo; ?>" alt="" />
          </a>
        </div>
        <?php endif; ?>
        <div class="top-bar-right">
          <?php print render($page['header']); ?>
        </div>
      </div>
    </section>

    <section id="main" role="main">
      <div class="grid-container narrow">
        <div id="top">
          <?php if ($title): ?>
          <h1 id="page-title"><?php print $title; ?></h1>
          <?php endif; ?>
        </div>

        <div id="content">
          <p><?php print $content; ?></p>
          <?php print $messages; ?>
        </div>
      </div>
    </section>

  </div>

</body>
</html>
