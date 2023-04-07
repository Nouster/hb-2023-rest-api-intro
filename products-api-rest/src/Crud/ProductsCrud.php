<?php

namespace App\Crud;

use App\Crud\Exception\UnprocessableContentException;
use PDO;

class ProductsCrud
{
  public function __construct(private PDO $pdo)
  {
  }

  /**
   * Creates a new product
   *
   * @param array $data name, base price & description (optional)
   * @return int ID of created product
   * @throws Exception
   */
  public function create(array $data): int
  {
    if (!isset($data['name']) || !isset($data['baseprice'])) {
      throw new UnprocessableContentException("Name and base price are required");
    }

    $query = "INSERT INTO products VALUES (null, :product_name, :baseprice, :product_description)";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute([
      'product_name' => $data['name'],
      'baseprice' => $data['baseprice'],
      'product_description' => $data['description']
    ]);

    return $this->pdo->lastInsertId();
  }

  public function findAll(): array
  {
    $stmt = $this->pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll();

    return ($products === false) ? [] : $products;
  }

  public function find(int $id): ?array
  {
    return [];
  }

  public function update(int $id, array $data): bool
  {
    return true;
  }

  public function delete(int $id): bool
  {
    return true;
  }
}
