<?php

//echo "hello, ordering controller<br>";
class Order_controller //extends Database_master
{
	
	public $layout = 'layout.php';
	public $template;
	protected $output_data = array();

	function __construct($action)
	{
		$function_names = array(
			'cart' => 'view_cart',
			'add' => 'add_to_cart',
			'delete-good' => 'delete_from_cart',
			'edit-quantity' => 'edit_good_quantity',
			'save-draft' => 'save_draft',
			'push-order' => 'push_order',
			'list' => 'view_user_orders_list');
		$function = (isset($function_names[$action])) ? $function_names[$action] : $function_names['list'];
		//echo $function_names[$subctrl];
		$this->$function();
	}

	protected function view_cart()
	{
		global $security_pass;	
		if($security_pass->auth_status) {
			$this->output_data['message'] = 'просмотр корзины';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=list">К списку заказов</a>';
			$this->template = 'shop_message.php';
		} else {
			$this->output_data['message'] = 'Чтобы добавлять товары в корзину, вам нужно войти или зарегистрироваться';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=user&action=login">Войти</a>';
			$this->template = 'shop_message.php';
		}
	}

		protected function view_user_orders_list()
	{
		global $security_pass;	
		if($security_pass->auth_status) {
			$this->output_data['message'] = 'просмотр списка заказов';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=list">К списку заказов</a>';
			$this->template = 'shop_message.php';
		} else {
			$this->output_data['message'] = 'Чтобы проверить свои заказы, вам нужно войти или зарегистрироваться';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=user&action=login">Войти</a>';
			$this->template = 'shop_message.php';
		}
	}

	protected function add_to_cart()
	{
		global $security_pass;	
		if($security_pass->auth_status) {

			if ( !isset($_SESSION['cart']) ) {
				$_SESSION['cart'] = array();
			}

			if (isset($_POST['good_id'])) {
				$_SESSION['cart'] 
			}

			$this->output_data['message'] = 'просмотр корзины';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=list">К списку заказов</a>';
			$this->template = 'shop_message.php';
		} else {
			$this->output_data['message'] = 'Чтобы добавлять товары в корзину, вам нужно войти или зарегистрироваться';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=user&action=login">Войти</a>';
			$this->template = 'shop_message.php';
		}
	}

	public function get_output_data()
	{
		return $this->output_data;
	}
}