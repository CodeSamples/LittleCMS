<?php

class Package extends Controller {

    public function getAll() {
        $response = new Response();
        $p = new PackageModel();
        $f = new FeatureModel();

        $list = array();
        $list["features"] = $f->getAll();
        $list["packages"] = $p->getAllFeatures();

        $response->setResponse($list);

        return $response;
    }

    public function save() {
        $response = new Response();
        $p = new PackageModel();

        if (isset($_POST["package-name"])) {
            if (trim($_POST["package-name"]) === '') {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(NAME_REQUIRED);
                $response->setResponse(false);
                return $response;
            }

            $p->setName($_POST["package-name"]);
            if ($p->save()) {
                $response->addMessage(PACKAGE_UPDATE);
                $response->setResponse(true);
            } else {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(PACKAGE_UPDATE_FAIL);
                $response->setResponse(false);
            }
        } else {
            //Guardando features de un paquete existente
            if (!isset($_POST["ID"]) || !isset($_POST["name"])) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(PACKAGE_UPDATE_FAIL);
                $response->setResponse(false);
                return $response;
            }
            //Actualizo datos del paquete
            $p->setID($_POST["ID"]);
            $p->setName($_POST["name"]);
            $features = $_POST["feature"][$p->getID()];
            if ($p->save() && $p->saveFeatures($features)) {
                $response->addMessage(PACKAGE_UPDATE);
                $response->setResponse(true);
            }
        }

        return $response;
    }

    public function delete() {
        $response = new Response();
        $p = new PackageModel();
        $response->setResponse(false);

        if (isset($_POST["ID"])) {
            $p->setID($_POST["ID"]);
            if ($p->delete()) {
                $response->addMessage(PACKAGE_DELETE);
                $response->setResponse(true);
            } else {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(PACKAGE_DELETE_FAIL);
            }
        }

        return $response;
    }

    public function generateJsonPackages() {
        $response = new Response();        
        $p = new PackageModel();
        $list = $p->getAllFeaturesApi();

        $prefix = '';
        if(ENVIRONMENT == DEV_ENVIRONMENT) {
            $prefix = 'dev-';
        }
        $result = file_put_contents(TMP_PATH.$prefix.'packages.json', json_encode($list));

        if ($result === false) {
            $response->addError(FILE_ERROR_TMP);
            $response->setCode(Response::ERROR_CODE);
            $response->setResponse(false);
            return $response;
        }

        try {
            require_once(EXTERNAL_LIB_PATH . 'aws/aws-autoloader.php');
            $s3Client = Aws\S3\S3Client::factory(array(
                        'key' => CDN_ACCESS_KEY,
                        'secret' => CDN_SECRET_KEY
            ));

            $resultS3 = $s3Client->putObject(array(
                'Bucket' => CDN_BUCKET,
                'Key' => CDN_APP_DIR.DS.$prefix.'packages.json',
                'SourceFile' => TMP_PATH.$prefix.'packages.json',
                'ACL' => 'public-read',
                'CacheControl' => CDN_CACHE_CONTROL,
                'ContentType' => 'application/json'
            ));
        } catch (Exception $e) {
            @unlink(TMP_PATH.$prefix.'packages.json');
            $response->setCode(Response::ERROR_CODE);
            $response->addError(PACKAGE_FILE_ERROR);
            $response->setResponse(false);
            return $response;
        }
        
        @unlink(TMP_PATH . 'packages.json');
        $response->setResponse(true);

        return $response;
    }

}

?>