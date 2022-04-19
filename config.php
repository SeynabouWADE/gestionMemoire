<?php

//vente en ligne
define("articleDbTable", "article");


//Production or development mode
define("devMode", true);  //to addapte the kine of errors messages showing
define("bgBlack", false);
define("errorMessage", "Page introuvable"); //en mode production
//define("idsession", "Page introuvable"); //en mode production 

define("tooltipImportOptional", true); //si c'est true alors l'import dépend de l'appelle de la funtion tooltip() de php. Sino c'est à vous de faire l'import manuellement dans le template, ansi vous pouvez utiliser la funtion tooltip() de js. Dans tous les cas tooltip() de php marche.

define('lang', "fr");
//define('lang', navigatorLang()); //navigatorLang(), fr, en, ar, wo (pour le wolof c'est possible...) TODO
define('provideInternationalization', true); // Prévoir internationalisation: util pour la génération de code/base de données
define('fallbackLocale', 'fr'); // Langue par défaut TODO is util ?
// dateFormat, dateTimeFormat and timeFormat
if(lang == "en"){
    define('dateFormat', 'Y/m/d'); // see date_format on php 
    define('dateTimeFormat', 'Y/m/d H:i:s'); // see date_format on php 
    define('timeFormat', 'H:i:s'); // see date_format on php
}else{
    define('dateFormat', 'd/m/Y'); // see date_format on php
    define('dateTimeFormat', 'd/m/Y H:i:s'); // // see date_format on php
    define('timeFormat', 'H:i:s'); //'g:i A' (5:45 PM), 'G:ia' (05:45pm)... see date_format on php
}
//

define('numberOfDecimal', 2);
define('dec_point', ','); // , or .
define('thousands_sep', "&#160;"); //&#160; (espace insécable) or, 
define('delDecimalPartIfItsNull', true); //Pour ne pas afficher la partie décimale quand c'est null.
define('defaultColors', ['primary', 'info', 'success', 'danger', 'warning', 'default', 'rose', 'gray', 'white', 'secondary', 'light', 'dark', 'hide', 'muted']);
//define ('progressBarColor', ["#008000","#32CD32","#00FF00","#7CFC00","#7FFF00","#ADFF2F","#FFFACD","#F0E68C","#FFD700","#ffcZ00","#fc5b47","#DC143C","#B22222"]);//TODO add ajout  
define ('progressBarColor', ["#00C800", "#6EFF0F", "#B9FF00", "#C6F307", "#FFFF00", "#FFDC00", "#FFB900", "#FF6400", "#FF0000"]);
define('spinnerType', "grow"); //grow, border
define('spinnerColor', "default"); // default, primary, success, danger, warning, info, light, dark
define("loadingText", "chargement..."); //TODO internationnaliser
define("template", "tools/template"); // Must is in viewPkg and viewPkg can be "".
define("errorTemplate", "tools/errorTemplate"); // Must is in viewPkg and viewPkg can be "".
define("formFunctions", "formFunctions2.php");
define("multipleSubmitAccepted", "azerty12345");
//For the data base
define("host", "127.0.0.1");
define("username", "root");
define("password", "");
define("database", "depot_memoire");

define("submitName", "pamboupSub");

define("withoutDefault", "tokencsrf,pamhopo,submit,".submitName.",password_confirmation,ttQ"); // util si on veux soumtre tout un post excepter ces champs
define("addCreateSeparators", [",", ";", "/", " "]); // order us importante and it is space by default
define("primaryKeyFiel", "id");
define("sqlIntegerTab", ["tinyint", "mediumint", "smallint", "int", "bigint", "bit"]);
define("sqlRealTab", ["float", "double", "decimal"]);
define("sqlNumericTab",array_merge(sqlRealTab, sqlIntegerTab));
define("sqlDateTimeTab", ["date", "time", "year", "timestamp"]);
define("sqlStringTab", ["char", "varchar", "tinytext", "text", "mediumtext", "longtext"]);
define("sqlListTab", ["set", "enum"]);
define("numberLinesPerPage", 20);
define("numeric",array_merge(["numeric","integer"], sqlNumericTab));
define("dateTime",array_merge(sqlDateTimeTab, ['datetime']));

define("pagintionMaxNumber", 7);
define("buttonStyle", "round"); //"" or "round"
define("buttonTableStyle", buttonStyle); //"" or "round"

