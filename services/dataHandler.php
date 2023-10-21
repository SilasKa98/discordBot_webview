<?php


class DataHandler{

    function inputToArrayFilter($input, $matchingTo){
        $names = [];
        foreach($input as $item){
            array_push($names,$item[$matchingTo]);
        }
        return $names;
    }

    function inputToDictionaryFilter($input, $matchingBy, $matchingTo){
        $dic = [];
        foreach($input as $item){
            $dic[$item[$matchingBy]] = $item[$matchingTo];
        }
        return $dic;
    }



    function getArrayKeyByValue($array, $keyword){
        for($i=0;$i<count($array);$i++){
            if($array[$i] == $keyword){
                return $i;
            }
        }
    }

}



?>