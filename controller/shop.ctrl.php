<?php


require_once(MODEL_DIR . '/goods_data.class.php');
$data = [];
$goods_data = new Goods_data();

$categories_array = $goods_data->get_categories_list();
$data['categories_array'] = $categories_array;

if (isset($_GET['good'])) {
	$single_good_data = $goods_data->get_public_single_good( (int)$_GET['good'] );
	if ($single_good_data !== 'empty_result') {
		$data['single_good_data'] = $goods_data->get_public_single_good( (int)$_GET['good'] );
		$template = 'single_good_tpl.php';
		if (isset($_GET['message'])) {
		$data['single_good_data']['message'] = $_GET['message'];
		}
	} else {
		$template = 'shop_message.php';
		$data['message'] = 'Такой товар не найден.';
		$data['suggested_link'] = '<a href="./">На главную</a>';
	}
	
} else {
	$goods_params = [];
	if (isset($_GET['category'])) {
	$goods_params['categories'] = explode(',', $_GET['category']);
	}
	//то есть теоретически мы можем сделать выборку из нескольких категорий
	$data['goods_data_array'] = $goods_data->get_public_goods_list($goods_params);
	$template = 'goods_list_tpl.php';
}

require_once(TEMPLATE_DIR . DIRECTORY_SEPARATOR . 'template.class.php');
$page = new Template();
$page->layout = 'layout.php';
$page->template = $template;
$page->render($data);