<?php
$basePath = dirname(__DIR__, 1);
require $basePath.'/vendor/autoload.php';
/**
 * using the htmlPurifier library to filter for illegal user inputs that try to xss 
 * DOC: http://htmlpurifier.org/docs
 * GIT: https://github.com/ezyang/htmlpurifier/
 */

class SanitiseInputService{

    function sanitiseInput($inputText){
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $clean_html = $purifier->purify($inputText);
        return $clean_html;
    }

    function guildIdValidator($urlId, $sessionId){
        if($urlId != $sessionId){
            return exit("illegalGuildId");
        }
    }
    
}

?>