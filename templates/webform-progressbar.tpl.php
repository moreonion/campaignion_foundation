<?php

/**
 * @file
 * Display the progress bar for multipage forms.
 *
 * NB: Some variables are not used as we always want to display the page/step
 * number and page/step label. We also do not want to display percentages.
 *
 * Available variables:
 * - $node: The webform node.
 * - $progressbar_page_number: TRUE if the actual page number should be
 *   displayed.
 * - $progressbar_percent: TRUE if the percentage complete should be displayed.
 * - $progressbar_bar: TRUE if the bar should be displayed.
 * - $progressbar_pagebreak_labels: TRUE if the page break labels should be
 *   displayed.
 *
 * - $page_num: The current page number.
 * - $page_count: The total number of pages in this form.
 * - $page_labels: The labels for the pages. This typically includes a label for
 *   the starting page (index 0), each page in the form based on page break
 *   labels, and then the confirmation page (index number of pages + 1).
 * - $percent: The percentage complete.
 */
?>
<?php if ($progressbar_bar) : ?>
  <div class="form-steps-wrapper">
    <div class="form-steps webform-progressbar" data-form-steps-total="<?php print $page_count ?>" data-form-steps-current="<?php print $page_num ?>">
    <?php for ($n = 1; $n <= $page_count; $n++) : ?>
      <?php
        $classes = array();
        if ($n == $page_num) {
          $classes[] = 'current';
        }
        if ($n < $page_num) {
          $classes[] = 'previous';
        }
        if ($n == 1) {
          $classes[] = 'first';
        }
        elseif ($n == $page_count) {
          $classes[] = 'last';
        };
      ?>
      <div class="step webform-progressbar-page <?php print implode(' ', $classes); ?>" data-form-step-number="<?php print $n; ?>" title="<?php print check_plain($page_labels[$n - 1]); ?>">
        <?php if ($progressbar_page_number): ?>
        <span class="step-number"><?php print $n; ?></span>
        <?php endif; ?>
        <?php if ($progressbar_pagebreak_labels): ?>
        <span class="step-label">
          <?php print check_plain($page_labels[$n - 1]); ?>
        </span>
        <?php endif; ?>
      </div>
    <?php endfor; ?>
    </div>
  </div>
<?php endif; ?>
