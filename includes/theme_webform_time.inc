<?php

/**
 * @file
 * Override webform time elements.
 */

/**
 * Implements theme_webform_date().
 *
 * - Add class 'input-group' to wrapper.
 * - Add custom attribute '#input_group' on children.
 * - Add custom attribute '#label_attributes' on children.
 * - Add input-group classes for child fields and labels.
 *
 * See webform/components/time.inc.
 */
function campaignion_foundation_webform_time($variables) {
  $element = $variables['element'];

  $element['hour']['#attributes']['class'][] = 'hour';
  $element['minute']['#attributes']['class'][] = 'minute';

  // Add error classes to all items within the element.
  if (form_get_error($element)) {
    $element['hour']['#attributes']['class'][] = 'error';
    $element['minute']['#attributes']['class'][] = 'error';
  }

  // Add HTML5 required attribute, if needed.
  if ($element['#required']) {
    $element['hour']['#attributes']['required'] = 'required';
    $element['minute']['#attributes']['required'] = 'required';
    if (!empty($element['ampm'])) {
      $element['ampm']['am']['#attributes']['required'] = 'required';
      $element['ampm']['pm']['#attributes']['required'] = 'required';
    }
  }

  // Pass information from parent to children and set classes.
  $element['hour']['#input_group'] = TRUE;
  $element['minute']['#input_group'] = TRUE;

  $element['hour']['#attributes']['class'][] = 'input-group-field';
  $element['minute']['#attributes']['class'][] = 'input-group-field';

  $element['hour']['#label_attributes']['class'][] = 'input-group-label';
  $element['minute']['#label_attributes']['class'][] = 'input-group-label';

  $output = '<div class="webform-container-inline input-group">' . drupal_render($element['hour']) . drupal_render($element['minute']) . drupal_render($element['ampm']) . '</div>';

  return $output;
}
