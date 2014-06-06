<?php

class PackageModel extends Model{
    const QUERY_PACKAGE_FEATURES = "SELECT df.ID, df.name, dpf.value
      FROM dashboard_features AS df
      LEFT JOIN dashboard_packages_features AS dpf
        ON dpf.featureID = df.ID
      WHERE dpf.packageID = %d";
    const QUERY_PACKAGE_FEATURES_BY_PROPERTY = "SELECT df.name, dpf.value
      FROM dashboard_features AS df
      LEFT JOIN dashboard_packages_features AS dpf
        ON dpf.featureID = df.ID
      LEFT JOIN properties_dashboard AS pd
        ON pd.properties_id = %d
      WHERE dpf.packageID = pd.package_id";

    private $tableName = 'dashboard_packages';
    public $ID=0;
    public $name;
    
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
    
    public function save(){
        $DB = new DBManager();

        if($this->ID==0){
            $query = sprintf("INSERT INTO ".$this->tableName."
                    (name) VALUES ('%s');", 
                    mysqli_escape_string($DB->getLink(), $this->name));
        } else {
            $query = sprintf("UPDATE ".$this->tableName."
                    SET name='%s'
                    WHERE ID=%d LIMIT 1;", 
                    mysqli_escape_string($DB->getLink(), $this->name),
                    $this->ID);
        }
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
    
    public function getAll(){
        $DB = new DBManager();

        $query = sprintf(
             "SELECT p.ID as ID, p.name as name
                  FROM ".$this->tableName." as p;                  
        ");
        $result = $DB->query($query); 
        $list = array();
        
        while($package=$DB->next($result)){     
               $p = new PackageModel();
               $p->setID($package->ID);
               $p->setName($package->name);
               $list[]=$p;
        }       
        return $list;
    }
    
    public function saveFeatures($features){
        $DB = new DBManager();

        $values=array();
        foreach ($features as $feature_key => $feature_value){
            if(trim($feature_value)!=''){
              $values[]=sprintf("(%d,%d,'%s')",
                           $this->ID, $feature_key,
                           mysqli_escape_string($DB->getLink(), $feature_value));
            }
        }
        
  
        $query = sprintf(
             "INSERT INTO dashboard_packages_features 
                 (packageID,featureID,value)
                 VALUES
                 ".  implode(',', $values)."
                 ON DUPLICATE KEY
                 UPDATE value=VALUES(value);    
        ");
        
        $result = $DB->query($query); 
         
        return $result;
    }

    public function getAllFeatures(){
        $DB = new DBManager();

        $query = sprintf(
             "SELECT p.ID as ID, p.name as name, f.featureID as feature_id, f.value as feature_value
                  FROM ".$this->tableName." as p
                  LEFT JOIN  dashboard_packages_features f ON f.packageID=p.ID;                  
        ");
        $result = $DB->query($query); 
        $list = array();
        
        while($package=$DB->next($result)){     
            if(!isset($list[$package->ID])){
               $p = new PackageModel();
               $p->setID($package->ID);
               $p->setName($package->name);
               $list[$package->ID]["package"]=$p;
            }
            
            $list[$package->ID]["features"][$package->feature_id]=$package->feature_value;               
        }
        return $list;
    }
    
    public function getAllFeaturesApi(){
        $DB = new DBManager();

        $query = sprintf(
             "SELECT p.ID as ID, p.name as name, pf.value as feature_value, f.name as feature_name
                  FROM ".$this->tableName." as p
                  LEFT JOIN  dashboard_packages_features pf ON pf.packageID=p.ID
                  LEFT JOIN  dashboard_features f ON f.ID=pf.featureID;                  
        ");
        $result = $DB->query($query); 
        $list = array();
        
        while($package=$DB->next($result)){    
           if(!isset($list[$package->ID])){
               $obj = new stdClass();
               $obj->name = $package->name;
               $obj->features = array();
               $f = new stdClass();
               $f->name = $package->feature_name;
               $f->value = $package->feature_value;
               $obj->features[]=$f;
               $list[$package->ID] = $obj;
           } else {
               $obj = $list[$package->ID];
               $f = new stdClass();
               $f->name = $package->feature_name;
               $f->value = $package->feature_value;
               $obj->features[]=$f;
               $list[$package->ID] = $obj;
           }           
        }
        return $list;
    }
    
    public function getAllFeaturesByName(){
        $DB = new DBManager();

        $query = sprintf(
             "SELECT p.ID as ID, p.name as name, pf.value as feature_value, f.name as feature_name
                  FROM ".$this->tableName." as p
                  LEFT JOIN  dashboard_packages_features pf ON pf.packageID=p.ID
                  LEFT JOIN  dashboard_features f ON f.ID=pf.featureID;                  
        ");
        $result = $DB->query($query); 
        $list = array();
        
        while($package=$DB->next($result)){    
           if(!isset($list[$package->ID])){
               $obj = new stdClass();
               $obj->name = $package->name;
               $obj->features = array();
               $obj->features[$package->feature_name]=$package->feature_value;
               $list[$package->ID] = $obj;
           } else {
               $obj = $list[$package->ID];
               $obj->features[$package->feature_name]=$package->feature_value;
               $list[$package->ID] = $obj;
           }           
        }
        return $list;
    }


    public function getPackageFeatures() {
      $db = new DBManager();
      $query = sprintf(self::QUERY_PACKAGE_FEATURES, $this->getID());
      $result = $db->query($query);
      $features = array();
      if($result) {
        while($row = $db->next($result)) {
          $features[] = $row;
        }
      }
      return $features;
    }

    public static function getPackageFeaturesByPropertyId($properties_id) {
      $db = new DBManager();
      $query = sprintf(self::QUERY_PACKAGE_FEATURES_BY_PROPERTY, $properties_id);
      $result = $db->query($query);
      $features = array();
      if($result) {
        while($row = $db->next($result)) {
          $features[$row->name] = $row->value;
        }
      }
      return $features;
    }
    
    
}
?>