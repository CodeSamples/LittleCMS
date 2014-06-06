<?php 

	class CouponModel extends Model {

		const TABLE_NAME = "franchise_coupon";
		const QUERY_INSERT = "INSERT INTO %s (%s) VALUES (%s)";
		const QUERY_UPDATE = "UPDATE %s SET %s WHERE id = %d";
		const QUERY_DELETE = "DELETE FROM %s WHERE id = %d";
		const QUERY_FETCH_ACTUAL = "SELECT c.id, c.name, c.caption, c.image, c.start, c.end, c.pdf
			FROM %s AS c
			WHERE c.property_id = %d
			AND c.end >= NOW()
			ORDER BY c.end DESC
			LIMIT 0,1";
		const QUERY_FETCH = "SELECT c.name, c.caption, c.image, c.start, c.end, c.pdf
			FROM %s AS c
			WHERE c.id = %d";
		const IMG_PLACEHOLDER = 'logo-placeholder-120.gif';

		private $id;
		private $property_id;
		private $name;
		private $caption;
		private $image;
		private $start;
		private $end;
		private $pdf;
		private $user_count;

		public function setId($id) { $this->id = $id; }
		public function setProperty_id($property_id) { $this->property_id = $property_id; }
		public function setName($name) { $this->name = $name; }		
		public function setCaption($caption) { $this->caption = $caption; }
		public function setImage($image) { $this->image = $image; }
		public function setStart($start) { $this->start = $start; }		
		public function setEnd($end) { $this->end = $end; }
		public function setPdf($pdf) { $this->pdf = $pdf; }
		public function setUser_count($user_count) { $this->user_count = $user_count; }

		public function getId() { return $this->id; }
		public function getProperty_id() { return $this->property_id; }
		public function getName() { return $this->name; }		
		public function getCaption() { return $this->caption; }
		public function getImage() { return $this->image; }
		public function getStart() { return $this->start; }		
		public function getEnd() { return $this->end; }
		public function getPdf() { return $this->pdf; }
		public function getUser_count() { return $this->user_count; }

		public function save() {
			if(!isset($this->image) || trim($this->image) == '') {
				$this->image = CDN_BASE_URL.CDN_APP_DIR.self::IMG_PLACEHOLDER;
			}

			$db = new DBManager();
			$query = "";
			$fields = array(
				'property_id' => $this->property_id,
				'name' => $this->name,
				'caption' => $this->caption,
				'image' => $this->image,
				'start' => $this->start,
				'end' => $this->end,
				'pdf' => $this->pdf);

			$new = false;
			if(!isset($this->id)) {
				$new = true;
				$query = sprintf(self::QUERY_INSERT,
					self::TABLE_NAME,
					implode(',', array_keys($fields)),
					"'" . implode("','", array_values($fields)) . "'");
			} else {
				$updateStr = "";
				$i = 1;
				foreach ($fields as $key => $value) {
					$updateStr .= $key . "='" . mysqli_escape_string(
						$db->getLink(), $value) . "'";
					if($i < sizeof($fields)) {
						$updateStr .= ',';
					}
					$i++;
				}
				$query = sprintf(self::QUERY_UPDATE,
					self::TABLE_NAME,
					$updateStr,
					$this->id);
			}

			$result = $db->query($query);
			if($result && $new) {
				$result = mysqli_insert_id($db->getLink());
			}
			return $result;
		}

		public function delete() {
			$db = new DBManager();
			$query = sprintf(self::QUERY_DELETE,
				self::TABLE_NAME,
				$this->id);
			return $db->query($query);
		}

		public function fetchActual() {
			$query = sprintf(self::QUERY_FETCH_ACTUAL, self::TABLE_NAME, $this->getProperty_id());
			$db = new DBManager();
			$result = $db->getValue($query);
			if($result) {
				$this->setId(intval($result->id));
				$this->setName($result->name);
				$this->setCaption($result->caption);
				$this->setImage($result->image);
				$this->setStart($result->start);
				$this->setEnd($result->end);
				$this->setPdf($result->pdf);
			}
		}

		public function fetch() {
			$query = sprintf(self::QUERY_FETCH, self::TABLE_NAME, $this->id);
			$db = new DBManager();
			$result = $db->getValue($query);
			if($result) {
				$this->setName($result->name);
				$this->setCaption($result->caption);
				$this->setImage($result->image);
				$this->setStart($result->start);
				$this->setEnd($result->end);
				$this->setPdf($result->pdf);
			} else {
				$this->id = null;
			}
		}

	}

?>