<?php

/**
 * Класс для отрисовки страницы
 */
error_reporting(E_ALL);

class Template {
	public $layout;  //основной шаблон
	public $title;
	public $styles = [];  //ПИШУТСЯ НАЗВАНИЯ ФАЙЛОВ СТИЛЕЙ, БЕЗ РАСШИРЕНИЯ ".CSS"
	public $scripts = [];
	public $template;  //маленький шаблон

	public function render($data) {  //рендерим содержание маленького темплейта
		foreach ($data as $key => $value) {  //в этом цикле ассоциативный массив превращается в пачку переменных
			$$key = $value;
		}

		unset($data);

		ob_start();
		require_once TEMPLATE_DIR . DIRECTORY_SEPARATOR . $this->template;
		$content = ob_get_contents();
		ob_end_clean();

		require_once TEMPLATE_DIR . DIRECTORY_SEPARATOR . $this->layout;  //вот теперь уже у нас случится вывод на экран

	}
}

