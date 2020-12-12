-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Сен 19 2017 г., 04:05
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `integra`
--

-- --------------------------------------------------------

--
-- Структура таблицы `dolg`
--

CREATE TABLE IF NOT EXISTS `dolg` (
  `id_dolg` int(11) NOT NULL AUTO_INCREMENT,
  `golg` varchar(240) NOT NULL,
  `dolg_ang` varchar(240) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id_dolg`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Дамп данных таблицы `dolg`
--

INSERT INTO `dolg` (`id_dolg`, `golg`, `dolg_ang`, `status`) VALUES
(1, 'Прораб', 'Foreman', 0),
(2, 'Закупщик', 'Purchaser', 1),
(3, 'Технический директор', 'Technical Director', 2),
(4, 'Генеральный директор', 'CEO', 2),
(5, 'Главный закупок', 'Head of Purchasing', 2),
(6, 'Бухгалтер', 'Accountant', 9),
(7, 'Начальство', 'Boss', 2),
(8, 'Начальник закупок', '', 2),
(9, 'Складовщик', 'sklad', 5);

-- --------------------------------------------------------

--
-- Структура таблицы `sostoyanie`
--

CREATE TABLE IF NOT EXISTS `sostoyanie` (
  `id_sost` int(11) NOT NULL AUTO_INCREMENT,
  `sostoyanie` varchar(50) NOT NULL,
  `sostang` varchar(70) NOT NULL,
  PRIMARY KEY (`id_sost`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `sostoyanie`
--

INSERT INTO `sostoyanie` (`id_sost`, `sostoyanie`, `sostang`) VALUES
(1, 'Одобрено', 'Approved'),
(3, 'На доработку', 'For revision'),
(4, 'Пересмотр', 'Revision'),
(5, 'На исполнение', 'For execution'),
(6, 'На подпись', 'For signature'),
(7, 'Указание цен', 'Indication of prices'),
(8, 'Одобрение цен', ''),
(9, 'На рассмотрении', ''),
(10, 'Подписывается', '');

-- --------------------------------------------------------

--
-- Структура таблицы `spisok`
--

CREATE TABLE IF NOT EXISTS `spisok` (
  `id_spis` int(11) NOT NULL AUTO_INCREMENT,
  `idzay` int(11) NOT NULL,
  `naim` varchar(240) NOT NULL,
  `edizm` varchar(100) NOT NULL,
  `kolvo` double NOT NULL,
  `cenazaed` double NOT NULL,
  `summa` double NOT NULL,
  `datapost` varchar(20) NOT NULL,
  `prim` text NOT NULL,
  `status` varchar(240) NOT NULL,
  `idzakupshik` int(11) NOT NULL,
  `fiozakupshik` varchar(240) NOT NULL,
  `zakbool` int(11) NOT NULL,
  `skladkv` double DEFAULT NULL,
  `ced1` double NOT NULL,
  `ced2` double NOT NULL,
  `ced3` double NOT NULL,
  `prim1` text NOT NULL,
  `prim2` text NOT NULL,
  `prim3` text NOT NULL,
  PRIMARY KEY (`id_spis`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=192 ;

--
-- Дамп данных таблицы `spisok`
--

INSERT INTO `spisok` (`id_spis`, `idzay`, `naim`, `edizm`, `kolvo`, `cenazaed`, `summa`, `datapost`, `prim`, `status`, `idzakupshik`, `fiozakupshik`, `zakbool`, `skladkv`, `ced1`, `ced2`, `ced3`, `prim1`, `prim2`, `prim3`) VALUES
(185, 1, 'Почки', 'шт', 15, 100, 1500, '2017-12-31', 'нет', '1', 2, 'Нурбек ', 0, 5, 100, 300, 500, 'нет', 'нет', 'нет'),
(186, 1, 'печень', 'шт', 5, 20, 100, '2017-12-31', 'нет', '1', 2, 'Нурбек ', 0, 4, 10, 20, 0, 'нет', 'нет', 'нет'),
(187, 1, 'Боинг', 'шт', 10, 40, 400, '2017-12-31', 'нет', '1', 2, 'Нурбек ', 0, 6, 40, 55, 600, 'нет', 'нет', 'нет'),
(188, 2, 'Гвозди', 'штук', 10, 100, 1000, '2017-12-30', 'нет1', '1', 2, 'Нурбек ', 0, 0, 100, 200, 300, 'нет1', 'нет2', 'нет3'),
(189, 2, 'Балки', 'штук', 10, 50, 500, '2017-12-31', 'нет', '1', 2, 'Нурбек ', 0, 0, 40, 50, 60, 'нет', 'нет', 'нет'),
(190, 3, 'Рейки', 'шт', 50, 0, 0, '2017-04-26', '', '1', 2, 'Нурбек ', 1, 10, 0, 0, 0, 'нет', 'нет', 'нет'),
(191, 3, 'Страпила', 'шт', 40, 0, 0, '2017-04-29', '', '1', 2, 'Нурбек ', 1, 15, 0, 0, 0, 'нет', 'нет', 'нет');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id_polz` int(11) NOT NULL AUTO_INCREMENT,
  `pass` varchar(240) NOT NULL,
  `login` varchar(240) NOT NULL,
  `polz` varchar(240) NOT NULL,
  `iddolg` int(11) NOT NULL,
  PRIMARY KEY (`id_polz`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id_polz`, `pass`, `login`, `polz`, `iddolg`) VALUES
(1, 'prorab1', 'prorab1', 'Абылкасымов Н.', 1),
(2, 'zakup1', 'zakup1', 'Нурбек ', 2),
(3, 'tehnichka', 'tehnichka', 'Владимир Степанович', 3),
(4, 'gena', 'gena', 'Генадий Генадьевич Грибоедов', 4),
(5, 'pechkin', 'pechkin', 'Дима', 5),
(6, 'buharik', 'buharik', 'Счетоводов Александр Александрович', 6),
(7, 'boss', 'boss', 'Начальство', 7),
(8, 'prorab2', 'prorab2', 'Сергей Сергеич', 1),
(9, 'prorab3', 'prorab3', 'Гробовский Петр', 1),
(10, 'zakup2', 'zakup2', 'Марат', 2),
(11, 'zakup3', 'zakup3', 'Адмиралов Закупор', 2),
(12, 'nachzak', 'nachzak', 'Мунарбек', 8),
(13, 'sklad', 'sklad', 'кладовщик', 9);

-- --------------------------------------------------------

--
-- Структура таблицы `zayavka`
--

CREATE TABLE IF NOT EXISTS `zayavka` (
  `nomer` int(11) NOT NULL,
  `data` varchar(50) NOT NULL,
  `prorab` varchar(240) NOT NULL,
  `direktor` varchar(240) NOT NULL,
  `zakupshik` varchar(240) NOT NULL,
  `sostoyanieid` int(11) NOT NULL,
  `na_obrabotkeid` int(11) NOT NULL,
  `object` varchar(240) NOT NULL,
  `idprorab` int(11) NOT NULL,
  `idzakup` int(11) NOT NULL,
  `datesleg` text NOT NULL,
  `itogo` double NOT NULL,
  PRIMARY KEY (`nomer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `zayavka`
--

INSERT INTO `zayavka` (`nomer`, `data`, `prorab`, `direktor`, `zakupshik`, `sostoyanieid`, `na_obrabotkeid`, `object`, `idprorab`, `idzakup`, `datesleg`, `itogo`) VALUES
(1, '2017-04-19', 'Абылкасымов Н.', '0', '0', 5, 6, 'ТП Ортосай', 1, 0, 'Заявка создана: 2017-04-19<br>Дата одобрения тех. дир.: 23.04.17<br>Дата одобрения гл. закуп.: 23.04.17<br>Дата одобрения нач. закуп.: 23.04.17<br>Дата утверждения тех. дир.: 23.04.17<br>', 1180),
(2, '2017-04-20', 'Абылкасымов Н.', '0', '0', 5, 6, 'ТП Ортосай', 1, 0, 'Заявка создана: 2017-04-20<br>Дата одобрения тех. дир.: 24.04.17<br>Дата одобрения гл. закуп.: 24.04.17<br>Дата одобрения нач. закуп.: 24.04.17<br>Дата утверждения тех. дир.: 24.04.17<br>', 2000),
(3, '2017-04-20', 'Абылкасымов Н.', '0', '0', 1, 5, 'Алькатрас', 1, 0, 'Заявка создана: 2017-04-20<br>Дата одобрения тех. дир.: 24.04.17<br>', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
