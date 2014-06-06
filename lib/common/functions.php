<?php

/*
 * Funciones comunes del Framework 
 */

function __autoload($class_name){
        //classes directories
        $directorys = array(
            MODELS_PATH,
            CONTROLLERS_PATH,
            COMMON_PATH,
            CLASSES_PATH,
            CLASSES_PATH.DS.'exceptions'.DS,
            CLASSES_PATH.DS.'interceptors'.DS
        );
        
        foreach($directorys as $directory)
        {
            if(file_exists($directory.$class_name . '.php'))
            {
                require_once($directory.$class_name . '.php');
                return;
            }            
        }
 }
 
?>