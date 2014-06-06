<?php

	class Coupon extends Controller {

		public function getCoupons() {
			if(!isset($_POST['restaurant_id'])) {
				die('Missing params.');
			}
                        
                        $start_list = isset($_GET["jtStartIndex"]) ? intval($_GET["jtStartIndex"]) : 0;
                        $limit_list = isset($_GET["jtPageSize"]) ? intval($_GET["jtPageSize"]) : -1;

			$couponList = new CouponListModel();
			$couponList->fetchList($_POST['restaurant_id'], $start_list, $limit_list);
			$records = array();
			foreach ($couponList->getList() as $single) {
				$row = array();
				$row['id'] = $single->getId();
				$row['name'] = $single->getName();
				$row['caption'] = $single->getCaption();
				$row['image'] = $single->getImage();
				$row['start'] = $single->getStart();
				$row['end'] = $single->getEnd();
				$row['pdf'] = $single->getPdf();
				$row['user_count'] = $single->getUser_count();
				$records[] = $row;
			}

			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['TotalRecordCount'] = $couponList->countAll($_POST['restaurant_id']);
			$jTableResult['Records'] = $records;  
			$response = new Response();  
			$response->setResponse($jTableResult);
			return $response;
		}


		public function getActualCoupon() {
			if(!isset($_GET['restaurant_id'])) {
				die('Missing params.');
			}

			$coupon = new CouponModel();
			$coupon->setProperty_id(intval($_GET['restaurant_id']));
			$coupon->fetchActual();
			$response = new Response(); 
			if(null === $coupon->getId()) {
				$response->setCode(Response::ERROR_CODE);
				$response->addError(COUPON_EMPTY);
				$response->setResponse(false);
			} else {
				$couponResponse = new stdClass();
				$couponResponse->id = $coupon->getId();
				$couponResponse->propertyId = $coupon->getProperty_id();
				$couponResponse->name = $coupon->getName();
				$couponResponse->caption = $coupon->getCaption();
				$couponResponse->image = $coupon->getImage();
				$couponResponse->start = $coupon->getStart();
				$couponResponse->end = $coupon->getEnd();
				$response->setResponse($couponResponse);
			}
			return $response;
		}


		public function addUser() {
			$requiredParams = array('apiKey','couponId','salId','salLogin','salEmail','salDisplayName','salUIDGigya', 'salPostTitle');
			$blockedValues = array('false', '0', 'undefined', 'null', '');

			foreach ($requiredParams as $param) {
				if(!isset($_GET[$param]) || in_array($_GET[$param], $blockedValues)) {
					die('Missing params.');
					break;
				}
			}

			$couponUser = new CouponUserModel();
			$couponUser->setCoupon_id($_GET['couponId']);
			$couponUser->setSal_id($_GET['salId']);
			$couponUser->setSal_login($_GET['salLogin']);
			$couponUser->setSal_email($_GET['salEmail']);
			$couponUser->setSal_display_name($_GET['salDisplayName']);
			$couponUser->setSal_UID_gigya($_GET['salUIDGigya']);
			$couponUser->insert();

			$response = new Response();

			if($couponUser->getId() != null) {
				$coupon = new CouponModel();
				require_once(EXTERNAL_LIB_PATH . 'PHPMailerAutoload.php');

				$coupon->setId($couponUser->getCoupon_id());
				$coupon->fetch();
				if(null != $coupon->getId()) {
					$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
					
					$pdf_binary = file_get_contents($coupon->getPdf(), false, $context);
					 			
					$email_template = THEME_PATH."emails/coupon_email.html";
					if (file_exists($email_template)) {
						$mail_body = file_get_contents($email_template, false, $context);
						$mail_body = str_ireplace("%BASE_URL%", "http://".BASE_URL."/", $mail_body);
						$mail_body = str_ireplace("%PLACE_NAME%", $_GET['salPostTitle'], $mail_body);
						$mail_body = str_ireplace("%COUPON_DESC%", $coupon->getCaption(), $mail_body);
					    $mail_body = str_ireplace("%COUPON_IS_VALID_UNTIL_START%", date('d/m/Y h:i a', strtotime($coupon->getStart())), $mail_body);
						$mail_body = str_ireplace("%COUPON_IS_VALID_UNTIL_END%", date('d/m/Y h:i a', strtotime($coupon->getEnd())), $mail_body);
					    $mail_body = str_ireplace("%COUPON_IMAGE_SRC%", $coupon->getImage(), $mail_body);
					} else {
						$mail_body = sprintf(nl2br(FRANCHISE_COUPON_MAIL_BODY),
							$_GET['salPostTitle'],
							$coupon->getCaption(),
							date('d/m/Y h:i a', strtotime($coupon->getStart())),
							date('d/m/Y h:i a', strtotime($coupon->getEnd()))
						);						
					}

					$mail = new PHPMailer();
					$mail->IsSMTP();
					$mail->SMTPAuth = true; 
					$mail->SMTPKeepAlive = true;           
					$mail->CharSet = 'utf-8';
					$mail->Host = MAIL_HOST;
					$mail->Port = MAIL_PORT;
					$mail->Username = MAIL_USER;
					$mail->Password = MAIL_PASS;
					$mail->From = "restaurantes@sal.pr";
					$mail->FromName = "Sal!";
					$mail->AddReplyTo("restaurantes@sal.pr", "Sal!");
					$mail->Subject = FRANCHISE_COUPON_MAIL_SUBJECT;
					$mail->WordWrap = 50;
				    $mail->AddAddress($couponUser->getSal_email(), $couponUser->getSal_display_name());
				    $mail->AddStringAttachment(
				    	$pdf_binary, 
				    	basename($coupon->getPdf()), 
				    	$encoding = 'base64', 
				    	$type = 'application/pdf');
					$mail->IsHTML(true);	
					$mail->Body = $mail_body;
					if(!$mail->Send()) {
						$response->setCode(Response::ERROR_CODE);
						$response->addError($mail->ErrorInfo);
						$response->setResponse(false);
					} else {
						$response->setResponse(true);
					}
				} else {
					$response->setCode(Response::ERROR_CODE);
					$response->addError(COUPON_LINK_FAILURE);
					$response->setResponse(false);
				}	
			} else {
				$response->setCode(Response::ERROR_CODE);
				$response->addError(COUPON_LINK_FAILURE);
				$response->setResponse(false);
			}

			return $response;
		}


		public function addCoupon() {
			if(!isset($_POST['restaurant_id'])) {
				die('Missing params.');
			}

			$response = new Response();
			$result = false;
			$error = false;
			$requiredVars = array('name', 'caption', 'start', 'end', 'pdf');
			foreach ($requiredVars as $key) {
				if(!isset($_POST[$key]) || trim($_POST[$key]) == '') {
					$error = true;
					break;
				}

				if($key == 'start' || $key == 'end') {
					if(preg_match('/00:00:00/', $_POST[$key])) {
						$error = true;
					}
				}
			}

			if($error) {
				$responseVal = array(
					'result' => $result,
					'message' => MISSING_FIELDS);
				$response->setResponse(json_encode($responseVal));
				return $response;
				break;
			}

			$newCoupon = new CouponModel();
			$newCoupon->setProperty_id($_POST['restaurant_id']);
			$newCoupon->setName($_POST['name']);
			$newCoupon->setImage($_POST['image']);
			$newCoupon->setCaption($_POST['caption']);
			$newCoupon->setStart($_POST['start']);
			$newCoupon->setEnd($_POST['end']);
			$newCoupon->setPdf($_POST['pdf']);
			$result = $newCoupon->save();

			if($result) {
				$newCoupon->setId($result);
				$defaultImg = CDN_BASE_URL.CDN_APP_DIR.CouponModel::IMG_PLACEHOLDER;
				if($newCoupon->getImage() != $defaultImg) {
					$this->moveCouponImage($newCoupon);
					$newCoupon->save();
				}

			}
			$message = ($result) ? COUPON_INSERTED_SUCCESS : COUPON_INSERTED_FAILURE;
			$responseVal = array(
				'result' => $result,
				'message' => $message);
			$response->setResponse(json_encode($responseVal));
			return $response;
		}

		public function moveCouponImage($coupon) {
			$imageRelPath = str_replace(CDN_BASE_URL, '', $coupon->getImage());
			$oldKey = CDN_BUCKET.DS.$imageRelPath;
			$newKey = CDN_APP_DIR.get_class($this).DS.$coupon->getId().DS.basename($coupon->getImage());
			$coupon->setImage(File::moveUploadedObject($oldKey,$newKey));
		}

		public function deleteCoupon() {
			$deletedCoupon = new CouponModel();
			$deletedCoupon->setId($_POST['id']);
			$result = $deletedCoupon->delete();

			$response = new Response();
			$message = ($result) ? COUPON_DELETED_SUCCESS : COUPON_DELETED_FAILURE;
			$Result = ($result) ? "OK" : "ERROR";
			$responseVal = array(
				'Result' => $Result,
				'Message' => $message);
			$response->setResponse($responseVal);
			return $response;
		}


		public function edit() {
			if(!isset($_POST['restaurant_id'])) {
				die('Missing params.');
			}

			$response = new Response();
			$restaurant = new RestaurantDashModel();
			$restaurant->fetchById($_POST['restaurant_id']);

			$editedCoupon = new CouponModel();
			$editedCoupon->setId($_POST['edit_coupon_id']);
			$editedCoupon->setProperty_id($_POST['restaurant_id']);
			$editedCoupon->setName($_POST['name_edit']);
			$editedCoupon->setCaption($_POST['caption_edit']);
			$editedCoupon->setImage($_POST['image_edit']);
			$editedCoupon->setStart($_POST['start_edit']);
			$editedCoupon->setEnd($_POST['end_edit']);
			$editedCoupon->setPdf($_POST['pdf_edit']);
			$result = $editedCoupon->save();

			$couponList = new CouponListModel();
			$couponList->fetchList($_POST['restaurant_id']);
			$couponPublicList = array();
			foreach ($couponList->getList() as $coupon) {
				$add = array(
					'id' => $coupon->getId(),
					'start' => $coupon->getStart(),
					'end' => $coupon->getEnd()
					);
				$couponPublicList[] = $add;
			}



			try {
				$prefix = '';
		        if(ENVIRONMENT == DEV_ENVIRONMENT) {
		            $prefix = 'dev-';
		        }
            	require_once(EXTERNAL_LIB_PATH . 'aws/aws-autoloader.php');
	            $s3Client = Aws\S3\S3Client::factory(array(
                    'key' => CDN_ACCESS_KEY,
                    'secret' => CDN_SECRET_KEY
	            ));

	            $resultS3 = $s3Client->putObject(array(
	                'Bucket' => CDN_BUCKET,
	                'Key' => CDN_APP_DIR.DS.'CouponList'.DS.$prefix.$restaurant->getSal_id().'.json',
	                'Body' => json_encode($couponPublicList),
	                'ACL' => 'public-read',
	                'CacheControl' => CDN_CACHE_CONTROL,
	                'ContentType' => 'application/json'
	            ));
	        } catch (Exception $e) {
	            
	        }
			
			$message = ($result) ? COUPON_EDITED_SUCCESS : COUPON_EDITED_FAILURE;
			$responseVal = array(
				'result' => $result,
				'message' => $message);
			$response->setResponse(json_encode($responseVal));
			return $response;
		}


		public function exportXls() {
			if(!isset($_SESSION['userObject'])) {
				die("No tiene privilegios para ejecutar esta acción.");
			}

			if(!isset($_GET['restaurant_id']) || trim($_GET['restaurant_id']) == '') {
				die('Missing params');
			}

			$response = new Response();
			$userCoupon = new CouponListModel();

			$isAdmin = ($_SESSION['userObject']->getReservaRole() == ROLE_ADMIN) ? true : false;
			if($isAdmin || $userCoupon->validateUserProperty(intval($_GET['restaurant_id']))) {
				$userCoupon->fetchList(intval($_GET['restaurant_id']));
				require_once(EXTERNAL_LIB_PATH . 'PHPExcel.php');
				$objPHPExcel = new PHPExcel();
				$objPHPExcel->getProperties()->setCreator(BASE_URL);
				$objPHPExcel->getProperties()->setLastModifiedBy(BASE_URL);
				$objPHPExcel->getProperties()->setSubject(FRANCHISE_COUPON_LIST);
				$objPHPExcel->getProperties()->setDescription(FRANCHISE_COUPON_LIST);
				$objPHPExcel->setActiveSheetIndex(0);
				$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

				$excelTitles = array(
					ID, 
					FRANCHISE_COUPON_NAME, 
					FRANCHISE_COUPON_CAPTION,
					FRANCHISE_COUPON_IMAGE, 
					FRANCHISE_COUPON_START,
					FRANCHISE_COUPON_END,
					FRANCHISE_COUPON_PDF,
					FRANCHISE_COUPON_USER_COUNT
					);

				$excelData = array($excelTitles);
				foreach ($userCoupon->getList() as $coupon) {
					$excelData[] = array(
						$coupon->getId(),
						$coupon->getName(),
						$coupon->getCaption(),
						$coupon->getImage(),
						$coupon->getStart(),
						$coupon->getEnd(),
						$coupon->getPdf(),
						$coupon->getUser_count()
						);
				}

				$actualColumn = 'A';
				$numberColumns = array('A', 'H');
				for($i = 0; $i < sizeof($excelTitles); $i++) {
					$objPHPExcel->getActiveSheet()
						->getColumnDimension($actualColumn)
						->setAutoSize(true);
					$objPHPExcel->getActiveSheet()
				 		->getStyle($actualColumn . '1')
				 		->getAlignment()
				 		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
				 	$objPHPExcel->getActiveSheet()
				 		->getStyle($actualColumn . '1')
				 		->getFont()
				 		->setBold(true);

				 	if(!in_array($actualColumn, $numberColumns)) {
					 	$range = $actualColumn.'2:'.$actualColumn.sizeof($excelData);
						$objPHPExcel->getActiveSheet()
						    ->getStyle($range)
						    ->getNumberFormat()
						    ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
						$objPHPExcel->getActiveSheet()
					 		->getStyle($range)
					 		->getAlignment()
					 		->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);  
					}	
					$actualColumn++;
				}

				$objPHPExcel->getActiveSheet()->fromArray($excelData);
				$response->setResponse(new PHPExcel_Writer_Excel5($objPHPExcel));
			} else {
				$response->setCode(Response::ERROR_CODE);
				$response->addError(INVALID_USER_FOR_ACTION);
				$response->setResponse(false);
			}

			return $response;
		}

	}

?>