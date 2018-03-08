<?php
/*
*
*/
require_once('../config.php');
require_once './database_master.class.php';

$data = new Database_master();
/*$data->hello_test();
$array = array('id','name','surname');
$string = $data->columns_array_to_string($array);
echo $string . '<br>';
$array = array('surname');
$string = $data->columns_array_to_string($array);
echo $string . '<br>';

$params = array(
	//'columns' => array('id','name','surname'),
	'columns' => array(),
	'table' => 'table',
	);
$string = $data->build_read_query($params);
echo $string . '<br>';*/

$params = array(
	'columns' => array('id','user_name','role'),
	//'columns' => array(),
	'table' => 'users',
	);

$string = $data->read_any_table($params);
var_dump($string);
echo "<br>";
$array=array('a.b AS c','d','e');
$string = $data->columns_array_to_string($array);
echo $string . '<br>';
$string = $data->columns_array_to_string_2($array);
echo $string . '<br>';
