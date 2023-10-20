<?php

    include_once "oAuth/oAuthService.php";
    class ApiRequests{
        /**
         * get loggedIn userInfos about the guilds
         */
        function getUserGuildInfos($guildId, $userId){
            $url = "https://discord.com/api/guilds/{$guildId}/members/{$userId}";
            $token = $_ENV["bot_token"];
            // Set the request headers
            $header = array(
                'Authorization: Bot ' . $token,
                'Content-Type: application/json'
            );
            $curlOptions = [
                CURLOPT_HTTPHEADER=>$header,
                CURLOPT_URL=>$url,
                CURLOPT_POST=>false,
                CURLOPT_RETURNTRANSFER=>true
            ];
            $oAuthService = new oAuthService();
            $result = $oAuthService->doCurl($curlOptions);
            $result = json_decode($result, true);
            return $result;   
        }

        function checkUserOnServer($guildId, $userId){
            $bot_token = $_ENV["bot_token"];
        
            $url = "https://discord.com/api/v9/guilds/$guildId/members/$userId";
            $oAuthService = new oAuthService();
            $curlOptions = [
                CURLOPT_RETURNTRANSFER=>true,
                CURLOPT_HTTPHEADER=>[
                    'Authorization: Bot ' . $bot_token,
                    'Content-Type: application/json',
                ]
            ];
            $response = $oAuthService->doCurlWithUrl($curlOptions, $url);
        
            $data = json_decode($response, true);
            return $data;
        }


        function getDiscordEntity($guild_id, $bot_token, $entity){

            $url = "https://discord.com/api/guilds/{$guild_id}/{$entity}";

            include_once "oAuth/oAuthService.php";
            $oAuthService = new oAuthService();

            //curl options for given entity infos
            $curlOptions = [
                CURLOPT_URL=>$url,
                CURLOPT_RETURNTRANSFER=>true,
                CURLOPT_HTTPHEADER=>[
                                        "Authorization: Bot {$bot_token}",
                                        "Content-Type: application/json"
                                    ]
            ];

            $result = $oAuthService->doCurl($curlOptions);
            return json_decode($result, true);
        }

    }

?>