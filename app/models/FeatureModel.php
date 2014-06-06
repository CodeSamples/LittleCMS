<?php

class FeatureModel extends Model{
    private $tableName = 'dashboard_features';
    public $ID;
    public $name;
    public $type;
    
    public $types=array(
      "int" => array("from" => 0,"to" => 100),
      "text" => "textValue",
      "options" => array("option1","option2"),
      "boolean" => 0  
    );
    
    public function getID() {
        return $this->ID;
    }

    public function setID($ID) {
        $this->ID = $ID;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }
    
    public function getAll(){
        $DB = new DBManager();

        $query = sprintf(
             "SELECT ID, name, type
                  FROM ".$this->tableName.";                  
        ");
        $result = $DB->query($query); 
        $list = array();
        
        while($feature=$DB->next($result)){  
               $f = new FeatureModel();
               $f->setID($feature->ID);
               $f->setName($feature->name);
               $f->setType(unserialize($feature->type));
               $list[]=$f;                         
        }
        return $list;
    }
    
    public function saveFeatures($features){
        $DB = new DBManager();

        $values=array();
        foreach ($features as $feature_key => $feature_value){
            $values[]=sprintf("(%d,'%s','%s')",
                           $feature_key,
                           mysqli_escape_string($DB->getLink(), $feature_value["name"]),
                           $feature_value["type"]);            
        }        
  
        $query = sprintf(
             "INSERT INTO ".$this->tableName."
                 (ID,name,type)
                 VALUES
                 ".implode(',', $values)."
                 ON DUPLICATE KEY
                 UPDATE name=VALUES(name), type=VALUES(type);    
        ");
        
        $result = $DB->query($query); 
         
        return $result;
    }
    
    public function delete(){
        $DB = new DBManager();

        $query = sprintf("DELETE FROM ".$this->tableName."
                    WHERE ID=%d LIMIT 1;", 
                    $this->ID);
        
        $result = $DB->query($query); 
         
        return $result;
    }


}    
?>