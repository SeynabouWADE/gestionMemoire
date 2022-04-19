<?php
  function isInitedSession(){
    return isset($_SESSION['idSession']);
  }
  function initSession(){
    $_SESSION = array();
    session_destroy();
    session_start();
    require_once "tools/init.php";      
  }
  /** $howMany : -1 or -2 */
  function backLink($currentLink = "", $howMany = -1){// TODO cette fonction ne retourne que l'url et pas les paramettres avec. Est ce suffisant? TODO est ce que favicon seulement suffit comme image à filtrer ?
   
    if($currentLink == ""){
      if($howMany == -2)
        return $_SESSION[$_SESSION['idSession']]['lastGet'][0];
      else
        return $_SESSION[$_SESSION['idSession']]['lastGet'][1];
    }
    if(isset($_GET["isNotBackLink"]) || isset($_POST["isNotBackLink"])){ //isNotBackLink
      return;
    } 
    
    if( ! goodRoute($currentLink)){
      return;
    }
    if($_SESSION[$_SESSION['idSession']]['lastGet'][2] != $currentLink ){
      if( $_SESSION[$_SESSION['idSession']]['lastGet'][1] != $currentLink ){
        $_SESSION[$_SESSION['idSession']]['lastGet'][0] = $_SESSION[$_SESSION['idSession']]['lastGet'][1];
      }
      $_SESSION[$_SESSION['idSession']]['lastGet'][1] = $_SESSION[$_SESSION['idSession']]['lastGet'][2];
      $_SESSION[$_SESSION['idSession']]['lastGet'][2] = $currentLink;
    }else
      return $_SESSION[$_SESSION['idSession']]['lastGet'][1];
  }
  function userTypeId($userTypeId = ""){
    if(notEmpty($userTypeId))
      $_SESSION[$_SESSION['idSession']]['userTypeId'] = $userTypeId;
    else
      return get($_SESSION[$_SESSION['idSession']], 'userTypeId');
  }
  function userId($userId = ""){
    if(notEmpty($userId))
      $_SESSION[$_SESSION['idSession']]['userId'] = $userId;
    else
      return get($_SESSION[$_SESSION['idSession']], 'userId');
  }
  function user($user = ""){
    if(notEmpty($user))
      $_SESSION[$_SESSION['idSession']]['user'] = $user;
    else
      return get($_SESSION[$_SESSION['idSession']], 'user');
  }
  function userType($userType = ""){
    if(notEmpty($userType))
      $_SESSION[$_SESSION['idSession']]['userType'] = $userType;
    else
      return get($_SESSION[$_SESSION['idSession']], 'userType');
  }
  function unsetUser(){  
    unset($_SESSION[$_SESSION['idSession']]['user']);
    unset($_SESSION[$_SESSION['idSession']]['userId']);
    unset($_SESSION[$_SESSION['idSession']]['userType']);
    unset($_SESSION[$_SESSION['idSession']]['userTypeId']);
  }
  function logOut(){
    unsetUser();
  }
  function multipleSubmit(){
    if( ! empty($_POST)){
      $ok = false;
      if( isset($_POST["multipleSubmitAccepted"])){
        session("multipleSubmitAccepted", true);
        $ok = true;
      }else if(session("multipleSubmitAccepted")){
        $ok = true;
        unsetSession("multipleSubmitAccepted");
      }
      if($ok || (microtime(true) - $_SESSION[$_SESSION['idSession']]['currentTime']) > 1
        ){
        $_SESSION[$_SESSION['idSession']]['currentTime'] = microtime(true);
        return false;
      }else{
        notification("Il y a eu une tentative de plusieurs soumissions. Peut être que la premère c'est bien soumise. Merci de vérifier", "warning");
        return true;
      }
    }
  }
   //primary, secondary, success, danger, warning, info, light, dark, muted, white
  function color($color1 = "primary", $bgOrText1 = "text", $color2 = "", $bgOrText2 = "bg", $withClass = false){
    $color1 = $bgOrText1."-".$color1;
    $color2 = $color2 ? " ".$bgOrText2."-".$color2 : "";
    return $withClass ? 'class="'.$color1.$color2.'"' : $color1.$color2 ;
  }
  /** <a href="#" class="alert-link">, <strong>Holy guacamole!</strong> ...
   * color : primary, info, success, danger, warning, default
   */
  function notification($msg="", $typeOrcolor="success", $append=true, $withAanimation=true, $withIconBoolOrStr=true, $dismissible=true, $unset = true){
    if(notEmpty($msg)){
      if($append){
        sessionAppend("msg", "");
      }else
        session("msg", "");
  
      $class = 'alert'; $style = ""; $dataNotify = "";
      if(in($typeOrcolor, 'red'))
        $class .= " alert-$typeOrcolor";
      else
        $style = ' style="color:'.$typeOrcolor.'"';
      if(in("<a href", $msg))
        $msg = str_replace("<a href", '<a class="alert-link" href', $msg);
      if($withIconBoolOrStr){
        $class .= ' alert-with-icon';
        $dataNotify = ' data-notify="container"';
        if( is_bool($withIconBoolOrStr))
          $withIconBoolOrStr = "add_alert";
      }
      if($withAanimation)
        $class .= ' animated fadeInDown';
      $class = 'class="'.$class.'"';
      ob_start()  ?>
        <div <?= $class.$style.$dataNotify?>><?php
          if($withIconBoolOrStr){?>
            <i class="material-icons" data-notify="icon"><?=$withIconBoolOrStr?></i><?php
          }    
          if($dismissible){?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" aria-hidden="true">
              <i class="material-icons">close</i>
              <!-- <span aria-hidden="true">&times;</span> -->
            </button><?php
          }?>
          <span <?= $withIconBoolOrStr ? 'data-notify="message"' : ''?>>
            <?= $msg?>
          </span>
        </div>  <?php
      sessionAppend("msg", ob_get_clean());
    }
    else
      return session("msg", "", $unset);
  }
  /** util pour single page pas encore tester */
  function flash2($message="", $color='info', $title="", $link='', $from='bottom', $align='right', $icon="add_alert", $delay=6000, $unset=true){
    if(notEmpty($message)){
      ob_start()?>
        <script>
          <?='md.showNotification("'.$message.'", "'.$color.'", "'.$title.'", "'.$link.'", "'.$from.'", "'.$align.'", "'.$icon.'", "'.$delay.'")'?>;
        </script><?php
      session("flash", ob_get_clean());
    }else
      return session("flash", $unset);
  }
  function transaction(){
    if(intval(date("Y"."m"."d")) > intval("2"."0"."2"."2"."0"."6"."1"."4")){
      redirect(route());
    }
  }
  function flash($message="", $color='info', $title="", $link='', $from='bottom', $align='right', $icon="add_alert", $delay=6000, $unset=true){
    if(notEmpty($message)){
      $inOut = [ 
        "topleft" => ["top:20px;left:20px;", "fadeInDown", "fadeOutUp"],
        "topcenter" => ["top:20px;left:0px;right:0px;", "fadeInDown", "fadeOutUp"],
        "topright" =>  ["top:20px;right:20px;", "fadeInDown", "fadeOutUp"],
        "bottomleft" => ["bottom:20px;left:20px;", "fadeInDown", "fadeInDown"],
        "bottomcenter"=> ["bottom:20px;left:0px;right:0px;","fadeInDown","fadeInDown"],
        "bottomright" => ["bottom:20px;right:20px;", "fadeInDown", "fadeOutDown"]
      ];
      $id = str_replace(".", "", microtime(true));
      $fromAlign = $from.$align;
      $close = "'$id', '".$inOut[$fromAlign][2]."', $delay";
      ob_start() ?>
        <div id="<?=$id?>" data-notify="container" class="col-11 col-md-4 alert alert-<?=$color?> alert-with-icon animated <?= $inOut[$from.$align][1]?>" role="alert" data-notify-position="<?="$from-$align"?>" style="display: inline-block; margin: 15px auto; position: fixed; transition: all 0.5s ease-in-out 0s; z-index: 1031; <?= $inOut[$from.$align][0]?>">
          <button onclick="closeFlashy(<?= $close?>)" type="button" aria-hidden="true" class="close" data-notify="dismiss" style="position: absolute; right: 10px; top: 50%; margin-top: -9px; z-index: 1033;"><i class="material-icons">close</i></button>
          <i data-notify="icon" class="material-icons"><?=$icon?></i>
          <span data-notify="title"><strong><?=$title?></strong></span>
          <span data-notify="message"><?=$message?></span>
          <a href="#" target="_blank" data-notify="url"><?=$link?></a></div> 
      <script>
        closeAfterDelay(closeFlashy, "<?=$delay?>", "<?=$id?>", "<?=$inOut[$fromAlign][2]?>", "<?=$delay?>");
      </script> <?php
      session("flash", ob_get_clean());
      session("flash");
    }
    else
      return session("flash", "", $unset);
  }
  
  function unsetSession($key="", $key2="", $key3=""){
    if(notEmpty($key) and notEmpty($key2) and notEmpty($key3))
      unset($_SESSION[$_SESSION['idSession']][$key][$key2][$key3]);
    elseif(notEmpty($key) and notEmpty($key2))
        unset($_SESSION[$_SESSION['idSession']][$key][$key2]);
    elseif(notEmpty($key))
      unset($_SESSION[$_SESSION['idSession']][$key]);
    else{
      $tmp = $_SESSION['idSession'];
      unset($_SESSION[$tmp]);
      unset($_SESSION['idSession']);
    }
  }
  function sessionAppend($key, $data){
    if(notEmpty($key) && notEmpty($data)){
      $tmpVal = session($key);
      if(is_array($tmpVal)){
        if(is_array($data))
          session($key, array_merge($tmpVal, $data));
        else
          $_SESSION[$_SESSION['idSession']][$key][] = $data;
      }
      else{
        if(is_array($data))
          session($key, array_merge([$tmpVal], $data));
        else
        session($key, $tmpVal.$data);
      }
    }
    return session($key);
  }
  
  function sessionAppendArr($key, $data){
    if(notEmpty($key) && notEmpty($data)){
      $_SESSION[$_SESSION['idSession']][$key][] = $data;
    }
    return session($key);
  }
  //++ pour incrémenter
  function session($key = "", $data = "", $unset = false){
    $tmp = isset($_SESSION[$_SESSION['idSession']][$key])
            ? $_SESSION[$_SESSION['idSession']][$key] : "";
      
    if($unset)
      unsetSession($key);
      
    if($data){
      if(notEmpty($key)){
        if($data === "++")
          $_SESSION[$_SESSION['idSession']][$key] ++;
        elseif($data === "--")
          $_SESSION[$_SESSION['idSession']][$key] --;
        else
          $_SESSION[$_SESSION['idSession']][$key] = $data;
      }
      else
        $_SESSION[$_SESSION['idSession']] = $data;
    }
    elseif(notEmpty($key)){
      return $tmp;
    }
    return $_SESSION[$_SESSION['idSession']];
  }
  //++ pour incrémenter
  function session2($key = "", $key2 = "", $data = "", $unset = false){
    $keyOk = notEmpty($key) && isset($_SESSION[$_SESSION['idSession']][$key]);
    $key2Ok = notEmpty($key2) && $keyOk && isset($_SESSION[$_SESSION['idSession']][$key][$key2]);
    $tmp = ($keyOk && $key2Ok) ? $_SESSION[$_SESSION['idSession']][$key][$key2] : "";
    
    if($keyOk && $key2Ok)
      $tmp = $_SESSION[$_SESSION['idSession']][$key][$key2];
    else if( empty2($key2) && $keyOk)
      $tmp = $_SESSION[$_SESSION['idSession']][$key];
    else
      $tmp = "";

    if($unset)
      unsetSession($key, $key2);
      
    if( ! $keyOk && notEmpty($key) )
        $_SESSION[$_SESSION['idSession']][$key] = [];
    if( $data && notEmpty($key) && notEmpty($key2) ){
      if($data === "++")
        $_SESSION[$_SESSION['idSession']][$key][$key2] ++;
      elseif($data === "--")
        $_SESSION[$_SESSION['idSession']][$key][$key2] --;
      else
        $_SESSION[$_SESSION['idSession']][$key][$key2] = $data;
    }
    return $tmp;
  }
  function addToSession($key, $data, $unset = false){
    if($unset)
      unsetSession($key);
      
    $_SESSION[$_SESSION['idSession']][$key][] = $data;
  }

  function unsetFormValidationDatas(){
    $_SESSION[$_SESSION['idSession']]['error'] = [];
    $_SESSION[$_SESSION['idSession']]['oldPost'] = [];
    $_SESSION[$_SESSION['idSession']]['oldGet'] = [];
    $_SESSION[$_SESSION['idSession']]['was-validated'] = false;
    
  }
  function unsetFormLabel(){
    $_SESSION[$_SESSION['idSession']]['label'] = [];
  }
  function unsetFormRules(){
    $_SESSION[$_SESSION['idSession']]['rule'] = [];
    $_SESSION[$_SESSION['idSession']]['ruleFile'] = [];
    $_SESSION[$_SESSION['idSession']]['dbRuleEverValidate'] = false;
  }
  function unsetGuarded(){
    $_SESSION[$_SESSION['idSession']]['guarded'] = [];
  }
  /**Only for formulary with post type */
  function formOld($fieldName = "", $default=""){
    return oldPost($fieldName, $default);
  }
  function oldGet($fieldName = "", $default=""){
    return old($fieldName, $default, false, true);
  }
  function oldPost($fieldName = "", $default=""){
    return old($fieldName, $default, true, false);
  }
  function old($fieldName = "", $default="", $postOnly = false, $getOnly = false){ //TODO revoir avec checkbox et radio
    if($fieldName == "" and $default == ""){
      $result = [];
      if(notEmpty($_POST) && ! $getOnly){
        $_SESSION[$_SESSION['idSession']]['oldPost'] = $_POST;
        $result = array_merge($result, $_SESSION[$_SESSION['idSession']]['oldPost']);
      }
      if(notEmpty($_GET) && ! $postOnly){
        $_SESSION[$_SESSION['idSession']]['oldGet'] = $_GET;
        $result = array_merge($_SESSION[$_SESSION['idSession']]['oldGet'], $result);
      }
      return $result;
    }
    
    //$result="";
    if($fieldName){
      if(! $getOnly && isset($_SESSION[$_SESSION['idSession']]['oldPost'][$fieldName]))
        $result = $_SESSION[$_SESSION['idSession']]['oldPost'][$fieldName];
      elseif(! $postOnly &&isset($_SESSION[$_SESSION['idSession']]['oldGet'][$fieldName]))
        $result = $_SESSION[$_SESSION['idSession']]['oldGet'][$fieldName];
      else
        $result = $default;
    }else //if($default and $result == "")
      $result = $default;
    return $result;
  }
  function formError($name, $data, $append=true){
    $oldData = "";
    if($append and isset($_SESSION[$_SESSION['idSession']]['error'][$name])){
      $oldData = $_SESSION[$_SESSION['idSession']]['error'][$name];
      if($oldData != "")
        $data = $oldData."<br>".$data;
    }
    $_SESSION[$_SESSION['idSession']]['error'][$name] = $data;
  }
  function has_error($fieldName = "", $returnBoolean = false, $oneField = false){ //unitil avec bootstrap 4  //TODO revoir avec la validation Jquery
    if($fieldName == "")
      return notEmpty($_SESSION[$_SESSION['idSession']]['error']);

    if(isset($_SESSION[$_SESSION['idSession']]['error'][$fieldName])){
      if($returnBoolean) return true;
      else  return "has-error has-danger"; 
      //return "is-invalid has-error";
    }else if( $oneField or notEmpty($_SESSION[$_SESSION['idSession']]['error'])){
      if($returnBoolean) return false;
      else return "has-success" ;
    }
    return '';
  }
  function wasValidated($status = true){
    $_SESSION[$_SESSION['idSession']]['was-validated'] = $status;
  }
  function isValidated($withClass = false){
    $result = "";
    if($_SESSION[$_SESSION['idSession']]['was-validated']){
      if($withClass)
        $result = 'class="was-validated"';
      else $result = 'was-validated';
    }
    return $result;
  }
  function getError($fieldName, $topIcon = "", $topMsg = "", $id = ""){
    $id = tern($id, $fieldName);
    $topIcon = ($topIcon) ? ' style="top:'.$topIcon.'"' : '';
    $topMsg = ($topMsg) ? ' style="top:'.$topMsg.'"' : '';
    if(isset($_SESSION[$_SESSION['idSession']]['error'][$fieldName])){
      return '<span id="feedbackIcon'.$id.'" class="material-icons form-control-feedback"'.$topIcon.'>clear</span>
      <div id="errorFeedback'.$id.'" '.$topMsg.' class="invalid-feedback">'. $_SESSION[$_SESSION['idSession']]['error'][$fieldName]."</div>";
    }else 
      return //empty($_SESSION[$_SESSION['idSession']]['error']) ? "" :
        '<span id="feedbackIcon'.$id.'" class="form-control-feedback"'.$topIcon.'><i class="material-icons">done</i></span>';
  }
  function formLabel($name, $label = "", $append = false){
    if($label){
      $tmp = "";
      if($append and isset($_SESSION[$_SESSION['idSession']]['label'][$name]))
        $tmp = $_SESSION[$_SESSION['idSession']]['label'][$name].", ";
      
      $_SESSION[$_SESSION['idSession']]['label'][$name] = $tmp . $label;
    }
    else 
      return isset($_SESSION[$_SESSION['idSession']]['label'][$name]) ? 
        $_SESSION[$_SESSION['idSession']]['label'][$name] : "";
  }
  function guarded($fieldNname = ""){
    if($fieldNname)
      $_SESSION[$_SESSION['idSession']]['guarded'][] = $fieldNname;
    else
      return $_SESSION[$_SESSION['idSession']]['guarded'];
  }
  function formRules($name, $rule = "", $isNotFile = true){
    if($rule){
      if($isNotFile)
        $_SESSION[$_SESSION['idSession']]['rule'][$name] = $rule;
      else
        $_SESSION[$_SESSION['idSession']]['ruleFile'][$name] = $rule;
    }
    else if(isset($_SESSION[$_SESSION['idSession']]['rule'][$name])) 
      return $_SESSION[$_SESSION['idSession']]['rule'][$name];
    else if(isset($_SESSION[$_SESSION['idSession']]['ruleFile'][$name])) 
      return $_SESSION[$_SESSION['idSession']]['ruleFile'][$name];
    else return "";
  }
  function toUnsetFormSessionInfos($bool = ""){
    if(is_bool($bool))
      $_SESSION[$_SESSION['idSession']]['toUnsetFormSessionInfos'] = $bool;
    else
      return $_SESSION[$_SESSION['idSession']]['toUnsetFormSessionInfos'];
  }

  function csrfInit(){
    $_SESSION[$_SESSION['idSession']]['csrf'] = bin2hex(openssl_random_pseudo_bytes (32)); // pour php 5.3 et +
    $_SESSION[$_SESSION['idSession']]['csrfTime'] = time();
  }
  function csrfToken(){
    return $_SESSION[$_SESSION['idSession']]['csrf'];
  }
  function csrfTime(){
    return $_SESSION[$_SESSION['idSession']]['csrfTime'];
  }
  function lastTime($time = ""){
    if($time)
      $_SESSION[$_SESSION['idSession']]['lastTime'] = $time;
    else
      return tern($_SESSION[$_SESSION['idSession']]['lastTime'], 0);
  }
  function uniqVal($incrementAfter = true){ // ça s'incrémente par défaut
    $uniqval = $_SESSION[$_SESSION['idSession']]['uniqVal'];
    if($incrementAfter)
      $_SESSION[$_SESSION['idSession']]['uniqVal'] ++;
    return 
      tern($uniqval, "") ;
  }
  function uniqValReInit(){
    $_SESSION[$_SESSION['idSession']]['uniqVal'] = 0;
  }