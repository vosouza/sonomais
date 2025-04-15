<?php


namespace Vosouza\Sonomais\data;

use PDO;
use PDOException;

class DataSource implements DataSourceInterface{

    private String $host = '127.0.0.1:3306';
    private String $dbname = 'u665653267_sonomais';
    private String $user = 'u665653267_cpudvini';
    private String $pass = 'Barista#281298';
    private PDO $pdoConection;
    
    public function __construct(){
        try {
            
           $this->pdoConection = new PDO(
            'mysql:host='.$this->host.';dbname='.$this->dbname.';charset=utf8',
            $this->user,
            $this->pass);
            
            // Opcional: configura o modo de erro para lançar exceções
            $this->pdoConection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            echo "Conexão bem-sucedida!";
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }

    public function getConection() : PDO{
        return $this->pdoConection;
    }
}