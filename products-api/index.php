<?php

header('Content-type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');

set_exception_handler(function (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'error' => 'Une erreur est survenue',
    'code' => $e->getCode()
  ]);
});

require_once 'db/pdo.php';

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
  $stmt->execute([
    'product_name' => $data['name'],
    'base_price' => $data['baseprice'],
    'desc_product' => $data['description']
  ]);
  http_response_code(201);
}

if ($uri === '/update') {
  $data = json_decode(file_get_contents("php://input"), true);

  $query = "UPDATE products SET name=:product_name, basePrice=:base_price, description=:desc_product WHERE id=:id";

  $stmt = $pdo->prepare($query);
  $stmt->execute([
    'product_name' => $data['name'],
    'base_price' => $data['baseprice'],
    'desc_product' => $data['description'],
    'id' => $data['id']
  ]);
  http_response_code(204);
}
