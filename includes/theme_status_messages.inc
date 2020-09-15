<?php

/**
 * @file
 * Override status messages.
 */

/**
 * Returns HTML for status messages.
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
    $output .= '<div class="callout ' . ($status[$type]['class'] ?? $type) . "\" data-closable>\n";
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
    // Add close button.
    $output .= '<button class="close-button" aria-label="Dismiss alert" type="button" data-close>' . "\n";
    $output .= '<span aria-hidden="true">&times;</span>' . "\n";
    $output .= "</button>\n";

    $output .= "</div>\n";
  }
  return $output;
}
