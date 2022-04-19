<?php
define('msgs', [

    "csrf" => "Sécurité Il est obligatoire de mettre le champ pour le token csrf Il suffit d'appeler csrf() ou d'utiliser la fonction input('submit',...) dans votre formulaire !",
    "csrfExpiration" => "Sécurité Le token csrf est expiré. Veuillez ressoumettre le formulaire !",

    "honeyPotDevMode" => "Honey Pot erreur (anti spam et robot): Le champs honey pot (pamhopo) est anormalement remplit !",
    "honeyPotProdMode" => "Pour des mesures de sécurité, le formulaire n'est pas traité. Veuillez recommancer ou contacter votre administrateur !",
    
    "captchaConnecting" => "Sécurité connecting ...", //pour le bouton de soumission

    "keyMissing" => "Cette cléword1 n'est pas trouvée dans la donnée !",
    "functionName" => "C'est la fonction ",

    "required" => "requis",
    "retour" => "Retour à la page précédante.",
    "accueil" => "Retour à la page d'accueil.",
    "or" => "ou",
    "and" => "et",
    "notExistRule" => " n'existe pas (ou pas encore) comme règle de validation !",
    "preview" => "Aperçu",
    "close" => "Fermer",
    "open" => "Ouvrir",
    "noFile" => "Il n\'y a pas de fichier",
    "noImage" => "Il n\'y a pas d'image",
    "htlmTableSortKey" => "sort",
    "htlmTableDirKey" => "dir",
    "searchFilter" => "Filtres ",
    "searchFilterAll" => "Tout",
    "createSubmitButton" => "Envoyer",
    "updateSubmitButton" => "Modifier",
    "search" => 'Recherche <i class="material-icons" style="font-size:15px;">search</i>',
    "typeaheadSearchSuffix" => ' <i class="material-icons" style="font-size:15px;">search</i>', //' (Recherche)'
    'previous' => '&laquo; Précédent', // ❮❮  «  &laquo;
    'next'     => 'Suivant &raquo;', // ❯❯   »  &raquo;
    'paginatioArrows' => true,
    'previousArrow' => '❮❮', // ❮❮  «  &laquo;
    'nextArrow' => '❯❯',  // ❯❯   »  &raquo;   
    'actionText' => "Action", // for table's show, modification or deletion 
    'actionsText' => "Actions", // for table's show, modification or deletion 
    'showActionButtonsWhenAccessDenied' => true, //show but desactivate them

    'confirmTitle' => "Êtes vous sure ?", //Are you sure?
    'confirmText' => "Vous ne pourrez pas revenir en arrière!", //You won't be able to 'revert' => this!
    'confirmConfirmButtonText' => "Oui, supprimez-le!", //Yes, delete it!,
    'confirmCancelButtonText' => "Non, annulez!", //No, cancel!,
    'confirmDeletedTitle' => "Supprimé!",//Deleted!
    'confirmDeletedText' => "Votre fichier a été supprimé.", //Your file has been deleted.
    'confirmCancelledTitle' => "Annulé", //Cancelled
    'confirmCancelledText' => "Votre enregistrement est en sécurité)", //Your recording is safe or Your imaginary file is safe),
    'confirmButtonColor' => "success",
    'confirmCancelColor' => "danger",
    'confirmErrorTitle' => "Erreur",
    'confirmErrorText' => "La suppression n'a pas about!",
    
    //'passwordConfirm' => "Confirmation de mot de passe",
    'loginError' => "Login ou mot de passe est invalide !",
    'loginErrorColor' => "Login ou mot de passe est invalide !",
    'loginSuccess' => "Connexion avec succès !",
    'mayBeDeconnected' => "Il se que votre session soit expirée! Veuillez dans ce cas vous re-conecter.
    See more details of the error in console",

    //crud
    'create' => "Ajouter",
    'createCardHeader' => "Ajout d'un", 
    'createCardFirstLine' => "", 
    'read' => "Lister",
    'readCardHeader' => "Liste des", 
    'readCardFirstLine' => "", 
    'show' => "Afficher",
    'showCardHeader' => "Affichage d'un", 
    'showCardFirstLine' => "", 
    'update' => "Modifier", //TODO voir si c'est utile
    'updateCardHeader' => "Modification d'un", 
    'updateCardFirstLine' => "", 
    'delete' => "Supprimer", //TODO voir si c'est utile
    'deleteCardHeader' => "Suppression d'un", //TODO voir si c'est utile
    'deleteCardFirstLine' => "", //TODO voir si c'est utile
    'logIn' => "Se connecter", // Authentification
    'logInCardHeader' => "Se connecter",
    'logInCardFirstLine' => "", //TODO voir si c'est utile
    'logOut' => "Se déconnecter",
    'logOutCardHeader' => "Se déconnecter",
    'logOutCardFirstLine' => "", //TODO voir si c'est utile
    'registration' => "S'enregistrer",
    'registrationCardHeader' => "S'enregistrer",
    'registrationCardFirstLine' => "", //TODO voir si c'est utile
    
    'download' => "Télécharger", //TODO voir si c'est utile
    'downloadCardHeader' => "Téléchargement d'un", //TODO voir si c'est utile
    'downloadCardFirstLine' => "", //TODO voir si c'est utile
    
    'print' => "Imprimer", //TODO voir si c'est utile
    'printCardHeader' => "Impression d'un", //TODO voir si c'est utile
    'printCardFirstLine' => "", //TODO voir si c'est utile
    
    'crudWordFistOnRoute' => true,
    'crudWordFistOnPage' => true,
    'accessDenied' => "Accès refusé",
    
]);
function msg($key){ 
 switch ($key) { //j'adapte cette methode pour linstant pour faciliter l'accès js
  case"csrf": return "Sécurité Il est obligatoire de mettre le champ pour le token csrf Il suffit d'appeler csrf() ou d'utiliser la fonction input('submit',...) dans votre formulaire !";
  case"csrfExpiration": return "Sécurité Le token csrf est expiré. Veuillez ressoumettre le formulaire !";

  case"honeyPotDevMode": return "Honey Pot erreur (anti spam et robot): Le champs honey pot (pamhopo) est anormalement remplit !";
  case"honeyPotProdMode": return "Pour des mesures de sécurité, le formulaire n'est pas traité. Veuillez recommancer ou contacter votre administrateur !";
  
  case"captchaConnecting": return "Sécurité connecting ..."; //pour le bouton de soumission

  case"keyMissing": return "Cette cléword1 n'est pas trouvée dans la donnée !";
  case"functionName": return "C'est la fonction ";

  case"required": return "requis";
  case"retour": return "Retour à la page précédante.";
  case"accueil": return "Retour à la page d'accueil.";
  case"or": return "ou";
  case"and": return "et";
  case"notExistRule": return " n'existe pas (ou pas encore) comme règle de validation !";
  case"preview": return "Aperçu";
  case"close": return "Fermer";
  case"open": return "Ouvrir";
  case"noFile": return "Il n\'y a pas de fichier";
  case"noImage": return "Il n\'y a pas d'image";
  case"htlmTableSortKey": return "sort";
  case"htlmTableDirKey": return "dir";
  case"searchFilter": return "Filtres ";
  case"searchFilterAll": return "Tout";
  case"createSubmitButton": return "Envoyer";
  case"updateSubmitButton": return "Modifier";
  case"search": return 'Recherche <i class="material-icons" style="font-size:15px;">search</i>';
  case"typeaheadSearchSuffix": return ' <i class="material-icons" style="font-size:15px;">search</i>'; //' (Recherche)'
  case'previous': return '&laquo; Précédent'; // ❮❮  «  &laquo;
  case'next'    : return 'Suivant &raquo;'; // ❯❯   »  &raquo;
  case'paginatioArrows': return true;
  case'previousArrow': return '❮❮'; // ❮❮  «  &laquo;
  case'nextArrow': return '❯❯';  // ❯❯   »  &raquo;   
  case'actionText': return "Action"; // for table's show, modification or deletion 
  case'actionsText': return "Actions"; // for table's show, modification or deletion 
  case'showActionButtonsWhenAccessDenied': return true; //show but desactivate them

  case'confirmTitle': return "Êtes vous sure ?"; //Are you sure?
  case'confirmText': return "Vous ne pourrez pas revenir en arrière!"; //You won't be able to 'revert': return this!
  case'confirmConfirmButtonText': return "Oui, supprimez-le!"; //Yes, delete it!,
  case'confirmCancelButtonText': return "Non, annulez!"; //No, cancel!,
  case'confirmDeletedTitle': return "Supprimé!";//Deleted!
  case'confirmDeletedText': return "Votre fichier a été supprimé."; //Your file has been deleted.
  case'confirmCancelledTitle': return "Annulé"; //Cancelled
  case'confirmCancelledText': return "Votre enregistrement est en sécurité)"; //Your recording is safe or Your imaginary file is safe),
  case'confirmButtonColor': return "success";
  case'confirmCancelColor': return "danger";
  case'confirmErrorTitle': return "Erreur";
  case'confirmErrorText': return "La suppression n'a pas about!";
       
  //'passwordConfirm': return "Confirmation de mot de passe";
  case'loginError': return "Login ou mot de passe est invalide !";
  case'loginErrorColor': return "Login ou mot de passe est invalide !";
  case'loginSuccess': return "Connexion avec succès !";
  case'mayBeDeconnected': return "Il se peut que votre session soit expirée! Veuillez dans ce cas vous re-conecter. S'il s'agit de remplissage de formulaire, vous pouvez vous re-connecter en ouvrant un nouvel onglet et revenir sur celui-ci continuer votre remplissage. See more details of the error in console";

  //crud
  case'create': return "Ajouter";
  case'createCardHeader': return "Ajout d'un"; 
  case'createCardFirstLine': return ""; 
  case'read': return "Lister";
  case'readCardHeader': return "Liste des"; 
  case'readCardFirstLine': return ""; 
  case'show': return "Afficher";
  case'showCardHeader': return "Affichage d'un"; 
  case'showCardFirstLine': return ""; 
  case'update': return "Modifier"; //TODO voir si c'est utile
  case'updateCardHeader': return "Modification d'un"; 
  case'updateCardFirstLine': return ""; 
  case'delete': return "Supprimer"; //TODO voir si c'est utile
  case'deleteCardHeader': return "Suppression d'un"; //TODO voir si c'est utile
  case'deleteCardFirstLine': return ""; //TODO voir si c'est utile
  case'logIn': return "Se connecter"; // Authentification
  case'logInCardHeader': return "Se connecter";
  case'logInCardFirstLine': return ""; //TODO voir si c'est utile
  case'logOut': return "Se déconnecter";
  case'logOutCardHeader': return "Se déconnecter";
  case'logOutCardFirstLine': return ""; //TODO voir si c'est utile
  case'registration': return "S'enregistrer";
  case'registrationCardHeader': return "S'enregistrer";
  case'registrationCardFirstLine': return ""; //TODO voir si c'est utile
       
  case'download': return "Télécharger"; //TODO voir si c'est utile
  case'downloadCardHeader': return "Téléchargement d'un"; //TODO voir si c'est utile
  case'downloadCardFirstLine': return ""; //TODO voir si c'est utile
       
  case'print': return "Imprimer"; //TODO voir si c'est utile
  case'printCardHeader': return "Impression d'un"; //TODO voir si c'est utile
  case'printCardFirstLine': return ""; //TODO voir si c'est utile
       
  case'crudWordFistOnRoute': return true;
  case'crudWordFistOnPage': return true;
  case'accessDenied': return "Accès refusé";
  default : return notice("<b>$key</b> doesn't exit");
 }
}