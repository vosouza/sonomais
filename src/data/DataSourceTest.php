<?php


namespace Vosouza\Sonomais\data;

use PDO;
use PDOException;

class DataSourceTest implements DataSourceInterface{

    private String $host = '127.0.0.1:3306';
    private String $dbname = 'u665653267_sonomais';
    private String $user = 'root';
    private String $pass = 'admin';
    private PDO $pdoConection;
    
    public function __construct(){
        try {
            
           $this->pdoConection = new PDO(
            'mysql:host='.$this->host.';dbname='.$this->dbname.';charset=utf8',
            $this->user,
            $this->pass);
            
            $this->pdoConection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }

    public function getConection() : PDO{
        return $this->pdoConection;
    }
}