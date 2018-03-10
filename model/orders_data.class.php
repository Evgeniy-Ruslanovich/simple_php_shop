<?php
/*
*
*/
require_once MODEL_DIR . DIRECTORY_SEPARATOR . 'database_master.class.php';

class Orders_data extends Database_master
{
	/*
		$params = array(
		//SELECT
		'columns' => array(), //or * Пока ХЕВИНГ и ГРУП БАЙ не будем юзать, нам интересны только ТАБЛИЦА, КОЛОНКИ, WHERE, ОРДЕР
		//FROM
		'table' => 'table',
		//WHERE
		'where' => array(),
		//group by
		'group by' =>'',
		//HAVING
		'group by' =>array(),
		//ORDER BY
		'order_by' =>''

		$goods_params = array(  //ЭТО уже должно соответствовать конкретной таблице, в данном случае товаров.
		//SELECT
		'fields' => array(), //or * fields аналогично columns - Это задаем ЖОСКА
		//FROM
		'table' => 'goods', //Это тоже ЖОСКА
		//WHERE
		'categories' => array(),
		'hidden' => 0 показывать открытые, 1, показать только скрытые, или ничего, тогда показывать всё //ЖОСКА
		//group by
		'group by' =>'', //НЕ НАДА
		//HAVING
		'group by' =>array(),
		//ORDER BY
		'order_by' =>'' //НАДА
		//ПО сути нам для публичного списка товаров нада: категорию, и сортировать по..
		То есть: */
	/*
	$goods_params = array(
		'categories' => array(),
		'order_by' =>'' //НАДА
	*/



	public function get_user_orders_list($orders_params) {
		$query_params = array('table' => 'orders');
		$query_params['columns'] = array('id', 'product_name', 'category', 'price', 'good_main_photo');//Если хотим выбрать ВСЕ, то массив надо оставлять ПУСТЫМ. Мы ЖОСКА задаем поля, и вообще все что можно задать, чтобы ократить до минимума пользовательский ввод в базу. Юзер, по сути, вводит только свой плогин-пароль, и комментарий к заказу.. пока что.
		//$query_params['hidden'] = 0; //
		$query_params['where'] = $this->build_where_statement_for_public_goods_list($orders_params);
		//$query_params['order_by'] = null;
		$result = $this->read_any_table($query_params);

		return $result;

	}

	public function get_public_single_order($order_id) {
		$query_params = array('table' => 'orders');
		$query_params['columns'] = array('id', 'product_name', 'category', 'price', 'good_main_photo', 'product_description');
		$query_params['where'] = 'WHERE `id`=\'' . (int)$order_id . '\' ';
		$result = $this->read_any_table($query_params)[0];
		return $result;
	}
	
	public function get_admin_single_order($order_id) {
		$query_params = array('table' => 'goods');
		//$query_params['columns'] = array('id', 'product_name', 'category', 'price', 'good_main_photo', 'product_description');
		$query_params['where'] = 'WHERE `id`=\'' . (int)$good_id . '\' ';
		$result = $this->read_any_table($query_params)[0];
		return $result;
	}
	/* protected function build_where_statement($goods_params)
	{
		$where_statement = '';
		if (isset($goods_params['hidden'] && $goods_params['categories'])) {
			$where_statement .= 'WHERE ';

		}
	}*/

	protected function build_where_statement_for_public_goods_list($goods_params)
	{//для публичного магазина нам нужно только знать категорию, потому что скрытые по умолчанию не показываются
		//$where_statement = 'WHERE `hidden`=0';  //скрытые нам не нужны
		$where_statement = '';
		if (isset($goods_params['categories']) && count($goods_params['categories']))  {
			//$where_statement .= ' AND ';
			foreach ($goods_params['categories'] as $key => $categoriy) {
				$where_statement .= 'OR `category`=\''. $categoriy .'\' ';
				
			}
			$where_statement = substr($where_statement, 3);
			$where_statement = 'AND (' . $where_statement . ')';
		
		}
			$where_statement = 'WHERE `hidden`=\'0\' ' . $where_statement . '';
		return $where_statement;
	}

