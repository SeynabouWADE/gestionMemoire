<?php
require_once("tools/Controller.php");
require_once("tools/ModelCart.php");

class ControllerCart extends Controller{
    
    public function __construct($url="", $errorMsg="", $viewName=""){
        parent::__construct($url, $errorMsg, $viewName);
        $this->model = tern($this->model, new ModelCart());
    }

    function addCart_json($idArticle = ""){
        $json = array('error'=> true);
        $idArticle = tern($idArticle, old('id'));
        if($idArticle){
            $dbTable = articleDbTable;
            $produit = $this->model->get_obj($dbTable, $idArticle);
    
            //$produit = $DB->query('SELECT id_prod, prix FROM produit WHERE id_prod=:id', array('id' => $_GET['id']));
            if(empty($produit)){
                die("Ce produit n'existe pas");
            }
            $this->add($produit[0]->idarticle);
            $json['error'] = false;
            $json['total'] = $this->total();
            $json['totalPartiel'] 	= $this->totalPartiel($produit[0]->id_prod, $produit[0]->prix);
            $json['totalQuantity'] 	= $this->totalQuantity();
            $json['totalArticle'] 	= $this->totalArticle();
            $json['error'] = false;
            $json['message'] = 'Le produit à bien été ajouté à votre panier';  //<a href="javascript:history.back()">retourner sur le catalogue</a>');
            
        }else{
            $json['message'] = "Vous n'avez pas sélectionner de produit à ajouter au panier";
        }
        echo json_encode($json);
    }

	// public function recalculer(){
    //     if(isset($_POST['panier']['quantity'])){
    //         session('panier', $_POST['panier']['quantity']); // moins sure; pour éviter cetaines injections...
    //         foreach(session('panier') as $produit_id => $quatity){
    //             if(isset($_POST['panier']['quantity'][$produit_id])){	
    //                 session2( 'panier', $produit_id, intval($_POST['panier']['quantity'][$produit_id]) );
    //             }else{

