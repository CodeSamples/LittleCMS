<?php
  $request = RequestManager::getInstance();
?>
<!doctype html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
  <title>Sal! Dashboard</title>
  <meta name="robots" content="noindex,nofollow" />	
  <meta name="viewport" content="width=device-width, initial-scale=1.0 maximum-scale=1.0, user-scalable=no">
  <link media="all" type="text/css" href="/css/style.css" rel="stylesheet">
  <link rel="shortcut icon" href="/theme/reserva/img/favicon.ico">
  <link href="/css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
  <script src="/js/jquery-1.10.2.min.js"></script>
  <script src="/js/jquery-ui-1.10.4.custom.min.js"></script>
  <script src="/js/jquery.messages.js"></script>

  <style type="text/css">
      <?php 
        $packages = new PackageModel();
        $packages_features = $packages->getAllFeaturesByName();
        foreach($packages_features as $key=>$package){
            echo '.package_'.$key.'{';
            if(isset($package->features["color"])){
              echo ' background-color: '.$package->features["color"].';';
            }
            echo '}';
        }
      ?>
  </style>
</head>
    <body id="<?php echo $request->getAction(); ?>">
<?php
  $errors = $obj_response->getErrors();
  $messages = $obj_response->getMessages();
?>