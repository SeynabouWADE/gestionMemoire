<?php
    // TODO embélir
    $this->setTitle("Error");
    notice($errorMsg);
    echobr(tag('a href="'.backLink().'"', msgs['retour']));
    echo tag('a href="'.racine().'"', msgs['accueil']);

    $modelExeption = session('modelExeption', "", true);
    if($modelExeption and devMode){
        see("<h3>More informations :</h3>");
        see("<h4>Prepared statement (request) and associative datas:</h4>");
        see($modelExeption[0]);
        see($modelExeption[1]);
        tag("hr");
        see($modelExeption[2]);
    }