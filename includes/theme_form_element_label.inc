<?php

/**
 * @file
 * Override form element labels.
 */

/**
 * Implements theme_form_element_label().
 *
 * Add label attributes.
 *
 * See drupal/includes/form.inc.
 */
function campaignion_foundation_form_element_label($variables) {
  $element = $variables['element'];

  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // If title and required marker are both empty, output no label.
  if ((!isset($element['#title']) || $element['#title'] === '') && empty($element['#required'])) {
    return '';
  }

  // If the element is required, a required marker is appended to the label.
  $required = !empty($element['#required']) ? theme('form_required_marker', array(
    'element' => $element,
  )) : '';
  $title = filter_xss_admin($element['#title']);
  $attributes = $element['#label_attributes'] ?? array();

  // Style the label as class option to display inline with the element.
  if ($element['#title_display'] == 'after') {
    $attributes['class'][] = 'option';
  }
  elseif ($element['#title_display'] == 'invisible') {
    $attributes['class'][] = 'element-invisible';
  }
  if (!empty($element['#id'])) {
    $attributes['for'] = $element['#id'];
  }

  // The leading whitespace helps visually separate fields from inline labels.
  return ' <label' . drupal_attributes($attributes) . '>' . $t('!title !required', array(
    '!title' => $title,
    '!required' => $required,
  )) . "</label>\n";
}
