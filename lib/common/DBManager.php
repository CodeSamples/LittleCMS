<?php

class DBManager {
    private $resource;
    private $result;
    const OBJECT='OBJECT';
    const A_ARRAY='ARRAY';
    
    public function getValue($query, $result_type=DBManager::OBJECT){
        $rs = mysqli_query($this->resource, $query);
        $result = false;
        if($rs) {
            $funcName = ''; 
            switch ($result_type) {
                case DBManager::A_ARRAY:
                    $funcName = 'mysqli_fetch_array';
                    break;
                case DBManager::OBJECT:
                default:
                    $funcName = 'mysqli_fetch_object';
                    break;
            }    
            $result = $funcName($rs);
        }
        return $result;
    }
    
    public function query($query){
        $this->result = mysqli_query($this->resource, $query); 
        return $this->result;
    }
    
    public function lastID(){
        
        return mysqli_insert_id($this->resource);
    }
    
    public function next($result, $result_type=DBManager::OBJECT){
        switch ($result_type){
            case DBManager::A_ARRAY:
                $result = mysqli_fetch_array($this->result);
                break;        
            case DBManager::OBJECT:
            default:
                $result = mysqli_fetch_object($this->result);
                break;            
        }
        return $result;
    }
    
    public function getLink(){
        return $this->resource;
    }

    private function connect(){
       $this->resource = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
       
       if (mysqli_connect_errno()) {
           throw new DBException("Error de conexión a la Base de Datos.");
       }

       mysqli_set_charset($this->resource, 'utf8');
    }
    
    public function __construct() {
        $this->connect();
    }
    
    public function __destruct() {
        mysqli_close($this->resource);
    }
}
?>
