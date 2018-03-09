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
			'add_to_cart' => 'add_to_cart',
			'delete_good' => 'delete_from_cart',
			'edit_cart' => 'edit_session_cart',
			'edit_quantity' => 'edit_good_quantity',
			'save_draft' => 'save_draft',
			'push_order' => 'push_order',
			'list' => 'view_user_orders_list');
		$function = (isset($function_names[$action])) ? $function_names[$action] : $function_names['list'];
		//echo $function_names[$subctrl];
		$this->$function();
	}

	protected function view_cart()
	{
		global $security_pass;	
		if($security_pass->auth_status) {
			$have_draft = false;//здесь должна быть проверка на наличие черновика
			if ($have_draft) {
				$this->view_order_draft();
			} else {
				$this->view_session_cart();
			}
		} else {
			$this->output_data['message'] = 'Чтобы добавлять товары в корзину, вам нужно войти или зарегистрироваться';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=user&action=login">Войти</a>';
			$this->template = 'shop_message.php';
		}
	}

	protected function view_session_cart()
	{
		if (isset($_SESSION['cart']) && count($_SESSION['cart'])) {
			$this->output_data['cart'] = array();
			foreach ($_SESSION['cart'] as $good_id => $good_data) {
				$this->output_data['cart'][] = array(
					'good_id' => $good_id, 
					'product_name' => $good_data['product_name'], 
					'price' => $good_data['price'], 
					'quantity' => $good_data['quantity']);
				$this->template = 'cart.php';
			}
		} else {
			$this->output_data['message'] = 'Ваша корзина пуста';
			$this->output_data['suggested_link'] = '<a href="./">Перейти в магазин</a>';
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
		echo "вызван контроллер добавления в корзину";
		global $security_pass;	
		if($security_pass->auth_status) {
			$have_draft = false;//здесь должна быть проверка на наличие черновика
			if ($have_draft) {
				$this->add_good_to_draft();
			} else {
				$this->add_good_to_session_cart();
			}
		} else {
			$this->output_data['message'] = 'Чтобы добавлять товары в корзину, вам нужно войти или зарегистрироваться';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=user&action=login">Войти</a>';
			$this->template = 'shop_message.php';
		}		
	}

	protected function add_good_to_session_cart()
	{
		//echo "вызвана функция добавления в сессионную корзину";
		if ( !isset($_SESSION['cart']) ) {
			$_SESSION['cart'] = array();
		}
		/*В сессии будет корзина, это массив, где ключи - это айди товара в виде целого числа (пользователь может смухлевать на клиенте, и заменить айди, которое посылается через ПОСТ, но я хрен знает, что с этим делать, можно ходить каждый раз в базу и проверять наличие такого товара. Пока не будем этим заморачиваться.*/
		if (isset($_POST['good_id'])) {
			var_dump($_POST);
			if (isset($_SESSION['cart'][(int)$_POST['good_id']])) { //случай. когда товар уже есть в корзине
				$this->output_data['message'] = 'Товар уже есть в корзине';
				$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=cart">Просмотреть корзину</a>';
				$this->template = 'shop_message.php';
			} else { //когда товара еще нет в корзине, кладем его, и устанавливаем количество 1. Имя товара тоже сохраняем, чтобы был список
				$_SESSION['cart'][(int)$_POST['good_id']] = array(
									'product_name' => $_POST['product_name'],
									'price' => $_POST['price'],
									'quantity' => 1);
				$this->output_data['message'] = 'Товар добавлен в корзину';
				$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=cart">Просмотреть корзину</a> | <a href="./">Перейти в магазин</a>';
				$this->template = 'shop_message.php';
			}
		} else {
			echo "Не передан товар, что за фигня? Юзер, хватит баловаться!";//потом придумаю, куда переадресовать
		}
		/*
		$this->output_data['message'] = 'просмотр корзины';
		$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=list">К списку заказов</a>';
		$this->template = 'shop_message.php';*/
	}

	protected function edit_session_cart()
	{
		//var_dump($_POST); echo "<br>";
		$i = 0;
		foreach ($_POST['good_id'] as  $value) {
			$delete = false;
			if(isset($_POST['delete'])){
				if (in_array($value, $_POST['delete'])) {
					$delete = true;
				}
			}
			$quantity = $_POST['quantity'][$i];
			$i++;
			$this->set_new_values_to_session_cart($value, $delete, $quantity);
		}
		header('Location: ./?ctrl=ordering&action=cart');
		die();
		/*
		$this->output_data['message'] = 'Редактирование сессионной корзины';
		$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=cart">Просмотреть корзину</a> | <a href="./">Перейти в магазин</a>';
		$this->template = 'shop_message.php';*/
	}

	public function get_output_data()
	{
		return $this->output_data;
	}

	protected function set_new_values_to_session_cart($good_id, $delete, $quantity)
	{
		//echo 'Устанавливаются новые значения для ' . $good_id . ' Удалить: ' . $delete . 'Количество: ' . $quantity . '<br>';
		if ($delete) {
			unset($_SESSION['cart'][(int)$good_id]);
		} else {
			$_SESSION['cart'][(int)$good_id]['quantity'] = (int)$quantity;
		}
	}

	protected function save_draft()
	{
		$this->output_data['message'] = 'Сохранение корзины в черновик заказа';
		$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=cart">Просмотреть корзину</a> | <a href="./">Перейти в магазин</a>';
		$this->template = 'shop_message.php';
		require_once MODEL_DIR . DIRECTORY_SEPARATOR . 'orders_data.class.php';
		$order_data = new Orders_data();
		$order_data->save_draft();

	}

}