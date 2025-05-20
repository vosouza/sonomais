<?php

namespace Vosouza\Sonomais\data\repository;

use PDO;
use PDOException;
use Vosouza\Sonomais\data\DataSourceInterface;
use Vosouza\Sonomais\model\Product;
use Vosouza\Sonomais\SonoLogger;

class ProductRepository
{

    private PDO $pdo;
    private string $uploadDir = __DIR__ . '/../../../uploads/'; // Ajuste o caminho conforme sua estrutura

    public function __construct(DataSourceInterface $source)
    {
        $this->pdo = $source->getConection();
        // $this->createTable(); // Garante que a tabela exista
    }

    private function extrairUrls(string $stringUrls): array
    {
        $listaDeUrls = explode(';', $stringUrls);
        $listaDeUrlsTratada = array_map('trim', $listaDeUrls);
        $listaDeUrlsFiltrada = array_filter($listaDeUrlsTratada, function ($url) {
            return !empty($url);
        });
        
        return array_values($listaDeUrlsFiltrada);
    }


    // private function createTable(): void
    // {
    //     try {
    //         $this->pdo->exec("CREATE TABLE IF NOT EXISTS produtos (
    //             id INTEGER PRIMARY KEY AUTO_INCREMENT,
    //             name TEXT NOT NULL,
    //             description TEXT NOT NULL,
    //             price REAL NOT NULL,
    //             type TEXT NOT NULL,
    //             image TEXT NOT NULL,
    //             isStar INTEGER NOT NULL
    //         )");
    //     } catch (PDOException $e) {
    //         error_log("Erro ao criar tabela: " . $e->getMessage());
    //         throw $e; // Re-lança a exceção para tratamento externo
    //     }
    // }

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
            // Busca as imagens do produto antes de deletá-lo
            $productToDelete = $this->getById($id);
            $imagesToDelete = $productToDelete ? $productToDelete->imageList : [];

            $stmt = $this->pdo->prepare("DELETE FROM produtos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $deleted = $stmt->rowCount() > 0;

            if ($deleted) {
                $this->deleteProductImages($imagesToDelete);
            }

            return $deleted;
        } catch (PDOException $e) {
            error_log("Erro ao deletar produto: " . $e->getMessage());
            return false;
        }
    }

    public function update(Product $product, int $id): bool
    {
        try {
            // Busca as imagens antigas antes de atualizar
            $oldProduct = $this->getById($id);
            $oldImages = $oldProduct ? $oldProduct->imageList : [];
            $newImages = $this->extrairUrls($product->image);

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
            $updated = $stmt->rowCount() > 0;

            if ($updated) {
                $this->deleteUnusedImages($oldImages, $newImages);
            }

            return $updated;
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

    public function findAllFeatured(int $limit = 10): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM produtos Where produtos.isStar > 0 LIMIT :limit");
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

    public function findByType(String $productType = "todos",int $limit = 10): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM produtos Where produtos.type = :productType LIMIT :limit");
            $stmt->bindValue(':productType', $productType, PDO::PARAM_STR);
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
            SonoLogger::log('Buscando produto com ID: ' . $id);
            $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE id = :id");
            SonoLogger::log('SQL da consulta: SELECT * FROM produtos WHERE id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            SonoLogger::log('Valor bindado para :id: ' . $id);
            $executou = $stmt->execute();
            SonoLogger::log('Resultado da execução da query: ' . ($executou ? 'true' : 'false'));
            if (!$executou) {
                SonoLogger::log('Erro na execução da query: ' . print_r($stmt->errorInfo(), true));
                return null;
            }
            $produc = $stmt->fetch(PDO::FETCH_ASSOC);
            SonoLogger::log('Resultado do fetch (array associativo): ' . print_r($produc, true));
            if ($produc) {
                $urlList = $this->extrairUrls($produc['image'] ?? '');
                return new Product(
                    (int) $produc['id'],
                    $produc['name'] ?? '',
                    $produc['description'] ?? '',
                    (float) $produc['price'] ?? 0.0,
                    $produc['type'] ?? '',
                    $produc['image'] ?? '',
                    (bool) $produc['isStar'] ?? false,
                    $urlList ?? []
                );
            } else {
                SonoLogger::log('Nenhum produto encontrado com o ID: ' . $id);
                return null;
            }
        } catch (PDOException $e) {
            error_log("Erro ao buscar produtos: " . $e->getMessage());
            SonoLogger::log('Erro PDOException: ' . $e->getMessage());
            return null;
        }
    }
    private function deleteProductImages(array $imageUrls): void
    {
        foreach ($imageUrls as $url) {
            $filePath = $this->uploadDir . basename($url);
            if (file_exists($filePath) && is_writable($filePath)) {
                if (unlink($filePath)) {
                    SonoLogger::log('Arquivo de imagem deletado: ' . $filePath);
                } else {
                    SonoLogger::log('Erro ao deletar arquivo de imagem: ' . $filePath);
                }
            } elseif (!file_exists($filePath)) {
                SonoLogger::log('Arquivo de imagem não encontrado: ' . $filePath);
            } else {
                SonoLogger::log('Permissão negada para deletar arquivo: ' . $filePath);
            }
        }
    }

    private function deleteUnusedImages(array $oldImages, array $newImages): void
    {
        $imagesToDelete = array_diff($oldImages, $newImages);
        $this->deleteProductImages($imagesToDelete);
    }
}