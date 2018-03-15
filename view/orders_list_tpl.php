<?php

/**
 * 
 */
//var_dump($orders_list_array);
/*
?>
<h3>Заказ <b>№<?= $single_order_data[0]['id'] ?></b></h3>
<p>Время заказа <b><?= $single_order_data[0]['order_time'] ?></b></p>
<p>Статус <b><?= $single_order_data[0]['status_name_rus'] ?></b></p>
<br>
<?php
if ($single_order_data[0]['order_status'] === '2') {
	echo '<p>В ближайшее время наш менеджер свяжется с Вами, чтобы уточнить детали заказа, и подтвердить его. Спасибо за Ваш заказ!</p><br>';
}*/
echo '<h3>Мои заказы</h3>';
foreach ($orders_list_array as $value) {
	?>
	<p><a href="./?ctrl=ordering&action=order&order=<?= $value['id'] ?>"><b>№<?= $value['id'] ?></b></a></p>
	<p>Время заказа: <?= $value['order_time'] ?> 
	Сумма: <?= $value['total_amount'] ?> 
	Oплачено: <?php
		if((bool)$value['paid']){
			echo '<span style="color:green">Да</span> ';
		} else {
			echo '<span style="color:red">Нет</span> ';
		}
		?> 
	Статус: <?= $value['status_name_rus'] ?></p>
	<br>
	<?php
}/*
?>
<p>Общая сумма <b><?= $single_order_data[0]['total_amount'] ?></b></p>
<p>Способ доставки <b><?= $single_order_data[0]['delivery_method'] ?></b></p>
<p>Способ оплаты <b><?= $single_order_data[0]['payment_method'] ?></b></p>
<p>Оплачен <b><?= $single_order_data[0]['paid'] ?></b></p>
<p>Комментарий пользователя: <i><?= $single_order_data[0]['users_comment'] ?></i></p>
<p>Адрес доставки <b><?= $single_order_data[0]['delivery_address'] ?></b></p>
<p>Стоимость доставки <b><?= $single_order_data[0]['shipping_cost'] ?></b></p>
<?php*/
