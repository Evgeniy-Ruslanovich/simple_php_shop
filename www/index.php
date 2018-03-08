<?php
error_reporting(E_ALL);
/**
 * ФАЙЛ КОНФИГУРАЦИИ, ЗАПРАШИВАЕТСЯ ИНДЕКСОМ В САМОМ НАЧАЛЕ СКРИПТА
 */
require_once '../config.php';
session_start();
require_once(MODEL_DIR . '/security_pass.class.php');
$security_pass = new Security_pass();
$security_pass->chek_auth();
$controller = "shop"; //контроллер по умолчанию

if (isset($_GET['ctrl'])) {
	switch ($_GET['ctrl']) {
		case 'shop':
			$controller = "shop"; //actions: view_default_goods_list, view_single_good
			break;
		case 'admin':
			$controller = "admin";//subctrl: orders(list, single, add_new, edit, delete, change_status), users(list+sort, single, add_new, edit, delete), roles(list, single, add_new, edit, delete), goods(list+sort, single,add_new, edit, delete), categories(list, add_new, edit, delete)
			break;
		case 'user':
			$controller = "user_management"; //actions: login, logout, register, edit_user_data
			break;
		case 'ordering':
			$controller = "ordering"; //actions: add_to_cart, view_cart, save_as_dreft, edit_draft, make_order, view_order_list
			break;
		default:
			$controller = "shop"; //юзер может в адресную строку вставить какую-то чушь, тогда все равно попадет в магазин
			break;
	}
}

require_once(CONTROLLER_DIR . DIRECTORY_SEPARATOR . $controller . '.ctrl.php');
