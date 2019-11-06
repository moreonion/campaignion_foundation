<?php

/**
 * @file
 * Template for the Campaignion Foundation Theme.
 */

/**
 * Modify html variables, add assets.
 */
function campaignion_foundation_preprocess_html(&$vars) {
  drupal_add_css(theme_get_setting('foundation_assets_css'), [
    'type' => 'external',
    'group' => CSS_THEME,
    'every_page' => TRUE,
  ]);
  drupal_add_js(theme_get_setting('foundation_assets_js'), [
    'type' => 'external',
    'scope' => 'footer',
    'group' => JS_THEME,
    'every_page' => TRUE,
    'requires_jquery' => FALSE,
  ]);
}

/**
 * Remove annoying Drupal core CSS files.
 */
function campaignion_foundation_css_alter(&$css) {
  $blacklist = ['webform.css'];
  foreach ($css as $path => $values) {
    // Remove blacklist and files where the name starts with "system"
    // (e.g. system.base.css).
    if (in_array(basename($path), $blacklist) || strpos(basename($path), 'system') === 0) {
      unset($css[$path]);
    }
  }
}
