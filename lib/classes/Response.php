<?php

class Response extends BaseResponse{
    public $response;
    public $code;
    public $errors=array();
    public $messages=array();
    public $status;    
    
    public function getResponse() {
        return $this->response;
    }

    public function setResponse($response) {
        $this->response = $response;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }
    
    public function getErrors() {
        return $this->errors;
    }

    public function addError($error) {
        $this->errors[] = $error;
    }
    
    public function getMessages() {
        return $this->messages;
    }

    public function addMessage($message) {
        $this->messages[] = $message;
    }
    
    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
        
    public function __construct() {
        $this->code = Response::OK_CODE;
    }

}


?>