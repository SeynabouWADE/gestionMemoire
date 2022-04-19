<?php
  // require_once("tools/view&ctrlTools.php");  //TODO à revoir est ce nécessaire
/** Pour profiter de la transaction sql il faut impérativement use directement ou indirectement les fonctions prepareQuery() et executePrepared() après la fonction prepare(). NB Toues les fonctions de cette classe respecte la transaction. ()*/
class Model{

  private $db;
  protected $table;
  private $tableInfo;
  private $transactionStatus;
  private $transactionErrorMsg;
  private $uploadedFileForRollBack;

  private static $_instance = null;
  public static function getInstance() { // Juste pour avoire une seule istance du model et pas de ces enfants // TODO lang a traduire
 
    if(is_null(self::$_instance)) {
      self::$_instance = new Model();  
    }

    return self::$_instance;
  }
  
public function __construct($host = null, $username = null, $password = null, $database = null){
  
  if($host != null){
    $_host = $host;
    $_username = $username;
    $_password = $password;
    $_database = $database;
  }
  else{  // on les prend de config.php   
    $_host = host;
    $_username = username;
    $_password = password;
    $_database = database;
  }
  try{ 
    $this->db = new PDO('mysql:host='.$_host.';dbname='.$_database.';charset=utf8', $_username, $_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $this->db->beginTransaction(); transaction();
    $this->transactionStatus = true;
    $this->transactionErrorMsg = "";
    $this->uploadedFileForRollBack = [];
  }catch (Exception $e) {
    die('<h1>Impossible de se connecter à la base de données</h1> : '.$e->getMessage());
  }
  if($this->isExiste("tables_info"))
    $this->tableInfo = $this->first("tables_info", ["name"=>$this->table]); //
}
public function getTablesInfo($field="", $tableName="", $tableInfo = ""){  
  if(notEmpty($tableName) || notEmpty($tableInfo)){
    if(empty2($tableName))
      $tableName = $this->getTable();
    $tableInfo = notEmpty($tableInfo) ? $tableInfo : "tables_info";
    $tableInfo = $this->first($tableInfo, ["name"=>$tableName]);
  }else
    $tableInfo = $this->tableInfo;
  return (notEmpty($field)) ? get($tableInfo, $field) : $tableInfo;
}
public function getLibelle(){
  return getLibelle($this->tableInfo);
}
public function getLibelleInPlural(){
  return getLibelleInPlural($this->tableInfo);
}
public function getTransactionStatus(){
  return $this->transactionStatus;
}
public function getTransactionErrorMsg(){ //TODO 111 où afficher le message d'erreur ?... voir TODO 111
  return $this->transactionErrorMsg;
}
public function commit(){
  if($this->transactionStatus)
    return $this->db->commit();
  return false;
}
public function rollBack($msg = "", $back = false){
  $this->transactionErrorMsg = $msg;
  $this->transactionStatus = false;
  $this->db->rollBack();
  $this->rollBackFiles();
  notification($this->transactionErrorMsg, "danger");
  if($back) back();
}
public function rollBackFiles(){
  $ok = 0;
  $error = 0;
  foreach ($this->uploadedFileForRollBack as $link) {
    if(unlink($link)) $ok ++;
    else $error ++;
  }
  if($ok) 
    $this->transactionErrorMsg = br().$ok." file".plural($ok).plural2($ok, " rolls", " roll")." back";
  if($error) 
    $this->transactionErrorMsg = br().$error." file".plural($error).plural2($error, " was", " were")." not roll back";
}

public function getWithNameAsKey($table, $whereAssoArray = array(), $name = "name", $select = ""){
  $result = [];
  foreach ($this->get($table, $whereAssoArray, "", "", [], $select) as $key => $value) {
    $result[get($value, $name)] = $value;
  }
  return $result;
}
public function getWithNameAsKey_obj($table, $whereAssoArray = array(), $name = "name", $select = ""){
  $result = [];
  foreach ($this->get_obj($table, $whereAssoArray, "", "", [], $select) as $key => $value) {
    $result[get($value, $name)] = $value;
  }
  return $result;
}
public function getWithIdAsKey($table, $whereAssoArray = array(), $select = ""){
  return $this->getWithNameAsKey($table, $whereAssoArray, "id", $select);
}
public function getWithIdAsKey_obj($table, $whereAssoArray = array(), $select = ""){
  return $this->getWithNameAsKey_obj($table, $whereAssoArray, "id", $select);
}
/////////// READ ///////////////
/**
 * Sécurité : PDO, Requete préparée et htmlspecialchars(). <br>
 * Ex :
 * select * from $table where $where = $whereValue order by $desc
 * select * from $table
 * @return array (tableau)
 */
public function get($table="", $whereAssoArray = array(),  $orderBy = "", $limit = "", $otherTab = [], $select = ""){
  return $this->getAllcommun($table, $whereAssoArray, $orderBy, "array", $limit, false, $otherTab, $select);
}
public function read($table="", $whereAssoArray = array(),  $orderBy = ""){
  return $this->get($table, $whereAssoArray, $orderBy);
}
/**
 * Sécurité : PDO, Requete préparée et htmlspecialchars(). <br>
 * Ex :
 * select * from $table where $where = $whereValue order by $desc
 * select * from $table
 * @return object
 */
public function get_obj($table="", $whereAssoArray = array(), $orderBy = "", $limit = "", $otherTab = [], $select = ""){
  return $this->getAllcommun($table, $whereAssoArray, $orderBy, "object", $limit, false, $otherTab, $select);	
}
public function read_obj($table="", $whereAssoArray = array(), $orderBy = ""){
  return $this->get_obj($table, $whereAssoArray, $orderBy);	
}
public function getSql($sql, $data = array()){
  return $this->prepareQuery($sql, $data);
}
public function getSql_Obj($sql, $data = array()){
  return $this->prepareQuery($sql, $data, "object");
}
/**
 * Ex. $table : etudiant, $where : id, $whereValue : 1
 * résultat : select id from etudiant where id = :id limit 1
*/
public function getId($table="", $whereAssoArray){
  $result = $this->getIds($table, $whereAssoArray, 1);
  if(! empty($result)) return $result[0]["id"];
  else  return null;	
}
public function getIds($table="", $idOrWhereAssoArray = [], $limit = ""){
  if(empty2($table))
    $table = $this->getTable();
  $limit = $limit ? "limit $limit" : "";
  
  $data = $this->formatPrepareSqlForWhere($idOrWhereAssoArray);
  $wherelist = $data[0];
  $idOrWhereAssoArray = $data[1];

  $sql = "select id from `$table` where $wherelist $limit";
  return $this->prepareQuery($sql, $idOrWhereAssoArray);
}
public function getOne($table="", $id, $oneSelectName=""){
  if(empty2($table))
    $table = $this->getTable();
  $sql = "select * from `$table` where `id`=:id limit 1";
  $result = $this->prepareQuery($sql, ["id"=>$id]);
  if(! empty($result)){
    if($oneSelectName == "") return $result[0];	
    else return $result[0][$oneSelectName];
  }
  else return null;	
}


public function first($table="", $whereAssoArray = array(), $orderBy="", $otherTab = [], $select = ""){
  $tmp = $this->getAllcommun($table, $whereAssoArray, $orderBy, "array", 1, false, $otherTab, $select);
  return isset($tmp[0]) ? $tmp[0] : null;
}
public function first_obj($table="", $whereAssoArray = array(), $orderBy="", $otherTab = [], $select = ""){
  $tmp = $this->getAllcommun($table, $whereAssoArray, $orderBy, "object", 1, false, $otherTab, $select);
  return isset($tmp[0]) ? $tmp[0] : null;
}
public function last($table="", $whereAssoArray = array(), $otherTab = [], $select = ""){
  return $this->getAllcommun($table, $whereAssoArray, "`id` desc", "array", 1, false, $otherTab, $select)[0];
}
public function last_Obj($table="", $whereAssoArray = array(), $otherTab = [], $select = ""){
  return $this->getAllcommun($table, $whereAssoArray, "`id` desc", "object", 1, false, $otherTab, $select)[0];
}
public function count($table="", $whereAssoArray = array(), $limit = "", $otherTab = []){
  return $this->getAllcommun($table, $whereAssoArray, "", "", $limit, true, $otherTab)[0][0];
}
public function statDesc($table="", $statDescCols, $whereAssoArray = array(), $otherTab = []){
  return $this->getAllcommun($table, $whereAssoArray, "", "", "", "statDesc, ".$statDescCols, $otherTab)[0];
}
public function statDesc_obj($table="", $statDescCols, $whereAssoArray = array(), $otherTab = []){
  return $this->getAllcommun($table, $whereAssoArray, "", "object", "", "statDesc, ".$statDescCols, $otherTab)[0];
}
public function stat($table="", $fct, $fields, $whereAssoArray = array(), $type = "", $otherTab = []){
  $tmp = "";
  $fields = is_array($fields) ? $fields : toArray($fields);
  $as = "";
  foreach ($fields as $value){
    $value = str_replace(" as ", " ", $value);
    $value = str_replace(" aS ", " ", $value);
    $value = str_replace(" As ", " ", $value);
    $value = str_replace(" AS ", " ", $value);
    $value = toArray($value, " ");
    $as = isset($value[1]) ? $value[1] : str_replace(".", "_", $value[0]);
    $tmp .= "$fct(".$value[0].") ".$as.", ";
  }
  $result = $this->getAllcommun($table, $whereAssoArray, "", $type, "", trimPlus($tmp), $otherTab)[0];
  if(isSeveral($fields))
    return $result;
  else
    return $type == "object" ? $result->$as : $result[0] ;
}
public function min($table="", $fields, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "min", $fields, $whereAssoArray, "", $otherTab);
}
public function max($table="", $fields, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "max", $fields, $whereAssoArray, "", $otherTab);
}
public function sum($table="", $fields, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "sum", $fields, $whereAssoArray, "", $otherTab);
}
public function avg($table="", $fields, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "avg", $fields, $whereAssoArray, "", $otherTab);
}
public function var_pop($table="", $field, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "var_pop", $field, $whereAssoArray, "", $otherTab);
}
public function variance($table="", $field, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "var_pop", $field, $whereAssoArray, "", $otherTab);
}
public function std($table="", $fields, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "std", $fields, $whereAssoArray, "", $otherTab);
}
public function min_obj($table="", $fields, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "min", $fields, $whereAssoArray, "object", $otherTab);
}
public function max_obj($table="", $fields, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "max", $fields, $whereAssoArray, "object", $otherTab);
}
public function sum_obj($table="", $fields, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "sum", $fields, $whereAssoArray, "object", $otherTab);
}
public function avg_obj($table="", $fields, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "avg", $fields, $whereAssoArray, "object", $otherTab);
}
public function var_pop_obj($table="", $field, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "var_pop", $field, $whereAssoArray, "object", $otherTab);
}
public function variance_obj($table="", $field, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "var_pop", $field, $whereAssoArray, "object", $otherTab);
}
public function std_obj($table="", $fields, $whereAssoArray = array(), $otherTab = []){
  return $this->stat($table, "std", $fields, $whereAssoArray, "object", $otherTab);
}

