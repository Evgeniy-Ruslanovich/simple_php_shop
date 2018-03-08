<?php

/**
 * 
 */
foreach ($categories_array as $value) {
	echo '<a href="./?category=' . $value['id'] . '">' .  $value['category_name'] . '</a>&nbsp';
}
?>
<br><br>
<h3><?= $message ?></h3>
<br>
<p><?= $suggested_link ?></p>
