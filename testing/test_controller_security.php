<?php
/*
*
*/
error_reporting(E_ALL);
require_once('../config.php');
require_once('../db_connect.php');
require_once(TESTING_DIR . '/security_pass.class.php');


session_start();
$security_pass = new Security_pass();
if (isset($_POST['user_email'])){
	$security_pass->login();
	header('Location ./');
}
if (isset($_POST['logout'])){
	$security_pass->logout();
	header('Location ./');
}
/*
$_SESSION['user_id'] = 5;
$_SESSION['passw'] = '12345';
*/
echo 'Сессия: ';
var_dump($_SESSION);
echo '<br>';

$security_pass->chek_auth();
echo '<br>АВторизован?: ' . $security_pass->auth_status . '<br>';
echo '<br>Роль: ' . $security_pass->get_role() . '<br>';

echo 'Новый пароль: ' . md5('1234' . SALT);

if ($security_pass->auth_status) {
	echo '<br>Привет, ' . $security_pass->user_name . '<br>';
}
if ($security_pass->auth_status) {
	?>
<form method="post">
	<input type="hidden" name="logout" value="1">
	<button>Разлогиниться</button>
</form>
<?php
} else {
	?>
<form method="post">
	<label>Емейл<input type="text" name="user_email"></label>
	<label>Пароль<input type="text" name="passw"></label>
	<button>Отправить</button>
</form>
<?php
}

/*Окей, короче, мы можем залогиниться и разлогиниться, а также проверить, что мы залогинены. Остается открытым вопрос, как мы будем передавать факт того, что мы залогинены, в дальнейее исполнение скрипта? И как будем передавать список разрешений? И роль? Может через контроллер?*/



//unset($_SESSION);
//session_destroy();

