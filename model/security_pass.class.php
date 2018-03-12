<?php
/*
*Парль всех юзеров - 1234
Пароль суперАдмина - 12345
Этот класс запускается в самом начале и дает нам такие вещи:
Залогинен ли узер? Бууль
Имя юзера, роль юзера.
*/
require_once 'database_master.class.php';

//

class Security_pass extends Database_master
{
	protected $user_id;
	protected $passw;
	public  $user_name;
	protected $user_role;
	protected $user_permissions;
	protected $auth_status = false;
	public $error = false;

	public function chek_auth()
	{
		global $link;
		if ( isset($_SESSION['user_id']) && isset($_SESSION['passw']) ) {
			//юзер есть в сессии. Правильны ли его данные?
			$this->user_id = $_SESSION['user_id'];
			$this->passw = $_SESSION['passw'];
			$this->auth_status = $this->chek_user_in_db($this->user_id, $this->passw); //hashed = true по умолчанию
			if ($this->auth_status) {
				return true;
				//Если в базе пароль соответствует, то возвращаемт тру, юзер залогинен, может дальше действовать в соответствии со своей ролью
			}
			else {
				$this->error = 'Ошибка аутентификации: пароль не соответствует.';
				$this->logout();//еще не проверял, как будет это работать, добавил позже. Но по сути. логично. Если все не совпадает, то надо зачистить куки и сессию, зачем нам это хранить.
				return false;
			}	
		}
		else {
			if ( isset($_COOKIE['user_id']) && isset($_COOKIE['passw']) ) {
				//нет в сессии, есть в куках
				$this->user_id = $_COOKIE['user_id'];
				$this->passw = $_COOKIE['passw'];
				$this->auth_status = $this->chek_user_in_db($_COOKIE['user_id'],$_COOKIE['passw']);
				if ($this->auth_status) {
					$_SESSION['user_id'] = (int)$_COOKIE['user_id'];
					$_SESSION['passw'] = $_COOKIE['passw'];
					return true;
					//Если юзер "запомнен" в куках, и пароль соответствует, переносим в сессию, юзер залогинен, может дальше действовать
				}
				else {
					$this->error = 'Ошибка аутентификации: данные КУКИ не верны';
					$this->logout();//еще не проверял, как будет это работать, добавил позже. Но по сути. логично. Если все не совпадает, то надо зачистить куки и сессию, зачем нам это хранить.
					return false;
				}	
			}
			else {
				$this->error = 'Пользователь не аутентифицирован';
				return false;
			}
		}
	}

	protected function chek_user_in_db($id, $passw, $md5hashed = true)
	{
		/*if (!$md5hashed) {
			$password = md5($password . SALT);
		}*/
		$query_params = array(
			'columns' => array(/*'id',*/ 'passw', 'user_name', 'role'), //да в принципе айдишник нам не нужен из базы, мы же сами его ей даем в запросе
			'table' => 'users',
			'where' => 'WHERE `id`=\'' . (int)$id . '\' ', //приводим $id к целому, чтобы избежать sql-инекции через кукис
			);

		$result=$this->read_any_table($query_params)[0];
		//echo 'результат из базы:';var_dump($result);
		if ($result['passw'] === $this->passw) {
			$this->user_name = $result['user_name'];
			$this->user_role = (int)$result['role'];
			//var_dump($result);
			return true;
		}
		else {
			return false;
		}		
	}

	public function login() //функция chek_user_in_db тут не подходит, потому что хочу проверить не по айдишнику, а по емейлу. Потом можнобудет объединить, но с какими-то доп. параметрами.. А может и необъединять.
	{
		if ( isset($_POST['user_email']) && isset($_POST['passw']) ) {
			/*echo "Массив ПОСТ при логине: ";
			var_dump($_POST);
			echo "<br>";*/
			global $link;
			//echo "<br>" . $link . "<br>";
			$user_email = mysqli_real_escape_string($link, $_POST['user_email']);
			//$user_email = $_POST['user_email'];

			$passw = md5($_POST['passw'] . SALT); //после хеширования инъекции точно уже не будет, можно пихать в запрос
			//$passw = $_POST['passw']; //потом добавим. У меня все пароли нехешированные в базе пока.
			$query_params = array(
				'columns' => array('id', 'passw', 'user_name', 'role'), //
				'table' => 'users',
				'where' => 'WHERE `user_email`=\'' . $user_email . '\' ',
			);
			$result = $this->read_any_table($query_params)[0];
			/*echo "Данные из базы при логине: ";
			var_dump($result);
			echo "<br>";*/
			if ($passw === $result['passw']) {
				$_SESSION['user_id'] = $result['id'];
				$_SESSION['passw'] = $result['passw'];
				return true;
			}
		}
		else {
			$this->error .= 'Нет данных для авторизации.';
			return false;
		}
	}

	public function logout()
	{
		if (count($_COOKIE)) { //Если куки вообще есть, а то может и нет их.
			setcookie("user_id", "", time() - 100500);
			setcookie("passw", "", time() - 100500);
		}
		unset($_SESSION['user_id']);
		unset($_SESSION['passw']);
		session_destroy(); 

		//header('Location: ./login.php');
		//die();
	}
/*
	public function register_new_user()
	{
		# code...
	}*/

	public function get_permissions($value='')
	{
		$query_params = array(
			'table' => 'roles',
			'where' => 'WHERE `id`=\'' . $this->user_role .'\''
			);
		$result = $this->read_any_table($query_params)[0];
		return $result;
	}

	public function chek_permission($permission_needed)
	{
		$permission_array = $this->get_permissions();

		return (bool)$permission_array[$permission_needed];//теоретически,эта функция должна работать
	}

	public function get_role()
	{
		return $this->user_role;
	}

	public function get_user_id()
	{
		return $this->user_id;
	}

	public function get_auth_status()
	{
		return $this->auth_status;
	}
}