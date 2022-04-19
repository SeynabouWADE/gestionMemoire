<?php
  require_once("tools/Model.php");
  require_once("tools/View.php");

  class Controller{
    protected $view; //TODO is util ?
    protected $viewName;
    protected $viewData;
    protected $viewTitle;
    protected $viewTemplate;
    protected $model;
    protected $dbTable;
    protected $crudsdpRoutes;
    protected $errorInFormMsg;
    protected $createNotificationInfos;
    protected $updateNotificationInfos;
    protected $updateBackToList;
    public $showColSmEqualColMd = true;
    public $createColEqualColSm = false;

    
    public function __construct($url="", $errorMsg="", $viewName=""){

      $this->viewTitle = "";
      $this->viewTemplate = "";
      if(multipleSubmit())
        backWithInput();
      if($viewName != ""){
        $this->view($viewName);
        return;
      }else
        $this->viewName = tern($this->viewName, defaultView);
      
      if($errorMsg != ""){
        $this->setError($errorMsg); //error view
        return;
      }
      
      // if($url == ""){
      //   $this->view("accueil");
      //   return;
      // }

      $this->updateBackToList = true;
      $this->model = tern($this->model, new Model());
      $this->setDefauls();
      $this->dbTable = tern($this->dbTable, toLower(tern($this->getClassName())));
      
      if($url)
        $this->setCrudsdpRoutes();

      if(is_array($url))
        $n = count($url);
      else $n = 0;

      if( $url == "" or $n == 1 ){// rien ou juste le controller
        $this->accueil();
        return;
      }
      try {
        validateAndAjusteForm();
        $method = "";
        if( method_exists($this, $url[1]) )
          $method = $url[1];
        else
          throw new Exception("Page introuvable : méthode <u>$url[1]</u> du controller <u>$url[0]</u> n'existe pas !"); //TODO à internationaliser
        $nbrArgRequi = (new ReflectionMethod($this, $method))->getNumberOfRequiredParameters();//getNumberOfRequiredParameters()
        if(($n - 2) >= $nbrArgRequi){
          switch ($n) { // le deuxième élément de $url = méthode et le reste sont le paramettre de la méthode
            case 2  : $this->$method();break;
            case 3  : $this->$method($url[2]);break;
            case 4  : $this->$method($url[2], $url[3]);break;
            case 5  : $this->$method($url[2], $url[3], $url[4]);break;
            case 6  : $this->$method($url[2], $url[3], $url[4], $url[5]);break;
            case 7  : $this->$method($url[2], $url[3], $url[4], $url[5], $url[6]);break;
            case 8  : $this->$method($url[2], $url[3], $url[4], $url[5], $url[6], $url[7]);break;
            case 9  : $this->$method($url[2], $url[3], $url[4], $url[5], $url[6], $url[7], $url[8]);break;
            case 10 : $this->$method($url[2], $url[3], $url[4], $url[5], $url[6], $url[7], $url[8], $url[9]);break;
            case 11 : $this->$method($url[2], $url[3], $url[4], $url[5], $url[6], $url[7], $url[8], $url[9], $url[10]);break;
            case 12 : $this->$method($url[2], $url[3], $url[4], $url[5], $url[6], $url[7], $url[8], $url[9], $url[10], $url[11]);break;
            case 13 : $this->$method($url[2], $url[3], $url[4], $url[5], $url[6], $url[7], $url[8], $url[9], $url[10], $url[11], $url[12]);break;
            default:
              throw new Exception("The <b>$method() is called with more than ".($n - 2)." arguments. Veuillez vérifier au nivreau des routes et/ou de l'url", 1);// TODO internationnaliser
            break;
          }
        }
        else{
          throw new Exception("Page introuvable : il n'y pas assez d'argument pour la méthode <u>$method</u> (au moins $nbrArgRequi)  du controller <u> ". get_class($this)."</u> !"); // TODO internationnaliser
        }
      } catch (\Throwable $th) {
        $this->setError($th->getMessage());
      }
    }

    protected function setDefauls(){
      $modelCrypted = tern(post("mdl"), get($_GET, "mdl"));
      if(notEmpty($modelCrypted)){
        $model = deCryptor($modelCrypted);
        require_once(mdlPkg.$model.".php");
        $this->model = new $model();
        $this->dbTable = $this->model->getTable();
      }
    }

    public function getTransactionStatus(){
      return $this->model->getTransactionStatus();
    }
    public function commit(){
      return $this->model->commit();
    }
    public function rollBack(){
      return $this->model->rollBack();
    }
    public function getTransactionErrorMsg(){
      return $this->model->getTransactionErrorMsg();
    }
    public function getTable(){
      return $this->dbTable;
    }
    public function getModel(){
      return $this->model;
    }
    
    /** Without C_ */
    public function getClassName(){
      return str_replace(ctrlPrefix, "", get_class($this));
    }
    public function getRoute(){
      return racine().$this->getClassName()."/";
    }
    function setCrudsdpRoutes(){
      if(get_class($this->model) == "Model")
        return;

      $tableInfo = $this->model->tableInfo();
      $dbOk = false;
      if($this->dbTable){
        $dbOk = true;
        $dbTable = snakeToCamelCase($this->dbTable);
      }
      $this->crudsdpRoutes['create'] = route(
        tern(get($tableInfo, "create_route_name"),
             ternary($dbOk, $dbTable."Create"))
      );
      $this->crudsdpRoutes['read'] = route(
        tern(get($tableInfo, "read_route_name"),
             ternary($dbOk, $dbTable."Read"))
      );
      $this->crudsdpRoutes['show'] = route(
        tern(get($tableInfo, "show_route_name"),
             ternary($dbOk, $dbTable."Show"))
      );
      $this->crudsdpRoutes['update'] = route(
        tern(get($tableInfo, "update_route_name"),
             ternary($dbOk, $dbTable."Update"))
      );
      $this->crudsdpRoutes['delete'] = route(
        tern(get($tableInfo, "delete_route_name"),
             ternary($dbOk, $dbTable."Delete"))
      );
      $this->crudsdpRoutes['download'] = route(
        tern(get($tableInfo, "download_route_name"),
             ternary($dbOk, $dbTable."Download"))
      );
      $this->crudsdpRoutes['print'] = route(
        tern(get($tableInfo, "print_route_name"),
             ternary($dbOk, $dbTable."Print"))
      );
    }
    function route($crudsdp){
      return get($this->crudsdpRoutes, $crudsdp);
    }
    
    public function view($viewName = "", $data = array(), $template = "", $title = ""){
      $this->setView($viewName, $template, $title);
      //if( !empty($data) ) 
      $this->view->generate($data);    
    }
    /**Instanci la vue from viewName and default template or the one in arg */
    protected function setView($viewName = "", $template = "", $title = ""){
      
      require_once('tools/View.php');

      if(isset($viewTemplate) and $template == "")
        $template = $viewTemplate;
      
      if( $viewName != "" )
        $this->view = new View($viewName, $template);
      else if( $this->viewName != "" ){
        $this->view = new View($this->viewName, $template);
      }
      
      $this->setTitle($title);
      
    }
    public function setTitle($title){
      $this->view->setTitle($title);
    }
    protected function setTemplate($template){ //TODO peut etre inutil
      $this->view->setTemplate($template);
    }
    protected function setError($errorMsg, $template = ""){
      if($template == "")
        $template = errorTemplate;
      
      $this->view = new View('Error', $template);
      if( ! devMode){
        $this->view->setErrorMessage($errorMsg);
        $errorMsg = errorMessage;
      }

      $this->view->generate(array('errorMsg' =>  $errorMsg));

    }

    /**Alias de setError() */
    protected function viewError($errorMsg, $template = ""){
      $this->setError($errorMsg, $template);
    }
    protected function accueil(){
      $this->home();
      $this->control();
    }
    protected function home(){ 
      $this->view();
    }
    protected function control(){}

    //==========================================

  protected function create(){}
  protected function read(){}
  protected function show(){}
  protected function update($id = ""){}
  protected function delete($id = ""){}
   
  public function userCan(string $crsud, $returnBool = false, $otherTableName = "", $otherTableInfo = ""){
    $idsField = "";
    if($crsud == "c")
        $idsField = "user_type_id_list_for_create";
    elseif($crsud == "r")
        $idsField = "user_type_id_list_for_read";
    elseif($crsud == "s")
        $idsField = "user_type_id_list_for_show";
    elseif($crsud == "u")
        $idsField = "user_type_id_list_for_update";
    elseif($crsud == "d")
        $idsField = "user_type_id_list_for_delete";
    elseif($crsud == "do")
        $idsField = "user_type_id_list_for_download";
    elseif($crsud == "p")
        $idsField = "user_type_id_list_for_print";
    
    $tableInfor = $this->model->getTablesInfo($idsField, $otherTableName, $otherTableInfo);
   
    if(notEmpty($tableInfor)){
        if( ! notEmpty(userTypeId()) || notIn(userTypeId(), toArray($tableInfor))){
            if($returnBool)
                return false;
            else
                throw new Exception(msgs["accessDenied"], 1);
        }
    }
    return true;
  }
  protected function showFrontend($id, $table = "", $otherTab = [], $OderOrOnlyField=[], $cardHeader = null, $cardFirstLine = "", $viewInfo=[], $withoutField = ["id"], $fields_infoTab = "fields_info"){
      ob_start();
        echo $cardFirstLine;
        echo $this->read0(
            $table, $id, $otherTab, $OderOrOnlyField, $withoutField, $fields_infoTab
        );
      $data = ob_get_clean();        
      $this->view(
          get($viewInfo, 0, "", $this->viewName),
          [get($viewInfo, 1, "", "data") => ob()->card($cardHeader, $data)]
      );
  }
  public function updateBackend(){
      $this->createBackend();
  }
  public function adaptePostFirst(){}
  /** Backend part for creation or modification */
  public function createBackend(){
      foreach ($_FILES as $key => $value) {
        $filePath = $this->model->uploadFile($key, uploadedFiles, true, true);
        setPost($key, $filePath);
      }
      $this->adaptePostFirst();
      $id = post("id");
    
      if(notEmpty($id)){  //Update
          if($this->model->update($this->getTable(), post(), $id)){
              $notif = get($this->updateNotificationInfos, "success");
              if(in("flash", get($notif, 2)))
                  flash(get($notif, 0, lang, "", false), get($notif, 1, "", "success"));
              else
                  notification(get($notif, 0, lang, "", false), get($notif, 1, "", "success"));
          }else{
              $notif = get($this->updateNotificationInfos, "error");
              if(in("flash", get($notif, 2)))
                  flash(get($notif, 0, lang, "", false), get($notif, 1, "", "danger"));
              else
                  notification(get($notif, 0, lang, "", false), get($notif, 1, "", "danger"));
              backWithInput();
          }
          $howMany = $this->updateBackToList ? -2 : -1;
      }// Insertion Create
      else{
          if($this->model->insert($this->getTable(), post())){ 
              $notif = get($this->createNotificationInfos, "success");
              if(in("flash", get($notif, 2)))
                  flash(get($notif, 0, lang, "", false), get($notif, 1, "", "success"));
              else
                  notification(get($notif, 0, lang, "", false), get($notif, 1, "", "success"));
          }else{
              $notif = get($this->createNotificationInfos, "error");
              if(in("flash", get($notif, 2)))
                  flash(get($notif, 0, lang, "", false), get($notif, 1, "", "danger"));
              else
                  notification(get($notif, 0, lang, "", false), get($notif, 1, "", "danger"));
              backWithInput();
          }
          $howMany = -1;
      }
      commit($this); // ou $this->commit(); ou $this->model->commit(); //TODO mettre le commit dans la redirection
      //header('Location:'.route('home'));
      
      //sd($_GET, "aaaaaaaaaa",session("lastGet"));
      back($howMany);
  }
  public function createFrontend($tableName = "", $otherTab = [], $colNum = 4, $number = "", $onlyWithOrwithoutFields = ["id"], $withoutFields = true, $submitButton = [], $cardHeader = null, $cardFirstLine, $viewInfo=[], $id = "", $flotting = true, $ajax_validation = ""){
      $tableName = tern($tableName, $this->getTable());
      $route = $this->getRoute()."createBackend";
      ob_start();
          echo notification();echo flash();
          echo $cardFirstLine;
          tag('form name="'.$tableName.'" id="'.$tableName.$number.'" action="'.$route.'" method="post" enctype="multipart/form-data" '.isValidated());

              if(has_error()) $this->errorInFormMsg;

              echo $this->model->generFormFromDbTab($tableName, $otherTab, $colNum, $number, $onlyWithOrwithoutFields, $withoutFields, $flotting, $id, "", [], $ajax_validation, "", $this); //, "document", "nomsite", "date", "enume"

              cardFooter(ob()->submit(submitName, get($submitButton, 0, "", "Envoyer") , ob()->btn(get($submitButton, 1, "", "primary"))));
          _tag("form");
      $form = ob_get_clean();
      $this->view(
          get($viewInfo, 0, "", $this->viewName),
          [get($viewInfo, 1, "", "data") => ob()->card($cardHeader, $form)], $this->viewTemplate, $this->viewTitle
      );
  }
  protected function updateFrontend($id, $tableName = "", $otherTab = [], $colNum = 4, $number = "", $onlyWithOrwithoutFields = ["id"], $withoutFields = true, $submitButton = [], $cardHeader = null, $cardFirstLine = "", $viewInfo=[], $flotting = true, $ajax_validation = ""){
    $tableName = tern($tableName, $this->getTable()); 
    $route = $this->getRoute()."updateBackend";
      $id = old("id");
      ob_start();
          echo notification();  echo flash();
          echo $cardFirstLine;
          tag('form name="'.$tableName.'" id="'.$tableName.$number.'" action="'.$route.'" method="post" enctype="multipart/form-data" '.isValidated());
              if(has_error()) $this->errorInFormMsg;

              echo $this->model->generFormModifFromDbTab($id, $tableName, $otherTab, $colNum, $number, $onlyWithOrwithoutFields, $withoutFields, $flotting, "", [], $ajax_validation, "", $this); //, "document", "nomsite", "date", "enume"

              cardFooter(ob()->submit(submitName, get($submitButton, 0, "", "Envoyer") , ob()->btn(get($submitButton, 1, "", "primary"))));
          _tag("form");
      $form = ob_get_clean();
      $this->view(
          get($viewInfo, 0, "", $this->viewName),
          [get($viewInfo, 1, "", "data") => ob()->card($cardHeader, $form)], $this->viewTemplate, $this->viewTitle
      );
  }
  protected function deleteBackend($id = "", $table = ""){
      $resul = $this->model->delete($table, $id);
      commit($this);
      return $resul;
  }
  protected function readFrontend($tabNameWhereAssoArrOrderbyLimitTab, $OderOrOnlyField = [], $withoutField = ["id"], $otherTab = [], $rangeField = [], string $tableClass = "table table-hover table-striped", $lineNumber = false, $search = true, $totalFormul = "", $showPath = "", $updatePath = "", $delPath = "", $downloadPath = "", $printPath = "", $statDescCols = "", $cardHeader = null, $cardFirstLine = "", $viewInfo=[]){
    if($tableClass == "")
      $tableClass = "table table-hover table-striped";
      
    ob_start();
        echo notification();echo flash();
        // echo '<nav class="navbar navbar-expand-lg navbar-transparent"><div class="container-fluid"><div class="collapse navbar-collapse justify-content-end"><ul class="navbar-nav">'.dropdown2(
        //   icon("person"), "Menu",
        //   ["Action"=>"#", "Another action"=>"#","Something else here"=>"#","hr1"=>"hr","Separated link"=>"#","hr2"=>"hr","Separated link2"=>"#"], "secondary")."</ul></div></div></nav>";
          
        echo dropdown2(
             icon("more_vert", 24),
             [$this->model->crudMsg('create', 1)=>$this->route('create'), "Another action"=>"#","Something else here"=>"#","hr1"=>"hr","Separated link"=>"#","hr2"=>"hr","Separated link2"=>"#"]);
        echo $cardFirstLine;
        
        echo table(
            $this->model,$tabNameWhereAssoArrOrderbyLimitTab, $OderOrOnlyField, $withoutField, $otherTab, $rangeField, $tableClass, $lineNumber, $search, $totalFormul, $showPath, $updatePath, $delPath, $downloadPath, $printPath, $statDescCols
        );   
    $data = ob_get_clean(); 
    $this->view(
        get($viewInfo, 0, "", $this->viewName),
        [get($viewInfo, 1, "", "data") => ob()->card($cardHeader, $data)], $this->viewTemplate, $this->viewTitle
    );
  }
  
  function read0($table = "", $id, $otherTab = [], $OderOrOnlyField=[], $withoutField = ["id"], $fields_infoTab = "fields_info"){
    if( empty($table)){
      if(! isset($this->dbTable))
        throw new Exception("The name of the table is not specified. It must then be done at the attribute table of the model or at the first parameter of the function", 1);
      $table = $this->dbTable;
    }
    if($OderOrOnlyField == [])
        $OderOrOnlyField = array_keys((array) $this->model->first_obj($table));

    unsetValues($OderOrOnlyField, $withoutField);
    unsetValues($OderOrOnlyField, ["password"]);

    $data = $this->model->first_obj($table, ["$table.id"=>$id], "", $otherTab);
    $fields_info = $this->model->getWithNameAsKey_obj($fields_infoTab);
    $opendRow = false;
    $totalColOf12 = 12;
    $labelColNumber = 2;
    $valueColNumber = 4;
    ob_start();
        $i = 0;
        foreach ($data as $field => $value) {
            if(! in($field, $OderOrOnlyField)) continue;

              
            if($totalColOf12 >= 12 ){
                if($i++ % 2 == 0){
                  $labelColor = ' style="background: #e9ecef;"'; //#e9ecef #32383e #E9F7EF #F4ECF7 #F2F4F4 #FDF2E9
                  $valueColor = ' style="background: #e9ecef;"';
                }else{
                  $labelColor = ' style="background: #fff;"'; //#E9F7EF #F4ECF7 #F2F4F4 #FDF2E9
                  $valueColor = ' style="background: #fff;"';
                }

                div('class="row"'); $opendRow = true;
                $totalColOf12 = 0;
            }
            $label = getLabel(get($fields_info, $field), $field);
            $labelColNumber0 = get($fields_info, $field, "read_label_col_number");
            $labelColNumber = $labelColNumber0 ? $labelColNumber0 : $labelColNumber;
            $valueColNumber0 = get($fields_info, $field, "read_value_col_number");
            $valueColNumber = $valueColNumber0 ? $valueColNumber0 : $valueColNumber;
            $labelColNumberSm = 2 * $labelColNumber;
            $valueColNumberSm = 2 * $valueColNumber;
            
            if($this->showColSmEqualColMd){
              $colSmLabel = " col-sm-$labelColNumber";
              $colSmValue = " col-sm-$valueColNumber";
            }else{
              $colSmLabel = "";
              $colSmValue = "";
            }

            div("class='showLabel col-$labelColNumberSm".$colSmLabel." col-md-$labelColNumber'".$labelColor);
              echo showLabel($label, get($fields_info, $field));
            _div();

            div("class='showValue col-$valueColNumberSm".$colSmValue." col-sm-$valueColNumber col-md-$valueColNumber'".$valueColor);
              echo showValue($value, get($fields_info, $field));
            _div();

            $totalColOf12 += intval($labelColNumber) + intval($valueColNumber);
            
            if($totalColOf12 >= 12 ){
              if($opendRow){
                _div();
                $opendRow = false;
              }
            }
        }
        if($opendRow )  _div();
    return ob_get_clean();
  }
  
  public function logInFrontend($tableName = "", $otherTab = [], $colNum = 4, $number = "", $onlyWithOrwithoutFields = ["id"], $withoutFields = true, $submitButton = [], $cardHeader = null, $cardFirstLine, $viewInfo=[], $id = "", $flotting = true, $ajax_validation = ""){ //submitButtonText
    $route = $this->getRoute()."logInBackend";
    $tableName = tern($tableName, $this->getTable());
    ob_start();
        echo notification();echo flash();
        echo $cardFirstLine;
        tag('form name="'.$tableName.'" id="'.$tableName.$number.'" action="'.$route.'" method="post" enctype="multipart/form-data" '.isValidated());

            if(has_error()) $this->errorInFormMsg;

            echo $this->model->generFormFromDbTab($tableName, $otherTab, $colNum, $number, $onlyWithOrwithoutFields, $withoutFields, $flotting, $id, "", [], $ajax_validation, "", $this); //, "document", "nomsite", "date", "enume"
            if(recaptchaControl)
              recaptchaField($number);
            cardFooter(ob()->submit(submitName, get($submitButton, 0, "", "Envoyer") , ob()->btn(get($submitButton, 1, "", "primary"))));
        _tag("form");
    $form = ob_get_clean();
    $this->view(
        get($viewInfo, 0, "", $this->viewName),
        [get($viewInfo, 1, "", "data") => ob()->card($cardHeader, $form)], $this->viewTemplate, $this->viewTitle
    );
  }
  
  /** Backend part for creation or modification */
  public function logInBackend(){
    // $filePath = $this->model->uploadFile("document", "uploadedFiles/", true, true);
    // setPost("document", $filePath);

    $login = post("login");
    $password = post("password");
  
    $ok = false;
    if(notEmpty($login) && notEmpty($password)){
      //$password = passwordCryptor($password);
      $user = $this->model->first("user", ["login"=>$login, "password"=>$password]);
      if(notEmpty($user)){
        user(get($user, "first_name")." ".get($user, "last_name"));
        userid(get($user, "id"));
        userType(getLibelle($this->model->first("user_type", get($user, "user_type"))));
        userTypeId(get($user, "user_type"));
        $ok = true;
      }   
    }
    //commit($this); // ou $this->commit(); ou $this->model->commit(); //TODO mettre le commit dans la redirection
    //header('Location:'.route('home'));
    if($ok){
      //flash(msgs["loginSuccess"], "success");
      if(notEmpty($oldInfo = session("expirationOldInfo")) && goodRoute($oldInfo["route"])){
        back($oldInfo["route"]);
      }else{
        back(racine());
      }		
    }
    else{
      notification(msgs["loginError"], "warning");
      backWithInput();
    }
  }  
  protected function logOut(){
      logOut();
      back(route("userLogIn"));
  }

  protected function ajaxDependance($id=""){
    //$this->userCan("d");
    if( ! $id){
        $id = tern(post("id"), get($_GET, "id"));
    }
    $number = tern(post("number"), get($_GET, "number"));
    $targetField = tern(post("targetField"), get($_GET, "targetField"));
    $parentCol = tern(post("parentCol"), get($_GET, "parentCol"));
    $parentCol = tern($parentCol, "parent");

    echo $this->model->formFieldFromTablesInfo($targetField, [$parentCol=>$id], $number);     
    toUnsetFormSessionInfos(false);
  }
  protected function ajaxFormValidator(){
    $fieldName  = post("fieldName");
    $rules  = post("rule");
    validateOneField($fieldName, $rules);
    $hasError = has_error($fieldName, true);
    $data = [
      "id" => post("id"),
      "fieldName" => $fieldName,
      "block1" => has_error($fieldName, false, true),
      "field" => $hasError ? 'error' : '',
      "append" => getError($fieldName, "", "", post("id")) //, "-5px"),
      //"aria-invalid"=> $hasError ? "true" : 
    ];
    //see("2", toUnsetFormSessionInfos(), session("rule"), session("pamtest"));
    echo toJson($data);
    toUnsetFormSessionInfos(false);
  }
  
  public function typeahead($id = ""){
    $q = $_POST["ttQ"];
    $dbTable = $_POST["dbTable"];
    $tableFields = $this->model->getTableInfos($dbTable, "", "Field");

    $searchFields0 = $_POST["searchFields"]; // prenom, nom, patient.tel, col3, col4
    if( ! $searchFields0)
      $searchFields0 = implode(",", $tableFields);
    $searchFields = "CONCAT($searchFields0)"; //toSqlConcat($searchFields0);

    $tableInfos = $this->model->getTablesInfo("", $dbTable);
    $idFieldName = "$dbTable.".tern(get($tableInfos, "id_field_name"), "id")." as id";
    $otherTab = [];
    if(get($_POST, "withOtherTab")){
      $otherTab = get($tableInfos, "other_tab");
      if( ! $otherTab)
        $otherTab = [];
      else
        $otherTab = toArrayOfArray($otherTab);
    }

    $display = $_POST["display"]; //prenom nom, patient.tel(col3, col4)
    if( ! $display)
      $display = implode(",' - ', ", $tableFields);
    $display = toSqlConcat($display, "tt_display_");//"CONCAT($display) as tt_display_"; 
    
    $select = $display.", ".$searchFields0.", ".$idFieldName;

    if($id){
      $whereAssArr = [first($idFieldName, " as") => $id];
      $result = $this->model->first_obj($dbTable, $whereAssArr, "", $otherTab, $select);
      return get($result, "tt_display_");
    }
    $q = toArray($q, " ");
    $whereAssArr = [];
    $pctg = "";
    $and = "";
    foreach ($q as $value) {
        $whereAssArr["$and$searchFields like"] = $pctg.$value."%";
        $pctg = "%";
        $and = "and ";
    }
    $result = $this->model->get_obj($dbTable, $whereAssArr, "", "", $otherTab, $select);
     
    $whereAssArr = [];
    $or = "";
    foreach ($q as $value) {
        $whereAssArr["$or$searchFields like"] = "%$value%";
        $or = "or ";
    }
    $result2 = $this->model->get_obj($dbTable, $whereAssArr, "", "", $otherTab, $select);
    $result = array_merge($result, $result2);

    if(notEmpty($result))
        echo json_encode($result);
    else echo "[]";
    toUnsetFormSessionInfos(false);
  }
}