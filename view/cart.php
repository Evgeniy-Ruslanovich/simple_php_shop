<?php

/**
 * 
 */
if (isset($show_categories) && $show_categories) {
	foreach ($categories_array as $value) {
	echo '<a href="./?category=' . $value['id'] . '">' .  $value['category_name'] . '</a>&nbsp';
	}
}
$total_sum = 0;
echo '<form method="post" action="./?ctrl=ordering&ctrl=edit_cart">';
foreach ($cart as $key => $value) {
	$sum = (int)$value['quantity'] * (float)$value['price'];
	$total_sum += $sum;
	echo '<p><b>' . $value['product_name'] . '</b></p>';
	echo '<p>Количество: ' . $value['quantity'] . ' Цена: ' . $value['price'] . ' Сумма: ' .  $sum . '</p>';
	echo '<p>Удалить товар<input type="checkbox" name="delete_' . $value['good_id'] . '"></p><br>';
}
echo '<p>Общая сумма: <b>' . $total_sum . '</b></p>';
echo '</form>';

/*
?>
<br><br>
<h3><?= $message ?></h3>
<br>
<p><?= $suggested_link ?></p>*/
