<?php

/**
 * @file
 * Override views mini pager.
 */

/**
 * Implements theme_views_mini_pager().
 *
 * Replace 'pager' → 'pagination'.
 *
 * See views/theme/theme.inc.
 */
function campaignion_foundation_views_mini_pager($variables) {
  global $pager_page_array, $pager_total;

  $tags = $variables['tags'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];

  // Current is the page we are currently paged to.
  $pager_current = $pager_page_array[$element] + 1;
  // Max is the maximum page number.
  $pager_max = $pager_total[$element];
  // End of marker calculations.
  if ($pager_total[$element] > 1) {

    $li_previous = theme('pager_previous',
      array(
        'text' => (isset($tags[1]) ? $tags[1] : t('‹‹')),
        'element' => $element,
        'interval' => 1,
        'parameters' => $parameters,
      )
    );
    if (empty($li_previous)) {
      $li_previous = "&nbsp;";
    }

    $li_next = theme('pager_next',
      array(
        'text' => (isset($tags[3]) ? $tags[3] : t('››')),
        'element' => $element,
        'interval' => 1,
        'parameters' => $parameters,
      )
    );

    if (empty($li_next)) {
      $li_next = "&nbsp;";
    }

    $items[] = array(
      'class' => array('pagination-previous'),
      'data' => $li_previous,
    );

    $items[] = array(
      'class' => array('current'),
      'data' => t('@current of @max', array('@current' => $pager_current, '@max' => $pager_max)),
    );

    $items[] = array(
      'class' => array('pagination-next'),
      'data' => $li_next,
    );

    return theme('item_list',
      array(
        'items' => $items,
        'title' => NULL,
        'type' => 'ul',
        'attributes' => array('class' => array('pagination')),
      )
    );
  }
}
