<?php
$host = '127.0.0.1:3306'; // ou o IP do servidor
$dbname = 'u665653267_sonomais';
$user = 'u665653267_cpudvini';
$pass = 'Barista#281298';


if (!array_key_exists('PATH_INFO', $_SERVER) || $_SERVER['PATH_INFO'] === '/') {
    $controller = new VideoListController($videoRepository);
}

// try {
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
//     // Opcional: configura o modo de erro para lançar exceções
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//     echo "Conexão bem-sucedida!";
// } catch (PDOException $e) {
//     echo "Erro na conexão: " . $e->getMessage();
// }
?>