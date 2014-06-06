<?php

    class Gallery extends Controller {

        public function getGallery() {
            if(!isset($_GET['gallery_id'])) {
                die('Missing params.');
            }

            $response = new Response();
            $gallery = new GalleryModel();
            $gallery->setId(intval($_GET['gallery_id']));
            if($gallery->exists()) {
                $gallery->fetch();
                $response->setResponse($gallery);
            } else {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(GALLERY_NOT_EXISTS);
                $response->setResponse(false);
            }

            return $response;
        }


        public function save() {
            $required = array('restaurant_id', 'media_list');
            foreach ($required as $value) {
                if(!isset($_POST[$value])) {
                    die('Missing params.');
                }
            }

            if(!is_array($_POST['media_list']) ||  trim($_POST['restaurant_id']) == '') {
                die('Bad request');
            }

            $gallery = new GalleryModel();
            $gallery->setProperty_id(intval($_POST['restaurant_id']));
            if(isset($_POST['gallery_id']) && trim($_POST['gallery_id']) != '' && trim($_POST['gallery_id']) != '0') {
                $gallery->setId(intval($_POST['gallery_id']));
            } else {
                $gallery->setId($gallery->insert());
                $restaurant = new RestaurantDetailModel();
                $restaurant->fetchSingle(intval($_POST['restaurant_id']));
                $restaurant->setGallery_id($gallery->getId());
                $restaurant->save();
            }
            $gallery->setProperty_id(intval($_POST['restaurant_id']));
            $mediaList = array();

            foreach ($_POST['media_list'] as $key => $value) {
                $media = new GalleryMediaModel();
                $media->setGallery_id($gallery->getId());
                if(is_int($key)) {
                    $media->setExternal_id($key);
                } else {
                    $media->setExternal_id(0);
                }
                $data = explode(',', $value);
                $media->setFilename($data[0]);
                $media->setType($data[1]);
                $mediaList[] = $media;
            }
            $gallery->setMedia_list($mediaList);

            $response = new Response();
            $result = $gallery->save();
            $message = ($result) ? GALLERY_UPDATED_SUCCESS : GALLERY_UPDATED_FAILURE;
            $responseVal = array(
                'result' => $result,
                'message' => $message);
            $response->setResponse($responseVal);
            return $response;
        }


        public function syncFromSal() {
            $required = array('gallery_id', 'sal_id', 'restaurant_id');
            foreach ($required as $value) {
                if(!isset($_GET[$value]) || trim($_GET[$value]) == '') {
                    die('Missing params.');
                }
            }
            $gallery = new GalleryModel();
            $gallery->setProperty_id(intval($_GET['restaurant_id']));
            if(intval($_GET['gallery_id']) === 0) {
                $storedId = $gallery->getGalleryId();
                if(!$storedId) {
                    $gallery->setId($gallery->insert());
                    $restaurant = new RestaurantDetailModel();
                    $restaurant->fetchSingle(intval($_GET['restaurant_id']));
                    $restaurant->setGallery_id($gallery->getId());
                    $result = $restaurant->save();

                } else {
                    $gallery->setId($storedId);
                }
            } else {
                $gallery->setId(intval($_GET['gallery_id']));    
            }
            $gallery->syncFromSal(intval($_GET['sal_id']));
            $gallery->fetch();
            $response = new Response();
            $response->setResponse($gallery);
            return $response;
        }

    }

?>