<?php

/**
 * @file
 * Override form elements.
 */

/**
 * Returns HTML for a form element.
 *
 * - Replace description class: 'description' → 'help-text'.
 * - Omit wrapper for elements in input-groups.
 *
 * See drupal/includes/form.inc.
 */
function campaignion_foundation_form_element(&$variables) {
  $element =& $variables['element'];

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }

  // Add element's #type and #name as class to aid with JS/CSS selectors.
  $attributes['class'] = array(
    'form-item',
  );
  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(
      ' ' => '-',
      '_' => '-',
      '[' => '-',
      ']' => '',
    ));
  }

  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }

  // Add wrapper unless the field is part of an input group.
  $output = empty($element['#input_group']) ? '<div' . drupal_attributes($attributes) . '>' . "\n" : '';

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';
  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':

      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }
  if (!empty($element['#description'])) {
    $output .= '<div class="help-text">' . $element['#description'] . "</div>\n";
  }
  $output .= empty($element['#input_group']) ? "</div>\n" : '';
  return $output;
}
