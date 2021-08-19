<?php

/**
 * @file
 * Override webform managed file elements.
 */

/**
 * Returns HTML for a webform managed file component.
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
  $attributes['data-file-upload'][] = '';
  $attributes['class'][] = 'form-managed-file';
  $attributes['class'][] = 'file-upload';
  // Additional class after a file has been uploaded.
  if (!empty($element['fid']['#value'])) {
    $attributes['class'][] = 'file-selected';
  }

  // Hide native file input.
  $element['upload']['#attributes']['class'][] = 'show-for-sr';

  // Hide Drupal upload button (files will be uploaded via JS).
  $element['upload_button']['#attributes']['class'][] = 'upload';
  $element['upload_button']['#attributes']['class'][] = 'element-hidden';
  // Disable clientside validation.
  $element['upload_button']['#attributes']['class'][] = 'cancel';

  // Styles for the remove button (only visible after submit).
  $element['remove_button']['#attributes']['class'][] = 'small';
  $element['remove_button']['#attributes']['class'][] = 'secondary';
  $element['remove_button']['#attributes']['class'][] = 'button';
  // Disable clientside validation.
  $element['remove_button']['#attributes']['class'][] = 'cancel';

  // Add attributes for fake file button.
  $element['#title'] = t('Choose file');
  $element['#label_attributes']['class'][] = 'small';
  $element['#label_attributes']['class'][] = 'secondary';
  $element['#label_attributes']['class'][] = 'button';

  // Replace file name markup.
  if (!empty($element['filename']['#markup'])) {
    $element['filename']['#markup'] = '<div class="text-input">' . $element['#file']->filename . '</div>';
  }

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
