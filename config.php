<?php

define("DBHOST", "localhost");
define("DBUSER", "root");
define("DBPASS", "494611");
define("DB", "apple");
define("PATH", "http://localhost/goods_catalog/");

$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DB, "3307") or die("Нет соединения с БД");
mysqli_set_charset($connection, "utf8") or die("Не установлена кодировка соединения");