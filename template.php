<?php

/**
 * @file
 * Template for the Campaignion Foundation Theme.
 */

use Drupal\little_helpers\ElementTree;

include 'includes/theme_filter_guidelines.inc';
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
  $info['default'] = [
    'title' => t('A: Standard (2 columns)'),
    'reversable' => TRUE,
  ];
  $info['banner'] = [
    'title' => t('B: Banner image (2 columns)'),
    'fields' => [
      'layout_background_image' => [
        'variable' => 'background_image',
        'display' => [],
      ],
      'layout_headline' => [
        'variable' => 'headline',
        'display' => [],
      ],
    ],
    'reversable' => TRUE,
  ];
  $info['cover-2col'] = [
    'title' => t('C: Fixed background image (2 columns)'),
    'fields' => [
      'layout_background_image' => [
        'variable' => 'background_image',
        'display' => [],
      ],
      'layout_headline' => [
        'variable' => 'headline',
        'display' => [],
      ],
    ],
    'reversable' => TRUE,
  ];
  $info['cover-banner'] = [
    'title' => t('D: Fixed banner image (2 columns)'),
    'fields' => [
      'layout_background_image' => [
        'variable' => 'background_image',
        'display' => [],
      ],
      'layout_headline' => [
        'variable' => 'headline',
        'display' => [],
      ],
    ],
  ];
  $info['cover-1col'] = [
    'title' => t('E: Fixed background image (1 column)'),
    'fields' => [
      'layout_background_image' => [
        'variable' => 'background_image',
        'display' => [],
      ],
      'layout_headline' => [
        'variable' => 'headline',
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
    'preprocess' => FALSE,
    'attributes' => ['type' => 'module'],
  ]);
}

/**
 * Prepares variables for page templates.
 */
function campaignion_foundation_preprocess_page(&$vars) {
  // Layout helper variables.
  $vars['layout'] = $vars['layout'] ?? 'default';
  $is_single_column = in_array($vars['layout'], ['cover-1col']);
  $teaser_blocks = ['views_actions-block', 'views_actions_promoted-block'];
  $content_blocks = ($vars['page']['content_top'] ?? []) + ($vars['page']['content'] ?? []) + ($vars['page']['content_bottom'] ?? []);
  $content_blocks = element_children($content_blocks);
  $sidebar_blocks = ($vars['page']['sidebar_first'] ?? []) + ($vars['page']['sidebar_second'] ?? []);
  $sidebar_blocks = element_children($sidebar_blocks);
  $has_teasers = current_path() == 'node' || array_intersect($teaser_blocks, $content_blocks);
  $has_sidebar = !empty($sidebar_blocks);
  if ($vars['layout'] === 'cover-banner' && $has_sidebar) {
    $has_sidebar = !empty($vars['page']['sidebar_second']);
  }
  $vars['has_sidebar'] = $has_sidebar && !$is_single_column;
  $vars['is_narrow'] = $is_single_column || (!$has_sidebar && !$has_teasers);
  // Layout config variables.
  $vars['highlighted_grid'] = theme_get_setting('grid_options_highlighted') ?? 'default';
  $vars['bottom_grid'] = theme_get_setting('grid_options_bottom') ?? 'default';
  // Page classes.
  $vars['page_classes'] = drupal_clean_css_identifier($vars['layout']) . '-layout';
  if (!empty($vars['headline'])) {
    $vars['page_classes'] .= " with-headline";
  }
}

/**
 * Prepares variables for node templates.
 */
