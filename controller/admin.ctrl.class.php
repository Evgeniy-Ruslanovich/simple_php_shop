<?php
require_once(MODEL_DIR . '/database_master.class.php');

/**
* 
*/
class Admin_controller //extends Database_master
{
	
	public $layout = 'layout_admin.php';
	public $template;
	protected $output_data = array();

	function __construct($subctrl, $action)
	{

		$function_names = array(
			'dashboard' => 'open_admin_dashboard',
			'orders' => 'open_orders_handler',
			'users' => 'open_users_management',
			'roles' => 'open_roles_management',
			'goods' => 'open_goods_management',
			'categories' => 'open_categories_management');
		//echo $function_names[$subctrl];
		$this->$function_names[$subctrl]($action);
	}

	public function get_output_data()
	{
		return $this->output_data;
	}
	protected function open_admin_dashboard($action) {
		$this->output_data['example_subctrl'] = 'Dashboard';
		$this->template = 'admin_default_tpl.php';
	}
	//Обработка заказов
	protected function open_orders_handler($action) {
		/*action get list*/
		global $security_pass;
		$permission = $security_pass->check_permission('view_orders');
		if($permission) {
			$function_names = array(
			'default' => 'get_orders_list',
			'list' => 'get_goods_list',
			'good' => 'get_single_good',
			'new_good' => 'create_new_good',
			'edit_good' => 'edit_good',
			'delete_good' => 'delete_good');
		$function = (isset($function_names[$action])) ? $function_names[$action] : $function_names['default'];
		$this->$function();
		} else {
			$this->not_permitted();
		}
		/*
		require_once(MODEL_DIR . '/orders_data.class.php');
		$orders_data = new Orders_data();
		$orders_list_array = $orders_data->get_admin_orders_list();
		var_dump($orders_list_array);
		$this->output_data['example_subctrl'] = 'Заказы. Есть разрешение: ' . $permission;
		$this->output_data['orders_list_array'] = $orders_list_array;
		$this->template = 'admin_orders_tpl.php';*/
		/*$query_params = array(
			'table' => 'orders',
			'columns' => array('id', 'user_id', 'order_time', 'total_amount', 'order_status'),
			);
		if ( isset($_GET['order_status']) ) {
			$query_params['where'] = 'WHERE `order_status`=';
		}*/

		}

		protected function get_orders_list() {
			global $security_pass;
			$permission = $security_pass->check_permission('view_orders');
			require_once(MODEL_DIR . '/orders_data.class.php');
			$orders_data = new Orders_data();
			$orders_list_array = $orders_data->get_admin_orders_list();
			var_dump($orders_list_array);
			$this->output_data['example_subctrl'] = 'Заказы. Есть разрешение: ' . $permission;
			$this->output_data['orders_list_array'] = $orders_list_array;
			$this->template = 'admin_orders_tpl.php';
		}

		protected function edit_order(){

		}

		protected function get_single_order(){

		}
	//Управление товарами
	protected function open_goods_management($action) {
		//echo "hello " . $action . '<br>';
		$function_names = array(
			'default' => 'get_goods_list',
			'list' => 'get_goods_list',
			'good' => 'get_single_good',
			'new_good' => 'create_new_good',
			'edit_good' => 'edit_good',
			'delete_good' => 'delete_good');
		$function = (isset($function_names[$action])) ? $function_names[$action] : $function_names['default'];
		
		//echo $function . '<br>';
		//$this->$function_names[$action]();
		$this->$function();
		}

		protected function create_new_good(){
			echo "Новый товар";
		}

		protected function get_goods_list(){
			$this->output_data['example_subctrl'] = 'Список товаров';
			$this->template = 'admin_goods_list_tpl.php';
			require_once(MODEL_DIR . '/goods_data.class.php');
			$goods_data = new Goods_data();
			$query_params = array();
			$goods_data_array = $goods_data->get_public_goods_list($query_params);
			$this->output_data['goods_data_array'] = $goods_data_array;
		}

		protected function get_single_good(){
			//echo "Посмотреть один товар";
			if (isset($_GET['good'])) {
				require_once(MODEL_DIR . '/goods_data.class.php');
				$single_good_data = new Goods_data();
				$good_data_array = $single_good_data->get_admin_single_good( (int)$_GET['good'] );
				if(!$good_data_array) {
					$this->output_data['error'] = 'Товар не найден';
					$this->template = 'error_layout.php';
				}
				$this->output_data['good_data_array'] = $good_data_array;
				$this->template = 'admin_single_good_tpl.php';
				//var_dump($good_data_array);
			} else {
				header('Location: ./?ctrl=admin&subctrl=goods');
			}
		}
			

		protected function edit_good(){
			echo "Новый товар";
		}

		protected function delete_good(){
			echo "Удалить товар";
		}
	//Управление категориями
	protected function open_categories_management($action) {
		$this->output_data['example_subctrl'] = 'Категории';
		$this->template = 'admin_default_tpl.php';
	}

	protected function access_denied($action) {

	}
	protected function open_users_management($action) {
		$this->output_data['example_subctrl'] = 'Пользователи';
		$this->template = 'admin_default_tpl.php';
	}

	protected function open_roles_management($action) {
		$this->output_data['example_subctrl'] = 'Роли';
		$this->template = 'admin_default_tpl.php';
	}

	protected function not_permitted() {
		$this->output_data['example_subctrl'] = 'У вас нет допуска для этого действия';
		$this->template = 'admin_default_tpl.php';
	}
}
