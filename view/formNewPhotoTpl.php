<?php

/**
 * 
 */
echo $database_upload_success;
if($debug_mode){
	echo '$_POST '; var_dump($_POST);
	echo "<br>";
	echo '$_FILES';
	var_dump($_FILES);
	echo "<br>";
	echo 'database upload success: '  . $database_upload_success;
	echo "<br>";
	echo $debug_info . '<br>';
}

echo "<a href=\"./\">на главную страницу</a><br>форма для добавления новой фотки";

?>
<form method="post" enctype="multipart/form-data">
	<p>Название
	<input type="text" name="title"></p>
	<p>Описание<br>
	<textarea rows="10" cols="45" name="description"></textarea></p>
	<p>Файл
	<input type="file" name="picture"></p>
	<button>Отправить</button>
</form>
<?php
