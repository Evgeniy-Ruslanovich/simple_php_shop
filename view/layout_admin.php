<?php

/**
 * @var $title
 * @var $styles
 * @var $content
 * Для отрисовки страницы нужны эти переменные
 */

$this->title = 'Админка';
$this->styles = ['style1', 'style2'];
global $security_pass;
?>
<!doctype html>
<html>
	<head>
		<title><?= $this->title ?></title>
		<meta charset="UTF8">
		<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
		<?php
		$this->styles;
		foreach ($this->styles as $style) { //циклом добавляем все стили 
		 ?>
			<link rel="stylesheet" type="text/css" href="css/<?= $style ?>.css">
		<?php } ?>
		
		
	</head>
	<body>
	<div class="main">
		<div class="header">
			<div class="header-center">
				<a href="./?ctrl=admin"><h1>Админка</h1></a>
				<p style="color: white;">
				<a style="color: #ddd;" href="./?ctrl=admin&subctrl=orders">Заказы</a> |
				<a style="color: #ddd;" href="./?ctrl=admin&subctrl=goods">Товары</a> |
				<a style="color: #ddd;" href="./?ctrl=admin&subctrl=categories">Категории</a> |
				<a style="color: #ddd;" href="./?ctrl=admin&subctrl=users">Пользователи</a> |
				<a style="color: #ddd;" href="./?ctrl=admin&subctrl=roles">Роли</a>
				</p>
				<p style="color: white;">
				Привет, <?= $security_pass->user_name ?> | <a style="color: #ddd;" href="./?ctrl=user&action=logout">Выйти</a> |
				<a style="color: #ddd;" href="./">В магазин</a>
			</div>
		</div>
		<div class="content">
			<?= $content ?>
		</div>

	</div>
	<div class="prefooter">
	</div>
	<div class="footer">
			<div class="footer-center">
				Это футер
			</div>
	</div>
	</body>
</html>
