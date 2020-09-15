<?php

/**
 * @file
 * Theme implementation to display a list of translation links.
 *
 * Available variables:
 * - $links: An array of $link-arrays. Each $link in $links contains:
 *   - $link['li_attributes']: Attribute array for the list-item.
 *   - $link['renderable']: A renderable array for the link content.
 *
 * @see template_preprocess_campaignion_language_switcher()
 */
?>
<?php
  // If there aren’t any links, there’s no need for a language switcher.
  if (!$links_accessible) {
    return;
  }
?>
<nav>
  <ul <?php print $attributes; ?> class="<?php print $classes; ?>">
    <li<?php print drupal_attributes($active_link['li_attributes'] ?? []); ?>><?php print render($active_link['renderable']); ?>
      <ul class="menu">
        <?php foreach ($links_accessible as $link): ?>
        <li<?php print drupal_attributes($link['li_attributes']); ?>><?php print render($link['renderable']); ?></li>
        <?php endforeach; ?>
      </ul>
    </li>
  </ul>
</nav>
