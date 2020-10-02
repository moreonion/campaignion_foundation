<?php

/**
 * @file
 * Template for the Campaignion Foundation Theme.
 */

use Drupal\little_helpers\ElementTree;

include 'includes/theme_filter_guidelines.inc';
include 'includes/theme_form_element.inc';
include 'includes/theme_form_element_label.inc';
include 'includes/theme_menu_local_tasks.inc';
include 'includes/theme_pager.inc';
include 'includes/theme_recent_supporters.inc';
include 'includes/theme_status_messages.inc';
include 'includes/theme_text_format_wrapper.inc';
include 'includes/theme_views_mini_pager.inc';
include 'includes/theme_webform_date.inc';
include 'includes/theme_webform_element.inc';
include 'includes/theme_webform_managed_file.inc';
include 'includes/theme_webform_time.inc';

/**
 * Implements hook_campaignion_layout_info().
 *
 * Make theme layouts available.
 */
function campaignion_foundation_campaignion_layout_info() {
  $info['banner'] = [
    'title' => t('Big banner layout'),
    'fields' => [
      'layout_background_image' => [
        'variable' => 'background_image',
        'display' => [],
      ],
    ],
  ];
  return $info;
}

/**
 * Prepares variables for html templates.
 */
function campaignion_foundation_preprocess_html(&$vars) {
  $query_string = variable_get('css_js_query_string', '0');
  $css_path = theme_get_setting('foundation_assets_css');
  drupal_add_css("$css_path?$query_string", [
    'type' => 'external',
    'group' => CSS_THEME,
    'every_page' => TRUE,
  ]);
  $js_path = theme_get_setting('foundation_assets_js');
  drupal_add_js("$js_path?$query_string", [
    'type' => 'external',
    'scope' => 'footer',
    'group' => JS_THEME,
    'every_page' => TRUE,
  ]);
}

/**
 * Prepares variables for node templates.
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
 * Prepares variables for node processing.
 */
function campaignion_foundation_process_node(&$vars) {
  // Render custom attribute arrays.
  // See template_process() in drupal/includes/theme.inc.
  $vars['header_attributes'] = !empty($vars['header_attributes_array']) ? drupal_attributes($vars['header_attributes_array']) : '';
  $vars['footer_attributes'] = !empty($vars['footer_attributes_array']) ? drupal_attributes($vars['footer_attributes_array']) : '';
}

/**
 * Prepares variables for page templates.
 */
function campaignion_foundation_preprocess_page(&$vars) {
  // Define the fields which constitute the form fields.
  $vars['form_blocks'] = ['cck_blocks_field_form_intro', 'cck_blocks_pgbar_default', 'webform_block_current'];
}

/**
 * Prepares variables for block templates.
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
  // Add classes to blocks.
  if ($vars['block']->module == 'share_light') {
    $vars['classes_array'][] = 'share-buttons';
  }
}

/**
 * Prepares variables for file entity templates.
 */
function campaignion_foundation_preprocess_file_entity(&$vars) {
  // Add class for responsive videos and full-width images.
  if ($vars['type'] == 'video' && $vars['content']['file']['#theme'] !== 'image_style') {
    $vars['classes_array'][] = 'responsive-embed';
    $vars['classes_array'][] = 'widescreen';
    $vars['classes_array'][] = 'media-stretch';
  }
  if (($vars['content']['file']['#image_style'] ?? '') == 'full') {
    $vars['classes_array'][] = 'media-stretch';
  }
  // Remove wrapper class for disabled contextual links.
  if ($key = array_search('contextual-links-region', $vars['classes_array'])) {
    unset($vars['classes_array'][$key]);
  }
}

/**
 * Prepares variables for webform form templates.
 */
function campaignion_foundation_preprocess_webform_form(&$vars) {
  // Remove webform fields that start with 'below_button' from the form and push
  // them to the end of the form array so they are rendered after the buttons.
  $identifier = 'below_button';
  $below_button = array();
  foreach ($vars['form']['submitted'] as $key => $value) {
    if (substr($key, 0, strlen($identifier)) === $identifier) {
      $below_button[$key] = $value;
      unset($vars['form']['submitted'][$key]);
    }
  }
  $vars['form']['below_button'] = $below_button;
}

/**
 * Prepares variables for campaignion language switcher templates.
 */
function campaignion_foundation_preprocess_campaignion_language_switcher(&$vars) {
  // Save the currently active link into $active_link and remove it from the
  // list of links. (With GeoIP enabled, the path might not match the actual
  // node path and no link is considered active, therefore we have to fake a
  // default active link.)
  $active_link = [
    'renderable' => [
      '#text' => t('Choose country'),
      '#path' => '#',
      '#theme' => 'link',
      '#options' => ['attributes' => []],
    ],
  ];
  if (count($vars['links_accessible']) == 1) {
    // If there is just one accessible link, that has to be the active link.
    $active_link = array_pop($vars['links_accessible']);
    array_pop($vars['links']);
  }
  else {
    // Look for a link with the class "active".
    foreach ($vars['links_accessible'] as $key => $link) {
      if (in_array('active', $link['li_attributes']['class'])) {
        $active_link = $link;
        unset($vars['links_accessible'][$key]);
        unset($vars['links'][$key]);
        break;
      }
    }
  }
  // Make the active link available to the template.
  $vars['active_link'] = $active_link;
  // Add classes and attributes.
  $vars['classes_array'][] = 'dropdown';
  $vars['classes_array'][] = 'menu';
  $vars['attributes_array']['data-dropdown-menu'] = 'true';
  $vars['attributes_array']['data-disable-hover'] = 'true';
  $vars['attributes_array']['data-click-open'] = 'true';
}

