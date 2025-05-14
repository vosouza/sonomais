<?php

namespace Vosouza\Sonomais\model;
use Vosouza\Sonomais\{
    SonoLogger,
};
use Imagick; // Certifique-se de que a classe Imagick esteja disponível

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
            if (!mkdir($uploadDir, 0777, true)) {
                SonoLogger::log( 'Erro ao criar o diretório de uploads: ' . $uploadDir);
                return null;
            }
        }

        $numFiles = count($imageFiles['name']);
        for ($i = 0; $i < $numFiles; $i++) {
            if ($imageFiles['error'][$i] === UPLOAD_ERR_OK) {
                $tmpFilePath = $imageFiles['tmp_name'][$i];
                $originalName = $imageFiles['name'][$i];
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $newFileName = uniqid('product_' . preg_replace('/\s+/', '_', strtolower($productName)) . '_') . '.' . $extension;
                $destination = $uploadDir . $newFileName;

                if (extension_loaded('imagick')) {
                    if (Product::compressImageImagick($tmpFilePath, $destination, 70)) {
                        $uploadedPaths[] = $destination;
                    } else {
                        SonoLogger::log( 'Erro ao comprimir imagem com Imagick: ' . $originalName . '. Salvando original.');
                        if (move_uploaded_file($tmpFilePath, $destination)) {
                            $uploadedPaths[] = $destination;
                        } else {
                            SonoLogger::log( 'Erro ao mover o arquivo original (após falha na compressão): ' . $originalName);
                            return null;
                        }
                    }
                } else {
                    if (move_uploaded_file($tmpFilePath, $destination)) {
                        $uploadedPaths[] = $destination;
                    } else {
                        SonoLogger::log( 'Erro ao mover o arquivo (sem Imagick): ' . $originalName);
                        return null;
                    }
                }

            } elseif ($imageFiles['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                // Outro erro de upload (além de nenhum arquivo enviado)
                SonoLogger::log( 'Erro no upload do arquivo ' . $imageFiles['name'][$i] . ': ' . $imageFiles['error'][$i]);
                return null; // Ou você pode optar por continuar e logar os erros
            }
            // Se UPLOAD_ERR_NO_FILE, o usuário não enviou este arquivo, então ignoramos.
        }

        return $uploadedPaths;
    }

    private static function compressImageImagick(string $source, string $destination, int $quality): bool{
        try {
            $image = new Imagick($source);

            // Remover metadados (opcional, mas ajuda a reduzir o tamanho)
            $image->stripImage();

            // Definir a qualidade da compressão (para JPEG e outros formatos com suporte)
            $image->setImageCompressionQuality($quality);

            // Otimizar a imagem
            $image->optimizeImageLayers();
            $image->setImageOptimized(true);

            // Salvar a imagem no destino
            $image->writeImage($destination);

            $image->destroy();
            return true;

        } catch (\ImagickException $e) {
            SonoLogger::log( 'Erro ao processar imagem com Imagick: ' . $e->getMessage() . ' (Arquivo: ' . $source . ')');
            return false;
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