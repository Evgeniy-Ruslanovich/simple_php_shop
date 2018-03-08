<?php

/**
 * @var $photoArray
 */

/*
foreach ($categories_array as $value) {
	echo '<a href="./?category=' . $value['id'] . '">' .  $value['category_name'] . '</a>&nbsp';
}*/
?>
<form action="?ctrl=admin&subctrl=goods" method="get">
	<input type="hidden" name="ctrl" value="admin">
	<input type="hidden" name="subctrl" value="goods">
	<label>Не показывать скрытые <input type="radio" name="show_hidden" value="not_hidden_only" <?php
		if(!isset($_GET['show_hidden']) || $_GET['show_hidden'] === 'not_hidden_only') {
			echo 'checked="checked"';}
	?>></label> |
	
	<label>Только скрытые <input type="radio" name="show_hidden" value="hidden_only" <?php
		if(isset($_GET['show_hidden']) && $_GET['show_hidden'] === 'hidden_only') {
			echo 'checked="checked"';}
	?>></label> |
	<label>Все <input type="radio" name="show_hidden" value="all" <?php
		if(isset($_GET['show_hidden']) && $_GET['show_hidden'] === 'all') {
			echo 'checked="checked"';}
	?>></label><br>
	<button>Фильтровать</button>
</form>
<?php


foreach  ($goods_data_array as $value) {
	$href = $value['id'];
		echo
		'<a href="?ctrl=admin&subctrl=goods&action=good&good=' . $href .'">
			<div class="photo-div" >
				<img style="max-height:300px; max-width:300px;" src="img/thumbs/' . $value['good_main_photo'] . '">
				<p>' . $value['product_name'] . '</p>
				<p><i>' . $value['category'] . '</i></p>
				<p><b>' . $value['price'] . '</b></p>
			</div>
		</a>';
}

echo '<div style="clear: both"></div>';