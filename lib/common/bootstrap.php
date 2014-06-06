<?php

require_once(COMMON_PATH.'functions.php');

switch(ENVIRONMENT){
    case DEV_ENVIRONMENT:
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        break;
    case PROD_ENVIRONMENT:
        ini_set('display_errors', 0);
        break;
    default:
        ini_set('display_errors', 0);
}

$requestManager = RequestManager::getInstance();
$requestManager->excecute();

?>