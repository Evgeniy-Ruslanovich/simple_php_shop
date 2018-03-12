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
echo '<form method="post" action="./?ctrl=ordering&action=edit_cart">';
foreach ($cart as $key => $value) {
	$sum = (int)$value['good_count'] * (float)$value['price'];
	$total_sum += $sum;
	?>
	<p><b><?= $value['product_name'] ?></b></p>
	<p>Количество: <?= $value['good_count'] ?> Цена: <?= $value['price'] ?> Сумма: <?= $sum ?></p>
	<p><label>Удалить товар<input type="checkbox" name="delete[]" value="<?= $value['good_id'] ?>"></label> | 
	<label>Изменить количество <input type="number" name="good_count[]" value="<?= $value['good_count'] ?>"></label></p><br>
	<input type="hidden" name="good_id[]" value="<?= $value['good_id'] ?>">
	<?php
}
echo '<p>Общая сумма: <b>' . $total_sum . '</b></p>';
echo "<button>Сохранить изменения</button>";
echo '</form>';

echo '<br><br><form method="post" action="./?ctrl=ordering&action=save_draft"><button>Перейти к оформлению заказа</button></form>';
/*
?>
<br><br>
<h3><?= $message ?></h3>
<br>
<p><?= $suggested_link ?></p>*/
