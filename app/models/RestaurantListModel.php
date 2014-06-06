<?php

	class RestaurantListModel extends Model {

		const QUERY_LIST_ADMIN = "SELECT p.id, p.name 
			FROM %s AS p 
			LEFT JOIN %s AS pd ON pd.properties_id = p.id
			WHERE p.dashboard = 1
			AND pd.franchise_parent = 0
			ORDER BY p.name ASC";
		const QUERY_LIST_PENDING_ADMIN = "SELECT p.id, p.name 
			FROM %s AS p 
			LEFT JOIN %s AS pd ON pd.properties_id = p.id
			WHERE p.dashboard = 1
			AND pd.pending = 1
			ORDER BY p.name ASC";
		const QUERY_LIST_MANAGER = "SELECT DISTINCT p.id, p.name
				FROM %s AS u1
				LEFT JOIN %s AS u2 ON u2.user = u1.userID
				LEFT JOIN %s AS p ON p.id IN (u2.propiedad, u1.property_id)
				LEFT JOIN %s AS pd ON pd.properties_id = p.id
				WHERE u1.userID = %d
				AND p.dashboard = 1
				AND pd.franchise_parent = 0
				ORDER BY p.name ASC";
		const QUERY_LIST_ALL = "SELECT p.id, p.name, p.dashboard, 
			COALESCE(pd.package_id, 0) AS package_id, p.foodType
			FROM %s AS p
			LEFT JOIN %s AS pd ON pd.properties_id = p.id
			WHERE pd.franchise_parent = 0
			GROUP BY p.id
			ORDER BY p.name ASC";
                const QUERY_FOOD_TYPES = "SELECT DISTINCT p.foodType
			FROM %s AS p
			ORDER BY p.foodType ASC";
		const PROPERTIES_TABLE_NAME = 'properties';
		const PROPERTIES_TABLE_EXTENSION_NAME = 'properties_dashboard';
		const USERS_TABLE_NAME = 'plc_users';
		const USERS_LINK_TABLE_NAME = 'users_properties';
		
		private $restaurantList = array();

		public function getRestaurantList() { return $this->restaurantList; }

		public function fetchRestaurantList() {
			$db = new DBManager();
			if($_SESSION['userObject']->getDashboardRole() > ROLE_ADMIN) {
				$query = sprintf(
					self::QUERY_LIST_MANAGER,
					self::USERS_TABLE_NAME,
					self::USERS_LINK_TABLE_NAME,
					self::PROPERTIES_TABLE_NAME,
					self::PROPERTIES_TABLE_EXTENSION_NAME,
					$_SESSION['userObject']->getId()
					);
			} else {
				$query = sprintf(
					self::QUERY_LIST_ADMIN, 
					self::PROPERTIES_TABLE_NAME, 
					self::PROPERTIES_TABLE_EXTENSION_NAME
					);
			}

			$result = $db->query($query);
			if($result) {
				$this->restaurantList = array();
				while($single = $db->next($result)) {
					$restaurantBase = new RestaurantBaseModel();
					$restaurantBase->setId($single->id);
					$restaurantBase->setName($single->name);
					$this->restaurantList[] = $restaurantBase;
				}
			}
		}

		public function fetchPendingRestaurantList() {
			$db = new DBManager();
			$query = sprintf(
					self::QUERY_LIST_PENDING_ADMIN, 
					self::PROPERTIES_TABLE_NAME,
					self::PROPERTIES_TABLE_EXTENSION_NAME);
			
			$result = $db->query($query);
			if($result) {
				$this->restaurantList = array();
				while($single = $db->next($result)) {
					$restaurantBase = new RestaurantBaseModel();
					$restaurantBase->setId($single->id);
					$restaurantBase->setName($single->name);
					$this->restaurantList[] = $restaurantBase;
				}
			}
		}

		public function fetchAllRestaurantList() {
			$db = new DBManager();
			$query = sprintf(
					self::QUERY_LIST_ALL, 
					self::PROPERTIES_TABLE_NAME,
					self::PROPERTIES_TABLE_EXTENSION_NAME);
			
			$result = $db->query($query);
			if($result) {
				$this->restaurantList = array();
				while($single = $db->next($result)) {
					$restaurantDash = new RestaurantDashModel();
					$restaurantDash->setId($single->id);
					$restaurantDash->setName($single->name);
					$restaurantDash->setDashboard($single->dashboard);
					$restaurantDash->setPackage_id($single->package_id);
                                        $restaurantDash->setFoodType($single->foodType);
					$this->restaurantList[] = $restaurantDash;
				}
			}
		}
                
                public function getFoodTypes(){
                        $db = new DBManager();
                        $foodTypes = array();
                        
			$query = sprintf(
					self::QUERY_FOOD_TYPES, 
					self::PROPERTIES_TABLE_NAME);
			
			$result = $db->query($query);
			if($result) {				
				while($single = $db->next($result)) {
					$foodTypes[] = $single->foodType;
				}
			}
                        
                        return $foodTypes;
                }

	}

?>