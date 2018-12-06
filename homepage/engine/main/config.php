<?php
// Максимально допустимое количество символов для имени
define('JOURNAL_MAX_NAME_LENGTH', 30);
// Максимально допустимое количество символов для заметки
define('JOURNAL_MAX_MSG_LENGTH', 200);
// Максимальное количество записей в журнале посещений
define('JOURNAL_MAX_RECORD', 256);
// Путь к файлу с сообщениями
define('JOURNAL_DATA', 'data/journal.txt');
// Имя для записи в журнал как Пономарев Дмитрий
define('JOURNAL_NAME','admin_write');
// Пароль для записи в журнал как Пономарев Дмитрий
define('JOURNAL_PASSWORD','e92ce22705068278b4a192290ca0ff88');
// Длина пароля для записи в журнал как Пономарев Дмитрий
define('JOURNAL_PASSWORD_LENGTH', 9);
// Количество допустимых переносов строки в заметке
define('JOURNAL_CRLF', 8);
// Количество сообщений на странице
define('JOURNAL_NOTE_COUNT', 4);
// Сколько ссылок на страницы выводить одновременно
define('JOURNAL_NAV_LINKS_COUNT', 4);

// Путь к файлу с описанием сайта
define('DESCRIPTION_DATA', 'data/description.txt');

// Путь к файлу с новостями
define('NEWS_DATA', 'data/news.txt');
// Количество новостей на главной странице в нормальном режиме
define('NEWS_COUNT_DEFAULT', 2);
// Количество новостей на главной странице в режиме навигации по страницам
define('NEWS_COUNT', 8);
// Сколько ссылок на страницы выводить одновременно
define('NEWS_NAV_LINKS_COUNT', 5);

// Путь к файлу с учетом посетителей
define('VISITOR_DATA', 'data/visitors.txt');

// Путь к файлу голосования
define('VOTING_DATA', 'data/voting.txt');
// Ширина изображения гистограммы голосования
define('VOTING_IMG_WIDTH', 250);
// Высота изображения гистограммы голосования
define('VOTING_IMG_HEIGHT', 170);
// Строки легенды
$legendarr = array(
'Internet Explorer',
'Mozilla Firefox',
'Opera',
'Другой');
// Размер поля отступа в пикселах
define('VOTING_FIELD', 5);
// Путь к файлу с TrueType шрифтом
define('VOTING_TTF_PATH', '../../Fonts/Monotype-Corsiva.TTF');
// Размер шрифта
define('VOTING_TTF_SIZE', 10);
// Угол наклона шрифта
define('VOTING_TTF_ANGLE', 0);
// Межстрочный интервал
define('VOTING_TTF_LINEHEIGHT', 0.5);
// Отступ графика от легенды
define('VOTING_LEGEND_GRAPHIC_SPAN', 5);
// Расстояние между столбцами
define('VOTING_COLUMN_SPAN', 8);
// Размер шрифта для % до 5
define('VOTING_PERCENT_SIZE', 2);
// Путь к файлу с ip и временем голосования
define('VOTING_FLUD', 'data/votingflud.txt');
// Время запрета повторного голосования в сек.
define('VOTING_FLUD_TIME', 3600);

// Путь к файлу логотипов
define('LOGOS_DATA', 'data/logotypes.ini');
// Перфикс к полю logo
define('LOGOS_PREFIX', 'img/logos/');
// Количество логотипов на странице
define('LOGOS_COUNT', 5);
// Сколько ссылок на страницы выводить одновременно
define('LOGOS_NAV_LINKS_COUNT', 5);

define('RSS_COUNT', 5);
?>