<?php

abstract class Controller {
    protected $view;   
    
    public function getView(){
        return $this->view;
    }
    
    public function setView($view){
        $this->view = $view;
    }
    
    public function getInterceptors(){
        return array();
    }
}

?>