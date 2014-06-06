<?php

	class File extends Controller {

		const MAX_FILE_SIZE = 5242880; // in bytes
		public static $allowedFileTypes = array(
			'image/jpeg',
			'image/png',
			'image/gif',
			'application/pdf',
			'video/mp4'
			);

		public function upload() {
			$response = new Response();
			$response->setCode(Response::ERROR_CODE);
			$response->setResponse(false);
            $response->setStatus(false);


			if(!isset($_FILES) || !isset($_FILES['file'])) {
                $response->addError(sprintf(FILE_ERROR_EMPTY,
                	implode(', ', self::$allowedFileTypes)));    
                return $response;
			}

			if($_FILES['file']['error'] == 1) {
				$response->addError(sprintf(FILE_ERROR_SIZE, ini_get('post_max_size')));
				return $response;
			}

			$compareTypes = self::$allowedFileTypes;
			if(isset($_POST['fileType'])) {
				if(trim($_POST['fileType']) != '') {
					$compareTypes = array();
					foreach (self::$allowedFileTypes as $type) {
						if(preg_match('/' . $_POST['fileType'] . '/', $type)) {
							$compareTypes[] = $type;
						}
					}
				}
			}

			if(!in_array($_FILES['file']['type'], $compareTypes)) {
                $response->addError(sprintf(FILE_ERROR_TYPE,
                	implode(', ', $compareTypes)));    
                return $response;
			}

			if($_FILES['file']['size'] > self::MAX_FILE_SIZE) {
				$response->addError(sprintf(FILE_ERROR_SIZE,
					self::MAX_FILE_SIZE / 1024 . ' kb'));
				return $response;
			}

			if(preg_match('/image/', $_FILES['file']['type']) && extension_loaded('gd')) {
				$info = getimagesize($_FILES['file']['tmp_name']);

				$maxWidth = MAX_IMAGE_WIDTH;
				$minWidth = MIN_IMAGE_WIDTH;
				$maxHeight = MAX_IMAGE_HEIGHT;
				$minHeight = MIN_IMAGE_HEIGHT;

				if(isset($_POST['fileSizeW']) && trim($_POST['fileSizeW']) != '') {
					$maxWidth = intval($_POST['fileSizeW']);
					$minWidth = intval($_POST['fileSizeW']);
				}

				if(isset($_POST['fileSizeH']) && trim($_POST['fileSizeH']) != '') {
					$maxHeight = intval($_POST['fileSizeH']);
					$minHeight = intval($_POST['fileSizeH']);
				}


				if($info[0] > $maxWidth || $info[1] > $maxHeight) {
					self::resizeImage($_FILES['file']['tmp_name'], $maxWidth, $maxHeight);
				} elseif($info[0] < $minWidth || $info[1] < $minHeight) {
					$response->addError(sprintf(FILE_ERROR_IMAGE_SMALL,
						$minWidth, $minHeight));
					return $response;
				}
			}

			$file = new FileModel();
			require_once(EXTERNAL_LIB_PATH . 'aws/aws-autoloader.php');
			$s3Client = Aws\S3\S3Client::factory(array(
			    'key' => CDN_ACCESS_KEY,
			    'secret' => CDN_SECRET_KEY
			));

			try {
				$normalizedFileName = self::normalizeFileName($_FILES['file']['name']);
				$dirname = (isset($_POST['fileDirname'])) ? $_POST['fileDirname'] : "";
				$dirname = preg_replace('/(^\/)|(\/$)/', '', $dirname);

				$result = $s3Client->putObject(array(
				    'Bucket' => CDN_BUCKET,
				    'Key' => CDN_APP_DIR.DS.$dirname.DS.$normalizedFileName,
				    'SourceFile' => $_FILES['file']['tmp_name'],
				    'ACL' => 'public-read',
				    'CacheControl' => CDN_CACHE_CONTROL,
				    'ContentType' => $_FILES['file']['type']
				));
			} catch (Exception $e) {
				$response->addError(FILE_ERROR_EXTERNAL);
				return $response;
			}

			$response->setStatus(true);	

			$file->setPath($result['ObjectURL']);
			$file->setType($_FILES['file']['type']);
			$file->setSize($_FILES['file']['size']);
			$response->setResponse($file);
			return $response;
		}

		public static function resizeImage($file, $w = MAX_IMAGE_WIDTH, $h = MAX_IMAGE_HEIGHT, $crop = false) {
			$imageInfo = getimagesize($file);
		    list($width, $height) = $imageInfo;
		    $r = $width / $height;
		    if ($crop) {
		        if ($width > $height) {
		            $width = ceil($width-($width*abs($r-$w/$h)));
		        } else {
		            $height = ceil($height-($height*abs($r-$w/$h)));
		        }
		        $newwidth = $w;
		        $newheight = $h;
		    } else {
		        if ($w/$h > $r) {
		            $newwidth = $h*$r;
		            $newheight = $h;
		        } else {
		            $newheight = $w/$r;
		            $newwidth = $w;
		        }
		    }

		    switch($imageInfo['mime']) {
		    	case 'image/jpeg':
		    	case 'image/jpg':
		    		$src = imagecreatefromjpeg($file);
		    	break;
		    	case 'image/png':
		    		$src = imagecreatefrompng($file);
		    	break;
		    	case 'image/gif':
		    		$src = imagecreatefromgif($file);
		    	break;
		    }

		    $dst = imagecreatetruecolor($newwidth, $newheight);
		    if(preg_match('/(png)|(gif)/', $imageInfo['mime'])) {
				imagealphablending($dst, false);
				$transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
				imagefilledrectangle($dst, 0, 0, $newwidth, $newheight, $transparent);
				imagealphablending($dst, true);
				imagesavealpha($dst,true);
		    }
		    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

		    switch($imageInfo['mime']) {
		    	case 'image/jpeg':
		    	case 'image/jpg':
		    		imagejpeg($dst, $file, 75);
		    	break;
		    	case 'image/png':
		    		imagepng($dst, $file);
		    	break;
		    	case 'image/gif':	
		    		imagegif($dst, $file);
		    	break;
		    }

		    imagedestroy($dst);
		}

		public static function normalizeFileName($string){
		    $string = preg_replace("/[^A-Za-z0-9 \.\-]/", '', strtolower($string));
		    $string = preg_replace('/([\s])\1+/', '_', $string );
		    $string = str_replace(" ", "_", $string);
		    $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		    return $string;
		}

		public static function moveUploadedObject($oldKey, $newKey) {
			require_once(EXTERNAL_LIB_PATH . 'aws/aws-autoloader.php');
			$s3Client = Aws\S3\S3Client::factory(array(
			    'key' => CDN_ACCESS_KEY,
			    'secret' => CDN_SECRET_KEY
			));
			$result = $s3Client->copyObject(array(
			    'Bucket' => CDN_BUCKET,
			    'Key' => $newKey,
			    'CopySource' => $oldKey,
			    'ACL' => 'public-read',
				'CacheControl' => CDN_CACHE_CONTROL,
			));
			if($result) {
				$deleteResult = $s3Client->deleteObject(array(
					'Bucket' => CDN_BUCKET,
					'Key' => $oldKey
					));
				return CDN_BASE_URL . $newKey;
			}

			return CDN_BASE_URL . $oldKey;
		}

		public static function deleteS3Object($filename) {
			require_once(EXTERNAL_LIB_PATH . 'aws/aws-autoloader.php');
			try {
				$s3Client = Aws\S3\S3Client::factory(array(
				    'key' => CDN_ACCESS_KEY,
				    'secret' => CDN_SECRET_KEY
				));
				$s3Client->deleteObject(array(
					'Bucket' => CDN_BUCKET,
					'Key' => str_replace(CDN_BASE_URL, '', $filename)
				));
			} catch(Exception $e) {
				return $e;
			}
			return true;
		}

	}

?>