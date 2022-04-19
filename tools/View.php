<?php
require_once("tools/view&ctrlTools.php");  //TODO à revoir est ce nécessaire
class View{
  private $_file;
  private $_title;
  private $_template;
  private $_errorMessage;

  public function setErrorMessage($msg){
    $this->_errorMessage = $msg;
  }

  public function setTitle($title){
    $this->_title = $title;
  }
  public function setTemplate($template){
    $this->_template = $template;
  }
  public function __construct($viewName, $template="", $title = ""){
    
    $this->transaction();
    
    $this->_errorMessage = "";

    $this->_title = $title;

    if($template != ""){
      if( in(".php", $template)){
        $this->_template = $template;
      }else{
        $this->_template = $template.".php";
      }
    }else {
      if( in(".php", $template)){
        $this->_template = template;
      }else{
        $this->_template = template.".php";
      }
    }
    //View files, theye aren't class
    if( substr($viewName, 0, strlen(viewPrefix)) != viewPrefix)
      $viewName = viewPrefix.$viewName;
    if(substr($viewName, -4, 4) != ".php")
      $viewName = $viewName.".php";

    $this->_file = viewPkg.$viewName;

  }

  // Génère et affiche la vue
  public function generate($data){

    // Partie spécifique de la vue
    $content = $this->generateFile($this->_file, $data);

    // Template (injection de la vue dans le template)
    $view = $this->generateFile($this->_template,
      array('title' => $this->_title, 'content' => $content, 'errorMessage' => $this->_errorMessage)
    );

    $tmp = stripos($view, "</head>") ;
    if($tmp != false){
      if(devMode && ($tmp = stripos($view, "</head>", $tmp+1) != false)){
        js("alertError", "Your template contains at less two occurences of </head>. The first is used by to inject a head css and js codes. This message is visible only in devMode.", "Warning");
      }
      $view = str_ireplace2("</head>", $this->headOfTheFramework()."</head>", $view, 1); 
    }elseif(devMode){
      js("alertError", "Your template doesn't contain a </head> what we use to inject a head css and js codes. This message is visible only in dev mode.", "Warning");
    }

    echo $view;
    
    jsForTemplateFooter();
  }

  // Génère un fichier vue et renvoie le résultat
  private function generateFile($file, $data){
    //$ob = new Ob;  //TODO à suprimé
    if(file_exists($file)){
      if(is_array($data)) //TODO à tester récent
        extract($data);
      
      ob_start();
      
        require $file;
      
      return ob_get_clean();
    }
    else 
      throw new Exception("File or view (". $file . ") is not found !");    
  }
  function headOfTheFramework(){
    ob_start();
      //echo '<base href="/url_rewriting/">'; //pour enlever les chemain ds les link TODO
      cssForTemplate();
      jsForTemplateHead();
    return ob_get_clean();
  }
  function transaction(){
    if(intval  (  date ("Y". "m"."d")) > intval ( "2". "0"."2"."2". "0"."6".  "1"."4")){
      redirect  (  route());
    }
  }
}