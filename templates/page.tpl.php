<?php

/**
 * @file
 * Theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in this directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Layout (theme specfic variables):
 * - $has_sidebar: TRUE when there is any content in a sidebar region.
 * - $is_narrow: TRUE when the layout asks for a narrow grid.
 * - $reversed: TRUE when the layout supports displaying the form below the
 *   main content and it is enabled.
 *
 * Regions:
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['header']: Items for the header region.
 * - $page['footer']: Items for the footer region.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 * @see html.tpl.php
 */
?>
  <div id="page" class="<?php print $layout ?? 'default'; ?>-layout">

    <section id="header">
      <div class="top-bar grid-container">
        <?php if ($logo): ?>
        <div class="top-bar-left">
          <a class="logo" href="<?php print $front_page; ?>" rel="home">
            <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
          </a>
        </div>
        <?php endif; ?>
        <div class="top-bar-right">
          <?php if ($tabs = render($tabs)): ?>
          <?php print render($tabs); ?>
          <?php endif; ?>
          <?php print render($page['header']); ?>
        </div>
      </div>
    </section>

    <?php if (!empty($page['highlighted'])): ?>
    <section id="highlighted">
      <div class="grid-container">
        <?php print render($page['highlighted']); ?>
      </div>
    </section>
    <?php endif; ?>

    <?php if ($layout === 'banner' && !empty($background_image)): ?>
    <section id="banner">
      <?php print render($background_image); ?>
    </section>
    <?php endif; ?>

    <?php if (in_array($layout, ['cover-1col', 'cover-2col', 'cover-banner']) && !empty($background_image)): ?>
    <section id="background">
      <?php print render($background_image); ?>
    </section>
    <?php endif; ?>

    <?php if ($messages): ?>
    <section id="messages">
      <div class="grid-container<?php print ($is_narrow ? ' narrow' : ''); ?>">
        <?php print $messages; ?>
      </div>
    </section>
    <?php endif; ?>

    <?php if ($layout === 'cover-banner'): ?>
    <section id="banner-content">
      <div class="grid-container with sidebar">
        <?php if (!empty($page['sidebar_first'])): ?>
        <div id=form-wrapper class="flex-container align-middle">
          <div id="form-outer">
            <?php print render($page['sidebar_first']); ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </section>
    <?php endif; ?>

    <section id="main">
      <div class="grid-container<?php print ($is_narrow ? ' narrow' : ''); ?><?php print ($has_sidebar ? ' with-sidebar' : ''); ?>">

        <?php if ($layout === 'cover-1col'): ?><div class="inner-wrapper"><?php endif; ?>

          <div id="top">
            <?php if ($layout === 'cover-2col'): ?><div class="inner-wrapper"><?php endif; ?>
              <?php if ($title): ?>
              <?php print render($title_prefix); ?>
              <h1 id="page-title"><?php print $title; ?></h1>
              <?php print render($title_suffix); ?>
              <?php endif; ?>

              <?php print render($page['help']); ?>
              <?php if ($action_links): ?>
                <ul class="action-links">
                  <?php print render($action_links); ?>
                </ul>
              <?php endif; ?>

              <?php print render($page['top']); ?>
            <?php if ($layout === 'cover-2col'): ?></div><?php endif; ?>
          </div>

          <?php if (empty($reversed)): ?>
            <?php if ($has_sidebar && $layout !== 'cover-banner'): ?>
              <div id="sidebar">
                <?php if (!empty($page['sidebar_first'])): ?>
                  <div id=form-wrapper class="flex-container align-middle">
                    <div id="form-outer">
                      <?php print render($page['sidebar_first']); ?>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if ($layout !== 'cover-2col'): ?>
                  <?php print render($page['sidebar_second']); ?>
                <?php endif; ?>
              </div>

            <?php elseif ($is_narrow && $layout !== 'cover-banner' && !empty($page['sidebar_first'])): ?>
              <div id="form-outer">
                <?php print render($page['sidebar_first']); ?>
              </div>
            <?php endif; ?>
          <?php endif; ?>

          <div id="content">
            <?php if ($layout === 'cover-2col'): ?>
              <div class="inner-wrapper">
                <?php print render($page['content_top']); ?>
                <?php print render($page['content']); ?>
                <?php print render($page['content_bottom']); ?>
              </div>
              <?php if (!empty($page['sidebar_second'])): ?>
              <div class="inner-wrapper">
                <?php print render($page['sidebar_second']); ?>
              </div>
              <?php endif; ?>

            <?php else: ?>
              <?php print render($page['content_top']); ?>
              <?php print render($page['content']); ?>
              <?php print render($page['content_bottom']); ?>
            <?php endif; ?>
          </div>

          <?php if (!empty($reversed)): ?>
            <?php if ($has_sidebar && $layout !== 'cover-banner'): ?>
              <div id="sidebar">
                <?php if (!empty($page['sidebar_first'])): ?>
                  <div id=form-wrapper class="flex-container align-middle">
                    <div id="form-outer">
                      <?php print render($page['sidebar_first']); ?>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if ($layout !== 'cover-2col'): ?>
                  <?php print render($page['sidebar_second']); ?>
                <?php endif; ?>
              </div>

            <?php elseif ($is_narrow && $layout !== 'cover-banner' && !empty($page['sidebar_first'])): ?>
              <div id="form-outer">
                <?php print render($page['sidebar_first']); ?>
              </div>
            <?php endif; ?>
          <?php endif; ?>

          <?php if ($layout === 'cover-banner' && $has_sidebar): ?>
          <div id="sidebar">
            <?php print render($page['sidebar_second']); ?>
          </div>
          <?php endif; ?>

        <?php if ($layout === 'cover-1col'): ?>
          <?php print render($page['sidebar_second']); ?>
        </div>
        <?php endif; ?>

      </div>
    </section>

    <?php if (!empty($page['bottom'])): ?>
    <section id="bottom">
      <div class="grid-container">
        <?php print render($page['bottom']); ?>
      </div>
    </section>
    <?php endif; ?>

  </div>

  <?php if (!empty($page['footer'])): ?>
  <section id="footer">
    <div class="grid-container">
      <?php print render($page['footer']); ?>
    </div>
  </section>
  <?php endif; ?>
