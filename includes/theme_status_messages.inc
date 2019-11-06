<?php

/**
 * @file
 * Override status messages.
 */

/**
 * Implements theme_status_messages().
 *
 * - Add class 'callout'.
 * - Add status classes and use this class instead of type.
 * - Replace 'element-invisible' → 'show-for-sr'.
 *
 * See drupal/includes/theme.inc.
 */
function campaignion_foundation_status_messages($variables) {
  $output = '';
  $status = [
    'status'  => ['heading' => t('Status message'), 'class' => 'success'],
    'error'   => ['heading' => t('Error message'), 'class' => 'alert'],
    'warning' => ['heading' => t('Warning message'), 'class' => 'warning'],
  ];
  foreach (drupal_get_messages($variables['display']) as $type => $messages) {
    $output .= '<div class="callout ' . ($status[$type]['class'] ?? $type) . "\">\n";
    if (!empty($status[$type]['heading'])) {
      $output .= '<h2 class="show-for-sr">' . $status[$type]['heading'] . "</h2>\n";
    }
    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= reset($messages);
    }
    $output .= "</div>\n";
  }
  return $output;
}
