<?php 

    class GalleryModel extends Model {

        const TABLE_NAME = "gallery";
        const TABLE_PROPERTIES_NAME = "properties";
        const QUERY_INSERT = "INSERT INTO %s (property_id) VALUES (%d)";
        const QUERY_DELETE = "DELETE FROM %s WHERE id = %d";
        const QUERY_FETCH = "SELECT property_id FROM %s WHERE id = %d";
        const QUERY_GET_ID = "SELECT id FROM %s WHERE property_id = %d LIMIT 0,1";
        const QUERY_GET_SAL_ID = "SELECT id_sal FROM %s WHERE id = %d LIMIT 0,1";
        const QUERY_EXISTS = "SELECT COUNT(id) AS count FROM %s WHERE id = %d";

        public $id;
        public $property_id;
        public $media_list;

        public function setId($id) { $this->id = $id; }
        public function setProperty_id($property_id) { 
            $this->property_id = $property_id;
        }
        public function setMedia_list($media_list) { $this->media_list = $media_list; }

        public function getId() { return $this->id; }
        public function getProperty_id() { return $this->property_id; }
        public function getMedia_list() { return $this->media_list; }

        public function insert() {
            $db = new DBManager();
            $query = sprintf(self::QUERY_INSERT,
                self::TABLE_NAME,
                $this->property_id
                );
            $result = $db->query($query);
            if($result) {
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

        public function fetch() {
            $db = new DBManager();
            $query = (sprintf(self::QUERY_FETCH, self::TABLE_NAME, $this->id));
            $result = $db->getValue($query);
            if($result) {
                $this->property_id = $result->property_id;
                $this->fetchMediaList();
            }
        }

        public function exists() {
            $db = new DBManager();
            $query = sprintf(
                self::QUERY_EXISTS,
                self::TABLE_NAME,
                $this->id
            );
            $obj = $db->getValue($query);   
            return $obj->count;
        }

        public function fetchMediaList() {
            $this->media_list = GalleryMediaModel::getByGalleryID($this->id);
        }

        public function fetchMediaListFromSal($salId){
            $returnVal = array();
            $curlData = array(
                'restaurant_id' => $salId,
                'api_key' => SAL_API_KEY,
                'cleanCache' => '1',
                'rand' => rand()
                );
            $salData = $this->curlWrapper(
                SAL_API_URL . 'restaurants/detailDashboardGallery/', 'GET', $curlData);
            $salGallery = json_decode($salData);
            if(sizeof($salGallery->result->videos) > 0) {
                $bumbia_ids = array();
                foreach ($salGallery->result->videos as $key => $value) {
                    if(is_numeric($value)) {
                        $bumbia_ids[] = $value;
                    } elseif(is_object($value)) {
                        $bumbia_ids[] = $value->external_id;
                    }
                }
                $_GET['bumbia_ids'] = implode(',', $bumbia_ids);
                $bumbiaVideos = Video::getVideos();
                if($bumbiaVideos) {
                    foreach ($bumbiaVideos->response as $vid) {
                        $media = new GalleryMediaModel();
                        $media->setGallery_id($this->id);
                        $media->setExternal_id($vid->id);
                        $media->setFilename($vid->thumb);
                        $media->setType(BUMBIA_FILETYPE);
                        $returnVal[] = $media;
                    }
                }
            }
            
            foreach ($salGallery->result->images as $key => $value) {
                $media = new GalleryMediaModel();
                $media->setGallery_id($this->id);
                $media->setExternal_id(intval($key));
                $media->setFilename($value->file);
                $media->setType($value->type);
                $returnVal[] = $media;
            }
            
            return $returnVal;
        }

        public function save() {
            $db = new DBManager();
            $newMediaList = $this->media_list;
            $this->fetchMediaList();   

            foreach ($newMediaList as $media) {
                $toDelete = false;
                foreach ($this->media_list as $key => $old) {
                    if($old->external_id == $media->external_id && $old->filename == $media->filename) {
                        unset($this->media_list[$key]);
                        $toDelete = true;
                        break;
                    }
                }
                if(!$toDelete) {
                    $media->save();
                }
            }
            
            foreach ($this->media_list as $media) {
                $media->delete();
            }

            if($_SESSION['userObject']->getDashboardRole() == ROLE_ADMIN) {
                $this->syncToSal();
            }

            return true;
        }


        public function syncFromSal($salId) {
            $this->fetch();

            foreach ($this->media_list as $media) {
                $media->delete();
            }

            $curlData = array(
                'restaurant_id' => $salId,
                'api_key' => SAL_API_KEY
                );
            $salData = $this->curlWrapper(
                SAL_API_URL . 'restaurants/detailDashboardGallery/', 'GET', $curlData);
            $salGallery = json_decode($salData);
            if(sizeof($salGallery->result->videos) > 0) {
                $bumbia_ids = array();
                foreach ($salGallery->result->videos as $key => $value) {
                    if(is_numeric($value)) {
                        $bumbia_ids[] = $value;
                    } elseif(is_object($value)) {
                        $bumbia_ids[] = $value->external_id;
                    }
                }
                $_GET['bumbia_ids'] = implode(',', $bumbia_ids);
                $bumbiaVideos = Video::getVideos();
                if($bumbiaVideos) {
                    foreach ($bumbiaVideos->response as $vid) {
                        $media = new GalleryMediaModel();
                        $media->setGallery_id($this->id);
                        $media->setExternal_id($vid->id);
                        $media->setFilename($vid->thumb);
                        $media->setType(BUMBIA_FILETYPE);
                        $media->save();    
                    }
                }
            }
            
            foreach ($salGallery->result->images as $key => $value) {
                $media = new GalleryMediaModel();
                $media->setGallery_id($this->id);
                $media->setExternal_id(intval($key));
                $media->setFilename($value->file);
                $media->setType($value->type);
                $media->save();
            }
        } 


        public function syncToSal() {
            $this->id = $this->getGalleryId();
            $this->fetch();

            $salGallery = new stdClass();
            $salGallery->restaurant_id = $this->getProperty_id();
            $salGallery->media_list = $this->getMedia_list();

            $base_url = BASE_URL;
            if(!preg_match('/\/$/', $base_url)) {
                $base_url .= '/';
            }
            $callbackParams = array(
                'apiKey' => API_KEY
                );
            $callbackUri = 'json/Video/videoGfrCallback/?'.http_build_query($callbackParams);

            $curlData = array(
                'gallery' => $salGallery,
                'api_key' => SAL_API_KEY,
                'video_callback' => 'http://'.$base_url.$callbackUri
                );
            
            $salResult = $this->curlWrapper(
                SAL_API_URL . 'restaurants/syncDashboardGallery/', 'POST', $curlData);

            $json = json_decode(trim($salResult));
            
            if(!isset($json) || !isset($json->status) || !$json->status == 'ok') {
                return false;
            }

            return true;
        }


        public function getGalleryId() {
            if(!isset($this->property_id)) {
                return false;
            }
            $db = new DBManager();
            $query = sprintf(self::QUERY_GET_ID,
                self::TABLE_NAME,
                $this->property_id);
            $result = $db->getValue($query);
            if($result) {
                $result = $result->id;
            }
            return $result;
        }

        protected function getSalId() {
            $db = new DBManager();
            $query = sprintf(
                self::QUERY_GET_SAL_ID,
                self::TABLE_PROPERTIES_NAME,
                $this->getProperty_id()
                );
            $result = $db->getValue($query);
            if($result) {
                $result = $result->id_sal;
            }
            return $result;
        }
    }

?>