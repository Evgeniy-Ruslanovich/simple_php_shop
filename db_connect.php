<?php

/**
 * ФАЙЛ КОНФИГУРАЦИИ, ЗАПРАШИВАЕТСЯ ИНДЕКСОМ В САМОМ НАЧАЛЕ СКРИПТА
 */
require_once 'config.php';

$link = mysqli_connect(SQL_HOST, DB_USER, DB_PASSW);