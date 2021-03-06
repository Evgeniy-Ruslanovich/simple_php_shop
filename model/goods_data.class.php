<?php
/*
*
*/
require_once MODEL_DIR . DIRECTORY_SEPARATOR . 'database_master.class.php';

class Goods_data extends Database_master
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



	public function get_public_goods_list($goods_params) {
		$query_params = array('table' => 'goods');
		$query_params['columns'] = array('goods.id', 'product_name', 'category', 'price', 'good_main_photo', 'categories.category_name');//Если хотим выбрать ВСЕ, то массив надо оставлять ПУСТЫМ. Мы ЖОСКА задаем поля, и вообще все что можно задать, чтобы ократить до минимума пользовательский ввод в базу. Юзер, по сути, вводит только свой плогин-пароль, и комментарий к заказу.. пока что.
		//$query_params['hidden'] = 0; //
		$query_params['where'] = $this->build_where_statement_for_public_goods_list($goods_params);
		$query_params['join'] = "INNER JOIN " . DB_NAME . ".`categories` ON `goods`.`category` = `categories`.`id`";
		//$query_params['order_by'] = null;
		$result = $this->read_any_table($query_params);
		//var_dump($result);
		//echo "<BR>";
		return $result;

	}

	public function get_public_single_good($good_id) {
		$query_params = array('table' => 'goods');
		$query_params['columns'] = array('goods.id', 'product_name', 'category', 'price', 'good_main_photo', 'product_description', 'categories.category_name');
		$query_params['where'] = 'WHERE `goods`.`id`=\'' . (int)$good_id . '\' ';
		$query_params['join'] = "INNER JOIN " . DB_NAME . ".`categories` ON `goods`.`category` = `categories`.`id`";
		$result = $this->read_any_table($query_params)[0];
		//var_dump($result);
		//echo "<BR>";
		return $result;
	}
	
	public function get_admin_single_good($good_id) {
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
/*SELECT `goods`.`id`,`product_name`,`product_description`,`category`,`categories`.`category_name`,`categories`.`parent_category` FROM `goods` INNER JOIN `categories` ON `goods`.`category` = `categories`.`id` WHERE 1*/
	protected function build_where_statement_for_public_goods_list($goods_params)
	{//для публичного магазина нам нужно только знать категорию, потому что скрытые по умолчанию не показываются
		//$where_statement = 'WHERE `hidden`=0';  //скрытые нам не нужны
		$where_statement = '';
		if (isset($goods_params['categories']) && count($goods_params['categories']))  {
			//$where_statement .= ' AND ';
			foreach ($goods_params['categories'] as $key => $categoriy) {
				$where_statement .= 'OR `category`=\''. $categoriy .'\' OR `parent_category`=\''. $categoriy .'\'';//пока что двухуровневая вложенность категорий				
			}
			$where_statement = substr($where_statement, 3); //обрезаем первый OR
			$where_statement = 'AND (' . $where_statement . ')';	
		}
			$where_statement = 'WHERE `hidden`=\'0\' ' . $where_statement . '';
		return $where_statement;
	}

	public function get_categories_list()
	{
		$query_params = array('table' => 'categories');
		$query_params['columns'] = array('id', 'category_name');
		$result = $this->read_any_table($query_params);
		return $result;
	}
}