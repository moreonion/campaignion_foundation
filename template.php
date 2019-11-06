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
