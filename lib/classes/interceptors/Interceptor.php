<?php

abstract class Interceptor {
    public abstract function afterAction($response);
    
    public abstract function beforeAction($controller);
}
?>