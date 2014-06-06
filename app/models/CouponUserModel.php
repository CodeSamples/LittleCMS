<?php

    class CouponUserModel extends Model {

        const TABLE_NAME = "franchise_coupon_users";
        const QUERY_INSERT = "INSERT INTO %s 
            (coupon_id,sal_id,sal_login,sal_email,sal_display_name,sal_UID_gigya) 
            VALUES (%s)";

        private $id;
        private $coupon_id;
        private $sal_id;
        private $sal_login;
        private $sal_email;
        private $sal_display_name;
        private $sal_UID_gigya;

        public function getId() { return $this->id; }
        public function getCoupon_id() { return $this->coupon_id; }
        public function getSal_id() { return $this->sal_id; }
        public function getSal_login() { return $this->sal_login; }
        public function getSal_email() { return $this->sal_email; }
        public function getSal_display_name() { return $this->sal_display_name; }
        public function getSal_UID_gigya() { return $this->sal_UID_gigya; }

        public function setId($id) {
            $this->id = $id; 
        }
        public function setCoupon_id($coupon_id) {
            $this->coupon_id = $coupon_id; 
        }
        public function setSal_id($sal_id) {
            $this->sal_id = $sal_id; 
        }
        public function setSal_login($sal_login) {
            $this->sal_login = $sal_login; 
        }
        public function setSal_email($sal_email) {
            $this->sal_email = $sal_email; 
        }
        public function setSal_display_name($sal_display_name) {
            $this->sal_display_name = $sal_display_name; 
        }
        public function setSal_UID_gigya($sal_UID_gigya) {
            $this->sal_UID_gigya = $sal_UID_gigya; 
        }


        public function insert() {
            if(isset($this->id)) {
                return false;
            }
            $db = new DBManager();
            $query = sprintf(self::QUERY_INSERT,
                self::TABLE_NAME,
                intval($this->coupon_id) . ','
                . intval($this->sal_id) . ','
                . "'" . mysqli_escape_string($db->getLink(), $this->sal_login) . "',"
                . "'" . mysqli_escape_string($db->getLink(), $this->sal_email) . "',"
                . "'" . mysqli_escape_string($db->getLink(), $this->sal_display_name) . "',"
                . "'" . mysqli_escape_string($db->getLink(), $this->sal_UID_gigya) . "'"
                );
            $result = $db->query($query);
            if($result) {
                $this->id = mysqli_insert_id($db->getLink());
            }
        }



    }

?>