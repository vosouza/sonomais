<?php


namespace Vosouza\Sonomais\data;

use PDO;
use PDOException;
use Vosouza\Sonomais\{
    SonoLogger,
};

class ProductionDataSource implements DataSourceInterface{

    private String $host = '127.0.0.1:3306';
    private String $dbname = 'u665653267_sonomais_prod';
    private String $user = 'u665653267_cpudvini_prod';
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
        
        } catch (PDOException $e) {
            SonoLogger::log( "Erro na conexão: " . $e->getMessage());
        }
    }

    public function getConection() : PDO{
        return $this->pdoConection;
    }
}