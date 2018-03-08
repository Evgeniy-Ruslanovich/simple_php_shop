<?php

/**
 * 
 */
if($update_success && $update_try) { //если мы пробовали обновиться и получилось
	echo 'Обновление успешно<br><a href="./">на главную страницу</a><br>
	<a href="./?edit=' . $_GET['edit'] .'">Вернуться к редактированию</a>';
} else {
	if($update_try && !$update_success) {
		echo 'Попытка обновления не удалась<br>';
	}
if ($debug_mode) {
	echo $debug_info . '<br>';
}

?>
<a href="./">на главную страницу</a>
<div class=big-photo>
	<a href="img/<?= $photoInfo['photo'] ?>" target="_blank">
		<img src="img/<?= $photoInfo['photo'] ?>" style="max-height: 600px; max-width: 100%">
	</a>
</div>
<form method="post" enctype="multipart/form-data">
	<p>Название
	<input type="text" name="title" value="<?= $photoInfo['title'] ?>"></p>
	<p>Описание<br>
	<textarea rows="10" cols="45" name="description"><?= $photoInfo['description'] ?></textarea></p>
	<p>Файл
	<input type="file" name="picture"></p>
	<button>Отправить</button>
</form>
<p><a href="./?delete=<?= $photoInfo['id'] ?>">Удалить</a></p>
<?php
}