<?php

/**
 * 
 */
//var_dump($good_data_array);
?>
<h1><?= $good_data_array['product_name'] ?></h1>
<div class=big-photo>
	<a href="img/<?= $good_data_array['good_main_photo'] ?>" target="_blank">
		<img src="img/<?= $good_data_array['good_main_photo'] ?>" style="max-height: 600px; max-width: 100%">
	</a>
</div>
<p><i>Описание: </i><?= $good_data_array['product_description'] ?> </p>
<p><i>Категория: </i><?= $good_data_array['category'] ?> </p>
<p><i>Цена: </i><?= $good_data_array['price'] ?> </p>
<p><i>Товар скрыт: </i><?= $good_data_array['hidden'] ?> </p>
<p><i>Остаток на складе: </i><?= $good_data_array['quantity_in_stock'] ?> </p>
<p><i>Зарезервировано в заказах: </i><?= $good_data_array['quantity_in_reserve'] ?> </p>

<a href="./">на главную страницу</a><br>

<h1><?= $good_data_array['product_name'] ?></h1>

<!-- <p>ID: <?= $good_data_array['id'] ?> </p> -->
<!-- <p><a href="./?edit=<?= $good_data_array['id'] ?>">Редактировать</a></p>
<p><a href="./?delete=<?= $good_data_array['id'] ?>">Удалить</a></p>-->

<form method="post" action="./?ctrl=admin&subctrl=goods&action=edit_good">
	<input type="hidden" name="good_id" value="<?= $good_data_array['id'] ?>">
	<button>Редактировать</button>
</form>
<?php
