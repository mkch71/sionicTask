<?php
$dbhost = "localhost";
$dbuser = "mkch71_sionic";
$dbpass = "%KpJO7Y8";
$dbname = "mkch71_sionic";
$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
$mysqli->set_charset("utf-8");
  
  //Проврка подключения
if ($mysqli->connect_error) {
  die("Не удалось подключиться к БД:" .$mysqli->connect_error);
}