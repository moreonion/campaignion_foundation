<?php

/**
 * @file
 * Override webform managed file elements.
 */

/**
 * Implements theme_webform_managed_file().
 *
 * - Hide native file input (can’t be styled).
 * - Hide superfluous buttons.
 * - Add a custom file button instead.
 *
 * See webform/components/file.inc.
 */
function campaignion_foundation_webform_managed_file(&$variables) {
  $element = $variables['element'];

  $attributes = $element['#attributes'] ?? array();
  $attributes['class'][] = 'form-managed-file';

  // Hide native file input.
  $element['upload']['#attributes']['class'][] = 'show-for-sr';

  // Hide Drupal upload button (files will be uploaded on submit).
  $element['upload_button']['#access'] = FALSE;

  // Styles for the remove button (only visible after submit).
  $element['remove_button']['#attributes']['class'][] = 'button';
  $element['remove_button']['#attributes']['class'][] = 'clear';

  // Add attributes for fake file button.
  $element['#title'] = t('Choose file');
  $element['#label_attributes']['class'][] = 'small';
  $element['#label_attributes']['class'][] = 'secondary';
  $element['#label_attributes']['class'][] = 'button';

  // This wrapper is required to apply JS behaviors and CSS styling.
  $output = '';
  $output .= '<div' . drupal_attributes($attributes) . '>';
  $output .= drupal_render_children($element);
  if (empty($element['fid']['#value'])) {
    // Add a second label that looks like a button to trigger the file dialoge.
    $output .= theme('form_element_label', ['element' => $element]);
  }
  $output .= '</div>';
  return $output;
}
