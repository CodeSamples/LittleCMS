<?php

	class RestaurantDetailModel extends RestaurantBaseModel {

		const QUERY_SINGLE = "SELECT 
			p.id, 
			p.name, 
			p.content, 
			p.excerpt, 
			p.id_sal, 
			p.street AS address, 
			pd.cityname AS city, 
			p.foodType AS food, 
			p.logo_filename AS logo, 
			pd.lat, 
			pd.lng, 
			pd.tags, 
			pd.keywords, 
			pd.gallery_id, 
			p.local_elements AS elements, 
			p.ambience, 
			p.price, 
			p.timing AS time, 
			p.phone, 
			p.email AS mail, 
			pd.menu_pdf, 
			pd.menu_locu, 
			p.social_fb AS facebook, 
			p.social_tw AS twitter,
			p.dashboard,
			pd.pending,
			COALESCE(pd.package_id, 0) AS package_id,
			COALESCE(pd.franchise, 0) AS franchise,
			COALESCE(pd.franchise_parent, 0) AS franchise_parent
			FROM properties AS p
			LEFT JOIN properties_dashboard AS pd ON pd.properties_id = p.id
			WHERE p.id = %d";
		const QUERY_SINGLE_SAL = "SELECT 
			p.id, 
			p.name, 
			p.content, 
			p.excerpt, 
			p.id_sal, 
			p.street AS address, 
			pd.cityname AS city, 
			p.foodType AS food, 
			p.logo_filename AS logo, 
			pd.lat, 
			pd.lng, 
			pd.tags, 
			pd.keywords, 
			pd.gallery_id, 
			p.local_elements AS elements, 
			p.ambience, 
			p.price, 
			p.timing AS time, 
			p.phone, 
			p.email AS mail, 
			pd.menu_pdf, 
			pd.menu_locu, 
			p.social_fb AS facebook, 
			p.social_tw AS twitter,
			p.dashboard,
			pd.pending,
			COALESCE(pd.package_id, 0) AS package_id,
			COALESCE(pd.franchise, 0) AS franchise,
			COALESCE(pd.franchise_parent, 0) AS franchise_parent
			FROM properties AS p
			LEFT JOIN properties_dashboard AS pd ON pd.properties_id = p.id
			WHERE p.id_sal = %d";
		const QUERY_UPDATE = "UPDATE %s SET %s WHERE id = %d";
		const QUERY_EXTENSION_UPDATE = "UPDATE %s SET %s WHERE properties_id = %d";
		const QUERY_INSERT = "INSERT INTO %s (%s) VALUES (%s)";
		const QUERY_EXISTS = "SELECT COUNT(id) AS count FROM %s WHERE %s";
        const QUERY_EXISTS_SAL_ID = "SELECT id, dashboard, name FROM %s WHERE id_sal=%d LIMIT 1";
        const QUERY_RESERVE_ACTIVE = "SELECT p.status
			FROM %s AS p
			WHERE p.dashboard = 1
			AND p.ID = %d";
		const QUERY_FRANCHISE = "SELECT pd.franchise
			FROM %s AS pd
			WHERE pd.properties_id = %d";
		const TABLE_NAME = 'properties';
		const TABLE_EXTENSION_NAME = 'properties_dashboard';

		public $content;
		public $excerpt;
		public $id_sal;
		public $address;
		public $lat;
		public $lng;
		public $city;
		public $food;
		public $tags;
		public $keywords;
		public $logo;
		public $gallery_id;
		public $elements;
		public $ambience;
		public $price;
		public $time;
		public $phone;
		public $mail;
		public $menu_pdf;
		public $menu_locu;
		public $facebook;
		public $twitter;
		public $pending;
		public $dashboard;
		public $package_id;
		public $franchise;
		public $franchise_parent;

		public function getContent() { return $this->content; }
		public function getExcerpt() { return $this->excerpt; }
		public function getId_sal() { return $this->id_sal; }
		public function getAddress() { return $this->address; }
		public function getLat() { return $this->lat; }
		public function getLng() { return $this->lng; }
		public function getCity() { return $this->city; }
		public function getFood() { return $this->food; }
		public function getTags() { return $this->tags; }
		public function getKeywords() { return $this->keywords; }
		public function getLogo() { return $this->logo; }
		public function getGallery_id() { return $this->gallery_id; }
		public function getElements() { return $this->elements; }
		public function getAmbience() { return $this->ambience; }
		public function getPrice() { return $this->price; }
		public function getTime() { return $this->time; }
		public function getPhone() { return $this->phone; }
		public function getMail() { return $this->mail; }
		public function getMenu_pdf() { return $this->menu_pdf; }
		public function getMenu_locu() { return $this->menu_locu; }
		public function getFacebook() { return $this->facebook; }
		public function getTwitter() { return $this->twitter; }
		public function getPending() { return $this->pending; }
		public function getDashboard() { return $this->dashboard; }
		public function getPackage_id() { return $this->package_id; }
		public function getFranchise() { return $this->franchise; }
		public function getFranchise_parent() { return $this->franchise_parent; }

		public function setContent($content) {$this->content = $content; }
		public function setExcerpt($excerpt) {$this->excerpt = $excerpt; }
		public function setId_sal($id_sal) {$this->id_sal = $id_sal; }
		public function setAddress($address) {$this->address = $address; }
		public function setLat($lat) {$this->lat = $lat; }
		public function setLng($lng) {$this->lng = $lng; }
		public function setCity($city) {$this->city = $city; }
		public function setFood($food) {$this->food = $food; }
		public function setTags($tags) {$this->tags = $tags; }
		public function setKeywords($keywords) {$this->keywords = $keywords; }
		public function setLogo($logo) {$this->logo = $logo; }
		public function setGallery_id($gallery_id) {$this->gallery_id = $gallery_id; }
		public function setElements($elements) {$this->elements = $elements; }
		public function setAmbience($ambience) {$this->ambience = $ambience; }
		public function setPrice($price) {$this->price = $price; }
		public function setTime($time) {$this->time = $time; }
		public function setPhone($phone) {$this->phone = $phone; }
		public function setMail($mail) {$this->mail = $mail; }
		public function setMenu_pdf($menu_pdf) {$this->menu_pdf = $menu_pdf; }
		public function setMenu_locu($menu_locu) {$this->menu_locu = $menu_locu; }
		public function setFacebook($facebook) {$this->facebook = $facebook; }
		public function setTwitter($twitter) {$this->twitter = $twitter; }
		public function setPending($pending) {$this->pending = $pending; }
		public function setDashboard($dashboard) {$this->dashboard = $dashboard; }
		public function setPackage_id($package_id) {$this->package_id = $package_id; }
		public function setFranchise($franchise) {$this->franchise = $franchise; }
		public function setFranchise_parent($franchise_parent) {$this->franchise_parent = $franchise_parent; }


		public function fetchSingle($id, $isSal = false) {
			$db = new DBManager();
			$result = false;
			if($isSal) {
				$result = $db->getValue(sprintf(self::QUERY_SINGLE_SAL, $id));
			} else {
				$result = $db->getValue(sprintf(self::QUERY_SINGLE, $id));	
			}
			
			if($result) {
				$this->setId($result->id);
				$this->setName($result->name);
				$this->setContent($result->content);
				$this->setExcerpt($result->excerpt);
				$this->setId_sal($result->id_sal);
				$this->setAddress($result->address);
				$this->setLat($result->lat);
				$this->setLng($result->lng);
				$this->setCity($result->city);
				$this->setFood($result->food);
				$this->setTags($result->tags);
				$this->setKeywords($result->keywords);
				$this->setLogo($result->logo);
				$this->setGallery_id($result->gallery_id);

				$elements = @json_decode($result->elements);
				if(!isset($elements)) {
					$elements = array();
				}
				$this->setElements($elements);

				$ambience = @json_decode($result->ambience);
				if(!isset($ambience)) {
					$ambience = array();
				}
				$this->setAmbience($ambience);
				$this->setPrice($result->price);
				$this->setTime($result->time);
				$this->setPhone($result->phone);
				$this->setMail($result->mail);
				$this->setMenu_pdf($result->menu_pdf);
				$this->setMenu_locu($result->menu_locu);
				$this->setFacebook($result->facebook);
				$this->setTwitter($result->twitter);
				$this->setPending($result->pending);
				$this->setDashboard($result->dashboard);
				$this->setPackage_id($result->package_id);
				$this->setFranchise($result->franchise);
				$this->setFranchise_parent($result->franchise_parent);
			}
		}

		public function save() {
			$result = false; $insert=false;
			$extension_result = false;
			$db = new DBManager();
			$query = '';
			if($this->exists()) {
				$insertStr = "name = '" . mysqli_escape_string($db->getLink(), $this->name) . "'," .
					"content = '" . mysqli_escape_string($db->getLink(), $this->content) . "'," .
					"excerpt = '" . mysqli_escape_string($db->getLink(), $this->excerpt) . "'," .
					"street = '" . mysqli_escape_string($db->getLink(), $this->address) . "'," .
					"foodType = '" . mysqli_escape_string($db->getLink(), $this->food) . "'," .
					"logo_filename = '" . mysqli_escape_string($db->getLink(), $this->logo) . "'," .
					"local_elements = '" . mysqli_escape_string($db->getLink(), json_encode($this->elements)) . "'," .
					"ambience = '" . mysqli_escape_string($db->getLink(), json_encode($this->ambience)) . "'," .
					"price = '" . mysqli_escape_string($db->getLink(), $this->price) . "'," .
					"timing = '" . mysqli_escape_string($db->getLink(), $this->time) . "'," .
					"phone = '" . mysqli_escape_string($db->getLink(), $this->phone) . "'," .
					"email = '" . mysqli_escape_string($db->getLink(), $this->mail) . "'," .
					"social_fb = '" . mysqli_escape_string($db->getLink(), $this->facebook) . "'," .
                                        "dashboard = '" . mysqli_escape_string($db->getLink(), $this->dashboard) . "'," .
                                        "id_sal = '" . mysqli_escape_string($db->getLink(), $this->id_sal) . "'," .
					"social_tw = '" . mysqli_escape_string($db->getLink(), $this->twitter) . "'";
				$query = sprintf(self::QUERY_UPDATE, 
					self::TABLE_NAME,
					$insertStr,
					$this->getId());
			} else {
				$updateFieldsStr = "name,content,excerpt,street,city_id,foodType,logo_filename,local_elements,ambience,price,timing,phone,email,social_fb,dashboard,social_tw";
				$updateValuesStr = "'" . mysqli_escape_string($db->getLink(), $this->name) . "'," .
					"'" . mysqli_escape_string($db->getLink(), $this->content) . "'," .
					"'" . mysqli_escape_string($db->getLink(), $this->excerpt) . "'," .
					"'" . mysqli_escape_string($db->getLink(), $this->address) . "'," .
                                        "'" . mysqli_escape_string($db->getLink(), $this->city) . "'," .
					"'" . mysqli_escape_string($db->getLink(), $this->food) . "'," .
					"'" . mysqli_escape_string($db->getLink(), $this->logo) . "'," .
					"'" . mysqli_escape_string($db->getLink(), json_encode($this->elements)) . "'," .
					"'" . mysqli_escape_string($db->getLink(), json_encode($this->ambience)) . "'," .
                                        "'" . mysqli_escape_string($db->getLink(), $this->price) . "'," .
					"'" . mysqli_escape_string($db->getLink(), $this->time) . "'," .
					"'" . mysqli_escape_string($db->getLink(), $this->phone) . "'," .
					"'" . mysqli_escape_string($db->getLink(), $this->mail) . "'," .
					"'" . mysqli_escape_string($db->getLink(), $this->facebook) . "'," .
                                        "'" . mysqli_escape_string($db->getLink(), $this->dashboard) . "'," .
					"'" . mysqli_escape_string($db->getLink(), $this->twitter) . "'";
				$query = sprintf(self::QUERY_INSERT,
                                        self::TABLE_NAME,
					$updateFieldsStr,
					$updateValuesStr);
                                $insert = true;
			}
			$result = $db->query($query);
                        if($insert){
                            $this->id = $db->lastID();
                        }
			
			if($result) {
				$extension_query = '';
				if($this->extension_exists()) {
					$insertStr = "lat = '" . mysqli_escape_string($db->getLink(), $this->lat) . "'," .
						"lng = '" . mysqli_escape_string($db->getLink(), $this->lng) . "'," .
						"tags = '" . mysqli_escape_string($db->getLink(), $this->tags) . "'," .
						"keywords = '" . mysqli_escape_string($db->getLink(), $this->keywords) . "'," .
						"gallery_id = '" . intval($this->gallery_id) . "'," .
						"menu_pdf = '" . mysqli_escape_string($db->getLink(), $this->menu_pdf) . "'," .
						"menu_locu = '" . mysqli_escape_string($db->getLink(), $this->menu_locu) . "'," .
						"cityname = '" . mysqli_escape_string($db->getLink(), $this->city) . "'," .
						"franchise = '" . intval($this->franchise) . "'," .
						"franchise_parent = '" . intval($this->franchise_parent) . "'";
					if($_SESSION['userObject']->getDashboardRole() == ROLE_MANAGER) {
						$insertStr .= ",pending=1";	
					} else {
						$insertStr .= ",pending=0";	
					}
					$extension_query = sprintf(self::QUERY_EXTENSION_UPDATE,
						self::TABLE_EXTENSION_NAME,
						$insertStr,
						$this->getId());
				} else {
					$updateFieldsStr = "properties_id,lat,lng,tags,keywords,gallery_id,menu_pdf,menu_locu,cityname,franchise,franchise_parent";
					$updateValuesStr = "'" . intval($this->getId()) . "'," .
						"'" . mysqli_escape_string($db->getLink(), $this->lat) . "'," .
						"'" . mysqli_escape_string($db->getLink(), $this->lng) . "'," .
						"'" . mysqli_escape_string($db->getLink(), $this->tags) . "'," .
						"'" . mysqli_escape_string($db->getLink(), $this->keywords) . "'," .
						"'" . intval($this->gallery_id) . "'," .
						"'" . mysqli_escape_string($db->getLink(), $this->menu_pdf) . "'," .
						"'" . mysqli_escape_string($db->getLink(), $this->menu_locu) . "'," .
						"'" . mysqli_escape_string($db->getLink(), $this->city) . "'," .
						"'" . intval($this->franchise) . "'," .
						"'" . intval($this->franchise_parent) . "'";
					if($_SESSION['userObject']->getDashboardRole() == ROLE_MANAGER) {
						$updateFieldsStr .= ",pending";	
						$updateValuesStr .= ",1";
					} else {
						$updateFieldsStr .= ",pending";	
						$updateValuesStr .= ",0";
					}
					$extension_query = sprintf(self::QUERY_INSERT,
						self::TABLE_EXTENSION_NAME,
						$updateFieldsStr,
						$updateValuesStr);

				}
				$extension_result = $db->query($extension_query);
			}

			if($_SESSION['userObject']->getDashboardRole() == ROLE_ADMIN) {
				$this->syncToSal();
			}

			return $extension_result;
		}
                
		public function exists() {
			$db = new DBManager();
                        $query = sprintf(
				self::QUERY_EXISTS,
				self::TABLE_NAME,
				"id=" . $this->getId()
                        );
                        $obj = $db->getValue($query);   
                        if(is_object($obj)){
                            return $obj->count;
                        }
                        return false;
		}

		public function extension_exists() {
			$db = new DBManager();
	        $query = sprintf(
				self::QUERY_EXISTS,
				self::TABLE_EXTENSION_NAME,
				"properties_id=" . $this->getId()
	        );
	        $obj = $db->getValue($query);   
                        if(is_object($obj)){
                            return $obj->count;
                        }
                        return false;
		}
                
                public function existsSalId() {
			$db = new DBManager();
	        $query = sprintf(
				self::QUERY_EXISTS_SAL_ID,
				self::TABLE_NAME,
				$this->getId_sal()
	        );
	        $obj = $db->getValue($query);   
                
                 if(is_object($obj)){                
	          return $obj;
                 }
                 return false;
		}
                
                public function existsSalWeb($id){
                    $curlData = array(
				'restaurant_id' => $id,
				'api_key' => SAL_API_KEY
				);
                    $salData = $this->curlWrapper(SAL_API_URL . 'restaurants/detailDashboard/', 'GET', $curlData);
                    $response = json_decode($salData);
                    return ($response->status=='ok');
                }

		public function syncFromSal($id, $fetchDB=true) {
                        if($fetchDB){
                            $this->fetchSingle($id);
                        }
			$curlData = array(
				'restaurant_id' => $this->id_sal,
				'cleanCache' => '1',
				'rand' => rand(),
				'api_key' => SAL_API_KEY
				);
			$salData = $this->curlWrapper(SAL_API_URL . 'restaurants/detailDashboard/', 'GET', $curlData);
			$salRestaurant = json_decode($salData);
			$salRestaurant = $salRestaurant->result;

			$this->setName($salRestaurant->post_title);
			$this->setContent($salRestaurant->post_content);
			$this->setExcerpt($salRestaurant->post_excerpt);
			$this->setAddress($salRestaurant->geo_address);
			$this->setLat($salRestaurant->geo_latitude);
			$this->setLng($salRestaurant->geo_longitude);
			$this->setCity($salRestaurant->cityname);
			$this->setFood($salRestaurant->foodType);
			$this->setTags($salRestaurant->tags);
			$this->setKeywords($salRestaurant->kw_tags);
			$this->setTime($salRestaurant->timing);
			$this->setPhone($salRestaurant->contact);
			$this->setMail($salRestaurant->email);
			$this->setMenu_pdf($salRestaurant->pdf_menu_custom_attachment);
			$this->setMenu_locu($salRestaurant->locu_id);
			$this->setFacebook($salRestaurant->facebook);
			$this->setTwitter($salRestaurant->twitter);
			$this->setFranchise($salRestaurant->franchise);
			$this->setFranchise_parent($salRestaurant->post_parent);
			
			$dashboardLocalElements = array();
			foreach ($salRestaurant->elementos_de_su_local as $element) {
				foreach (unserialize(SAL_LOCAL_ELEMENTS) as $key => $value) {
					if(strtolower(trim($element)) == strtolower(trim($value))) {
						$dashboardLocalElements[] = $key;
						break;
					}
				}
			}
			$this->setElements($dashboardLocalElements);

			$dashboardAmbience = array();
			foreach ($salRestaurant->ambiente_place as $ambience) {
				foreach (unserialize(SAL_AMBIENCES) as $key => $value) {
					if(strtolower(trim($ambience)) == strtolower(trim($value))) {
						$dashboardAmbience[] = $key;
						break;
					}
				}
			}
			$this->setAmbience($dashboardAmbience);

			$dashboardPrice = '';
			foreach (unserialize(SAL_PRICES) as $key => $value) {
				if(strtolower(trim($salRestaurant->precio)) == strtolower(trim($value))) {
					$dashboardPrice = $key;
					break;
				}
			}
			$this->setPrice($dashboardPrice);
			$this->save();
		}

		public function syncToSal() {
			$this->fetchSingle($this->id);
			$salRest = new stdClass();
			$salRest->id = $this->getId();
			$salRest->id_sal = $this->getId_sal();
            if(is_null($salRest->id_sal) || !is_numeric($salRest->id_sal) || $salRest->id_sal == 0){
                return false;
            }
			$salRest->content = $this->getContent();
			$salRest->excerpt = $this->getExcerpt();
			$salRest->address = $this->getAddress();
			$salRest->lat = $this->getLat();
			$salRest->lng = $this->getLng();
			$salRest->logo = $this->getLogo();
			$salRest->time = $this->getTime();
			$salRest->phone = $this->getPhone();
			$salRest->mail = $this->getMail();
			$salRest->menu_pdf = $this->getMenu_pdf();
			$salRest->menu_locu = $this->getMenu_locu();
			$salRest->facebook = $this->getFacebook();
			$salRest->twitter = $this->getTwitter();
			
			$salAmbiences = unserialize(SAL_AMBIENCES);
			$salRest->ambience = array();
			foreach ($this->getAmbience() as $value) {
				$salRest->ambience[] = $salAmbiences[$value];
			}

			$salElements = unserialize(SAL_LOCAL_ELEMENTS);
			$salRest->elements = array();
			foreach ($this->getElements() as $value) {
				$salRest->elements[] = $salElements[$value];
			}

			$salPrices = unserialize(SAL_PRICES);
            if(isset($salPrices[$this->getPrice()])){
				$salRest->price = array($salPrices[$this->getPrice()]);
            }
			$curlData = array(
				'restaurant' => $salRest,
				'api_key' => SAL_API_KEY
				);

			$salResult = $this->curlWrapper(
				SAL_API_URL . 'restaurants/syncDashboardRest/', 'POST', $curlData);
			$json = json_decode(trim($salResult));
			if(!isset($json->status) || $json->status != 'ok') {
				return false;
			}

			$db = new DBManager();
			$query = sprintf(
				self::QUERY_EXTENSION_UPDATE,
				self::TABLE_EXTENSION_NAME,
				'pending=0',
				$this->id);
			$result = $db->query($query);
			return $result;
		}

		public static function getReserveStatus($id) {
			$db = new DBManager();
			$query = sprintf(
				RestaurantDetailModel::QUERY_RESERVE_ACTIVE,
				RestaurantDetailModel::TABLE_NAME,
				$id
				);
			$result = $db->getValue($query);
			if(!isset($result)) {
				$result = 'inactive';
			} else {
				$result = $result->status;
			}
			return $result;
		}

		public static function isFranchise($id) {
			$db = new DBManager();
			$query = sprintf(
				RestaurantDetailModel::QUERY_FRANCHISE,
				RestaurantDetailModel::TABLE_EXTENSION_NAME,
				$id
				);
			$result = $db->getValue($query);
			if(!isset($result)) {
				$result = '0';
			} else {
				$result = $result->franchise;
			}
			return $result;
		}
                
                public function fetchFromSal(){
                        $curlData = array(
				'restaurant_id' => $this->getId_sal(),
				'api_key' => SAL_API_KEY
				);
			$salData = $this->curlWrapper(SAL_API_URL . 'restaurants/detailDashboard/', 'GET', $curlData);
			$salRestaurant = json_decode($salData);
			$salRestaurant = $salRestaurant->result;

			$this->setName($salRestaurant->post_title);
			$this->setContent($salRestaurant->post_content);
			$this->setExcerpt($salRestaurant->post_excerpt);
			$this->setAddress($salRestaurant->geo_address);
			$this->setLat($salRestaurant->geo_latitude);
			$this->setLng($salRestaurant->geo_longitude);
			$this->setCity($salRestaurant->cityname);
			$this->setFood($salRestaurant->foodType);
			$this->setTags($salRestaurant->tags);
			$this->setKeywords($salRestaurant->kw_tags);
			$this->setTime($salRestaurant->timing);
			$this->setPhone($salRestaurant->contact);
			$this->setMail($salRestaurant->email);
			$this->setMenu_pdf($salRestaurant->pdf_menu_custom_attachment);
			$this->setMenu_locu($salRestaurant->locu_id);
			$this->setFacebook($salRestaurant->facebook);
			$this->setTwitter($salRestaurant->twitter);
			$this->setFranchise($salRestaurant->franchise);
			
			$dashboardLocalElements = array();
			foreach ($salRestaurant->elementos_de_su_local as $element) {
				foreach (unserialize(SAL_LOCAL_ELEMENTS) as $key => $value) {
					if(strtolower(trim($element)) == strtolower(trim($value))) {
						$dashboardLocalElements[] = $key;
						break;
					}
				}
			}
			$this->setElements($dashboardLocalElements);

			$dashboardAmbience = array();
			foreach ($salRestaurant->ambiente_place as $ambience) {
				foreach (unserialize(SAL_AMBIENCES) as $key => $value) {
					if(strtolower(trim($ambience)) == strtolower(trim($value))) {
						$dashboardAmbience[] = $key;
						break;
					}
				}
			}
			$this->setAmbience($dashboardAmbience);

			$dashboardPrice = '';
			foreach (unserialize(SAL_PRICES) as $key => $value) {
				if(strtolower(trim($salRestaurant->precio)) == strtolower(trim($value))) {
					$dashboardPrice = $key;
					break;
				}
			}
			$this->setPrice($dashboardPrice);
                 }
                

	}

?>