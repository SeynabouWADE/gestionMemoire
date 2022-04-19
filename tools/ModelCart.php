<?php
require_once("tools/Model.php");

class ModelCart extends Model {
	
	// protected $table = "article";

    public function getIdClientDiversSucc(){
        return $this->first("client", ["idsuccursale" => $_SESSION[$_SESSION['rep1']]["idsuccursale"]], "idclient")["idclient"]; //TODO 
    }
    public function getClient($idclient){
        return $this->first("client", ['idsuccursale'=>$_SESSION[$_SESSION['rep1']]["idsuccursale"], "idclient"=>$idclient]);
    }
    public function getClient_obj($idclient){
        return $this->first_obj("client", ['idsuccursale'=>$_SESSION[$_SESSION['rep1']]["idsuccursale"], "idclient"=>$idclient]);
    }

}