function statByInterval($table, $lineWhereAssAr, $colWhereAssAr, $colWhereInterval, $type = "", $otherTab = []){
  $lineWhere = $this->getAllcommun($table, $lineWhereAssAr, "", 'object', "", false, $otherTab);
  $colWhereAssAr = ternary(is_array($colWhereAssAr), $colWhereAssAr, toArray($colWhereAssAr, ";"));
  $result = [];
  $sexe_ = "";
  if(in ("between", $colWhereAssAr[0])){ // on peut ne pas avoir de between mais par exemple sexe seulement.
    $field = last($colWhereAssAr[0], " ");
    if(isset($colWhereAssAr[1])){
      if(in(":", $colWhereAssAr[1])){
        $sexe_ = toArray($colWhereAssAr[1], ":");
        $sexe = toArray($sexe_[1]);
        foreach ($sexe as $key => $value) {
          $sexe[$key] = trim($sexe[$key]);
          foreach ($colWhereInterval as $borne) {
            $result[$borne][$sexe[$key]] = 0;
          }
        } 
        $sexe_ = trim($sexe_[0]);
      }
    }else{
      foreach ($colWhereInterval as $borne) {
          $result[$borne] = 0;
      }
    }
    foreach ($lineWhere as $value){
      $number = $value->$field;
      foreach ($colWhereInterval as $borne) {
        if($number <= $borne){
          if(notEmpty($sexe_)){
            $result[$borne][$value->$sexe_] = $result[$borne][$value->$sexe_] + 1;
          }else{
            $result[$borne] = $result[$borne] + 1;
          }
        }
      }
    }
    if(isset($colWhereAssAr[2])){ // les totaux
      $totalKey = trim($colWhereAssAr[2]);
      foreach ($result as $borne => $sndFieldTab) {
        $total = 0;
        foreach ($sndFieldTab as $val) {
          $total += $val;
        }
        $result[$borne][$totalKey] = $total;
      }
    }
  }
  return ternary($type != "object", $result, (object) $result);
}


public function isExiste($table="", $idOrWhereAssoArray = ""){
  if($table && empty2($idOrWhereAssoArray)){
    return $this->isExisteTable($table);
  }
  if(empty2($table))
    $table = $this->getTable();
  $data = $this->formatPrepareSqlForWhere($idOrWhereAssoArray);
  $wherelist = $data[0];
  $idOrWhereAssoArray = $data[1];
  
  $sql = "select * from `$table` where $wherelist limit 1";
  return $this->prepareQuery($sql, $idOrWhereAssoArray);
}

function isExisteTable($tableName){
  $query = $this->db->query("SHOW tables");
  while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    if(in($tableName, $row))
      return true;
  }
  return false;
}
public function isExisteSql($sql, $whereAssoArray){
  $result = $this->prepareQuery($sql, $whereAssoArray);
  return ! empty($result);
}

private function getAllcommun0($table="", $idOrWhereAssoArray, $orderBy = "", $type="array", $limit = "", $count = false, $otherTab = []){
  if( empty($table))
    $table = $this->getTable();
  $otherTabConcat = "";
  $tables = toArray($table);
  //["tranche_age"=>["libelle']] or ["id_tranche_age->tranche_age"=>["libelle']]
  if(notEmpty($otherTab)){
    if( ! notEmpty($idOrWhereAssoArray))
      $idOrWhereAssoArray = [];
    elseif(is_string($idOrWhereAssoArray) or is_numeric($idOrWhereAssoArray))
      $idOrWhereAssoArray = [$table.'.id'=>$idOrWhereAssoArray];

    $tables2 = $tables;
    foreach ($otherTab as $newTab => $cols) { //"id_tranche_age->tranche_age"
      $oldTabField = first($newTab, "->"); // id_tranche_age
      $newTab = last($newTab, "->");       // tranche_age
      $table .= ", $newTab";
      $n = count($cols);
      $cols0 = $cols[0];
      unset($cols[0]);
      if($n > 2)
        $otherTabConcat .= ", CONCAT(`$newTab`.`$cols0`, ' (', `$newTab`.`".implode("`, ', ', `$newTab`.`", $cols)."`, ')' ) as $oldTabField";
      else if($n == 2){
        $cols = ternary(is_array($cols), first($cols), $cols);
        $otherTabConcat .= ", CONCAT(`$newTab`.`$cols0`, ' (', `$newTab`.`".$cols."`, ')' ) as $oldTabField";
      }else if($n == 1)
        $otherTabConcat .= ", `$newTab`.`$cols0` as $oldTabField";
      else
        $otherTabConcat .= ", `$newTab`.`libelle` as $oldTabField";

      foreach ($tables as $oldTab) {
        $idOrWhereAssoArray[] = "$oldTab.$oldTabField=$newTab.id";
      }
      $tables2[] = $newTab;
    }
  }
  $data = $this->formatPrepareSqlForWhere($idOrWhereAssoArray);
  
  $wherelist = $data[0];
  $idOrWhereAssoArray = $data[1];
  
  if(notEmpty($limit))
    $limit = " limit $limit";

  $order = "";
  if($orderBy != "")
    $order = " order by $orderBy"; // Ex : $orderBy <- `id` desc
  $table = "`".str_replace(",", "`,`", str_replace(" ", "", $table))."`";
  $select = "";
  if($count or notEmpty($count)){
    if(is_bool($count))
      $select = "count(*) ";
    else{
      $counts = is_array($count) ? $count : toArray($count);
      if(count($counts) == 1 and "count" == toLower($counts[0]))
        $select = "count(*) ";
      else if(in("statDesc", $counts[0], "", false)) {
        unset($counts[0]);
        $tmp = 0;
        foreach ($counts as $col) {
          $_col = $tmp == 0 ? "" : "_".str_replace(".", "_",$col);
          $select .= "COUNT($col) count$_col, MIN($col) min$_col, MAX($col) max$_col, var_pop($col) var$_col, STD($col) std$_col, SUM($col) sum$_col, AVG($col) avg$_col, ";
          $tmp ++;
        }
        $select = trimPlus($select);     
      }else{
        $select .= $count;
      }
    }
  }else{
    $select = "`$tables[0]`.*";
    if(notEmpty($otherTab) && count($tables2)> 1){
      unset($tables2[0]);
      foreach ($tables2 as $oneTable) {
        foreach (array_keys($this->getTableInfos($oneTable)) as $field){
          $select .= ", $oneTable.$field as $oneTable"."_".$field; 
        }
      }
    }
    $select .= $otherTabConcat;
    $tables = toArray($table);
  }
  $sql = "select $select from $table where $wherelist$order$limit";
  $result = $this->prepareQuery($sql, $idOrWhereAssoArray, $type);
  if($count)
    return $result;

  return $result;
}
private function getAllcommun($table="", $idOrWhereAssoArray, $orderBy = "", $type="array", $limit = "", $count = false, $otherTab = [], $select = ""){
  if( empty($table))
    $table = $this->getTable();
  $otherTabConcat = "";
  $leftJoin = [];
  $tables = toArray($table);
  $tables2 = $tables;
  //["tranche_age"=>["libelle']] or ["id_tranche_age->tranche_age"=>["libelle']]
  if(notEmpty($otherTab)){
    // if( ! notEmpty($idOrWhereAssoArray))
    //   $idOrWhereAssoArray = [];
    // elseif(is_string($idOrWhereAssoArray) or is_numeric($idOrWhereAssoArray))
    //   $idOrWhereAssoArray = [$table.'.id'=>$idOrWhereAssoArray];
    foreach ($otherTab as $newTab => $cols) { //"id_tranche_age->tranche_age"
      $oldTabField = first($newTab, "->"); // id_tranche_age choc
      $newTab = last($newTab, "->");       // tranche_age n_o_n
      $firstTab = in(".", $oldTabField) ? first($oldTabField, ".") : $tables[0];
      $tmp = "";
      foreach ($cols as $col) {
        $tmp = "`$oldTabField`.`$col` $oldTabField"."_$col, ";
      }
      $otherTabConcat .= ", $tmp";

      // $table .= ", $newTab";
      $n = count($cols);
      $cols0 = get($cols, 0);
      unset($cols[0]);
      if($n > 2){
        $otherTabConcat .= "CONCAT(`$oldTabField`.`$cols0`, ' (', `$oldTabField`.`".implode("`, ', ', `$oldTabField`.`", $cols)."`, ')' )";
      }else if($n == 2){
        $cols = ternary(is_array($cols), first($cols), $cols);
        $otherTabConcat .= "CONCAT(`$oldTabField`.`$cols0`, ' (', `$oldTabField`.`".$cols."`, ')' )";
      }else if($n == 1)
        $otherTabConcat .= "`$oldTabField`.`$cols0`";
      else
        $otherTabConcat .= "`$oldTabField`.`libelle`";
      $otherTabConcat .= " $oldTabField"; //ie " as $oldTabField"
      
      $leftJoin[$firstTab][] = " left join $newTab $oldTabField ON $firstTab.$oldTabField = $oldTabField.id";

      // foreach ($tables as $oldTab) {
      //   $idOrWhereAssoArray[] = "$oldTab.$oldTabField=$newTab.id";
      // }
      //$tables2[] = $newTab;
    }
  }
  $data = $this->formatPrepareSqlForWhere($idOrWhereAssoArray);
  
  $tabeAndLeftJoin = "";
  foreach ($leftJoin as $key => $value) {
    $tabeAndLeftJoin .= "`$key` ".implode(' ', $value).", ";
    unsetValues($tables, [$key]);
  }
  if(notEmpty($tables)){
    $tabeAndLeftJoin .= "`".implode("`, `", $tables)."`";
  }
  
  $tabeAndLeftJoin = trimPlus($tabeAndLeftJoin);

  //$table = "`".str_replace(",", "`,`", str_replace(" ", "", $table))."`";
  $wherelist = $data[0];
  $idOrWhereAssoArray = $data[1];
  
  if(notEmpty($limit))
    $limit = " limit $limit";

  $order = "";
  if($orderBy != "")
    $order = " order by $orderBy"; // Ex : $orderBy <- `id` desc
  
  // TODO if $select != "" vérifier s'il contien les back tick ``
  if($select === ""){
    if($count or notEmpty($count)){
      if(is_bool($count))
        $select = "count(*) ";
      else{
        $counts = is_array($count) ? $count : toArray($count);
        if(count($counts) == 1 and "count" == toLower($counts[0]))
          $select = "count(*) ";
        else if(in("statDesc", $counts[0], "", false)) {
          unset($counts[0]);
          $tmp = 0;
          foreach ($counts as $col) {
            $_col = $tmp == 0 ? "" : "_".str_replace(".", "_",$col);
            $select .= "COUNT($col) count$_col, MIN($col) min$_col, MAX($col) max$_col, var_pop($col) var$_col, STD($col) std$_col, SUM($col) sum$_col, AVG($col) avg$_col, ";
            $tmp ++;
          }
          $select = trimPlus($select);     
        }else{
          $select .= $count;
        }
      }
    }else{
      $select = "`$tables2[0]`.*";
      if(isset($tables2[1])){ // ie count($tables2) > 1
        unset($tables2[0]);
        foreach ($tables2 as $oneTable) {
          foreach (array_keys($this->getTableInfos($oneTable)) as $field){
            $select .= ", $oneTable.$field as $oneTable"."_".$field; 
          }
        }
      }
      $select .= $otherTabConcat;
      //$tables = toArray($table);
    }
  }
  $sql = "select $select from $tabeAndLeftJoin where $wherelist$order$limit";
  //seec($sql, $idOrWhereAssoArray);
  $result = $this->prepareQuery($sql, $idOrWhereAssoArray, $type);
  if($count)
    return $result;

  return $result;
}

