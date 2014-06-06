<?php

	class CouponListModel extends Model {

		const TABLE_NAME = "franchise_coupon";
		const TABLE_USERS_NAME = "franchise_coupon_users";
		const TABLE_USERS_LOGIN = "plc_users";
		const TABLE_USERS_LINK = "users_properties";
		const QUERY_FETCH = "SELECT c.id, c.name, c.caption, c.image, c.start, c.end, c.pdf, COUNT(cu.id) AS user_count
			FROM %s AS c
			LEFT JOIN %s AS cu ON cu.coupon_id = c.id
			WHERE c.property_id = %d
			GROUP BY c.id";
                const QUERY_FETCH_LIMIT = "SELECT c.id, c.name, c.caption, c.image, c.start, c.end, c.pdf, COUNT(cu.id) AS user_count
			FROM %s AS c
			LEFT JOIN %s AS cu ON cu.coupon_id = c.id
			WHERE c.property_id = %d
                        GROUP BY c.id
                        LIMIT %d,%d";
		const QUERY_VALIDATE_USER = "SELECT 
			CONCAT_WS(
				',',
				GROUP_CONCAT(DISTINCT u.userID), 
				GROUP_CONCAT(DISTINCT up.user)
			) AS users
			FROM %s AS u
			LEFT JOIN %s AS up 
				ON up.propiedad = u.property_id
			WHERE u.property_id = %d";

		private $list = array();


		public function fetchList($restaurant_id, $start=0, $limit=-1) {
			$db = new DBManager();
                        if($limit!=-1){
                            $query = sprintf(self::QUERY_FETCH_LIMIT,
				self::TABLE_NAME,
				self::TABLE_USERS_NAME,
				$restaurant_id, $start, $limit);
                        } else {
                            $query = sprintf(self::QUERY_FETCH,
				self::TABLE_NAME,
				self::TABLE_USERS_NAME,
				$restaurant_id);
                        }
			$result = $db->query($query);
			if($result) {
				$this->list = array();
				while($single = $db->next($result)) {
					$coupon = new CouponModel();
					$coupon->setId($single->id);
					$coupon->setProperty_id($restaurant_id);
					$coupon->setName($single->name);
					$coupon->setCaption($single->caption);
					$coupon->setImage($single->image);
					$coupon->setStart($single->start);
					$coupon->setEnd($single->end);
					$coupon->setPdf($single->pdf);
					$coupon->setUser_count($single->user_count);
					$this->list[] = $coupon;
				}
			}
		}

		public function validateUserProperty($restaurant_id) {
			$db = new DBManager();
			$query = sprintf(self::QUERY_VALIDATE_USER,
				self::TABLE_USERS_LOGIN,
				self::TABLE_USERS_LINK,
				intval($restaurant_id));
			$result = $db->getValue($query);
			if($result) {
				$userArray = array_unique(explode(',', $result->users));	
				if(!in_array($_SESSION['userObject']->getId(), $userArray)) {
					$result = false;
				}
			}
			return $result;
		}
                
                public function countAll($restaurant_id) {
                    $DB = new DBManager();
                    $query = sprintf(
                       "SELECT count(id) as count
                        FROM " . self::TABLE_NAME . " WHERE property_id = %d", $restaurant_id
                    );
                    $obj = $DB->getValue($query);

                    return intval($obj->count);
                }

		public function getList() {
			return $this->list;
		}

	}

?>