<?php
/*Задача этого файлика - выбрать нужный экшн для админа*/
$action = 'default';
if (!$security_pass->auth_status) {
	$subctrl = 'access_denied';
	$action = 'not_logged_in';
} else {
	if ($security_pass->get_role() === 1) {
		$subctrl = 'access_denied';
		$reason = 'have_not_permission';
	} else {
		$subctrl = 'dashboard';
		if (isset($_GET['subctrl'])) {
			if (in_array($_GET['subctrl'], array('dashboard', 'orders', 'users', 'roles', 'goods', 'categories'), true)) {
				$subctrl = $_GET['subctrl'];
			} //Короче, если есть в разрешенном списке действий, то валяй, а если нет, останется действие по умолчанию - dashboard
		}
		if (isset($_GET['action'])) {
			$action = $_GET['action'];
		}
	}
}
require_once (CONTROLLER_DIR . DIRECTORY_SEPARATOR . 'admin.ctrl.class.php');
$admin_controller = new Admin_controller($subctrl, $action);

$data = $admin_controller->get_output_data();
require_once(TEMPLATE_DIR . DIRECTORY_SEPARATOR . 'template.class.php');
$page = new Template();
//$page->layout = 'layout_admin.php';
$page->layout = $admin_controller->layout;
$page->template = $admin_controller->template;
$page->render($data);