<?php

    class FranchiseChildsModel extends Model {

        public $parent = 0;
        public $childs = array();

        public function __construct($parent) {
            $curlData = array(
                'restaurant_id' => $parent,
                'api_key' => SAL_API_KEY
                );
            $curlResult = @json_decode(trim($this->curlWrapper(
                SAL_API_URL . 'restaurants/getFranchiseChilds/', 'GET', $curlData)));
            if(isset($curlResult) && $curlResult !== false) {
                $this->childs = $curlResult->result;
            }
        }

    }

?>