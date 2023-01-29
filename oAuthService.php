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