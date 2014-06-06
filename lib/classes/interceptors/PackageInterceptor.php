<?php

    class PackageInterceptor extends Interceptor{
        
        public function afterAction($response) {
            //pass
        }

        public function beforeAction($controller) {
            if(!isset($_SESSION['userObject'])) {
                $this->stopExecution();                
            }

            if($_SESSION['userObject']->getDashboardRole() > ROLE_ADMIN) {

                if(!isset($_REQUEST['restaurant_id'])) {
                    $this->stopExecution();       
                }

                $restaurant_id = intval($_REQUEST['restaurant_id']);
                $request = RequestManager::getInstance();
                $features = PackageModel::getPackageFeaturesByPropertyId($restaurant_id);

                switch ($request->getController()) {
                    case 'Restaurant':
                        if(!isset($features['content_alter']) || $features['content_alter'] != '1') {
                            $this->stopExecution();
                        }
                        break;

                    case 'Coupon':
                        if(!RestaurantDetailModel::isFranchise($restaurant_id)) {
                            $this->stopExecution(RESTAURANT_FRANCHISE_ONLY);
                        }
                        if(!isset($features['coupon']) || $features['coupon'] != '1') {
                            $this->stopExecution();   
                        }
                        break;
                    
                    case 'Offer':
                        if(RestaurantDetailModel::isFranchise($restaurant_id)) {
                            $this->stopExecution(RESTAURANT_FRANCHISE_ERROR);
                        }

                        $status = RestaurantDetailModel::getReserveStatus($restaurant_id);
                        if($status != 'active') {
                            $this->stopExecution();
                        }
                        break;

                    case 'Gallery':
                        if(!isset($features['pics']) || !isset($features['videos'])) {
                            $this->stopExecution();
                        }

                        if(is_array($_REQUEST['media_list']) && sizeof($_REQUEST['media_list']) > 0) {
                            $imgs = 0;
                            $vids = 0;
                            foreach ($_REQUEST['media_list'] as $media) {
                                $data = explode(',', $media);
                                if(preg_match('/image/', $data[1])) {
                                    $imgs++;
                                } else {
                                    $vids++;
                                }
                            }
                        }

                        if($imgs > intval($features['pics'])) {
                            $this->stopExecution(MEDIA_IMG_LIMIT);
                        }

                        if($vids > intval($features['videos'])) {
                            $this->stopExecution(MEDIA_VIDEO_LIMIT);
                        }
                        break;
                }
            }
        }    

        private function stopExecution($msg = RESTAURANT_PACKAGE_ERROR) {
            $response = new Response();
            $response->setCode(Response::ERROR_CODE);
            $response->addError($msg);
            $response->setStatus(false);
            $response->setResponse(false);
            echo json_encode($response);
            die();
        }
    }

?>