<?php
class Panier{
	private $db;
	public function __construct($db){
		$this->db = $db;
		if( ! isset($_SESSION))
			session_start();
		// if( ! isset(session('panier')))
		// 	sessionAppendArr('panier', array());
		if(isset($_GET['delPanier'])){
			$this->del($_GET['delPanier']);
		}
		if(isset( $_POST['panier']['quantity'] )){
			$this->recalculer();
			
		}
		if(isset($_POST['commander'])){
			if($this->totalQuantity() > 0)
				echo $this->commander();
		}
	}
	public function recalculer(){
		//$_SESSION['panier'] = $_POST['panier']['quantity']; // moins sure; pour éviter cetaines injections...
		foreach(session('panier') as $produit_id => $quatity){
			if(isset($_POST['panier']['quantity'][$produit_id])){	
				session2( 'panier', $produit_id, intval($_POST['panier']['quantity'][$produit_id]) );
			}
		}
	}
	public function commander(){
		if(session2('client', 'iduser')){
			$this->recalculer();		
					
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
	public function add($produit_id){
		if(session2('panier', $produit_id)){
			session2('panier', $produit_id, "++");
		}else{
			session2('panier', $produit_id, 1);
		}
	}
	public function del($produit_id){
		if(session2('panier', $produit_id)){
			unsetSession('panier', $produit_id);
		}
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
}


?>