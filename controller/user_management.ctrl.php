<?php

//session_start();
/*require_once(MODEL_DIR . '/security_pass.class.php');
$security_pass = new Security_pass();
$security_pass->chek_auth();*/
$action = ($security_pass->auth_status) ? 'edit_user_data' : 'login' ;
if(isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'login':
			$action = ($security_pass->auth_status) ? 'edit_user_data' : 'login' ; //если он уже залогинен, то нафига логиниться
			break;
		case 'logout':
			$action = ($security_pass->auth_status) ? 'logout' : 'login' ; //если он уже залогинен, то может разлогиниться. А если не залогинен, то пусть логинится
			break;
		case 'edit_user_data':
			$action = ($security_pass->auth_status) ? 'edit_user_data' : 'login' ;
			break;
		case 'register':
			$action = ($security_pass->auth_status) ? 'edit_user_data' : 'register' ;
			break;
		default:
			$action = ($security_pass->auth_status) ? 'edit_user_data' : 'login' ;
			break;
	}
}
/*
switch ($action) {
	case 'logout':
		$security_pass->logout();
		header('Location: ./?ctrl=shop');
		die();
		break;
	case 'login':
		if ($security_pass->login()) {
			header('Location: ./?ctrl=shop');
			die();
		} else {
		echo 'Не удалось войти. Неправильный логин или пароль<br>';
		}
		break;
		
	default:
		# code...
		break;
}*/

if ($action === 'logout') {
	$security_pass->logout();
	header('Location: ./?ctrl=shop');
	die();
}
if ($action === 'login') {
	if ($security_pass->login()) {
		header('Location: ./?ctrl=shop');
		die();
	} else {
		echo 'Не удалось войти. Неправильный логин или пароль<br>';
	}
	?>
<form method="post">
	<label>Емейл<input type="text" name="user_email"></label>
	<label>Пароль<input type="text" name="passw"></label>
	<button>Отправить</button>
</form>
<?php
}

if ($action === 'edit_user_data') {
	echo "edit_user_data<br>";
}

echo "hello, user controller<br>";
var_dump($_SESSION);