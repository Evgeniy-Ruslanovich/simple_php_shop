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
		if(!$security_pass->get_auth_status()){//безобразие! почему это свойство публичное? надо переделать, и получать через геттер
			$this->output_data['message'] = 'Для этого действия вам нужно войти или зарегистрироваться';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=user&action=login">Войти</a>';
			$this->template = 'shop_message.php';
			return;//короче, все действия в этом контроллере требуют авторизации. Но кое-где я уже указал сообщения отдельные, потом можно будет удалить. Получится не так красиво, но будет тратиться меньше ресурсов на прочитывание и исполнение лишнего кода.
		}	
		$function_names = array(
			'cart' => 'view_cart',
			'add_to_cart' => 'add_to_cart',
			//'delete_good' => 'delete_from_cart',
			'edit_cart' => 'edit_session_cart',
			'edit_good_count' => 'edit_good_count',//это вроде не нужно
			'save_draft' => 'save_draft',
			'push_order' => 'push_order',
			'list' => 'view_user_orders_list',
			'order' => 'view_user_single_order',
			'edit_draft_detail' => 'edit_draft_detail',
			'edit_draft' => 'edit_draft');
		$function = (isset($function_names[$action])) ? $function_names[$action] : $function_names['list'];
		//echo $function_names[$subctrl];
		$this->$function();
	}

	/*РАБОТА С КОРЗИНОЙ*/
		/*Просмотр корзины, либо из сессии, либо черновика заказа*/
		protected function view_cart()
		{
			//echo "вызван просмотр корзины";
			global $security_pass;	
			if($security_pass->get_auth_status()) {
				$have_draft = $this->check_draft();//здесь должна быть проверка на наличие черновика
				//echo 'Наличие черновика:' . $have_draft . '<br>';
				if ($have_draft) {
					$this->view_order_draft();
				} else {
					//echo "ветка просмотра драфта";
					$this->view_session_cart();
				}
			} else {
				$this->output_data['message'] = 'Чтобы добавлять товары в корзину, вам нужно войти или зарегистрироваться';
				$this->output_data['suggested_link'] = '<a href="./?ctrl=user&action=login">Войти</a>';
				$this->template = 'shop_message.php';
			}
		}

		/*Узнаем, есть ли у человека черновик заказа в базе данных. Если есть, то все добавления/удаления товаров переадресуем туда, и делаем запись в сессии, что есть черновик*/
		protected function check_draft()
		{
			if (isset($_SESSION['have_draft'])){
				return $_SESSION['have_draft'];
			} else {
				require_once MODEL_DIR . DIRECTORY_SEPARATOR . 'orders_data.class.php';
				$draft_data = new Orders_data();
				$have_draft = $draft_data->check_draft();
				$_SESSION['have_draft'] = $have_draft;
				return $have_draft;
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
						'good_count' => $good_data['good_count']);
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
			$this->output_data['message'] = 'Просмотр черновика заказа';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=user&action=login">Войти</a>';
			$this->template = 'shop_message.php';

			require_once MODEL_DIR . DIRECTORY_SEPARATOR . 'orders_data.class.php';
			$order_data = new Orders_data();
			$this->output_data['payment_methods_data'] = $order_data->get_payment_methods_data('draft');
			$this->output_data['delivery_methods_data'] = $order_data->get_delivery_methods_data('draft');
			$this->output_data['single_order_data'] = $order_data->get_public_single_order('draft');
			$this->template = 'draft_tpl.php';
		}

		/*Добавление в корзину, либо в сессионную, либо в черновик заказа*/
		protected function add_to_cart()
		{
			echo "вызван контроллер добавления в корзину<br>";
			global $security_pass;	
			/*if($security_pass->get_auth_status()) {*/ //тут мы уберем пока эту шляпу, потому что поставили заслон в самом начале контроллера, в конструкте
				$have_draft = $this->check_draft();//здесь должна быть проверка на наличие черновика
				if ($have_draft) {
					$this->add_good_to_draft();
				} else {
					$this->add_good_to_session_cart();
				}
			/*} else {
				$this->output_data['message'] = 'Чтобы добавлять товары в корзину, вам нужно войти или зарегистрироваться';
				$this->output_data['suggested_link'] = '<a href="./?ctrl=user&action=login">Войти</a>';
				$this->template = 'shop_message.php';
			}	*/	
		}

		/*Добавление товара в сессионную корзину*/
		protected function add_good_to_session_cart()
		{
			if ( !isset($_SESSION['cart']) ) {
				$_SESSION['cart'] = array();//если корзины еще нет, заводим корзину
			}
			/*В сессии будет корзина, это массив, где ключи - это айди товара в виде целого числа (пользователь может смухлевать на клиенте, и заменить айди, которое посылается через ПОСТ, на товар, которого нет в магазине, но я хрен знает, что с этим делать, можно ходить каждый раз в базу и проверять наличие такого товара. Пока не будем этим заморачиваться.*/
			if (isset($_POST['good_id'])) {
				//var_dump($_POST);
				if (isset($_SESSION['cart'][(int)$_POST['good_id']])) { //случай. когда товар уже есть в корзине
					$this->output_data['message'] = 'Товар уже есть в корзине';
					$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=cart">Просмотреть корзину</a>';
					$this->template = 'shop_message.php';
				} else { //когда товара еще нет в корзине, кладем его, и устанавливаем количество 1. Имя товара тоже сохраняем, чтобы был список
					$_SESSION['cart'][(int)$_POST['good_id']] = array(
										'product_name' => $_POST['product_name'],
										'price' => $_POST['price'],
										'good_count' => 1);
					$this->output_data['message'] = 'Товар добавлен в корзину';
					$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=cart">Просмотреть корзину</a> | <a href="./">Перейти в магазин</a>';
					$this->template = 'shop_message.php';
				}
			} else {
				echo "Не передан товар, что за фигня? Юзер, хватит баловаться!";//потом придумаю, куда переадресовать
			}
		}

		/*Добавление товара в черновик, это вам не тоже самое, что в сессионную корзину.. или.. хз*/
		protected function add_good_to_draft()
		{
			//НАДА написать, видимо через функцию update_order() а вообще все, что тут творится, связано с юзерами, а не с админами, для админов другие функции

			/*КОРОЧЕ, надо делать вставку с подзапросом, типа такого:
			INSERT INTO `order_goods`(`order_id`, `good_id`, `good_count`) VALUES ((SELECT `id` FROM `orders` WHERE `user_id` = 1 AND `order_status`=1),11,1)
			Тут подзапрос дает нам айди нужного заказа. Мы не будем сначала получать айди заказа, а потом передавать его, сделаем это сразу в эскуэли.
			Тут все гениально и просто. Мы должны для добавления товара передать айди заказа, айди товара, и количество - 1. Откуда мы возьмемайди заказа, если нам нужно добавить в драфт? Не искатьже этот драфт сперва. И вот, тот драфт мы получаем в поздапросе. То есть, грубо говоря, эскуэль, сначала, конечно, ищет драфт, но это делается в виде подзапроса. Айди драфта он вставляет туда, где Values, на первое место, и все. мы об это вообще не думаем, все на автомате. Нам не нужно в скрипт будет передавать айди драфта, передадим только айди юзера, а драфт эскуэль сам найдет. 
			#1062 - Дублирующаяся запись '2-11' по ключу 'PRIMARY'

			*/
			/*должно что-то такое получитьсяINSERT INTO `order_goods`(`order_id`, `good_id`, `good_count`) VALUES ((SELECT `id` FROM `orders` WHERE `user_id`=4 AND `order_status`=1 LIMIT 1),8,1)*/
			//echo "<br><b>POST:  </b> ";
			//var_dump($_POST);
			//echo "<br>";
			$this->template = 'shop_message.php';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=cart">Просмотреть корзину</a> | <a href="./">Перейти в магазин</a>';
			if(isset($_POST['good_id'])){
				require_once MODEL_DIR . DIRECTORY_SEPARATOR . 'orders_data.class.php';
				$order_data = new Orders_data();
				$result = $order_data->add_good_to_draft((int)$_POST['good_id']);

				//echo "<br><b>$result возвращаемый в контроллер  </b> ";
				//var_dump($result);
				//echo "<br>";

				if ($result === 'already_exists') {
					$this->output_data['message'] = 'Товар уже есть в корзине.';
				} else {
					if ($result === true) {
						$this->output_data['message'] = 'Товар добавлен в корзину.';
					} else {
						$this->output_data['message'] = 'Произошла ошибка, добавление не удалось. Попробуйте еще раз. Если ошибка повторится, обратитесь к службе поддержки';
					}
				}
			} else {
				$this->output_data['message'] = 'Произошла ошибка. Не передан идентификатор товара.';
			}
		}
		/*Пользователь добавляет товары из магазина. Потом, когда он просматривает корзину, то может изменить количество товара, потому что по умолчанию добавляется одна штука. Либо изменить количество. Со странички редактирования приходит ПОСТ в котором айди товара, количество, и метка "удалить", из чекбокса*/

		protected function edit_draft_detail()
		{
			$this->output_data['message'] = 'edit_draft_detail';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=cart">Просмотреть корзину</a>';
			$this->template = 'shop_message.php';
			//var_dump($_POST);
		}

		protected function edit_draft_goods()
		{
			$this->output_data['message'] = 'edit_draft_goods';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=cart">Просмотреть корзину</a>';
			$this->template = 'shop_message.php';
			//var_dump($_POST);
		}

		protected function edit_draft()
		{
			if(isset($_POST['edit_draft_hidden'])) {
				//echo "Вызвана ветка редактирования черновика";
				$push_order = false;//Это оначает перевод заказа из состояния черновика в активный заказ в статусе "ждет подтверждения"
				if(isset($_POST['push_order'])){
					$push_order = true;
					$_SESSION['have_draft'] = false;
				}
				require_once MODEL_DIR . DIRECTORY_SEPARATOR . 'orders_data.class.php';
				$order_data = new Orders_data();
				$order_data->edit_draft($push_order);
			} else {
				$this->view_cart();
			}
			$this->output_data['message'] = 'Заказ передан в магазин';
			$this->output_data['suggested_link'] = '<a href="./?ctrl=ordering&action=list">Просмотреть мои заказы</a>';
			$this->template = 'shop_message.php';
			//var_dump($_POST);
		}

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
				$good_count = $_POST['good_count'][$i];
				$i++;
				$this->set_new_values_to_session_cart($value, $delete, $good_count);
			}
			header('Location: ./?ctrl=ordering&action=cart');
			die();
		}

		/*Вспомогательная функция для установки новых значений в сессионную корзину*/
		protected function set_new_values_to_session_cart($good_id, $delete, $good_count)
		{
			if ($delete || (int)$good_count <= 0) {
				unset($_SESSION['cart'][(int)$good_id]);
			} else {
				$_SESSION['cart'][(int)$good_id]['good_count'] = (int)$good_count;
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
			if($security_pass->get_auth_status()) {
				require_once MODEL_DIR . DIRECTORY_SEPARATOR . 'orders_data.class.php';
				$order_data = new Orders_data();
				$this->output_data['orders_list_array'] = $order_data->get_user_orders_list();
				if ($this->output_data['orders_list_array'][0] === 'empty_result') {
					$this->output_data['message'] = 'Заказов не обнаружено';
					$this->output_data['suggested_link'] = '<a href="./">На главную</a>';
					$this->template = 'shop_message.php';
				} else {
					$this->template = 'orders_list_tpl.php';

				}
			} else {
				$this->output_data['message'] = 'Чтобы проверить свои заказы, вам нужно войти или зарегистрироваться';
				$this->output_data['suggested_link'] = '<a href="./?ctrl=user&action=login">Войти</a>';
				$this->template = 'shop_message.php';
			}
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

	public function get_output_data()
	{
		return $this->output_data;
	}
}

