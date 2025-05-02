<?php

namespace Vosouza\Sonomais\data\repository;

use PDO;
use PDOException;
use Vosouza\Sonomais\data\DataSourceInterface;
use Vosouza\Sonomais\model\Product;

class ProductRepository
{

    private PDO $pdo;

    public function __construct(DataSourceInterface $source)
    {
        $this->pdo = $source->getConection();
        $this->createTable(); // Garante que a tabela exista
    }

    private function extrairUrls(string $stringUrls): array
    {
        // Divide a string em um array de URLs usando o ponto e vírgula como delimitador.
        $listaDeUrls = explode(';', $stringUrls);

        // Remove espaços em branco extras de cada URL na lista.
        $listaDeUrlsTratada = array_map('trim', $listaDeUrls);

        // Filtra URLs vazias que podem ter resultado de múltiplos ponto e vírgulas.
        $listaDeUrlsFiltrada = array_filter($listaDeUrlsTratada, function ($url) {
            return !empty($url);
        });

        // Retorna o array de URLs filtradas.
        return array_values($listaDeUrlsFiltrada);
    }

    private function createTable(): void
    {
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS produtos (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
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

    public function insert(Product $product): ?int
    {
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

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM produtos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao deletar produto: " . $e->getMessage());
            return false;
        }
    }

    public function update(Product $product, int $id): bool
    {
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

    public function findAll(int $limit = 10): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM produtos LIMIT :limit");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_FUNC, function ($id, $name, $description, $price, $category, $image_url, $isStar) {
                $urlList = $this->extrairUrls($image_url);
                return new Product($id, $name, $description, $price, $category, $image_url, $isStar, $urlList);
            });
        } catch (PDOException $e) {
            error_log("Erro ao buscar produtos: " . $e->getMessage());
            return [];
        }
    }

    public function getById(int $id): ?Product
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE id = :id");
            $stmt->bindValue(':id', $$id);
            $stmt->execute();
            $produc = $stmt->fetch(PDO::FETCH_ASSOC);
            $urlList = $this->extrairUrls($produc['image']);
            return new Product($id, $produc['name'], $produc['description'], $produc['price'], $produc['type'], $produc['image'], $produc['isStar'], $urlList);
        } catch (PDOException $e) {
            error_log("Erro ao buscar produtos: " . $e->getMessage());
            return null;
        }
    }
}
