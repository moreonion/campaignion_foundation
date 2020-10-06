<?php

/**
 * @file
 * Override webform date elements.
 */

/**
 * Returns HTML for a webform date component.
 *
 * - Add class 'input-group' to wrapper.
 * - Add custom attribute '#input_group' on children.
 * - Add custom attribute '#label_attributes' on children.
 * - Add input-group classes for child fields and labels.
 *
 * See webform/components/date.inc.
 */
function campaignion_foundation_webform_date($variables) {
  $element = $variables['element'];

  $element['year']['#attributes']['class'][] = 'year';
  $element['month']['#attributes']['class'][] = 'month';
  $element['day']['#attributes']['class'][] = 'day';

  // Add error classes to all items within the element.
  if (form_get_error($element)) {
    $element['year']['#attributes']['class'][] = 'error';
    $element['month']['#attributes']['class'][] = 'error';
    $element['day']['#attributes']['class'][] = 'error';
  }

  // Add HTML5 required attribute, if needed.
  if ($element['#required']) {
    $element['year']['#attributes']['required'] = 'required';
    $element['month']['#attributes']['required'] = 'required';
    $element['day']['#attributes']['required'] = 'required';
  }

  // Pass information from parent to children and set classes.
  $element['year']['#input_group'] = TRUE;
  $element['month']['#input_group'] = TRUE;
  $element['day']['#input_group'] = TRUE;

  $element['year']['#attributes']['class'][] = 'input-group-field';
  $element['month']['#attributes']['class'][] = 'input-group-field';
  $element['day']['#attributes']['class'][] = 'input-group-field';

  $element['year']['#label_attributes']['class'][] = 'input-group-label';
  $element['month']['#label_attributes']['class'][] = 'input-group-label';
  $element['day']['#label_attributes']['class'][] = 'input-group-label';

  $class = array('webform-container-inline input-group');

  // Add the JavaScript calendar if available (provided by Date module package).
  if (!empty($element['#datepicker'])) {
    $class[] = 'webform-datepicker';
    $calendar_class = array('webform-calendar');
    if ($element['#start_date']) {
      $calendar_class[] = 'webform-calendar-start-' . $element['#start_date'];
    }
    if ($element['#end_date']) {
      $calendar_class[] = 'webform-calendar-end-' . $element['#end_date'];
    }
    $calendar_class[] = 'webform-calendar-day-' . variable_get('date_first_day', 0);

    $calendar = theme('webform_calendar', array('component' => $element['#webform_component'], 'calendar_classes' => $calendar_class));
  }

  $output = '';
  $output .= '<div class="' . implode(' ', $class) . '">';
  $output .= drupal_render_children($element);
  $output .= isset($calendar) ? $calendar : '';
  $output .= '</div>';

  return $output;
}
