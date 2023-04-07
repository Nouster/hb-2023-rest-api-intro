<?php

// Charge l'autoloader PSR-4 de Composer
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\DbInitializer;
use App\Config\ExceptionHandlerInitializer;
use App\Crud\Exception\UnprocessableContentException;
use App\Crud\ProductsCrud;
use Symfony\Component\Dotenv\Dotenv;

header('Content-type: application/json; charset=UTF-8');

// Charge les variables d'environnement
$dotenv = new Dotenv();
$dotenv->loadEnv('.env');

// Définit un gestionnaire d'exceptions au niveau global
ExceptionHandlerInitializer::registerGlobalExceptionHandler();
$pdo = DbInitializer::getPdoInstance();

$uri = $_SERVER['REQUEST_URI'];
$httpMethod = $_SERVER['REQUEST_METHOD'];
const RESOURCES = ['products'];

// Ressource seule, type /products/{id}
// Explode :
// /products => ['', 'products']
// /products/5 => ['', 'products', '5']
// /products/coucou => ['', 'products', 'coucou']
$uriParts = explode('/', $uri);
$isItemOperation = count($uriParts) === 3;
$productsCrud = new ProductsCrud($pdo);

// Collection de produits
if ($uri === '/products' && $httpMethod === 'GET') {
  echo json_encode($productsCrud->findAll());
  exit;
}

// Création de produit
if ($uri === '/products' && $httpMethod === 'POST') {
  try {
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = $productsCrud->create($data);
    http_response_code(201);
    echo json_encode([
      'uri' => '/products/' . $productId
    ]);
  } catch (UnprocessableContentException $e) {
    http_response_code(422);
    echo json_encode([
      'error' => $e->getMessage()
    ]);
  } finally {
    exit;
  }
}

// Identifie si on est sur une opération sur un élément
if (!$isItemOperation) {
  http_response_code(404);
  echo json_encode([
    'error' => 'Route non trouvée'
  ]);
  exit;
}

// Identifie si l'ID est valide (pas s'il existe en bdd)
$resourceName = $uriParts[1];
$id = intval($uriParts[2]);
if ($id === 0) {
  http_response_code(400);
  echo json_encode([
    'error' => 'ID non valide'
  ]);
  exit;
}

if ($resourceName === 'products' && $isItemOperation && $httpMethod === 'GET') {
  $query = "SELECT * FROM products WHERE id = :id";
  $stmt = $pdo->prepare($query);
  $stmt->execute(['id' => $id]);

  $product = $stmt->fetch();

  if ($product === false) {
    http_response_code(404);
    echo json_encode([
      'error' => 'Produit non trouvé'
    ]);
    exit;
  }

  echo json_encode($product);
}

if ($resourceName === 'products' && $isItemOperation && $httpMethod === 'PUT') {
  $data = json_decode(file_get_contents('php://input'), true);

  if (!isset($data['name']) || !isset($data['baseprice'])) {
    http_response_code(422);
    echo json_encode([
      'error' => 'Name and base price are required'
    ]);
    exit;
  }

  $query = "UPDATE products SET name=:product_name, basePrice=:baseprice, description=:product_description WHERE id = :id";
  $stmt = $pdo->prepare($query);
  $stmt->execute([
    'product_name' => $data['name'],
    'baseprice' => $data['baseprice'],
    'product_description' => $data['description'],
    'id' => $id
  ]);
  if ($stmt->rowCount() === 0) {
    http_response_code(404);
    echo json_encode([
      'error' => 'Produit non trouvé'
    ]);
    exit;
  }
  http_response_code(204);
}

if ($resourceName === 'products' && $isItemOperation && $httpMethod === 'DELETE') {
  $query = "DELETE FROM products WHERE id = :id";
  $stmt = $pdo->prepare($query);
  $stmt->execute(['id' => $id]);
  if ($stmt->rowCount() === 0) {
    http_response_code(404);
    echo json_encode([
      'error' => 'Produit non trouvé'
    ]);
    exit;
  }
  http_response_code(204);
}
