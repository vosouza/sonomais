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

        $image = $_FILES['image'];
        $imageName = $image['name'];
        $imageTmpName = $image['tmp_name'];
        $imageError = $image['error'];

        // Verifique se não houve erros no upload
        if ($imageError === UPLOAD_ERR_OK) {
            // Mova o arquivo temporário para um local permanente
            $destination = __DIR__.'/../../public_html/images/' . $imageName; // Defina o diretório de destino
            move_uploaded_file($imageTmpName, $destination);

            // Armazene o caminho da imagem no objeto Product
            $imagePath = $destination;
        } else {
            echo 'Erro no upload da imagem';
            echo $imageError;
            // 
            return null;
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
}