function campaignion_foundation_preprocess_node(&$vars) {
  // Add 'content' class to attributes array instead of hardcoding it in the
  // node template so more classes can be added if needed.
  $vars['content_attributes_array']['class'][] = 'content';

  // Add card classes to teasers and customize the read-more link.
  if (!empty($vars['teaser'])) {
    $vars['classes_array'][] = 'card';
    $vars['header_attributes_array']['class'][] = 'card-section';
    $vars['content_attributes_array']['class'][] = 'card-section';
    $vars['footer_attributes_array']['class'][] = 'card-section';
    $vars['content']['links']['#attributes']['class'][] = 'no-bullet';

    $action_types = [
      'webform',
      'petition',
      'email_to_target',
      'match_to_target',
    ];
    foreach ($vars['content']['links']['node']['#links'] as $name => &$link) {
      foreach (['button', 'small'] as $class) {
        $link['attributes']['class'][] = $class;
      }
      if ($name == 'node-readmore') {
        $link['attributes']['class'][] = 'card-link';
        // Remove link title.
        unset($link['attributes']['title']);
        // Replace button text per node type.
        $title_stripped = strip_tags($vars['title']);
        if (in_array($vars['type'], $action_types)) {
          $link['title'] = t(
            'Take action<span class="show-for-sr"> on @title</span>',
            ['@title' => $title_stripped]
          );
        }
        elseif ($vars['type'] == 'donation') {
          $link['title'] = t(
            'Donate now<span class="show-for-sr"> on @title</span>',
            ['@title' => $title_stripped]
          );
        }
      }
    }
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
  // Add id to webform blocks.
  if ($vars['block']->module == 'webform_block') {
    $vars['content_attributes_array']['id'] = drupal_html_id('form');
  }
  // Add classes to blocks.
  if ($vars['block']->module == 'share_light') {
    $vars['classes_array'][] = 'share-buttons';
    $vars['title_attributes_array']['class'][] = 'share-buttons-title';
  }
  if ($vars['block']->module == 'recent_supporters') {
    $vars['title_attributes_array']['class'][] = 'recent-supporters-title';
  }
  if ($vars['block']->delta == 'pgbar_default') {
    $vars['title_attributes_array']['class'][] = 'progress-title';
  }
  if ((current_path() == 'node' && $vars['block']->module == 'system')) {
    $vars['content_attributes_array']['class'][] = 'teasers';
  }
  if (!empty($vars['elements']['#layout_class'])) {
    $vars['classes_array'][] = $vars['elements']['#layout_class'];
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
 * Prepares variables for the form element.
 */
function campaignion_foundation_preprocess_form_element(&$variables) {
  $element = &$variables['element'];
  // Use .help-text instead of .description as class for the #description wrapper.
  $element['#description_attributes']['class'] = ['help-text' => 'help-text'];
  // Add button class for donation amount radios.
  $is_donation_amount = function ($form_key) {
    return substr($form_key, 0, strlen('donation_amount')) === 'donation_amount';
  };
  if (array_filter($element['#parents'], $is_donation_amount)) {
    if ($element['#type'] === 'radio' && $element['#return_value'] !== 'select_or_other') {
      $element['#label_attributes']['class'][] = 'button';
    }
    elseif ($element['#type'] === 'textfield' && in_array('other', $element['#parents'])) {
      $element['#wrapper_attributes']['class'][] = 'donation-amount-other';
    }
  }
}

/**
 * Preprocess variables for the form_element_label theme function.
 *
 * - Wrap radio/checkbox labels in a `<span>`.
 */
function campaignion_foundation_preprocess_form_element_label(&$variables) {
  $element = &$variables['element'];
  if ($element['#title_display'] == 'after') {
    $element['#title'] = '<span>' . $element['#title'] . '</span>';
  }
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
 * Prepares variables for mimemail message templates.
 */
function campaignion_foundation_preprocess_mimemail_message(&$vars) {
  $theme = mailsystem_get_mail_theme();
  $themepath = drupal_get_path('theme', $theme);
  $sitestyle = variable_get('mimemail_sitestyle', 1);
  $mailstyles = file_scan_directory($themepath, '#^mail(-.+)?\.(c|le|sc|sa)ss$#');
  $css_path = theme_get_setting('foundation_assets_css', $theme);
  // Add external css if mimemail is set to include style sheets and
  // no dedicated mail styles file exists in the theme.
  if ($sitestyle && empty($mailstyles)) {
    $vars['css'] = drupal_load_stylesheet($css_path);
  }
}

/**
 * Implements hook_css_alter().
 *
 * Remove annoying Drupal core CSS files.
 */
function campaignion_foundation_css_alter(&$css) {
  $exclude = [
    'webform.css',
    'filter.css',
    'recent-supporters.css',
    'campaignion_overlay.css',
  ];
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
  $exclude = ['campaignion_language_switcher.js', 'campaignion_overlay.js'];
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
        $icon = $link['attributes']['data-share'] . '-icon';
        $link['attributes']['class'] = array_merge(
          $link['attributes']['class'] ?? [],
          ['large', 'expanded', 'share', 'button', $icon]
        );
      }
    }
  }
}

/**
 * Implements hook_field_attach_view_alter().
 *
 * Add a theme hook for pgbars displayed on teasers.
 * This takes precedence over styles configured in the pgbar settings.
 */
function campaignion_foundation_field_attach_view_alter(&$output, $context) {
  if (!empty($output['pgbar_default']) && $context['view_mode'] == 'teaser') {
    array_unshift($output['pgbar_default'][0]['#theme'], 'pgbar__teaser');
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
    if (isset($form['actions'][$type]) && ($button = &$form['actions'][$type])) {
      $button_classes = $button['#attributes']['class'] ?? [];
      // Remove `button-primary` class added by webform. We use just `primary`.
      $button_classes = array_filter($button_classes, function ($class) {
        return $class !== 'button-primary';
      });
      // Add submit button classes.
      $button['#attributes']['class'] = array_merge($button_classes, $classes);
    }
  }
  // Don’t wrap form buttons in container.
  $form['actions']['#theme_wrappers'] = [];
  // Hide step button (webform_steps).
  if (isset($form['step_buttons'])) {
    $form['step_buttons']['#attributes']['class'][] = 'show-for-sr';
  }
  // Hide previous buttons on webforms.
  if (isset($form['actions']['previous'])) {
    $form['actions']['previous']['#attributes']['class'][] = 'show-for-sr';
  }

  // Add wrapper class for extra space on some form elements.
  $elements = $form;
  if (!empty($form['submitted'])) {
    $elements = &$form['submitted'];
  }
  ElementTree::applyRecursively($elements, function (&$element, $key, &$parent) {
    $element_types = ['radio', 'checkbox', 'radios', 'checkboxes'];
    if (in_array($element['#select_type'] ?? $element['#type'] ?? '', $element_types)) {
      $element['#wrapper_attributes']['class'][] = 'extra-spacing';
    }
  });
}

/**
 * Implements hook_form_FORM_ID_alter() for webform_client_form().
 *
 * Provide the form step information in Drupal.settings for the behavior
 * JavaScripts to pick up.
 */
function campaignion_foundation_form_webform_client_form_alter(&$form, &$form_state, $form_id) {
  // When the page nid and the webform's nid are the same we treat the form as
  // the "main form" of the page. Only the main form determines the current
  // step.
  $is_page_node = ($node = menu_get_object()) && $node->nid == $form['#node']->nid;
  if ($is_page_node) {
    $settings['campaignion_foundation']['webform'] = [
      'total_steps' => $form_state['webform']['page_count'],
      'current_step' => $form_state['webform']['page_num'],
      'last_completed_step' => $form_state['webform']['page_visited'] ?? 0,
      'id' => $form_id,
      'selector' => '.webform-client-form-' . $node->nid,
    ];
    $form['#attached']['js'][] = ['data' => $settings, 'type' => 'setting'];
  }
}

/**
 * Implements hook_payment_forms_payment_form_alter().
 */
function campaignion_foundation_payment_forms_payment_form_alter(&$element, \Payment $payment) {
  if ($payment->method->controller->name == 'braintree_payment_credit_card') {
    $element['expiry_date']['month']['#select_two'] = FALSE;
    $element['expiry_date']['year']['#select_two'] = FALSE;
  }
}

/**
 * Implements hook_webform_component_render_alter().
 */
function campaignion_foundation_webform_component_render_alter(&$element, $component) {
  // Add donation button classes for various form keys.
  $form_key = $element['#webform_component']['form_key'];
  if (substr($form_key, 0, strlen('donation_amount')) === 'donation_amount') {
    $element['#wrapper_attributes']['class'][] = 'donation-amount';
    if (in_array($element['#type'], ['radios', 'select_or_other'])) {
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
    $type['select']['#select_two'] = TRUE;
  }
  if (isset($type['autocomplete_api_select'])) {
    $type['autocomplete_api_select']['#process'][] = '_campaignion_foundation_process_autocomplete';
  }
}

/**
 * Add data-select-two attribute to select elements.
 *
 * This lets the Foundation SelectTwo plugin discover the select elements.
 */
function _campaignion_foundation_pre_render_select($element) {
  if ($element['#select_two']) {
    $element['#attributes']['data-select-two'] = "select-two";
  }
  return $element;
}

/**
 * Add theme option for autocomplete elements.
 *
 * In this case the autocomplete handles initializing select2.
 */
function _campaignion_foundation_process_autocomplete($element, &$form_state) {
  $element['#attributes']['data-theme'] = 'foundation';
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
