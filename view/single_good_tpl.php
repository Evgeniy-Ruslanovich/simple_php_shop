<?php

/**
 * 
 */
?>
<a href="./">на главную страницу</a><br>
<?php
foreach ($categories_array as $value) {
	echo '<a href="./?category=' . $value['id'] . '">' .  $value['category_name'] . '</a>&nbsp';
}
?>
<div class=big-photo>
	<a href="img/<?= $single_good_data['good_main_photo'] ?>" target="_blank">
		<img src="img/<?= $single_good_data['good_main_photo'] ?>" style="max-height: 600px; max-width: 100%">
	</a>
</div>
<h1><?= $single_good_data['product_name'] ?></h1>
<p><?= $single_good_data['product_description'] ?> </p>
<p>Категория: <a href="./?category=<?= $single_good_data['category'] ?>"><?= $single_good_data['category_name'] ?></a></p>
<!-- <p>ID: <?= $single_good_data['id'] ?> </p> -->
<!-- <p><a href="./?edit=<?= $single_good_data['id'] ?>">Редактировать</a></p>
<p><a href="./?delete=<?= $single_good_data['id'] ?>">Удалить</a></p>-->
<?php
if (isset($single_good_data['message'])) {
	if ($single_good_data['message'] === '1') {
		echo '<script>alert("Товар добавлен в корзину");</script>';
	} else {
		if ($single_good_data['message'] === '0') {
		echo '<script>alert("Не удалось добавить товар в корзину");</script>';
		}
	}
}
?>
<form method="post" action="./?ctrl=ordering&action=add_to_cart">
	<input type="hidden" name="good_id" value="<?= $single_good_data['id'] ?>">
	<input type="hidden" name="product_name" value="<?= $single_good_data['product_name'] ?>">
	<button>Добавить в корзину</button>
</form>
<?php
