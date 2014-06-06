<?php

/*
 * Clase Request Manager.
 * Es la encargada de realizar el procesamiento del request, invocando al controlador/método específico.
 * También ejecuta las acciones sobre el layout actual y los eventos sobre los interceptors declarados atachados al request.
 */

class RequestManager {
    private static $instancia;
    private $layout;
    private $controller; 
    private $action;
    private $interceptors=array();
    
    public function excecute(){
      /*
      var_dump($_GET);
      die();
        */
        if(!isset($_GET["controller_action"])){
            $this->controller = DEFAULT_CONTROLLER;
            $this->action = 'home';
            $this->layout = new WebLayout();   
        } else {
        
          $request = explode('/',$_GET["controller_action"]);
          
          /*
           * @TODO: ver como implementar este cambio de vista segun request con un interceptor o simplemente
           * quitarlo y dejarlo explicito en cada controlador->metodo
           */
          switch($request[0]){
              case 'api': $this->layout = new ApiLayout(); 
                          array_shift($request);                          
                          break;  
              case 'ajax': $this->layout = new AjaxLayout();    
                          array_shift($request);
                          break;  
              case 'json': $this->layout = new JsonLayout();    
                          array_shift($request);
                          break;        
              default: $this->layout = new WebLayout();        
          }
          
          if(count($request)==0){
                $request[0] = DEFAULT_CONTROLLER;
                $request[1] = 'home';
          }
          /*
           * Fin del codigo para identificar el tipo de request
           */
        
          if(!is_array($request) || count($request)<2){
            throw new RequestException("Invalid Request.");
          } else {
            $this->controller = $request[0];
            $this->action = $request[1];
          }
        }
        
        if(file_exists(CONTROLLERS_PATH.$this->controller.'.php')){
            $classname = $this->controller;
            $action = $this->action;
            $controller = new $classname;
            $controller->setView($action);
            
            /*
             * Incluimos el idioma por defecto o el seleccionado
             */
            if(file_exists(LANGUAGE_PATH.$this->layout->getLanguage().'.php')) {
                require_once(LANGUAGE_PATH.$this->layout->getLanguage().'.php');
              } else {
                throw new LanguageException("Language file \"" . $this->layout->getLanguage().".php\" not found in: ".LANGUAGE_PATH.$this->layout->getLanguage().".php");
            }
            
            /*
             * Incluimos los interceptors específicos para el controlador->action
             */
            $this->includeRequestInterceptors($this->controller, $this->action);
            
            /*
             * Ejecutamos las acciones BeforeAction del layouts y de los interceptors definidos.
             */
            $this->layout->beforeAction();
            $this->runInterceptorsBefore($controller);
            
            if(method_exists($controller, $action)){
               $obj_response = $controller->$action();
            } else {
               throw new RequestException("Action ".$action." not found in controller ".$classname); 
            }
            
            if(!($obj_response instanceof Response)){
                throw new ResponseException("Action no returns Response Object on ".$classname."->".$action); 
            }
            
            if($this->layout->includeView()){
              $this->layout->beforeView($obj_response); 
              $view = $controller->getView(); 
              
              if(file_exists(VIEWS_PATH.$classname.DS.$view.'.php')){
                $response = $obj_response->getResponse();  
                include(VIEWS_PATH.$classname.DS.$view.'.php');
              } else {
                 throw new RequestException("View ".$classname."->".$action." not found in: ".VIEWS_PATH.$classname.DS.$action.'.php'); 
              }

              $this->layout->afterView($obj_response); 
            }
            $this->runInterceptorsAfter($obj_response);
            $this->layout->afterAction($obj_response);
        } else {
            throw new RequestException("Controller ".$this->controller." not found in: ".CONTROLLERS_PATH.$this->controller.'.php');
        }
        
    }
    
    public function getController() {
        return $this->controller;
    }

    public function getAction() {
        return $this->action;
    }
        
    public function redirect($controller,$action){
        header("location: /".$controller.'/'.$action);
        exit();
    }
    
    public function addInterceptor($interceptor){
        $find = false;
        foreach($this->interceptors as $interceptor_obj){
             if(get_class($interceptor_obj) ==  get_class($interceptor)){
                 $find=true; 
                 return;
             }
        }
        if(!$find){
            $this->interceptors[]=$interceptor;
        }
    }
    
    /**
     * @param String $interceptor_name Nombre de la clase sin Interceptor,
     *               por ejemplo para UserInterceptor, solo "User".
     * @return boolean TRUE si el Interceptor existia y fue eliminado-
     *                 FALSE en otro caso.
     */    
    public function removeInterceptor($interceptor_name){
        $new_interceptors = array();
        $find = false;
        foreach($this->interceptors as $interceptor){
             if(!($interceptor instanceof $interceptor_name.'Interceptor')){
                 $new_interceptors[]=$interceptor;
             } else {
                 $find = true;
             }
        }
        $this->interceptors = $new_interceptors;
        return $find;
    }
    
    public static function getInstance(){
      if (!self::$instancia instanceof self)
      {
         self::$instancia = new self;
      }
      return self::$instancia;
    }
    
    private function runInterceptorsAfter($response){
        foreach($this->interceptors as $interceptor){
             $interceptor->afterAction($response);             
        }
    }
    
    private function runInterceptorsBefore($controller){
        foreach($this->interceptors as $interceptor){
             $interceptor->beforeAction($controller);             
        }
    }
    
    private function includeRequestInterceptors($controller, $action){
        $interceptors_config = AppConfiguration::getInterceptorsConfig();
        
        foreach($interceptors_config["controller"] as $controller_name => $interceptors){
            if($controller_name===$controller){
                foreach($interceptors as $interceptor_name){
                  if(class_exists($interceptor_name.'Interceptor')){
                         $interceptor_name.='Interceptor';
                         $this->addInterceptor(new $interceptor_name);
                  }            
                }
                break;
            }
        }
        
        foreach($interceptors_config["action"] as $controller_action => $interceptors){
            if($controller_action===$controller.'-'.$action){
                foreach($interceptors as $interceptor_name){
                  if(class_exists($interceptor_name.'Interceptor')){
                         $interceptor_name.='Interceptor';
                         $this->addInterceptor(new $interceptor_name);
                  }            
                }
                break;
            }
        }
    }
    
    private function __construct() {        
        $interceptors = AppConfiguration::getInterceptorsConfig();
        foreach($interceptors["global"] as $interceptor_name){
            if(class_exists($interceptor_name.'Interceptor')){
                $interceptor_name.='Interceptor';
                $this->interceptors[]=new $interceptor_name;
            }            
        }
    }
}

?>