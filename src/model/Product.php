<?php

namespace Vosouza\Sonomais\model;

class Product {

    public string $name;
    public string $description;
    public float $price;
    public string $type;
    public string $image;
    public bool $isStar;

    public function __construct(
        string $name,
        string $description,
        float $price,
        string $type,
        string $image,
        bool $isStar
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->type = $type;
        $this->isStar = $isStar;
        $this->image = $image;
    }

    public static function fromPost(): ?Product {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_SERVER['REQUEST_URI'] !== '/dash') {
            echo "Não é um POST para /dash.";
            return null; // Não é um POST para /dash
        }
        var_dump($_POST);
        // Validação básica dos dados do formulário
        if (
            !isset($_POST['name']) ||
            !isset($_POST['description']) ||
            !isset($_POST['price']) ||
            !isset($_POST['type']) ||
            !isset($_FILES['image']) ||
            !isset($_POST['isStar'])
        ) {
            echo 'Dados incompletos';
            return null; // Dados incompletos
        }

        // Sanitização e conversão dos dados
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        $isStar = filter_var($_POST['isStar'], FILTER_VALIDATE_BOOLEAN);

        $imagePath = self::uploadImage($_FILES['image'], $name);
        if ($imagePath === null) {
            echo 'erro no upload da imagem';
            return null; // Falha no upload da imagem
        }

        // Validação adicional (opcional)
        if ($name === null || $description === null || $price === false || $type === null || $imagePath === null || $isStar === null) {
            echo 'Falha na validação/conversão';
            return null; // Falha na validação/conversão
        }

        return new Product(
            $name,
            $description,
            $price,
            $type,
            $imagePath,
            $isStar
        );
    }

    private static function uploadImage(array $imageFile, string $productName): ?string
    {
        if ($imageFile['error'] === UPLOAD_ERR_OK) {
            $originalName = $imageFile['name'];
            $tmpName = $imageFile['tmp_name'];
            $uniqueName = self::generateUniqueImageName($originalName, $productName);
            $uploadDir = '/images/';
            $destinationPath = __DIR__.'/../../public_html' . $uploadDir . $uniqueName;
            $relativeImagePath = $uploadDir . $uniqueName;

            if (move_uploaded_file($tmpName, $destinationPath)) {
                return $relativeImagePath;
            } else {
                echo 'Erro ao mover o arquivo para o destino.';
                return null;
            }
        } else {
            echo 'Erro no upload da imagem: ' . $imageFile['error'];
            return null;
        }
    }

    private static function generateUniqueImageName(string $originalName, string $productName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $nameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);

        // Remove espaços e converte para lowercase
        $sanitizedProductName = strtolower(str_replace(' ', '-', $productName));

        // Remove caracteres especiais (mantém letras, números e hífens)
        $sanitizedProductName = preg_replace('/[^a-z0-9-]+/', '', $sanitizedProductName);

        // Adiciona a data e hora do upload para garantir a unicidade
        $timestamp = date('YmdHis');

        return $sanitizedProductName . '-' . $timestamp . '.' . $extension;
    }


}
