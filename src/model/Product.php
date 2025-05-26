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
    public string $image; // Assume que esta é a imagem principal (primeira da imageList)
    public bool $isStar;
    public array $imageList;
    public ?string $thumbnail; // Renomeado para 'thumbnail' (mais comum em inglês)

    public function __construct(
        int $id,
        string $name,
        string $description,
        float $price,
        string $type,
        string $image,
        bool $isStar,
        array $imageList,
        ?string $thumbnail // Renomeado para 'thumbnail'
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->type = $type;
        $this->isStar = $isStar;
        $this->image = $image;
        $this->imageList = $imageList;
        $this->thumbnail = $thumbnail; // Atribuído ao atributo
    }

    public static function fromPost(): ?Product {
        SonoLogger::log("Dados POST recebidos: " . print_r($_POST, true));
        SonoLogger::log("Dados FILES recebidos: " . print_r($_FILES, true));

        // Validação básica dos campos de texto E dos arquivos
        if (
            !isset($_POST['name']) ||
            !isset($_POST['description']) ||
            !isset($_POST['price']) ||
            !isset($_POST['type']) ||
            // Para 'image', esperamos um array de arquivos ou um arquivo único
            !isset($_FILES['image']) || !is_array($_FILES['image']['name']) ||
            // Para 'thumbnail', esperamos um único arquivo
            !isset($_FILES['thumbnail'])
        ) {
            SonoLogger::log('Dados POST ou FILEs incompletos. Faltam campos obrigatórios.');
            return null; // Dados incompletos
        }

        // Sanitização e conversão dos dados de texto
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT) ?? 0; // Se 'id' não vier, assume 0
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

        // O checkbox 'isStar' pode não ser enviado se não for marcado
        $isStar = isset($_POST['isStar']) && filter_var($_POST['isStar'], FILTER_VALIDATE_BOOLEAN);

        // --- Processamento do Upload da Miniatura (Thumbnail) ---
        $thumbnailPath = null;
        // Verifica se o arquivo de thumbnail foi enviado e sem erros
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            // A função uploadImageFile é nova e serve para um único arquivo
            
            SonoLogger::log("Fazendo upload do thumbnail");
            $thumbnailPath = self::uploadImageFile($_FILES['thumbnail'], $name . '_thumbnail');
            if ($thumbnailPath === null) {
                SonoLogger::log('Erro ao fazer upload da imagem de thumbnail.');
                return null; // Falha no upload da miniatura
            }
        } else if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
             // Houve um erro no upload do thumbnail (ex: tamanho, tipo inválido)
            SonoLogger::log('Erro no upload do thumbnail: Código ' . $_FILES['thumbnail']['error']);
            return null;
        }

        // --- Processamento do Upload das Imagens Principais e Adicionais ---
        $imagePaths = self::uploadMultipleImages($_FILES['image'], $name);
        if ($imagePaths === null || empty($imagePaths)) {
            SonoLogger::log('Erro no upload de uma ou mais imagens principais/adicionais.');
            // Se nenhuma imagem for essencial, você pode remover o 'return null' daqui
            return null;
        }

        $mainImage = $imagePaths[0] ?? ''; // A primeira imagem da lista é a principal
        $allImagePaths = implode(';', $imagePaths); // Para persistir no banco de dados, por exemplo

        // Validação final dos dados
        if ($name === null || $description === null || $price === false || $type === null || empty($mainImage)) {
            SonoLogger::log('Falha na validação/conversão final dos dados do produto.');
            return null;
        }

        // Se o id não for válido (ex: 0 se não for um ID de edição), o construtor já o trata.
        return new Product(
            $id,
            $name,
            $description,
            $price,
            $type,
            $mainImage, // Passa a imagem principal
            $isStar,
            $imagePaths, // Passa o array de caminhos de imagens
            $thumbnailPath // Passa o caminho da miniatura
        );
    }

    public static function editFromPost(): ?Product {
        SonoLogger::log("Dados POST recebidos para edição: " . var_export($_POST, true));
        SonoLogger::log("Dados FILES recebidos para edição: " . var_export($_FILES, true));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            SonoLogger::log('Método de requisição inválido para edição. Esperado POST.');
            return null;
        }

        // Recuperar dados existentes do produto se necessário (para manter imagens antigas se não forem enviadas novas)
        // Isso geralmente viria de um banco de dados com base no ID.
        // Por simplificação, vamos assumir que você está lidando com isso no controlador.

        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

        $isStar = isset($_POST['isStar']) && filter_var($_POST['isStar'], FILTER_VALIDATE_BOOLEAN);

        // Inicialize com valores vazios ou default
        $mainImage = '';
        $imagePaths = [];
        $thumbnailPath = null;

        SonoLogger::log("Dados THUMBNAIL");
        // Lógica para upload da miniatura (thumbnail) na edição
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            
            SonoLogger::log("Dados THUMBNAIL is set");
            $thumbnailPath = self::uploadImageFile($_FILES['thumbnail'], $name . '_thumbnail');
            SonoLogger::log("THUMBNAIL: ".$thumbnailPath);
            if ($thumbnailPath === null) {
                SonoLogger::log('Erro ao fazer upload da imagem de thumbnail para edição.');
                // Decide se deve retornar null ou continuar com o thumbnail antigo
                // Se o thumbnail é opcional na edição, você pode simplesmente não sobrescrever $thumbnailPath.
            }
        } else if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
            SonoLogger::log('Erro no upload do thumbnail para edição: Código ' . $_FILES['thumbnail']['error']);
            // Decidir se isso é um erro fatal ou se continua com o thumbnail existente
        }
        // Se UPLOAD_ERR_NO_FILE para thumbnail, significa que não foi enviada uma nova,
        // então você precisaria carregar o caminho antigo do banco de dados para $thumbnailPath.

        // Lógica para upload das imagens principais/adicionais na edição
        if (isset($_FILES['image']) && is_array($_FILES['image']['name'])) {
            $newImagePaths = self::uploadMultipleImages($_FILES['image'], $name);
            if ($newImagePaths === null) {
                SonoLogger::log('Erro no upload de novas imagens para edição.');
                // Decide se deve retornar null ou continuar com as imagens antigas
            } else {
                // Se novas imagens foram enviadas, você pode decidir se substitui todas
                // as antigas ou as adiciona à lista.
                // Aqui, estou assumindo que novas imagens substituem as antigas para a lista principal.
                $imagePaths = $newImagePaths;
                $mainImage = $imagePaths[0] ?? '';
            }
        }
        // Se UPLOAD_ERR_NO_FILE para 'image', significa que não foram enviadas novas imagens,
        // então você precisaria carregar os caminhos antigos do banco de dados para $imagePaths e $mainImage.S

        // Cria a instância do produto. A lista de imagens e o thumbnail devem ser preenchidos
        // com os novos caminhos OU com os caminhos existentes se não foram enviados novos.
        return new Product(
            $id,
            $name,
            $description,
            $price,
            $type,
            $mainImage, // Imagem principal (nova ou antiga)
            $isStar,
            $imagePaths, // Lista de imagens (novas ou antigas)
            $thumbnailPath // Thumbnail (novo ou antigo)
        );
    }

    /**
     * Lida com o upload de um único arquivo de imagem.
     * @param array $fileData Array de $_FILES['nome_do_campo'] para um único arquivo.
     * @param string $prefix Prefixo para o nome do arquivo.
     * @return string|null O caminho do arquivo salvo ou null em caso de erro.
     */
    private static function uploadImageFile(array $fileData, string $prefix): ?string {
        $uploadDir = 'uploads/'; // Defina seu diretório de uploads
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Cria o diretório se não existir
        }

        if ($fileData['error'] === UPLOAD_ERR_OK) {
            $tmpFilePath = $fileData['tmp_name'];
            $originalName = $fileData['name'];
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            // Gera um nome único para evitar colisões
            $newFileName = uniqid(preg_replace('/\s+/', '_', strtolower($prefix)) . '_') . '.' . $extension;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpFilePath, $destination)) {
                return $destination; // Retorna o caminho do arquivo salvo
            } else {
                SonoLogger::log('Erro ao mover o arquivo: ' . $originalName);
                return null;
            }
        } elseif ($fileData['error'] !== UPLOAD_ERR_NO_FILE) {
            SonoLogger::log('Erro no upload do arquivo: Código ' . $fileData['error']);
        }
        return null;
    }

    /**
     * Lida com o upload de múltiplos arquivos de imagem.
     * @param array $imageFiles Array de $_FILES['nome_do_campo_multiplo'] para múltiplos arquivos.
     * @param string $productName Nome do produto para usar no prefixo do nome do arquivo.
     * @return array|null Um array de caminhos dos arquivos salvos ou null em caso de erro.
     */
    private static function uploadMultipleImages(array $imageFiles, string $productName): ?array {
        $uploadedPaths = [];
        $uploadDir = 'uploads/'; // Defina seu diretório de uploads
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $numFiles = count($imageFiles['name']);
        for ($i = 0; $i < $numFiles; $i++) {
            // Verifica se o arquivo foi enviado e não houve erro
            if ($imageFiles['error'][$i] === UPLOAD_ERR_OK) {
                $tmpFilePath = $imageFiles['tmp_name'][$i];
                $originalName = $imageFiles['name'][$i];
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $newFileName = uniqid('product_' . preg_replace('/\s+/', '_', strtolower($productName)) . '_') . '.' . $extension;
                $destination = $uploadDir . $newFileName;

                if (move_uploaded_file($tmpFilePath, $destination)) {
                    $uploadedPaths[] = $destination;
                } else {
                    SonoLogger::log('Erro ao mover o arquivo: ' . $originalName);
                    return null; // Retorna null se um único arquivo falhar
                }
            } elseif ($imageFiles['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                // Outro erro de upload (além de nenhum arquivo enviado)
                SonoLogger::log('Erro no upload do arquivo : Código ' . $imageFiles['error'][$i]);
                return null;
            }
            // Se UPLOAD_ERR_NO_FILE, o usuário não enviou este arquivo, então ignoramos.
        }

        return $uploadedPaths;
    }
}