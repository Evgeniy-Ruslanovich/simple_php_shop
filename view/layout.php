<?php

/**
 * @var $title
 * @var $styles
 * @var $content
 * Для отрисовки страницы нужны эти переменные
 */

$this->title = 'Магазин';
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
				<a href="./"><h1>Магазин</h1></a>
			
			<p style="color: white;"><?php
			if ($security_pass->auth_status) {
				echo 'Привет, ' . $security_pass->user_name . ' | ';
				if ($security_pass->get_role() != 1) {
					echo '<a style="color: #ddd;" href="./?ctrl=admin">В админку</a> | ';
				}
				echo '<a style="color: #ddd;" href="./?ctrl=ordering&action=cart">Корзина</a> | ';
				echo '<a style="color: #ddd;" href="./?ctrl=user&action=logout">Выйти</a>';
			} else {
				echo '<a style="color: #ddd;" href="./?ctrl=user&action=login">Войти</a>';
			}
			?></p>
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
