<?php

    class VideoListModel extends Model {

        protected $bumbia_ids;
        protected $bumbia_videos;

        public function getBumbia_ids() { return $this->bumbia_ids; }
        public function getBumbia_videos() { return $this->bumbia_videos; }

        public function setBumbia_ids($bumbia_ids) { $this->bumbia_ids = $bumbia_ids; }
        public function setBumbia_videos($bumbia_videos) { $this->bumbia_videos = $bumbia_videos; }


        public function getVideosFromBumbia() {
            if(!isset($this->bumbia_ids) || !is_array($this->bumbia_ids)) {
                return false;
            }

            $videosParams = array(
                'scope' => 'unscoped',
                'api_key' => BUMBIA_API_KEY
                );
            $videosService = BUMBIA_API_FIND_BY_VIDEO;

            $videos = array_unique($this->bumbia_ids);
            if(sizeof($videos) > 1) {
                $videosParams['video_ids'] = implode(',', $videos);
            } else {
                $videosService .= $videos[0] . '/';
            }

            $videoList = array();
            $bumbiaResponse = $this->curlWrapper($videosService, 'GET', $videosParams);
            if($bumbiaResponse) {
                $jsonResponse = @json_decode($bumbiaResponse);
                if(isset($jsonResponse) && $jsonResponse !== false) {
                    if(sizeof($videos) > 1) {
                        foreach ($jsonResponse->items as $item) {
                            $video = new VideoItemModel();
                            $video->setId($item->id);
                            $video->setTitle($item->title);
                            $video->setDesc($item->short_description);
                            $video->setThumb(BUMBIA_CDN . $item->s3_thumbnail_url);
                            $videoList[] = $video;
                        }
                    } else {
                        $video = new VideoItemModel();
                        $video->setId($jsonResponse->id);
                        $video->setTitle($jsonResponse->title);
                        $video->setDesc($jsonResponse->short_description);
                        $video->setThumb(BUMBIA_CDN . $jsonResponse->s3_thumbnail_url);
                        $videoList[] = $video;
                    }
                }
            } 
            $this->setBumbia_videos($videoList);
        }

    }

?>