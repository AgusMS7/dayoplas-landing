<?php
$dsn  = 'mysql:host=127.0.0.1;dbname=u177763909_dayloplas;charset=utf8mb4'; 
$user = 'u177763909_andrescastelli';       
$pass = 'WebDaylo2025';         

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];
$pdo = new PDO($dsn, $user, $pass, $options);
