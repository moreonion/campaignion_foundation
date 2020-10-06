<?php

/**
 * @file
 * Override text format wrapper.
 */

/**
 * Returns HTML for a text format wrapper.
 *
 * Replace description class: 'description' → 'help-text'.
 *
 * See drupal/modules/filter/filter.module.
 */
function campaignion_foundation_text_format_wrapper($variables) {
  $element = $variables['element'];
  $output = '<div class="text-format-wrapper">';
  $output .= $element['#children'];
  if (!empty($element['#description'])) {
    $output .= '<div class="help-text">' . $element['#description'] . '</div>';
  }
  $output .= "</div>\n";
  return $output;
}
