<?php

$dbConfig = parse_ini_file(__DIR__ . '/../config/db.ini');
[
  'DB_HOST' => $dbHost,
  'DB_PORT' => $dbPort,
  'DB_NAME' => $dbName,
  'DB_USER' => $dbUser,
  'DB_PASSWORD' => $dbPassword
] = $dbConfig;

$dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";

$options = [
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];

try {
  $pdo = new PDO($dsn, $dbUser, $dbPassword, $options);
} catch (PDOException $e) {
  echo "Une erreur est survenue lors de la connexion à la base de données";
  exit;
}