private function getAllcommun2($table="", $idOrWhereAssoArray, $orderBy = "", $type="array", $limit = "", $count = false, $otherTab = []){
  if( empty($table))
    $table = $this->getTable();
  $otherTabConcat = "";
  $tables = toArray($table);
  //["tranche_age"=>["libelle']] or ["id_tranche_age->tranche_age"=>["libelle']]
  
  $colTab = [];
  if(notEmpty($otherTab)){
    if( ! notEmpty($idOrWhereAssoArray))
      $idOrWhereAssoArray = [];
    elseif(is_string($idOrWhereAssoArray) or is_numeric($idOrWhereAssoArray))
      $idOrWhereAssoArray = [$table.'.id'=>$idOrWhereAssoArray];

    $tables2 = $tables;
    $complTab = [];
    $complTab2 = [];
    foreach ($otherTab as $newTab => $cols) { //"id_tranche_age->tranche_age"
      $oldTabField = first($newTab, "->");    // id_tranche_age
      $newTab = last($newTab, "->");       // tranche_age

      if(notIn($newTab, $complTab2)){
        $complTab[$newTab] = (array) $this->getWithIdAsKey_obj($newTab);
        $complTab2[] = $newTab;
      }else{
        $colTab[$oldTabField] = [$newTab, $cols];
        continue;
      }     
      $table .= ", $newTab";
      $n = count($cols);
      $cols0 = get($cols, 0);
      unset($cols[0]);
      if($n > 2)
        $otherTabConcat .= ", CONCAT(`$newTab`.`$cols0`, ' (', `$newTab`.`".implode("`, ', ', `$newTab`.`", $cols)."`, ')' ) as $oldTabField";
      else if($n == 2){
        $cols = ternary(is_array($cols), first($cols), $cols);
        $otherTabConcat .= ", CONCAT(`$newTab`.`$cols0`, ' (', `$newTab`.`".$cols."`, ')' ) as $oldTabField";
      }else if($n == 1)
        $otherTabConcat .= ", `$newTab`.`$cols0` as $oldTabField";
      else
        $otherTabConcat .= ", `$newTab`.`libelle` as $oldTabField";

      foreach ($tables as $oldTab) {
        $idOrWhereAssoArray[] = "$oldTab.$oldTabField=$newTab.id";
      }
      $tables2[] = $newTab;
    }
  }
  $data = $this->formatPrepareSqlForWhere($idOrWhereAssoArray);
  
  $wherelist = $data[0];
  $idOrWhereAssoArray = $data[1];
  
  if(notEmpty($limit))
    $limit = " limit $limit";

  $order = "";
  if($orderBy != "")
    $order = " order by $orderBy"; // Ex : $orderBy <- `id` desc
  $table = "`".str_replace(",", "`,`", str_replace(" ", "", $table))."`";
  
  $select = "";
  if($count or notEmpty($count)){
    if(is_bool($count))
      $select = "count(*) ";
    else{
      $counts = is_array($count) ? $count : toArray($count);
      if(count($counts) == 1 and "count" == toLower($counts[0]))
        $select = "count(*) ";
      else if(in("statDesc", $counts[0], "", false)) {
        unset($counts[0]);
        $tmp = 0;
        foreach ($counts as $col) {
          $_col = $tmp == 0 ? "" : "_".str_replace(".", "_",$col);
          $select .= "COUNT($col) count$_col, MIN($col) min$_col, MAX($col) max$_col, var_pop($col) var$_col, STD($col) std$_col, SUM($col) sum$_col, AVG($col) avg$_col, ";
          $tmp ++;
        }
        $select = trimPlus($select);     
      }else{
        $select .= $count;
      }
    }
  }else{
    $select = "`$tables[0]`.*";
    if(notEmpty($otherTab) && count($tables2)> 1){
      unset($tables2[0]);
      foreach ($tables2 as $oneTable) {
        foreach (array_keys($this->getTableInfos($oneTable)) as $field){
          $select .= ", $oneTable.$field as $oneTable"."_".$field; 
        }
      }
    }
    $select .= $otherTabConcat;
    $tables = toArray($table);
  }
  $sql = "select $select from $table where $wherelist$order$limit";
  $result = $this->prepareQuery($sql, $idOrWhereAssoArray, $type);
  if($count)
    return $result;
  
  foreach($result as $oneResKey => $oneResult){
    foreach($colTab as $oldTabField => $newTabCols){
      if(get($oneResult, $oldTabField)){
        $result[$oneResKey]->$oldTabField = 
        labelBuilder($complTab[$newTabCols[0]]
        [$oneResult->$oldTabField], //[id=>1,libelle=>Karate,prix=>10000,heure=>12:00]
        $newTabCols[1], 0);//[id, libelle, prix, heure]
      }
    }
  }
  return $result;
}
private function getAllcommun1($table="", $idOrWhereAssoArray, $orderBy = "", $type="array", $limit = "", $count = false, $otherTab = []){
  if( empty($table))
    $table = $this->getTable();
  $tables = toArray($table);
  
  $data = $this->formatPrepareSqlForWhere($idOrWhereAssoArray);
  $wherelist = $data[0];
  $idOrWhereAssoArray = $data[1];
  
  if(notEmpty($limit))
    $limit = " limit $limit";

  $order = "";
  if($orderBy != "")
    $order = " order by $orderBy"; // Ex : $orderBy <- `id` desc
  $table = "`".str_replace(",", "`,`", str_replace(" ", "", $table))."`";
  $select = "";
  if($count or notEmpty($count)){
    if(is_bool($count))
      $select = "count(*) ";
    else{
      $counts = is_array($count) ? $count : toArray($count);
      if(count($counts) == 1 and "count" == toLower($counts[0]))
        $select = "count(*) ";
      else if(in("statDesc", $counts[0], "", false)) {
        unset($counts[0]);
        $tmp = 0;
        foreach ($counts as $col) {
          $_col = $tmp == 0 ? "" : "_".str_replace(".", "_",$col);
          $select .= "COUNT($col) count$_col, MIN($col) min$_col, MAX($col) max$_col, var_pop($col) var$_col, STD($col) std$_col, SUM($col) sum$_col, AVG($col) avg$_col, ";
          $tmp ++;
        }
        $select = trimPlus($select);     
      }else{
        $select .= $count;
      }
    }
  }else{
    $select = "`$tables[0]`.*";
    if(count($tables)> 1){
      unset($tables[0]);
      foreach ($tables as $oneTable) {
        foreach (array_keys($this->getTableInfos($oneTable)) as $field){
          $select .= ", $oneTable.$field as $oneTable"."_".$field; 
        }
      }
    }
  }
  $sql = "select $select from $table where $wherelist$order$limit";
  $result = $this->prepareQuery($sql, $idOrWhereAssoArray, $type);
  if($count)
    return $result;
  
  //["tranche_age"=>["libelle']] or ["id_tranche_age->tranche_age"=>["libelle']]
   if(notEmpty($otherTab)){
     $complTab = [];
     $complTab2 = [];
     foreach ($otherTab as $newTab => $cols) { //"id_tranche_age->tranche_age"
       $oldTabField = first($newTab, "->");    // id_tranche_age
       $newTab = last($newTab, "->");          // tranche_age

       $colTab[$oldTabField] = [$newTab, $cols];
       if(notIn($newTab, $complTab2)){
         $complTab[$newTab] = (array) $this->getWithIdAsKey_obj($newTab);
         $complTab[] = $newTab;
       }
     }
     foreach($result as $oneResKey => $oneResult){
      foreach($colTab as $oldTabField => $newTabCols){
        if(get($oneResult, $oldTabField)){
          $result[$oneResKey]->$oldTabField = 
          labelBuilder($complTab[$newTabCols[0]][$oneResult->$oldTabField], //[id=>1,libelle=>Karate,prix=>10000,heure=>12:00]
          $newTabCols[1], 0);//[id, libelle, prix, heure]
        }
      }
     }
   }

  return $result;
}
  
///////// UPDATE ///////
public function update($table="", $valuesAssoArray, $idOrWhereAssoArray,  $without="", $withoutDefault = withoutDefault, $deleteIdField = "id"){
  if(empty2($table))
    $table = $this->getTable();

  $without .= ",".$withoutDefault;
  if($without != ""){
    foreach(toArray($without, ",") as $one){
      unset($valuesAssoArray[$one]);
    }
    if($deleteIdField)
      unset($valuesAssoArray[$deleteIdField]);
  }

  $valuesAssoArray = $this->setDefaultValues($valuesAssoArray, $table);
 
  $data = $this->arrayToPrepareSql($valuesAssoArray);
  $valueslist = str_replace(" and", ",", $data[0]);
  $valuesAssoArray = $data[1];
  
  $data = $this->formatPrepareSqlForWhere($idOrWhereAssoArray);
  $wherelist = $data[0];
  $idOrWhereAssoArray = $data[1];
  $sql = "UPDATE `$table` SET $valueslist WHERE $wherelist";
  //sd("aaaa",[$sql, $valuesAssoArray + $idOrWhereAssoArray]);
  return $this->prepareQuery($sql, $valuesAssoArray + $idOrWhereAssoArray);
}
///////////// CREATE ///////////
/**
 * A condition que les champs de la table dans la BD correspondent aux clés de la table $data.
 */
public function insert($table="", $data, $backIfRollBack = false, $without="", $withoutDefault = withoutDefault, $primaryKeyFiel = primaryKeyFiel){
  if(empty2($table))
    $table = $this->getTable();
  //numeric, string, dateTime, list
  $without .= ",".$withoutDefault;
  if($without != ""){
    foreach(toArray($without, ",") as $one){
      unset($data[$one]);
    }
  }
  $data = $this->setDefaultValues($data, $table);
  $sql = "INSERT INTO `".$table."` (`".implode("`, `", array_keys($data))."`) VALUES(:".implode(", :", array_keys($data)).")";
  // if($prenom=$_SESSION["hf2"]['prenom']){ echo $prenom;}
  $result = $this->prepareQuery($sql, $data, $backIfRollBack);
  if($result)
    $result = $this->db->lastInsertId($primaryKeyFiel);
  return $result;
}
public function setDefaultValues($data, $table = ""){
  if(empty2($table))
    $table = $this->getTable();
  $fieldsShema = $this->getTableInfos($table);
  foreach ($data as $oneField => $value) {
    if($value == "" && notIn(first($fieldsShema[$oneField]["Type"], "("), sqlStringTab)){
      $data[$oneField] = get($fieldsShema, $oneField, "Default", null);
    }
  }
  return $data;
}
  
