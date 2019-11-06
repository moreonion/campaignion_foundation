<?php

/**
 * @file
 * Override menu local tasks (=tabs).
 */

/**
 * Implements theme_menu_local_tasks().
 *
 * - Replace tabs with menu classes.
 * - Add 'admin' class to identify the menu.
 * - Replace 'element-invisible' → 'show-for-sr'.
 * - Remove menu active item.
 *
 * See drupal/includes/menu.inc.
 */
function campaignion_foundation_menu_local_tasks(&$vars) {
  $output = '';
  if (!empty($vars['primary'])) {
    $vars['primary']['#prefix'] = '<h2 class="show-for-sr">' . t('Primary tabs') . '</h2>';
    $vars['primary']['#prefix'] .= '<ul class="admin menu dropdown">';
    $vars['primary']['#suffix'] = '</ul>';
    // Remove active item.
    if (empty($vars['secondary'])) {
      foreach ($vars['primary'] as $i => $item) {
        if (!empty($item['#active'])) {
          unset($vars['primary'][$i]);
        }
      }
    }
    $output .= drupal_render($vars['primary']);
  }
  if (!empty($vars['secondary'])) {
    $vars['secondary']['#prefix'] = '<h2 class="show-for-sr">' . t('Secondary tabs') . '</h2>';
    $vars['secondary']['#prefix'] .= '<ul class="admin menu vertical">';
    $vars['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($vars['secondary']);
    // Remove active item.
    foreach ($vars['secondary'] as $i => $item) {
      if (!empty($item['#active'])) {
        unset($vars['secondary'][$i]);
      }
    }
  }
  return $output;
}
