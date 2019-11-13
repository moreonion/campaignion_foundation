<?php

/**
 * @file
 * Template for the Campaignion Foundation Theme.
 */

include 'includes/theme_form_element.inc';
include 'includes/theme_form_element_label.inc';
include 'includes/theme_menu_local_tasks.inc';
include 'includes/theme_status_messages.inc';
include 'includes/theme_webform_date.inc';
include 'includes/theme_webform_element.inc';
include 'includes/theme_webform_managed_file.inc';
include 'includes/theme_webform_time.inc';

/**
 * Modify html variables, add assets.
 */
function campaignion_foundation_preprocess_html(&$vars) {
  drupal_add_css(theme_get_setting('foundation_assets_css'), [
    'type' => 'external',
    'group' => CSS_THEME,
    'every_page' => TRUE,
  ]);
  drupal_add_js(theme_get_setting('foundation_assets_js'), [
    'type' => 'external',
    'scope' => 'footer',
    'group' => JS_THEME,
    'every_page' => TRUE,
    'requires_jquery' => FALSE,
  ]);
}

/**
 * Modify node variables.
 */
function campaignion_foundation_preprocess_node(&$vars) {
  // Add 'content' class to attributes array instead of hardcoding it in the
  // node template so more classes can be added if needed.
  $vars['content_attributes_array']['class'][] = 'content';
  // Add card classes to teasers.
  if (!empty($vars['teaser'])) {
    $vars['classes_array'][] = 'card';
    $vars['header_attributes_array']['class'][] = 'card-section';
    $vars['content_attributes_array']['class'][] = 'card-section';
    $vars['footer_attributes_array']['class'][] = 'card-section';
    $vars['content']['links']['#attributes']['class'][] = 'no-bullet';
  }
}

/**
 * Modify node processing.
 */
function campaignion_foundation_process_node(&$vars) {
  // Render custom attribute arrays.
  // See template_process() in drupal/includes/theme.inc.
  $vars['header_attributes'] = !empty($vars['header_attributes_array']) ? drupal_attributes($vars['header_attributes_array']) : '';
  $vars['footer_attributes'] = !empty($vars['footer_attributes_array']) ? drupal_attributes($vars['footer_attributes_array']) : '';
}

/**
 * Modify block variables.
 */
function campaignion_foundation_preprocess_block(&$vars) {
  // Add 'content' class to attributes array instead of hardcoding it in the
  // block template so more classes can be added if needed.
  $vars['content_attributes_array']['class'][] = 'content';
  // Remove wrapper class for disabled contextual links.
  if (_campaignion_foundation_exclude_block_from_contextual_links($vars['block']->module)) {
    if ($key = array_search('contextual-links-region', $vars['classes_array'])) {
      unset($vars['classes_array'][$key]);
    }
  }
  // Hide language switcher block title.
  if ($vars['block']->module == 'campaignion_language_switcher') {
    $vars['title_attributes_array']['class'][] = 'show-for-sr';
  }
}

/**
 * Remove annoying Drupal core CSS files.
 */
function campaignion_foundation_css_alter(&$css) {
  $blacklist = ['webform.css'];
  foreach ($css as $path => $values) {
    // Remove blacklist and files where the name starts with "system"
    // (e.g. system.base.css).
    if (in_array(basename($path), $blacklist) || strpos(basename($path), 'system') === 0) {
      unset($css[$path]);
    }
  }
}

/**
 * Disable contextual links on certain blocks.
 */
function campaignion_foundation_contextual_links_view_alter(&$element, $items) {
  $block = $element['#element']['#block'] ?? NULL;
  if ($block && _campaignion_foundation_exclude_block_from_contextual_links($block->module)) {
    unset($element['#links']);
  }
}

/**
 * Override forms.
 */
function campaignion_foundation_form_alter(&$form, $form_state, $form_id) {
  if (empty($form['actions']['#type']) || $form['actions']['#type'] !== 'actions') {
    return;
  }
  // Add submit button classes.
  $button_classes = ['small-only-expanded', 'primary', 'button'];
  foreach (array_values($button_classes) as $class) {
    $form['actions']['next']['#attributes']['class'][] = $class;
    $form['actions']['submit']['#attributes']['class'][] = $class;
  }
  // Don’t wrap form buttons in container.
  $form['actions']['#theme_wrappers'] = [];
  // Hide step buttons.
  $form['step_buttons']['#attributes']['class'][] = 'show-for-sr';
  $form['actions']['previous']['#attributes']['class'][] = 'show-for-sr';
}

/**
 * Helper function to keep blacklist for contextual links in one place.
 */
function _campaignion_foundation_exclude_block_from_contextual_links($module) {
  $blacklist = [
    'cck_blocks',
    'webform_block',
    'pgbar',
    'recent_supporters',
    'share_light',
    'campaignion_language_switcher',
  ];
  return in_array($module, $blacklist);
}
