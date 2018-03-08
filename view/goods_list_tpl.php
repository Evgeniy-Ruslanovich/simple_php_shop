<?php

/**
 * @var $photoArray
 */


foreach ($categories_array as $value) {
	echo '<a href="./?category=' . $value['id'] . '">' .  $value['category_name'] . '</a>&nbsp';
}

foreach  ($goods_data_array as $value) {
	$href = $value['id'];
		echo
		'<a href="?&good=' . $href .'">
			<div class="photo-div" >
				<img style="max-height:300px; max-width:300px;" src="img/thumbs/' . $value['good_main_photo'] . '">
				<p>' . $value['product_name'] . '</p>
				<p><i>' . $value['category_name'] . '</i></p>
				<p><b>' . $value['price'] . '</b></p>
			</div>
		</a>';
}

echo '<div style="clear: both"></div>';