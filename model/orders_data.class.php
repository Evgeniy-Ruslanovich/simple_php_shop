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
		'order_by' =>'');
	*/



	public function get_user_orders_list() {

		$query_params = array('table' => 'orders');
		$query_params['columns'] = array('orders.id', 'order_time', 'total_amount', 'paid', 'order_status.status_name_rus');//Если хотим выбрать ВСЕ, то массив надо оставлять ПУСТЫМ. Мы ЖОСКА задаем поля, и вообще все что можно задать, чтобы ократить до минимума пользовательский ввод в базу. Юзер, по сути, вводит только свой плогин-пароль, и комментарий к заказу.. пока что.
		//$query_params['hidden'] = 0; //
		global $security_pass;
		$user_id = $security_pass->get_user_id();
		//$query_params['where'] = $this->build_where_statement_for_public_goods_list($orders_params);
		$query_params['where'] = "WHERE `orders`.`user_id`=" . $user_id;
		$query_params['join'] = "INNER JOIN `order_status` ON `orders`.`order_status` = `order_status`.`id`";
		//$query_params['order_by'] = null;
		$result = $this->read_any_table($query_params);

		return $result;
	}

	public function get_public_single_order($order_id)
	{
		global $security_pass;
		$user_id = $security_pass->get_user_id();
		$query = "SELECT `orders`.*, 
		`delivery_methods`.`method_name_rus` AS 'delivery_name', 
		`payment_methods`.`method_name_rus` AS 'payment_name',
		`order_status`.`status_name_rus`, 
		`order_goods`.`good_id`,`order_goods`.`good_count`,
		`goods`.`product_name`,`goods`.`price`, 
		`order_goods`.`good_count`*`goods`.`price` AS 'good_sum'
		FROM `orders` 
		LEFT JOIN `order_goods` on `order_goods`.`order_id` = `orders`.`id`
		LEFT JOIN `goods` ON `goods`.`id` = `order_goods`.`good_id`
		LEFT JOIN `order_status` on `order_status`.`id` = `orders`.`order_status`  
		LEFT JOIN `delivery_methods` ON `orders`.`delivery_method` = `delivery_methods`.`id` 
		LEFT JOIN `payment_methods` ON `orders`.`payment_method` = `payment_methods`.`id` 
		 ";
		//Пришлось все джоины заменить на левые, потому что если хоть чего-то не хватает, например не указан способ доставки, или оплаты, то заказ не отображается, потому что любой иннер, если нет пересечения, убивает нафиг все предыдущие результаты.
		if($order_id === 'draft'){
			$where_statement = "WHERE `orders`.`order_status`=1 AND `orders`.`user_id` = " . $user_id; //ну там, вдруг будет более одного черновика, берем первый попавшийся. Хотя не должно быть больше одного
		} else {
			$where_statement = "WHERE `orders`.`id` = " . (int)$order_id . " AND `orders`.`user_id` = " . $user_id;
		}
		$query .= $where_statement;
		//echo 'запрос: ' . $query;
		//Мы задаем два условия, номер заказа и айди юзера. Таким образом, если юзер захочет посмотреть не свой заказ, просто вбив  в адресную строку номер заказа, то увидит кукиш. МОжно видеть только те заказы, которые соответствуют твоему айдишнику из $security_pass		
		/*Это тот случай, когда запрос специфический, и блин проще просто дать текст запроса, чем бить на парамерты, потом оратно их конвертировать в запрос. МОжно было и разбить, но блин запарился.*/
		$result = $this->read_any_table_ready_query($query);
		return $result;
	}
	
	public function get_admin_single_order($order_id)
	{
		$query_params = array('table' => 'goods');
		//$query_params['columns'] = array('id', 'product_name', 'category', 'price', 'good_main_photo', 'product_description');
		$query_params['where'] = 'WHERE `id`=\'' . (int)$good_id . '\' ';
		$result = $this->read_any_table($query_params)[0];
		return $result;
	}
	/*
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
	}*/
	/*Функция для сохранения из сессии в черновик*/
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
					)
				);
			 //тут должен быть массив в массиве, потому что можно вносить новые записи по одной, а можно пачкой (как, например, товары в заказе), и если пачкой - то это массив массивов. А если по одной - то это опять же массив с одним элементом, тоже массивом.
		$result = $this->insert_new_entry($order_params);
		if (!$result) {
			return false; //если чо не так, выходим на фиг
		}
		$order_id = mysqli_insert_id($link); //вообще можно это в самом скуэль-запросе сделать https://dev.mysql.com/doc/refman/5.7/en/getting-unique-id.html
		/*АЙДИ ТОВАРА СОХРАНЯЮТСЯ КАК КЛЮЧИ МАССИВА cart В СЕССИИ*/
		//echo $order_id . " -Последний айдишник<br>";
		$order_goods_params = array('table' => 'order_goods');
		$order_goods_params['keyvalue'] = array();
		foreach ($_SESSION['cart'] as $key => $value) {
			$order_goods_params['keyvalue'][] = array('order_id' => $order_id, 'good_id' => $key, 'good_count' => $value['good_count']);
		}
		$result = $this->insert_new_entry($order_goods_params);
		if (!$result) {
			return false; //если чо не так, выходим на фиг
		}
		//var_dump($result); echo " Результат добавления товаров<br>";
		$result = $this->recount_order_summ($order_id);
		if (!$result) {
			return false; //если чо не так, выходим на фиг
		}
		unset($_SESSION['cart']);
		$_SESSION['have_draft'] = true; //Запомним, что теперь у нас есть черновик, и в след. раз полезем туда вместо корзины. Не забыть переставить на ФОЛС, когда переправим заказ в обработку. Тогда можно снова начать набивать корзину.
		return true;
	}

	protected function create_new_order($user, $goods, $status)
	{
		//это для админов.. или нет? как они это будут все делать? брр.. не понимаю пока
	}

	protected /*public*/ function get_draft_id_subquery()
	{
		global $security_pass;
		$user_id = $security_pass->get_user_id();
		$subquery = "(SELECT `id` FROM `orders` WHERE `user_id`=" . $user_id . " AND `order_status`=1 LIMIT 1)";
		return $subquery;
	}

	protected /*public*/ function recount_order_summ($order_id)
	{
		global $link;
		if ($order_id === 'draft') {
			$subquery = $this->get_draft_id_subquery();
			//К сожалению, MySQL не даем мне так схитрить и использовать в этом месте подзапрос вместо айди. Мол обратиться в подзапросе к той же таблице, которую я хочу апдейтить.. А такая задумка была веселая. Ну будем костылять, получать айди запросом.
			$order_id = mysqli_fetch_assoc( mysqli_query($link, $subquery))['id'];

		} else {
			$order_id = (int)$order_id;
		}
		$query = "SELECT SUM(" . DB_NAME . ".`order_goods`.`good_count` * " . DB_NAME . ".`goods`.`price`) as 'total_amount' FROM " . DB_NAME . ".`order_goods` INNER JOIN " . DB_NAME . ".`goods` ON `goods`.`id`= `order_goods`.`good_id` WHERE `order_goods`.`order_id` = " . $order_id; //запарился указывать DB_NAME с другой стороны, если у нас будут разные базы, то легко будет вставить нужные константы..
		//echo 'запрос на пересчет:' . $query . '<br>';
		$result = mysqli_query($link, $query);
		//var_dump(mysqli_error_list($link));
		$total_amount = mysqli_fetch_assoc($result)['total_amount'];
		/*Короче, проблема. Если мы удалили все товары из заказа, то запрос дает нам НУЛЛ.*/
		//echo 'Новая сумма:' . $total_amount . '<br>';
		//echo 'Тип переменой новой суммы:' .gettype($total_amount) . '<br>';

		if($total_amount === NULL) { //на тот случай, если в заказе сейчас нет товаров, тогда подсчет суммы даст нам нулл
			$total_amount = 0;
		}
		//var_dump($result);
		//var_dump($total_amount);
		$query = "UPDATE " . DB_NAME . ".`orders` SET `total_amount`='" . $total_amount . "' WHERE `id`=" . $order_id;
		//echo 'запрос на апдейт суммы:' . $query . '<br>';
		$result = mysqli_query($link, $query);
		echo 'Попытка апдейта суммы, ошибки: ';
		var_dump(mysqli_error_list($link));
		echo '<br>';

		return (bool)$result;
	}

	public function check_draft(){
		global $security_pass;
		$user_id = $security_pass->get_user_id();
		$query = "SELECT COUNT(*) AS 'drafts' FROM `orders` WHERE `user_id`=" . $user_id . " AND `order_status` ='1'";
		$result = $this->read_any_table_ready_query($query);
		return (bool)$result[0]['drafts'];//ващет, если драфтов больше одного, то это не есть нормально. И в дальнейшем бы обращаемся к какому-то одному, который нам выдаст база, а выдаст она нам, при отсутствии других условий, более ранний, так как сортировать будет по айдишнику. А лучше бы более поздний. Поэтому надо в субкуюери добавить ордербай по времени по убиванию, тогда самый первый будет самый поздний
	}

	public function get_payment_methods_data()
	{
		$query_params = array(
			'table' => 'payment_methods',
			'columns' => array('id AS payment_id', 'method_name_rus AS payment_name')
			);
		$result = $this->read_any_table($query_params);
		return $result;
	}

	public function get_delivery_methods_data()
	{
		$query_params = array(
			'table' => 'delivery_methods',
			'columns' => array('id AS delivery_id', 'method_name_rus AS delivery_name')
			);
		$result = $this->read_any_table($query_params);
		return $result;
	}

	public function edit_draft($push_order = false)
	{
		global $link;
		//var_dump($_POST);
		//echo "<br> ПОСТ ГУУУУДС: ";
		//var_dump($_POST['goods']);
		$order_id_subquery = $this->get_draft_id_subquery();
		//echo 'order_id_subquery: ' . $order_id_subquery . '<br>';
		$goods_to_delete = array();
		$goods_to_update = array();
		if (isset($_POST['goods']['delete'])) {
			//echo " || Ёу, есть удаление в ПОСТ || ";
			foreach ($_POST['goods']['delete'] as $value) {
				$goods_to_delete[] = $value;
				//echo " || Ёу, мы удаляем товар || ";
			}
		}
		if (isset($_POST['goods']['good_id'])) {
			foreach ($_POST['goods']['good_id'] as $key => $value) {
				if ( in_array($value, $goods_to_delete) ) {
					continue; //Если товар помечен на удаление, то идем дальше
				} else {
					if ( (int)$_POST['goods']['good_count'][$key] <= 0){ //блин как же геморно этот ПОСТ разбирать
						$goods_to_delete[] = $value;
					}
					$goods_to_update[] = array(
							'table' => 'order_goods',
							'keyvalue' => array(
								'good_count' => $_POST['goods']['good_count'][$key]
								),
							'where' => " WHERE `order_id`=" . $order_id_subquery . " AND `good_id`=" . (int)$value 
							);
					/*Дело в том,  что айдишники и количество товаров идут параллельно в нумерованных массивах, поэтому если мы хотим узнать, какое количество присвоить товару, мы должны взять айди из одного массива, и количество из соседнего массива под тем же ключом (там нумерованные массивы, автоматически создающиеся из ПОСТ*/
					/*Потом уж я подумал, что можно это по другому сделать, и давать массив goods[good_id] со значением делит, и каунт.*/
				}
			}
		}
		foreach ($goods_to_delete as $value) {
			$params = array('table' => 'order_goods', 
				'where' => " WHERE `order_id`=" . $order_id_subquery . " AND `good_id`=" . (int)$value);
			$this->delete_entry($params);
			//var_dump(mysqli_error_list($link));
		}
		foreach ($goods_to_update as $single_update_params) {
			$this->update_any_entry($single_update_params);
		}
		$order_id = mysqli_fetch_assoc( mysqli_query($link, $order_id_subquery))['id'];//нужно получить айди до того, как мы перепортачим заказ. ЭТО ВАЖНО. Потому что заказ отправлялся нормально, но сумма не пересчитывалась. ПОтому что сумма пересчитывалась по айдишнику заказа, а айдишник по подзапросу, а в подзапросе ищем статус "черновик", а статус мы, сука, поменяли, оттого при отрпавлении заказа сумма не пересчитывалась. Поэтому на фиг вся история с передачей draft в качестве аргумента, оказалась, в итоге бесполеной.
		$order_details_params = array('table' => 'orders');
		$delivery_method = (int)$_POST['delivery_id'];
		$payment_method = (int)$_POST['payment_id'];
		//global $link;
		$users_comment = mysqli_real_escape_string($link, $_POST['users_comment']);
		$delivery_address = mysqli_real_escape_string($link, $_POST['delivery_address']);
		$order_details_params['keyvalue'] = array(
			'delivery_method' => $delivery_method,
			'payment_method' => $payment_method,
			'users_comment' => $users_comment,
			'delivery_address' => $delivery_address
			);
		if($push_order) {
			$order_details_params['keyvalue']['order_status'] = 2;
		}
		//$order_id = mysqli_fetch_assoc( mysqli_query($link, $order_id_subquery))['id'];//пришлось таки запрашивать отдельным 
		$order_details_params['where'] = "WHERE `id`=" . $order_id;

		$this->update_any_entry($order_details_params);

		//$this->update_any_entry($goods_to_update_keyvalue);
		//echo 'Товары на удаление: '; var_dump($goods_to_delete);
		//echo '<br>Товары на апдейт: '; var_dump($goods_to_update);
		//echo '<br>';
		$this->recount_order_summ($order_id);
		if(!$push_order){
		header('Location: ./?ctrl=ordering&action=cart');
		}
	}
	/*
	public function get_users_order_draft()
	{
		global $security_pass;
		$user_id = $security_pass->get_user_id();
		$query_params = array();
	}*/