    //             }
    //         }
    //     }
	// }
	public function commander(){
        if(post('commander') && $this->totalQuantity() > 0){
            if(session2('client', 'iduser')){

                //$this->recalculer();		
                        
                $sql = 'INSERT INTO `commande` (`total`, `date_com`, `heure_com`, `date_livre`, `num_CB`, `date_CB`, `cle_CB`) 
                VALUES (:total, :date_com, :heure_com, :date_livre, :num_CB, :date_CB, :cle_CB)';
                $valeurs = array('total' => $this->total(), 'date_com' => date("Y-m-d"), 'heure_com' => date("H:i:s"), 'date_livre' => date("Y-m-d"), 'num_CB' => NULL, 'date_CB' => NULL, 'cle_CB' => NULL);
                
                $test = $this->db->queryPut($sql, $valeurs);
                if($test){
                    $numCom = $this->db->query('SELECT num_com FROM commande order by num_com desc limit 1');
                    $ids = array_keys(session('panier'));
                    if(empty($ids)){
                        $produits = array();
                    }else{
                        $produits = $this->db->query('SELECT * FROM produit WHERE id_prod IN ('.implode(',',$ids).')');
                    }
                        
                    foreach($produits as $produit){
                        $qte = intval(session2('panier', $produit->id_prod));
                        $sql = 'INSERT INTO `prod_commande` (`numClient`, `num_prod`, `numCom`, `qte_com`, `numFournisseur`, `traite`, `prix`) VALUES (:numClient, :num_prod, :numCom, :qte_com, :numFournisseur, :traite, :prix)';
                        $valeurs = array(
                            'numClient' => session2('client', 'iduser'), 'num_prod' => $produit->id_prod,
                            'numCom' => $numCom['0']->num_com, 'qte_com' => $qte,
                            'numFournisseur' => $produit->fournisseur, 'traite' => '0', 'prix' => $produit->prix);
                                    
                        $test1 = $this->db->queryPut($sql, $valeurs);
                        if( ! $test1)
                            flash("Problème d\'insertion dans la table prod_commande", "danger");
                    }
                }else 
                    flash("Problème d\'insertion dans la table commande", "danger"); //TODO envoyer l'erreur par mail à l'admin ...
                if($test && $test1){
                    flash("La commande est bien envoyée vous serez contacté bientôt", "success");//TODO envoyer l'erreur par mail à l'admin ...
                    
                    $this->ressetPanier();
                }
            }else
                notification("Veillez <b>vous connecter</b> afin de <b>poursuivre</b> l\'envoie de votre commande");
	    }
    }
	public function add($produit_id){
		if(session2('panier', $produit_id)){
			session2('panier', $produit_id, "++");
		}else{
			session2('panier', $produit_id, 1);
		}
	}
	public function setQuantity($produit_id, $qte){
        session2('panier', $produit_id, $qte);
	}
	public function del($produit_id = ""){
        $produit_id = tern($produit_id, $_GET['delCart']);
        unsetSession('panier', $produit_id);
	}
	public function ressetPanier(){
		//unset($_SESSION['panier']);
		session('panier', array());
	}
	public function totalPartiel($produit_id, $prix){
		return session2('panier', $produit_id) * $prix;
	}
	public function total(){
		$total = 0;
		$ids = array_keys(session('panier'));
		if(empty($ids)){
			$produits = array();
		}else{
			$produits = $this->db->query('SELECT * FROM produit WHERE id_prod IN ('.implode(',',$ids).')');
		}
		foreach($produits as $produit){	
			$total += $produit->prix * session2('panier', $produit->id_prod) 	;
		}
		return $total;
	}
	public function totalQuantity(){
		return array_sum(session('panier'));
	}
	public function totalArticle(){
		return count(session('panier'));
	}





    
    public function venteFacture(){
        //require_once("fonctions.php"); //ici ok

        $idclient = "";
        if(isset($_POST['tva']))
            $tva = session("tva");
        else
            $tva = 0;

        if(isset($_POST['numerofacture'])) // si le numéro facture existe déjà alors c 1e modif
            session('modifierfacture', "pam");
        else session('ventefacture', "pam");

     /* if((microtime(true) - session('currentTime')) > 2){
        session('currentTime', microtime(true));
        date_default_timezone_set('UTC');
        try{ $bdd=new PDO('mysql:host='.session('localhost').';dbname='.session('bd'),
            session('root'), session('pass'));	}
        catch (Exception $e) {	die('Erreur : '.$e->getMessage());	}
        //echo count($_POST)."<pre>";print_r($_POST); echo "</pre>";		break; */
        $test = true;	
        $testDroit = true;			
        $isModification = false;
        if(session('idclient')){
            $idclient = session('idclient');
            $voie = 2;
            $date = date("Y-m-d");
            $time = date("H:i:s");
            $typefacture = "preparee";
        }
        else{
            $idclient = htmlspecialchars($_POST['idclient']);
            $typefacture = htmlspecialchars($_POST['typefacture']);
            $voie = 1;
            $date = htmlspecialchars($_POST['date']);
            $time = htmlspecialchars($_POST['time']);
        }
        if(strlen($idclient) == 0){//ajout new client
            $idclient = $this->client_fct();
        }
        else if(session("idsuccursale") != 1 and $idclient == 1){ // CLIENT DIVERS pour les autres succursales le numéro du client divers est != 1
            $idclient = $this->model->getIdClientDiversSucc();
        }
        $clientTmp = $this->model->getClient($idclient);
        $_POST["prenom"] = $clientTmp["prenom"];
        $_POST["nom"] = $clientTmp["nom"];

        if(isset($_POST['alivreadom']))
            $alivreadom = 1; //oui
        else
            $alivreadom = 0; //non
        
        $remise = 0.0;
        if(isset($_POST['remise']) && $_POST['remise'] != "")
            $remise = floatval($_POST['remise']); //oui
        
        //TEST (avec $a ++;) SI LE CLIENT A ACHETÉ QUELQUE CHOSE OU SI C'EST UN SIMPLE INSERTION CLIENT
        //et ENRÉGISTRER LES ARTICLES QUI CONSERNENT CETTE FACTURE
        if((strlen($idclient) != 0 && (isset($_POST['numerofacture'])) || ! empty(trim($_POST['idchoisilist'])))){
            
            if(isset($_POST['numerofacture'])){ //For modification
            $isModification = true;
                //session('idclient'])

            $numerofacture = htmlspecialchars($_POST['numerofacture']);

            if(session('idtypeemploi') > 2){
                
                $facturePayeeOuLivree = $this->model->first("facture", ['idsuccursale'=>session('idsuccursale'),
                'numerofacture'=>$_POST['numerofacture']]);

                $facturePatielPayee = $this->model->first("caisseentree", ['idsuccursale'=>session('idsuccursale'),
                'numerofacture'=>$_POST['numerofacture']], "", [], "montant");
            
                $typefacture = $facturePayeeOuLivree['typefacture'];
                
                $payee = $facturePayeeOuLivree['facturepayee'] == 'oui';
                $livree = $facturePayeeOuLivree['facturelivree'] == 'oui';
                $autoriselivree = $facturePayeeOuLivree['autoriselivree'] == 'oui';
                $facturePatielPayee = isset($facturePatielPayee["montant"]);

                //'.$client_en_ligne.$voie.'
                if( session('idclient') && $typefacture != "preparee" ){
                    $testDroit = false;
                    session('message', "<font size='4' color=\"#F7819F\"> Désolé, cette facture N&deg; <b>".$numerofacture."</b> n'est plus en <b>prépartion</b> pour que vous puissiez la modifier !!! Vous pouvez demaner à un vendeur. </font>");
                }elseif( $payee or $livree or $autoriselivree or $facturePatielPayee ){
                    $testDroit = false;
                    echo "<font size='4' color=\"#F7819F\">Il faut &ecirc;tre <b>administrateur</b> pour modifier cette facture <b>N&deg; ".$numerofacture."</b> car elle est livr&eacute;e, autoris&eacute;e à livraison, ou pay&eacute;e !!! </font>";
                }
            }
                
            if($testDroit){
                
                isset($_POST['tva']) ? $tva = session('tva') : $tva = 0;

                $result = $this->model->first("facture", ['idsuccursale'=>session('idsuccursale'),
                'numerofacture'=>$numerofacture], "", [], "facturelivree");

                $facturelivree0 = $result['facturelivree'];

                $testmodif1 = $this->update("facture", 
                    [
                        "idmodificateur" => session('idutilisateur'),
                        "tva" => $tva, "heuremodification" => date("H:i:s"), "datemodification" =>date("Y-m-d"), "idclient" => $idclient, "typefacture" => $typefacture,
                        "alivreadom" => $alivreadom, "facturelivree" => 'non', "autoriselivree" => 'non', "facturepayee" => 'non',
                        "prototype" => isset($_POST['prototype']) ? trim($_POST["prenom"].' '.$_POST["nom"]): "", "remise" => $remise
                    ], 
                
                    [
                        'numerofacture'=>$numerofacture, 
                        'idsuccursale'=>session("idsuccursale")
                    ]
                );

                // $req=$bdd->prepare("UPDATE facture SET idmodificateur = ".session('idutilisateur').", 
                // tva = :tva, heuremodification = '".date("H:i:s")."', datemodification = '".date("Y-m-d")."',
                // idclient = :idclient, typefacture = :typefacture,
                // alivreadom = :alivreadom, facturelivree = 'non', autoriselivree = 'non', 
                // facturepayee = 'non', prototype = :prototype, remise = :remise WHERE idsuccursale = :idsuccursale and numerofacture = :numerofacture LIMIT 1");
                // $testmodif1 = $req->execute(array(
                // 	'numerofacture'=>$numerofacture,
                // 	'alivreadom'=>$alivreadom,
                // 'tva'=>$tva, 'idsuccursale'=>session("idsuccursale"], 
                // 'idclient'=>$idclient, 'typefacture'=>$typefacture,
                // 'prototype'=> isset($_POST['prototype']) ? trim($_POST["prenom"].' '.$_POST["nom"]) : "",
                // 'remise'=>$remise,
                // ))
                // or die(print_r($req->errorInfo())); $req->closeCursor();

                // $req=$bdd->prepare("SELECT montant FROM caisseentree WHERE idsuccursale = :idsuccursale and numerofacture = :numerofacture");
                // $test = $req->execute(array('idsuccursale'=>session("idsuccursale"], 'numerofacture'=>$numerofacture)) or die(print_r($req->errorInfo()));
                // $result = $req->fetchAll(); $req->closeCursor();

                $test = $result = $this->model->get("caisseentree", ['idsuccursale'=>session("idsuccursale"), 'numerofacture'=>$numerofacture], "", "", [], "montant");

                $montant = 0;
                $testmodif2 = true;
                if(! empty($result)){ //décaissement
                    
                    // $req=$bdd->prepare(
                    // 	"UPDATE caisseentree SET montant = 0, annulee_montant = :annulee_montant, idutilisateurannule = :idutilisateurannule
                    // 	WHERE idsuccursale = :idsuccursale and numerofacture = :numerofacture and montant = :old_montant LIMIT 1");
                    foreach ($result as $value) {
                        /*$testmodif2 = $req->execute(
                            array(
                                'idsuccursale'=>session("idsuccursale"),
                                'numerofacture'=>$numerofacture,
                                'old_montant'=>$value['montant'],
                                'annulee_montant'=>$value['montant'],
                                'idutilisateurannule'=>session('idutilisateur')
                            )
                        ) or die(print_r($req->errorInfo())); */

                        $this->update("caisseentree", 
                            [
                                "montant" => 0, "annulee_montant" => $value['montant'], "idutilisateurannule" => session('idutilisateur')], 
                            [
                                'idsuccursale'=>session("idsuccursale"),
                                'numerofacture'=>$numerofacture,
                                'montant'=>$value['montant']
                            ]
                        );
                        $montant += $value['montant'];
                    }
                }

                if($facturelivree0 == "oui"){ // délivraison
                    $backLivraison = true;
                    require_once('encaiment_livraison.php'); //ici
                }else $backLivraison = false;

                $testmodif3 = true;
                if($facturelivree0 == "oui" || $montant > 0){ //mettre dans les factures annulée
                    $testmodif3 = $this->model->insert("facutre_annulee", array(
                        'idsuccursale'=>session("idsuccursale"),
                        'idutilisateur'=>session('idutilisateur'),
                        'numerofacture'=>$numerofacture,
                        'montant'=>$montant,
                        'date'=>date("Y-m-d"),
                        'heure'=>date("H:i:s")) 
                    );
                    // $req=$bdd->prepare("INSERT INTO facutre_annulee (idsuccursale, idutilisateur, numerofacture, montant, `date`, heure) VALUES(:idsuccursale, :idutilisateur, :numerofacture, :montant, :date, :heure)");
                    // $testmodif3 = $req->execute(array(
                    // 	'idsuccursale'=>session("idsuccursale"),
                    // 	'idutilisateur'=>session('idutilisateur'),
                    // 	'numerofacture'=>$numerofacture,
                    // 	'montant'=>$montant,
                    // 	'date'=>date("Y-m-d"),
                    // 	'heure'=>date("H:i:s")
                    // ))	or die(print_r($req->errorInfo()));
                    // $req->closeCursor();
                    if( ! $test){
                        echo "<font color=\"red\"> La facture N° ".$numerofacture." n'est pas insérée dans facutre_annulee (pour modification !!! </font>"; 
                        die('paml');
                    }
                }
                
                $testmodif4 = $this->delete("articlesdefacture", array('numerofacture'=>$numerofacture, 'idsuccursale'=>session("idsuccursale")));
                /*$req=$bdd->prepare("DELETE FROM `articlesdefacture` WHERE `idsuccursale`= :idsuccursale and `numerofacture` = :numerofacture");
                $testmodif4 = $req->execute(array('numerofacture'=>$numerofacture, 'idsuccursale'=>session("idsuccursale")))or die(print_r($req->errorInfo())); $req->closeCursor(); */
                
                if( $testmodif1 && $testmodif2 && $testmodif3 && $testmodif4 ){ //For modification
                    $test = true;
                }
                else{
                    $test = false;
                    session('message', "<font color = red size = 4>Facture N° ".$numerofacture." (".htmlspecialchars($_POST['prenom'])." ".htmlspecialchars($_POST['nom']).") n'est pas modifiée ou entièrement modifiée ! veillez r&eacute;essayer svp.</font>");
                }
            }
            }
            else{
                /* $req=$bdd->prepare('SELECT numerofacture from derniernumerofacture where idsuccursale = :idsuccursale limit 1');
                $req->execute(array('idsuccursale'=>session('idsuccursale'])) or die(print_r($req->errorInfo()));
                $numerofacture = $req->fetch()["numerofacture"]+1;	$req->closeCursor(); */
                
                $numerofacture = $this->model->first("derniernumerofacture", array('idsuccursale'=>session('idsuccursale')))["numerofacture"]+1;

                $test = $this->model->insert("facture", array(
                    'idsuccursale'=>session("idsuccursale"),
                    'numerofacture'=>$numerofacture,
                    'typefacture'=>$typefacture,
                    'voie'=>$voie,
                    'alivreadom'=>$alivreadom,
                    'tva'=>$tva,
                    'facturepayee'=>'non',
                    'autoriselivree'=>'non',
                    'facturelivree'=>'non',
                    'iduserfacture'=>session('idutilisateur'),
                    'date'=>$date,
                    'time'=>$time,
                    'date2'=>date("Y-m-d"),
                    'time2'=>date("H:i:s"),
                    'idclient'=>$idclient,
                    'prototype'=> isset($_POST['prototype']) ? trim($_POST["prenom"].' '.$_POST["nom"]) : "",
                    'remise'=> $remise,
                    )
                );
                /*
                $req=$bdd->prepare("INSERT INTO facture (idsuccursale, numerofacture, typefacture, voie, alivreadom, tva, facturepayee, autoriselivree, facturelivree, 
                    iduserfacture, date, time, date2, time2, idclient, prototype, remise) VALUES(:idsuccursale, :numerofacture, :typefacture, :voie, :alivreadom, :tva,
                    :facturepayee, :autoriselivree, :facturelivree, :iduserfacture, :date, :time, :date2, :time2, :idclient, :prototype, :remise)");
                $test = $req->execute(array(
                    'idsuccursale'=>session("idsuccursale"),
                    'numerofacture'=>$numerofacture,
                    'typefacture'=>$typefacture,
                    'voie'=>$voie,
                    'alivreadom'=>$alivreadom,
                    'tva'=>$tva,
                    'facturepayee'=>'non',
                    'autoriselivree'=>'non',
                    'facturelivree'=>'non',
                    'iduserfacture'=>session('idutilisateur'),
                    'date'=>$date,
                    'time'=>$time,
                    'date2'=>date("Y-m-d"),
                    'time2'=>date("H:i:s"),
                    'idclient'=>$idclient,
                    'prototype'=> isset($_POST['prototype']) ? trim($_POST["prenom"].' '.$_POST["nom"]) : "",
                    'remise'=> $remise,
                    )
                )	or die(print_r($req->errorInfo()));
                $req->closeCursor();*/
                if( ! $test){
                    echo "<font color=\"red\"> La facture N° ".$numerofacture." n'est pas insérée dans facture !!! </font>"; 
                    die('paml');
                }
            }
        }
            
        $a=0;
        if($test and $testDroit){ // pour nouvelle facture et modification facture

            
            /* $req=$bdd->prepare("INSERT INTO articlesdefacture (idsuccursale, numerofacture, idarticle, quantite, prixunitaire) 
                values(:idsuccursale, :numerofacture, :idarticle, :quantite, :prixunitaire)");
            */
            $idchoisilist = explode("/", htmlspecialchars($_POST['idchoisilist']));
            $idList = array();
            $test = true;
            foreach($idchoisilist as $oneid) {
                if( ! in_array($oneid, $idList) && isset($_POST[$oneid."_2"]) && $_POST[$oneid."_2"]!=NULL ){
                    $idList[$a] = $oneid;
                    $a ++;
                
                    $quantite = htmlspecialchars($_POST['quantite-'.$oneid."_2"]);
                    $quantite = str_replace(" ", "", $quantite);
                    $quantite = str_replace(",", ".", $quantite);
                    $quantite = str_replace(",", ".", $quantite);

                    $prixunitaire = htmlspecialchars($_POST['prixdetail-'.$oneid."_2"]);
                    if($prixunitaire =="")
                        $prixunitaire = htmlspecialchars($_POST['prixgros-'.$oneid."_2"]);

                    $test = $test && $this->model->insert("articlesdefacture", array('idsuccursale'=>session("idsuccursale"), 'numerofacture'=>$numerofacture,
                        'idarticle'=>$oneid, 'quantite'=>$quantite,
                        'prixunitaire'=>$prixunitaire));
                        
                    /* $test = $req->execute(array('idsuccursale'=>session("idsuccursale"), 'numerofacture'=>$numerofacture,
                        'idarticle'=>$oneid, 'quantite'=>$quantite,
                        'prixunitaire'=>$prixunitaire)) or die(print_r($req->errorInfo(), TRUE)); */
                }
            }$req->closeCursor();
            session('message', "<font color = green size = 4>Facture N° ".$numerofacture." est modifi&eacute;e avec succ&egrave;s.</font>");
        }

        if( $test and $a == 0 and ! $isModification){
            $test = $this->delete("facture", array('numerofacture'=>$numerofacture, 'idsuccursale'=>session("idsuccursale")));

            /* $req=$bdd->prepare("DELETE FROM `facture` WHERE `idsuccursale`= :idsuccursale and `numerofacture` = :numerofacture");
            $test = $req->execute(array('numerofacture'=>$numerofacture, 'idsuccursale'=>session("idsuccursale"))) or die(print_r($req->errorInfo(), TRUE)); $req->closeCursor(); */
        }
        if(( ! $isModification and $a != 0 )){

            $test = $this->update("derniernumerofacture", array('numerofacture'=>$numerofacture), array('idsuccursale'=>session("idsuccursale")));

            /* $req=$bdd->prepare("UPDATE derniernumerofacture SET numerofacture = :numerofacture 
                    WHERE idsuccursale = :idsuccursale LIMIT 1");
            $test = $req->execute(array('numerofacture'=>$numerofacture, 'idsuccursale'=>session("idsuccursale")))
                    or die(print_r($req->errorInfo(), TRUE)); $req->closeCursor(); */
            session('message', "<font color = green size = 4>Facture N° ".$numerofacture." est prète pour ".htmlspecialchars($_POST['prenom'])." ".htmlspecialchars($_POST['nom']).".</font>");
            
            if($test and (session('idtypeemploi') <= 3) 
                and (isset($_POST['totalementpayee']) /*checkbox*/ or $_POST['facturepayee'] != null
                    or isset($_POST['facturelivree']) or isset($_POST['autoriselivree']) )){
                //encaissement autorisation livraison et Livraison
                require_once('encaiment_livraison.php'); //ici
            }
            if(isset($_POST['A4']) or isset($_POST['ticketcaisse'])){
                
                ob_start();
                    include("affichagefacture.php"); //ici
                session('ob_start1', ob_get_clean());

                // if(isset($_POST['A4'])) session('A4'] = "pam";
                // if(isset($_POST['ticketcaisse'])) session('ticketcaisse') = "pam";
            
            }
        }
     /* }else{
        if(session('message') == "") session('message', <font color = green size = 3>Il semble que la facture est bien établie. Veillez identifier son numéro dans Livraison.</font>");
        sessionAppend('message', "<br/><font color = red  size = 3>Tentative de plusieurs insertions : Seule la 1<sup>ère</sup> a réussi.</font>");
     } */
    	
        $this->goToIndex(); //ici
	
		//session('numerofacture', $numerofacture);
		//header('Location:'.$_SESSION['rep'].'/affichagefacturepdf.php');
    }





    
    public function recaptchaTest(){ //ici
        $key = "6Ldkd7MUAAAAAL9KFWoFF8BVc9RiWCzr32LUP0N8";  //for senoptimizer.com
        $pam = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$key."&response=".$_POST['g-recaptcha-response']);
        $response = json_decode($pam);
        if(($response->success == 1 and $response->success >= 0.6) )
            return true;
        else
            return false;
    }
    public function dateFormatSep ($date, $sepentree, $sepsortie){ //	$sepantree (séparateur entrée) : -, / ; $sepsortie ...
        return str_replace($sepentree, $sepsortie, $date);
    } 
    public function dateFormatlan ($date, $sep){//	$sep (séparateur) : -, /
        $explode = explode($sep, $date);
        return $explode[2]."".$sep."".$explode[1]."".$sep."".$explode[0];
    }
    public function dateEn ($date){//	$sep (séparateur) : -, /
        $date = str_replace("/", "-", $date);
        $explode = explode("-", $date);
        if(strlen($explode[0]) == 4)
            return $date;
        else
            return "$explode[2]-$explode[1]-$explode[0]";
    }

    public function my_number_format ($number){
        $numberTab = explode(".", $number);
        if(isset($numberTab[1]) and intval($numberTab[1]) > 0)
            $decimal = session("ncv");
        else
            $decimal = 0;
        return number_format($number, $decimal,","," ");
    }

    public function goToIndex(){ //ici
        header('Location:'.$_SESSION['rep'].'/index.php');
    }
    public function popup($page, $width = 900, $height = 650, $left = 2){?>  
        <SCRIPT LANGUAGE='JavaScript'>
            width = <?= $width ?>;
            height = <?= $height ?>;       
            if(window.innerWidth){
                var left = (window.innerWidth-width)/<?= $left ?>;
                var top = (window.innerHeight-height)/2;
            }else{
                var left = (document.body.clientWidth-width)/<?= $left ?>;
                var top = (document.body.clientHeight-height)/2;
            }
            window.open('<?= $page?>','', 
            ' top='+top+', left='+left+', width='+width+', height='+height+', resible=no');
            //window.open('facturepdf.php','_blank');
        </SCRIPT><?php
    }
    public function for_impression(){  ?> 
        <form method="post" action="facturepdf.php" target="_blank">
            <fieldset style="width:250px">
                <!-- <input type="text"  name="imprimer" value="imprimer" hidden /> -->
                <img src="<?= session('ogb') ?>/images/print.png" style="width:27px;height:25px">
                <b>:</b> &nbsp;
                <input <?= session("tc_default") ? 'checked' : '' ?> type="checkbox" name="ticketcaisse" id="ticketcaisse">
                <label for="ticketcaisse">Ticket caisse / </label>
                <input type="checkbox" name="A4" id="A4">
                <label for="A4">A4</label>
                <input hidden type="text" name="numerofacture" value="<?php echo ($_POST['numerofacture0'] != "") ? $_POST['numerofacture0'] : $_POST['numerofacture'] ?>">
                <input style="width:100%" class="submitcorp" type=submit  name="imprimer" value="Imprimer" accesskey="2"/>
            </fieldset>
            <?= csrf()?>/>
        </form><?php
    }

    public function divquantiteprix($idarticle, $quantite, $prixunitaire, $bdd, $articlOnly_pamtr = false, $succ = []){
        
        $pam = $succ['pieceargentmin'];

        $prixvente_1 = " CASE WHEN ".$succ['plus3']." != 0 THEN CONCAT(ROUND(s.prixvente1 * ".(1 + $succ['plus3']/100.)." / ".$pam.") * ".$pam.",' (+".$succ['plus3']."%)') ELSE '-1' END AS prixvente1,";
        $prixvente_2 = " CASE WHEN ".$succ['plus2']." != 0 THEN CONCAT(ROUND(s.prixvente1 * ".(1 + $succ['plus2']/100.)." / ".$pam.") * ".$pam.",' (+".$succ['plus2']."%)') ELSE '-1' END AS prixvente2,";
        $prixvente_3 = " CASE WHEN ".$succ['plus1']." != 0 THEN CONCAT(ROUND(s.prixvente1 * ".(1 + $succ['plus1']/100.)." / ".$pam.") * ".$pam.",' (+".$succ['plus1']."%)') ELSE '-1' END AS prixvente3,";
        
        $prixvente_4 = " CONCAT(s.prixvente1,' (DEPOT)') AS prixvente4,";

        $prixvente_5 = " CASE WHEN ".$succ['moins1']." != 0 THEN CONCAT(ROUND(s.prixvente1 * ".(1 - $succ['moins1']/100.)." / ".$pam.") * ".$pam.",' (-".$succ['moins1']."%)') ELSE '-1' END AS prixvente5,";
        $prixvente_6 = " CASE WHEN ".$succ['moins2']." != 0 THEN CONCAT(ROUND(s.prixvente1 * ".(1 - $succ['moins2']/100.)." / ".$pam.") * ".$pam.",' (-".$succ['moins2']."%)') ELSE '-1' END AS prixvente6,";
        $prixvente_7 = " CASE WHEN ".$succ['moins3']." != 0 THEN CONCAT(ROUND(s.prixvente1 * ".(1 - $succ['moins3']/100.)." / ".$pam.") * ".$pam.",' (-".$succ['moins3']."%)') ELSE '-1' END AS prixvente7";

        $prix_s = $prixvente_1.$prixvente_2.$prixvente_3.$prixvente_4.$prixvente_5.$prixvente_6.$prixvente_7;

        //stockcourant
        $sql = "SELECT DISTINCT a.idarticle as idarticle, a.nom as anom, a.reference as reference, 
            a.seuil as seuil, a.referenceinternationale as referenceinternationale, a.idmarque as idmarque,
            a.idsuperarticle as idsuperarticle, m.nom as mnom, ".$prix_s.", s.quantite as quantite
            FROM article a, marque m, stockcourant".session('idsuccursale')." s
            WHERE a.idmarque = m.idmarque and a.idarticle = s.idarticle and a.idarticle = $idarticle LIMIT 1";
        $req = $bdd->prepare($sql);
        $req->execute() or die(print_r($req->errorInfo()));
        $info = $req->fetch(); $req->closeCursor();

        $anom = $info['anom'].' ('.$info['reference'].')';
        $quantiteDispo = $info['quantite'];

        $prix_s = "";
        if($info['prixvente1'] != -1){
            $tmp = explode(" ", $info['prixvente1']);
            $prix_s .= '<option value="'.$tmp[0].'">'.str_replace(".",",", $this->my_number_format($tmp[0]))." ".$tmp[1].'</option>';
        }if($info['prixvente2'] != -1){
            $tmp = explode(" ", $info['prixvente2']);
            $prix_s .= '<option value="'.$tmp[0].'">'.str_replace(".",",", $this->my_number_format($tmp[0]))." ".$tmp[1].'</option>';
        }if($info['prixvente3'] != -1){
            $tmp = explode(" ", $info['prixvente3']);
            $prix_s .= '<option value="'.$tmp[0].'">'.str_replace(".",",", $this->my_number_format($tmp[0]))." ".$tmp[1].'</option>';
        }if($info['prixvente4'] != -1){
            $tmp = explode(" ", $info['prixvente4']);
            $prix_s .= '<option value="'.$tmp[0].'">'.str_replace(".",",", $this->my_number_format($tmp[0])).'</option>';
        }if($info['prixvente5'] != -1){
            $tmp = explode(" ", $info['prixvente5']);
            $prix_s .= '<option value="'.$tmp[0].'">'.str_replace(".",",", $this->my_number_format($tmp[0]))." ".$tmp[1].'</option>';
        }if($info['prixvente6'] != -1){
            $tmp = explode(" ", $info['prixvente6']);
            $prix_s .= '<option value="'.$tmp[0].'">'.str_replace(".",",", $this->my_number_format($tmp[0]))." ".$tmp[1].'</option>';
        }if($info['prixvente7'] != -1){
            $tmp = explode(" ", $info['prixvente7']);
            $prix_s .= '<option value="'.$tmp[0].'">'.str_replace(".",",", $this->my_number_format($tmp[0]))." ".$tmp[1].'</option>';
        }

        $prixventemin = 0;//$prixvente3;
        //var prixvente3_ = '<option //value="'+prixvente3+'">'+prixvente3.replace(".",",")+'</option><option></option>';
        //if(idtypeemploi > 1){ prixventemin = prixvente2;	prixvente3_ = '<option></option>';}
                    
        $couleur = "green";
        if($quantiteDispo == $info['seuil']) 		$couleur = "#FE9A2E"; //orange
        else if($quantiteDispo < $info['seuil'])	$couleur = "red";				
    
        // $pamtr = "";
        if($articlOnly_pamtr == false){
            $articleWidth = "60%";
            $quantiteWidth = "7%";
            $articlOnlyDisplay = '';
            $autrePrix = '<option value="" selected>-----:-----</option>';
            $basculevente2 = 'onchange="basculevente2(this);"';
        }
        else {
            $articleWidth = "78%";
            $quantiteWidth = "22%";
            $articlOnlyDisplay = 'style="display:none"';
            $autrePrix = '';
            $basculevente2 = "";
            // if($articlOnly_pamtr != true)
            // 	$pamtr = $articlOnly_pamtr; // la page article stock peut avoir plusieur id="pamtr" ...
        }
        if($prixunitaire == "") $prixunitaire = 0;

        ob_start(); ?>
        <table border width="100%" style="background:white;border-collapse:collapse;"><tr>
            <td width="<?= $articleWidth ?>"><input name="<?= $idarticle.'_2'?>" type="checkbox" checked
                onclick="basculevente('<?= 'qp-'.$idarticle?>')" id="<?= $idarticle.'_2'?>"/>						
                <label for="<?= $idarticle.'_2'?>" style="font-weight:bold; font-size:100%;"><?= $anom?></label></td>
            <td width="7%" <?= $articlOnlyDisplay ?>>
                <font color="<?= $couleur?>"><abbr title="Qantit&eacute; disponible" lang="fr">
                <div style="width:42px;"><?= $quantiteDispo?></div></abbr></font></td>
            <td width="<?= $quantiteWidth; ?>" align="right">
            <input type="number" name="<?= 'quantite-'.$idarticle.'_2'?>" step="<?= $succ['stepqte']?>" id="<?= 'quantite-'.$idarticle.'_2'?>" style="width:76px;"
                <?= $basculevente2 ?> value="<?= $quantite?>" min="0" /> </td>
            <td width="11%" align="right" <?= $articlOnlyDisplay ?>>
            <input type="number" name="<?= 'prixdetail-'.$idarticle.'_2'?>"
                id="<?= 'prixdetail-'.$idarticle.'_2'?>" style="width:80px;"
                <?= $basculevente2 ?> value="<?= $prixunitaire?>" min="<?= $prixventemin;?>" step="<?= $succ['pieceargentmin']?>"/></td>
            <td width="15%" align="right" <?= $articlOnlyDisplay ?>>
            <SELECT style="width:126px;" name="<?= 'prixgros-'.$idarticle.'_2'?>"
                <?= $basculevente2 ?> id="<?= 'prixgros-'.$idarticle.'_2'?>">
                <?= $prix_s?>
                <?= $autrePrix ?></SELECT></td>
            </tr>
        </table>
        <input hidden name="<?= '2-'.$idarticle.'_2'?>" type="checkbox" id="<?= '2-'.$idarticle.'_2'?>" checked />
        <input hidden type="number" name="<?= 'quantite2-'.$idarticle.'_2'?>" step="<?= $succ['stepqte']?>" id="<?= 'quantite2-'.$idarticle.'_2'?>" value="<?= $quantite?>"/>
        <input hidden type="number" name="<?= 'prixdetail2-'.$idarticle.'_2'?>" id="<?= 'prixdetail2-'.$idarticle.'_2'?>" value="<?= $prixunitaire?>" step="<?= $succ['pieceargentmin']?>" />
        <input hidden type="number" name="<?= 'prixgros2-'.$idarticle.'_2'?>" id="<?= 'prixgros2-'.$idarticle.'_2'?>" value="3" /><?php
        $ob_start = ob_get_clean();
        return $ob_start;
    }

    /*			
    function local(){
        $ip = $_SERVER['REMOTE_ADDR'];
        $http_host = $_SERVER['HTTP_HOST'];
        if( strpos($ip, '192.168') == 0 and strpos($ip, '::1') == 0 and strpos($http_host, 'localhost') == 0 )
            return ! false;
        else 
            return ! true;
    }*/

    public function client_fct(){ //NOUVEAU client
        session('message', "");
        if( ! session('connection'))
            $clientEnLigne1 = true;
        else 
            $clientEnLigne1 = false;
        if( $clientEnLigne1 and isset($_POST['idsuccursale'])){
            $idsuccursale = $this->model->first("succursale", ['idsuccursale'=>$_POST['idsuccursale']])['idsuccursale'];
            /* $req=$bdd->prepare("select idsuccursale from succursale where idsuccursale= :idsuccursale limit 1");
            $req->execute(array('idsuccursale'=>$_POST['idsuccursale'])); 
            $idsuccursale = $req->fetch()['idsuccursale'];
            $req->closeCursor(); */
            if($idsuccursale != $_POST['idsuccursale']){
                sessionAppend('message', "<font color='red'>Cette succursale n'existe pas</font>");
                $this->goToIndex(); //ici
            }else
                session('idsuccursale', $idsuccursale);
        }
        $client = "";
        $idclient = "";
        $tel2 = "";

        // 00221 77 271 76 55 ou +221 77 271 76 55
        // tel -> //+221772717655 ou 00221772717655
        // tel2-> //772717655
        if(isset($_POST["tel"])){
            $tel 	 = htmlspecialchars(trim($_POST["tel"]));
            $tel 	 = str_replace(" ", "",$tel);
            $tel 	 = str_replace("-", "",$tel);
            $tel 	 = str_replace(".", "",$tel);
            $tel2 	 = str_replace("+", "",$tel);
            if($tel2[0] == 0) $tel2 = substr($tel2, 1);
            if($tel2[0] == 0) $tel2 = substr($tel2, 1);
            $tel2 	 = str_replace("221", "",$tel); //TODO pamboup à généraliser
        }
        if(strlen($tel2) == 9){
            $client = $this->model->first("client", ["idsuccursale" =>session("idsuccursale"), "tel like"=> "%".$tel2."%"]);
            /* $req=$bdd->prepare("SELECT * FROM client where idsuccursale = ".session("idsuccursale")." and tel like '%".$tel2."%' limit 1");$req->execute() or die(print_r($req->errorInfo()));
            $client = $req->fetch(); $req->closeCursor(); */
        }
        if(! empty($client)){//tentative de répétition client
            session('message', "<font color=\"red\"> Client déjà dans la base de données !!! </font>");
            $idclient = $client["idclient"];

        //on exige le téléphone que pour les clients en ligne pour l'instant. //TOTO pamboup 
        }else if(strlen($_POST["nom"]) > 0 and (strlen($tel2) == 9 or ! $clientEnLigne1 )){
            $idclient = $this->model->max("client", "idclient as idclient")["idclient"]+1;
            /* $req=$bdd->prepare("SELECT max(`idclient`) as idclient FROM client");$req->execute()or die(print_r($req->errorInfo()));
            $idclient = $req->fetch()["idclient"]+1; $req->closeCursor(); */
            
            $prenom  = trim(htmlspecialchars($_POST["prenom"]));
            $nom 	 = trim(htmlspecialchars($_POST["nom"]));
            $email 	 = trim(htmlspecialchars($_POST["email"]));
            $adresse = trim(htmlspecialchars($_POST["adresse"]));
            if(isset($_POST["detail"]))
                $detail  = trim(htmlspecialchars($_POST["detail"]));
            else
                $detail = "";
            if(isset($_POST["type"]))
                $type  = 2 ; //client revendeur
            else
                $type = 1; //client simple

            $all = $prenom.' '.$nom.' '.$tel.' '.$email.' '.$adresse.' '.$detail;
            $all = trim($all);
            $all = str_replace("  ", " ",trim($all));
            $all = str_replace("  ", " ",$all);
            $all = str_replace("  ", " ",$all);

            $test = $this->model->insert("client", array('idsuccursale'=>session("idsuccursale"), 'idclient'=>$idclient, 'prenom'=>$prenom, 'nom'=>$nom, 'tel'=>$tel, 'email'=>$email,'adresse'=>$adresse, 'detail'=>$detail, 'type'=>$type, 'all' => $all));

            /* $req=$bdd->prepare("INSERT INTO client (idsuccursale, idclient, prenom, nom, tel, email, adresse, detail, `type`, `all`) 
                values(:idsuccursale, :idclient, :prenom, :nom, :tel, :email, :adresse, :detail, :type, :all)");
            $test = $req->execute(array('idsuccursale'=>session("idsuccursale"), 'idclient'=>$idclient, 'prenom'=>$prenom, 'nom'=>$nom,
            'tel'=>$tel, 'email'=>$email, 'adresse'=>$adresse, 'detail'=>$detail, 'type'=>$type, 'all' => $all))
                or die(print_r($req->errorInfo()));
            $req->closeCursor(); */

            if( ! $test){
                session('message', "<font color=\"red\"> Le client n'est pas inséré dans la base de données !!! </font>");}
            else{
                session('message', "<font color='#0ECBF6'> Client ".htmlspecialchars($_POST['prenom'])." ".htmlspecialchars($_POST['nom'])." ".htmlspecialchars($_POST['tel'])." ajouté.</font>");
                //header('Location:'.$_SESSION['rep'].'/index.php');
            }
        }
        return $idclient;
    }
    public function livraison_a_distance($alivreadom){
        if($alivreadom == 1)
            echo '<label><font color="#007bff">Livraison à distance</font></label>';
        else
            echo '<label>Livraison sur place</label>';
        echo "<br>";
    }
    public function downloadble($data, $fileNameWithExtension = "download.xls"){
        $tmp = explode(".", $fileNameWithExtension);
        $extension = $tmp[count($tmp)-1];
        header("Content-Type: application/$extension");
        header("Content-Disposition: attachment; filename=$fileNameWithExtension");
        echo iconv( 'UTF-8' ,'Windows-1252', $data);
    }

    public function existsTable($tabName){
        return $this->model->isExiste($tabName);
    }
}