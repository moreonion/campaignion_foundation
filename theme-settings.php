<?php

/**
 * @file
 * Custom theme settings for the Campaignion Foundation Theme.
 */

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function campaignion_foundation_form_system_theme_settings_alter(&$form, $form_state) {
  $form['foundation_assets'] = [
    '#type' => 'fieldset',
    '#title' => t('Asset links'),
    '#collapsible' => FALSE,
  ];
  $form['foundation_assets']['foundation_assets_css'] = [
    '#type'          => 'textfield',
    '#title'         => t('Main CSS file'),
    '#default_value' => theme_get_setting('foundation_assets_css'),
    '#description'   => t('Link to your main CSS file.'),
  ];
  $form['foundation_assets']['foundation_assets_js'] = [
    '#type'          => 'textfield',
    '#title'         => t('Main JS file'),
    '#default_value' => theme_get_setting('foundation_assets_js'),
    '#description'   => t('Link to your main JS file.'),
  ];
}
