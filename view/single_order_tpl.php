<?php

/**
 * 
 */
//var_dump($single_order_data);
?>
<h3>Заказ <b>№<?= $single_order_data[0]['id'] ?></b></h3>
<p>Время заказа <b><?= $single_order_data[0]['order_time'] ?></b></p>
<p>Статус <b><?= $single_order_data[0]['status_name_rus'] ?></b></p>
<br>
<?php
if ($single_order_data[0]['order_status'] === '2') {
	echo '<p>В ближайшее время наш менеджер свяжется с Вами, чтобы уточнить детали заказа, и подтвердить его. Спасибо за Ваш заказ!</p><br>';
}
echo '<h4>Товары в заказе:</h4>';
if ($single_order_data[0]['good_id'] === NULL) {
	echo "<p>Этот заказ пустой. В заказе товаров не обнаружено.</p>";
	if ($single_order_data[0]['order_status'] === '1') {
		echo '<p>Вы можете отправиться в магазин и добавить нужные товары.</p>';
	}
	echo '<br>';
} else {
	foreach ($single_order_data as $key => $value) {
	?>
	<p><a href="./?ctrl=shop&good=<?= $value['good_id'] ?>"><b><?= $value['product_name'] ?></b></a></p>
	<p>Количество: <?= $value['good_count'] ?> Цена: <?= $value['price'] ?> Сумма: <?= $value['good_sum'] ?></p>
	<br>
	<?php
	}
}
/*foreach ($single_order_data as $key => $value) {
	?>
	<p><a href="./?ctrl=shop&good=<?= $value['good_id'] ?>"><b><?= $value['product_name'] ?></b></a></p>
	<p>Количество: <?= $value['good_count'] ?> Цена: <?= $value['price'] ?> Сумма: <?= $value['good_sum'] ?></p>
	<br>
	<?php
}*/
?>
<p>Общая сумма <b><?= $single_order_data[0]['total_amount'] ?></b></p>
<p>Способ доставки <b><?= $single_order_data[0]['delivery_name'] ?></b></p>
<p>Способ оплаты <b><?= $single_order_data[0]['payment_name'] ?></b></p>
<p>Оплачен <b><?= $single_order_data[0]['paid'] ?></b></p>
<p>Комментарий пользователя: <i><?= $single_order_data[0]['users_comment'] ?></i></p>
<p>Адрес доставки <b><?= $single_order_data[0]['delivery_address'] ?></b></p>
<p>Стоимость доставки <b><?= $single_order_data[0]['shipping_cost'] ?></b></p>
<?php
