<?php

/**
 * @file
 * Displays a progess bar on a teaser.
 *
 * Available variables:
 * - $format_fn: A function for formatting numbers with the same signature as
 *   the two argument version of number_format().
 * - $current: (int) The current count.
 * - $target: (int) The current target.
 * - $percentage: (float) The percentage.
 * - $goal_reached: (bool) TRUE when the current count has reached the target.
 * - $texts: An array with configured texts.
 * - $html_id: ID used to bind the field settings. This needs to be in the
 *   ID of the outermost wrapper.
 */

?>
<div id="<?php print $html_id; ?>" class="pgbar-wrapper progress-wrapper" data-pgbar-current="<?php print $current; ?>" data-pgbar-target="<?php print $target; ?>">
  <div class="progress" role="progressbar" tabindex="0" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
    <div class="progress-meter" style="width:<?php echo $percentage; ?>%"></div>
    <span class="progress-meter-text"><?php print $format_fn($current, 0); ?></span>
  </div>
</div>
