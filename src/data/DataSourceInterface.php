<?php


namespace Vosouza\Sonomais\data;

use PDO;

interface  DataSourceInterface{
    public function getConection() : PDO;
}