//////////// DELETE ///////////
public function delete($table="", $idOrWhereAssoArray, $backIfRollBack = false){
  if(empty2($table))
    $table = $this->getTable();
  $data = $this->formatPrepareSqlForWhere($idOrWhereAssoArray);
  $wherelist = $data[0];
  $idOrWhereAssoArray = $data[1];
  if(empty($idOrWhereAssoArray)) return false;
  $sql = "DELETE FROM `$table` WHERE $wherelist";
  return $this->prepareQuery($sql, $idOrWhereAssoArray, $backIfRollBack);
}
public function del($table = "", $idOrWhereAssoArray, $backIfRollBack = false){
  return $this->delete($table, $idOrWhereAssoArray, $backIfRollBack);
}
/** $path peut contenir tout sauf l'extension car c'est déduit du fichier... TODO tester upload de fichier */
public function uploadFile($fieldName, $path = "", $backIfWrong = true, $replaceExisted = false){
  $valiMsg = "";
  if($_FILES[$fieldName]['error'] == 0){
    //On formate le nom du fichier ici...
    if($path == "" or last($path) == "/"){
      $pam = basename($_FILES[$fieldName]['name']);
      $pam = strtr($pam,
      'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
      'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
      $pam = preg_replace('/([^.a-z0-9]+)/i', '-', $pam);
      
      $path .= $pam;
    }else{
      $path = $path.".".strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }
    if( ! $replaceExisted and file_exists($path)){
      $valiMsg = "already_exists";
    }
    else if( ! move_uploaded_file($_FILES[$fieldName]["tmp_name"], $path)){
      $valiMsg = "uploaded"; // not uploaded
    }else
      $this->uploadedFileForRollBack[] = $path;
  }
  if($valiMsg != "" and $backIfWrong){
    formError($fieldName, validationMsg([$valiMsg], ['attribute:'.formLabel($fieldName)]));
    wasValidated();
    back();
    die;
  }
  else if($valiMsg == ""){
    return $path;
  }
  else{
    return false;
  }

}
public function crudMsg(string $type, int $libelleHeadOrFirstLine123){
  $msgs = "";
  $libelle = "";
  if("create" == $type){
    $libelle = $this->getLibelle();
    switch ($libelleHeadOrFirstLine123) {
      case 1: $msgs = msgs['create']; break;
      case 2: $msgs = msgs['createCardHeader'];break;
      case 3: $msgs = msgs['createCardFirstLine'];break;
    }
  }
  elseif("read" == $type){
    $libelle = $this->getLibelleInPlural();
    switch ($libelleHeadOrFirstLine123) {
      case 1: $msgs = msgs['read'];break;
      case 2: $msgs = msgs['readCardHeader'];break;
      case 3: $msgs = msgs['readCardFirstLine'];break;
    }
  }
  elseif("show" == $type){
    $libelle = $this->getLibelle();
    switch ($libelleHeadOrFirstLine123) {
      case 1: $msgs = msgs['show'];break;
      case 2: $msgs = msgs['showCardHeader'];break;
      case 3: $msgs = msgs['showCardFirstLine'];break;
    }
  }
  elseif("update" == $type){
    $libelle = $this->getLibelle();
    switch ($libelleHeadOrFirstLine123) {
      case 1: $msgs = msgs['update'];break;
      case 2: $msgs = msgs['updateCardHeader'];break;
      case 3: $msgs = msgs['updateCardFirstLine'];break;
    }
  }
  elseif("delete" == $type){
    $libelle = $this->getLibelle();
    switch ($libelleHeadOrFirstLine123) {
      case 1: $msgs = msgs['delete'];break;
      case 2: $msgs = msgs['deleteCardHeader'];break;
      case 3: $msgs = msgs['deleteCardFirstLine'];break;
    }
  }
  elseif("logIn" == $type){
    $libelle = "";
    switch ($libelleHeadOrFirstLine123) {
      case 1: $msgs = msgs['logIn'];break;
      case 2: $msgs = msgs['logInCardHeader'];break;
      case 3: $msgs = msgs['logInCardFirstLine'];break;
    }
  }
  elseif("logOut" == $type){
    $libelle = "";
    switch ($libelleHeadOrFirstLine123) {
      case 1: $msgs = msgs['logOut'];break;
      case 2: $msgs = msgs['logOutCardHeader'];break;
      case 3: $msgs = msgs['logOutCardFirstLine'];break;
    }
  }
  elseif("registration" == $type){
    $libelle = "";
    switch ($libelleHeadOrFirstLine123) {
      case 1: $msgs = msgs['registration'];break;
      case 2: $msgs = msgs['registrationCardHeader'];break;
      case 3: $msgs = msgs['registrationCardFirstLine'];break;
    }
  }

  if(msgs['crudWordFistOnPage'])
    return trim($msgs." ".$libelle);
  else   
    return trim(ucfirst($libelle)." ".$msgs);
}
public function tableInfo($field="", $tableName="", $tableInfo = "tables_info"){
  return $this->getTablesInfo($field, $tableName, $tableInfo);
}
public function getTable(){
  if(isset($this->table))
    return $this->table;
  else{
    if(devMode){
      notice("The name of the table is not specified. It must then be done at the table attribute of the model or at the first parameter of the function");
      
      see(debug_backtrace());
    }else
      throw new Exception("The name of the table is not specified. It must then be done at the table attribute of each model or at the first parameter of the function", 1); //TODO revoir la phrase elle n'est pas précise en bug et fatoriser aussi
  }
}
public function setTable($table){
  $this->table = $table;
}
public function query($sql){
  return $this->db->query($sql);
}
 
/** en cas de select retourn un tableau ou un objet sinon retourn un boolean.*/
public function prepareQuery($sql, $data, $returnArrayOrObj = "array", $backIfRollBack = false){  
  try {
    $prepare = $this->db->prepare($sql);
    $result = $prepare->execute($data);
  } catch (\Throwable $th) {
    $prepare->closeCursor();
    $this->rollBack($th->getMessage(), $backIfRollBack);
    session("modelExeption", ([$sql, $data, $th]));
    throw $th;
  }
  if(in(trim($sql)[0], ["s", "S"])){  // en cas de select ...
    if($returnArrayOrObj == "" or $returnArrayOrObj == "array")
      $result = $prepare->fetchAll();
    else
      $result = $prepare->fetchAll(PDO::FETCH_OBJ);
  }
  $prepare->closeCursor();
  return $result;
}
public function prepare($sql){
  return $this->db->prepare($sql);
}
public function executePrepared($prepare, $data = array(), $backIfRollBack = false){
  try {
    return $prepare->execute($data);
  } catch (Exception $e) {
    $this->rollBack($e->getMessage(), $backIfRollBack);
  }
}
public function closePrepared($prepare){
  return $prepare->closeCursor();
}
public function clean($donnees){
  $donnees=trim($donnees);
  $donnees=stripslashes($donnees);
  //$donnees=strip_tags($donnees);
  return $donnees;
}

private function formatPrepareSqlForWhere ($whereAssoArray){
  if(empty($whereAssoArray) and $whereAssoArray != 0)
    return ["1 ", []];

  if(is_string($whereAssoArray) or is_numeric($whereAssoArray))
    return ["`id`=:id", ['id'=>$whereAssoArray]];

  $data = $this->arrayToPrepareSql($whereAssoArray);  
  return [get($data, 0), get($data, 1)]; // [$wherelist, $whereAssoArray]
}
private function arrayToPrepareSql($whereAssoArray){
  if(!notEmpty($whereAssoArray))
    return "";
  $sqlKeysWord = [' between',' not in',' in',' like'];
  $sqlKeysSymb = ['!=','>=','<=','=','>','<'];
  $sqlKeys = $sqlKeysWord + $sqlKeysSymb;
  $keys = array_keys($whereAssoArray);
  $n = count($keys);

  $wherelist = "";
  $whereAssoArray2 = [];

  for($i = 0 ; $i < $n; $i++){
    $oneSqlKey = "";
    if(is_numeric($keys[$i])){ //quand il n'y a pas de deuxième partie pour une clé
      $operator = "";
      $oneKey2 = "";
      $oneKey = trimPlus($whereAssoArray[$keys[$i]]);
      $oneKey = str_replace(", ", ",", $oneKey);
      foreach ($sqlKeysWord as $value) {
        $oneKey = str_ireplace("$value ", "`$value` ", $oneKey);
      }
      foreach ($sqlKeysSymb as $value) { // " >=", ">= ", " >= " ou ">=" avec les ``
        $oneKey = str_replace($value, "`$value`", $oneKey, $pos);
        if($pos){
          $oneKey = str_replace("`$value` ", "`$value`", $oneKey);
          $oneKey = str_replace(" `$value`", "`$value`", $oneKey);
        }
      }
    }else{
      $operator = "=";
      $oneKey = trimPlus($keys[$i]);
      $oneSqlKey = endsWith($oneKey, $sqlKeys);
      if($oneSqlKey){
        $operator = "$oneSqlKey "; 
        $oneKey = substr($oneKey, 0, strlen($oneKey) - strlen($oneSqlKey));
      }
      $oneKey2 = str_replace(".", "_", $oneKey);
      //$oneKey2 = str_replace(" ", "_", $oneKey2);
    }
    if($i == 0){
      $andOr = "";
    }else{
      $andOr = startsWith($oneKey, ["and ", "or "]);
      if($andOr){
        $lenTmp = strlen($andOr);
        $oneKey = substr($oneKey, $lenTmp);
        $oneKey2 = substr($oneKey2, $lenTmp);
        $andOr = " ".$andOr;
      }else
        $andOr = " and ";
    }

    $oneKey = trim($oneKey);
    $oneKey2 = trim($oneKey2);
    if(trim(toLower(first($oneKey, '('))) == "concat"){
      $oneKey = addPrefixes($oneKey,"","`,`");
      $oneKey = addPrefixes($oneKey,"","(`", "(");
      $oneKey = addPrefixes($oneKey,"","`)", ")");
      $oneKey2 = "concat";
    }else
      $oneKey = "`$oneKey`";

    $oneKey = $oneKey ? str_replace(".", "`.`", $oneKey) : "";
    $oneKey = $oneKey ? str_replace("`' '`", "' '", $oneKey) : "";
    if($oneKey2){
      if(in($oneSqlKey, $sqlKeysWord)){
        $oneKey2_0 = $oneKey2;
        $oneKey2 = "";
        $vals = trimPlus($whereAssoArray[$keys[$i]]);
        $vals = str_replace(", ", ",", $vals);

        if($oneSqlKey == ' between'){
          $vals = explode(" and ", $vals);
          $oneKey2 .= ":between$i"."_0 and :between$i"."_1";
          $whereAssoArray2["between$i"."_0"] = $vals[0];
          $whereAssoArray2["between$i"."_1"] = $vals[1];
        }
        elseif($oneSqlKey == ' not in' or $oneSqlKey == ' in'){ //(1) (1,2,3)
          if($vals[0]=="(") $vals = substr($vals, 1, strlen($vals)-2);
          $vals = explode(",", $vals);
          $nTmp = count($vals);
          $oneKey2 .= ":in$i"."_0";
          $whereAssoArray2["in$i"."_0"] = $vals[0];
          for($j = 1; $j < $nTmp; $j++) {
            $oneKey2 .= ",:in$i"."_$j";
            $whereAssoArray2["in$i"."_$j"] = $vals[$j];
          }
          $oneKey2 = "($oneKey2)";
        }
        elseif($oneSqlKey == ' like'){
          $whereAssoArray2["$oneKey2_0$i"] = $whereAssoArray[$keys[$i]];
          $oneKey2 = ":$oneKey2_0$i";
        }
      }
      else{
        $whereAssoArray2[$oneKey2.$i] = $whereAssoArray[$keys[$i]];
        $oneKey2 = ":$oneKey2$i";
      }
      //$sqlKeysWord = [' between',' not in',' in',' like'];
    }
    $wherelist .= "$andOr$oneKey$operator$oneKey2";
  }
  return [$wherelist." ", $whereAssoArray2];
}

function getTables($withInfos = false){
  $query = $this->db->query("SHOW tables");
  $i = 0;
  while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $oneTable = first($row);
    $listTables[$i++] = [$oneTable, firstKey($row)];
    if($withInfos){
      $listTables[$oneTable] = $this->getTableInfos($oneTable);
    }
  }
  return $listTables;
}
function isTable($withInfos = false){
  $query = $this->db->query("SHOW tables");
  $i = 0;
  while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $oneTable = first($row);
    $listTables[$i++] = [$oneTable, firstKey($row)];
    if($withInfos){
      $listTables[$oneTable] = $this->getTableInfos($oneTable);
    }
  }
  return $listTables;
}
/**contrainteName possible Field, Type, Null, Key, Default, Extra  */
function getTableInfos($tabName="", $colName = "", $contrainteName = "", $strict = true){
  if(empty2($tabName))
    $tabName = $this->getTable();
    
    $listTables = array();
    $query = $this->db->query("SHOW COLUMNS FROM `$tabName`");
    
    if($colName == "" and $contrainteName != ""){
      while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        // $listTables[$i++] = $row;
        $contrainteName = ucfirst($contrainteName);
        if($contrainteName == "Type"){
          if($strict){
            $listTables[$row['Field']] = first($row[$contrainteName], "(");
          }else{
            $tmp = split($row[$contrainteName], "(");
            $tmp[1] = isset($tmp[1]) ? trimPlus($tmp[1], ")") : "";
            $listTables[$row['Field']] = [$tmp[0], $tmp[1]];
          }
        }else
          $listTables[$row['Field']] = $row[$contrainteName];
      }
    }else {
      while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      // $listTables[$i++] = $row;
       $listTables[$row['Field']] = $row;
      }
    }
    if($colName){
      if($contrainteName)
        return $listTables[$colName][$contrainteName];
      return $listTables[$colName];
    }
    return $listTables;
}
/**contrainteName possible Field, Type, Null, Key, Default, Extra */
function getTableInfosType($tabName="", $contrainteName = "Type", $strict = true){
  return $this->getTableInfos($tabName, "", $contrainteName, $strict);
}
/** numeric, string, dateTime, list */
function getTableInfosPhpType($tabNames="", $contrainteName = "Type"){
  if(empty2($tabNames))
    $tabNames = $this->getTable();

  if(is_string($tabNames))
    $tabNames = toArray($tabNames);
  $result = []; $tmp0 = "";
  foreach ($tabNames as $tabName) {
    $tabName = last($tabName, "->");
    if($tmp0 == "") //TODO à vérifier complètement, prioritaire
      $tmp0 = "";
    else
      $tmp0 = "$tabName.";
    foreach ($this->getTableInfos($tabName, "", $contrainteName) as $key => $value) {
      $tmp = $tmp0.$key;
      if(in($value, sqlIntegerTab))
        $result[$tmp] = "integer";
      else if(in($value, sqlRealTab))
        $result[$tmp] = "real";
      else if(in($value, sqlStringTab))
        $result[$tmp] = "string";
      else if(in($value, sqlListTab))
        $result[$tmp] = "list";
      else if(in($value, sqlDateTimeTab))
        $result[$tmp] = "datetime";
      else 
        $result[$tmp] = $value;
    }
    $tmp0 = "pam"; //just to put something
  }
  return $result;
}

