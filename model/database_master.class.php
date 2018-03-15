<?php
/*
*
*/

require_once('../config.php');
require_once('../db_connect.php');

class Database_master {

	//protected $link;
	//$link = mysqli_connect(SQL_HOST, DB_USER, DB_PASSW);
	/* параметры для чтени явыглядят примерно так:
	$params = array(
	//SELECT
	'columns' => array(), //or *
	//FROM
	'table' => 'table',
	//WHERE
	'where' => array(), Хрен там, а не аррей... хз как это сделать. Это зависит уже от таблицы. Поэтому вхере мне будут давать вышестоящие классы
	//group by
	'group_by' =>'',
	'join' =>array(),
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

	protected /*public*/ function read_any_table($params)
	{
		global $link;
		$query = $this->build_read_query($params);
		//$query = mysqli_real_escape_string($link, $query);
		//echo 'запрос ' . $query . '<br>';

		$result = mysqli_query($link, $query);
		if(!$result) {echo "FAIL!!!!!<br>LOOSER!!!<br>DATABASE QUERY ERROR<br>"; return;}
		//var_dump($result);
		if ($result->num_rows > 0) {
			
			//var_dump($result);
		
			$result_array = array();
			while ($row = mysqli_fetch_assoc($result)) {
			array_push($result_array, $row);
			} 
			return $result_array;
		} else {
			$result_array = array('empty_result');
			return $result_array;
		}
	}

	protected /*public*/ function read_any_table_ready_query($query)
	{
		global $link;
		/*$query = $this->build_read_query($params);
		//$query = mysqli_real_escape_string($link, $query);
		//echo 'запрос ' . $query . '<br>';*/

		$result = mysqli_query($link, $query);
		//var_dump(mysqli_error_list($link));
		if(!$result) {echo "<br>FAIL!!!!!<br>LOOSER!!!<br>DATABASE QUERY ERROR<br>"; return;}
		//var_dump($result);
		if ($result->num_rows > 0) {			
			//var_dump($result);		
			$result_array = array();
			while ($row = mysqli_fetch_assoc($result)) {
			array_push($result_array, $row);
			} 
			return $result_array;
		} else {
			$result_array = array('empty_result');
			return $result_array;
		}
	}

	protected /*public*/ function build_read_query($params)
	{
		global $link;
		$query = 'SELECT '; //пробелы ставятся ПОСЛЕ каждого элемента, а не до, не путаться
		if (count($params['columns'])) {
			$escaped_array = array();
			foreach ($params['columns'] as $value) {
				$escaped_array[] = mysqli_real_escape_string($link, $value);//хз надо оно или нет
			}
			$query .= $this->columns_array_to_string_2($escaped_array);
			//$query .= $this->columns_array_to_string_2($params['columns']);
		} else {
			$query .= '* ';
		}
		$params['table'] = mysqli_real_escape_string($link, $params['table']);//тоже хз, надо оно или нет, мы же ЖОСКА даем таблицу, это не юзерь нам дает
		$query .= 'FROM ' . DB_NAME . '.`' . $params['table'] . '` '; //DB_NAME не надо дополнительно обносить обратными кавычками, она изначально обкавычена уже в конфиге..
		if (isset($params['join'])) {
			$query .= $params['join'] . ' '; //
		}
		if (isset($params['where'])) {
			$query .= $params['where'] . ' '; //присоединяем where, при этом его нужно зачистить на предыдущем этапе
		}
		if (isset($params['order_by'])) {
			$params['order_by'][0] = mysqli_real_escape_string($link, $params['order_by'][0]);
			$query .= 'ORDER BY `' . $params['order_by'] . '` '; //что за фигня, там же должен быть массив
		}
		return $query;
	}

	protected /*public*/ function columns_array_to_string($array){ //array_to_comma_separated_list_whith_backquotes
		global $link;
		foreach ($array as $value) {
				$value = mysqli_real_escape_string($link, $value);
			}
		$string = implode('`, `', $array);
		$string = '`' . $string . '` ';
		return $string;//тут надо потом дописать
	}

	protected /*public*/ function one_entry_values_array_to_string($one_entry_values_array, $use_subquery = false){ //array_to_comma_separated_list_whith_quotes
		global $link;
		$escaped_array = array();
		foreach ($one_entry_values_array as $value) {
			$escaped_array[] = mysqli_real_escape_string($link, $value);
		}
		if ($use_subquery) {
			$string = implode(', ' , $escaped_array) . ' ';
		} else {
			$string = implode('\', \'', $escaped_array);
			$string = '\'' . $string . '\' ';
		}
				//к сожалению, эта функция сыграла со мной злую шутку, из-за того, что все берется в кавычки, я теперь не могу вставить вместо айдишника подзапрос. (собственно, для того кавычки эти и нужны епта, чтоб не было инъекций. Но блин когда я сам хочу сделать "инъекцию", то хрен тебе) Придется убирать кавычки, и проверять безопасность уровнем выше.
				//надо подумать еще, что с этим делать.
		return $string;//тут надо потом дописать
	}

