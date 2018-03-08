<?php

//echo "hello, ordering controller<br>";
//require_once (MODEL_DIR . DIRECTORY_SEPARATOR . 'orders_data.class.php');
require_once (CONTROLLER_DIR . DIRECTORY_SEPARATOR . 'ordering.ctrl.class.php');
//$action = 'view_cart'; //действие по умолчанию
//$data = [];
/*if (isset($_GET['action']) && ($_GET['action'] === 'add_to_cart')) {
	header('Location: ./?good=' . $_POST['good_id'] . '&message=1');
	echo "Добавление в корзину " . $_POST['good_id'] . "<br>";
}*/
//var_dump($_SESSION);

$action = ( isset($_GET['action']) ) ? $_GET['action'] : 'list' ;

$order_controller = new Order_controller($action);
$data = $order_controller->get_output_data();
require_once(TEMPLATE_DIR . DIRECTORY_SEPARATOR . 'template.class.php');
$page = new Template();
$page->layout = 'layout.php';
$page->template = $order_controller->template;
$page->render($data);
