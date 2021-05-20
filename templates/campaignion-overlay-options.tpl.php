<?php

/**
 * @file
 * Displays an overlay containing a form.
 *
 * Available variables:
 *  - $introduction: A short introductory text,
 *  - $form: A renderable form array.
 *
 * @see campaignion_overlay_field_collection_item_view()
 */
?>
<div id="<?php echo drupal_html_id('overlay'); ?>" class="reveal" data-reveal data-open>
  <?php echo render($introduction); ?>
  <?php echo render($content); ?>
  <button class="close-button large" data-close aria-label="Close overlay" type="button"></button>
</div>
