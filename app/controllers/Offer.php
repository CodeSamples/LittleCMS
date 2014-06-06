<?php

    class Offer extends Controller {

        public function getRestaurantOffer() {
            if(!isset($_GET['restaurant_id']) || trim($_GET['restaurant_id']) == '') {
                die('Missing params');
            }
            $response = new Response();
            $offer = new OfferModel();
            $offer->setRestaurant_id(intval($_GET['restaurant_id']));
            $offer->fetch();
            $response->setResponse(array(
                'offer' => $offer->getOffer(),
                'offer_plus' => $offer->getOffer_plus(),
                'offer_dated' => $offer->getOffer_dated(),
                'sal_phone' => $offer->getSal_phone()
                ));
            return $response;
        }

        public function save() {
            $required = array('restaurant_id', 'offer', 'offer_plus', 'offer_dated');
            foreach ($required as $value) {
                if(!isset($_POST[$value])) {
                    die('Missing params.');
                }
            }

            $response = new Response();

            $offer = new OfferModel();
            $offer->setRestaurant_id(intval($_POST['restaurant_id']));
            $offer->setOffer($_POST['offer']);
            $offer->setOffer_plus($_POST['offer_plus']);

            $offerDated = explode(',', $_POST['offer_dated']);
            $offerDatedToSave = array();
            foreach ($offerDated as $value) {
                $arr = explode(':', $value);
                $offerDatedToSave[$arr[0]] = explode('_', $arr[1]);
            }
            $offer->setOffer_dated(serialize($offerDatedToSave));
            $result = $offer->save();
            if(!$result) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(OFFER_UPDATE_ERROR);    
                $response->setResponse(false);
            } else {
                $response->addMessage(OFFER_UPDATE_SUCCESS);    
                $response->setResponse(true);
            }

            return $response;
        }

    }

?>