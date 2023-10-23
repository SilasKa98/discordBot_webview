<?php


class oAuthService{

    function __constructor(){

    }

    
    function doCurl($curlOptions){
        $ch = curl_init();
        foreach($curlOptions as $key => $value){
            curl_setopt($ch,$key,$value);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_ENCODING, '');

        $result = curl_exec($ch);

        if(!$result){
            echo curl_error($ch);
            exit();
        }
        return $result;  
    }
    


    function doAsyncCurl($curlOptionsArray) {
        $multiHandle = curl_multi_init();
        $curlHandles = [];
        $results = [];

        foreach ($curlOptionsArray as $index => $options) {
            $ch = curl_init();
            curl_setopt_array($ch, $options);
            curl_multi_add_handle($multiHandle, $ch);
            $curlHandles[$index] = $ch;
        }
    
        $active = null;
        do {
            curl_multi_exec($multiHandle, $active);
        } while ($active > 0);
    
        foreach ($curlHandles as $index => $ch) {
            $results[$index] = curl_multi_getcontent($ch);
            curl_multi_remove_handle($multiHandle, $ch);
        }
    
        curl_multi_close($multiHandle);
    
        return $results;
    }
    



    function doCurlWithUrl($curlOptions, $url){
        $ch = curl_init($url);
        foreach($curlOptions as $key => $value){
            curl_setopt($ch,$key,$value);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);

        if(!$result){
            echo curl_error($ch);
            exit();
        }
        return $result;  
    }



    function getBearerToken($curlResult){
        $result = json_decode($curlResult,true);
        $bearer_token = $result["access_token"];
        return $bearer_token;
    }


}



?>