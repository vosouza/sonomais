<?php

namespace Vosouza\Sonomais\model;
use Vosouza\Sonomais\{
    SonoLogger,
};

class Product {

    public int $id;
    public string $name;
    public string $description;
    public float $price;
    public string $type;
    public string $image;
    public bool $isStar;
    public array $imageList;

    public function __construct(
        int $id,
        string $name,
        string $description,
        float $price,
        string $type,
        string $image,
        bool $isStar,
        array $imageList,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->type = $type;
        $this->isStar = $isStar;
        $this->image = $image;
        $this->imageList = $imageList;
    }

    public static function fromPost(): ?Product {
        var_dump($_POST);
        var_dump($_FILES); // Importante para entender a estrutura de $_FILES

        // Validação básica dos dados do formulário (campos de texto)
        if (
            !isset($_POST['name']) ||
            !isset($_POST['description']) ||
            !isset($_POST['price']) ||
            !isset($_POST['type']) ||
            !isset($_FILES['image']) || !is_array($_FILES['image']['name']) // Verifica se 'image' é um array
        ) {
            SonoLogger::log( 'Dados incompletos');
            return null; // Dados incompletos
        }

        // Sanitização e conversão dos dados de texto
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

        $isStar = isset($_POST['isStar']) ? filter_var($_POST['isStar'], FILTER_VALIDATE_BOOLEAN) : false;

        $imagePaths = self::uploadImages($_FILES['image'], $name);
        if ($imagePaths === null || empty($imagePaths)) {
            SonoLogger::log( 'Erro no upload de uma ou mais imagens');
            return null; // Falha no upload de alguma imagem
        }

        $allImagePaths = implode(';', $imagePaths);

        // Validação adicional (opcional)
        if ($name === null || $description === null || $price === false || $type === null || empty($allImagePaths) || $isStar === null) {
            SonoLogger::log( 'Falha na validação/conversão');
            return null; // Falha na validação/conversão
        }

        return new Product(
            $id ?? 0,
            $name,
            $description,
            $price,
            $type,
            $allImagePaths,
            $isStar,
            [],
        );
    }

    private static function uploadImages(array $imageFiles, string $productName): ?array {
        $uploadedPaths = [];
        $uploadDir = 'uploads/'; // Defina seu diretório de uploads
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $numFiles = count($imageFiles['name']);
        for ($i = 0; $i < $numFiles; $i++) {
            if ($imageFiles['error'][$i] === UPLOAD_ERR_OK) {
                $tmpFilePath = $imageFiles['tmp_name'][$i];
                $originalName = $imageFiles['name'][$i];
                $tmpName = $imageFiles['name'][$i];
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $newFileName = uniqid('product_' . preg_replace('/\s+/', '_', strtolower($productName)) . '_') . '.' . $extension;
                $destination = $uploadDir . $newFileName;

                if (move_uploaded_file($tmpFilePath, $destination)) {
                    $uploadedPaths[] = $destination;
                } else {
                    // Erro ao mover o arquivo
                    SonoLogger::log( 'Erro ao mover o arquivo: ' . $originalName . '<br>0');
                    return null; // Ou você pode optar por continuar e logar os erros
                }
            } elseif ($imageFiles['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                // Outro erro de upload (além de nenhum arquivo enviado)
                SonoLogger::log( 'Erro no upload do arquivo ' . $imageFiles['name'][$i] . ': ' . $imageFiles['error'][$i] . '<br>');
                return null; // Ou você pode optar por continuar e logar os erros
            }
            // Se UPLOAD_ERR_NO_FILE, o usuário não enviou este arquivo, então ignoramos.
        }

        return $uploadedPaths;
    }
}