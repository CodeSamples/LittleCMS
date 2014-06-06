<?php

class User extends Controller{
    
    public function login(){
       $response = new Response();
       
       if(!empty($_POST)){
        
        if(!isset($_POST["username"]) || !isset($_POST["password"]) ||
           trim($_POST["username"])=='' || trim($_POST["password"])==''){ 
          
            $response->setCode(Response::ERROR_CODE);
            $response->addError(REQUIRED_USER_PASS);   
            $response->setResponse(false);
            
        } else {            
            $user = new UserModel();
            
            if($user->validate($_POST["username"], $_POST["password"])){                
                
                $_SESSION["username"] = $user->getUsername();
                $_SESSION["userObject"] = $user;
                
                /*
                 * Variable sesión para login en reserva:
                 */
                $_SESSION["dashboard-autologin"] = $user->getId();
                
                $request = RequestManager::getInstance();
                $request->redirect("Dashboard","home");
            } else {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(INVALID_USER_PASS);    
                $response->setResponse(false);
            }
        }
       } else {
           //Check autologin from sal reserva
           if(!isset($_SESSION)){
               session_start();
           }
           if(isset($_SESSION["reserva-autologin"])){
               $user = new UserModel();
               if($user->autologin($_SESSION["reserva-autologin"])){
                   unset($_SESSION["reserva-autologin"]);
                   $_SESSION["username"] = $user->getUsername();
                   $_SESSION["userObject"] = $user;
                   $request = RequestManager::getInstance();
                   $request->redirect("Dashboard","home");
               }
           }
       }
        
       return $response;
    }
    
    public function create(){
        $response = new Response();
        $user = new UserModel();

        if(trim($_POST["username"])===''){
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = USERNAME_REQUIRED;
            $response->setResponse($jTableResult);
        
            return $response;
        }
        
        if (!preg_match('/^[a-z0-9_-]{3,16}$/', $_POST["username"])){
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = USERNAME_INVALID;
            $response->setResponse($jTableResult);
        
            return $response;
        }
        
        if(strlen(trim($_POST["userpassword"]))<8){
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = PASSWD_REQUIRED;
            $response->setResponse($jTableResult);
        
            return $response;
        }
        
        if(strlen(trim($_POST["realname"]))<4){
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = NAME_REQUIRED;
            $response->setResponse($jTableResult);
        
            return $response;
        }
        
        if(strlen(trim($_POST["email"]))<4){
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = EMAIL_REQUIRED;
            $response->setResponse($jTableResult);
        
            return $response;
        }
        
        if(!preg_match('/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/', $_POST["email"])){
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = EMAIL_INVALID;
            $response->setResponse($jTableResult);
        
            return $response;
        }
        
        $user->setUsername($_POST["username"]);
        $user->setPassword($_POST["userpassword"]);
        $user->setEmail($_POST["email"]);
        $user->setRealname($_POST["realname"]);
        $user->setDashboardRole($_POST["dashboard_role"]);
        $jTableResult = array();
        
        //Controlamos que no exista
        if(!$user->exists()){        
           $result = $user->save();                         
        
           if($result){
              $jTableResult['Result'] = "OK";
              $row["username"] = $user->getUsername();
              $row["realname"] = $user->getRealname();
              $row["email"] = $user->getEmail();
              $row["dashboard_role"] = $user->getDashboardRole();
              $jTableResult['Record'] = $row;    
           } else {
              $jTableResult['Result'] = "ERROR";
              $jTableResult['Message'] = "Ocurrió un error al crear el usuario.";
           }        
        } else {
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = "Ya existe un usuario con ese username.";
        }
        $response->setResponse($jTableResult);
        
        return $response;
    }
    
