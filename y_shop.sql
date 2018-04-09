-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 08 2018 г., 18:56
-- Версия сервера: 5.7.16
-- Версия PHP: 5.6.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `y_shop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` tinyint(255) UNSIGNED NOT NULL,
  `category_name` varchar(45) NOT NULL,
  `parent_category` tinyint(255) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Вложенность категорий задается с помощью поля ''parent_category'' которая ссылается на ID из той же таблицы. Минус такой структуры - это может вызвать бесконечную рекурсию при поиске по вложенным категориям. Может так случиться, что категория вложена сама в себя, или родительская окажется вложенной в дочернюю, непосредственно, или через несколько других потомков. То есть кольцевая вложенность. Думаю, такую ошибку нужно обрабатывать на уровне скрипта. Я не знаю, можно ли запретить вложенность непосредственно на уровне БД.';

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `parent_category`) VALUES
(1, 'Без категории', NULL),
(2, 'Валенки', 6),
(3, 'Матрешки', 8),
(4, 'Балалайки', 7),
(5, 'Шапки-ушанки', 6),
(6, 'Одежда', NULL),
(7, 'Музыкальные инструменты', NULL),
(8, 'Сувениры', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `delivery_methods`
--

CREATE TABLE `delivery_methods` (
  `id` tinyint(4) UNSIGNED NOT NULL,
  `method_name` varchar(45) NOT NULL,
  `method_name_rus` varchar(45) DEFAULT NULL,
  `basic_cost` decimal(8,2) UNSIGNED DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `delivery_methods`
--

INSERT INTO `delivery_methods` (`id`, `method_name`, `method_name_rus`, `basic_cost`, `description`) VALUES
(1, 'courier', 'курьер', '300.00', NULL),
(2, 'Russian Post', 'Почта России', '300.00', NULL),
(3, 'self-export', 'самовывоз', '0.00', NULL),
(4, 'DHL', 'DHL', '0.00', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `goods`
--

CREATE TABLE `goods` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(127) NOT NULL,
  `product_description` text,
  `category` tinyint(255) UNSIGNED NOT NULL DEFAULT '1',
  `price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `good_main_photo` varchar(45) DEFAULT 'good_photo_placeholder.jpg',
  `hidden` tinyint(1) UNSIGNED DEFAULT '0',
  `quantity_in_stock` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `quantity_in_reserve` tinyint(3) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `goods`
--

INSERT INTO `goods` (`id`, `product_name`, `product_description`, `category`, `price`, `good_main_photo`, `hidden`, `quantity_in_stock`, `quantity_in_reserve`) VALUES
(1, 'Красные валенки', 'Очень хорошие красные валенки', 2, '2500.00', 'good_photo_placeholder.jpg', 1, 0, 0),
(2, 'Синие валенки', 'Отличные синие валенки', 2, '2500.00', 'good_photo_placeholder.jpg', 0, 0, 0),
(3, 'Матрешка - руководители России/СССР', 'Все Лидеры страны от Николая 2го до Владимира ПУтина', 3, '1500.00', 'good_photo_placeholder.jpg', 0, 0, 0),
(4, 'Матрешка классическая 6 вложений', NULL, 3, '1000.00', 'good_photo_placeholder.jpg', 0, 0, 0),
(5, 'Матрешка малая - 3 вложения', NULL, 3, '600.00', 'good_photo_placeholder.jpg', 0, 0, 0),
(8, 'Балалайка Yamaha', 'Высококачественная японская балалайка', 4, '25000.00', 'good_photo_placeholder.jpg', 0, 0, 0),
(9, 'Флешка с логотипом ФСИН', 'Отличный подарок', 8, '800.00', 'good_photo_placeholder.jpg', 0, 0, 0),
(10, 'Влажные салфетки \"Чистые руки\"', 'Выпущена в рамках программы борьбы с коррупцией', 1, '50.50', 'good_photo_placeholder.jpg', 0, 0, 0),
(11, 'Шапка бобровая', 'Ни один бобер не пострадал слишком сильно', 5, '5000.00', 'good_photo_placeholder.jpg', 0, 0, 0),
(12, 'Шапка зайчья', 'А нефиг было ездить зайцем', 5, '4000.00', 'good_photo_placeholder.jpg', 1, 0, 0),
(13, 'Шапка норковая', 'Добыта из самый глубоких норок якутии', 5, '15000.00', 'good_photo_placeholder.jpg', 0, 0, 0),
(14, 'Балалайка ядреная', 'Производство \"Запсибдревпромоборонкомплект\". Может применяться в качестве оружия самообороны.', 4, '4500.00', 'good_photo_placeholder.jpg', 0, 0, 0),
(15, 'Восхитительные штаны', 'Да, они просто восхитительные', 6, '300.00', 'good_photo_placeholder.jpg', 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_time` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `total_amount` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `order_status` tinyint(4) UNSIGNED NOT NULL DEFAULT '1',
  `delivery_method` tinyint(4) UNSIGNED DEFAULT NULL,
  `payment_method` tinyint(4) UNSIGNED DEFAULT NULL,
  `paid` tinyint(1) UNSIGNED DEFAULT '0',
  `users_comment` text,
  `delivery_address` tinytext,
  `shipping_cost` decimal(10,2) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_time`, `total_amount`, `order_status`, `delivery_method`, `payment_method`, `paid`, `users_comment`, `delivery_address`, `shipping_cost`) VALUES
(2, 1, '2017-06-03 13:35:21.005321', '4550.50', 1, NULL, NULL, 0, NULL, NULL, NULL),
(3, 4, '2017-06-03 13:36:51.273808', '32651.50', 2, 1, 4, 0, 'Чтоб все пучком чур наелся кур leh', 'Пустыня Найроби, у второго кактуса справа', NULL),
(4, 4, '2018-03-09 16:09:34.189920', '9752.50', 2, NULL, 4, 0, NULL, NULL, NULL),
(5, 4, '2018-03-09 16:25:33.495349', '9752.50', 3, NULL, NULL, 0, NULL, NULL, NULL),
(6, 3, '2018-03-10 13:49:15.767007', '2500.00', 1, 2, 4, 0, 'Блабла', '', NULL),
(10, 6, '2018-03-15 12:59:12.443079', '6600.00', 2, 1, 2, 0, 'просьба не опазываться', 'Завтра у метро парк культуры', NULL),
(11, 6, '2018-03-15 13:10:54.911193', '7000.00', 2, 1, 2, 0, '', 'Г. Тьмутаракань, ул. Ленина, д.1, корп 5, кв 22. Абубакиру Мурмухамедову', NULL),
(12, 4, '2018-03-30 12:34:53.907212', '800.00', 2, 1, 1, 0, 'комментарий', 'Деревня подлипково Дейр-эр-резорской области', NULL),
(13, 6, '2018-04-08 15:21:57.869171', '50000.00', 2, 2, 3, 0, 'qweqer', 'qweqwerweqr', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `order_goods`
--

CREATE TABLE `order_goods` (
  `order_id` int(10) UNSIGNED NOT NULL,
  `good_id` int(10) UNSIGNED NOT NULL,
  `good_count` tinyint(255) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `order_goods`
--

INSERT INTO `order_goods` (`order_id`, `good_id`, `good_count`) VALUES
(2, 10, 1),
(2, 11, 1),
(2, 14, 1),
(3, 2, 3),
(3, 8, 1),
(3, 10, 3),
(4, 2, 2),
(4, 3, 1),
(4, 4, 3),
(4, 10, 5),
(5, 2, 2),
(5, 3, 1),
(5, 4, 3),
(5, 10, 5),
(6, 2, 1),
(11, 3, 2),
(11, 4, 4),
(12, 9, 1),
(13, 8, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `order_status`
--

CREATE TABLE `order_status` (
  `id` tinyint(4) UNSIGNED NOT NULL,
  `status_name` varchar(45) NOT NULL,
  `status_name_rus` varchar(45) DEFAULT NULL,
  `description_rus` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `order_status`
--

INSERT INTO `order_status` (`id`, `status_name`, `status_name_rus`, `description_rus`) VALUES
(1, 'draft', 'черновик', 'человек пока делает заказ, и не нажал заветрую кнопку \"отправить заказ\", но промежуточные варианты сохраняются'),
(2, 'not confirmed', 'ожидает подтверждения', 'человек уже сделал заказ, но пока менеджер не подтвердил'),
(3, 'confirmed', 'подтвержден', NULL),
(4, 'on assembly', 'на сборке', NULL),
(5, 'in delivery', 'в доставке', 'передан в отдел доставки'),
(6, 'sent by mail', 'отправлен по почте', NULL),
(7, 'expects self-export', 'ожидает самовывоз', NULL),
(8, 'waiting for payment', 'ожидаем оплату', NULL),
(9, 'completed', 'завершен', 'оплачено доставлено, клиент доволен'),
(10, 'cancelled', 'отменен', 'отменен клиентом'),
(11, 'return', 'возврат', 'ожидаем возврата'),
(12, 'archive', 'архив', 'старые заказы могут быть запредены к редактированию, и вообще удалены из базы магазина в другое место, в некую архивную базу');

-- --------------------------------------------------------

--
-- Структура таблицы `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` tinyint(4) UNSIGNED NOT NULL,
  `method_name` varchar(45) NOT NULL,
  `method_name_rus` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `method_name`, `method_name_rus`) VALUES
(1, 'cash', 'Наличные'),
(2, 'сredit_card', 'Банковская карта'),
(3, 'Bitcoin', 'Биткоин'),
(4, 'Amunition', 'Патроны'),
(5, 'Barter', 'Бартер');

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` tinyint(4) UNSIGNED NOT NULL,
  `role_name` varchar(45) NOT NULL,
  `role_rus_name` varchar(45) DEFAULT NULL,
  `admin_panel` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `add_good` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `update_good` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `delete_good` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `hide_good` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `add_category` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `update_category` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `delete_caregory` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `hide_category` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `make_order_user` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `make_testimonial` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `make_post` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `ban_user` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `change_user_roles` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `view_orders` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `update_orders` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `delete_orders` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `mark_as_payed` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `message_to_client` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `view_customers_list` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `update_order_state` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `order_admin_comment` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `order_status_allowed_to_see` varchar(255) DEFAULT NULL,
  `order_status_allowed_to_set` varchar(255) DEFAULT NULL,
  `order_status_allowed_to_change` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `role_rus_name`, `admin_panel`, `add_good`, `update_good`, `delete_good`, `hide_good`, `add_category`, `update_category`, `delete_caregory`, `hide_category`, `make_order_user`, `make_testimonial`, `make_post`, `ban_user`, `change_user_roles`, `view_orders`, `update_orders`, `delete_orders`, `mark_as_payed`, `message_to_client`, `view_customers_list`, `update_order_state`, `order_admin_comment`, `order_status_allowed_to_see`, `order_status_allowed_to_set`, `order_status_allowed_to_change`) VALUES
(1, 'customer', 'клиент', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '{all}', '{2}', '{1}'),
(2, 'super_admin', 'супер админ', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '{all}', '{all}', '{all}'),
(3, 'goods_manager', 'менеджер товаров', 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, '{all}', '{}', '{}'),
(4, 'jun_goods_manager', 'мл. менеджер товаров', 1, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '{}', '{}', '{}'),
(5, 'seller', 'продавец', 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, '{all}', '{2,3,4,8,9,10,11,12}', '{1,2,3}'),
(6, 'delivery', 'служба доставки', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 1, 1, '{4,5,6,7}', '{4,5,6,7}', '{4,5,6,7}');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_name` varchar(255) DEFAULT 'Покупатель',
  `user_email` varchar(255) NOT NULL,
  `phone_number` int(10) UNSIGNED DEFAULT NULL,
  `passw` varchar(255) NOT NULL,
  `role` tinyint(4) UNSIGNED NOT NULL,
  `registration_time` timestamp(6) NULL DEFAULT CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `user_name`, `user_email`, `phone_number`, `passw`, `role`, `registration_time`) VALUES
(1, 'Вася', 'vasia@gmail.com', NULL, '58c4fcbdbd189123ab093df7fb7d86a5', 4, '2017-12-18 15:11:11.574750'),
(2, 'Петр', 'petr.poroshenko@ukraine.com.ua', NULL, '58c4fcbdbd189123ab093df7fb7d86a5', 1, '2017-12-18 15:11:11.574750'),
(3, 'Donald', 'president@whitehouse.gov', NULL, '58c4fcbdbd189123ab093df7fb7d86a5', 1, '2017-12-18 15:11:11.574750'),
(4, 'Bashar', 'asad@dictators.net', NULL, '58c4fcbdbd189123ab093df7fb7d86a5', 1, '2017-12-18 15:11:11.574750'),
(5, 'SuperAdmin', 'putin@putin.putin', NULL, '8cfa2282b17de0a598c010f5f0109e7d', 2, '2017-10-18 15:11:11.000000'),
(6, 'Dan', 'q@q.q', 2323232, '58c4fcbdbd189123ab093df7fb7d86a5', 1, '2018-01-18 15:11:11.574750');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `delivery_methods`
--
ALTER TABLE `delivery_methods`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `goods`
--
ALTER TABLE `goods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_good_category_idx` (`category`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_user_idx` (`user_id`),
  ADD KEY `fk_order_status` (`order_status`),
  ADD KEY `fk_order_delivery_idx` (`delivery_method`),
  ADD KEY `fk_order_payment_idx` (`payment_method`);

--
-- Индексы таблицы `order_goods`
--
ALTER TABLE `order_goods`
  ADD PRIMARY KEY (`order_id`,`good_id`),
  ADD KEY `fk_order_id_idx` (`order_id`),
  ADD KEY `fk_good_id_idx` (`good_id`);

--
-- Индексы таблицы `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ruser_roles_idx` (`role`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` tinyint(255) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT для таблицы `delivery_methods`
--
ALTER TABLE `delivery_methods`
  MODIFY `id` tinyint(4) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `goods`
--
ALTER TABLE `goods`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT для таблицы `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` tinyint(4) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` tinyint(4) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `goods`
--
ALTER TABLE `goods`
  ADD CONSTRAINT `fk_good_category` FOREIGN KEY (`category`) REFERENCES `categories` (`id`) ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_delivery` FOREIGN KEY (`delivery_method`) REFERENCES `delivery_methods` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_payment` FOREIGN KEY (`payment_method`) REFERENCES `payment_methods` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_status` FOREIGN KEY (`order_status`) REFERENCES `order_status` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `order_goods`
--
ALTER TABLE `order_goods`
  ADD CONSTRAINT `fk_good_id` FOREIGN KEY (`good_id`) REFERENCES `goods` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_ruser_roles` FOREIGN KEY (`role`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