private function arrayToPrepareSql0($whereAssoArray){
  if(empty($whereAssoArray) and $whereAssoArray != 0)
    return "";
  $sqlKey = ['between','in','not in','like','!=','>=','<=','=','>','<'];
  $keys = array_keys($whereAssoArray);
  $n = count($keys);
  $operator = "=";
  $oneKey = $keys[0];
  $i = 0;

  $oneSqlKey = endsWith($keys[$i], $sqlKey);
  if($oneSqlKey){
    $operator = $oneSqlKey; 
    $oneKey = trim(str_ireplace($oneSqlKey, "", $keys[$i]));
    $oneKey2 = str_replace(".", "_", $oneKey);
    $oneKey = str_replace(".", "`.`", $oneKey);
  }
  $whereAssoArray2[$oneKey2]= $whereAssoArray[$keys[$i]];

  $wherelist = "`$oneKey`$operator:$oneKey2";
  for($i = 1 ; $i < $n; $i++){
    $operator = "=";
    $oneKey = $keys[$i];
    $oneSqlKey = endsWith($keys[$i], $sqlKey);
    
    if($oneSqlKey){
      $operator = $oneSqlKey; 
      $oneKey = trim(str_replace($oneSqlKey, "", $keys[$i]));
      $oneKey2 = str_replace(".", "_", $oneKey);
      $oneKey = str_replace(".", "`.`", $oneKey);
    }
    $whereAssoArray2[$oneKey2]= $whereAssoArray[$keys[$i]];

    $wherelist .= " and `$oneKey`$operator:$oneKey2";
  }
  return [$wherelist." ", $whereAssoArray2];
}