    public function update(){
        $response = new Response();
        $user = new UserModel();

        if(!isset($_POST['userID']) || trim($_POST['userID']) == '') {
          $jTableResult['Result'] = "ERROR";
          $jTableResult['Message'] = USERID_REQUIRED;
          $response->setResponse($jTableResult);
          return $response; 
        }

        if(trim($_POST["username"])===''){
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = USERNAME_REQUIRED;
            $response->setResponse($jTableResult);
            return $response;
        }

        if(strlen(trim($_POST["userpassword"])) < 8) {
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = PASSWD_REQUIRED;
            $response->setResponse($jTableResult);
            return $response;
        }

        $user->setId($_POST['userID']);
        $user->setUsername($_POST["username"]);
        $user->setPassword($_POST["userpassword"]);
        $user->setEmail($_POST["email"]);
        $user->setRealname($_POST["realname"]);
        $user->setDashboardRole($_POST["dashboard_role"]);                

        if(!$user->existsById()) {
          $jTableResult['Result'] = "ERROR";
          $jTableResult['Message'] = USER_NOT_EXISTS;
          $response->setResponse($jTableResult);
          return $response;
        } 

        if($user->usernameUsed()) {
          $jTableResult['Result'] = "ERROR";
          $jTableResult['Message'] = USERNAME_USED;
          $response->setResponse($jTableResult);
          return $response;
        } 

        $jTableResult = array();           
        $result = $user->save();                         
        if($result){
          $jTableResult['Result'] = "OK";
        } else {
          $jTableResult['Result'] = "ERROR";
          $jTableResult['Message'] = "Ocurrió un error al crear el usuario.";
        }        
        $response->setResponse($jTableResult);
        
        return $response;
    }
    
    public function delete(){
        $response = new Response();
        $user = new UserModel();

        $user->setUsername($_POST["username"]);            
        $jTableResult = array();
        
        //Controlamos que exista
        if($user->exists()){        
           $result = $user->delete();                         
        
           if($result){
              $jTableResult['Result'] = "OK";
           } else {
              $jTableResult['Result'] = "ERROR";
              $jTableResult['Message'] = "Ocurrió un error al intentar eliminar el usuario.";
           }        
        } else {
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = USERNAME_NOT_EXIST;
        }
        $response->setResponse($jTableResult);
        
        return $response;
    }
    
    public function getAll(){
        $response = new Response();
        $user = new UserModel();
        $from = 0; $limit = 10; $sortBy="";
        
        if(isset($_GET["jtStartIndex"])){
            $from = intval($_GET["jtStartIndex"]);
        }
        if(isset($_GET["jtPageSize"])){
            $limit = intval($_GET["jtPageSize"]);
        }
        if(isset($_GET["jtSorting"])){
            $sortBy = $_GET["jtSorting"];
        }
        
        $recordCount = $user->countAll();        
        $all_user = $user->getAll($from, $limit, $sortBy);   
        
        $rows=array();
        foreach ($all_user as $user){
            $row=array();
            $row["userID"] = $user->getId();
            $row["username"] = $user->getUsername();
            $row["userpassword"] = $user->getPassword();
            $row["realname"] = $user->getRealname();
            $row["email"] = $user->getEmail();
            $row["dashboard_role"] = $user->getDashboardRole();
            $manager_data = $user->getManager();
            $field='';
            foreach($manager_data as $rest_id => $rest_name){
                if($field!=''){ $field.=', '; }
                $field.='<span data-id="'.$rest_id.'">'.$rest_name.' ['.$rest_id.']</span>';
            }
            $field.='<span class="edit_manager" data-userid="'.$user->getId().'" data-username="'.$user->getUsername().'" data-names="'.  implode(',',array_values($manager_data)).'" data-ids="'.  implode(',',array_keys($manager_data)).'"> (EDITAR)</span>';
            $row["rest_manager"] = $field;
            $rows[]=$row;
        }
        
        $jTableResult = array();
	$jTableResult['Result'] = "OK";
	$jTableResult['TotalRecordCount'] = $recordCount;
	$jTableResult['Records'] = $rows;        
        
        $response->setResponse($jTableResult);
        
        return $response;
    }
    
    public function saveManager(){
        $response = new Response();
        $user = new UserModel();
        
        if(isset($_POST["manager"]) && count($_POST["manager"])>0){
            $userID = array_keys($_POST["manager"]);
            $userID = $userID[0];
            $restaurants = array_keys($_POST["manager"][$userID]);       
            
            $user->updateManagerRelations($userID, $restaurants);
        } else {
            $user->updateManagerRelations($_POST['user_id'], array());
        }
        
        $response->addMessage('Restaurants asociados actualizados para el manager');    
        $response->setResponse(true);
        
        return $response;
    }
    
    public function logout(){
        $response = new Response();
        
        unset($_SESSION["username"]);
        session_destroy();
        $response->addMessage(SESSION_CLOSE);    
        $response->setResponse(false);
        
        $this->setView('login');
        
        return $response;
    }
}
?>
