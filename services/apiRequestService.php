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
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_URL => $url,
                CURLOPT_POST => false,
                CURLOPT_RETURNTRANSFER => true
            ];
            $oAuthService = new oAuthService();
            $curlOptionsArray = [$curlOptions];
            $results = $oAuthService->doAsyncCurl($curlOptionsArray);
            // Ergebnisse verarbeiten
            foreach ($results as $index => $result) {
                $final_result = json_decode($result, true);
            }
            return $final_result;   
        }


        function generateCurlOptionsForUserGuildInfos($guildId, $userId){ 

            $url = "https://discord.com/api/guilds/{$guildId}/members/{$userId}";
            $token = $_ENV["bot_token"];
            // Set the request headers
            $header = array(
                'Authorization: Bot ' . $token,
                'Content-Type: application/json'
            );
            $curlOptions = [
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_URL => $url,
                CURLOPT_POST => false,
                CURLOPT_RETURNTRANSFER => true
            ];

            return $curlOptions;
        }


        function checkUserOnServer($guildId, $userId){
            $bot_token = $_ENV["bot_token"];
        
            $url = "https://discord.com/api/guilds/$guildId/members/$userId";
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
   
            return json_decode($result,true);
        }

    }

?>