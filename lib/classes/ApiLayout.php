<?php

class ApiLayout extends Layout{
    
    public function afterAction($response) {
        header('Content-Type: application/json');
        echo json_encode($response->getResponse()); 
    }

    public function beforeAction() {
             
    }

    public function includeView() {
        return false;
    }

    public function afterView($response) {
        
    }

    public function beforeView($response) {
        
    }    
}
?>