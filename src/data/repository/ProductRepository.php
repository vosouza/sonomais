<?php

namespace Vosouza\Sonomais\data\repository;

use PDO;
use PDOException;
use Vosouza\Sonomais\data\DataSourceInterface;
use Vosouza\Sonomais\model\Product;

class ProductRepository {

    private PDO $pdo;

    public function __construct(DataSourceInterface $source) {
        $this->pdo = $source->getConection();
        $this->createTable(); // Garante que a tabela exista
    }

    private function createTable(): void {
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS produtos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT NOT NULL,
                price REAL NOT NULL,
                type TEXT NOT NULL,
                image TEXT NOT NULL,
                isStar INTEGER NOT NULL
            )");
        } catch (PDOException $e) {
            error_log("Erro ao criar tabela: " . $e->getMessage());
            throw $e; // Re-lança a exceção para tratamento externo
        }
    }

    public function insert(Product $product): ?int {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO produtos (name, description, price, type, image, isStar) VALUES (:name, :description, :price, :type, :image, :isStar)");
            $stmt->execute([
                ':name' => $product->name,
                ':description' => $product->description,
                ':price' => $product->price,
                ':type' => $product->type,
                ':image' => $product->image,
                ':isStar' => $product->isStar ? 1 : 0,
            ]);
            return (int) $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao inserir produto: " . $e->getMessage());
            return null;
        }
    }

    public function delete(int $id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM produtos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao deletar produto: " . $e->getMessage());
            return false;
        }
    }

    public function update(Product $product, int $id): bool {
        try {
            $stmt = $this->pdo->prepare("UPDATE produtos SET name = :name, description = :description, price = :price, type = :type, image = :image, isStar = :isStar WHERE id = :id");
            $stmt->execute([
                ':name' => $product->name,
                ':description' => $product->description,
                ':price' => $product->price,
                ':type' => $product->type,
                ':image' => $product->image,
                ':isStar' => $product->isStar ? 1 : 0,
                ':id' => $id,
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar produto: " . $e->getMessage());
            return false;
        }
    }

    public function findAll(int $limit = 10): array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM produtos LIMIT :limit");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_CLASS, Product::class);
        } catch (PDOException $e) {
            error_log("Erro ao buscar produtos: " . $e->getMessage());
            return [];
        }
    }

}
