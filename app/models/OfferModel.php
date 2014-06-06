<?php 

    class OfferModel extends Model {

        const TABLE_PROPERTIES = "properties";
        const TABLE_PROPERTIES_META = "properties_meta";
        const QUERY_GET_OFFERS = "SELECT p.special_offers,
            MAX(CASE WHEN pm.meta_key = 'offer_dated' THEN pm.meta_value ELSE '' END) AS offer_dated,
            MAX(CASE WHEN pm.meta_key = 'special_offers_plus' THEN pm.meta_value ELSE '' END) AS special_offers_plus,
            t.salphones
            FROM %s AS p
            LEFT JOIN %s AS pm
                ON pm.property_parent = p.id
                AND pm.meta_type = 'meta'
                AND pm.meta_key IN ('offer_dated', 'special_offers_plus')
            LEFT JOIN account_tropo AS t
                ON t.propiedad = p.id
            WHERE p.id = %d";
        const QUERY_SAVE_OFFERS = "UPDATE %s SET special_offers = '%s' WHERE id = %d";
        const QUERY_UPDATE_OFFERS_META = "UPDATE %s 
            SET meta_value = '%s' WHERE meta_id = %d";
        const QUERY_EXISTS_OFFERS_META = "SELECT meta_id 
            FROM %s WHERE property_parent = %d AND meta_type = 'meta' AND meta_key = '%s'";
        const QUERY_INSERT_OFFERS_META = "INSERT INTO %s (%s) VALUES(%s)";

        private $restaurant_id;
        private $offer;
        private $offer_plus;
        private $offer_dated;
        private $sal_phone;

        public function getRestaurant_id() { return $this->restaurant_id; }
        public function getOffer() { return $this->offer; }
        public function getOffer_plus() { return $this->offer_plus; }
        public function getOffer_dated() { return $this->offer_dated; }
        public function getSal_phone() { return $this->sal_phone; }

        public function setRestaurant_id($restaurant_id) { $this->restaurant_id = $restaurant_id; }
        public function setOffer($offer) { $this->offer = $offer; }
        public function setOffer_plus($offer_plus) { $this->offer_plus = $offer_plus; }
        public function setOffer_dated($offer_dated) { $this->offer_dated = $offer_dated; }
        public function setSal_phone($sal_phone) { $this->sal_phone = $sal_phone; }


        public function fetch() {
            if(!isset($this->restaurant_id)) {
                return false;
            }

            $db = new DBManager();
            $query = sprintf(self::QUERY_GET_OFFERS,
                self::TABLE_PROPERTIES,
                self::TABLE_PROPERTIES_META,
                $this->restaurant_id);
            $result = $db->getValue($query);
            if($result) {
                $this->offer = $result->special_offers;
                $this->offer_plus = $result->special_offers_plus;

                $offer_dated = @unserialize($result->offer_dated);
                if(!$offer_dated) {
                    $offer_dated = array();
                }
                $this->offer_dated = $offer_dated;
                $this->sal_phone = $result->salphones;
            }
        }

        public function save() {
            if(!isset($this->restaurant_id)) {
                return false;
            }

            $returnVal = false;
            $db = new DBManager();
            $offersQuery = sprintf(self::QUERY_SAVE_OFFERS,
                self::TABLE_PROPERTIES,
                mysqli_escape_string($db->getLink(), $this->offer),
                $this->restaurant_id);
            $offersResult = $db->query($offersQuery);
            if($offersResult) {
                $plusResult = $this->updateMeta('special_offers_plus',
                    htmlspecialchars($this->offer_plus, ENT_QUOTES), $db);
                if($plusResult) {
                    $datedResult = $this->updateMeta('offer_dated', $this->offer_dated, $db);
                    if($datedResult) {
                        $returnVal = true;
                        if($_SESSION['userObject']->getDashboardRole() == ROLE_ADMIN) {
                            $this->syncToSal();
                        }
                    }
                }
            }
            return $returnVal;
        }

        protected function updateMeta($metaKey, $metaValue, $db) {
            $metaId = $this->metaExists($metaKey, $db);
            if($metaId) {
                $metaQuery = sprintf(self::QUERY_UPDATE_OFFERS_META,
                    self::TABLE_PROPERTIES_META,
                    mysqli_escape_string($db->getLink(), $metaValue),
                    $metaId);
            } else {
                $metaQuery = sprintf(self::QUERY_INSERT_OFFERS_META,
                    self::TABLE_PROPERTIES_META,
                    'property_parent,meta_type,meta_key,meta_value,userID',
                    intval($this->restaurant_id) . ","
                    . "'meta',"
                    . "'" . $metaKey . "',"
                    . "'" . mysqli_escape_string($db->getLink(), $metaValue) . "',"
                    . intval($_SESSION['userObject']->getId())
                    );
            }
            return $db->query($metaQuery);
        }

        protected function metaExists($metaKey, $db) {
            $metaExistsQuery = sprintf(self::QUERY_EXISTS_OFFERS_META,
                    self::TABLE_PROPERTIES_META,
                    $this->restaurant_id,
                    $metaKey);
            $metaId = $db->getValue($metaExistsQuery);
            $returnVal = 0;
            if($metaId && isset($metaId->meta_id) && trim($metaId->meta_id) != '') {
                $returnVal = $metaId->meta_id;
            }
            return $returnVal;
        }

        public function syncToSal() {
            $this->fetch();
            $salOffer = new stdClass();
            $salOffer->restaurant_id = $this->getRestaurant_id();
            $salOffer->offer = $this->getOffer();
            $salOffer->offer_plus = $this->getOffer_plus();
            $salOffer->offer_dated = $this->getOffer_dated();
            $curlData = array(
                'offer' => $salOffer,
                'api_key' => SAL_API_KEY
                );
            $salResult = $this->curlWrapper(
                SAL_API_URL . 'restaurants/syncDashboardOffer/', 'POST', $curlData);

            $json = json_decode(trim($salResult));
            if(!$json->status == 'ok') {
                return false;
            }
            return true;
        }
        
        public function fetchFromSal($salId) {

            $curlData = array(
                'restaurant_id' => $salId,
                'api_key' => SAL_API_KEY
                );
            $salData = $this->curlWrapper(
                SAL_API_URL . 'restaurants/getDashboardOffer/', 'POST', $curlData);
            $offer = json_decode($salData);
          
            if($offer) {
                $this->offer = $offer->result->offer;
                $this->offer_plus = $offer->result->offer_plus;

                $offer_dated = @unserialize($offer->result->dated_offers);
                if(!$offer_dated) {
                    $offer_dated = array();
                }
                $this->offer_dated = $offer_dated;
                $this->sal_phone = $offer->result->sal_phone;
            }
        }
    }

?>