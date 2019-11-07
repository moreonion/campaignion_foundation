<?php

/**
 * @file
 * Override webform elements.
 */

/**
 * Implements theme_webform_element().
 *
 * Replace description class: 'description' → 'help-text'.
 *
 * See webform/webform.module.
 */
function campaignion_foundation_webform_element(&$variables) {
  $element = $variables['element'];

  $output = '<div ' . drupal_attributes($element['#wrapper_attributes']) . '>' . "\n";
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . webform_filter_xss($element['#field_prefix']) . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . webform_filter_xss($element['#field_suffix']) . '</span>' : '';

  // Generate description for above or below the field.
  $above = !empty($element['#webform_component']['extra']['description_above']);
  $description = array(
    FALSE => '',
    TRUE => !empty($element['#description']) ? ' <div class="help-text">' . $element['#description'] . "</div>\n" : '',
  );

  // If #children does not contain an element with a matching @id, do not
  // include @for in the label.
  if (isset($variables['element']['#id']) && strpos($element['#children'], ' id="' . $variables['element']['#id'] . '"') === FALSE) {
    $variables['element']['#id'] = NULL;
  }

  // Determine whether or not this element has form control children. If so and
  // if webform_fieldset_wrap is TRUE, wrap them in a fieldset and use legend
  // instead of label.
  $has_element_children = FALSE;
  if (webform_variable_get('webform_fieldset_wrap')) {
    foreach (array_keys($element) as $key) {
      if (substr($key, 0, 1) !== '#') {
        $has_element_children = TRUE;
        break;
      }
    }
  }

  if ($has_element_children) {
    $output .= '<fieldset class="fieldset-invisible">';
  }

  switch ($element['#title_display']) {
    case 'inline':
      $output .= $description[$above];
      $description[$above] = '';
    case 'before':
    case 'invisible':
    case 'after':
      if ($has_element_children) {
        $title = '<legend>' . $element['#title'];

        if ($element['#required']) {
          $title .= ' ' . theme('form_required_marker', $variables);
        }

        $title .= '</legend>';
      }
      else {
        $title = ' ' . theme('form_element_label', $variables);
      }
      break;
  }

  $children = ' ' . $description[$above] . $prefix . $element['#children'] . $suffix;
  switch ($element['#title_display']) {
    case 'inline':
    case 'before':
    case 'invisible':
      $output .= $title;
      $output .= $children;
      break;

    case 'after':
      $output .= $children;
      $output .= $title;
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= $children;
      break;
  }
  $output .= "\n";

  $output .= $description[!$above];

  if ($has_element_children) {
    $output .= '</fieldset>';
  }

  $output .= "</div>\n";

  return $output;
}
