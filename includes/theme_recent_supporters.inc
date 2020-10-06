<?php

/**
 * @file
 * Override recent supporters.
 */

/**
 * Returns HTML for recent supporters.
 *
 * - Remove "clearfix" class on supporter list items.
 * - Move wrapper outside so it’s also there for no supporters.
 * - Add "no-bullet" class on list (Foundaton bullets won’t work when cycling).
 * - Add classes indicating what supporter components to expect (comments, flags).
 * - Put the “no activity” text in a paragraph.
 *
 * See recent_supporters/recent_supporters.module.
 */
function campaignion_foundation_recent_supporters($variables) {
  $supporters = $variables['supporters'];
  if ($show_country = $variables['show_country']) {
    $countries = country_get_list();
  }

  $output = "<div id=\"{$variables['id']}\" class=\"recent-supporters-wrapper\">";

  if (count($supporters) < 1) {
    $output .= '<div class="no-activity-yet"><p>' . t('No recent activity.') . '</p></div>';
  }
  else {
    $classes = ['no-bullet', 'recent-supporters'];
    $classes[] = $variables['all_actions'] ? 'all-actions' : 'single-action';
    $classes[] = $variables['show_comment'] ? 'with-comments' : 'no-comments';
    $classes[] = $show_country ? 'with-flags' : 'no-flags';
    $output .= "<ul class=\"" . implode(' ', $classes) . "\">\n";
    $count = 1;
    foreach ($supporters as $supporter) {
      $action_type = "";
      if (!empty($supporter['action_type'])) {
        $action_type = "action-type-" . $supporter['action_type'];
      }
      $output .= "<li class=\"supporter $action_type\"";
      if ($count++ > $variables['visible_count']) {
        $output .= ' style="display: none;"';
      }
      $output .= ">\n";

      if ($variables['show_comment'] && !empty($supporter['comment'])) {
        $output .= "\n<span class=\"comment\">{$supporter['comment']}</span>\n";
      }

      if ($show_country) {
        $country_code = empty($supporter['country']) ? 'no-cc' : $supporter['country'];
        $country_name = $country_code == 'no-cc' ? '' : $countries[$country_code];
        $classes = "flag-" . strtolower(drupal_clean_css_identifier($country_code));
        $output .= "<span title=\"" . $country_name
            . "\" class=\"country flag " . $classes . "\">";
        $output .= $country_code;
        $output .= "</span>\n";
      }
      if ($variables['all_actions']) {
        $first_name = $supporter['first_name'] . (!empty($supporter['first_name']) ? " " : "");
        $vars['!supporter_name'] = "<span class=\"name\">" . $first_name . "<span class=\"last_name\">" . $supporter['last_name'] . "</span></span>\n";
        $vars['!action_title'] = "<span class=\"title\">";
        if (!empty($supporter['action_url'])) {
          $vars['!action_title'] .= '<a href="' . $supporter['action_url'] . '" ';
          $vars['!action_title'] .= 'title="' . $supporter['action_title'] . '">';
          $vars['!action_title'] .= $supporter['action_title'];
          $vars['!action_title'] .= '</a>';
        }
        else {
          $vars['!action_title'] .= $supporter['action_title'];
        }
        $vars['!action_title'] .= "</span>";
        $output .= format_string($variables['texts'][$supporter['action_type']], $vars);
      }
      else {
        $output .= "<span class=\"name\">";
        $output .= implode(" ", array($supporter['first_name'], $supporter['last_name']));
        $output .= "</span>\n";
      }
      $output .= "\n<span class=\"time\" data-timestamp=\"" . $supporter['timestamp'] . "\" title=\"" . $supporter['rfc8601'] . "\">";
      $output .= date('d.m.Y H:i', $supporter['timestamp']);
      $output .= "</span>\n";

      $output .= "</li>\n";
    }
    $output .= "</ul>\n";
  }
  $output .= "</div>\n";
  return $output;
}
