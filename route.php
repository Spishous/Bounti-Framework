<?php

use function Tools\check;
use function Tools\has;

abstract class route{
    static function route(): array{
        /**
         *      check() -> l'url est identique
         *      has() -> url comment par ...
         **/
        if(check("")){
            $title="Ma page d'accueil";
        }
        elseif(has("/page2")){
            $title='PAGE 2';
            $page="page-2";
        }
        elseif(check("/test")){
            $title='test';
            $page="test";
        }
        elseif(check("/a/a")){
            $title='home A';

        }
        elseif(check("/img")){
            $title='compress img';
            $page="img";
        }
        else{
            $title='Page non trouvé';
            $page="404";
        }



        return [
            'template'=>$template??'',
            'title'=>$title??'',
            'page'=>$page??'',
        ];
    }
}

