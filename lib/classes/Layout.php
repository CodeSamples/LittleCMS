<?php

abstract class Layout {

	protected $language;
        
    public abstract function beforeAction();
    
    public abstract function afterAction($response);
    
    public abstract function beforeView($response);
    
    public abstract function afterView($response);
    
    public abstract function includeView();

    public function __construct() {
    	$this->language = VIEW_LANG;
    }

    public function setLanguage($lang) { $this->language = $lang; }
    
    public function getLanguage() { return $this->language; }

    public function rewriteThemePaths() {
        ob_start();
        ob_start('self::rewriteBuffer');
    }

    protected static function rewriteBuffer($buffer) {
        $allowedFiles = array('css','js','png','gif','jpg','jpeg');
        $buffer = preg_replace('/(\/.[^\/]+\/[^\/]*)(\.)('. implode('|', $allowedFiles) . ')/is', THEME_URI . "$1$2$3", $buffer);
        return $buffer;
    }
    
}

?>
