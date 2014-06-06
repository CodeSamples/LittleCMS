<?php

class Dashboard extends Controller{

    protected function baseResponse() {
        $responseVal = new stdClass();
        $responseVal->userRealName = $_SESSION['userObject']->getRealname();
        $responseVal->userName = $_SESSION['userObject']->getUsername();
        return $responseVal;
    }
    
    public function home(){
        $response = new Response();
        $userRole = intval($_SESSION['userObject']->getDashboardRole());
        if($userRole > ROLE_ADMIN) {
            $request = RequestManager::getInstance();
            $request->redirect("Dashboard","manager");
        } else {
            //Redireccionar siempre user admin tambien.
            $request = RequestManager::getInstance();
            //$request->redirect("Dashboard","admin");
            $request->redirect("Dashboard","manager");
        }

        $responseVal = $this->baseResponse();
    	$responseVal->allowedAreas = array(
    		'admin' => ADMIN_AREA,
    		'manager' => MANAGER_AREA);
 
        $response->setResponse($responseVal);
        
        return $response;
    }

    public function manager() {
        $response = new Response();
        $responseVal = $this->baseResponse();

        if(isset($_GET['restaurant_id']) && trim($_GET['restaurant_id']) != '') {
            $isAdmin = ($_SESSION['userObject']->getDashboardRole() == ROLE_ADMIN) ? true : false;
            $access = $_SESSION['userObject']->validateRestaurantAccess(
                intval($_GET['restaurant_id']));
            if($isAdmin || $access) {
                $responseVal->restaurant_id = intval($_GET['restaurant_id']);
            }
        } 

        $response->setResponse($responseVal);
        return $response;
    }
    
    public function admin(){
        $response = new Response();

        $userRole = intval($_SESSION['userObject']->getDashboardRole());
        
        $responseVal = $this->baseResponse();
        $response->setResponse($responseVal);
        
        return $response;
    }
    
    public function userState(){
        $response = new Response();
        $settings = array();        
        if(isset($_COOKIE["dashboard_settings"])){
            $settings = json_decode($_COOKIE["dashboard_settings"]);
        }
        
        if(isset($_POST["setting"])){
            switch($_POST["setting"]){
                case 'default_admin_tab':
                    if(is_array($settings)){
                        $settings["default_admin_tab"]=intval($_POST["value"]);    
                    } elseif(is_object($settings)) {
                        $settings->default_admin_tab=intval($_POST["value"]);
                    }
                    break;
            }
            
            setcookie('dashboard_settings', json_encode($settings), time()+2678400, '/', BASE_URL);
        }
       
        $response->setResponse(true);
        
        return $response;
    }
}

?>