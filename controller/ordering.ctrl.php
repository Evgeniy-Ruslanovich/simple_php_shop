<?php

//echo "hello, ordering controller<br>";
require_once (MODEL_DIR . '/orders_data.class.php');
$action = 'view_cart';
$data = [];
if (isset($_GET['action']) && ($_GET['action'] === 'add_to_cart')) {
	header('Location: ./?good=' . $_POST['good_id'] . '&message=1');
	echo "Добавление в корзину " . $_POST['good_id'] . "<br>";
}
var_dump($_SESSION);

$order = new Order_ctrl();

require_once(TEMPLATE_DIR . DIRECTORY_SEPARATOR . 'template.class.php');
$page = new Template();
$page->layout = 'layout.php';
$page->template = $template;
$page->render($data);