function ruleTypeFromDb($dbType){
  return $this->ruleAndHtmlTypeFromDb($dbType)[0];
}
function htmlTypeFromDb($dbType){
  return $this->ruleAndHtmlTypeFromDb($dbType)[1];
}
private function ruleAndHtmlTypeFromDb($dbType){
	$tmp = [ // type sql => [type php, type html5]
	  "char"=>["string", "text"],
	  "varchar"=>["string", "text"],
	  "binary"=>["string", "text"],
	  "varbinary"=>["string", "text"],
	  "tinyblob"=>["string", "text"],
	  "tinytext"=>["string", "text"],
	  "text"=>["string", "text"],
	  "blob"=>["string", "text"],
	  "mediumtext"=>["string", "text"],
	  "mediumblob"=>["string", "text"],
	  "longtext"=>["string", "text"],
	  "longblob"=>["string", "text"],
	  "enum"=>["string", "text"],  //only one values -> radio (<= 4 elmts) or select
    "set"=>["string", "checkbox"],   // 0 or more values -> checkbox
    "json"=>["string", "text"], //TODO json
	  "bit"=>["integer", "number"],
	  "tinyint"=>["integer", "number"],
	  "bool"=>["boolean", "text"],
	  "boolean"=>["boolean", "text"],
	  "smallint"=>["integer", "number"],
	  "mediumint"=>["integer", "number"],
	  "int"=>["integer", "number"],
	  "integer"=>["integer", "number"],
	  "bigint"=>["integer", "number"],
	  "float"=>["numeric", "number"],
	  "double"=>["numeric", "number"],
	  "decimal"=>["numeric", "number"],
	  "dec"=>["numeric", "number"],
	  "date"=>["date", "date"],
	  "datetime"=>["datetime", "datetime"], //TODO to add in rules
	  "timestamp"=>["timestamp", "timestamp"], //TODO to add in rules
	  "time"=>["time", "time"], //TODO to add in rules
	  "year"=>["year", "year"]  //TODO to add in rules
  ];
	return $tmp[strtolower($dbType)];
	//name, type, othersHtml
	//tel , tel,
}
function getHtmlType($columnInfosType, $fieldsInfoType = ""){
  
  return $fieldsInfoType ? $fieldsInfoType : $this->htmlTypeFromDb($columnInfosType);
}
function getRulesFromDbColInfo(array $columnInfos){
	$rules = "";
	if(in($columnInfos["Null"], ["NO", "no"]))
	  $rules = "required|";
	$superType = explode("(", $columnInfos["Type"]); //ex. int(11)
	$rules .= $this->ruleTypeFromDb($superType[0])[0];
	if(isset($superType[1])){
	  if(in($superType[0], ["enum", "set"])) //TODO revoir le set qui peut accepter plusieurs valeurs à la fois
		$rules .= "|in(".$superType[1];
	  else
		$rules .= "|max:".explode(")", $superType[1])[0];
	}
	return $rules;
}
function oneFormField($columnInfos, $fieldsInfo, $type, $otherTab = [], $defaultValue = "", $floating = true, $number = "", $whereAssoArray = [], $ajaxValidation = "onblur", $fieldSuffixe = "", Controller $ctrl = null){

  $defaultWhereOrderByLimit = get($fieldsInfo, "default_where_order_by_limit");
  if($defaultWhereOrderByLimit){
    if(notIn(",", $defaultWhereOrderByLimit))
      $defaultWhereOrderByLimit = $defaultWhereOrderByLimit.",,";
    $defaultWhereOrderByLimit = toArray($defaultWhereOrderByLimit);
    if(empty($whereAssoArray))
      $whereAssoArray = toArray(get($defaultWhereOrderByLimit, 0));
    $orderBy = get($defaultWhereOrderByLimit, 1);
    $limit = get($defaultWhereOrderByLimit, 2);
  }else{
    $orderBy = "";
    $limit = "";
  }
      
  $defaultValue0 = $defaultValue;
  if($defaultValue == "")
    $defaultValue = get($columnInfos, 'Default', lang, "", false);
  if($defaultValue == "")
    $defaultValue = get($fieldsInfo, 'default_value', lang, "", false);

  $title = get($fieldsInfo, 'title', lang, "", false);
  $realName = $columnInfos['Field'];
  $name  = $realName.$fieldSuffixe.$number; //TODO test pour la cas ou on a un select multiple *

  if(in($type, ['typeahead', 'select'])){
    $otherName = ""; // "id_age->age"
    $oneOtherTab = "";
    //les infos dans $otherTab sont forcément associatifs
    $oneOtherTabInfo = getWhereKeyStartsWith($otherTab, $realName, true); //New 10/01/2021
    if($oneOtherTabInfo){
      $otherName = $oneOtherTabInfo[0];
      $oneOtherTab = $oneOtherTabInfo[0];
    }
    if( ! $otherName )
      $otherName = $realName;

    $otherTabName = last($otherName, "->");
  }
  $id = get($fieldsInfo, "html_id", "", $name);  
  // $ajaxValidation .= '=\'ajaxFormValidator({"fielName":"'.$name.'", "value": $(this).val(), "id":"'.$id.'"})\''; 
  $ajaxValidation .= "=\"ajaxFormValidator'$id', '$number')\""; 
  
 ob_start();
  if(notEmpty($fieldsInfo)){
    $otherHtml = get($fieldsInfo, "other_html");
    $otherHtml .= get($columnInfos, "otherHtml");    
    /* $otherHtmlPhp1 = between($otherHtml, "<?=", "?>", false);
    
    if($otherHtmlPhp1){
      $otherHtmlPhp0 = "<?=".$otherHtmlPhp1."?>";
    }else{
      $otherHtmlPhp1 = between($otherHtml, "<?php", "?>", false);
      $otherHtmlPhp0 = "<?php".$otherHtmlPhp1."?>";
    }
    if($otherHtmlPhp1){
      $otherHtmlPhp = last(trim($otherHtmlPhp1), "echo");
      $otherHtmlPhp = str_replace("$", "_dolar_", $otherHtmlPhp);
      if(first($otherHtmlPhp, "(")) // en cas d'appelle de fonction
        $otherHtmlPhp = callFunctionFromString($otherHtmlPhp);
      else
        notice("Quand on est dans field info, on use du php qu'en appelant une fonciton
        Revoir le champs other_html de la table fields_info à la ligne concernant ".
        get($fieldsInfo, "other_html").". NB : En cas de dependance vous pouvez vérifier
         dans dependance_field du champs dépendant de celui-ci."); //TOTO mettre en anglais

      $otherHtmlPhp = str_replace("_dolar_", "$", $otherHtmlPhp);

      $otherHtml = str_replace($otherHtmlPhp0, $otherHtmlPhp, $otherHtml);

      // if(get($columnInfos, 'isDependance'))
      //   $fieldsInfo["ajax_validation"] = "none";

    } */

    $otherHtml .= ternary2(get($fieldsInfo, "ajax_validation"), ' valideventtype="', '"');

    $label = get($fieldsInfo, "label", lang, "", false);

    if($type == 'file'){
      $accepted = "";
      if($phpJsRule = $fieldsInfo["php_js_rule"]){ //TODO Revoir les types de fichier a prendre en compte et les validations
        $fileTypes = "";
        if(in("mimes:", $phpJsRule))
          $fileTypes = "mimes:";
        elseif(in("mimetypes:", $phpJsRule))
          $fileTypes = "mimetypes:";
        elseif(in("types:", $phpJsRule))
          $fileTypes = "types:";
        if($fileTypes)
          $accepted = tern((between($phpJsRule, $fileTypes, "|")));
      }
      uploadFile($name, $label, $title, "", "", $accepted, "", "", "", $ajaxValidation); // TODO ajouter $fieldsInfo["html_id"], $fieldsInfo["html_class"] et $fieldsInfo["other_html"] dans les parametres de uploadFile
    }

    elseif($type == 'textarea'){
      $defaultValue = get($columnInfos, 'Default', lang, "", false);      
      textarea($label, $name, $defaultValue, get($fieldsInfo, "html_id"), get($fieldsInfo, "placeholder"), get($fieldsInfo, "description"), get($fieldsInfo, "html_class"), $otherHtml, $floating, get($fieldsInfo, "icon"), $title, $ajaxValidation);
    }
    //'editorBasic','editorFull','editorFullAll','editorStandard','editorStandardAll'
    else if(in('editor', $type)){
      //  $defaultValue = get($columnInfos, 'Default', lang, false);
      $functionName = $type."FCK4";
      $functionName($label, $name, $defaultValue, get($fieldsInfo, "html_id"), 10, 10, get($fieldsInfo, "description"), get($fieldsInfo, "icon"), $title); //TODO METTRE $ajaxValidation
    }
    else if($type == 'select'){
      $data = []; $idAndLabelInfos = "";
      //$realName = $columnInfos['Field'];
      $dependance = [];
      if( $defaultValue && ($dependance = get($columnInfos, "dependance")) ){
        if( ! isset($dependance[1]) )
          throw new Exception("Pour le champs $realName qui dépend de la table $dependance[0], il faut préciser le champs !", 1); //TODO traduire en anglais
        if(isset($dependance[2]))
          $whereAssoArray[$dependance[1]] = $dependance[2];
        else
          unset($whereAssoArray[$dependance[1]]);
      }

      // $otherName = ""; // "id_age->age"
       //les infos dans $otherTab sont forcément associatifs
      // $oneOtherTabInfo = getWhereKeyStartsWith($otherTab, $realName, true); //New 10/01/2021
      if($oneOtherTabInfo){
      /* if(isset($otherTab[$realName]) or in($realName, $otherTab) 
          or ($otherName = getKeyFromPartOfKeyOrValue($realName."->", $otherTab))){ */ //["age", "id_age->age", "sexe"=>[], "id_sexe->sexe2"=>[], "sport"=>[id, libelle, prix, heure], ...]
       
        // $otherName = $oneOtherTabInfo[0];
        // $oneOtherTab = $oneOtherTabInfo[0];

        $data = (array) $this->get($otherTabName, $whereAssoArray, $orderBy, $limit);

        /* if( isset($otherTab[$otherName]) and ! empty($otherTab[$otherName]) ) */
        if(notEmpty($oneOtherTab))
          $idAndLabelInfos = $otherTab[$otherName];
        else if(notEmpty($data))
          $idAndLabelInfos = getKeys(first($data));
      }
      if(empty($data) && $this->isExisteTable($realName)){
        $data = (array) $this->get($realName, $whereAssoArray, $orderBy, $limit);
        $idAndLabelInfos = ["id", "libelle"];
      }
      if(empty($data) && empty($whereAssoArray)){
        $data = trimPlus(toArray(between($columnInfos['Type'], "(", ")")), "'");
        $data = changeKeys($data, $data);
      }
      if( ($selectFirstOption = get($fieldsInfo, "select_first_option")) ){
        $selectFirstOption = toArray($selectFirstOption);
        $tmp = [];
        $i = 0;
        foreach ($idAndLabelInfos as $value) {
          $tmp[$value] = get($selectFirstOption, $i);
          $i ++;          
        }
        $data = array_merge([$tmp], $data);
      }
      // $defaultValue = get($columnInfos, 'Default', lang, false);
      $options = options($data, $name, $idAndLabelInfos, $defaultValue);

      //, $fieldsInfo["placeholder"], $fieldsInfo["description"]
      select($label, $name, $options, get($fieldsInfo, "html_id"), get($fieldsInfo, "description"), get($fieldsInfo, "other_class"), $otherHtml, $floating, get($fieldsInfo, "icon"), $title);
    }
    else if($type == 'typeahead'){
      $otherTabName;
      
      // $oneOtherTabInfo = getWhereKeyStartsWith($otherTab, $realName, true); //New 10/01/2021
    
      $otherTabInfos = $this->getTablesInfo("", $otherTabName);

      $typeaheadSearchFields = trimPlus(get($otherTabInfos, "typeahead_search_fields"));
      $typeaheadDisplay = trimPlus(get($otherTabInfos, "typeahead_display"));
      $typeaheadTempSugg  = evalStrContainingFct(trimPlus(get($otherTabInfos, "typeahead_temp_sugg")));
      $typeaheadTempEmpty = evalStrContainingFct(trimPlus(get($otherTabInfos, "typeahead_temp_empty", lang, "", false)));
      $searchFields = "";
      if($typeaheadSearchFields == "_all_")
        $searchFields = "";
      else if(in("-", $typeaheadSearchFields)){
        $notInSearchFields = toArray(trimPlus(last($typeaheadSearchFields, "-")));
        $searchFields = $this->getTableInfos($otherTabName, "", "Field");
        //$searchFields = trimPlus(str_replace($notInSearchFields, "", implode(', ', $searchFields)));
        unsetValues($searchFields, $notInSearchFields);
        $searchFields = implode(', ', $searchFields);
        // $notInSearchFields 
      }
      else if($typeaheadSearchFields)        
        $searchFields = $typeaheadSearchFields;

      //$tableFields = $this->getTableInfos($realName);
      $templates['empty'] = $typeaheadTempEmpty;
      $templates['suggestion'] = $typeaheadTempSugg;
      $display = $typeaheadDisplay ? $typeaheadDisplay : implode(",", $otherTab[$otherName]);
      if(empty2($this->table))
        $this->table = $ctrl->getModel();
      typeahead(route("typeahead"), $templates, $display, $name, $label.msgs['typeaheadSearchSuffix'], "", $otherTabName, $searchFields, $defaultValue, $ctrl);
      
      //typeahead_search_fields, typeahead_temp_sugg, typeahead_temp_empty
    }
    else{
      $idAndLabelInfos = $otherTab; //[age, sexe=>[], id_sport->sport=>[id, libelle, prix, heure], ...]
      $defaultValue = $defaultValue0;
      $data = $defaultValue; //ex. [id=>1,libelle=>Karate,prix=>10000,heure=>12:00]
      if(is_object($data))
        $data = (array) $data;
      if($data == "" or empty($data)){
        $defaultValue = get($columnInfos, 'Default', lang, "", false);
        $label = get($fieldsInfo, "label", lang, "", false);
      }
      else if(isset($idAndLabelInfos) and ! empty($idAndLabelInfos)){     
        $defaultValue = get($data, first($idAndLabelInfos));
        $label = labelBuilder($data, $idAndLabelInfos);
        if($label == "")
          $label = $defaultValue;
      }else{
        $label = $data;
      }
      // if(($tmp = get($label, lang))) // TODO à supprimer
      //   $label = $tmp;

      $radioCheckDefault = get($columnInfos, 'Default', lang, "", false);
      input($type, $label, $name, $defaultValue, get($fieldsInfo, "html_id"), get($fieldsInfo, "placeholder"), get($fieldsInfo, "description"), get($fieldsInfo, "other_class"), $otherHtml, $floating, get($fieldsInfo, "icon"), $title, $radioCheckDefault, $ajaxValidation);
      if($type == "password" && in(routeName(), passwordConfirRoutes)){ //TODO use of constant fields_info !
        $fieldsInfo0 = $fieldsInfo;
        $fieldsInfo = $this->first("fields_info", ["name"=>"password_confirmation"]);
        if( ! notEmpty($fieldsInfo)){ //TODO message redondante
          if(devMode)
            notice("Error : For using this method (oneFormFiel()) for colomn <b>password_confirmation</b>. You must configure fields_info in the database. You can do help(fields_info)");
          else
            throw new Exception("Error : For using this method (oneFormFiel()) for colomn <b>password_confirmation</b>. You must configure fields_info in the database. You can do help(fields_info)", 1);
        }else
          input($type, getLabel($fieldsInfo), "password_confirmation", $defaultValue, get($fieldsInfo, "html_id"), get($fieldsInfo, "placeholder"), get($fieldsInfo, "description"), get($fieldsInfo, "other_class"), $otherHtml, $floating, get($fieldsInfo, "icon"), $title, $radioCheckDefault, $ajaxValidation);
        $fieldsInfo = $fieldsInfo0;
      }
    }
  }else{
    if(devMode)
      notice("Error : For using this method (oneFormFiel()) for colomn <b>$realName</b>. You must configure fields_info in the database. You can do help(fields_info)");
    else
      throw new Exception("Error : For using this method (oneFormFiel()) for colomn <b>$realName</b>. You must configure fields_info in the database. You can do help(fields_info)", 1);
  }
  return ob_get_clean();
}

