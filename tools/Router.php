<?php
require_once("tools/view&ctrlTools.php");
require_once('tools/routes.php');
require_once('./routes.php');
  
class Router{
  private $_ctrl; //TODO is util ?

  // url, controller, routeName
  public static $views = array();
  // public static $posts = array();
  // public static $gets = array();
  public static $names = array();

  // public function hydrate(array $data, $object){
  //   foreach ($data as $method => $param) {
  //     if(method_exists($object, method))
  //       ...
  //   }
  // }
  public function routeReq(){ // la requette du routeur
    try {
      if( ! isInited() )
        init();
      $duration = time() - lastTime();

      $userLogInRoute = route("userLogIn");
      if(loginRequired && route() != $userLogInRoute ){
        if(empty2($userLogInRoute) )
          throw new Exception('{"fr":"Impossible d’acceder la route nommée userLogIn, pour regler cette erreur veuillez soit vous assurer d’avoir mise en place le system d’authentification des utilisateurs (vous pouvez le generer), soit désactiver  loginRequired dans le fichier de configuration.","en":"Cannot access the route named userLogIn, to resolve this error please either make sure you have set up the user authentication system (you can generate it), or deactivate loginRequired in the configuration file.
          "}');

        if(empty2(userId()) && ! in("/logInBackend", route())){
          back($userLogInRoute);
        }else {
          checkAndManageLoginDelay($duration); //there is a redirection 
        }
        if(($oldInfo = session("expirationOldInfo")) &&
        $oldInfo["route"] == route()){
          $_GET = $oldInfo["get"];
          $_POST = $oldInfo["post"];
          $_FILES = $oldInfo["file"];
          unsetSession("expirationOldInfo");
        }
      }
      //if(! empty($_GET)) //pamtable
      old();
      if( ! empty($_POST) ){
        //formOld();
        if(csrfControl)
          checkCsrf($duration);
        if(honeyPotControl )
          checkHoneyPot();
        
      }
      else if(toUnsetFormSessionInfos()){
        unsetFormLabel();
        unsetFormRules();
        unsetGuarded();
        toUnsetFormSessionInfos(false);
        uniqValReInit();
      }
      lastTime(time());
      $url0 = "";
      if(isset($_GET['url'])){
        //$url0 = filter_var($_GET['url'], FILTER_SANITIZE_URL);//TODO
        $url0Fct = urlSanitizer($_GET['url']);
        $url0 = $url0Fct; //ucfirst($url0Fct);
        //Le controlleur (ou le fichier de l'action) est inclus selon l'action de l'utilisateur
      }
      
      $lastGetTmp = session("lastGet");
      backLink(toHttp());
      if($url0 != ""){
        //backLink(home().$url0);

        $url = explode('/', $url0);
        
        $n = count($url);
        if(isset($url[$n - 1]) and $url[$n - 1] == ""){
          unset($url[$n - 1]);
          $n --;
        }
        
        $urlLower = ucfirst(toLower($url[0]));
        
        if( notEmpty($_POST) and ($newUrlTmp = getConstant("p".$urlLower)) ){
          $newUrl = $newUrlTmp;
        }
        else if(isset(self::$views[$urlLower])){
          if(devMode and ($newUrlTmp = getConstant("g".$urlLower)))
            notice("Notice (visible seulement en devMode) : Un même url (ou route) existe en mode get, mais c'est celui en mode view qui est choisi ici (voir routes.php).");
          require_once("tools/Controller.php");
          $this->_ctrl = new Controller("", "", self::$views[$urlLower]);
          return;
        }
        else if(($newUrlTmp = getConstant("g".$urlLower))){
          $newUrl = $newUrlTmp;
        }
        else if(($newUrlTmp = getConstant("p".$urlLower))){
          $newUrl = $newUrlTmp;
        }else
          $newUrl = $url[0];
        $newUrl = str_replace("@", "/", $newUrl);

        $urlTmp = [];
        if($n > 1){
          unset($url[0]);
          $urlTmp = $url;
        }
        $newUrl = array_merge(explode("/", $newUrl), $urlTmp);

        if(isset($newUrl[1])){ // count($newUrl) > 1
          // $url1 = [];
          // foreach ($newUrl as $value) {
          //   $url1[] = $value;
          // }
          $url = $newUrl; //array_merge($url1, $url);
        }
        $newUrl = $newUrl[0];
        
        $fileName = first($newUrl, ".php");
        $controller = ucfirst($fileName);
        if($controller == "Controller"){
          $controllerClass = $controller;
          $controllerFile = "tools/".$controllerClass.".php";
        }else{
          $controllerClass = ctrlPrefix.$controller;
          $controllerFile = ctrlPkg.$controllerClass.".php";
        }
        $ok = true;
        if( file_exists($controllerFile) ){
          
          require_once($controllerFile);
          
          if(class_exists($controllerClass)){

            $this->_ctrl = new $controllerClass($url);
            //if(method_exists($this->_ctrl, $methodName))
            
          }else{
            throw new Exception("File ($controllerFile) exists but the controller class is not correct ! May be see also in routes.php ");
          }

        }else if( file_exists($fileName.".php") ){

          require_once($fileName.".php");

        }else {
          if(devMode){
            // if(in("help", $url0)){
            //   help(str_replace(["(", ")",'"', "'"], "", toArray(toArray($url0, "help")[1],"/")[0]));
            // }
            $url0Fct = str_replace(",-", ",", $url0Fct);
            $ok = callProcedureFromString($url0Fct);
            if($ok)
              session("lastGet", $lastGetTmp);//TODO mettre dans une fonction skipBackLink ... voir aussi pour les envoie retour de formulaire avec erreurs (create, update)
          }
          else $ok = false;
        }
        if( ! $ok){
          throw new Exception("File or controller ($controllerFile) ".tern( ! devMode, "or method/function (".first($url0, "(")."())")." is introuvable. May be see also routes.php ");
        }
      }else { 
        //backLink(home());
        
        $fileName = defaultController;
        $controller = ucfirst($fileName);
        $controllerClass = ctrlPrefix.$controller;
        $controllerFile = ctrlPkg.$controllerClass.".php";

        if( file_exists($controllerFile) ){
          require_once($controllerFile);
          if(class_exists($controllerClass)){
            $this->_ctrl = new $controllerClass();
          }
        }else if( file_exists($fileName.".php") ){
          require_once($fileName.".php");
        }
        else{
          require_once("tools/Controller.php");
          $this->_ctrl = new Controller("");
        }
      }
      unsetFormValidationDatas();

    } catch (Exception $e) {
      $errorMsg =  $e->getMessage();

      require_once("tools/Controller.php");
      $this->_ctrl = new Controller("", $errorMsg);

    }
  }
}

// require_once('routes.php');
addRouteName("URL", URL);
addRouteName("url", URL);


function routePost($url, $ctrlOrView, $name="", $view = false){
  $url = ucfirst(toLower(urlSanitizer($url)));
 // Router::$posts[$url] = $ctrl;
 //TODO gérer les exception (il se peut que cette route soit déjà utilisée; voir routes.php)
  if($view)
    routeView($url, $ctrlOrView, $name);
  else{
    if($name != "")
      Router::$names[$name] = $url;
  
    define("p".$url, $ctrlOrView);
  }
}
function routeGet($url, $ctrlOrView, $name="", $view = false){
  $url = urlSanitizer($url);
  // Router::$gets[$url] = $ctrl;
   //TODO gérer les exception (il se peut cette route soit déjà utilisée; voir routes.php)
  if($view)
    routeView($url, $ctrlOrView, $name);
  else{
    if($name != "")
      Router::$names[$name] = $url;
    define("g".$url, $ctrlOrView);
  }
}
function routeView($url, $view, $name=""){
  $url = urlSanitizer($url);
  //TODO gérer les exception (il se peut cette route soit déjà utilisée; voir routes.php)
  Router::$views[$url] = $view;
  if($name != "")
    Router::$names[$name] = $url;
  define($url, $view);
}
function route($name = ""){
  if($name == "" || $name == "#")
    return racine().tern(get($_GET, "url"), old("url")); // return racine().old("url");
  if(isset( Router::$names[$name]))
    return racine().Router::$names[$name];
  return "";    
}
function controllerName($routeNameOrUrl = ""){
  if($routeNameOrUrl == "" || $routeNameOrUrl == "#")
    $url = old("url");
  elseif(isset( Router::$names[$routeNameOrUrl]))
    $url = Router::$names[$routeNameOrUrl];
  else
    $url = $routeNameOrUrl;

  $url = ucfirst(toLower($url));
  
  if( ($urlTmp = getConstant("p".$url)) )
    return first($urlTmp, "/");
  else if( ($urlTmp = getConstant("g".$url)) )
    return first(constant("g".$urlTmp), "/"); 
}
function routeName($url = ""){
  $url =  str_replace(racine(), "", tern($url, route()));
  return getKeyFromValue(Router::$names, $url);
}
function home(){
  return Router::$names["URL"];     
}

function addRouteName($name, $url){
  Router::$names[$name] = $url;
}
function racine(){
    return str_replace("index.php", "", $_SERVER["PHP_SELF"]);
}
function goodRoute($route){
  return ! in(["favicon.", ".js", ".css", ".scss", ".ico", ".jpg", ".jpeg", ".png", ".gif"], $route, "", false, false); //TODO revoir peut être
}
?>