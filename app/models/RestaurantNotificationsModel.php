<?php

    class RestaurantNotificationsModel extends RestaurantBaseModel {

        const TABLE_NAME = "properties_dashboard";
        const QUERY_FETCH = "SELECT pd.notifications FROM %s AS pd WHERE pd.properties_id = %d";
        const QUERY_SAVE = "UPDATE %s SET notifications = '%s' WHERE properties_id = %d";

        public $share;
        public $comment;
        public $rate;
        public $contact_form;

        public function setShare($share) { $this->share = $share; }
        public function setComment($comment) { $this->comment = $comment; }
        public function setRate($rate) { $this->rate = $rate; }
        public function setContact_form($contact_form) { $this->contact_form = $contact_form; }

        public function getShare() { return $this->share; }
        public function getComment() { return $this->comment; }
        public function getRate() { return $this->rate; }
        public function getContact_form() { return $this->contact_form; }


        public function fetch($id) {
            $this->setId(intval($id));
            $db = new DBManager();
            $query = sprintf(self::QUERY_FETCH, self::TABLE_NAME, $this->getId());
            $result = $db->getValue($query);
            if($result) {
                $data = @json_decode($result->notifications);
                if(!isset($data)) {
                    $data = new stdClass();
                    $data->share = '';
                    $data->comment = '';
                    $data->rate = '';
                    $data->contact_form = '';
                }
                $this->share = $data->share;
                $this->comment = $data->comment;
                $this->rate = $data->rate;
                $this->contact_form = $data->contact_form;
                $result = true;
            }
            return $result;
        }

        public function save() {
            if(null == $this->getId()) {
                return false;
            }
            $data = new stdClass();
            $data->share = (isset($this->share)) ? $this->share : '';
            $data->comment = (isset($this->comment)) ? $this->comment : '';
            $data->rate = (isset($this->rate)) ? $this->rate : '';
            $data->contact_form = (isset($this->contact_form)) ? $this->contact_form : '';
            $db = new DBManager();
            $query = sprintf(
                self::QUERY_SAVE,
                self::TABLE_NAME,
                mysqli_escape_string($db->getLink(), json_encode($data)),
                $this->getId()
                );
            $result = $db->query($query);
            return $result;
        }

    }

?>