/**
 * Implements hook_css_alter().
 *
 * Remove annoying Drupal core CSS files.
 */
function campaignion_foundation_css_alter(&$css) {
  $exclude = ['webform.css', 'filter.css', 'recent-supporters.css'];
  foreach ($css as $path => $values) {
    // Remove exclusion list and files where the name starts with "system"
    // (e.g. system.base.css).
    if (in_array(basename($path), $exclude) || strpos(basename($path), 'system') === 0) {
      unset($css[$path]);
    }
  }
}

/**
 * Implements hook_js_alter().
 *
 * Remove annoying Drupal core JS files.
 */
function campaignion_foundation_js_alter(&$js) {
  $exclude = ['campaignion_language_switcher.js'];
  foreach ($js as $path => $values) {
    if (in_array(basename($path), $exclude)) {
      unset($js[$path]);
    }
  }
}

/**
 * Implements hook_contextual_links_view_alter().
 *
 * Disable contextual links on certain elements: files, excluded blocks.
 */
function campaignion_foundation_contextual_links_view_alter(&$element, $items) {
  $file = $element['#element']['#file'] ?? NULL;
  $block = $element['#element']['#block'] ?? NULL;
  if ($file || ($block && _campaignion_foundation_exclude_block_from_contextual_links($block->module))) {
    unset($element['#links']);
  }
}

/**
 * Implements hook_block_view_alter().
 */
function campaignion_foundation_block_view_alter(&$data, $block) {
  // Add button classes for share light blocks.
  if ($block->module == 'share_light') {
    $data['content']['#attributes']['class'][] = 'no-bullet';
    if (isset($data['content']['#links'])) {
      foreach ($data['content']['#links'] as &$link) {
        $link['attributes']['class'][] = 'large';
        $link['attributes']['class'][] = 'share';
        $link['attributes']['class'][] = 'button';
        $link['attributes']['class'][] = 'expanded';
        $link['attributes']['class'][] = $link['attributes']['data-share'] . '-icon';
      }
    }
  }
}

/**
 * Implements hook_form_alter().
 */
function campaignion_foundation_form_alter(&$form, $form_state, $form_id) {
  if (empty($form['actions']['#type']) || $form['actions']['#type'] !== 'actions') {
    return;
  }
  // Edit submit button classes.
  $classes = ['large', 'expanded', 'primary', 'button'];
  foreach (array_values(['next', 'submit']) as $type) {
    $button_classes = &$form['actions'][$type]['#attributes']['class'];
    // Add submit button classes.
    foreach (array_values($classes) as $class) {
      $button_classes[] = $class;
    }
    // Remove `button-primary` class added by webform. We use just `primary`.
    $x = array_search('button-primary', $button_classes);
    unset($button_classes[$x]);
  }
  // Don’t wrap form buttons in container.
  $form['actions']['#theme_wrappers'] = [];
  // Hide step buttons.
  $form['step_buttons']['#attributes']['class'][] = 'show-for-sr';
  $form['actions']['previous']['#attributes']['class'][] = 'show-for-sr';

  // Add wrapper class for extra space on some form elements.
  $elements = $form;
  if (!empty($form['submitted'])) {
    $elements = &$form['submitted'];
  }
  ElementTree::applyRecursively($elements, function (&$element, $key, &$parent) {
    $element_types = ['radio', 'checkbox', 'radios', 'checkboxes'];
    if (in_array($element['#type'] ?? '', $element_types)) {
      $element['#wrapper_attributes']['class'][] = 'extra-spacing';
    }
  });
}

/**
 * Implements hook_webform_component_render_alter().
 */
function campaignion_foundation_webform_component_render_alter(&$element, $component) {
  // Add extra classes for donation amount radios.
  if ($element['#webform_component']['form_key'] == 'donation_amount') {
    if (in_array($element['#type'], ['radios', 'select_or_other'])) {
      $element['#wrapper_attributes']['class'][] = 'donation-amount';
      $element['#attributes']['class'][] = 'donation-amount-buttons';
    }
  }
}

/**
 * Implements hook_element_info_alter().
 */
function campaignion_foundation_element_info_alter(&$type) {
  // Add custom pre-render function to select elements.
  if (isset($type['select'])) {
    $type['select']['#pre_render'][] = '_campaignion_foundation_pre_render_select';
  }
}

/**
 * Add data-select-two attribute to select elements.
 *
 * This lets the Foundation SelectTwo plugin discover the select elements.
 */
function _campaignion_foundation_pre_render_select($element) {
  $element['#attributes']['data-select-two'] = "select-two";
  return $element;
}

/**
 * Helper function to keep exclusion list for contextual links in one place.
 */
function _campaignion_foundation_exclude_block_from_contextual_links($module) {
  $exclude = [
    'cck_blocks',
    'webform_block',
    'pgbar',
    'recent_supporters',
    'share_light',
    'campaignion_language_switcher',
  ];
  return in_array($module, $exclude);
}