define("csrfControl", true); // 
define("csrfExpirationDelay", 240000); // 40 minute, 2 400 seconds
define("loginRequired", true); // 40 minute, 2 400 seconds
define("loginExpirationDelay", 2400); // 40 minute, 2 400 seconds

define("honeyPotControl", true); // 

//recaptcha
define("recaptchaControl", false);
if(recaptchaControl){
    define("recaptchaSecretKey", "6Ldkd7MUAAAAAL9KFWoFF8BVc9RiWCzr32LUP0N8");
    define("recaptchaPublicKey", "6LcI1K0UAAAAADpKRE79PnG9KxMlC3nlg4ODboEp");
    define("recaptchaFieldIdName", "g-recaptcha-response");    
    define("recaptchaResponseScoreMin", 0.6);    
}

// For models
define("mdlPkg",    "models/");
define("mdlPrefix", "M_");
define("defaultModel", "Model");
define("defaultSuperModel", "tools/Model"); //for heritage in makeModel()

// For views
define("viewPkg",     "views/");
define("viewPrefix",  "v_");
define("defaultView", "Accueil");

// For controllers
define("ctrlPkg",    "controllers/");
define("ctrlPrefix", "C_");
define("defaultController", "Accueil");
define("defaultSuperController", "tools/Controller"); //for heritage in makeController()

// For files folder
define("uploadedFiles", "uploadedFiles/"); // uploadedFiles/


// For json
define("guillemet", "g_l_t"); // remplace temporairement les guillemets util pour un json dans un champs input

// For editor (ckeditor) version
define("editor4V", "4.14.0");
define("editor5V", "19.1.0");
define("html2pdfLink", "tools/html2pdf/html2pdf.class.php");

// Définition de la constante de l'url du site dela page index (l'url index)
// ce qui permettra de la recupérer n'importe où et n'importe quand
define('URL', str_replace("index.php", "", (isset($_SERVER['HTTPS']) ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]"));
define("urlSup", "urlsup"); //url supplémentaire
define("registrationFree", true);

define("menuBorderLeft", 1); // 0 ...
define("subMenuPaddingLeft", 20); // 0 ...
define("navbarSideTable", "menu"); // "", "menu" ...
define("navbarSideUser", false);


define("passwordRegex", regexPwd());
define("passwordConfirRoutes", ["userCreate", "userUpdate", "userRegistration"]);

function passwordCryptor($password){ //TODO revenir sur le nom
    /*N.B. Il faut impérativement changer le formule de criptage de cette fonction
    en mettant une qui vous est propre. Faites des associations, des mélanges d'algorithmes de hashages...
    Ex : 
    hash("sha256",
        hash("sha256", $password).sha1($password)
    );  ... 
    N'oubliez pas de bien tester la fonction (Ex. en l'appelant directement sur l'url)*/
    return hash("sha256", $password);
}
/**deCryptor($string) est sont complémentaire //TODO traduire en anglais */
function cryptor($string){ //TODO revenir sur le nom
    /*N.B. Il faut impérativement changer le formule de criptage de cette fonction
    en mettant une qui vous est propre.
    Il faut aussi addapter le complémentaire deCryptor suivant celle-ci (cryptor).
    N'oubliez pas de bien les tester (Ex. en les appelant directement sur l'url)*/
    
    $n = strlen($string);
    $crypted = "";
    for ($i=0; $i < $n; $i++) { 
        $crypted .= (ord($string[$i]) * ($i+1)) + $i ."-";
    }
    return $crypted;
}
function deCryptor($string){
    $string = toArray(trimPlus($string, "-"), "-");
    $deCrypted = "";
    $i = 0;
    foreach ($string as $value) {
        $deCrypted .= chr((intval($value) - $i) / ($i+1));
        $i++;
    }
    return $deCrypted;
}

define('scrollRevealDefaultImport', true);

function initAddedScrollEffects (){

    addScrollEffect('.scrEffDefault', '{"duration":2500,"distance":"150%","origin":"bottom", "delay":375,"reset":false}');
    addScrollEffect('.dia');
    
}
function adapteFieldsInfo($fieldsInfo){
    // $fieldsInfo["label"] = "pape";
   // seec($fieldsInfo["name"], get($_GET, "urlsup"));
   // seec($fieldsInfo);
    return $fieldsInfo;
}
function adapteTablesInfo($tablesInfo){
    return $tablesInfo;
}
?>