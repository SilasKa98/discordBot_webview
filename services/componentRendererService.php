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
                    <div class="row g-0">
                        <div class="col-md-8">
                            <div class="card-body" id="'.$cardBodyId.'">
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


/*
            print'
            <div class="carousel-item '.$status.'">      
                <div class="card moduleCard">
                    <div class="card-header" style="text-align:center;">
                        <img id="'.$imgId.'" src="'.$_ENV["app_root"].'media/'.$imgName.'">
                    </div>
                    <div class="card-body" id="'.$cardBodyId.'">
                        <h5 class="card-title">'.$titel.'</h5>
                        <p class="card-text">'.$msg.'</p>
                    </div>
                </div>
                <div>';
                    if($animation != ""){
                        include_once "./frontend/animations/".$animation."/gui.html";
                    }

        print'</div>
        </div>';*/
         
        }   
    }

?>