/* это тут не нужно, это из шоп-модели
	public function get_categories_list()
	{
		$query_params = array('table' => 'categories');
		$query_params['columns'] = array('id', 'category_name');
		$result = $this->read_any_table($query_params);
		return $result;
	}*/
	public function add_good_to_draft($good_id)
	{
		global $link;
		$order_id_subquery = $this->get_draft_id_subquery();
		$order_id = mysqli_fetch_assoc( mysqli_query($link, $order_id_subquery))['id'];
		$insert_params = array( //функция вставки принимает массив массивов, на случай вставления многих значений. Если вставляем одно, то массив с одним массивом
			'table' => 'order_goods',
			'keyvalue' =>
				array(
					array(
						'order_id' => $this->get_draft_id_subquery(),
						'good_id' => (int)$good_id,
						'good_count' => 1
					)
				)
			);
		$result = $this->insert_new_entry($insert_params, true); //true здесь это флаг, который говорит, что мы используем подзапрос, и поэтому не надо окавычивать вводимые данные. Все данные у наз безопасны сейчас.
		if(!$result){
			global $link;
			$error_list = mysqli_error_list($link);
			if($error_list[0]['errno'] === 1062) {
				return 'already_exists';
			}			
			return (bool)$result;
		}
		$this->recount_order_summ($order_id);
		return (bool)$result;
	}
}