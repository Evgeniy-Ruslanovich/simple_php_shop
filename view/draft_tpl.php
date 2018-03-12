<?php

/**
 * 
 */
/*
var_dump($single_order_data);
echo '<br>';
var_dump($payment_methods_data);
echo '<br>';
var_dump($delivery_methods_data);
echo '<br>';*/
?>
<h3>Заказ <b>№<?= $single_order_data[0]['id'] ?></b></h3>
<p>Время заказа <b><?= $single_order_data[0]['order_time'] ?></b></p>
<p>Статус <b><?= $single_order_data[0]['status_name_rus'] ?></b></p>
<br>
<?php
if ($single_order_data[0]['order_status'] === '2') {
	echo '<p>В ближайшее время наш менеджер свяжется с Вами, чтобы уточнить детали заказа, и подтвердить его. Спасибо за Ваш заказ!</p><br>';
}
?>
<h4>Товары в заказе:</h4>
<form action="./?ctrl=ordering&action=edit_draft" method="post">
	<input type="hidden" name="edit_draft_goods">
	<?php
		foreach ($single_order_data as $key => $value) {
			?>
			<p><a href="./?ctrl=shop&good=<?= $value['good_id'] ?>"><b><?= $value['product_name'] ?></b></a></p>
			<p>Количество: <?= $value['good_count'] ?> Цена: <?= $value['price'] ?> Сумма: <?= $value['good_sum'] ?></p>
			<p><label>Удалить товар<input type="checkbox" name="delete[]" value="<?= $value['good_id'] ?>"></label> | 
			<label>Изменить количество <input type="number" name="good_count[]" value="<?= $value['good_count'] ?>"></label></p><br>
			<input type="hidden" name="good_id[]" value="<?= $value['good_id'] ?>">
			<br>
			<?php
		}
	?>
	<button>Сохранить изменения в товарах</button><br><br>
</form>
<hr>
<h4>Детали заказа</h4>
<form action="./?ctrl=ordering&action=edit_draft_detail" method="post">
<input type="hidden" name="edit_draft_detail">
<p>Общая сумма <b><?= $single_order_data[0]['total_amount'] ?></b></p>
<p>
	<label>Способ доставки 
	<select name="delivery_id">
	<option>Не выбрано</option>
	<?php
		foreach ($delivery_methods_data as $value) {
			echo '<option';
			if ($value['delivery_id'] === $single_order_data[0]['delivery_method']) {
				echo ' selected';
			}
			echo ' value="' . $value['delivery_id'] . '">' . $value['delivery_name'] . '</option>';
		}
	?>
	</select>
	</label>
</p>
<p>
	<label>Способ оплаты 
	<select name="payment_id">
	<option>Не выбрано</option>
	<?php
		foreach ($payment_methods_data as $value) {
			echo '<option';
			if ($value['payment_id'] === $single_order_data[0]['payment_method']) {
				echo ' selected';
			}
			echo ' value="' . $value['payment_id'] . '">' . $value['payment_name'] . '</option>';
		}
	?>
	</select>
	</label>
</p>
<p>Оплачен <b><?= $single_order_data[0]['paid'] ?></b></p>
<p>
	<label>Комментарий пользователя: <br>
		<textarea name="users_comment">
			<?= $single_order_data[0]['users_comment'] ?>
		</textarea>
	</label>
</p>
	
<p>
	<label>Адрес доставки <br>
		<textarea name="delivery_address">
			<?= $single_order_data[0]['delivery_address'] ?>
		</textarea>
	</label>
</p>
<p>Стоимость доставки <b><?= $single_order_data[0]['shipping_cost'] ?></b></p>
<button>Сохранить изменения в деталях заказа</button>
</form>
<br>
<hr>
<br>
<form method="post" action="./?ctrl=ordering&action=push_order">
	<input type="hidden" name="push_order">
	<button>Отправить заказ в обработку</button><br>
	После нажатия этой кнопки заказ поступит  менеджерам магазина, и Вы больше не сможете его редактировать. Менеджер свяжутся с Вами для уточнения деталей и подтверждения заказа. Также, если вы захотите после отправки заказа что-либо изменить, свяжитесь с менеджером магазина или службой поддержки.
</form>
<?php
