<?php 

    class RestaurantDashModel extends RestaurantBaseModel {

        const QUERY_SAVE = "UPDATE %s SET %s WHERE %s";
        const QUERY_EXTENSION_EXISTS = "SELECT COUNT(id) AS extension 
            FROM %s 
            WHERE properties_id = %d";
        const QUERY_EXTENSION_INSERT = "INSERT INTO %s (%s) VALUES (%s)";
        const QUERY_PACKAGE_NAME = "SELECT name FROM %s WHERE ID = %d";
        const QUERY_SAL_ID = "SELECT id_sal FROM %s WHERE id = %d";
        const QUERY_FETCH_BY_ID = "SELECT p.id_sal, p.dashboard, p.foodType, pd.package_id
            FROM %s AS p
            LEFT JOIN %s AS pd ON pd.properties_id = p.id
            WHERE p.id = %d";
        const TABLE_NAME = "properties";
        const TABLE_EXTENSION_NAME = "properties_dashboard";
        const TABLE_PACKAGES_NAME = "dashboard_packages";

        public $dashboard;
        public $package_id;
        public $foodType;
        public $sal_id;

        public function setDashboard($dashboard) { $this->dashboard = $dashboard; }
        public function getDashboard() { return $this->dashboard; }
        
        public function setPackage_id($package_id) { $this->package_id = $package_id; }
        public function getPackage_id() { return $this->package_id; }
        
        public function getFoodType() { return $this->foodType; }
        public function setFoodType($foodType) { $this->foodType = $foodType; }

        public function getSal_id() { return $this->sal_id; }
        public function setSal_id($sal_id) { $this->sal_id = $sal_id; }
 
        public function save($sync = true) {
            if(null == $this->getId()) {
                return false;
            }

            if(!$this->extensionExists() && !$this->createExtension()) {
                return false;
            }

            $db = new DBManager();
            $query = sprintf(
                self::QUERY_SAVE,
                self::TABLE_NAME,
                'dashboard=' . intval($this->getDashboard()),
                'id=' . intval($this->getId())
                );

            if(!$db->query($query)) {
                return false;
            }

            $query_extension = sprintf(
                self::QUERY_SAVE,
                self::TABLE_EXTENSION_NAME,
                'package_id=' . intval($this->getPackage_id()),
                'properties_id=' . intval($this->getId())
                );

            if(!$db->query($query_extension)) {
                return false;
            }

            if($sync) {
                $this->syncToSal();
            }

            return true;
        }

        public function extensionExists() {
            $db = new DBManager();
            $query = sprintf(self::QUERY_EXTENSION_EXISTS, self::TABLE_EXTENSION_NAME, $this->getId());
            $result = $db->getValue($query);
            return intval($result->extension);
        }

        public function createExtension() {
            $db = new DBManager();
            $query = sprintf(self::QUERY_EXTENSION_INSERT,
                self::TABLE_EXTENSION_NAME,
                'properties_id',
                intval($this->getId())
                );
            return $db->query($query);
        }

        public function fetchById($id) {
            $db = new DBManager();
            $query = sprintf(self::QUERY_FETCH_BY_ID,
                self::TABLE_NAME,
                self::TABLE_EXTENSION_NAME,
                intval($id));

            $result = $db->getValue($query);
            if($result) {
                $this->setDashboard($result->dashboard);
                $this->setPackage_id($result->package_id);
                $this->setFoodType($result->foodType);
                $this->setSal_id($result->id_sal);
            }
        }

        public function syncToSal() {
            if(!isset($this->sal_id)) {
                $db = new DBManager();
                $query = sprintf(self::QUERY_SAL_ID, self::TABLE_NAME, $this->getId());
                $result = $db->getValue($query);
                if($result) {
                    $this->setSal_id($result->id_sal);
                } else {
                    return false;
                }
            }

            $curlData = array(
                'restaurant_id' => $this->getSal_id(),
                'package' => array(
                    'id' => $this->getPackage_id(),
                    'name' => $this->getPackageNane()
                    ),
                'api_key' => SAL_API_KEY
                );
            $salResult = $this->curlWrapper(
                SAL_API_URL . 'restaurants/syncDashboardPackage/', 'POST', $curlData);


            $json = json_decode(trim($salResult));
            if(!is_object($json) || $json->status != 'ok') {
                return false;
            }

            return true;
        }

        protected function getPackageNane() {
            $db = new DBManager();
            $query = sprintf(
                self::QUERY_PACKAGE_NAME, 
                self::TABLE_PACKAGES_NAME, 
                $this->getPackage_id()
                );
            $result = $db->getValue($query);
            if($result) {
                $result = $result->name;
            }
            return $result;
        }

    }

?>