public function formFieldFromTablesInfo($fieldName, $whereAssoArray = [], $number ="", $idOrOldInfoAssArr = "", $flotting = true, $ajax_validation = "", $fieldSuffixe = "", Controller $ctrl = null){    
    $tableInfo = $this->tableInfo;
    //$tableInfo = $this->tableInfo($tableName);
    //$tableInfo = $this->first("tables_info", ["name"=>$tableName]);

    $otherTab = toArrayOfArray(get($tableInfo, "other_tab"));
    $onlyWithOrwithoutFields = toArray(get($tableInfo, "field_not_to_show_list"));
    $withoutFields = true;

    return $this->generFormFromDbTab('', $otherTab, 4, $number, 
        $onlyWithOrwithoutFields, $withoutFields, $flotting, $idOrOldInfoAssArr, $fieldName, $whereAssoArray, $ajax_validation, $fieldSuffixe, $ctrl);
}
public function formField($tableName="", $idOrOldInfoAssArr = "", $number ="", $whereAssoArray = [], $otherTab = [], $onlyWithOrwithoutFields = ["id"], $withoutFields = true, $flotting = true, $ajax_validation = "", $fieldSuffixe = "", Controller $ctrl = null){
    
    return $this->generFormFromDbTab($tableName, $otherTab, 4, $number, 
        $onlyWithOrwithoutFields, $withoutFields, $flotting, $idOrOldInfoAssArr, true, $whereAssoArray, $ajax_validation, $fieldSuffixe, $ctrl);
}
/** Exemple of otherTab : [sport, sexe=>[], tranche_age=>[id, libelle, prix, heure], ...] or [..., tranche_age=>[id, libelle], ...] or [..., tranche_age=>[libelle, id], ...] ... Dans otherTab on met les compléments de détails pour les champs dans tableName. Par exemple dans la table dont le nom est tableName, il y a la colonne tranche_age qui contient les id des tranche_age. Les détails sur les tranches d'age se trovant dans la table tranche_age avec les colonnes : id, libelle, prix et heure. Si on met dans otherTab juste tranche_age sans les détails, alors dans le formulaire, la value correspondra à la première colone, le label à la deuxième, le reste seront mis entre parenthèse à coté du label. Pour changer cette ordre peut être changer dans les otherTab. */
public function generFormFromDbTab($tableName="", $otherTab = [], $colNum = 4, $number ="", $onlyWithOrwithoutFields = ["id"], $withoutFields = true, $flotting = true, $idOrOldInfoAssArr = "", $oneFormFieldOnly = "", $whereAssoArray = [], $ajaxValidationStyle = "", $fieldSuffixe = "", Controller $ctrl = null, $withValidRules = [], $withValidRulesDbTab = []){
      
  if(empty2($tableName))
    $tableName = $this->getTable();

  $dbTableInfos = $this->getTableInfos($tableName);

  $addCreateInfos2 = [];
  $arg = get($_GET, "arg");
  if( empty2($idOrOldInfoAssArr) && notEmpty($arg)){
    $sep = " ";
    $addCreateInfos = $this->getTablesInfo("add_create_infos", $tableName);
    if( notEmpty($addCreateInfos)){
      foreach(addCreateSeparators as $value) {
        if(in($value, $addCreateInfos)){
          $sep = $value;
          break;
        }
      }
      $addCreateInfos = toArray($addCreateInfos, $sep);
    }else{
      $addCreateInfos = getKeys($dbTableInfos);        
    }
      
    $idFieldName = tern($this->getTablesInfo("id_field_name", $tableName), "id");
    unsetValues($addCreateInfos, $idFieldName);

    $arg = toArray($arg, $sep);
    $min = min(count($addCreateInfos), count($arg));
    $i = 0;
    foreach ($addCreateInfos as $value) {
      $addCreateInfos2[$value] = $arg[$i++];
      if($i >= $min) break;
    }
  }

  if(! $oneFormFieldOnly)
    ajaxFormValidator($ajaxValidationStyle, $number);
  
	// get($tableInfo, "ajax_validation"));

  $dbTableInfos0 = [];
  foreach($dbTableInfos as $key => $value){ //TODO à mettre peut etre dans la prochaine foreach($dbTableInfos...
    $fieldsInfoTmp = $this->first("fields_info", ["name"=>$key]);   
    if(($dependanceField0 = get($fieldsInfoTmp, "dependance_field"))){
      $dependanceField0 = toArray($dependanceField0);
      $dependanceField = toArray($dependanceField0[0], ":");
      $tmpField = $dependanceField0[0];
      $dbTableInfos0[$tmpField] = $value;
      $dbTableInfos0[$tmpField]["Field"] = $dependanceField[0];
      $dbTableInfos0[$tmpField]["isDependance"] = get($dependanceField, 1, "", true);
        
      $onchange = tern(get($dependanceField0, 1), 'onchange');
      if($onchange == 'onchange'){
        $cryptor = cryptor(get_class($this));
        $otherHtml = "$onchange=\"ajaxDependance({'id': $(this).val(), 'targetField':'$key', 'parentCol': '$dependanceField[1]', 'mdl':'$cryptor'}, 'post', '".csrfToken()."')\"";
      }else{
        unset($dependanceField0[0]);
        if(notEmpty($dependanceField0))
          $otherHtml = implode(", ", $dependanceField0);
      }
      $dbTableInfos0[$tmpField]["otherHtml"] = $otherHtml;

      $value["dependance"] = $dependanceField; // [table, field]
    }
    $dbTableInfos0[$key] = $value;
  }
  $dbTableInfos = $dbTableInfos0;

  if(is_string($otherTab)){
    $otherTab = toArray($otherTab);
  }
  
  foreach ($otherTab as $key => $value) {
    $mainTabField = first($key, "->");
    if( ! isset($dbTableInfos[$mainTabField]))
      throw new Exception("Error in <b>tables_info</b> of the db : Unknown column <b>$mainTabField</b> in fields list of <b>$tableName</b> table !"); //TODO généraliser tables_info et internationaliser
    
    if(! is_array($value))
      $otherTab[$key] = toArray($value);

    if(notIn("id", $otherTab[$key]) &&
        get($this->getTableInfos(last($key, "->")), "id") &&
        $this->ruleTypeFromDb(first(get($dbTableInfos, $mainTabField, "Type"), "(")) == 'integer'
      )
      $otherTab[$key] = array_merge(["id"], $otherTab[$key]);
  }

  if(is_string($onlyWithOrwithoutFields))
    $onlyWithOrwithoutFields = toArray($onlyWithOrwithoutFields);

  $colNum = max($colNum, 1); 

  $pamCheckboxInfos_nearJson = "";
  
  // sd($dbTableInfos);
  $colNumI = $colNum; // 4
  $i = 0; $i2 = 1;
  $rowOpenNum = 0; $fieldsetOpenNum = 0; $openNum = 0;

  $colNum = intval(12/$colNum); // 3 // TODO prendre en compte l'aspect centré pour les 5, 7, 8, 9, 10 et 11 pour valeur initiale de $colNum. use peut etre les ofset
  ob_start();
  if(notEmpty($idOrOldInfoAssArr)){
    if(is_array($idOrOldInfoAssArr))
      $oldInfo = $idOrOldInfoAssArr;
    else{
      $oldInfo = $this->get_obj($tableName, $idOrOldInfoAssArr)[0];
      echo "<input type='hidden' name='id$number' value='$idOrOldInfoAssArr'>";      
    }
    // see(getKeys($this->getTableInfos($tableName)));
    // sd($oldInfo);
    foreach ($oldInfo as $field => $value) {  //TODO ceci peut peut etre etre aussi dans la meme boucle que foreach($dbTableInfos as $columnInfos)
      if(isset($dbTableInfos[$field])){ //service
        $dbTableInfos[$field]["Default"] = str_replace('"', '&quot;', $value);
        if( ($dependance = get($dbTableInfos[$field], "dependance")) ){
          if( ! isset($dependance[1]) )
            throw new Exception("Pour le champs $field qui dépend de la table $dependance[0], 
            il faut préciser le champs !", 1); //TODO traduire en anglais
          
          $dependanceTable = $dependance[0];
          $dependanceField = $dependance[1];
          $dependanceTableField = $dependanceTable.":".$dependanceField;
          $dbTableInfos[$dependanceTableField]["Field"] = $dependanceTable;
          $dbTableInfos[$dependanceTableField]["Default"] = 
            $this->isExiste($field) ?
              $this->first_obj($field, $value)->$dependanceField :
              $this->first_obj($dependanceTable, $value)->$dependanceField;

          $dbTableInfos[$field]["dependance"][2] = $dbTableInfos[$dependanceTableField]["Default"];
        }
      }
    }
  }

  div('class="row"'); $rowOpenNum = ++$openNum;
    foreach ($dbTableInfos as $columnInfos) {
      $name = $columnInfos['Field'];
      if(($addCreate = get($addCreateInfos2, $columnInfos["Field"])))
        $columnInfos['Default'] = $addCreate;
      
      if($oneFormFieldOnly && $name != $oneFormFieldOnly)
        continue;
      $openNum ++;
      
      $inWithOrwithout = in($name, $onlyWithOrwithoutFields);
      if($withoutFields && $inWithOrwithout) // without and it is in the list
        continue;
      else if(!$withoutFields && ! $inWithOrwithout) // with only and it is not in the list
        continue;
      if($i == $colNumI){
        if($rowOpenNum >= $fieldsetOpenNum){
          _div();  div('class="row"'); // Pour la gestion des row, peut etre mettre un nouveau à chaque fois qu'on a une somme de md = à 12 ...
          $i = 0; $i2 = 1;
          $rowOpenNum = $openNum;
        }else{
          if($i2 % $i == 0){
            echo "<br>";
          }
          $i2 ++; $i --;
        }
      }
      $i++;
      $fieldsInfo = $this->first("fields_info", ["name in"=>$tableName.".".$name.",".$name]); //label, htlm_type,
      if(in("start", get($fieldsInfo, 'fieldset'))){ //TODO mettre fieldset configurable
        $fieldset_legend = get($fieldsInfo, 'fieldset_legend', lang, "", false);
        $fieldsetOpenNum = 1 + max($openNum, $fieldsetOpenNum);//TODO style du fieldsef
        $a = 12;
        
        echo '<fieldset '.get($fieldsInfo, 'fieldset_other'). '>
          <div class="row">';
        if($fieldset_legend){
          echo '<legend class="col-form-label col-md-2 pt-0 pr-0 pl-1">'.$fieldset_legend.'</legend>';
          $a = 10;
        }
        echo'<div class="col-md-'.$a.'"><div class="row">';
      }

      $sqlType = first($columnInfos['Type'], "(");
      $type = $this->getHtmlType($sqlType, get($fieldsInfo,'htlm_type'));
      $rule = get($fieldsInfo, 'php_js_rule');
      if(in("password", $name) && notIn("regex", $rule)){
        if(passwordRegex){
          if($rule)
            $rule .= "|";
          $rule .= "regex:".passwordRegex;
        }
      }
      if(get($columnInfos,'isDependance'))
        guarded($name.$number);
      formRules($name.$number, $rule, $type != "file");
      
      if(in($type, ["radio", "checkbox"])){ // set ou enum('jeune','adult','vieu')
        
        $data = []; $idAndLabelInfos = "";

        $otherName = "";
        if(isset($otherTab[$name]) or in($name, $otherTab) 
            or ($otherName = getKeyFromPartOfKeyOrValue($name."->", $otherTab))){ //[age, id_age->age, sexe=>[], id_sexe->sexe2=>[], sport=>[id, libelle, prix, heure], ...]
          if( ! $otherName )
            $otherName = $name;

          $otherTabName = last($otherName, "->");

          $data = (array)$this->get_obj($otherTabName);//ex. sport [0=>[id=>1,libelle=>Karate,prix=>10000,heure=>12:00], 1...]
          if(isset($otherTab[$otherName]) and notEmpty($otherTab[$otherName]))
            $idAndLabelInfos = $otherTab[$otherName];
          else if(notEmpty($data))
            $idAndLabelInfos = getKeys(first($data));
        }
        if(empty($data) and in($sqlType, ['set', 'enum'])){
          $data = toArray(between($columnInfos['Type'], "(", ")"));
          $data = trimPlus($data, "'");
        }
        if(empty($data)){
          throw new Exception("Error : For using column <b>$name</b> as <b>radio</b> or <b>checkbox</b> type in <b>fields_info</b> table of the database, you must have :".brOb().nbspOb(4)."1) respectively <b>enum</b> or <b>set</b> in <b>$tableName</b> table of the database or ".brOb().nbspOb(4)."2) configure <b>otherTab</b> argument of <b>generFormFromDbTab()</b>. For more informations, you can do <b>help(generFormFromDbTab)</b>.");
        }
        $nData = count($data);
        //$i --;
        //$j = 0;
        //div('class="col-md-'.$colNum.'"');
       // _div();
        div('class="col-md-12"'); 
          div('class="row"',
            divOb('class="col-md-'. min($nData * $colNum, 12).'" style="margin-bottom:.4rem"', 
              tagOb("div", get($fieldsInfo, 'label', lang, "", false))
            )
          );
          $i = 0;

          div('class="row" style="margin-left:.25rem"');
            $columnInfosField = $columnInfos["Field"];
            $pamCheckboxInfos_nearJsonTmp = "";
            //sd($data);
            for ($k=0; $k < $nData; $k++) { //$data
              if($i == $colNumI){  _div();  div('class="row" style="margin-left:.25rem"');  $i = 0;  }
              $i ++;
              //$j ++;
              if($type == "checkbox"){
                $columnInfos["Field"] = $columnInfosField.$k;
                $pamCheckboxInfos_nearJsonTmp .= guillemet.escaper($columnInfosField, ",").$k.guillemet.',';
              }
              $colNum_ = ($fieldsInfo['col_number']) ? $fieldsInfo['col_number'] : $colNum;

              $idTmp = "block0".ternary(($idTmp = get($fieldsInfo, "html_id")), $idTmp, first($columnInfos['Field'], "[").$number);
              
              $col = $ctrl->createColEqualColSm ? 'col-'.($colNum_ * 2)." " : "";
              
              div('class="'.$col.'col-sm-'.($colNum_ * 2).' col-md-'.$colNum_.'" id="'.$idTmp.'"');
                     
                $oneFormField = $this->oneFormField($columnInfos, $fieldsInfo, $type, $idAndLabelInfos, $data[$k], $flotting, $number, $whereAssoArray, "", $fieldSuffixe, $ctrl);
              
                if($oneFormFieldOnly){
                  ob_get_clean();
                  return $oneFormField;
                }else
                  echo $oneFormField; 

              _div();            
              /*if($j == 2){
                $i ++; $j = 0;
                if($k < ($nData - 1)){
                  _div();
                  div('class="col-md-'.$colNum.'"');
                }
              }*/
            }
            if($pamCheckboxInfos_nearJsonTmp)
              $pamCheckboxInfos_nearJson .= guillemet.$name.$number.guillemet.':['.trimPlus($pamCheckboxInfos_nearJsonTmp, ',').'],';
          _div();  //div('class="row"'); $i = 0;
        _div();
        
      }
      else{
        $colNum_ = get($fieldsInfo, 'col_number');
        $colNum_ =  notEmpty($colNum_) ? $colNum_ : $colNum;
        $idTmp = "block0".ternary(($idTmp = get($fieldsInfo, "html_id")), $idTmp, first($columnInfos['Field'], "[").$number);

        $col = $ctrl->createColEqualColSm ? 'col-'.($colNum_ * 2)." " : "";
        
        div('class="'.$col.'col-sm-'.($colNum_ * 2).' col-md-'.$colNum_.'" id="'.$idTmp.'"');
                
          $oneFormField = $this->oneFormField($columnInfos, $fieldsInfo, $type, $otherTab, '', $flotting, $number, $whereAssoArray, "", $fieldSuffixe, $ctrl);
        
          if($oneFormFieldOnly){
            ob_get_clean();
            return $oneFormField;
          }else
            echo $oneFormField; 

        _div();
      }
      if($rowOpenNum <= $fieldsetOpenNum and in("end", $fieldsInfo['fieldset'])){
        //sd($rowOpenNum, $fieldsetOpenNum);
        echo "</div></div></div></fieldset>";
        $fieldsetOpenNum --;
        $i2 = 1;
        $fieldsetOpenNum = $rowOpenNum; //TODO fieldset revoir pour des fieldset récursifs
        $i = 0;
      }
    }
  _div();
  if($pamCheckboxInfos_nearJson){
    $pamCheckboxInfos_nearJson = '{'.trimPlus($pamCheckboxInfos_nearJson, ',').'}';
    //see(stringToJson($pamCheckboxInfos_nearJson));
    echo '<input type="hidden" name="pamCheckboxInfos_nearJson'.$number.'" value="'. $pamCheckboxInfos_nearJson.'"/>';
  }
  if(notEmpty($number)){
    formNumberInput($number);			
  }
  return ob_get_clean();
}
public function generFormModifFromDbTab($idOrOldInfoAssArr, $tableName="", $otherTab = [], $colNum = 4, $number ="", $onlyWithOrwithoutFields = ["id"], $withoutFields = true, $flotting = true, $oneFormFieldOnly = "", $whereAssoArray = [], $ajax_validation = "", $fieldSuffixe = "", Controller $ctrl = null, $withValidRules = [], $withValidRulesDbTab = []){
  return $this->generFormFromDbTab($tableName, $otherTab, $colNum, $number, $onlyWithOrwithoutFields, $withoutFields, $flotting, $idOrOldInfoAssArr, $oneFormFieldOnly, $whereAssoArray, $ajax_validation, $fieldSuffixe, $ctrl, $withValidRules, $withValidRulesDbTab);
}

function getTables_obj($withInfos = false){
  $query = $this->db->query("SHOW tables", PDO::FETCH_OBJ);
  $listTables = new stdClass();
  
  while ($row = $query->fetch()){

    $oneTable = first($row);
    if($withInfos)
      $listTables->$oneTable = toObject($this->getTableInfos($oneTable));
    else
      $listTables->$oneTable = $oneTable;
  }
  return $listTables;
}
/*
public function read0($tableName, $id, $otherTab = [], $colNum = 4, $onlyWithOrwithoutFields = ["id"], $withoutFields = true){

  if(is_string($otherTab))
    $otherTab = toArray($otherTab);
  if(is_string($onlyWithOrwithoutFields))
    $onlyWithOrwithoutFields = toArray($onlyWithOrwithoutFields);

  $colNum = max($colNum, 1); 

  $pamCheckboxInfos_nearJson = "";
  
 // sd($dbTableInfos);
  $colNumI = $colNum; // 4
  $i = 0; $i2 = 1;
  $rowOpenNum = 0; $fieldsetOpenNum = 0; $openNum = 0;

  $colNum = intval(12/$colNum); // 3 // TODO prendre en compte l'aspect centré pour les 5, 7, 8, 9, 10 et 11 pour valeur initiale de $colNum. use peut etre les ofset
  ob_start();
  $dbTableInfos = $this->getTableInfos($tableName); 
  if(notEmpty($id)){
    $oldInfo = $this->get_obj($tableName, $id)[0]; //TODO pour quoi table
    foreach ($oldInfo as $field => $value) {
      if(isset($dbTableInfos[$field]))
        $dbTableInfos[$field]["Default"] = $value;
    }
  }
  sd($dbTableInfos);
  div('class="row"'); $rowOpenNum = ++$openNum;
    foreach ($dbTableInfos as $columnInfos) {
      $name = $columnInfos['Field'];
      $openNum ++;

      $inWithOrwithout = in($name, $onlyWithOrwithoutFields);
      if($withoutFields and $inWithOrwithout) // without and it is in the list
        continue;
      else if(!$withoutFields and ! $inWithOrwithout) // with only and it is not in the list
        continue;
      if($i == $colNumI){
        if($rowOpenNum >= $fieldsetOpenNum){
          _div();  div('class="row"'); // Pour la gestion des row, peut etre mettre un nouveau à chaque fois qu'on a une somme de md = à 12 ...
          $i = 0; $i2 = 1;
          $rowOpenNum = $openNum;
        }else{
          if($i2 % $i == 0){
            echo "<br>";
          }
          $i2 ++; $i --;
        }
      }
      $i++;
      $fieldsInfo = $this->first("fields_info", ["name"=>$name]); //label, htlm_type,
      
      /*if(in("start", get($fieldsInfo, 'fieldset'))){ //TODO mettre fieldset configurable
        $fieldset_legend = get($fieldsInfo, 'fieldset_legend', lang, "", false);
        $fieldsetOpenNum = 1 + max($openNum, $fieldsetOpenNum);//TODO style du fieldsef
        echo '<fieldset style="display:inline; width:100%"><div class="row">';
        echo '<legend class="col-form-label col-md-2 pt-0">'.$fieldset_legend.'</legend><div class="col-md-10"><div class="row">';
      }*

      $sqlType = first($columnInfos['Type'], "(");
      $type = $this->getHtmlType($sqlType, get($fieldsInfo,'htlm_type'));

      if(in($type, ["radio", "checkbox"])){ // set ou enum('jeune','adult','vieu')
        
        $data = []; $idAndLabelInfos = "";

        if(isset($otherTab[$name]) or in($name, $otherTab)){//[age, sexe=>[], sport=>[id, libelle, prix, heure], ...]
          $data = (array)$this->get_obj($name);//ex. sport [0=>[id=>1,libelle=>Karate,prix=>10000,heure=>12:00], 1...]
          if(isset($otherTab[$name]) and notEmpty($otherTab[$name]))
            $idAndLabelInfos = $otherTab[$name];
          else if(notEmpty($data))
            $idAndLabelInfos = getKeys(first($data));
        }
        if(empty($data) and in($sqlType, ['set', 'enum'])){
          $data = toArray(between($columnInfos['Type'], "(", ")"));
          $data = trimPlus($data, "'");
        }
        $nData = count($data);
        //$i --;
        //$j = 0;
        //div('class="col-md-'.$colNum.'"');
       // _div();
        div('class="col-md-12"'); 
          div('class="row"',
            divOb('class="col-md-'. min($nData * $colNum, 12).'" style="margin-bottom:.4rem"', 
              tagOb("div", get($fieldsInfo, 'label', lang, "", false))
            )
          );
          $i = 0;

          div('class="row" style="margin-left:.25rem"');
            $columnInfosField = $columnInfos["Field"];
            $pamCheckboxInfos_nearJsonTmp = "";
            //sd($data);
            for ($k=0; $k < $nData; $k++) { //$data
              if($i == $colNumI){  _div();  div('class="row" style="margin-left:.25rem"');  $i = 0;  }
              $i ++;
              //$j ++;
              if($type == "checkbox"){
                $columnInfos["Field"] = $columnInfosField.$k;
                $pamCheckboxInfos_nearJsonTmp .= guillemet.escaper($columnInfosField, ",").$k.guillemet.',';
              }
              $colNum_ = ($fieldsInfo['col_number']) ? $fieldsInfo['col_number'] : $colNum;
              div('class="col-md-'.$colNum_.'"');
                $this->oneFormField($columnInfos, $fieldsInfo, $type, $idAndLabelInfos, $data[$k], $flotting, $number);
              _div();            
              /*if($j == 2){
                $i ++; $j = 0;
                if($k < ($nData - 1)){
                  _div();
                  div('class="col-md-'.$colNum.'"');
                }
              }*
            }
            if($pamCheckboxInfos_nearJsonTmp)
              $pamCheckboxInfos_nearJson .= guillemet.$name.$number.guillemet.':['.trimPlus($pamCheckboxInfos_nearJsonTmp, ',').'],';
          _div();  //div('class="row"'); $i = 0;
        _div();
        
      }
      else{
        $colNum_ = get($fieldsInfo, 'col_number');
        $colNum_ =  notEmpty($colNum_) ? $colNum_ : $colNum;
        div('class="col-md-'.$colNum_.'"');
          $this->oneFormField($columnInfos, $fieldsInfo, $type, $otherTab, '', $flotting, $number);
        _div();
      }
      if($rowOpenNum <= $fieldsetOpenNum and in("end", $fieldsInfo['fieldset'])){
        sd($rowOpenNum, $fieldsetOpenNum);
        echo "</div></div></div></fieldset>";
        $fieldsetOpenNum --;
        $i2 = 1;
        $fieldsetOpenNum = $rowOpenNum; //TODO fieldset revoir pour des fieldset récursifs
        $i = 0;
      }
    }
  _div();
  if($pamCheckboxInfos_nearJson){
    $pamCheckboxInfos_nearJson = '{'.trimPlus($pamCheckboxInfos_nearJson, ',').'}';
    //see(stringToJson($pamCheckboxInfos_nearJson));
  }
  return ob_get_clean();
}*/

}
?>