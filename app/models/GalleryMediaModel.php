<?php

    class GalleryMediaModel extends Model {

        const TABLE_NAME = "gallery_media";
        const QUERY_INSERT = "INSERT INTO %s (%s) VALUES (%s)";
        const QUERY_UPDATE = "UPDATE %s SET %s WHERE external_id = %d";
        const QUERY_UPDATE_BY_ID = "UPDATE %s SET %s WHERE id = %d";
        const QUERY_DELETE = "DELETE FROM %s WHERE id = %d";
        const QUERY_EXISTS = "SELECT COUNT(id) AS count FROM %s WHERE external_id = %d";
        const QUERY_FETCH_BY_EXTERNAL_ID = "SELECT id, gallery_id, filename, type 
            FROM %s 
            WHERE external_id = %d 
            AND type = '%s' 
            AND gallery_id = %d";
        const QUERY_GET_BY_GALLERY_ID = "SELECT id, external_id, filename, type 
            FROM %s 
            WHERE gallery_id = %d";
        const QUERY_FETCH_BY_ID = "SELECT gallery_id, external_id, filename, type
            FROM %s
            WHERE id = %d";

        public $id;
        public $gallery_id;
        public $external_id;
        public $filename;
        public $type;

        public function getId() { return $this->id; }
        public function getGallery_id() { return $this->gallery_id; }
        public function getExternal_id() { return $this->external_id; }
        public function getFilename() { return $this->filename; }
        public function getType() { return $this->type; }

        public function setId($id) { $this->id = $id; }
        public function setGallery_id($gallery_id) { $this->gallery_id = $gallery_id; }
        public function setExternal_id($external_id) { $this->external_id = $external_id; }
        public function setFilename($filename) { $this->filename = $filename; }
        public function setType($type) { $this->type = $type; }

        public function save() {
            $db = new DBManager();
            if( !isset($this->id) && ($this->external_id == 0 || !$this->existsByExternalId()) ) {
                $query = sprintf(self::QUERY_INSERT, self::TABLE_NAME,
                    'gallery_id,external_id,filename,type',
                    intval($this->gallery_id) . 
                    ',' . intval($this->external_id) .
                    ",'" . mysqli_escape_string($db->getLink(), $this->filename) . "'," .
                    "'" . mysqli_escape_string($db->getLink(), $this->type) . "'");
            } elseif(!isset($this->id)) {
                $query = sprintf(self::QUERY_UPDATE, self::TABLE_NAME,
                    "gallery_id=" . intval($this->gallery_id) . ","
                    . "external_id=" . intval($this->external_id) . ","
                    . "filename='" . mysqli_escape_string($db->getLink(), $this->filename) . "',"
                    . "type='" . mysqli_escape_string($db->getLink(), $this->type) . "'",
                    $this->external_id);
            } else {
                $query = sprintf(self::QUERY_UPDATE_BY_ID, self::TABLE_NAME,
                    "gallery_id=" . intval($this->gallery_id) . ","
                    . "external_id=" . intval($this->external_id) . ","
                    . "filename='" . mysqli_escape_string($db->getLink(), $this->filename) . "',"
                    . "type='" . mysqli_escape_string($db->getLink(), $this->type) . "'",
                    $this->id);
            }
            return $db->query($query);
        }

        public function existsByExternalId() {
            $db = new DBManager();
            $query = sprintf(
                self::QUERY_EXISTS,
                self::TABLE_NAME,
                $this->external_id,
                $this->gallery_id
            );
            $response = $db->getValue($query);  
            if($response) {
                $response = $response->count;
            }
            return $response;
        }

        public function fetch() {
            if(!isset($this->id)) {
                return false;
            }
            $query = sprintf(self::QUERY_FETCH_BY_ID, self::TABLE_NAME, $this->id);
            $db = new DBManager();
            $result = $db->getValue($query);
            if($result) {
                $this->setGallery_id($result->gallery_id);
                $this->setExternal_id($result->external_id);
                $this->setFilename($result->filename);
                $this->setType($result->type);
            }
            return true;
        }

        public function delete() {
            $db = new DBManager();
            $query = sprintf(self::QUERY_DELETE, self::TABLE_NAME, $this->id);
            return $db->query($query);
        }

        public static function getByGalleryID($gallery_id) {
            $db = new DBManager();
            $query = sprintf(self::QUERY_GET_BY_GALLERY_ID, self::TABLE_NAME, $gallery_id);
            $returnVal = array();
            $result = $db->query($query);
            if($result) {
                while($single = $db->next($result)) {
                    $galleryMedia = new GalleryMediaModel();
                    $galleryMedia->setId($single->id);
                    $galleryMedia->setGallery_id($gallery_id);
                    $galleryMedia->setExternal_id($single->external_id);
                    $galleryMedia->setFilename($single->filename);
                    $galleryMedia->setType($single->type);
                    $returnVal[] = $galleryMedia;
                }
            }
            return $returnVal;
        }

    }

?>