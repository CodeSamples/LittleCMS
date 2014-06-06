<?php

class UserModel extends Model {

    private $tableName = 'plc_users';
    private $tableMapName = 'users_properties';
    private $id;
    private $username;
    private $password;
    private $reservaRole;
    private $dashboardRole;
    private $email;
    private $realname;
    private $manager;

    public function getTableName() {
        return $this->tableName;
    }

    public function getTableMapName() {
        return $this->tableMapName;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getReservaRole() {
        return $this->reservaRole;
    }

    public function setReservaRole($reserva_role) {
        $this->reservaRole = $reserva_role;
    }

    public function getDashboardRole() {
        return $this->dashboardRole;
    }

    public function setDashboardRole($dashboard_role) {
        $this->dashboardRole = $dashboard_role;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getRealname() {
        return $this->realname;
    }

    public function setRealname($realname) {
        $this->realname = $realname;
    }
    
    public function getManager() {
        return $this->manager;
    }

    public function setManager($manager) {
        $this->manager = $manager;
    }
    
    public function exists() {
        $DB = new DBManager();
        $query = sprintf(
                "SELECT username
                  FROM " . $this->tableName . "
                  WHERE username='%s' LIMIT 1;", mysqli_escape_string($DB->getLink(), $this->username)
        );
        $obj = $DB->getValue($query);
        return ($obj != null);
    }

    public function existsById() {
        $db = new DBManager();
        $query = sprintf(
            "SELECT COUNT(u.userID) as present FROM %s AS u WHERE u.userID = %d;",
            $this->tableName,
            intval($this->getId())
            );
        $result = $db->getValue($query);
        if(!isset($result) || !is_object($result)) {
            return false;
        }
        return $result->present;
    }

    public function usernameUsed() {
        $db = new DBManager();
        $query = sprintf(
            "SELECT COUNT(u.userID) as used 
            FROM %s AS u 
            WHERE u.username = '%s'
            AND u.userID <> %d;",
            $this->tableName,
            mysqli_escape_string($db->getLink(), $this->getUsername()),
            intval($this->getId())
            );
        $result = $db->getValue($query);
        if(!isset($result) || !is_object($result)) {
            return false;
        }
        return $result->used;
    }

    public function delete() {
        $DB = new DBManager();
        $query = sprintf(
                "DELETE 
                  FROM " . $this->tableName . "
                  WHERE username='%s' LIMIT 1;", mysqli_escape_string($DB->getLink(), $this->username)
        );
        $rs = $DB->query($query);

        return $rs;
    }

    public function save() {
        $DB = new DBManager();

        $result = false;
        if ($this->exists()) {

            if (trim($this->password) == '') {
                $query = sprintf(
                        "UPDATE " . $this->tableName . "
                    SET realname='%s', email='%s', dashboard_role=%d
                    WHERE username='%s' LIMIT 1;
              ", mysqli_escape_string($DB->getLink(), $this->realname), mysqli_escape_string($DB->getLink(), $this->email), $this->dashboardRole, mysqli_escape_string($DB->getLink(), $this->username)
                );
            } else {
                $query = sprintf(
                        "UPDATE " . $this->tableName . "
                    SET pass='%s', realname='%s', email='%s', dashboard_role=%d
                    WHERE username='%s' LIMIT 1;
              ", mysqli_escape_string($DB->getLink(), $this->password), mysqli_escape_string($DB->getLink(), $this->realname), mysqli_escape_string($DB->getLink(), $this->email), $this->dashboardRole, mysqli_escape_string($DB->getLink(), $this->username)
                );
            }
            $result = $DB->query($query);
        } else {
            $query = sprintf(
                    "INSERT INTO " . $this->tableName . "
                    (username,pass,role,realname,email,dashboard_role)
                    VALUES ('%s','%s',-1,'%s','%s',%d);
              ", mysqli_escape_string($DB->getLink(), $this->username), mysqli_escape_string($DB->getLink(), $this->password), mysqli_escape_string($DB->getLink(), $this->realname), mysqli_escape_string($DB->getLink(), $this->email), $this->dashboardRole
            );
            $result = $DB->query($query);
        }

        return $result;
    }

    public function countAll() {
        $DB = new DBManager();
        $query = sprintf(
                "SELECT count(username) as count
                  FROM " . $this->tableName
        );
        $obj = $DB->getValue($query);

        return intval($obj->count);
    }

    public function getAll($from = 0, $limit = 10, $sort = '') {
        $DB = new DBManager();
        $str_sort = '';
        if ($sort != '') {
            $sort_by = explode(' ', $sort);
            $sort = mysqli_escape_string($DB->getLink(), $sort_by[0]);
            $order = mysqli_escape_string($DB->getLink(), $sort_by[1]);
            $str_sort = 'ORDER BY ' . $sort . ' ' . $order;
        }
        $query = sprintf(
                "SELECT u.userID, u.username, u.pass, u.realname, u.email, u.dashboard_role,      
                        CONCAT_WS(',', GROUP_CONCAT(DISTINCT p.id),u.property_id) AS manager_ids,
                        CONCAT_WS(',', GROUP_CONCAT(DISTINCT p.name), p1.name) AS manager_names                  
                FROM " . $this->tableName . " AS u
                LEFT JOIN users_properties AS up ON up.user = u.userID
                LEFT JOIN properties AS p ON p.id = up.propiedad
                LEFT JOIN properties AS p1 ON p1.id = u.property_id
                GROUP BY u.userID " . $str_sort . " 
                  LIMIT %d,%d;", $from, $limit
        );
        $result = $DB->query($query);
        $list = array();
        while ($user = $DB->next($result)) {
            $u = new UserModel();
            $u->setId($user->userID);
            $u->setEmail($user->email);
            $u->setUsername($user->username);
            $u->setPassword($user->pass);
            $u->setRealname($user->realname);
            $u->setDashboardRole($user->dashboard_role);
            $managers_data = array();
            $ids = explode(',',$user->manager_ids);
            $names = explode(',',$user->manager_names);
            $i=0;
            foreach ($ids as $property_id){
                if($names[$i]!=''){  
                    $managers_data[$property_id] = $names[$i];
                }
                $i++;
            }
            $u->setManager($managers_data);
            $list[] = $u;
        }
        return $list;
    }

    public function validate($username, $password) {
        $DB = new DBManager();
        $query = sprintf(
                "SELECT userID, username, realname, email, dashboard_role
                  FROM " . $this->tableName . "
                  WHERE username='%s' AND pass='%s' LIMIT 1;", mysqli_escape_string($DB->getLink(), $username), mysqli_escape_string($DB->getLink(), $password)
        );

        $obj = $DB->getValue($query);

        if ($obj != null) {
            //El usuario fue registrado en sal reserva y no tiene acceso al dashboard
            if ($obj->dashboard_role == null) {
                return false;
            } else {
                $this->id = $obj->userID;
                $this->username = $obj->username;
                $this->dashboardRole = $obj->dashboard_role;
                $this->realname = $obj->realname;
                $this->email = $obj->email;

                return true;
            }
        }

        return false;
    }
    
    public function autologin($userID) {
        $DB = new DBManager();
        $query = sprintf(
                "SELECT userID, username, realname, email, dashboard_role
                  FROM " . $this->tableName . "
                  WHERE userID=%d LIMIT 1;", $userID
        );
        $obj = $DB->getValue($query);

        if ($obj != null) {
            //El usuario fue registrado en sal reserva y no tiene acceso al dashboard
            if ($obj->dashboard_role == null) {
                return false;
            } else {
                $this->id = $obj->userID;
                $this->username = $obj->username;
                $this->dashboardRole = $obj->dashboard_role;
                $this->realname = $obj->realname;
                $this->email = $obj->email;

                return true;
            }
        }

        return false;
    }

    public function validateRestaurantAccess($restaurant_id) {
        $db = new DBManager();
        $query = "SELECT 
            CONCAT_WS(
                ',',
                GROUP_CONCAT(DISTINCT u.userID), 
                GROUP_CONCAT(DISTINCT up.user)
            ) AS users
            FROM %s AS u
            LEFT JOIN %s AS up 
                ON up.propiedad = u.property_id
            WHERE u.property_id = %d";
        $result = $db->getValue(sprintf(
            $query, $this->tableName, $this->tableMapName, intval($restaurant_id)));

        if($result) {
            $result = in_array($this->id, array_unique(explode(',', $result->users)));
        }
        return $result;
    }
    
    public function updateManagerRelations($userID, $restaurants){
        $db = new DBManager();
        
        $db->query(sprintf("DELETE FROM users_properties WHERE user = %d",$userID));
        
        $first=0;
        if(count($restaurants)>0){
            $first = array_shift($restaurants);
        }
        
        $db->query(sprintf("UPDATE plc_users SET property_id = %d WHERE userID = %d",$first,$userID));
        
        if(count($restaurants)>0){
           $values = array();   
        
           foreach($restaurants as $restID){
               $values[]='('.$userID.','.$restID.')';
           }
           
           $db->query("INSERT INTO users_properties (user,propiedad) VALUES ".implode(',',$values));             
        }
    }

}

?>
