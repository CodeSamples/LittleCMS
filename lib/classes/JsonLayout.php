<?php

class JsonLayout extends Layout{
    
    public function afterAction($response) {
        header('Content-Type: application/json');
        if($response->getStatus()==''){
            switch($response->getCode()){
               case BaseResponse::ERROR_CODE: $response->setStatus("ERROR"); break;
               case BaseResponse::OK_CODE: $response->setStatus("OK"); break;
            }
        }
        echo json_encode($response); 
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