<?php

	class FileModel extends Model {

		public $path;
		public $type;
		public $size;

		public function getPath() { return $this->path; }
		public function getType() { return $this->type; }
		public function getSize() { return $this->size; }

		public function setPath($path) { $this->path = $path; }
		public function setType($type) { $this->type = $type; }
		public function setSize($size) { $this->size = $size; }

	}

?>