	public function save_draft()
	{
		global $security_pass;
		global $link;
		$this->hello_test();
		$order_params = array('table' => 'orders');
		$order_params['keyvalue'] = array(
					array(
					'user_id' => $security_pass->get_user_id(),
					'order_status' => 1
					)/*,
					array(
					'user_id' => $security_pass->get_user_id(),
					'order_status' => 1
					)*/
				);
			 //тут должен быть массив в массиве, потому что можно вносить новые записи по одной, а можно пачкой (как, например, товары в заказе), и если пачкой - то это массив массивов. А если по одной - то это опять же массив с одним элементом, тоже массивом.
		$result = $this->insert_new_entry($order_params);
		if (!$result) {
			return false; //если чо не так, выходим на фиг
		}
		var_dump($result); echo " Результат создания нового заказа<br>";
		$order_id = mysqli_insert_id($link); //вообще можно это в самом скуэль-запросе сделать https://dev.mysql.com/doc/refman/5.7/en/getting-unique-id.html
		/*АЙДИ ТОВАРА СОХРАНЯЮТСЯ КАК КЛЮЧИ МАССИВА cart В СЕССИИ*/
		echo $order_id . " -Последний айдишник<br>";
		$order_goods_params = array('table' => 'order_goods');
		$order_params['keyvalue'] = array();/*
			array('order_id' => $order_id, = 'good_id' => , 'good_count'),
			array(),

			);*/
		foreach ($_SESSION['cart'] as $key => $value) {
			$order_goods_params['keyvalue'][] = array('order_id' => $order_id, 'good_id' => $key, 'good_count' => $value['quantity']);
		}
		//var_dump($order_goods_params['keyvalue']);
		echo "<br>";
		$result = $this->insert_new_entry($order_goods_params);
		if (!$result) {
			return false; //если чо не так, выходим на фиг
		}
		var_dump($result); echo " Результат добавления товаров<br>";
		$this->recount_order_summ($order_id);
		unset($_SESSION['cart']);
		$_SESSION['have_draft'] = true; //Запомним, что теперь у нас есть черновик, и в след. раз полезем туда вместо корзины. Не забыть переставить на ФОЛС, когда переправим заказ в обработку. Тогда можно снова начать набивать корзину.
	}

	protected function create_new_order($user, $goods, $status)
	{

	}

	/*protected*/ public function recount_order_summ($order_id)
	{
		global $link;
		$query = "SELECT SUM(" . DB_NAME . ".`order_goods`.`good_count` * " . DB_NAME . ".`goods`.`price`) as 'total_amount' FROM " . DB_NAME . ".`order_goods` INNER JOIN " . DB_NAME . ".`goods` ON `goods`.`id`= `order_goods`.`good_id` WHERE `order_goods`.`order_id` = '" . (int)$order_id . "'"; //запарился указывать DB_NAME с другой стороны, если у нас будут разные базы, то легко будет вставить нужные константы..
		$result = mysqli_query($link, $query);
		//var_dump(mysqli_error_list($link));
		$total_amount = mysqli_fetch_assoc($result)['total_amount'];
		echo $total_amount;
		//var_dump($result);
		//var_dump($total_amount);
		$query = "UPDATE " . DB_NAME . ".`orders` SET `total_amount`='" . $total_amount . "' WHERE `id`='" . (int)$order_id . "'"; //а хрен его знает, откуда этот одре_айди пришел, лучше перебдеть
		$result = mysqli_query($link, $query);
		var_dump(mysqli_error_list($link));
		return (bool)$result;

	}
/* это тут не нужно, это из шоп-модели
	public function get_categories_list()
	{
		$query_params = array('table' => 'categories');
		$query_params['columns'] = array('id', 'category_name');
		$result = $this->read_any_table($query_params);
		return $result;
	}*/
}