	protected /*public*/ function few_entries_values_array_to_string($few_entries_values_array, $use_subquery = false) //из массива разделенных запятой значений, в строку, где эти значения в скобках через запятую - VALUES ('1','2','3'),('1','2','3'),('1','2','3'). $few_entries_values_array - это массив массивов
	{
		$new_strings_array = array();
		foreach ($few_entries_values_array as $one_entry_values_array) {
			$new_strings_array[] = $this->one_entry_values_array_to_string($one_entry_values_array, $use_subquery);
		}
		$string = implode('), (', $new_strings_array);
		$string = '(' . $string . ')';
		return $string;

	}

	/*Короче, предыдущей функции columns_array_to_string() хватало ровно до того момента, как  не решил сделать джоин. тогда пришлось резко усложнять всю систему. Заново писать уже слишком долго, так что сделали вот такую заплатку. Вообще, мне кажется, идея с единым классом для базы слишком геморная, надо бует переходить потом на PDO или ORM. Но, я хотя бы попытаюсь))
	*/
	protected /*public*/ function columns_array_to_string_2($array){ //array_to_comma_separated_list_whith_backquotes
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
						# code...ээ а чо делать то? Вот блин засада, когда через несклько дней приходишь, и не знаешь уже, что хотел. Ну, принципе, все работает, несмотря на то, что тут пустое место. Видимо, тут ничего делать не надо, и так хорошо.
					}
				}
				$table_name = '';
				$table_name_array = explode('.', $column_string[0]);//может быть составное имя таблицы, разделенное точками
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
				//echo $value . '<br>';
			}
		//var_dump($array);
		$string = implode(', ', $array) . ' ';
		//$string = '`' . $string . '` ';
		return $string;//тут надо потом дописать
	}
	protected function update_any_entry($params)
	{
		//echo 'парамс для апдейта: ';

		/*params table, array column=>value, Where*/
		//Функция вообще построена неправильно, потому что отдельно вере, отдельно кейвалюе
		global $link;
		//echo "Вардамп апдейт парамс: "; var_dump($params['keyvalue']) ;
		//echo '<br>';
		$query = "UPDATE `" . $params['table'] . "` SET ";
		$keyvalue = '';
		foreach ($params['keyvalue'] as $key => $value) {
			$keyvalue .= ", `" . $key . "`='" . $value . "' ";
		}
		$keyvalue = substr($keyvalue, 2);
		$query .= $keyvalue . $params['where'];
		//echo "Апдейт запрос: " . $query . '<br>';
		$result = mysqli_query($link,$query);
		$error_list = mysqli_error_list($link);
		//echo '<b>Список ошибок при апдейте:</b> '; var_dump($error_list);
		//echo '<br>';
	}

	protected function delete_entry($params)
	{
		global $link;
		if(isset($params['ready_query'])) {
			$query = $params['ready_query'];
		} else {
			$query = "DELETE FROM `" . $params['table'] . "` " . $params['where'];	
		}
		//echo 'Запрос на удаление: ' . $query;
		//echo '<br>';
		$result = mysqli_query($link, $query);
		/*
		echo 'Список ошибок при удалении: '; var_dump($error_list);
		echo '<br>';*/
	}

	protected function insert_new_entry($params, $use_subquery = false)
	{
		//$query = "INSERT INTO `{$TABLE}` (column1, column2, column3) VALUES ('1','2','3'),('1','2','3')";
		global $link;
		$query = $this->build_insert_query($params, $use_subquery);
		$result = mysqli_query($link, $query);		
		$error_list = mysqli_error_list ($link);
		return (bool)$result;
		
	}

	protected function build_insert_query($params, $use_subquery = false)
	{
		//global $link;
		/*params = array(
		'table' => 'table',
		'keyvalue' => array(['column1' => 'value', 'column2' => 'value', 'column3' => 'value',],
							['column1' => 'value', 'column2' => 'value', 'column3' => 'value',],
							['column1' => 'value', 'column2' => 'value', 'column3' => 'value',]
							)
		)*/
		//На выходе - INSERT INTO `table` (`column1`, `column2`, `column3`) VALUES ('1','2','3'),('1','2','3')
		//Необходимо, чтобы порядок ключей во всех массивах с данными был одинаковый!!!
		$query = 'INSERT INTO ' . DB_NAME;
		$query = $query . '.`' . $params['table'] . '` ';
		$columns = array();
		$values = array();
		foreach ($params['keyvalue'] as $value) {
			$values[] = $value;
		}
		foreach ($params['keyvalue'][0] as $key => $value) { //берем для примера один первый массив, и достаем из него ключи. Во всех остальных массивах ключи должны быть точно такие же. Как тут сделать защиту от дурака??
			$columns[] = $key;
		}
		$columns_string = $this->columns_array_to_string($columns);
		$values_string = $this->few_entries_values_array_to_string($values, $use_subquery);
		

		$query .= '(' . $columns_string . ')' . ' VALUES ' . $values_string;
		return $query;
	}

	protected function insert_few_entries($params)
	{

	}
}