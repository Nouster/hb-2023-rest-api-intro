<?php

header('Content-type: application/json; charset=UTF-8');

require_once 'db/pdo.php';

// $_SERVER['REQUEST_URI']

$uri = $_SERVER['REQUEST_URI'];

if ($uri === '/') {
  $stmt = $pdo->query("SELECT * FROM products");
  $products = $stmt->fetchAll();

  echo json_encode($products);
}

if ($uri === '/insert') {
  $data = json_decode(file_get_contents("php://input"), true);

  $query = "INSERT INTO products VALUES (null, :product_name, :base_price, :desc_product)";

  $stmt = $pdo->prepare($query);

  try {
    $stmt->execute([
      'product_name' => $data['name'],
      'base_price' => $data['baseprice'],
      'desc_product' => $data['description']
    ]);
    http_response_code(201);
  } catch (PDOException $e) {
    var_dump($e);
  }
}
