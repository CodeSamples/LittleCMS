<?php

    class VideoItemModel extends Model {

        public $id;
        public $title;
        public $desc;
        public $thumb;

        public function setId($id) { $this->id = $id; }
        public function setTitle($title) { $this->title = $title; }
        public function setDesc($desc) { $this->desc = $desc; }
        public function setThumb($thumb) { $this->thumb = $thumb; }

        public function getId() { return $this->id; }
        public function getTitle() { return $this->title; }
        public function getDesc() { return $this->desc; }
        public function getThumb() { return $this->thumb; }

    }
    
?>