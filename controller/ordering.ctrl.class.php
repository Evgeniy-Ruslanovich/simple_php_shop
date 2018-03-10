<?php

//echo "hello, ordering controller<br>";
class Order_controller //extends Database_master
{
	
	public $layout = 'layout.php';
	public $template;
	protected $output_data = array();

	function __construct($action)
	{	
		global $security_pass;
		if(!$security_pass->auth_status){//безобразие! почему это свойство публичное? надо переделать, и получать через геттер
			$this->output_data['message'] = 'Для этого действия вам нужно войти или зарегистрироваться';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=user&action=login">Войти</a>';
			$this->template = 'shop_message.php';
			return;//короче, все действия в этом контроллере требуют авторизации. Но кое-где я уже указал сообщения отдельные, потом можно будет удалить. Получится не так красиво, но будет тратиться меньше ресурсов на прочитывание и исполнение лишнего кода.
		}	
		$function_names = array(
			'cart' => 'view_cart',
			'add_to_cart' => 'add_to_cart',
			'delete_good' => 'delete_from_cart',
			'edit_cart' => 'edit_session_cart',
			'edit_quantity' => 'edit_good_quantity',
			'save_draft' => 'save_draft',
			'push_order' => 'push_order',
			'list' => 'view_user_orders_list',
			'order' => 'view_user_single_order');
		$function = (isset($function_names[$action])) ? $function_names[$action] : $function_names['list'];
		//echo $function_names[$subctrl];
		$this->$function();
	}

	/*РАБОТА С КОРЗИНОЙ*/
		/*Просмотр корзины, либо из сессии, либо черновика заказа*/
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
		/*Просмотр корзины из сессии*/
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

		/*Просмотр корзины-черновика заказа*/
		protected function view_order_draft()
		{
			//НАДА написать видимо чере функцию view_user_single_order() или что-то вроде того, а вообще не нужно на него смотреть, нужно сразу редактировать
		}

		/*Добавление в корзину, либо в сессионную, либо в черновик заказа*/
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

		/*Добавление товара в сессионную корзину*/
		protected function add_good_to_session_cart()
		{
			if ( !isset($_SESSION['cart']) ) {
				$_SESSION['cart'] = array();
			}
			/*В сессии будет корзина, это массив, где ключи - это айди товара в виде целого числа (пользователь может смухлевать на клиенте, и заменить айди, которое посылается через ПОСТ, на товар, которого нет в магазине, но я хрен знает, что с этим делать, можно ходить каждый раз в базу и проверять наличие такого товара. Пока не будем этим заморачиваться.*/
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
		}

		/*Пользователь добавляет товары из магазина. Потом, когда он просматривает корзину, то может изменить количество товара, потому что по умолчанию добавляется одна штука. Либо изменить количество. Со странички редактирования приходит ПОСТ в котором айди товара, количество, и метка "удалить", из чекбокса*/
		protected function edit_session_cart()
		{
			$i = 0;
			foreach ($_POST['good_id'] as $value) {
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
		}

		/*Вспомогательная функция для установки новых значений в сессионную корзину*/
		protected function set_new_values_to_session_cart($good_id, $delete, $quantity)
		{
			if ($delete || (int)$quantity <= 0) {
				unset($_SESSION['cart'][(int)$good_id]);
			} else {
				$_SESSION['cart'][(int)$good_id]['quantity'] = (int)$quantity;
			}
		}

		/*Когда пользователь наигрался с корзиной, нажатием кнопочки может перейти к оформлению заказа, то есть указать адрес. спооб оплаты и т.п. При этом заказ из сессии сохраняется в черновик, а сессионная корзина удаляется*/
		protected function save_draft()
		{
			$this->output_data['message'] = 'Сохранение корзины в черновик заказа';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=cart">Просмотреть корзину</a> | <a href="./">Перейти в магазин</a>';
			$this->template = 'shop_message.php';
			require_once MODEL_DIR . DIRECTORY_SEPARATOR . 'orders_data.class.php';
			$order_data = new Orders_data();
			$order_data->save_draft();
		}

	/*ПРОСМОТР ПОЛЬЗОВАТЕЛЕМ СВОИХ ЗАКАЗОВ*/
		protected function view_user_orders_list()
		{
			global $security_pass;	
			if($security_pass->auth_status) {
				require_once MODEL_DIR . DIRECTORY_SEPARATOR . 'orders_data.class.php';
				$order_data = new Orders_data();
				//$order_data->recount_order_summ(5);//Это временно, просто проверка
				$this->output_data['message'] = 'просмотр списка заказов';
				$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=list">К списку заказов</a>';
				$this->template = 'shop_message.php';
			} else {
				$this->output_data['message'] = 'Чтобы проверить свои заказы, вам нужно войти или зарегистрироваться';
				$this->output_data['suggested_link'] = '<a href="./?ctrl=user&action=login">Войти</a>';
				$this->template = 'shop_message.php';
			}
		}



	public function get_output_data()
	{
		return $this->output_data;
	}

	protected function add_good_to_draft()
	{
		//НАДА написать, видимо через функцию update_order() а вообще все, что тут творится, связано с юзерами, а не с админами, для админов другие функции
	}



	public function view_user_single_order()
	{
		if (isset($_GET['order'])) {
			require_once MODEL_DIR . DIRECTORY_SEPARATOR . 'orders_data.class.php';
			$order_data = new Orders_data();
			$this->output_data['single_order_data'] = $order_data->get_public_single_order( (int)$_GET['order']); //мало ли какую гадость передадут через ГЕТ, надо привести к целому
			if ($this->output_data['single_order_data'][0] === 'empty_result') {
				$this->output_data['message'] = 'Такой заказ не обнаружен';
				unset($this->output_data['single_order_data']);
				$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=list">К списку заказов</a>';
				$this->template = 'shop_message.php';
			} else {
				$this->template = 'single_order_tpl.php';
			}
		} else {
			$this->view_user_orders_list();//если нам не дали айдишник заказа, то отправляем его смотреть список
		}		
	}

}