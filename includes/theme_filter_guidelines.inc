<?php

/**
 * @file
 * Override filter guidelines.
 */

/**
 * Returns HTML for filter guidelines.
 *
 * Add class 'help-text'.
 *
 * See drupal/modules/filter/filter.module.
 */
function campaignion_foundation_filter_guidelines($variables) {
  $format = $variables['format'];
  $attributes['class'][] = 'help-text';
  $attributes['class'][] = 'filter-guidelines-item';
  $attributes['class'][] = 'filter-guidelines-' . $format->format;
  $output = '<div' . drupal_attributes($attributes) . '>';
  $output .= '<h3>' . check_plain($format->name) . '</h3>';
  $output .= theme('filter_tips', array(
    'tips' => _filter_tips($format->format, FALSE),
  ));
  $output .= '</div>';
  return $output;
}
