<?php

class Feature extends Controller {

    public function getAll() {
        $response = new Response();
        $f = new FeatureModel();

        $list = $f->getAll();

        $response->setResponse($list);

        return $response;
    }

    public function save() {
        $response = new Response();

        try {
            $features = $_POST["feature"];

            $values = array();
            foreach ($features as $key => $feature) {
                $value = array();
                $values[$key] = array();
                switch ($feature["type"]) {
                    case 'int':
                        $value = array('int', array("from" => $feature["from"], "to" => $feature["to"]));
                        break;
                    case 'options':
                        $value = array('options', array("options" => explode(',', $feature["options"])));
                        break;
                    case 'boolean':
                        $value = array('boolean');
                        break;
                    case 'text':
                        $value = array('text');
                        break;
                }
                if(trim($feature["name"])===''){
                    $response->setCode(Response::ERROR_CODE);
                    $response->addError(NAME_REQUIRED);
                    $response->setResponse(false);
                    return $response;
                }
                
                $values[$key]["name"] = $feature["name"];
                $values[$key]["type"] = serialize($value);
            }

            $f = new FeatureModel();
            $f->saveFeatures($values);

            $response->addMessage(FEATURE_UPDATE);
            $response->setResponse(true);
        } catch (Exception $e) {
            $response->setCode(Response::ERROR_CODE);
            $response->addError(FEATURE_UPDATE_FAIL);
            $response->setResponse(false);
        }

        return $response;
    }

    public function delete() {
        $response = new Response();
        $f = new FeatureModel();

        if (isset($_POST["ID"])) {
            $f->setID($_POST["ID"]);
            if ($f->delete()) {
                $response->addMessage(FEATURE_DELETE);
                $response->setResponse(true);
            } else {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(FEATURE_DELETE_FAIL);
            }
        }

        return $response;
    }

}

?>