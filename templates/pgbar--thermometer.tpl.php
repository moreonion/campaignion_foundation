<?php

/**
 * @file
 * Displays a progess bar.
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
$vars['!current'] = '<strong>' . $format_fn($current, 0) . '</strong>';
$vars['!current-animated'] = '<strong class="pgbar-counter">' . $format_fn($current, 0) . '</strong>';
$vars['!target'] = '<strong>' . $format_fn($target, 0) . '</strong>';
$vars['!target-animated'] = '<strong class="pgbar-target">' . $format_fn($target, 0) . '</strong>';
$vars['!needed'] = $format_fn($target - $current, 0);
$vars['!needed-animated'] = '<span class="pgbar-needed">' . $format_fn($target - $current, 0) . '</span>';
$intro_message = format_string($goal_reached ? $texts['full_intro_message'] : $texts['intro_message'], $vars);
$status_message = format_string($goal_reached ? $texts['full_status_message'] : $texts['status_message'], $vars) . "\n";
?>
<div id="<?php print $html_id; ?>" class="pgbar-wrapper progress-wrapper flex-container" data-pgbar-current="<?php print $current; ?>" data-pgbar-target="<?php print $target; ?>" data-pgbar-direction="vertical">
  <div class="progress thermometer" role="meter" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
    <div class="pgbar-current progress-meter" style="height:<?php echo $percentage; ?>%"></div>
  </div>
  <div class="flex-container flex-dir-column align-justify">
    <p><?php print $intro_message; ?></p>
    <p><?php print $status_message; ?></p>
  </div>
</div>
