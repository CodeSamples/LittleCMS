<?php

class WebLayout extends Layout{
    
    public function afterAction($response) {
        
    }

    public function beforeAction() {         
            
    }

    public function includeView() {
        return true;
    }

    public function afterView($response) {
        $obj_response = $response;
        include(THEME_PATH.'footer.php');   
        @ob_end_flush(); 
    }

    public function beforeView($response) {
        $this->rewriteThemePaths();
        $obj_response = $response;
        include(THEME_PATH.'header.php');
    }    
}
?>