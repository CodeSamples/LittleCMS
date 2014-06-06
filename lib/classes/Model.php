<?php

abstract class Model {
    
    protected function curlWrapper($url, $method, $data) {
        $headers = array(
                'Accept:*/*'
            );
        switch($method) {
            case 'GET':
                $url .= '?' . http_build_query($data);
            break;
        }
        /*
        print_r("\n");
        print_r('Curl wrapper: ' . $url);
        print_r("\n"); 
        */

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        if($method == 'POST') {
            $dataStr = http_build_query($data);
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $headers[] = 'Content-Length: ' . strlen($dataStr);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $dataStr);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        $curl_response = curl_exec($curl);
        $response = '';
        if($curl_response) {
            $response = trim($curl_response);
        } else {
            $response = curl_error($curl);
        }
        curl_close($curl);
        return $curl_response;
    }
    
    
}
?>