<?php
echo '<h1>' . $example_subctrl .'</h1>';
foreach ($orders_list_array as $value) {
	?>
	<p><a href="./?ctrl=ordering&action=order&order=<?= $value['id'] ?>"><b>№<?= $value['id'] ?></b></a></p>
	<p>Время заказа: <?= $value['order_time'] ?></p>
	<p>Пукупатель: <?= $value['user_id'] ?> </p>
	<p>Сумма: <?= $value['total_amount'] ?> </p>
	<p>Способ доставки: <?= $value['delivery_method'] ?> </p>
	<p>Способ оплаты: <?= $value['payment_method'] ?> </p>
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
}