<?php
/*
*
*/

require_once('../config.php');
require_once('../db_connect.php');

class Database_master {

	//protected $link;
	//$link = mysqli_connect(SQL_HOST, DB_USER, DB_PASSW);
	/*
	$params = array(
	//SELECT
	'columns' => array(), //or *
	//FROM
	'table' => 'table',
	//WHERE
	'where' => array(), Хрен там, а не аррей... хз как это сделать. Это зависит уже от таблицы. Поэтому вхере мне будут давать вышестоящие классы
	//group by
	'group_by' =>'',
	//HAVING
	'group_by' =>array(),
	//ORDER BY
	'order_by' =>'Array('field', desc/asc(bool))'
	)
	*/
	public function hello_test()
	{
		echo "HELLO<br>";
	}

	/*protected*/ public function read_any_table($params)
	{
		global $link;
		$query = $this->build_read_query($params);
		//$query = mysqli_real_escape_string($link, $query);
		//echo 'запрос ' . $query . '<br>';

		$result = mysqli_query($link, $query);
		if(!$result) {echo "FAIL!!!!!<br>LOOSER!!!<br>DATABASE QUERY ERROR<br>"; return;}
		$result_array = array();
		while ($row = mysqli_fetch_assoc($result)) {
		array_push($result_array, $row);
		} 
		return $result_array;
	}

	protected /*public*/ function build_read_query($params)
	{
		global $link;
		$query = 'SELECT '; //пробелы ставятся ПОСЛЕ каждого элемента, а не до, не путаться
		if (count($params['columns'])) {
			foreach ($params['columns'] as $value) {
				$value = mysqli_real_escape_string($link, $value);
			}
			$query .= $this->columns_array_to_string($params['columns']);
		} else {
			$query .= '* ';
		}
		$params['table'] = mysqli_real_escape_string($link, $params['table']);
		$query .= 'FROM ' . DB_NAME . '.`' . $params['table'] . '` '; //DB_NAME не надо дополнительно обносить обратными кавычками, она изначально обкавычена уже в конфиге..
		//$query .= generate_where_statement($params['where']);
		//$query = mysql_real_escape_string($query);
		if (isset($params['where'])) {
			$query .= $params['where'] . ' '; //присоединяем where, при этом его нужно зачистить еще на предыдущем этапе
		}
		if (isset($params['order_by'])) {
			$params['order_by'][0] = mysqli_real_escape_string($link, $params['order_by'][0]);
			$query .= 'ORDER BY `' . $params['order_by'] . '` ';
		}
		return $query;
	}

	/*protected*/ public function columns_array_to_string($array){ //array_to_comma_separated_list_whith_backquotes
		global $link;
		foreach ($array as $value) {
				$value = mysqli_real_escape_string($link, $value);//проверим на всякий пожарный
			}
		$string = implode('`, `', $array);
		$string = '`' . $string . '` ';
		return $string;//тут надо потом дописать/ Три дня спустя - ЧТО БЛИН ТУТ НАДО ДОПИСАТЬ? уже забыл, что хотел
	}

	protected function update_any_entry($params)
	{
		# code...
	}

	protected function insert_new_entry($params)
	{
		# code...
	}

	public function columns_array_to_string_2($array){ //array_to_comma_separated_list_whith_backquotes
		global $link;
		$i = 0;
		foreach ($array as $value) {
				$column_string = '';
				$as_statement = '';
				$value = mysqli_real_escape_string($link, $value);
				$column_string = explode(' ', $value);
				if (count($column_string) === 3 /*and $column_string[1] === 'AS'*/) {
					$as_statement = " AS '$column_string[2]'";
				} else {
					if (count($column_string) === 1) {
						# code...
					}
				}
				$table_name = '';
				$table_name_array = explode('.', $column_string[0]);
				if (count($table_name_array) > 1) {
					$u = 0;
					foreach ($table_name_array as $value) {
						$table_name_array[$u] = '`' . $value . '`';
						$u++;
					}
					$table_name = implode('.', $table_name_array);
				} else {$table_name = '`' . $table_name_array[0] . '`';}

				$array[$i] = $table_name . $as_statement;
				$i++;
				echo $value . '<br>';
			}
		var_dump($array);
		$string = implode(', ', $array);
		//$string = '`' . $string . '` ';
		return $string;//тут надо потом дописать
	}
}