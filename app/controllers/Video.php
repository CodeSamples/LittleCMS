<?php

    class Video extends Controller {

        public static function getVideos() {
            $required = array('bumbia_ids');
            foreach ($required as $value) {
                if(!isset($_GET[$value]) || trim($_GET[$value]) == '') {
                    die('Missing params');
                }
            }

            $response = new Response();
            $videos = new VideoListModel();
            $videos->setBumbia_ids(explode(',', $_GET['bumbia_ids']));
            $videos->getVideosFromBumbia();
            $response->setResponse($videos->getBumbia_videos());
            return $response;
        }

        public function showVideo() {
            if(!isset($_GET['video_id']) || trim($_GET['video_id']) == '') {
                die('Missing params');
            }
            $response = new Response();
            $response->setResponse(intval($_GET['video_id']));
            return $response;
        }


        public function videoGfrCallback() {
            $response = new Response();
            $input = @json_decode(file_get_contents('php://input'));
            $error = false;

            if(!isset($input) || $input === false || !isset($input->video)) {
                $error = true;
            }

            if(!isset($_GET['media_id']) || !is_numeric($_GET['media_id'])) {
                $error = true;
            }

            if($error) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(MISSING_FIELDS);
                $response->setResponse(false);
                return $response;
            }

            $media = new GalleryMediaModel();
            $media->setId(intval($_GET['media_id']));
            if(!$media->fetch()) {
                $response->setCode(Response::ERROR_CODE);
                $response->addError(MISSING_FIELDS);
                $response->setResponse(false);
                return $response;
            }

            File::deleteS3Object($media->filename);
            $media->setExternal_id($input->video->id);
            $media->setFilename(BUMBIA_CDN . $input->video->s3_thumbnail_url);
            $media->save();

            $response = new Response();
            $response->setResponse(true);
            return $response;
        }

        

    }

?>