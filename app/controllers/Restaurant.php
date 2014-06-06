<?php

	class Restaurant extends Controller {

		public function listAll() {
			$response = new Response();
			$restaurantList = new RestaurantListModel();
			$restaurantList->fetchRestaurantList($_SESSION['userObject']);
			$returnList = array();
			foreach ($restaurantList->getRestaurantList() as $single) {
				$obj = new stdClass();
				$obj->id = $single->getId();
				$obj->name = $single->getName();
				$returnList[] = $obj;
			}
			$response->setResponse(json_encode($returnList));
			return $response;
		}

		public function listPending() {
			$response = new Response();
			$restaurantList = new RestaurantListModel();
			$restaurantList->fetchPendingRestaurantList();
			$returnList = array();
			foreach ($restaurantList->getRestaurantList() as $single) {
				$obj = new stdClass();
				$obj->id = $single->getId();
				$obj->name = $single->getName();
				$returnList[] = $obj;
			}
			$response->setResponse($returnList);
			return $response;
		}

		public function detail() {
			if(!isset($_GET['restaurant_id'])) {
				die('Missing params.');
			}
			$response = new Response();
			$restaurant = new RestaurantDetailModel();
			$restaurant->fetchSingle($_GET['restaurant_id']);
			$response->setResponse($restaurant);
			return $response;
		}

        public function childDetail() {
            $blockedValues = array('false', '0', 'undefined', 'null', '');
            $requiredParams = array('restaurant_id', 'parent_id');
            foreach ($requiredParams as $value) {
                if(!isset($_GET[$value]) 
                    || in_array($_GET[$value], $blockedValues)) {
                    die('Missing params.');
                }
            }

            $response = new Response();
            $restaurant = new RestaurantDetailModel();
            $restaurant->setId_sal(intval($_GET['restaurant_id']));
            $exists = $restaurant->existsSalId();
            if($exists) {
                $restaurant->fetchSingle($exists->id);
                if($restaurant->getId() != $_GET['parent_id']) {
                    $parent = $restaurant->getFranchise_parent();
                    if($parent == 0) {
                        $franchise = new RestaurantDetailModel();
                        $franchise->fetchSingle(intval($_GET['parent_id']), true);

                        $restaurantDash = new RestaurantDashModel();
                        $restaurantDash->setId($restaurant->getId());
                        $restaurantDash->setPackage_id($franchise->getPackage_id());
                        $restaurantDash->setDashboard($franchise->getDashboard());
                        $dashResult = $restaurantDash->save(false);    

                        $restaurant->setFranchise_parent($franchise->getId());
                        $restaurant->save();
                    }
                }
            } else {
                $_GET["sal_id"] = $restaurant->getId_sal();
                $importResponse = $this->importSalWeb();
                if($importResponse->getCode() != Response::ERROR_CODE) {
                    $restaurant = $importResponse->getResponse();
                    if($restaurant->getId() != $_GET['parent_id']) {
                        $parent = $restaurant->getFranchise_parent();
                        if($parent > 0) {
                            $franchise = new RestaurantDetailModel();
                            $franchise->fetchSingle($parent, true);

                            $restaurantDash = new RestaurantDashModel();
                            $restaurantDash->setId($restaurant->getId());
                            $restaurantDash->setPackage_id($franchise->getPackage_id());
                            $restaurantDash->setDashboard($franchise->getDashboard());
                            $dashResult = $restaurantDash->save(false);    
                        } else {
                            $franchise = new RestaurantDetailModel();
                            $franchise->fetchSingle(intval($_GET['parent_id']), true);

                            $restaurantDash = new RestaurantDashModel();
                            $restaurantDash->setId($restaurant->getId());
                            $restaurantDash->setPackage_id($franchise->getPackage_id());
                            $restaurantDash->setDashboard($franchise->getDashboard());
                            $dashResult = $restaurantDash->save(false);    

                            $restaurant->setFranchise_parent($franchise->getId());
                            $restaurant->save();
                        }
                    }
                } else {
                    $response->setCode(Response::ERROR_CODE);
                    $response->addError(RESTAURANT_VALIDATE_ID);
                    $response->setResponse(false);
                }
            }

            $response->setResponse($restaurant);
            return $response;
        }

		public function save() {
			if(!isset($_POST['restaurant_id'])) {
				die('Missing params.');
			}

			$response = new Response();
			$restaurant = new RestaurantDetailModel();
			$restaurant->fetchSingle($_POST['restaurant_id']);
            $features = PackageModel::getPackageFeaturesByPropertyId($_POST['restaurant_id']);

			$movePdf = ($restaurant->getMenu_pdf() != $_POST['menu_pdf']) ? true : false;
            $elements = (isset($_POST['elements'])) ? $_POST['elements'] : array();
            $ambience = (isset($_POST['ambience'])) ? $_POST['ambience'] : array();

			$restaurant->setContent($_POST['content']);
			$restaurant->setExcerpt($_POST['excerpt']);

			if(isset($features['logo']) && $features['logo'] == '1') {
                $restaurant->setLogo($_POST['logo']);
            }

			$restaurant->setElements($elements);
			$restaurant->setAmbience($ambience);

            if (isset($_POST['price'])) {
			 $restaurant->setPrice($_POST['price']);
            }
			$restaurant->setTime($_POST['time']);
			$restaurant->setPhone($_POST['phone']);
			$restaurant->setMail($_POST['mail']);
			$restaurant->setMenu_pdf($_POST['menu_pdf']);
			$restaurant->setMenu_locu($_POST['menu_locu']);
			$restaurant->setFacebook($_POST['facebook']);
			$restaurant->setTwitter($_POST['twitter']);
			$result = $restaurant->save();
			if($result && $movePdf && trim($_POST['menu_pdf']) != '') {
				$imageRelPath = str_replace(CDN_BASE_URL, '', $restaurant->getMenu_pdf());
				$oldKey = CDN_BUCKET.DS.$imageRelPath;
				$newKey = CDN_APP_DIR.get_class($this).DS.$restaurant->getId().DS.basename($restaurant->getMenu_pdf());
				$restaurant->setMenu_pdf(File::moveUploadedObject($oldKey,$newKey));
				$restaurant->save();
			}
			$message = ($result) ? RESTAURANT_UPDATED_SUCCESS : RESTAURANT_UPDATED_FAILURE;
			$responseVal = array(
				'result' => $result,
				'message' => $message);
			$response->setResponse(json_encode($responseVal));
			return $response;
		}

		public function syncFromSal() {
			if(!isset($_GET['restaurant_id'])) {
				die('Missing params.');
			}

			$response = new Response();
			$restaurant = new RestaurantDetailModel();
			$restaurant->syncFromSal(intval($_GET['restaurant_id']));
			$response->setResponse(json_encode($restaurant));
			return $response;
		}

		public function approve() {
			$requiredParams = array('restaurant_id', 'redirect_url');
			$blockedValues = array('false', '0', 'undefined', 'null', '');

			foreach ($requiredParams as $param) {
				if(!isset($_GET[$param]) || in_array($_GET[$param], $blockedValues)) {
					die('Missing params.');
					break;
				}
			}

			$restaurant = new RestaurantDetailModel();
			$restaurant->setId(intval($_GET['restaurant_id']));
			$restaurant->syncToSal();

			$offer = new OfferModel();
            $offer->setRestaurant_id(intval($_GET['restaurant_id']));
            $offer->syncToSal();

            $gallery = new GalleryModel();
            $gallery->setProperty_id(intval($_GET['restaurant_id']));
            $gallery->syncToSal();

			$redirect = explode('/', $_GET['redirect_url']);
			$request = RequestManager::getInstance();
                        $request->redirect($redirect[0],$redirect[1]);
		}

        public function reject() {
            $requiredParams = array('restaurant_id', 'redirect_url');
            $blockedValues = array('false', '0', 'undefined', 'null', '');

            foreach ($requiredParams as $param) {
                if(!isset($_GET[$param]) || in_array($_GET[$param], $blockedValues)) {
                    die('Missing params.');
                    break;
                }
            }

            $restaurant = new RestaurantDetailModel();
            $restaurant->syncFromSal(intval($_GET['restaurant_id']), true);

            $redirect = explode('/', $_GET['redirect_url']);
            $request = RequestManager::getInstance();
            $request->redirect($redirect[0],$redirect[1]);
        }
            
        public function importSalWeb(){
            $response = new Response();
            $restaurant = new RestaurantDetailModel();
            
            if(intval($_GET["sal_id"])==0){
                $response->setCode(Response::ERROR_CODE);
                $response->addError(RESTAURANT_VALIDATE_ID);
                $response->setResponse(false);
                return $response;
            }
            
            $restaurant->setId_sal(intval($_GET["sal_id"]));
            $obj = $restaurant->existsSalId();                                       
            
            try {
                                    
                if(is_object($obj) && is_numeric($obj->id) && $obj->id>0){     
                   if($obj->dashboard==1){
                       $response->setCode(Response::MESSAGE_CODE);
                       $response->addMessage(RESTAURANT_DASHBOARD_SELECT);
                       $response->setResponse($obj);
                       return $response;
                   } 
                    
                   $restaurant = new RestaurantDetailModel(); 
                   $restaurant->setId($obj->id);
                   $restaurant->fetchSingle($obj->id);
                   $restaurant->setDashboard(1);
                   $restaurant->save();
                   $response->setCode(Response::MESSAGE_CODE);
                   $response->addMessage(RESTAURANT_DASHBOARD_ACTIVE);
                   $response->setResponse($restaurant);
                } else {
                    $restaurant_new = new RestaurantDetailModel();
                    if($restaurant_new->existsSalWeb(intval($_GET["sal_id"]))){
                        //Aun no existe en reserva, si existe el en Sal Web creamos uno vacio y sincronizamos desde sal web                        
                        $restaurant_new->save();
                        $restaurant_new->setId_sal(intval($_GET["sal_id"]));       
                        $restaurant_new->setDashboard(1);                            
                        $restaurant_new->syncFromSal($restaurant_new->getId(),false);   
                        $response->setCode(Response::MESSAGE_CODE);
                        $response->addMessage(sprintf(RESTAURANT_DASHBOARD_ADD,$restaurant_new->getName()));
                        $response->setResponse($restaurant_new);
                    } else {
                        $response->setCode(Response::ERROR_CODE);
                        $response->addError(RESTAURANT_VALIDATE_SAL_ID);
                        $response->setResponse(false);
                    }
                }
            
            } catch (Exception $e) {           
                    $response->setCode(Response::ERROR_CODE);
                    $response->addError(RESTAURANT_SYNC_ERROR);
                    $response->setResponse(false);
                    return $response;
            }
                                
            return $response;
        }

        public function assignment() {
            $response = new Response();
            $list = new RestaurantListModel();
            $list->fetchAllRestaurantList();
            $packages = new PackageModel();
            
            $responseVal = array(
                'restaurants' => $list->getRestaurantList(),
                'packages' => $packages->getAll(),
                'foodTypes' => $list->getFoodTypes()
                );
            $response->setResponse($responseVal);
            return $response;
        }

        public function saveAssignment() {
            $response = new Response();
            $requiredParams = array('restaurant_id','dashboard','package');
            $blockedValues = array('false', 'undefined', 'null', '');
            foreach ($requiredParams as $param) {
                if(!isset($_POST[$param]) || in_array($_POST[$param], $blockedValues)) {
                    $response->setCode(Response::ERROR_CODE);
                    $response->addError(MISSING_FIELDS);
                    $response->setResponse(false);
                    return $response;
                    break;
                }
            }

            $restaurantDash = new RestaurantDashModel();
            $restaurantDash->setId(intval($_POST['restaurant_id']));
            $restaurantDash->setPackage_id(intval($_POST['package']));
            $restaurantDash->setDashboard(intval($_POST['dashboard']));
            if($restaurantDash->save()) {
                $response->addMessage(RESTAURANT_UPDATED_SUCCESS);
                $response->setResponse(true);
            } else {
                $response->addMessage(RESTAURANT_UPDATED_FAILURE);
                $response->setResponse(false);
            }
            return $response;
        }

        public function getRestaurantFeatures() {
            $response = new Response();
            if(!isset($_GET['restaurant_id'])) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(MISSING_FIELDS);
                $response->setResponse(false);
                return $response;
            }

            $restaurant = new RestaurantDetailModel();
            $restaurant->fetchSingle(intval($_GET['restaurant_id']));
            $package = new PackageModel();
            $package->setID($restaurant->getPackage_id());
            $features = $this->parseRestaurantFeatures($package->getPackageFeatures());

            $reserveStatus = RestaurantDetailModel::getReserveStatus(intval($_GET['restaurant_id']));
            $features['reserve'] = ($reserveStatus == 'active') ? true : false;

            $response->setResponse($features);
            return $response;            
        }

        public function saveNotifications() {
            $response = new Response();
            $requiredParams = array('restaurant_id','share','comment','rate','contact_form');
            $blockedValues = array('false', 'undefined', 'null', '0');
            foreach ($requiredParams as $param) {
                if(!isset($_POST[$param]) || in_array($_POST[$param], $blockedValues)) {
                    $response->setCode(Response::ERROR_CODE);
                    $response->addError(MISSING_FIELDS);
                    $response->setResponse(false);
                    return $response;
                    break;
                }
            }

            $restaurant = new RestaurantNotificationsModel();
            if(!$restaurant->fetch(intval($_POST['restaurant_id']))) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(FILE_ERROR_INTERNAL);
                $response->setResponse(false);
                return $response;   
            }

            $restaurant->share = $_POST['share'];
            $restaurant->comment = $_POST['comment'];
            $restaurant->rate = $_POST['rate'];
            $restaurant->contact_form = $_POST['contact_form'];
            if(!$restaurant->save()) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(RESTAURANT_UPDATED_FAILURE);
                $response->setResponse(false);
                return $response;      
            }

            $response->addMessage(RESTAURANT_UPDATED_SUCCESS);
            $response->setResponse($restaurant);
            return $response;
        }

        public function getNotifications() {
            $response = new Response();
            if(!isset($_GET['restaurant_id'])&&!isset($_GET['salID'])) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(MISSING_FIELDS);
                $response->setResponse(false);
                return $response;
            }
            
            if(isset($_GET['salID'])){
                //Obtengo el ID de dashboard a partir del ID de Sal Web en restaurant_id
                $rest = new RestaurantDetailModel();
                $rest->fetchSingle(intval($_GET['salID']),true);
                $ID = $rest->getId();
            } else {
                $ID = intval($_GET['restaurant_id']);
            }    
            
            $restaurant = new RestaurantNotificationsModel();
            if(!$restaurant->fetch($ID)) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(FILE_ERROR_INTERNAL);
                $response->setResponse(false);
                return $response;   
            }
            $response->setResponse($restaurant);
            return $response;
        }

        public function getFranchiseChilds() {
            $response = new Response();
            if(!isset($_GET['restaurant_id'])) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(MISSING_FIELDS);
                $response->setResponse(false);
                return $response;
            }
            $restaurant = new RestaurantDetailModel();
            $restaurant->fetchSingle(intval($_GET['restaurant_id']));
            $franchiseChilds = new FranchiseChildsModel($restaurant->getId_sal());
            $response->setResponse($franchiseChilds->childs);
            return $response;
        }

        public function setFranchise(){
            $response = new Response();
            
            if(!isset($_GET["salId"]) || !isset($_GET["franchise"])){
                $response->setCode(Response::ERROR_CODE);
                $response->addError(MISSING_FIELDS);
                $response->setResponse(false);
                return $response;
            }
            
            $rest = new RestaurantDetailModel();
            $rest->fetchSingle($_GET["salId"],true);
           
            //Si existe con ese ID            
            if($rest->getId_sal()==$_GET["salId"]){
                $franchise = ($_GET["franchise"]==1) ? 1 : 0;
                $rest->setFranchise($franchise);
                $rest->save();
            } else {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(RESTAURANT_VALIDATE_DASH_ID);
                $response->setResponse(false);
                return $response;
            }
            
            $response->setResponse(true);
            
            return $response;
        }

        public function salPreview() {
            $response = new Response();
            
            ini_set('display_errors',0);
            
            if(!isset($_GET['restaurant_id'])) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(MISSING_FIELDS);
                $response->setResponse(false);
                return $response;
            }

            $detail = new RestaurantDetailModel();
            $detail->fetchSingle(intval($_GET['restaurant_id']));
            if(!isset($detail->id)) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(RESTAURANT_PREVIEW_ID_ERROR);
                $response->setResponse(false);
                return $response;
            }
            $reserveStatus = RestaurantDetailModel::getReserveStatus($detail->getId());
            $reserveStatus = ($reserveStatus == 'active') ? true : false;
            
            //Obtenemos el estado actual en Sal! Web
            $rest_actual_sal = new RestaurantDetailModel(); 
            $rest_actual_sal->fetchSingle(intval($_GET['restaurant_id']));  
            $rest_actual_sal->fetchFromSal();
            
            $gallery = new GalleryModel();
            $gallery->setId($detail->gallery_id);
            $gallery->fetch();
            //Obtenemos el estado actual en Sal! Web para la gallery
            $gallery_sal = new GalleryModel();
            $gallery_sal->fetchMediaListFromSal($rest_actual_sal->getId_sal());
            
            $offer = false;
            if($detail->franchise) {
                $offer = new CouponModel();
                $offer->setProperty_id($detail->id);
                $offer->fetchActual();
            } else {
                $offer = new OfferModel();
                $offer->setRestaurant_id($detail->id);
                $offer->fetch();    
                
                $offer_sal = new OfferModel();
                $offer_sal->fetchFromSal($rest_actual_sal->getId_sal());
            }

            $response->setResponse(array(
                'detail' => $detail,
                'detail_sal' => $rest_actual_sal,
                'gallery' => $gallery,
                'gallery_sal' => $gallery_sal,
                'offer' => $offer,
                'offer_sal' => $offer_sal,
                'reserve' => $reserveStatus
                ));
            return $response;
        }

        public function updateFromSal() {
            $response = new Response();
            if(!isset($_POST['salId'])) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(MISSING_FIELDS);
                $response->setResponse(false);
                return $response;
            }

            $restaurant = new RestaurantDetailModel();
            $restaurant->fetchSingle($_POST['salId'], true);
            $gallery = new GalleryModel();
            $gallery->setId($restaurant->getGallery_id());
            $gallery->fetch();

            $restaurant->setContent($_POST['data']['detail']['content']);
            $restaurant->setExcerpt($_POST['data']['detail']['excerpt']);
            $restaurant->setAddress($_POST['data']['detail']['address']);
            $restaurant->setLat($_POST['data']['detail']['lat']);
            $restaurant->setLng($_POST['data']['detail']['lng']);
            $restaurant->setCity(html_entity_decode($_POST['data']['detail']['city']));
            $restaurant->setFood($_POST['data']['detail']['food']);
            $restaurant->setTags($_POST['data']['detail']['tags']);
            $restaurant->setKeywords($_POST['data']['detail']['keywords']);
            $restaurant->setLogo($_POST['data']['detail']['logo']);
            $restaurant->setTime($_POST['data']['detail']['time']);
            $restaurant->setMail($_POST['data']['detail']['mail']);
            $restaurant->setMenu_pdf($_POST['data']['detail']['menu_pdf']);
            $restaurant->setMenu_locu($_POST['data']['detail']['menu_locu']);
            $restaurant->setFacebook($_POST['data']['detail']['facebook']);
            $restaurant->setTwitter($_POST['data']['detail']['twitter']);

            $dashboardLocalElements = array();
            foreach ($_POST['data']['detail']['elements'] as $element) {
                foreach (unserialize(SAL_LOCAL_ELEMENTS) as $key => $value) {
                    if(strtolower(trim($element)) == strtolower(trim($value))) {
                        $dashboardLocalElements[] = $key;
                        break;
                    }
                }
            }
            $restaurant->setElements($dashboardLocalElements);

            $dashboardAmbience = array();
            foreach ($_POST['data']['detail']['ambience'] as $ambience) {
                foreach (unserialize(SAL_AMBIENCES) as $key => $value) {
                    if(strtolower(trim($ambience)) == strtolower(trim($value))) {
                        $dashboardAmbience[] = $key;
                        break;
                    }
                }
            }
            $restaurant->setAmbience($dashboardAmbience);

            $dashboardPrice = '';
            foreach (unserialize(SAL_PRICES) as $key => $value) {
                if(strtolower(trim($_POST['data']['detail']['price'])) == strtolower(trim($value))) {
                    $dashboardPrice = $key;
                    break;
                }
            }

            $media_list = array();
            foreach ($_POST['data']['gallery']['images'] as $key => $value) {
                $media = new GalleryMediaModel();
                $media->setGallery_id($gallery->getId());
                $media->setExternal_id($key);
                $media->setFilename($value['file']);
                $media->setType($value['type']);
                $media_list[] = $media;
            }

            if(is_array($_POST['data']['gallery']['videos']) 
                && sizeof($_POST['data']['gallery']['videos']) > 0) {
                $_GET['bumbia_ids'] = implode(',', $_POST['data']['gallery']['videos']);
                $bumbiaVideos = Video::getVideos();
                if($bumbiaVideos) {
                    foreach ($bumbiaVideos->response as $vid) {
                        $media = new GalleryMediaModel();
                        $media->setGallery_id($gallery->getId());
                        $media->setExternal_id($vid->id);
                        $media->setFilename($vid->thumb);
                        $media->setType(BUMBIA_FILETYPE);
                        $media_list[] = $media;
                    }
                }    
            }
            $gallery->setMedia_list($media_list);

            $restaurant->save();
            $gallery->save();
            $response->setResponse(true);
            return $response;
        }
        
        private function parseRestaurantFeatures($features) {
            $returnVal = array();
            foreach ($features as $value) {
                $returnVal[$value->name] = $value->value;
            }
            return $returnVal;
        }

	}

?>