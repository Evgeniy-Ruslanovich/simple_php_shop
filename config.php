<?php

/**
 * ФАЙЛ КОНФИГУРАЦИИ, ЗАПРАШИВАЕТСЯ ИНДЕКСОМ В САМОМ НАЧАЛЕ СКРИПТА
 */

define('SITE_ROOT', __DIR__);  //получаем директорию корня сайта - это та папка, в которой лежит конфиг
define('WWW_ROOT', SITE_ROOT . DIRECTORY_SEPARATOR . 'www'); //расположение публичной папки
define('TEMPLATE_DIR', SITE_ROOT . DIRECTORY_SEPARATOR . 'view');  //расположение шаблонов
define('MODEL_DIR', SITE_ROOT . DIRECTORY_SEPARATOR . 'model');  //расположение классов модели
define('CONTROLLER_DIR', SITE_ROOT . DIRECTORY_SEPARATOR . 'controller');  //расположение контроллеров
define('TESTING_DIR', SITE_ROOT . DIRECTORY_SEPARATOR . 'testing');  //расположение тестовых контроллеров

define('IMAGE_DIR', WWW_ROOT . DIRECTORY_SEPARATOR . 'img');  //расположение больших картинок
define('THUMBS_DIR', IMAGE_DIR . DIRECTORY_SEPARATOR . 'thumbs');  //расположение миниатюр
define('STYLE_DIR', WWW_ROOT . DIRECTORY_SEPARATOR . 'css'); //расположение папки с файлами стилей

//define('LOGIN_PAGE', './?ctrl=security&action=login');
define('SALT', '12345'); //соль для пароля
define('SQL_HOST', 'localhost');
define('DB_NAME', '`y_shop`');  //данные для доступа к базе
define('DB_USER', 'root');
define('DB_PASSW', '');