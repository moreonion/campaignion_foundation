<?php

/**
 * @file
 * Theme view template to display a list of actions.
 */
?>
<?php if (!empty($title)): ?>
<h3><?php print $title; ?></h3>
<?php endif;?>

<div class="teasers">
<?php
foreach ($rows as $id => $row) {
  print $row;
}
?>
</div>
