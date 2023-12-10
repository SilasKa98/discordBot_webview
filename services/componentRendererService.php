<?php

$basePath = dirname(__DIR__, 1);
require $basePath.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($basePath);
$dotenv->load();


class ComponentRendererService{


    function __construct(){

    }

    function createCarouselModule($titel, $msg, $status, $cardBodyId, $animation){

        print '
            <div class="carousel-item '.$status.'">
                <div class="card moduleCard mb-3">
                    <div class="row g-0 animationAndTextWrapper">
                        <div class="col-md-8">
                            <div class="card-body" id="'.$cardBodyId.'" style="min-height:35vh;">
                                <h5 class="card-title">'.$titel.'</h5>
                                <p class="card-text">'.$msg.'</p>
                            </div>
                        </div>
                        <div class="col-md-2 animatedGraphic" id="'.$animation.'">';
                        if($animation != ""){
                            include_once "./frontend/animations/".$animation."/gui.php";
                        }
                        print'</div>
                    </div>
                </div>
            </div>
        ';      
    }  
    
    function createLogMessage($logType, $logAuthor, $logContent, $logTime){
        print'
            <div class="alert alert-'.$logType.'" role="alert">
                <span class="badge rounded-pill text-bg-primary">@'.$logAuthor.'</span>
                <span class="badge text-bg-secondary" style="font-size: 11pt;">'.$logContent.'</span>
                <span class="badge rounded-pill text-bg-warning" style="float:right; margin-right:1px;">'.$logTime.'</span>
            </div>
        ';
    }
}

?>