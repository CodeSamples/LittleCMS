<?php

class UserInterceptor extends Interceptor{
    
    public function afterAction($response) {
        
    }

    public function beforeAction($controller) {
        $request = RequestManager::getInstance();
        
        if(!isset($_SESSION)){
            session_start();
        }
        /*
         * Control de usuario logueado
         */
        if($request->getController()=='User' && $request->getAction()=='login'){
            
        } else {
          $redirectUser = true;

          if(!isset($_SESSION["userObject"])){
            if(isset($_GET['apiKey']) && $_GET['apiKey'] === API_KEY) {
              $user = new UserModel();
              $user->setDashboardRole(ROLE_API);
              $_SESSION['userObject'] = $user;
              $redirectUser = false;

              /*
              if(isset($_SERVER['HTTP_REFERER'])) {
                $cleanRef = preg_replace('/http[s]*:\/\//', '', $_SERVER['HTTP_REFERER']);
                $cleanRef = preg_replace('/\/$/', '', $cleanRef);
                foreach (AppConfiguration::getApiHosts() as $host) {
                  if(strpos($cleanRef, $host) !== false) {
                    $user = new UserModel();
                    $user->setDashboardRole(ROLE_API);
                    $_SESSION['userObject'] = $user;
                    $redirectUser = false;
                    break;
                  }
                }
              }
              */
              
            }
          } else {
            $redirectUser = false;
          }

          if($redirectUser) {
            $request->redirect("User","login");
            exit();
          }

          
          /*
           * Control de privilegios segun el rol del usuario
           */
           $roles = AppConfiguration::getRolesConfig();
           $user = $_SESSION["userObject"]; 
           $user_role = $user->getDashboardRole();           
           $request_checked = false;
           
           foreach($roles["controller"] as $controller_name => $role){
            if($controller_name===$request->getController()){
                if($user_role>$role){
                    die("No tiene privilegios para ejecutar esta acción.");
                } else {
                  $request_checked = true;
                }
                break;
            }
           }
           
           foreach($roles["action"] as $controller_action => $role){
            if($controller_action===$request->getController().'-'.$request->getAction()){
                if($user_role>$role){
                    die("No tiene privilegios para ejecutar esta acción.");
                } else {
                  $request_checked = true;
                }                 
                break;
            }
           }

           if(!$request_checked && $user->getDashboardRole() > ROLE_ADMIN) {
            die("No tiene privilegios para ejecutar esta acción.");
           }
        }        
    }    
}
?>