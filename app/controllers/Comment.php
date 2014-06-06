<?php

    class Comment extends Controller {

        public function getComments() {

            if(!isset($_GET['restaurant_id'])) {
                die('Missing params');
            }

            $salId = null;
            if(isset($_GET['is_sal']) && $_GET['is_sal'] == '1') {
                $salId = intval($_GET['restaurant_id']);    
            } else {
                $restaurant = new RestaurantDetailModel();
                $restaurant->fetchSingle(intval($_GET['restaurant_id']));    
                $salId = $restaurant->getId_sal(); 
            }

            if(null === $salId) {
                die('Restaurant without sal_id');
            }

            $response = new Response();

            if($salId != '') {
                require_once(EXTERNAL_LIB_PATH . 'Gigya_PHP_SDK/GSSDK.php'); 
                $request = new GSRequest(GIGYA_API_KEY, GIGYA_SECRET_KEY, 'comments.getComments');
                $request->setParam('categoryID', 'comentarios');
                $request->setParam('streamID', 'comments-' . $salId);
                $gigyaResponse = $request->send();
                if($gigyaResponse->getErrorCode() == 0) { 
                    $resp = array(
                        'restaurant_id' => $salId,
                        'gigyaComments' => json_decode($gigyaResponse->getResponseText())
                        );
                    $response->setResponse($resp);
                } else {
                    $response->setCode(Response::ERROR_CODE);
                    $response->addError($gigyaResponse->getErrorMessage());
                    $response->setResponse(false);
                }    
            } else {
                $response->setCode(Response::ERROR_CODE);
                $response->addError('Malformed sal id.');
                $response->setResponse(false);
            }

            return $response;
        }


        public function getRatings() {

            if(!isset($_GET['restaurant_id'])) {
                die('Missing params');
            }

            $salId = null;
            $restaurant = new RestaurantDetailModel();
            if(isset($_GET['is_sal']) && $_GET['is_sal'] == '1') {
                $salId = intval($_GET['restaurant_id']);    
                $restaurant->fetchSingle($salId, true);    
            } else {
                $restaurant->fetchSingle(intval($_GET['restaurant_id']));    
                $salId = $restaurant->getId_sal(); 
            }

            if(null === $salId) {
                die('Restaurant without sal_id');
            }

            if(trim($salId) == '' || !is_numeric($salId)) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError('Malformed sal id.');
                $response->setResponse(false);
                return $response;
            }

            $childs = false;
            if($restaurant->franchise == true) {
                $model = new FranchiseChildsModel($salId);
                if(sizeof($model->childs) > 0) {
                    $childs = array();
                    foreach ($model->childs as $key => $value) {
                        $childs[] = $key;
                    }
                }
            }

            $response = new Response();
            require_once(EXTERNAL_LIB_PATH . 'Gigya_PHP_SDK/GSSDK.php'); 
            $cats = array('ambiente', 'comida', 'servicio');
            $gigyaResponses = array();
            
            foreach ($cats as $cat) {
                $request = new GSRequest(GIGYA_API_KEY, GIGYA_SECRET_KEY, 'comments.getStreamInfo');
                $request->setParam('categoryID', $cat);
                $request->setParam('streamID', $cat . '-' . $salId);
                if($childs !== false) {
                    $ids = "['".$cat."-".implode("','".$cat."-", $childs)."']";
                    $request->setParam('streamIDs', $ids);
                }

                $gigyaResponse = $request->send();
                if($gigyaResponse->getErrorCode() == 0) { 
                    $gigyaResponses[$cat] = json_decode($gigyaResponse->getResponseText());
                } else {
                    $gigyaResponses[$cat] = false;
                }
            }

            $ratings = array(
                'ambiente' => array(
                    'overall' => 0,
                    'count' => 0
                    ),
                'comida' => array(
                    'overall' => 0,
                    'count' => 0
                    ),
                'servicio' => array(
                    'overall' => 0,
                    'count' => 0
                    ),
                'total' => array(
                    'overall' => 0,
                    'count' => 0
                    )
                );

            foreach ($gigyaResponses as $key => $value) {
                if(is_array($value->streamInfo)) {
                    $votedStreams = 0;
                    $initTotalOverallValue = $ratings['total']['overall'];
                    $initTotalCountValue = $ratings['total']['count'];
                    foreach ($value->streamInfo as $singleStream) {
                        $this->parseRatingData($singleStream, $key, $ratings);
                        if($ratings['total']['count'] > $initTotalCountValue) {
                            $votedStreams++;
                        }
                    }
                    if($votedStreams > 0) {
                        $ratings[$key]['overall'] = round(round(
                            $ratings[$key]['overall'] / $votedStreams,1)*2)/2;    
                    } else {
                        $ratings[$key]['overall'] = round(round(
                            $ratings[$key]['overall'],1)*2)/2;    
                    }
                } else {
                    $this->parseRatingData($value->streamInfo, $key, $ratings);
                }
            }

            
            if($childs === false) {
                $ratings['total']['overall'] = round(round($ratings['total']['overall'] / 3, 1)*2)/2;
                $ratings['total']['count'] = floor($ratings['total']['count'] / 3);
            } else {
                $ratings['total']['overall'] = round(round(($ratings['ambiente']['overall'] +
                    $ratings['comida']['overall'] + 
                    $ratings['servicio']['overall']) / 3,1)*2)/2;
                $ratings['total']['count'] = floor($ratings['total']['count'] / 3);
            }
            $response->setResponse($ratings);

            return $response;
        }

        protected function parseRatingData($gigyaData, $key, &$ref) {
            $ref[$key]['overall'] += $gigyaData->avgRatings->_overall;
            $ref[$key]['count'] += $gigyaData->commentCount;
            $ref['total']['overall'] += $gigyaData->avgRatings->_overall;
            $ref['total']['count'] += $gigyaData->commentCount;
        }

    }

?>