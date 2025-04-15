<?php


namespace Vosouza\Sonomais\data;

use PDO;
use PDOException;

class SQLiteDataSource implements DataSourceInterface{
    private PDO $pdoConection;
    
    public function __construct(){
        try {
            
             $this->pdoConection = new PDO('sqlite:meu_banco.db');
            $this->pdoConection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }

    public function getConection(): PDO{
        return $this->pdoConection;
    }
}