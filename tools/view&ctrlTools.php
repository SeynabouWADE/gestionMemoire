<?php
require_once("./tools/config_tools.php");
require_once("./lang/".lang."/messages.php");

require_once("./tools/sessionTools.php");
require_once("./tools/".formFunctions);
require_once("./tools/diaFunctions.php");

class Ob{ 
    public function __call($method, $args){
		$tmp = "";
		if(in("$", get($args, 0))){
			throw new Exception("The argument cannot contains <b>$</b> symbol : <b>$args[0] ...</b> for calling <b>$method</b>(...) method", 1);
		}
		ob_start();
		if(count($args) == 1 && in(",",  $args[0]))
			$args = toArray($args[0]);
		// foreach ($args as $key => $value) {
		// 	$value = trimPlus($value, "'"); 
		// 	$args[$key] = trimPlus($value, '"');
		// }

		switch (count($args)) {
			case '0' : $tmp = $method();break;
			case '1' : $tmp = $method($args[0]);break;
			case '2' : $tmp = $method($args[0], $args[1]);break;
			case '3' : $tmp = $method($args[0], $args[1], $args[2]);break;
			case '4' : $tmp = $method($args[0], $args[1], $args[2], $args[3]);break;
			case '5' : $tmp = $method($args[0], $args[1], $args[2], $args[3], $args[4]);break;
			case '6' : $tmp = $method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);break;
			case '7' : $tmp = $method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);break;
			case '8' : $tmp = $method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);break;
			case '9' : $tmp = $method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]);break;
			case '10': $tmp = $method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9]);break;
			case '11': $tmp = $method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9], $args[10]);break;
			case '12': $tmp = $method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9], $args[10], $args[11]);break;
			case '13': $tmp = $method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9], $args[10], $args[11], $args[12]);break;
			case '14': $tmp = $method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9], $args[10], $args[11], $args[12], $args[13]);break;
			case '15': $tmp = $method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9], $args[10], $args[11], $args[12], $args[13], $args[14]);break;
			case '16': $tmp = $method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9], $args[10], $args[11], $args[12], $args[13], $args[14], $args[15]);break;
			default:
				throw new Exception("The <b>$method() is called with more than ".count($args)." arguments. See the Ob class.", 1);
			break;
		}
		return ob_get_clean().$tmp;
    }
}
function ob(){
	return new Ob();
}
function pwd(){
	seec(basename(dirname($_SERVER['SCRIPT_NAME'])));
}
function help($string){ //validation, between
	//$ctrl = controller("", "help");
	$helpInfos = getHelpMsg();
	if( ! $string )
		sd($helpInfos);

	$values = preg_split("#\W#", trimPlus(str_replace(",", " ", $string)));

	$resultTabKey = [];     //[ [keys, helpInfos], ... ]
	//$resultTabReturn = [];  //[ [keys, helpInfos], ... ]
	$resultTabDoc = [];     //[ [keys, helpInfos], ... ]
	$whereKeysStr = implode(" ", getKeys($helpInfos));

	$wholeValues = "";
	foreach ($values as $oneValue) {
		// Recherche sur les clés de helpInfos

		if(in($oneValue, $whereKeysStr)){ 

			$n = 1;
			do{
				$oneValueWhole = getWholeWord($oneValue, $whereKeysStr, $n ++);

				if(isset($helpInfos[$oneValueWhole])){
					if(dim($helpInfos[$oneValueWhole]) == 2){ // cas validate
						$flag = false;
						$validationKeysStr = implode(" ", getKeys($helpInfos[$oneValueWhole]));
						foreach ($values as $oneValue2) {
							$oneValueWhole2 = getWholeWord($oneValue2, $validationKeysStr);
							if(isset($helpInfos[$oneValueWhole][$oneValueWhole2])){ //validate, between,
								$flag = true;
								$resultTabKey["$oneValue:$oneValueWhole, $oneValue2:$oneValueWhole2"] = $helpInfos[$oneValueWhole][$oneValueWhole2];
							}
						}
						if( ! $flag) // on prend toutes les infos sur la "validate" par exemple 
							$resultTabKey["$oneValue:$oneValueWhole"] = $helpInfos[$oneValueWhole];
					}else // dim($helpInfos[$oneValueWhole]) == 1 // cas options
						$resultTabKey["$oneValue:$oneValueWhole"] = $helpInfos[$oneValueWhole];
				}
			}while($oneValueWhole != null);
		}
	}
		// Recherche sur la documentation
	foreach ($helpInfos as $key => $oneValue) {
		if($key != "validation" and in($values, [implode(" ", $oneValue)]))
			$resultTabDoc[$key] = $oneValue;
	}
	// foreach ($helpInfos["validate"] as $key => $oneValue) {
	//     if($oneValue != "validate" and in($values, preg_split("#\W#", implode(" ", $oneValue))))
	//         $resultTabDoc[$key] = $oneValue;
	//         $oneValue = getValues($helpInfos["validate"]);

	
	// $resultTabKey[$i] <=> [ 'option:options', ['options($datas, $selectName, $id_val="", $fielList="", $defaulValueId="")','string', "optionsDoc ..."]]
	// $resultTabKey[$i] <=> ["valid:validation, betwe:between",[
	//     "between:min,max", $backWithInfo, "The field under validation must have a size between the given min and max. Strings, numerics, arrays, and files are evaluated in the same fashion as the ".a('href="..."', "size")." rule."]];
	foreach ($resultTabKey as $key => $oneValue) { //TODO try catch
		//see([$key, $oneValue[0]]); continue;
		$key = toArray($key);
		$key0 = toArray($key[0], ":");
		$searchMots = str_replace($key0[0], "<font color='blue'>$key0[0]</font>", $key0[1]);
		$fct = "Syntaxe : "; $return = "Retour : "; $doc = "Documentation : "; //TODO lang
		
		if(dim($oneValue) == 2){
			see(b("Backend $searchMots : ")); //TODO lang
			foreach ($oneValue as $rule => $document) {
				see(b($rule));

				see(b($fct).$document[0]);
				see(b($return).$document[1]);
				see(b($doc).$document[2]);
			}
			continue;
		}
		$validation = isset($key[1]);
		if($validation){
			$key1 = toArray($key[1], ":");
			$searchMots = "Backend $searchMots, ".str_replace($key1[0], "<font color='blue'>$key1[0]</font>", $key1[1]);//TODO lang
			$searchMots .= " (à utiliser avec la fonction <a href=''>validate(\$data, \$rules)</a>)";//TODO lang
		}
		else {
			$fct = "Fonction : "; $return = "Type de retour : "; $doc = "Documentation : "; //TODO lang
		}
		see(b($searchMots." : "));

		see(b($fct).$oneValue[0]);
		see(b($return).$oneValue[1]);
		see(b($doc).$oneValue[2]);

	}

	foreach ($resultTabDoc as $key => $oneValue) {
		$searchMots = $key;
		foreach ($values as $valValue) {
			$searchMots = str_replace($valValue, "<font color='blue'>$valValue</font>", $searchMots);
		}
		see($searchMots .= " : ");

		see($oneValue);
	}

	// $resultTabDoc[$i] <=> [ 'options', ['options($datas, $selectName, $id_val="", $fielList="", $defaulValueId="")','string', "optionsDoc ..."]]
	
}
// function __call($name, $arguments){
// 	ob_start();
// 	  call_user_func_array(preg_split("Ob", $name)[0],  $arguments);
// 	return ob_get_clean();
// }
function isInited(){
    return isInitedSession();
}
function init(){
	initSession();	
}
function bgBlack($fontSize = 15){

	if (bgBlack and devMode) //#E5E7E9 #D7DBDD #CACFD2 #BDC3C7
		echo "style='background-color:#1e1e1e; color:#CACFD2; font : bold ".$fontSize."px Consolas'";

}
function constants($key = ""){
  if($key == "")
    return get_defined_constants(true);
  else
    return get_defined_constants(true)[$key];
}
function constantsUser(){
    return get_defined_constants(true)["user"];
}
 
/**
 * Cette fonction permet de découper une chaine de caractère, un tableau ou un objet et 
 * de renvoyer le dernier élément. NB : Les chaines de caractères peuvent être découpés
 *  à partir d’un séparateur.
 * eg. last(["Seynabou", "modoui", "Abdou"])) => Abdou
 *     last("Seynabou") => u,
 *     last("Seynabou", "y") => nabou
 */
function last($stringOrarrayOrObject, $stringSeparator = null){
	$isJson = ($stringSeparator === null && isJson($stringOrarrayOrObject));

	if(!$isJson && is_string($stringOrarrayOrObject)){
		if($stringSeparator){
			$stringOrarrayOrObject = explode($stringSeparator, $stringOrarrayOrObject);
			return $stringOrarrayOrObject[count($stringOrarrayOrObject) - 1];
		}
		return $stringOrarrayOrObject[strlen($stringOrarrayOrObject) - 1];
	}
	$keys = getKeys($stringOrarrayOrObject);
    return ($stringOrarrayOrObject[$keys[count($keys)-1]]);
}

/**
 * Cette fonction prend en parametre une tableau ou un objet et retourne le
 * son dernier indice.
 * eg. lastKey(["rm", "anothervalue"]) => 1
 *     lastKey(toObject(["rm"])) => 0;
 */
function lastKey($arrayOrObject){
	$keys = getKeys($arrayOrObject);
    return $keys[count($keys)-1];
}

/**
 * TODO touché
 * La fonction between renvoie le résultat entre deux valeurs qui peut être une * chaine de caractère, ou entre deux indexes d'un tableau ou un objet.
 * eg. between(["Abdoulaye", "b", "e"], 0, 2 ) => ["Abdoulaye", "b"];
 *     between("abdoulaye", "b", "a") => doul
 */
function between($stringOrArrayOrObject, $start, $end, $startInclu = true, $endInclu = false){
	if(is_string($stringOrArrayOrObject)){
		if($start)
			$stringOrArrayOrObject = get(explode($start, $stringOrArrayOrObject, 2), 1);
		if($end)
			$stringOrArrayOrObject = get(explode($end, $stringOrArrayOrObject, 2), 0);
		return $stringOrArrayOrObject;
	}else{ //TODO à tester
		$result = [];
		$flag = false;
		foreach ($stringOrArrayOrObject as $key => $value) {
			if( ! $endInclu and $key == $end)
				break;
			if($key == $start or $flag){
				$flag = true;
				if( ! $startInclu and $key == $start)
					continue;
				$result[$key] = $value;
			}
			if($key == $end)
				break;
		}

	}
	return $result;
}

/**
 * Cette fonction permet de verifier si une chaine de caractére commence par un element specifique 
 * ou de verifier le premier indice d'un tableau.
 * eg. startsWith("abdoulaye", ["a", "c"]) => a
 *     startsWith("abdoulaye", ["abdou", "a"]) =>abdou
 *     startsWith(["abdoulaye", "Salif"], ["abdou", "abdoulaye"]) => abdoulaye
 * 
 */
function startsWith($stringOrArray, array $with, $caseSensitive = false, $trim = true){
	if(is_string($stringOrArray)){
		if($trim)
			$stringOrArray = trim($stringOrArray);
		if( ! $caseSensitive)
			$stringOrArray = strtolower($stringOrArray);
		$n = strlen($stringOrArray);
		//0123456789
		//adamambouP
		//adama
		//01234
		foreach ($with as $value) {
			$n2 = strlen($value);
			for ($i=0; $i < $n2; $i++) {
				if($stringOrArray[$i] != $value[$i])
					break;
			}
			if($i == $n2)
				return $value; 
		}
		return false;
	} //Si c'est un array
	$first = first($stringOrArray);
	$first = ! $caseSensitive ? $first : strtolower($first);
	foreach ($with as $value) {
		if($first == $value)
			return $value;
	}
	return false;
}

/**
 * Cette fonction permet de verifier si une chaine de caractére se termine par un element specifique 
 * ou de verifier le premier indice d'un tableau.
 *  eg.startsWith("abdoulaye", ["abdoulaye", "a"]) =>laye
 *     startsWith("abdoulaye", ["a", "c"]) => ""
 *     startsWith(["abdoulaye", "Salif"], ["0234", "salif"]) => salif
 * 
 */
function endsWith($stringOrArray, array $with, $caseSensitive = false, $trim = true){
	if(is_string($stringOrArray)){
		if($trim)
			$stringOrArray = trim($stringOrArray);
		if( ! $caseSensitive)
			$stringOrArray = strtolower($stringOrArray);
		$n = strlen($stringOrArray);
		//0123456789
		//adamambouP
		//     mbouP
		//     01234
		foreach ($with as $value) {
			$n2 = strlen($value);
			$j = $n-$n2;
			if($j < 0) continue;
			for ($i=0; $i < $n2; $i++) {
				if($stringOrArray[$i + $j] != $value[$i])
					break;
			}
			if($i == $n2)
				return $value; 
		}
		return false;
	}
	$last = last($stringOrArray);
	$last = $caseSensitive ? $last : strtolower($last);
	foreach ($with as $value) {
		if($last == $value)
			return $value;
	}
	return false;
}
/**
 * TODO touché
 * La fonction last permet de découper une chaine de caractère, 
 * un tableau ou un *objet et de renvoyer le premier élément.
 * NB : Les chaines de caractères peuvent être découpés à partir d’un séparateur.
 */
function first($stringOrarrayObjectOrJson, $stringSeparator = null){
	$isJson = ($stringSeparator === null && isJson($stringOrarrayObjectOrJson));

	if(!$isJson && is_string($stringOrarrayObjectOrJson)){
		if($stringSeparator)
			return explode($stringSeparator, $stringOrarrayObjectOrJson, 2)[0];
		return substr($stringOrarrayObjectOrJson, 0, 1);
	}
   	return get($stringOrarrayObjectOrJson, firstKey($stringOrarrayObjectOrJson));
}
/*
function first($stringOrarrayOrObject, $stringSeparator = ""){//TODO json et object aussi
	if(is_string($stringOrarrayOrObject)){
		if($stringSeparator)
			return explode($stringSeparator, $stringOrarrayOrObject, 2)[0];
		return substr($stringOrarrayOrObject, 0, 1);
	}
    return $stringOrarrayOrObject[getKeys($stringOrarrayOrObject, 0)];
}
*/

/**
 * Cette fonction permet de recuperer le premiere indice d'un tableau ou d'un objet
 * eg.  firstKey(toObject(["Index1"=>"laye","Index2"=> "a"])) => Index1,
 * 		firstKey(["Index1"=>"laye","Index2"=> "a"])	=> Index1
 */
function firstKey($arrayOrObject){
    return get(getKeys($arrayOrObject), 0);
}
/**
 * Cette fonction permet de retourner une note d'erreur en cas d'echec de notre operation
 * eg. notice() => ERROR
 *     notice ("La methode Hello() is introuvable") => La methode Hello() is introuvable
 * 
 */

function notice($string = "ERROR", $color = "#FE2E64", $size="12px", $lineHeight = "normal"){
  echo "<p style='color:$color; font-size:$size; line-height:$lineHeight;'>$string</p>";
}
/**
 * Cette fonction permet de retourner un note d'information en cas de Success de notre operation
 * eg. notice() => Okkk
 *     notice ("Votre transaction est effectuée avec success") => Votre transaction est effectuée avec success
 * 
 */
function ok($string = "OKKKK", $color = "#42FF33", $size="14px", $lineHeight = "normal"){
	notice($string, $color, $size, $lineHeight);
}

/**
 * Cette fonction permet d'ajouter des informations au niveau d'un pied de page
 * TODO fonction a achever
 */
function pageFooter(){ //TODO 	?>

	<footer class="footer">
        <div class="container-fluid">
          <div class="copyright">
            ©
            <script>
              document.write(new Date().getFullYear())
            </script> Developpé <i class="tim-icons icon-heart-2"></i> par Senoptimizer.
          </div>
        </div>
	</footer><?php
}

function plural($numberOrData, string $sORxOrSingularWord="s", string $pluralWord="", $uperCase=false){
  if($pluralWord){
    $result = plural2($numberOrData, $sORxOrSingularWord, $pluralWord);
		if($uperCase) return strtoupper($result);
    else  return $result;
  }
  if(is_object($numberOrData))
    $numberOrData = (array) $numberOrData;
  if(is_array($numberOrData))
    $numberOrData = count($numberOrData);
  else if(is_string($numberOrData))
    $numberOrData = strlen($numberOrData);
	if(floatval($numberOrData) > 1){
		if( ! $uperCase)
			return $sORxOrSingularWord;
		else
			return strtoupper($sORxOrSingularWord);
	}return "";
}
function plural2($numberOrData, string $singularWord, string $pluralWord){
	if(is_object($numberOrData))
    $numberOrData = (array) $numberOrData;
  if(is_array($numberOrData))
    $numberOrData = count($numberOrData);
  else if(is_string($numberOrData))
    $numberOrData = strlen($numberOrData);
	if(floatval($numberOrData) > 1)
		return $pluralWord;
	return $singularWord;
}

function isSeveral(array $array){
	return (isset($array[0]) && isset($array[1])) || count($array) > 1;
}

function replaceLastCommaByOr($string){

	if(is_string($string))
		$string = toArray($string);
	$len = count($string);
	if($len > 1){
		$last = $string[$len -1];
		unset($string[$len -1]);
		$string = implode(", ", $string);
		$string = implode(" ".msgs['or']." ", [$string, $last]);
	}else
	return $string;
		return $string[0];
}
 
/**
 * cette fonction permet de remplacer la derniére virgule(,) d'une chaine par "and";
 * eg. replaceLastCommaByAnd("Le lorem ipsum est,") =>Le lorem ipsum est and;
 * 	   replaceLastCommaByAnd("Le lorem ipsum est, en imprimerie, une suite de mots ) => Le lorem ipsum est, en imprimerie and une suite de mots
 */ 
function replaceLastCommaByAnd($string){
	if(is_string($string))
		$string = toArray($string);
	$len = count($string);
	if($len > 1){
		$last = $string[$len -1];
		unset($string[$len -1]);
		$string = implode(", ", $string);
		$string = implode(" ".msgs['and']." ", [$string, $last]);
		return $string;
	}else
		return $string[0];
}

/**
 * Cette fonction permet d'epurer un URL de sort a eliminer les espaces, quotes simple et double(', ")
 * les tirets doubles (--) et d les remplacer par un tiret simple(-);
 * eg. urlSanitizer("https://URL sanitization means--exactly'what you think it") => https://-URL-sanitization-means-exactly-what-you-think-it
 */
function urlSanitizer(string $urlOrRoute){
	$urlOrRoute = trimPlus($urlOrRoute);
	$urlOrRoute = str_replace(" ", "-",$urlOrRoute);
	$urlOrRoute = str_replace("'", "-",$urlOrRoute);
	$urlOrRoute = str_replace('"', "-",$urlOrRoute);
	$urlOrRoute = str_replace('--', "-",$urlOrRoute);
	//$urlOrRoute = toLower($urlOrRoute);
	return $urlOrRoute;
}

/**
 * Cette fonction permet de supprimer le dernier caractere d'une chaine de caractere ou
 *  un index d'un tableau que l'on donne en parametre
 *  eg.  trimPlus("Le lorem ipsum est, en imprimerie", "e") =>Le lorem ipsum est, en imprimeri
 * 		 trimPlus(["Le lorem ipsum est", "imprimerie"], "imprimerie") =>['Le lorem ipsum est', ''] 

 */
function trimPlus($stringOrArrayOrObject, $onlyToDel = "", $key = false){ // TODO $key pas achever : quand c'est true ça ne marche pas
	if(is_array($stringOrArrayOrObject) or is_object($stringOrArrayOrObject)){
		$result = [];
		foreach ($stringOrArrayOrObject as $oneKey => $value) {
			if($key)
				$result[trimPlus($oneKey, $onlyToDel)] = $value;
			else
				$result[$oneKey] = trimPlus($value, $onlyToDel);
		}
		if(is_object($stringOrArrayOrObject))
			$result = (object) $result;
		return $result;
	}
	if( ! isset($stringOrArrayOrObject[0])) return $stringOrArrayOrObject;

	if($onlyToDel){
		$n2 = strlen($onlyToDel);
		if(substr($stringOrArrayOrObject,0, $n2) == $onlyToDel)
			$stringOrArrayOrObject = substr($stringOrArrayOrObject, $n2);
		$n = strlen($stringOrArrayOrObject);
		if(substr($stringOrArrayOrObject, $n - $n2) == $onlyToDel)
			$stringOrArrayOrObject = substr($stringOrArrayOrObject, 0, $n - $n2);

		return $stringOrArrayOrObject;
	}
	$n = 1;
	$stringOrArrayOrObject = trim($stringOrArrayOrObject);
	while ($n > 0) {
		$stringOrArrayOrObject = str_replace("  ", " ", $stringOrArrayOrObject, $n);
	}
	$stringOrArrayOrObject = str_replace(" ,", ",", $stringOrArrayOrObject);
	$stringOrArrayOrObject = str_replace(" .", ".", $stringOrArrayOrObject);
	$n = 1;
	while ($n > 0) {
		$stringOrArrayOrObject = str_replace(",,", ",", $stringOrArrayOrObject, $n);
	}
	$stringOrArrayOrObject = trim($stringOrArrayOrObject);
	$n = strlen($stringOrArrayOrObject);
	if($stringOrArrayOrObject[0] == ","){
		$stringOrArrayOrObject = substr($stringOrArrayOrObject, 1, $n);
		$n --;
	}
	if($stringOrArrayOrObject[$n - 1] == ",")
		$stringOrArrayOrObject = substr($stringOrArrayOrObject, 0, $n - 1);
	return trim($stringOrArrayOrObject);
}
/**
 * Cette fonction permet de convertir le nombre de second en heure sous le formate heure:minutes:secondes
 * eg. getTime(3710) => 02:01:50
 */

function getTime($timeStamp, $format="H:i:s"){
    return date($format, $timeStamp);
}

function getDate_str($timeStamp, $format="Y-m-d"){
    return date($format, $timeStamp);
}
function getDate_array($timeStamp = null){
    return getdate($timeStamp);
}
function getDateTime($timeStamp, $format="Y-m-d H:i:s"){
    return date($format, $timeStamp);
}
/**
 * Cette fonction permet de convertir en secondes le temps écoulé entre 1 Janvier 1970 à 00:00:00
 * et la date que tu mets en parametre
 * eg. getTimeStamp("08/17/2021") =>1629151200
 */
/** Parse about any English textual datetime description into a Unix timestamp (gets the number of seconds since 1 January 1970 at 00:00:00 UTC)*/
function getTimeStamp($datetime){
    return strtotime($datetime);
}
/**
 * Cette fonction permet de convertir en secondes le temps écoulé entre 1 Janvier 1970 à 00:00:00
 * et la date que tu mets en parametre
 * eg. dateTimeToSecond("08/17/2021") =>1629151200
 */
function dateTimeToSecond($datetime){
    return strtotime($datetime);
}
function test2(){
	see(getTypeNumberFor(2020));
}
function getTypeNumberFor($str){
	if(in($str, numeric))
		return 1;
	else if(in($str, dateTime)){
		if($str == "time")
			return 2;
		else
			return 3;
	}
	elseif(in($str, ["string", "text"]))
		return 4;
}
function cast($str, $typeOrNumber){
	if($typeOrNumber == 1)
		return (double) $str;
	if($typeOrNumber == 2){
		if(strlen($str) <= 8)
			$str = $str;
		return getTimeStamp("1970-01-01 ".$str);
	}
	if($typeOrNumber == 3)
		return getTimeStamp($str);
	if($typeOrNumber == 4)
		return (string) $str;
	
	if(in($typeOrNumber, numeric))
		return (double) $str;
	if(in($typeOrNumber, dateTime)){
		if($typeOrNumber == "time"){
			if(strlen($str) <= 8)
				$str = "1970-01-01 ".$str;
			return getTimeStamp($str);
		}else
			return getTimeStamp($str);
	}
	if(in($typeOrNumber, ["string", "text"]))
		return (string) $str;
}
function minPlus($arrayOrObject, $field="", $type="numeric"){
	return minMax($arrayOrObject, $field, $type)[0];
}
function maxPlus($arrayOrObject, $field="", $type="numeric"){
	return minMax($arrayOrObject, $field, $type)[1];
}
function minMax($arrayOrObject, $field="", $type="numeric", $skipEmpty = true){
	if(empty($type)) $type="numeric";
	$dim = dim($arrayOrObject);
	$type = getTypeNumberFor($type);
	$tmp0 = "";
	$tmp = cast("", $type);
	if($dim == 2){
		if($skipEmpty){
			foreach ($arrayOrObject as $value) {
				$tmp = get($value, $field);
				if(notEmpty($tmp))
					break;
			}
		}else
			$tmp = get(first($arrayOrObject), $field);
			
		$min0 = $max0 = $tmp;
		$min = $max = cast($tmp, $type);
		foreach ($arrayOrObject as $value) {
			$tmp0 = get($value, $field);
			$tmp = cast($tmp0, $type);
			if($skipEmpty && ! notEmpty($tmp))
				continue;
			if($tmp < $min){
				$min0 = $tmp0;
				$min = $tmp;
			}
			if($tmp > $max){
				$max0 = $tmp0;
				$max = $tmp;
			}
		}
		return [$min0, $max0];
	}else if($dim = 1){
		if($skipEmpty){
			foreach ($arrayOrObject as $value) {
				$tmp = $value;
				if(notEmpty($tmp))
					break;
			}
		}else
			$tmp = first($arrayOrObject);
			
		$min0 = $max0 = $tmp;
		$min = $max = cast($tmp, $type);

		foreach ($arrayOrObject as $value) {
			$tmp0 = $value;
			$tmp = cast($value, $type);
			if($skipEmpty && ! notEmpty($tmp))
				continue;
			if($tmp < $min){
				$min0 = $tmp0;
				$min = $tmp;
			}
			if($tmp > $max){
				$max0 = $tmp0;
				$max = $tmp;
			}
		}
		return [$min0, $max0];
	}
}

///// rangeSlider
function rangeSliderDateTime($nameAndOrId, $minOrArray, $max, $from="", $to="", $grid=false, $skin="round", $type="single", $from_min = "", $from_max = "", $hide_min_max=false, $hide_from_to=false, $block=false, $prefix="", $postfix="", $min_prefix="", $max_postfix="", $force_edges=false, $values_separator= " — ", $decorate_both=false, $prettify="", $prettify_enabled=false, $prettify_separator="", $resetButton = false){
	session('ionRangeSliderJs', "pam");
	session('ionRangeSliderCss', "pam");
    $nameAndOrId = str_replace(" ", ",", $nameAndOrId);
    $nameAndOrId = explode(',', $nameAndOrId); // TODO toArray

    $typeDate = true;

    $name = $nameAndOrId[0];
    $id = (isset($nameAndOrId[1])) ? $nameAndOrId[1] : "id".$name;

    echo "<input type='text' class='js-range-slider' name='$name' id='$id'/>";
    if($resetButton) echo "<button id='reset$id'>Reset</button>";
    if(is_array($minOrArray)){
        $minOrArray = "['".implode("', '", $minOrArray)."']";
        $a = "";
    }else 
        $a = "'";
    echo "<script>$('#$id').ionRangeSlider(rangeSliderDateTime($a$minOrArray$a, '$max', '$from', '$to', Boolean($grid), '$skin', '$type', '$from_min', '$from_max', Boolean($hide_min_max), Boolean($hide_from_to), Boolean($block), '$prefix', '$postfix', '$min_prefix', '$max_postfix', Boolean($force_edges), '$values_separator', Boolean($decorate_both), Boolean($typeDate), '$prettify',Boolean($prettify_enabled), '$prettify_separator'));</script>";
    if($resetButton){
        echo "<span id='reset$id'></span>";
        echo "<script>addResetRangeSliderButton('$id');</script>";
    }
}

function rangeSliderDateTime2($nameAndOrId, $minOrArray, $max, $from="", $to="", $grid=false, $skin="round", $type="double", $from_min = "", $from_max = "", $hide_min_max=false, $hide_from_to=false, $block=false, $prefix="", $postfix="", $min_prefix="", $max_postfix="", $force_edges=false, $values_separator= " — ", $decorate_both=false, $prettify="", $prettify_enabled=false, $prettify_separator="", $resetButton = false){

    rangeSliderDateTime($nameAndOrId, $minOrArray, $max, $from, $to, $grid, $skin, $type, $from_min, $from_max, $hide_min_max, $hide_from_to, $block, $prefix, $postfix, $min_prefix, $max_postfix, $force_edges, $values_separator, $decorate_both, $prettify, $prettify_enabled, $prettify_separator, $resetButton);
}

function rangeSlider($nameAndOrId, $minOrArray, $max, $from="", $to="", $grid=false, $skin="round", $type="single", $from_min = "", $from_max = "", $hide_min_max=false, $hide_from_to=false, $block=false, $prefix="", $postfix="", $min_prefix="", $max_postfix="", $force_edges=false, $values_separator= " — ", $decorate_both=true, $prettify="", $prettify_enabled=true, $prettify_separator=" ", $resetButton=false){
	session('ionRangeSliderJs', "pam");
	session('ionRangeSliderCss', "pam");
    $nameAndOrId = str_replace(" ", ",", $nameAndOrId);
    $nameAndOrId = explode(',', $nameAndOrId); // TODO toArray

    $typeDate = false;

    $name = $nameAndOrId[0];
    $id = (isset($nameAndOrId[1])) ? $nameAndOrId[1] : "id".$name;

    echo "<input type='text' class='js-range-slider' name='$name' id='$id'/>";
    if(is_array($minOrArray)){
        $minOrArray = "['".implode("', '", $minOrArray)."']";
        $a = "";
    }else 
        $a = "'";
    echo "<script>$('#$id').ionRangeSlider(rangeSliderDateTime($a$minOrArray$a, '$max', '$from', '$to', Boolean($grid), '$skin', '$type', '$from_min', '$from_max', Boolean($hide_min_max), Boolean($hide_from_to), Boolean($block), '$prefix', '$postfix', '$min_prefix', '$max_postfix', Boolean($force_edges), '$values_separator', Boolean($decorate_both), Boolean($typeDate), '$prettify', Boolean($prettify_enabled), '$prettify_separator'));</script>";
    if($resetButton){
        echo "<span id='reset$id'></span>";
        echo "<script>addResetRangeSliderButton('$id');</script>";
    }
}
function rangeSlider2($nameAndOrId, $minOrArray, $max, $from="", $to="", $grid=false, $skin="round", $type="double", $from_min = "", $from_max = "", $hide_min_max=false, $hide_from_to=false, $block=false, $prefix="", $postfix="", $min_prefix="", $max_postfix="", $force_edges=false, $values_separator= " — ", $decorate_both=true, $prettify="", $prettify_enabled=true, $prettify_separator=" ", $resetButton=false){
    
    rangeSlider($nameAndOrId, $minOrArray, $max, $from, $to, $grid, $skin, $type, $from_min, $from_max, $hide_min_max, $hide_from_to, $block, $prefix, $postfix, $min_prefix, $max_postfix, $force_edges, $values_separator, $decorate_both, $prettify, $prettify_enabled, $prettify_separator, $resetButton);
}


////// typeahead
/**
$typeaheadConf = ['display' => '', 'minLength' => 1, 'name' => 'name', 'limit' => 10, 'maxItem' => 5, 'hint' => true, 'highlight' => true];
$templates['empty'] = "No data";
$templates['suggestion'] = "'<div><strong>' + data.student_name + '</strong> - <img height:\"50px\" width:\"50px\" src='+data.image+'></div>'" (in js with ' ');
typeahead(route("test"), $templates, "student_name", "Recherche"); */
function typeahead($urlOrRoute, $templates, $display, $hiddenName = "", $label = "", $placeholder="", $dbTable = "", $searchFields = "", $defaultOrOldId = "", Controller $ctrl = null, $withOtherTab = true, $typeaheadConf = [], $rtl = false, $describetion="", $otherClass="typeahead", $othersAtt="", $labelClassFloatingOr=true, $icon="", $title=""){
	// ["tableName" => "patient", 
	//  "fields" => "field1, field2"]

	session('typeahead', "pam");
	$thConf = ['display' => '', 'minLength' => 1, 'name' => 'name', 'limit' => 10, 'maxItem' => 10, 'hint' => true, 'highlight' => true];
	if($display)
		$thConf['display'] = $display;
	// if(notIn($thConf['display'][0], ["'", '"']))
	// 	$thConf['display'] = "'$display'";
	foreach ($typeaheadConf as $key => $value) {
		$thConf[$key] = $value;
	}

	$id = "ttQ".ucfirst($hiddenName);

	$thConf1 = "hint: Boolean(".$thConf['hint']."), highlight: Boolean(".$thConf['highlight']."), minLength: ".$thConf['minLength'];
	$thConf2 = "name: ".$thConf['name'].", display: 'tt_display_', limit: ".$thConf['limit'].", maxItem: ".$thConf['maxItem'];
	$rtl = ternary($rtl, 'dir="rtl"', "");
	$hiddenId = "tt".ucfirst($hiddenName);
	$othersAtt = ternary($othersAtt, $othersAtt." $rtl", $rtl);
	$othersAtt .= " onkeyup=\"setValIfEmpty('', '#$hiddenId', '#$id')\"";

	$withOtherTab = $withOtherTab ? ", 'withOtherTab':1" : "";
	$createRoute = tern($ctrl->getModel()->getTablesInfo("create_route_name", $dbTable), $dbTable."Create");
	$defaultOrOldDisplay = "";

	if($defaultOrOldId){
		$_POST['ttQ'] = "";
		$_POST['dbTable'] =$dbTable;
		$_POST['searchFields'] = $searchFields;
		$_POST['display'] = $thConf['display'];
		$_POST['withOtherTab'] = "1";
		$defaultOrOldDisplay = $ctrl->typeahead($defaultOrOldId);
	}
	echo '<div>';
		input('text', $label, "ttQ", $defaultOrOldDisplay, $id, $placeholder, $describetion, $otherClass, $othersAtt, $labelClassFloatingOr, $icon, $title, "", "", [route($createRoute), "_blank"]);

		input("hidden", "", $hiddenName, $defaultOrOldId, $hiddenId);
	echo '</div>';

	if(notIn($templates['empty'][0], ["'", '"']))
		$templates['empty'] = "'".$templates['empty']."'";
	
	/* $urlFileOrRoute = ""; // TODO à supprimer
	$jsSuccessFunction = "";
	$ajax = ajax("{'id': data.id}", $urlFileOrRoute, $jsSuccessFunction);
 */
	$jsSetVal = "setVal('+data.id+', \'#$hiddenId\', \''+data.tt_display_+'\')";
	$jsSetVal = "onclick=\"$jsSetVal\" onkeypress=\"if (event.keyCode == 13) $jsSetVal\"";
	$templates['suggestion'] = "'<div $jsSetVal>'+".$templates['suggestion']."+'</div>'";

	echo "<script>"; ?>
		$("#<?=$id?>").typeahead(
			//null,
			{ <?=$thConf1?> },
			{	<?=$thConf2?>,
				source: function show(q, cb, cba) {
					$.ajax({
						type: "post",
						url: "<?=$urlOrRoute?>",
						data: { 
							'ttQ':q,
							'dbTable':'<?=$dbTable?>',
							'searchFields':'<?=$searchFields?>', 'display':'<?=$thConf["display"]?>'
							<?=$withOtherTab?>,
							'isNotBackLink':1,
							'tokencsrf':'<?=csrfToken()?>',
							'multipleSubmitAccepted':'<?= multipleSubmitAccepted?>'      
						}
					}).done(function(res) {
						var res2 = jsonToObject(res);
						if( res2 === false){
							console.log(res);
							//alert("< ?=msgs['mayBeDeconnected']?>");
							
							//swalDefaul();
							swalError("<?=msg('mayBeDeconnected')?>");

						}
						cba(res2);
					})
					.fail(function(err) {
						console.log(err);
						swalError("See error in console");
					});
				},
				templates: {
					empty: [
						'<div class="empty-message">',
							<?=$templates['empty']?>,
						'</div>'
					].join('\n'),
					suggestion: function(data) {
						return <?=$templates['suggestion']?>;
					}
				}
			}
		);<?php
	echo "</script>";
}

function remove($str, $toRem, $concatBefor = "", $concatAfter = ""){//TODO à achever
	$str = str_replace($toRem, "", $str);
}

// function addToValidationMap($validationMap, $newData){
	// if(empty($_SESSION['error']))
	// $_SESSION['error'] = $newData;
	// else
	// array_replace($_SESSION['error'], $newData);
// }
function getMsg($keys, $values = ""){//"between, numeric", "attribute:age, min:12, max:15"
	// msgs[
	// 	'between'=> [
	// 		'numeric' => 'La valeur de :attribute doit être comprise entre :min et :max.',
	// 		'file'    => 'La taille du fichier de :attribute doit être comprise entre :min et :max kilo-octets.',
	// 	];
	// ]
	$result = msgs["default"];
	// $keys = toArray($keys);
	// $values = toArray($values);
	if(isset($keys[1]))
		$result = msgs[$keys[0]][$keys[1]]; //msgs['between']['numeric']
	else if(isset($keys[0]))
		$result = msgs[$keys[0]]; //msgs['between']
	$values = toArray($values);
	foreach ($values as $value) { //["attribute:age", "min:12", "max:15"]
		$value = toArray($value, ":");
		$result = str_replace(":".$value[0], $value[1], $result);
	}
	return $result;
}
//string or date onely
function equals($str1, $str2, $caseSensitive = false, $date = false){
	if($date){	
		$date1 = explode('-', $str1); 
		$date1 = mktime(0, 0, 0, $date1[1], $date1[2], $date1[0]); 
		$date2 = explode('-', $str2); 
		$date2 = mktime(0, 0, 0, $date2[1], $date2[2], $date2[0]); 
		return isDate($date) and isDate($date) and $date1 == $date2 ; 
	}
	if(! $caseSensitive)
		return strtolower($str1) == strtolower($str2);
	else
		return $str1 == $str2;
}
function getValidationType($value){
	if($value == "")		return "";
	if(isDate($value))		return "date";
	if(is_numeric($value))	return "numeric";
	if(is_file($value))		return "file";
	if(is_string($value))	return "string";
	if(is_array($value))	return "array";
}
function supOrEquals($value, $min, $type = "numeric"){
	if($type == 'numeric')
		return $value >= $min;
	elseif($type == 'string')
		return strlen($value) >= intval($min);
	elseif($type == 'file')
		return $value >= $min;
	elseif($type == 'array')
		return count($value) >= intval($min);
	elseif($type == 'date')
		return isDate($value) and isDate($min) and (after($value, $min) or $value == $min);
}
function infOrEquals($value, $max, $type = "numeric"){
	if($type == 'numeric')
		return $value <= $max;
	elseif($type == 'string')
		return strlen($value) <= intval($max);
	elseif($type == 'file')
	 	return $value <= $max;
	elseif($type == 'array')
		return count($value) <= intval($max);
	elseif($type == 'date')
		return isDate($value) and isDate($max) and before($value, $max) or $value == $max;
}

function sizeEquals($value, $value2, $type = "numeric"){
	if($type == 'numeric')
		return $value == $value2;
	elseif($type == 'string')
		return strlen($value) == strlen($value2);
	elseif($type == 'file')
	 	return $value == $value2;
	elseif($type == 'array')
		return count($value) == count($value2);
}
function afterOrEquals($date, $dateLimit){
    return isDate($date) and isDate($dateLimit) and ! before($date, $dateLimit);
}
function beforeOrEquals($date, $dateLimit){
    return isDate($date) and ! after($date, $dateLimit);
}
function before($date, $dateLimit) {
    return isDate($date) and after($dateLimit, $date);
}
function after($date, $dateLimit){
	if( ! isDate($date) or ! isDate($dateLimit) )
		return false;
		
    $date = explode('-', $date); 
    $date = mktime(0, 0, 0, $date[1], $date[2], $date[0]); 
    $dateLimit = explode('-', $dateLimit); 
    $dateLimit = mktime(0, 0, 0, $dateLimit[1], $dateLimit[2], $dateLimit[0]); 
    return ($date > $dateLimit ); 
}
function isDate($date, $format = 'Y-m-d'){
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) === $date;
}
function isJson($data){
	if( ! is_string($data))
		return false;
	json_decode($data);
	return json_last_error() === JSON_ERROR_NONE;
}
function distinct($data){
	if(is_array($data) and dim($data) == 1)
		$n = count($data);	
	else if(is_string($data))
		$n = strlen($data);	
		
	for ($i=0; $i < $n; $i++)
		for ($j=$i+1; $j < $n; $j++)
			if($data[$i] === $data[$j])
				return false;
	return true;
}
//required_if:anotherfield,value,...
function requiredIf($data, $field, $anOtherFieldAndValues, $unless = false){
	$anOtherFieldAndValues = toArray($anOtherFieldAndValues);
	$anOtherField = $anOtherFieldAndValues[0];
	unset($anOtherFieldAndValues[0]);
	$values = $anOtherFieldAndValues;

	$requiredIfOrUnless = (empty($values) and required($data[$anOtherField])) or in($data[$anOtherField], $values) ;
	if( $unless) $requiredIfOrUnless = ! $requiredIfOrUnless;
	if( $requiredIfOrUnless ){
		if( required($data[$field]))
			return true;
		else 
			return false;
	}
	return true;
}
//required_if:anotherfield,value,...
function requiredUnless($data, $field, $anOtherFieldAndValues){
	requiredIf($data, $field, $anOtherFieldAndValues, true);
}
function required($value){
	return ( $value != null and $value != "" and strlen($value) != 0 );
}
function validationMsg(array $keys, array $values = []){//"between, numeric", "attribute:age, min:12, max:15"
	// $result = $validationMsgTab["default"];
	// $keys = toArray($keys);
	// $values = toArray($values);	
	require_once("lang/".lang."/validation.php"); // validationAllMsg()

	try { //TODO revenir sur ce try... est ce utile et le throw $th;
		if(isset($keys[1]))
			$result = validationAllMsg()[$keys[0]][$keys[1]]; //msgs['between']['numeric']
		else if(isset($keys[0]))
			$result = validationAllMsg()[$keys[0]]; //msgs['between']
		else return "";
		foreach ($values as $value) { //["attribute:age", "min:12", "max:15"]
			$value = toArray($value, ":");
			$result = str_replace(":".$value[0], $value[1], $result);
		}
		return $result;
	} catch (\Throwable $th) {
		throw $th;
	}
}
function msgAdapter($msg){
  $arg = func_get_args();
  $n = count($arg);
  for($i = 1; $i <$n; $i ++){
    $msg = str_replace(":word$i", "<b>$arg[$i]</b>", $msg);
  }  
  return $msg;
}
// function getValidationMsg(){
// }
function getHelpMsg(){
	require_once("lang/".lang."/help.php");
	return $helpMsg;
}
function regexPwd(int $minLenght = 6, int $maxLenght = 17, bool $digit = true, bool $lowerCase = true, bool $uperCase = true, $specialChars = true){ //   $@%*+-_!
	if($specialChars){
		if(is_bool($specialChars))
			$specialChars = "(?=.*\W)";
		else $specialChars = "(?=*.[$specialChars\W])";
	}else $specialChars = "";
	$digit = ($digit) ? "(?=.*[0-9])" : "";
	$lowerCase = ($lowerCase) ? "(?=.*[a-z])" : "";
	$uperCase = ($uperCase) ? "(?=.*[A-Z])" : "";
	
	return "#^".$lowerCase.$uperCase.$digit.$specialChars.".{".$minLenght.",".$maxLenght."}$#";
}
function regexTelAll(){
	return regexTelInternational();
}
function regexTelInternational(){
	return "#^(((\+\d{1,3}(-|.| )?\(?\d\)?(-|.| )?\d{1,5})|(\(\d{2,6}\)))(-|.| )?)?(\d{2,4}(-|.| )?){2,5}(( x| ext)\d{1,5}){0,1}$#";
}
function allIsEmpty(){
	foreach (func_get_args() as $value)
		if(is_string($value) and $value != "")
			return false;
		else if(is_array($value) and ! empty($value))
			return false;
	return true;
}
function oneIsEmpty(){
	foreach (func_get_args() as $value)
		if(is_string($value) and $value == "")
			return true;
		else if(is_array($value) and empty($value))
			return true;
}	return false;

function regexTel($code0 = "0 + required", $codes1 = "221 required", $separators ="-. ", $motifsLenght = "2 3 2 2"){
	if(allIsEmpty($code0, $codes1, $separators, $motifsLenght)){
		return regexTelInternational();
	}
	$code0 = strtolower($code0);
	$codes1 = strtolower($codes1);
	$code = "";
	$required = false;
	$regex = "";
	if($code0){
		$code0 = toArray(trimPlus(str_replace(" ", ",", $code0)));
		foreach ($code0 as $value) {
			if(in('req',$value))
				$required = true;
			else
				$regex .= $value."|"; 
		}
	}
	if(in("+", $regex) and ! in("\+", $regex))
		$regex = str_replace("+", "\+", $regex);
	$regex = trimPlus($regex, "|");
	if($regex){
		$regex = "($regex)";
		if( ! $required) $regex = $regex."?";
	}
	$code = $regex;

	$codes1 = toArray(trimPlus(str_replace(" ", ",", $codes1)));
	$regex1 = "";
	$required = false;
	foreach ($codes1 as $value) {
		if(in('req', $value))
			$required = true;
		else
			$regex1 .= $value."|"; 
	}
	$regex1 = trimPlus($regex1, "|");
	if($regex1){
		$regex1 = "($regex1)";
		if($code)
			$code = "(".$code.$regex1."[".$separators."]?)";
		else
			$code = $regex1."[".$separators."]?";
		if( ! $required) $code = "(".$code.")?";
	}
	
	//($separators ="-. ", $motifsLenght = "2 3 2 2")

	$regex = $code;
	$i = 0;
	foreach (toArray(trimPlus(str_replace(" ", ",", $motifsLenght))) as $len){
		$sep = "";
		if($i++ == 0)
			$regex .= "([0-9]{".$len."})";
		else
			$regex .= "("."[".$separators."]?[0-9]{".$len."})";
	}
	return "#^".$regex."$#";
}
function validateAndAjusteForm(){ // util pour les formulaires numéroté et provenant de la bd
	$formNumber = getFormNumber();
	if(recaptchaControl and session("recaptcha"))
		recaptchaValidation($formNumber);
	if(notEmpty($guarded_ = guarded())){
		foreach ($guarded_ as $oneField) {
			unset($_POST[$oneField]);
		}
	}
	formCheckboxAjuster($formNumber);
	validate();
	delFormNumber($formNumber);
	if(isset($_POST["password"]))
	$_POST["password"] = passwordCryptor($_POST["password"]); 
	if(isset($_POST["password_confirmation"]))
	$_POST["password_confirmation"] = passwordCryptor($_POST["password_confirmation"]); 
    
}
function validateOneField($fieldName, $rules = "", $fieldValue = ""){

	if($fieldName == "") //TODO bon pour validation ajax mais je ne sais pas pour le reste
		backWithInput(-2);
	if($fieldValue)
	$data = [$fieldName => $fieldName];
	elseif(isset($_POST[$fieldName]))	
		$data = [$fieldName => $_POST[$fieldName]];
	elseif(isset($_FILES[$fieldName]))
		$data = [$fieldName => $_FILES[$fieldName]];
	elseif(isset($_GET[$fieldName]))	
		$data = [$fieldName => $_GET[$fieldName]];
	else
		throw new Exception("Impossible to validate without ".b($fieldName)." field's value in ".b("validateOneField")." function");

	$rule = [];
	if($rules)
		$rule = [$fieldName => $rules];
	elseif(($rule = formRules($fieldName)))
		$rule = [$fieldName => $rule];
	
	if(notEmpty($rule))
		validate($data, $rule, [], [], true, true);
}
function validate($data=[], $rule=[], $dataFile=[], $ruleFile=[], $lastRecursiveCall=true, $onlyOneField = false){ //La validation dans la bd est prioritaire
	
	if( ! $onlyOneField && $lastRecursiveCall && !session("dbRuleEverValidate") && (notEmpty($_POST) || notEmpty($_FILES))){ // Or first call
		session("dbRuleEverValidate", true);
		if(notEmpty(session("rule"))){
			$formNumber = getFormNumber();
			foreach (session("rule") as $fieldName => $oneRules)
				if($formNumber == "" or notEmpty(endsWith($fieldName, [$formNumber])))
					$rule[$fieldName] = $oneRules;
			if(notEmpty($rule) and empty($dataFile))
				$data = $_POST;
		}

		if(notEmpty(session("ruleFile"))){
			foreach (session("ruleFile") as $fieldName => $oneRules)
				if($formNumber == "" or notEmpty(endsWith($fieldName, [$formNumber])))
					$ruleFile[$fieldName] = $oneRules;
			if(notEmpty($ruleFile) and empty($dataFile))
				$dataFile = $_FILES;
		}
	}
	if(notEmpty($data) && notEmpty($rule)){
		try {
			foreach ($rule as $fieldName => $oneRules) {//required|min:5
				$pipeInRegex = "";
				// Ex : req|regex:#lmj|lmj#|req|regex:/mlkjmm/|min:5
				if(in("regex", $oneRules) or (in("^", $oneRules) and in("$", $oneRules))){
					$regexTab = toArray($oneRules, "regex:"); // req|, #lmj|lmj#|req|, /mlkjmm/|min:5
					$n = count($regexTab);
					$oneRulesTmp = $regexTab[0]; //// req|
					for ($i=1; $i < $n; $i++) { 
						$a = strlen($regexTab[$i]); //#lmj|lmj#|req|

						if($a > 0){
							$regexDelim = $regexTab[$i][0];
							$regex = toArray($regexTab[$i], $regexDelim); // , lmj|lmj, |req|
							if(in("|", $regex[1])){ // lmj|lmj
								$pipeInRegex = "p_a_m_m_b_o_u_p"; // arbitrairement
								$regex[1] = str_replace("|", $pipeInRegex, $regex[1]); // lmjp_a_m_m_b_o_u_plmj
							}
							$regex = implode($regexDelim, $regex); // #lmjp_a_m_m_b_o_u_plmj#|req|
							$oneRulesTmp .= "regex:".$regex;   // req|regex:#lmjp_a_m_m_b_o_u_plmj#|req|
						}else
							$oneRulesTmp .= "regex:".$regexTab[$i]; // req|regex:#lmjp_a_m_m_b_o_u_plmj#|req|regex:/mlkjmm/|min:5
					}
					$oneRules = $oneRulesTmp; // req|regex:#lmjp_a_m_m_b_o_u_plmj#|req|regex:/mlkjmm/|min:5
				}
				
				$valueType = ""; $fileType = ""; $fileTmp_name = ""; $fileError = ""; $fileSize = ""; 

				$value = isset($data[$fieldName]) ? $data[$fieldName] : false; // false utile pour le test de type
				
				if(is_array($value)){// case file input 
					// [name] => Attestations.pdf
					// [type] => application/pdf
					// [tmp_name] => C:\Users\ThinKpad\AppData\Local\Temp\phpD21C.tmp
					// [error] => 0
					// [size] => 565
					$fileType  = $data[$fieldName]["type"]; 
					$fileTmp_name = $data[$fieldName]["tmp_name"]; 
					$fileError = $data[$fieldName]["error"]; $fileSize    = $data[$fieldName]["size"]; 
					$value = isset($data[$fieldName]["name"]) ? $data[$fieldName]["name"] : false;// false utile pour le test de type
					if($fileTmp_name) $valueType = getValidationType($fileTmp_name);

				}else if($value) $valueType = getValidationType($value);

				$valueLen = strlen($value);
				$valueNumeric = ($fileSize == "") ? $value : $fileSize;
				
				$contrains = toArray($oneRules, "|"); //required|min:5
				foreach ($contrains as $oneContrain) { // [required, min:5]
					
					$tmp = toArray($oneContrain, ":")[0];
					if ( validationMsg([$tmp]) == "") 
						throw new Exception("<u>".$tmp."</u>".msgs["notExistRule"]);					
					if ( $valueLen == 0 and $oneContrain != "required" and ! in("confirmed", $oneContrain))
						continue;
					
					$values = [];
					$keys = [];
					$values[] = "attribute:".formLabel($fieldName);
					$ok = true;
					if(in(":", $oneContrain)){  //"between:15,25" or in:12,15,18
						$oneContainTab = toArray($oneContrain, ":"); // ["between", "15,25"]
						$keys[] = $oneContainTab[0];
						$minMax = toArray($oneContainTab[1]);

						if(in($oneContainTab[0], ['max', 'min', 'size', 'between'])){
							
							$keys[] = $valueType;
							if(
								($oneContainTab[0] == 'min' and ! supOrEquals($valueNumeric, $oneContainTab[1], $valueType)) or
								($oneContainTab[0] == 'max' and ! infOrEquals($valueNumeric, $oneContainTab[1], $valueType)) or 
								($oneContainTab[0] == 'size' and ! sizeEquals($valueNumeric, $oneContainTab[1], $valueType)) )
							{
								if($valueType == "date"){
									$convDateFct = "date".lang;
									$oneContainTab[1] = $convDateFct($oneContainTab[1]);
								}
								$values[] = $oneContainTab[0].":".$oneContainTab[1];
								$ok = false;
							}
							else if( $oneContainTab[0] == 'between' ){

								if( ! (supOrEquals($valueNumeric, $minMax[0], $valueType) and infOrEquals($valueNumeric, $minMax[1], $valueType)) ){

									if($valueType == "date"){
										$convDateFct = "date".lang;
										$minMax = [$convDateFct($minMax[0]), $convDateFct($minMax[1])];
									}
									$values[] = "min:".$minMax[0];
									$values[] = "max:".$minMax[1];
									$ok = false;
								}
							}
						}
						else if( $oneContainTab[0] == 'digits_between' ){

							if(($valueType != 'numeric') or ! (supOrEquals($valueNumeric, $minMax[0], 'string') and infOrEquals($valueNumeric, $minMax[1], 'string')) ){//Je mets string pour que ces fonctions compte le nombre de chiffres.						
								$values[] = "min:".$minMax[0];
								$values[] = "max:".$minMax[1];
								$ok = false;
							}
						}
						elseif($oneContainTab[0] == 'confirmed' and isset($data[$oneContainTab[1]]) and ! ($value == $data[$oneContainTab[1]]) ){
							$tmp = formLabel($oneContainTab[1]);
							$tmp = ($tmp) ? " au champ ".$tmp : "";
							$values[] = "other:".$tmp;
							$ok = false;
						}
						elseif( ($oneContainTab[0] == 'after' and ! after($value, $oneContainTab[1])) or
							($oneContainTab[0] == 'after_or_equal' and ! afterOrEquals($value, $oneContainTab[1])) or
							($oneContainTab[0] == 'before' and ! before($value, $oneContainTab[1])) or
							($oneContainTab[0] == 'before_or_equal' and ! beforeOrEquals($value, $oneContainTab[1])) 
						){
							$convDateFct = "date".lang;
							$values[] = "date:".$convDateFct($minMax[0]);
							$ok = false;
						}
						elseif($oneContainTab[0] == 'date_format' and ! isDate($value, $oneContainTab[1])){
							$values[] = "format:".$oneContainTab[1];
							$ok = false;
						}

						elseif($oneContainTab[0] == 'regex'){
							if($pipeInRegex)
								$okRegex = str_replace($pipeInRegex, "|", $oneContainTab[1]);
							else
								$okRegex = $oneContainTab[1];
							if( ! preg_match($okRegex, $value)) $ok = false;
						}
						elseif(($oneContainTab[0] == 'required_if' and ! requiredIf($data, $fieldName, $oneContainTab[1]))){
							$otherValue = toArray($oneContainTab[1]);
							$values[] = "other:".$otherValue[0];
							$values[] = "value:".$otherValue[1];
							$ok = false;
						}
						elseif( ($oneContainTab[0] == 'required_unless' and ! requiredUnless($data, $fieldName, $oneContainTab[1])) ){
							$otherValue = toArray($oneContainTab[1]);
							$values[] = "other:".$otherValue[0];
							unset($otherValue[0]);
							$values[] = "values:".replaceLastCommaByOr($otherValue);
							$ok = false;
						}
						elseif(in($oneContainTab[0], ["mimes", "types", "mimetypes"]) and ($fileError != 0 || ($oneContainTab[1] != "" && ! in(last($value, "."), $oneContainTab[1], ",")))){//TODO le type du fichier est pris à partir du name du tableau $_FILE pour la validation. Est ce sécurisé ?
							$values[] = "values:".replaceLastCommaByOr($oneContainTab[1]);
							$ok = false;
						}
						elseif($oneContainTab[0] == 'unique'){
							$tableAndColumn = toArray($oneContainTab[1]);
							if(notEmpty(model()->first($tableAndColumn[0],
								[tern(get($tableAndColumn[1], 1), $fieldName) => $value]))){
								$values[] = "attribute:".formLabel($fieldName);
								$ok = false;
							}
						}
					}
					else{
						$keys[] = $oneContrain;
						
						if($oneContrain == 'accepted' and ( ! $value or (! equals($value, "yes") and ! equals($value, "1") and ! (is_bool($value) and $value))))
							$ok = false;
						// case 'active_url'  :
						// case 'alpha'  :
						// case 'alpha_dash'  :
						// case 'alpha_num'  :
						elseif($oneContrain == 'array' and ! is_array($value)) $ok = false;
						elseif($oneContrain == 'boolean' and ! is_bool($value)) $ok = false;
						elseif($oneContrain == 'confirmed' and isset($data[$oneContrain."_confirmation"]) and ! ($value == $data[$oneContrain."_confirmation"])){
							$tmp = formLabel($oneContrain."_confirmation");
							$tmp = ($tmp) ? "au champ ".$tmp : "";
							$values[] = "other:".$tmp;
							$ok = false;
						}
						elseif($oneContrain == 'date' and ! isDate($value)) $ok = false;
						//case 'dimensions' :
						elseif($oneContrain == 'distinct' and ! distinct($value)) $ok = false;
						elseif($oneContrain == 'email' and ! filter_var($value, FILTER_VALIDATE_EMAIL)) $ok = false;
						// case 'exists'  :
						elseif($oneContrain == 'file' and ($fileError != 0 or $valueType != 'file')) $ok = false;
						// case 'filled'  :
						// case 'image'  :
						// case 'in'  :
						// case 'integer'  :
						// case 'ip'  :
						// case 'ipv4'  :
						// case 'ipv6'  :
						// case 'json'  :
						// case 'not_in'  :
						elseif($oneContrain == 'numeric' and ! is_numeric($value)) $ok = false;
						// case 'present'  :
						elseif($oneContrain == 'required' and ! required($value)){
							if(in(", ", $values[0]))
								$values[0] =replaceLastCommaByOr($values[0]);
							$ok = false;
						}
						elseif($oneContrain == 'string' and ! is_string($value)) $ok = false;
						// case 'timezone'   :
						// case 'uploaded'  :
						// case 'url'  :

						// default:
						// 	# code...
						// 	break;
					}
					if($ok == false){
						formError($fieldName, validationMsg($keys, $values));
					}
					if($valueType == "file"){
						addToSession("filesInfos", [$fieldName, $keys, $values]);
					}
				}
			}
			if(notEmpty($dataFile)){
				validate($dataFile, $ruleFile, [], [], false, $onlyOneField);
			}
			if( ! $onlyOneField && $lastRecursiveCall){
				toUnsetFormSessionInfos(true);
				wasValidated();
				if(has_error()){
					if($filesInfosTmp = session("filesInfos", "", true)){
						foreach ($filesInfosTmp as $tab) {
							if(! has_error($tab[0], true)){
								formError($tab[0], validationMsg(["upload_again"], $tab[2]));
							}
						}
					}
					back();
					die;
				}
			}
		} catch (\Throwable $th) {
			throw $th;
		}
	}
}
function ajaxFormValidator($ajaxValidationStyle, $number = ""){
	if(empty($ajaxValidationStyle) or notIn($ajaxValidationStyle, ["no", "false"])){
		if(empty($ajaxValidationStyle))
			$ajaxValidationStyle = ",,";
		$count = count(toArray($ajaxValidationStyle));
		$ajaxValidationStyle .= $count == 1 ? ",," : ",";
		//TODO à caché class='hidden'
		echo "<span class='d-none' id='ajax-spinner-style$number'>$ajaxValidationStyle</span>";
		echo "<script>
			$(document).on('click keyup', function(e){
				generAjaxFormValidator('$number', e);
			});
		</script>";
	}
}

function commit($controllerOrModel){
	$controllerOrModel->commit();
}
function redirect($link, $withInput = false){
	if( ! $withInput and ! has_error())
		unsetFormValidationDatas();
	if( in($link, [-1, "back"]) )
		$link = backLink(); // id : $link = backLink("", -1);
	if( $link == -2 )
		$link = backLink("", -2);
	header("Location:$link");
	exit;
}
function redirectWithOldData($link){
	redirect($link, true);
}
function redirectWithInput($link){
	redirect($link, true);
}
function back($howManyOrLink = -1, $withInput = false){
	$howManyOrLink = ternary($howManyOrLink == "", -1, $howManyOrLink);
	redirect($howManyOrLink, $withInput);
}
function backWithInput($howManyOrLink = -1){
	redirect($howManyOrLink, true);
}
function backWithOldData($howManyOrLink = -1){
	redirect($howManyOrLink, true);
}
function form($ttrAndVal){
	echo '<form '.$ttrAndVal.'>';
	$arg = func_get_args();
	$n = count($arg);
	$j = 1;
	if( ! (is_bool($arg[1]) and !$arg[1])){
		if(has_error()) notice("Il y a eu des erreurs dans le formulaire ! &darr;"); //TODO mettre dans les validations msg
		$j = 2;
	}
	for ($i=$j; $i < $n; $i++) {
		echo $arg[$i];
	}
	echo '</form>';
}function formOb($ttrAndVal){
	ob_start();
		form($ttrAndVal);
	return ob_get_clean();
}
/**
 * Génère une liste d'option pour le champ select de formulaire	
 *
 * Ex 1 : 
 *
 * $fruits = ["Mangue", "Orange", "Banane"];
 * 
 * 1 : options($fruits);
 *
 * 2 : options($fruits, "", "", 3);
 * 
 * Result 1 : 
 * 
 *  <option value="Mangue">Mangue</option>
 * 
 *  <option value="Orange">Orange</option>
 * 
 *  <option value="Banane">Banane</option>
 *
 * Result 2 : 
 * 
 *  <option value="Mangue">Mangue</option>
 * 
 *  <option value="Orange">Orange</option>
 * 
 *  <option value="Banane" selected >Banane</option>
 *   
 * Ex 2 : 
 *
 * $fruits = [ 
 * 
 * &nbsp;&nbsp;&nbsp; ["id"=>1, "libelle"=>"Mangue", "prix"=>250, "qte"=>10],
 *      
 * &nbsp;&nbsp;&nbsp; ["id"=>2, "libelle"=>"Orange", "prix"=>200, "qte"=>11],
 *    
 * &nbsp;&nbsp;&nbsp; ["id"=>3, "libelle"=>"Banane", "prix"=>150, "qte"=>15]
 * 
 * ];
 * 
 * options($fruits, "id, libelle, prix" , 2); ou bien 
 * options($fruits, ["id", "libelle", "prix"] , 2);
 * 
 * Result : 
 * 
 *  <option value="1">Mangue 250</option>
 * 
 *  <option value="2" selected>Orange 200</option>
 * 
 *  <option value="3">Banane 150</option> 
 *
 *
 */
//TODO tester avec les set et enum, $defaulValueIds peut etre un tableau
function options($datas, $selectName, $idAndLabelInfosArrayOrString = "", $defaulValueIds=""){ //prevoir aussi des options sans id_val
	$result = "";
	$fielListOk = false;

	$id_val = ""; $fielList = ""; 
	if(notEmpty($idAndLabelInfosArrayOrString)){
		if(is_string($idAndLabelInfosArrayOrString))
			$idAndLabelInfosArrayOrString = toArray($idAndLabelInfosArrayOrString);

		$fielList = $idAndLabelInfosArrayOrString;
		$id_val = $fielList[0];
	}

	$dim = dim($datas);
	if(strlen(old($selectName)))
		$defaulValueIds = old($selectName);
	if($dim == 1){
		if(! is_array($defaulValueIds))
			$defaulValueIds = toArray($defaulValueIds);
		
		foreach ($datas as $key=>$data){
			$selected = in($key, $defaulValueIds) ? " selected" : "";
			$result .= "<option value='".trim($key)."'".$selected.">".trim($data)."</option>";
		}
	}elseif($dim == 2){
		if($fielList != ""){
			//$fielList = toArray($fielList, ",");
			$fielListOk = true;
		}
		foreach ($datas as $key=>$data) {
			if($id_val == ""){
				$value = $key; 
			}else{
				$value = (is_object($data)) ? $data->$id_val : $data[$id_val];
			}
	
			$selected = in($value, $defaulValueIds) ? " selected" : "";
			$result .= "<option value='".trim($value)."'".$selected.">";
			if($fielListOk){
				$resultTmp = labelBuilder($data, $fielList);
				// foreach($fielList as $one){
				// 	$one = $one;
				// 	$resultTmp .= (is_object($data)) ? $data->$one : $data[$one]." ";
				// }
				$result .= $resultTmp;
			}else
				$result .= $value;
			$result .= "</option>";
		}
	}else $result = notice("La dimension de datas doit être 1 ou 2 et pas $dim.");
	return $result;
}
function selectOb($label, $name, $options, $id="", $descripetion="", $otherClass="", $othersAtt="", $floating="label-floating", $icon="", $title=""){
	ob_start();
		select($label, $name, $options, $id, $descripetion, $otherClass, $othersAtt, $floating, $icon, $title);
	return ob_get_clean();
}
function inputOb($type, $label, $name, $defaulValue="", $id="", $placeholder="", $describetion="", $otherClass="", $othersAtt="", $floating=true, $icon="", $title=""){
	ob_start();
		input($type, $label, $name, $defaulValue, $id, $placeholder, $describetion, $otherClass, $othersAtt, $floating, $icon, $title);
	return ob_get_clean();
}
function submitOb($name, $value="", $class = "btn btn-fill btn-wd", $othersAtt=""){
	ob_start();
		submit($name, $value, $class, $othersAtt);
	return ob_get_clean();
}
function textareaOb($label, $name, $defaulValue="", $id="", $placeholder="", $describetion="", $otherClass="", $othersAtt="", $floating=true, $icon="", $title=""){
	ob_start();
		textarea($label, $name, $defaulValue, $id, $placeholder, $describetion, $otherClass, $othersAtt, $floating, $icon, $title);
	return ob_get_clean();
}
//basic standard standard-all full full-all
function editorBasicFCK4($label="", $name="editor1", $defaulValue="", $id="", $rows="10", $cols="10", $describetion="", $icon="", $title=""){
	editorFCK4($label, $name, $defaulValue, $id, $rows, $cols, $describetion, $icon, $title, "basic");
}
function editorStandardFCK4($label="", $name="editor1", $defaulValue="", $id="", $rows="10", $cols="10", $describetion="", $icon="", $title=""){
	editorFCK4($label, $name, $defaulValue, $id, $rows, $cols, $describetion, $icon, $title, "standard");
}
function editorStandardAllFCK4($label="", $name="editor1", $defaulValue="", $id="", $rows="10", $cols="10", $describetion="", $icon="", $title=""){
	editorFCK4($label, $name, $defaulValue, $id, $rows, $cols, $describetion, $icon, $title, "standard-all");
}
function editorFullFCK4($label="", $name="editor1", $defaulValue="", $id="", $rows="10", $cols="10", $describetion="", $icon="", $title=""){
	editorFCK4($label, $name, $defaulValue, $id, $rows, $cols, $describetion, $icon, $title, "full");
}
function editorFullAllFCK4($label="", $name="editor1", $defaulValue="", $id="", $rows="10", $cols="10", $describetion="", $icon="", $title=""){
	editorFCK4($label, $name, $defaulValue, $id, $rows, $cols, $describetion, $icon, $title, "full-all");
}
function editorFCK4($label="", $name="editor1", $defaulValue="", $id="", $rows="10", $cols="10", $describetion="", $icon="", $title="", $distribution="basic"){
	formLabel($name, $label);
	if($name == "" and $id == "") return notice("editorFull() method dont accepte name and id witch is empty !");
	session('editorLink', $distribution);
	if($id == "") $id = $name;
	$describeId = "descpam".$id;
	$describetionAtt = $describetion ? ' aria-describedby="'.$describeId.'"'  : '';
	$describetion = $describetion ? '<small id="'.$describeId.'" class="form-text text-muted">'.$describetion.'</small>' : '';
	$inputOrForm = "form-group";
	if($icon){ //TODO tester les formulaires avec les icons
		$inputOrForm = "input-group";
		$icon = '<span class="input-group-addon">'.$icon.'</span>';
	}
	if($title){
		$title = '<div class="'.$name.'TitleForm" id="'.$name.'TitleForm">'.$title.'</div>';
	}
	echo $title; 	
	?>
	<div class="<?= $inputOrForm?> <?= has_error($name) ?>">
		<?= $icon ?><?= "<label class='bmd-label-static' for='$id'>$label</label>" ?>	
		<textarea <?="name='$name' id='$id' rows='$rows cols='$cols'".$describetionAtt?><?= has_error($name, true) ? ' aria-invalid="true"' : ''?>><?= old($name, $defaulValue)?></textarea>
		<?=$describetion?>
		<?= getError($name) ?>
	</div>
	<script>
		CKEDITOR.replace('<?= $id?>');
	</script><?php
}
/**distributions : classic, inline, balloon, decoupled*/
/*
function editorClassicFCK5($id = "editor"){
	editorFCK5($id, "classic");
}
function editorInlineFCK5($id = "editor"){
	editorFCK5($id, "inline");
}
function editorBalloonFCK5($id = "editor"){
	editorFCK5($id, "balloon");
}
function editorBalloonBlockFCK5($id = "editor"){
	editorFCK5($id, "balloon-block");
}
function editorDecoupledFCK5($id = "editor"){
	editorFCK5($id, "decoupled-document ");
}
function editorFCK5($id = "editor", $distribution = "classic"){
	session('editorLink', $distribution);?>
	<div id="<?=$id?>">This is some sample content.</div>
	<script>
		<?=ucfirst(first($distribution, "-"))."Editor"?>.create( document.querySelector( '#<?=$id?>' ) )
			.then( editor => {
				console.log( editor );
			} )
			.catch( error => {
				console.error( error );
		} );
	</script><?php
}
*/


/**
 * Error possible : Échec de chargement du document PDF. revoir l'encodate en utf8 ou utf8 sans boom
 */
function htmlToPdfTc($obDate, $fileName, $css="", $pageFooter="", $footer="", $backtop="-5mm", $backleft="-2mm", $backright="0mm", $backbottom="0mm", $timezone = "UTC", $orientation="P", $format="A4", $lang = lang, $html2pdfLink=html2pdfLink){
    htmlToPdf($obDate, $fileName, $css, $pageFooter, $footer, $backtop, $backleft, $backright, $backbottom, $timezone, $orientation, $format, $lang, $html2pdfLink);
}
/**
 * Error possible : Échec de chargement du document PDF. revoir l'encodate en utf8 ou utf8 sans boom
 */
function htmlToPdfA4($obDate, $fileName, $css="", $pageFooter="", $footer="page; date; heure", $backtop="10mm", $backleft="20mm", $backright="20mm", $backbottom="10mm", $timezone = "UTC", $orientation="P", $format="A4", $lang = lang, $html2pdfLink=html2pdfLink){
    htmlToPdf($obDate, $fileName, $css, $pageFooter, $footer, $backtop, $backleft, $backright, $backbottom, $timezone, $orientation, $format, $lang, $html2pdfLink);
}
/**
 * Error possible : Échec de chargement du document PDF. revoir l'encodate en utf8 ou utf8 sans boom
 */
function pdf(){
	ob_start(); ?>
    <style type="text.css">
        /**{ color:#717375;}*/
        hr{background:#717375; height:1px; border:none;}
        p{ margin:0; padding:0;}
		th{background:#717375; color:white;}
        table {border-collapse:collapse; color:#000; font-size:11pt; 
            line-height:5mm;letter-spacing:2mm;}
        strong{ color:#000;} /**/
        em{font-size:9pt; text-align:left; color:#717375;}
        h1{color:#000; margin:0; padding:0;}
        /*td.right{ text-align:right;} class="right"*/
        table.border td, th{border:1px solid #CFD1D2; padding:1mm 1mm;}/*class="border"*/
        table.noborder{border:none;}
    </style><?php
	$css = ob_get_clean();
ob_start();?>
	<h1>sldfjl</h1>
	<table broder style="vertical-align:top; width:100%; margin-left:0px">
		<thead>
			<tr>
				<th>Salam</th>
				<th>Salam</th>
				<th>Salam</th>
				<th>Salam</th>
			</tr>
		</thead>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
		<tr><td>Salam</td><td>Salam</td><td>Salam</td><td>Salam</td></tr>
	</table><?php
	$pam = ob_get_clean();
	htmlToPdfA4($pam, "pape", $css);
}

/**
 * Error possible : Échec de chargement du document PDF. revoir l'encodate en utf8 ou utf8 sans boom
 */
function htmlToPdf($obDate, $fileName, $css, $pageFooter, $footer, $backtop, $backleft, $backright, $backbottom, $timezone, $orientation, $format, $lang, $html2pdfLink){
    date_default_timezone_set($timezone);
    //$_SESSION[$_SESSION['rep1']]['ogb'].'/html2pdf/html2pdf.class.php'
    require_once($html2pdfLink);
    ob_start();
        echo $css;
        echo "<page backtop='$backtop' backleft='$backleft' backright='$backright' backbottom='$backbottom' footer='$footer'>";
        echo $obDate;
        if($pageFooter)
            echo "<page_footer>$pageFooter</page_footer>"; 
        echo "</page>";
    $obDate = ob_get_clean();
	//sd($obDate, $fileName, $css, $pageFooter, $footer, $backtop, $backleft, $backright, $backbottom, $timezone, $orientation, $format, $lang, $html2pdfLink);
	// echo $obDate;
    try{
        $pdf = new HTML2PDF($orientation, $format, $lang);
        //$pdf->pdf->SetDisplayMode('fullpage'); 
        //$pdf->setDefaultFont('freeserif'); 
        $pdf->writeHTML($obDate);
        if( ! endsWith($fileName, [".pdf"]) )
            $fileName .= ".pdf";
        $pdf->Output($fileName);
    }catch(HTML2PDF_exception $e){
        echobr($e);
        echo $obDate;
        die;
    }
    // finally{
    // }
}

function recaptchaValidation($formNumber){
	$key = recaptchaSecretKey;  //for senoptimizer.com
	$pam = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$key."&response=".$_POST[recaptchaFieldIdName.$formNumber]);
	$response = json_decode($pam);
	see("recaptcha", $response);
	if($response->success == 1 and $response->score >= 	recaptchaResponseScoreMin )
		return true;
	else
		return false;
}
function recaptchaField($nameSuffixNumber){
	$nameId = recaptchaFieldIdName.$nameSuffixNumber;
	if(empty2($recapArr = session("recaptcha")))
		$recapArr = [];
	$recapArr[] = $nameId;
	session("recaptcha", $recapArr);

	echo '<input hid den type="text" id="'.$nameId.'" name="'.$nameId.'">';
}
function grecaptcha($submitId = submitName, $fieldId = recaptchaFieldIdName, $msgConnecting = msgs['captchaConnecting'], $actionType = "login"){
	ob_start();?>
	<script>
		grecaptcha.ready(function(){
			var submitId = "<?=$submitId?>";
			document.getElementById(submitId).disabled = true;
			var oldVal = document.getElementById(submitId).value;
			document.getElementById(submitId).value = "<?=$msgConnecting?>";
			grecaptcha.execute('<?=recaptchaPublicKey?>', {action: '<?=$actionType?>'}).then(function(token) {
				document.getElementById("<?=$fieldId?>").value=token;
				document.getElementById(submitId).disabled = false;
				document.getElementById(submitId).value = oldVal;
			});
		});
	</script><?php
	return ob_get_clean();
}

function labelBuilder($data, $idAndLabelInfos, $startIndex = 1){
	
    //$data = //ex. [id=>1,libelle=>Karate,prix=>10000,heure=>12:00]
	//$idAndLabelInfos ex. [id, libelle, prix, heure]
	
	if(is_object($data))
		$data = (array) $data;
	$nIdAlabInf = count($idAndLabelInfos);
	if($nIdAlabInf == 1)
		$startIndex = 0;
	$label = "";
	if($nIdAlabInf > $startIndex){
		$label = $data[$idAndLabelInfos[$startIndex]];
		if($nIdAlabInf > ($startIndex + 1)){
			$label .= " (".$data[$idAndLabelInfos[($startIndex + 1)]];
			for ($i=($startIndex + 2); $i < $nIdAlabInf; $i++)
				$label .= ", ".$data[$idAndLabelInfos[$i]];
			$label .= ")";
		}
	}else{
		$label = isset($data["libelle"]) ? ucfirst($data["libelle"]) : "";
	}
	return $label;
}
function table($db, $tabNameWhereAssoArrOrderbyLimitTab, $OderOrOnlyField=[], $withoutField=["id"], $otherTab=[], $rangeField=[], string $tableClass="table table-hover table-striped", $lineNumber=false, $search=true, $totalFormul="", $showPath="", $updatePath="", $delPath="", $downloadPath="", $printPath = "", $statDescCols = ""){//$showPath, $updatePath, $delPath, $downloadPath : chemin ou route suivi de la manière d'affichage ("popup", "target", "modal", "")$rangeField=["date"=>"double, date", "avis"]
	$otherTab = otherTabToAssArr($otherTab);
	$tableClass = $tableClass != "" ? $tableClass : "table table-striped";
	$showPath = actionInfosToArray($showPath);
	$updatePath = actionInfosToArray($updatePath);
	$delPath = actionInfosToArray($delPath);
	$downloadPath = actionInfosToArray($downloadPath);
	$printPath = actionInfosToArray($printPath);

	// $tmp = [];
	// foreach($rangeField as $key=>$value)
	// 	$tmp[str_replace(".", "_", $key)] = $value;
	// $rangeField = $tmp;
	
	if(is_string($tabNameWhereAssoArrOrderbyLimitTab))
		$tabNameWhereAssoArrOrderbyLimitTab = [$tabNameWhereAssoArrOrderbyLimitTab];
	elseif(is_object($tabNameWhereAssoArrOrderbyLimitTab))
		$tabNameWhereAssoArrOrderbyLimitTab = (array) $tabNameWhereAssoArrOrderbyLimitTab;
	
	//see($db->get_obj('tranche_age, table', ['table.tranche_age=tranche_age.id']));

	$tableName = get($tabNameWhereAssoArrOrderbyLimitTab, 0, "", "");
	$whereAssoArr = get($tabNameWhereAssoArrOrderbyLimitTab, 1, "", []);
	$orderby = get($tabNameWhereAssoArrOrderbyLimitTab, 2, "", "");
    $limit = get($tabNameWhereAssoArrOrderbyLimitTab, 3, "", "");
    $limit = notEmpty($limit) ? $limit : numberLinesPerPage;

	$tableNames = trimPlus("$tableName,". implode(",", getKeys($otherTab)));
	
	if($OderOrOnlyField == [])
		$OderOrOnlyField = array_keys((array) $db->first_obj($tableName, $whereAssoArr));
	
	unsetValues($OderOrOnlyField, $withoutField);
	unsetValues($OderOrOnlyField, ["password"]);//TODO conditionner ceci dans config.php 
	//$_POST['q'] = "ris,Lu et pa";see($_POST);
	foreach ($otherTab as $field => $otherTabValue) {
		$firstTabField = last($field, "->");
		$field = first($field, "->");
		if($pos = getKeyFromValue($OderOrOnlyField, $firstTabField))
			$OderOrOnlyField[$pos] = $field.".".first($otherTabValue);
	}
	//see($OderOrOnlyField);

	$bodyDatasO = $db->get_obj($tableName, $whereAssoArr, $orderby, "", $otherTab);
	
	$bodyDatasFieldType = $db->getTableInfosPhpType($tableNames);
	$old = old();
	$q = get($old, "q");
	$qs = get($old, "qs");
	if(notEmpty($qs) && ! is_array($qs))
		$qs = toArray($qs);
	$rangeOld = [];
	$fieldsInfo = $db->getWithNameAsKey("fields_info");
	$headDatas=[];
	if($lineNumber)
		$headDatas[] = is_string($lineNumber) ? $lineNumber : "N°";
	$searchSelect = [];
	if(isset($_POST["q"]))
		unset($_GET["p"]);
		
	$data = array_merge($_POST, $_GET);
	///For recherche
	foreach ($OderOrOnlyField as $oneValue) {
		// if(is_int($oneValue))
		// 	$headDatas[] = $oneValue;

		$oneValue0 = $oneValue;
		$oneValue = first($oneValue, ".");
		$oneValue = first($oneValue, "->");
		$oneFieldsInfo = get($fieldsInfo, $oneValue);
		$label = $oneFieldsInfo == null ? ucfirst($oneValue) : getLabel($fieldsInfo[$oneValue]);
		$headDatas[$oneValue] = htmlTableSort($oneValue, $label, $data, route());
		if(notIn(get($bodyDatasFieldType, $oneValue0), dateTime) && (! isset($rangeField[$oneValue0]) && notIn($oneValue0, $rangeField)) )
			$searchSelect[$oneValue0] = $label;
	}
	
	$otherTab2 = [];
	foreach ($otherTab as $field => $otherTabValue) {
		$field = first($field, "->");

		if(empty($otherTabValue))
			$otherTabValue[0] = "libelle";
		if(isset($headDatas[$field]))
			$headDatas[$field] = str_replace($field, $field.".".first($otherTabValue), $headDatas[$field]);
		$fieldLabel = getLabel(get($fieldsInfo, $field), $field);
		//$fieldLabel = $fieldLabel ? " ($fieldLabel)" : "";
		foreach ($otherTabValue as $oneValue){
			$otherTab2[$field."_".$oneValue] = $field.".".$oneValue;
			$oneFieldsInfo = get($fieldsInfo, $oneValue);
			$label = $oneFieldsInfo == null ? ucfirst($oneValue) : getLabel($fieldsInfo[$oneValue]);
			$label = $label ? " ($label)" : "";
			if(
				! in(get($bodyDatasFieldType, $field.".".$oneValue), sqlDateTimeTab) &&
				(! isset($rangeField[$field.".".$oneValue]) &&
				! in($field.".".$oneValue, $rangeField)) ){
				$searchSelect[$field.".".$oneValue] = $fieldLabel.$label;
				unset($searchSelect[$field]);
			}
		}
	}
	/* foreach ($otherTab as $fieldAndTab => $otherTabValue) {
		$field = first($fieldAndTab, "->");
		
		// if(empty($otherTabValue))
		//  	$otherTabValue[0] = "libelle";

		$fieldLabel = getLabel($fieldsInfo[$field]);
		//$fieldLabel = $fieldLabel ? " ($fieldLabel)" : "";
		foreach ($otherTabValue as $oneValue) {
			$otherTab2[$fieldAndTab] = $fieldAndTab.".".$oneValue;
			$oneFieldsInfo = get($fieldsInfo, $oneValue);
			$label = $oneFieldsInfo == null ? ucfirst($oneValue) : getLabel($fieldsInfo[$oneValue]);
			$label = $label ? " ($label)" : "";
			if(notIn(get($bodyDatasFieldType, $field.".".$oneValue), dateTime) && (! isset($rangeField[$fieldAndTab]) && notIn($fieldAndTab, $rangeField)) ){
				$searchSelect[$fieldAndTab.".".$oneValue] = $fieldLabel.$label;
				unset($searchSelect[$field]);
				unset($searchSelect[$field.".".$oneValue]);
			}
		}
	} */
	$uniqVal= uniqVal();
  ob_start();
	 //For search form
	if($search){
		$sort = get($old, msgs['htlmTableSortKey']);
		$dir = get($old, msgs['htlmTableDirKey']);
		$p = get($old, "p");
		$action = get($old, "url");
		$action .= notEmpty($sort) ? "&".msgs['htlmTableSortKey']."=$sort" : "";
		$action .= notEmpty($dir) ? "&".msgs['htlmTableDirKey']."=$dir" : "";
		$action .= notEmpty($p) ? "&p=$p" : "";
		tag('form action="'.$action.'" method="post" ');
			formNumberInput($uniqVal);
			div('class="row align-items-end"');
				div('class="col-md-10"');
					div('class="row"');
						div('class="col-md-5"');
							input("text", msgs['search'], "q$uniqVal", $q);
						_div();
						div('class="col-md-6 col-10"');
							$options = options($searchSelect, "nom", "", notEmpty($qs) ? $qs : getKeys($searchSelect));
							select( msgs['searchFilter'], "qs$uniqVal"."[]", $options);
						_div();
						div('class="col-md-1 col-2 pl-0"');
							input("checkbox", "", "searchFilterAll$uniqVal", "checked","","","","","","","",msgs['searchFilterAll'], "checked");
						?>
						<script>
							$("#searchFilterAll<?=$uniqVal?>").change(function(){
								var options = $("#qs<?=$uniqVal?> option");
								if (!$("#searchFilterAll<?=$uniqVal?>").is(":checked")){
									options.removeAttr("selected");
								}else{
									options.each(function () {
										$(this).attr('selected','selected'); 
									});
								}							
								
								$('#qs<?=$uniqVal?>').selectpicker('refresh');
							});
						</script>

						<?php
							_div();
					_div();

					$i = 0;
					$opened = false;
					foreach ($rangeField as $field =>$oneValue) {
						if($i % 2 == 0){
							div('class="row"');
							$opened = true;
						}
						$i++;
						
						$fieldArr = toArray($field, ".");
						if(count($fieldArr) > 1){
							$tmp = get($fieldArr, 0);							
							$oneFieldsInfo = get($fieldsInfo, $tmp);
							$label = $oneFieldsInfo == null ? ucfirst($tmp) : getLabel($fieldsInfo[$tmp]);
							$label = "($label)";
							unset($fieldArr[0]);
						}else $label = "";
						$field0 = first($fieldArr);
						$oneFieldsInfo = get($fieldsInfo, $field0);
						$label = ($oneFieldsInfo == null ? ucfirst($field0) : getLabel($fieldsInfo[$field0]) ).$label;

						$rangeSliderFct = "rangeSlider";
						$sOrD = "";
						$type="";
						if(notEmpty($oneValue)){
							if(is_string($oneValue))
								$oneValue = toArray($oneValue);
							if($oneValue[0] == "double")
								$sOrD = "2";
							if(isset($oneValue[1]))
								$type = $oneValue[1];
						}
						$field_ = str_replace(".", "_", $field);
						$rangeOld[$field_] = toArray(old($field_, ""), ";");
						if(in($type, dateTime)){
							$rangeSliderFct .= "DateTime";
							if(notEmpty($rangeOld[$field_][0]))
								$rangeOld[$field_][0] = getDateTime($rangeOld[$field_][0]);
							if(isset($rangeOld[$field_][1]) && notEmpty($rangeOld[$field_][1]))
								$rangeOld[$field_][1] = getDateTime($rangeOld[$field_][1]);
						}
						$rangeSliderFct .= $sOrD;
						$minMax = minMax($bodyDatasO, $field_, $type);
						div('class="col-md-6"');
							div('class="row align-items-end"');
								div('class="col-md-3"');
									echo $label;
								_div();
								div('class="col-md-8 mt-2"');
									$rangeSliderFct($field_, $minMax[0], $minMax[1],first($rangeOld[$field_]), last($rangeOld[$field_]));
								_div();
							_div();
						_div();

						if($i % 2 == 0){
							_div();
							$opened = false;
						}
					}
					if($opened)
						_div();
				_div();
				div('class="col-md-2"');
					submit("pamboup", "Recherche");
				_div();
			_div();
		_div("form"); ?>
		<?php
	}
	 //WhereAssoArr construction
	if(notEmpty($rangeOld)){ //date 2102 - 2111
		foreach ($rangeOld as $field => $value) {
			$field2 = get($otherTab2, $field) ;
			$field2 = $field2 ? $field2 : $field;
			if(notEmpty($value[0]))
				$whereAssoArr = array_merge(["$field2 between "=>first($value)." and ".last($value)], $whereAssoArr);
		}
	}
	if(notEmpty($q)){
		$searchField = []; //$OderOrOnlyField; // ...
		$searchFieldDateTime = [];
		foreach($bodyDatasFieldType as $field => $type){
			if(in($field, $qs)){
				if(in($type, dateTime))
					$searchFieldDateTime[] = $field;
				else
					$searchField[] = $field;
			}
		}
		if(notEmpty($searchField)){
			$searchField = toString($searchField, ", ' ', "); // il faut concatener avec espace pour éviter que SAMBOU Pape devienne SAMBOUPape et contien ainsi MBOUP
			$whereAssoArr = array_merge(["CONCAT($searchField) like"=>"%".$q."%"], $whereAssoArr);
		}
	}
			//see($q, $qs, $whereAssoArr);
	
	// Organisation
	if(isset($_GET[msgs['htlmTableSortKey']]) && in($_GET[msgs['htlmTableSortKey']], $OderOrOnlyField)){
		$sort =  $_GET[msgs['htlmTableSortKey']];
		$direction = $_GET[msgs['htlmTableDirKey']] ? $_GET[msgs['htlmTableDirKey']] : 'asc';
		if( ! in($direction, ['asc', 'desc']))
			$direction = 'asc';
		$orderby .= trim($orderby) ? ", " : "";
		$orderby .= "$sort $direction";
	}

	// Pagination 
	$nbOfLine = $db->count($tableName, $whereAssoArr, "", $otherTab);
	$pages = ceil($nbOfLine / $limit);
	$perPage = $limit;
	$page = (int)(get($_GET, 'p') ? get($_GET, 'p') : 1);
	$offset = ($page - 1) * $perPage;
	$limit = "$perPage OFFSET $offset";

	if(notEmpty($statDescCols)){
		see($db->statDesc_obj($tableName, $statDescCols,  $whereAssoArr, $otherTab)); //TODO ici
	}
	
	$num = $offset + 1;
	/*if($id){
		$whereAssoArr = array_merge([first($tableName.".id",',')=>$id], $whereAssoArr);
		return ;
	}*/
	$bodyDatas = $db->get_obj($tableName, $whereAssoArr, $orderby, $limit, $otherTab);
	
	//$bodyDatasFieldType = $db->getTableInfosPhpType($tableName);
	
	// if($OderOrOnlyField == [])
	// 	$OderOrOnlyField = array_keys((array) $bodyDatas[0]);
	
	// unsetValues($OderOrOnlyField, $withoutField);
	
	$actions = 0;
	?>
	<div class="table-responsive">
	<table class="<?=$tableClass?>" id="">
		<thead class="text-primary">
			<tr> <?php 
				// if($styles != "")
				// 	$styles = 'style="'.$styles.'"';
				$colSpan = "";
				foreach ($headDatas as $value) {
					if(is_int($value) and $value > 1 ){
						$colSpan = ' colspan="'.$value.'"';
						continue;
					}
					echo '<th'.$colSpan.'>'.$value.'</th>';
					if($colSpan != ""){
						$colSpan = "";
					}
				}
				if(get($showPath,0))	$actions ++;
				if(get($updatePath, 0))	$actions ++;
				if(get($delPath, 0))		$actions ++;
				if(get($downloadPath, 0))$actions ++;
				if(get($printPath, 0))	$actions ++;
				$nbr = $actions * 2 + 1;
				echo "<th class='text-right' style='min-width:".$nbr."em'>".plural2($actions, msgs['actionText'], msgs['actionsText'])."</th>";
				?>
			</tr>
		</thead>
		<tbody> <?php      
				
			$total2 = 0;
			foreach ($bodyDatas as $donnees){
				$id = get($donnees, "id");
				$totalFormul2 = "";
				echo"<tr id='genTabId$id'>";
				if($lineNumber)
					echo "<td> ".numberFormat($num ++) ."</td>";
				foreach ($OderOrOnlyField as $field){
					$value = get($donnees, first($field, "."));
					if(! notEmpty($value)){
						echo "<td><center> -- </center></td>";
						continue;
					}
					if($field == "moisannee") //TODO revenir ici
						$value = getMoisAnnee($value);
					// else if($field == "date")
					// 	$value = dateFormatSep(dateFormatlan($value, "-"), "-", "/");
					else if(strpos($totalFormul, $field)  !== false){
						if($totalFormul2 == "")
							$totalFormul2 = $totalFormul;

						$totalFormul2 = str_replace($field, $value, $totalFormul2);
					}
					
					if(in($bodyDatasFieldType[$field], ["integer", "real"]) && is_numeric($value)) 
						$value = numberFormat($value);
					else if($bodyDatasFieldType[$field] == "date")
						$value = dateFormat($value);
					else if(in($bodyDatasFieldType[$field], ["datetime", "timestamp"]))
						$value = dateTimeFormat($value);
					else if($bodyDatasFieldType[$field] == "time")
						$value = timeFormat($value);
					echo "<td class='td-name'>$value</td>";
				}
				if($totalFormul2)
					$total2 += $totalFormul2;
				$actions2 = "";
				if($actions > 0){
					if(get($showPath, 0))
						$actions2 .= actionClick($showPath[0], $id, $showPath[1], buttonMini(iconFa("eye"), $showPath[0]=="$"? "default" : "info", "", buttonTableStyle));
					if(get($updatePath, 0))
						$actions2 .= actionClick($updatePath[0], $id, $updatePath[1], buttonMini(iconFa("pencil"), $updatePath[0]=="$"? "default" : "success", "", buttonTableStyle));
					if(get($delPath, 0))
						$actions2 .= buttonMini(iconFa("trash-o fa-fw"), $delPath[0]=="$"? "default" : "danger", 'onclick="'.delete($delPath[0]."&$id", $id, "genTabId".$id).'"', buttonTableStyle);
					if(get($downloadPath, 0))
						$actions2 .= actionClick($downloadPath[0], $id, $downloadPath[1], buttonMini(iconFa("download"), $downloadPath[0]=="$"? "default" : "success", "", buttonTableStyle));
					if(get($printPath, 0))
						$actions2 .= actionClick($printPath[0], $id, $printPath[1], buttonMini(iconFa("print"), $printPath[0]=="$"? "default" : "success", "", buttonTableStyle));
					
					echo "<td class='td-actions text-right'>".$actions2."</td>";
				}
				echo "</tr>";
			} ?>
		</tbody>
	</table>
	</div><?php
	pagination($pages, $page, route());
	$table = ob_get_clean();
	return notEmpty($totalFormul) ? [$table, $total2] : $table;
}
///////////////////// a=>
function tableOb($headDatas, $bodyDatas, $bodyOder="", $numberFormatList="", $dateFormatList="", $totalFormul="", $lineNumber=false, $showPath=false, $updatePath=false, $delPath=false){
	ob_start();
		table($headDatas, $bodyDatas, $bodyOder, $numberFormatList, $dateFormatList, $totalFormul, $lineNumber, $showPath, $updatePath, $delPath);
	return ob_get_clean();
}
function pagination($nbrPages, $numCurrentPage, $href0="#", $title="", $pagintionMaxNumber=pagintionMaxNumber, $align="left", $arrows=msgs['paginatioArrows']){
	if($pagintionMaxNumber == "")
		$pagintionMaxNumber=pagintionMaxNumber;
	if($href0 == "")
		$href0="#";
	if($nbrPages <= 1) return;
	if($align == "center") $align = " justify-content-center";
	else if($align == "right") $align = " justify-content-end";
	else $align = "";
	if($arrows){
		$previous = msgs["previousArrow"];
		$next = msgs["nextArrow"];
	}else{
		$previous = msgs["previous"];
		$next = msgs["next"];
	}
	
	if($numCurrentPage <= 1){
		$prevDisabled = " disabled";
		$prevHref = "";
	}else{
		$prevDisabled = "";
		$prevHref = $href0.toHrefParamWithGETandPOST(["p" => $numCurrentPage - 1]);
	}

	if($numCurrentPage >= $nbrPages){
		$nextDisabled = " disabled";
		$nextHref = "";
	}else{
		$nextDisabled = "";
		$nextHref = $href0.toHrefParamWithGETandPOST(["p" => $numCurrentPage + 1]);
	}
	$axNumberPer2 = (int)($pagintionMaxNumber/2);
	$min = max($numCurrentPage - $axNumberPer2, 1);
	$max = $min + $pagintionMaxNumber - 1;
	$maxLoop = min($max, $nbrPages);
	$tmp = $maxLoop - $min;  // 7 - (22-18) = 3 // 
	if($tmp < $pagintionMaxNumber && $pagintionMaxNumber < $nbrPages)
		$minLoop = $min - ($pagintionMaxNumber - $tmp - 1);
	else
		$minLoop = $min;
	$etcPrev = max($minLoop - $axNumberPer2 - 1, 1);
	$etcNext = min($maxLoop + $axNumberPer2 + 1, $nbrPages);
	$etcPrev = $href0.toHrefParamWithGETandPOST(["p" => $etcPrev]);
	$etcNext = $href0.toHrefParamWithGETandPOST(["p" => $etcNext]);
	?>	
	<nav aria-label="<?=$title?>">
		<ul class="pagination<?=$align?>">
			<li class="page-item<?=$prevDisabled?>">
				<a class="page-link" href="<?=$prevHref?>" tabindex="-1"><?=$previous?></a>
			</li><?php
			
			if($min > 1){?>
				<li class="page-item">
					<a class="page-link" href="<?=$etcPrev?>">...</a>
				</li><?php
			}
			for ($p=$minLoop; $p <= $maxLoop; $p++) {
				$href = $href0.toHrefParamWithGETandPOST(["p" => $p]);
				if($p == $numCurrentPage){
					$active = " active";
					$srOnly = ' <span class="sr-only">(current)</span>';
				}else{
					$active = "";
					$srOnly = "";
				}
				?>
				<li class="page-item<?=$active?>">
					<a class="page-link" href="<?=$href?>"><?=$p.$srOnly?></a>
				</li><?php
			}
			if($max < $nbrPages){?>
				<li class="page-item">
					<a class="page-link" href="<?=$etcNext?>">... / <?=$nbrPages?></a>
				</li><?php
			}?>
			<li class="page-item<?=$nextDisabled?>">
				<a class="page-link" href="<?=$nextHref?>"><?=$next?></a>
			</li>
		</ul>
	</ul><?php
}/*
// Swal.fire({
// 	position: 'top-end',
// 	icon: 'success',
// 	title: 'Your work has been saved',
// 	showConfirmButton: false,
// 	timer: 1500
//   })
function swalModal($attrAssArrOrJson, $fctAndPartIfConfirmed="", $fctAndPartIfCanceled="", $buttonStyling = true, $confirmButtonColor = msgs['confirmButtonColor'], $confirmCancelColor = msgs['confirmCancelColor']){ //$attrAssArrOrJsonOrTitle,$text="",$type="",$icon="",$confirmButtonColor="",$confirmButtonText="",$cancelButtonColor="",$cancelButtonText=""){
	if($buttonStyling)
		$buttonStyling = "	
		buttonsStyling: false,
		customClass:{
			confirmButton: 'btn btn-$confirmButtonColor',
			cancelButton: 'btn btn-$confirmCancelColor'
		},";
	else $buttonStyling = "";
	if(is_array($attrAssArrOrJson))
		$attrAssArrOrJson = toJson($attrAssArrOrJson);
	$result = "Swal.fire({".$buttonStyling.trimPlus($attrAssArrOrJson,'{').")";
	if($fctAndPartIfConfirmed || $fctAndPartIfCanceled){
		$result .= ".then((result)=>{";
		if($fctAndPartIfConfirmed)
			$result .= "if(result.value){ $fctAndPartIfConfirmed }";
		if($fctAndPartIfCanceled)
			$result .= "if(result.dismiss === Swal.DismissReason.cancel){ $fctAndPartIfCanceled }";
		$result .= "})";
	}
	return $result;
}
function swalModalDefaul($fctIfConfirmed="", $fctIfCanceled="", $timer=""){
	if(is_int($timer))
		$timer = ", timer : $timer";
	else $timer = "";
	$attrAssArrOrJson = "{
		title: '".msgs['confirmTitle']."',
		text: '".msgs['confirmText']."',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: '".msgs['confirmConfirmButtonText']."',
		cancelButtonText: '".msgs['confirmCancelButtonText']."',
		reverseButtons: true}";

	$fctAndOtherIfConfirmed = swalModal("{title: '".msgs['confirmDeletedTitle']."', text: '".msgs['confirmDeletedText']."', icon: 'success'$timer}");
	$fctAndOtherIfError = swalModal("{title: '".msgs['confirmErrorTitle']."', text: '".escaper(msgs['confirmErrorText'], "'")."', icon: 'warning'$timer}");
	$fctAndOtherIfCanceled = swalModal("{title: '".msgs['confirmCancelledTitle']."', text: '".msgs['confirmCancelledText']."', icon: 'error'$timer}");

	if(notEmpty($fctIfConfirmed))
		$fctAndOtherIfConfirmed = "
		  	var fctIfConfirmed = $fctIfConfirmed; 
			if(fctIfConfirmed == true){ $fctAndOtherIfConfirmed }
			else if(fctIfConfirmed == false){ $fctAndOtherIfError }";
	if(notEmpty($fctIfCanceled))
	 	$fctAndOtherIfCanceled = "$fctIfCanceled, $fctAndOtherIfCanceled";

	return swalModal($attrAssArrOrJson, $fctAndOtherIfConfirmed, $fctAndOtherIfCanceled);
}

function swalModalDefaul2($fctIfConfirmed="", $fctIfCanceled="", $timer=""){
	if(is_int($timer))
		$timer = ", timer : $timer";
	else $timer = "";
	$attrAssArrOrJson = "{
		title: '".msgs['confirmTitle']."',
		text: '".msgs['confirmText']."',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: '".msgs['confirmConfirmButtonText']."',
		cancelButtonText: '".msgs['confirmCancelButtonText']."',
		reverseButtons: true}";

	if($fctIfConfirmed)
		$fctAndOtherIfConfirmed = "{title: '".msgs['confirmDeletedTitle']."', text: '".msgs['confirmDeletedText']."', icon: 'success'$timer}";
	else $fctAndOtherIfConfirmed = "";

	$fctAndOtherIfError = "{title: '".msgs['confirmErrorTitle']."', text: '".escaper(msgs['confirmErrorText'], "'")."', icon: 'warning'$timer}";
	
	if($fctIfCanceled)
		$fctAndOtherIfCanceled = "{title: '".msgs['confirmCancelledTitle']."', text: '".msgs['confirmCancelledText']."', icon: 'error'$timer}";
	else $fctAndOtherIfCanceled = "";

	return swalModal($attrAssArrOrJson, $fctAndOtherIfConfirmed, $fctAndOtherIfCanceled);
}
function delete0($routeOrUrl, $id){
	return swalModalDefaul("ajaxDel('$routeOrUrl', $id)");
}
function testswal(){
	return 
	button("Try me!", "primary", 'onclick="'.swalModalDefaul('aaaa(123456)').'"').
	button("Try me!", "primary", 'onclick="testsw3()"');
}*/
function delete($routeOrUrl, $id, $idHtmlToDel="", $postOrGet = "post", $alertIfConfirmAndOk=true, $alertIfConfirmAndNotOk=true, $alertIfCanceled=true, $bootstrapButtonStyling = true, $confirmButtonColor = msgs['confirmButtonColor'], $confirmCancelColor = msgs['confirmCancelColor']){
	if($routeOrUrl[0]=="$") //pour désactiver le bouton auc as ou  access denied
		return "";
	$postOrGet = trim(toLower($postOrGet));
	if(notIn($postOrGet, ["post", "get"]))
		$postOrGet =  "post";
	if( $postOrGet == "post" )
		$csrf = csrfToken();
	else $csrf = "";
	return "swalModal('$routeOrUrl', $id, '$idHtmlToDel', '$postOrGet', '$csrf', Boolean($alertIfConfirmAndOk), Boolean($alertIfConfirmAndNotOk), Boolean($alertIfCanceled), Boolean($bootstrapButtonStyling), '$confirmButtonColor', '$confirmCancelColor')";
}
function ajax($data, $urlFileOrRoute, $jsSuccessFunction="", $postOrGet="post"){
	$jsSuccessFunction = tern($jsSuccessFunction, $urlFileOrRoute);
	$postOrGet = trim(toLower($postOrGet));

	$quote = first($urlFileOrRoute);
	if($quote == "\\")
		$quote = substr($urlFileOrRoute, 0, 2);

	if(notIn($postOrGet, ["post", "get"]))
		$postOrGet =  "post";
	$csrf = ternary($postOrGet == "post", csrfToken());

	return "ajax($data, $urlFileOrRoute, $jsSuccessFunction, $quote$postOrGet$quote, $quote$csrf$quote)";	
}
// function ajaxDependance($data){
// 	return "ajaxDependance($data, 'post', '".csrfToken()."')";	
// }
/**  $screen : sm, md, lg, or xl. */
function right($data, $screen="", $margin="margin-bottom:0;margin-top:0"){
	return align($data, "right", $screen, $margin);
}
/**  $screen : sm, md, lg, or xl. */
function center($data, $screen="center", $margin="margin-bottom:0;margin-top:0"){
	return align($data, "", $screen, $margin);
}
/**  $screen : sm, md, lg, or xl. */
function left($data, $screen="", $margin="margin-bottom:0;margin-top:0"){
	return align($data, "left", $screen, $margin);
}
/**  $screen : sm, md, lg, or xl. */
function top($data, $screen="", $margin="margin-bottom:0;margin-top:0"){
	return align($data, "top", $screen, $margin);
}
/**  $screen : sm, md, lg, or xl. */
function middle($data, $screen="", $margin="margin-bottom:0;margin-top:0"){
	return align($data, "middle", $screen, $margin);
}
/**  $screen : sm, md, lg, or xl. */
function bottom($data, $screen="", $margin="margin-bottom:0;margin-top:0"){
	return align($data, "bottom", $screen, $margin);
}
/**  $screen : sm, md, lg, or xl. */
function align($data, $align, $screen="", $margin="margin-bottom:0;margin-top:0"){
	$screen = notEmpty($screen) ? "-$screen" : "";
	$margin = notEmpty($margin) ? " style='$margin'" : "";
	return "<p class='text$screen-$align'$margin>$data</p>";
}
function a($libelle, $href='', $aOrButtonWithColor="a", $target="", $otherAttr = ""){
	if($aOrButtonWithColor and in("button", $aOrButtonWithColor)){
		if( ! is_array($aOrButtonWithColor) )
			$aOrButtonWithColor = toArray($aOrButtonWithColor);
		$libelle = button($libelle, get($aOrButtonWithColor, 1), $otherAttr);
	}if($target == "")
		return "<a href='".$href."' ".$otherAttr.">".$libelle."</a> ";
	if(in("blank", $target))
		return "<a href='".$href."' target='_blank' ".$otherAttr.">".$libelle."</a> ";
}
function actionClick($pathOrRoute, $id, $target="", $buttonOrText){
	$href = ($pathOrRoute == "$") ? "" : " href='".$pathOrRoute."&id=$id'";
	if($target == "")
		return "<a$href>".$buttonOrText."</a> ";
	if(in("blank", $target))
		return "<a$href target='_blank'>".$buttonOrText."</a> ";
/*
	if($target == ""){
		return <<<HTML
		<a href="$pathOrRoute&id=$id">$buttonOrText</a>
HTML;
	}if(in("blank", $target)){
		return <<<HTML
		<a href="$pathOrRoute&id=$id" target='_blank'>$buttonOrText</a>
HTML;
	}*/
}
function actionInfosToArray($strOrTable){
	if( ! is_array($strOrTable))
		$strOrTable = toArray($strOrTable);
	$strOrTable[1] = isset($strOrTable[1]) ? $strOrTable[1] : "";
	return $strOrTable;
}
//TODO mettre bcp d'exemples d'icon, pour $class fa fa-twitter...
/**
 * $class : fa or other (material-icons)
 */
function icon($name = "", $size = "", $class = "material-icons"){
	if(in("fa", $class))
		return iconFa($name, $size);
	else
		return iconMaterial($name, $size);
}
/** $size : 18, 24, 36, 48 ... */
function iconMaterial($name, $size = ""){
	$size = ($size) ? " style='font-size:$size"."px;'" : "";
	return '<i class="material-icons"'.$size.'">'.$name.'</i>';
}
function iconFa($name, $size=""){
	$size = ($size) ? " style='font-size:$size"."px;'" : "";
	return "<i class='fa fa-$name'$size></i>";
}
//iconFa("plus"), secondary, onclick=alert(\'Salam\'), round, sm
function iconFaButton($iconName, $text="", $color="primary", $otherAttr="", $size="", $iconSize="", $buttonStyle = buttonStyle, $mini = false, $justIcon = false, $socialType = "", $socialSquare = "square", $otherClass = "", $spinner = ""){
	return button(iconFa($iconName, $iconSize).$text, $color, $otherAttr, $buttonStyle, $size, $mini, $justIcon, $socialType, $socialSquare, $otherClass, $spinner);
}

/**
 * round, just-icon, next previous finish wd fill default primary info success warning danger lg sm xs. 
 */
function btn($str = "", $separator = " "){
	$str = str_replace("btn", "", $str);
	$result = "btn";
	foreach (toArray($str, $separator) as $value) {
		$result .= " btn-".$value;
	}
	echo $result;
}
/**
 * round next previous finish wd fill default primary info success warning danger lg sm xs. 
 */
function btnWclass($str = "", $separator = " "){
	echo 'class="'.btn($str, $separator).'"';	
}
function div($attrAndVal = "", $content = "", $otherTag = ""){
	tag("div ".$attrAndVal, $content, $otherTag);
}
function divOb($attrAndVal = "", $content = "", $otherTag = ""){
	ob_start();
		div($attrAndVal, $content, $otherTag);
	return ob_get_clean();
}
function _div($otherTag = ""){
	_tag("div");
	($otherTag != "") ? tag($otherTag) : "";
}
function _divOb($otherTag = ""){
	ob_start();
		_div($otherTag);
	return ob_get_clean();
}
function balise($nameAttrAndVal, $content = ""){
	tag($nameAttrAndVal, $content);
}
function baliseOb($nameAttrAndVal, $content = ""){
	ob_start();
		balise($nameAttrAndVal, $content);
	return ob_get_clean();
}
function _balise($name, $otherTag = ""){
	_tag($name);
	($otherTag != "") ? tag($otherTag) : "";
}
function _baliseOb($name, $otherTag = ""){
	ob_start();
		_balise($name, $otherTag);
	return ob_get_clean();
}
function tag($nameAttrAndVal, $content = "", $otherTag = ""){ //TODO etudier le cas form (voir la fonction form ...)
	$close = ($content != "") ? "</".first($nameAttrAndVal, " ").">" : "";
	echo "<".$nameAttrAndVal.">".$content.$close;
	($otherTag != "") ? tag($otherTag) : "";
}
function tagOb($nameAttrAndVal, $content = "", $otherTag = ""){
	ob_start();
		tag($nameAttrAndVal, $content, $otherTag);
	return ob_get_clean();
}
function _tag($name){
	echo "</$name>";
}function _tagOb($name){
	ob_start();
		_tag($name);
	return ob_get_clean();
}
/** Fait des retours chariot (à la ligne) $n fois.*/
function br($n = 1){
	$n = intval($n);
	for ($i=0; $i < $n; $i++) { 
		echo "<br>";
	}
}
function brOb($n = 1){
	ob_start();
		br($n);
	return ob_get_clean();
}
/** Fait des retours chariot (à la ligne) $n fois.*/
function nbsp($n = 1){
	$n = intval($n);
	for ($i=0; $i < $n; $i++) { 
		echo "&nbsp;";
	}
}
function nbspOb($n = 1){
	ob_start();
		nbsp($n);
	return ob_get_clean();
}
function b($data){
	return "<b>$data</b>";
}
function u($data){
	return "<u>$data</u>";
}
function addPrefixes($arrayOrString, $prefixe, $prefixSeparator = ",", $stringSeparator = ","){
	if(is_string($arrayOrString))
		$arrayOrString = toArray($arrayOrString, $stringSeparator);
	return $prefixe.implode($prefixSeparator.$prefixe, $arrayOrString);
}/** if $ifTrue == null then $ifTrue = $conditionAnOrdData */
function ternary($conditionAnOrdData, $ifTrue=null, $ifFalse=""){
	if($ifTrue === null) $ifTrue = $conditionAnOrdData;
	return $conditionAnOrdData ? $ifTrue : $ifFalse;
}
function tern($conditionAnOrdData, $ifFalse=""){
	return $conditionAnOrdData ? $conditionAnOrdData : $ifFalse;
}
function ternary2($conditionAnOrdData, $cancatenateBefore="", $cancatenateAfter=""){
	return $conditionAnOrdData ? $cancatenateBefore.$conditionAnOrdData.$cancatenateAfter : "";
}
function card2($title="", $content, $id="", $color="info"){
	$id = ternary($id, ' id="'.$id.'"')	?>
	<div class="bd-callout bd-callout-<?=$color?>">
		<h4<?=$id?>><?=$title?></h4>
		<p><?=$content?></p>
	</div>		<?php
}
//il faut que card-header, card-avatar, card-body et card-footer soient les perères classe du div
function card($header="", $body="", $footer="", $id="", $align="", $otherStyle="", $otherClass=""){	
	$otherClass = ternary($otherClass, ' '.$otherClass);
	$otherStyle = ternary($otherStyle, ' style="'.$otherStyle.'"');
	$id = ternary($id, ' id="'.$id.'"');
	$align = ternary($align, ' text-'.$align);
	if($header){
		if(in("card-avatar", substr($header, 0, 30))){
			$otherClass = " card-profile".$otherClass;
		}
		else if(notIn("img", substr($header, 0, 10)))
			$header = ternary(in("card-header", substr($header, 0, 30)), $header, '<div class="card-header">'.$header.'</div>');
	}
	if($body)
		$body = ternary(in("card-body", substr($body, 0, 30)), $body, '<div class="card-body">'.$body.'</div>'); 
	if($footer)
		$footer = ternary(in("card-footer", substr($footer, 0, 30)), $footer, '<div class="card-footer">'.$footer.'</div>');
	echo '<div class="card'.$otherClass.$align.'"'.$id.$otherStyle.'>'.$header.$body.$footer.'</div>';
}

function cardBody($body=""){
	return '<div class="card card-body">'.$body.'</div>';
}
function cardHeader($title="", $body="", $cardClass="primary", $iconName="", $iconClass="material-icons", $otherStyle="", $otherClass=""){
	$otherClass = ternary($otherClass, ' '.$otherClass);
	$otherStyle = ternary($otherStyle, ' style="'.$otherStyle.'"');
	$result = "";
	if($title and $body) {
		$result = '<h4 class="card-title">'.$title.'</h4><p class="category">'.$body.'</p>';
	}else if($title){
		$cardClass .= ternary(notIn("text", $cardClass), " text");
		$result = '<div class="card-text"><h4 class="card-title">'.$title.'</h4></div>';
	}else if($body){
		$result = '<h4 class="card-title">'.$body.'</h4>';
	}else if($iconName){
		$cardClass .= ternary(notIn("icon", $cardClass), " icon");
		$result = '<div class="card-icon"><i class="'.$iconClass.'">'.$iconName.'</i></div>';
	}	
	$cardClass = $cardClass ? " ".addPrefixes($cardClass, "card-header-", " ", " ") : "";
	echo '<div class="card-header'.$cardClass.$otherClass.'"'.$otherStyle.'>'.$result.'</div>';
}
function cardAvatar($imgSrc, $href="javascript:;", $imgAlt="", $otherAvatarClass="", $otherAvatarStyle=""){
	echo '<div class="card-avatar'.ternary2($otherAvatarClass, " ").'"'.ternary2($otherAvatarStyle, ' style="', '"').'><a href="'.$href.'"><img class="img" src="'.$imgSrc.'" alt="'.$imgAlt.'"></a></div>';
}
function cardFooter($content, $otherClass="", $otherStyle="", $id=""){
    echo '<div class="card-footer'.ternary2($otherClass, " ").'"'.ternary2($otherStyle, ' style="', '"').ternary2($id, ' id="', '"').'><div class="stats">'.$content.'</div></div>';
}

function tooltip($id, $title, $placement = "top", $option="enable", $optionInStart = "show"){ // option : show|hide|toggle|dispose|enable|disable  see https://getbootstrap.com/docs/4.0/components/tooltips/
	$options = [$id, $option, $optionInStart];
	sessionAppendArr("tooltip", $options);
	return 'data-toggle="tooltip" data-placement="'.$placement.'" title="'.$title.'"';	
}
/** Doc modal .. */
function modal($titleOrModalHeader, $modalBody, $modalFooter="", $id, $taillSmOrLg="", $closeButton=true, $fluid=true, $otherClassHeader="", $otherClassBody="", $otherClassFooter=""){	
	if(($titleOrModalHeader = trim($titleOrModalHeader))){ 
		if($titleOrModalHeader[0] == "<") // si on ne donne pas quelque chose de complet button, a, input ...
			$titleOrModalHeader = ternary(in("modal-header", substr($titleOrModalHeader, 0, 30)), $titleOrModalHeader, '<div class="modal-header'.ternary2($otherClassHeader, " ").'">'.$titleOrModalHeader.'</div>'); 
		else
			$titleOrModalHeader = '<div class="modal-header'.ternary2($otherClassHeader, " ").'">
				<h5 class="modal-title" id="'.$id.'Label">'.$titleOrModalHeader.'</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>'; 
	}
	if(($modalBody = trim($modalBody))){ 
		if($modalBody[0] == "<") // si on ne donne pas quelque chose de complet button, a, input ...
			$modalBody = ternary(in("modal-header", substr($modalBody, 0, 30)), $modalBody, '<div class="modal-header'.ternary2($otherClassBody, " ").'">'.$modalBody.'</div>'); 
		else
			$modalBody = '<div class="modal-header'.ternary2($otherClassBody, " ").'">'.$modalBody.'</div>'; 
	}
	if(($modalFooter = trim($modalFooter))){ 
		if($modalFooter[0] == "<") // si on ne donne pas quelque chose de complet button, a, input ...
			$modalFooter = ternary(in("modal-footer", substr($modalFooter, 0, 30)), $modalFooter, '<div class="modal-footer'.ternary2($otherClassFooter, " ").'">'.$modalFooter.'</div>'); 
		else
			$modalFooter = '<div class="modal-footer'.ternary2($otherClassFooter, " ").'">'.ternary($closeButton, '<button type="button" class="btn btn-secondary" data-dismiss="modal">'.msgs['close'].'</button>').'<button type="button" class="btn btn-primary">'.$modalFooter.'</button></div>'; 
	}
	echo '<div class="modal'.ternary($fluid, " fade").'" id="'.$id.'" tabindex="-1" role="dialog" aria-labelledby="'.$id.'Label" aria-hidden="true">
		<div class="modal-dialog'.ternary2($taillSmOrLg, " modal-").'" role="document">
			<div class="modal-content">'.
			$titleOrModalHeader.$modalBody.$modalFooter.'
		</div></div></div>';
}
function modalLauncher($libelle, $modalId){
	echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#'.$modalId.'">'.$libelle.'</button>';
}
function lorem($number = 1, $withBr="<br><br>"){
	$result = "";
	for ($i=1; $i <= $number; $i++){
		$result .= 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Reprehenderit quae, deserunt minima ratione praesentium repellendus amet, deleniti molestiae similique, sequi cum itaque. Recusandae reiciendis labore consectetur vitae ab perferendis? Repellat';
		$result .= ($i < $number)? $withBr:"";
	}
	return $result;
}
function h($num, $content, $textClass="", $otherClass="", $otherStyle=""){
	$class = ternary($textClass, addPrefixes($textClass, ' text-'));
	$class .= ternary2($otherClass, ' ');
	echo '<h'.$num.ternary2(trim($class), ' class="', '"').ternary2($otherStyle, ' style="', '"').'>'.$content.'</h'.$num.'>';
}
function button($text, $color="primary", $otherAttr="", $style=buttonStyle, $size="", $mini=false, $justIcon=false, $socialType="", $socialSquare="square", $otherClass="", $spinner = ""){ 
	$otherAttr = notEmpty($otherAttr) ? " $otherAttr" : "";
	if($style == "round") $style = " btn-round";
	else if($style == "link") $style = " btn-link";
	else $style = "";
	$size = ($size) ? " btn-$size" : ""; 		//"sm", "", "lg"
	$mini = ($mini) ? " btn-fab btn-fab-mini" : ""; 
	$color = ($color == "") ? " btn-primary" : " btn-$color"; 	//"sm", "", "lg"
	$justIcon = ($justIcon) ? " btn-just-icon" : "";
	$$otherClass = ($otherClass) ? " $otherClass" : "";
	if($socialType){
		$socialSquare = ($socialSquare) ? "-$socialSquare" : "";
		$socialType = "<i class='fa fa-$socialType$socialSquare'>";
		$color = "";
	}//TODO j'ai mis span au lieu de button car les lien de show, update delete dans les tableau ne marchaient pas.
	
	return "<span class='btn$color$style$size$mini$justIcon$otherClass'$otherAttr>
		$socialType$text$spinner</span>";
}
function buttonGroup($texts, $color="primary", $style=buttonStyle, $size="", $mini=false, $justIcon=false, $socialType="", $socialSquare="square"){ 
	$result = '<div class="btn-group">';
	if(in("button", $texts))
		return $result.$texts."</div>"; 
	if(! is_array($texts))
		$texts = toAssArray($texts);
	foreach ($texts as $value) {
		$result .=button($value, $color, "", $style, $size, $mini, $justIcon, $socialType, $socialSquare);
	}
	return $result."</div>";
}
function buttonMini($text, $color="primary", $otherAttr = "", $style=buttonStyle, $size="", $justIcon=false){
	return button($text, $color, $otherAttr,$style, $size, true, $justIcon, "", "");
}
function buttonSpinner($spinType = spinnerType, $spinColor = spinnerColor, $spinSize = "sm", $spinText = loadingText,
$text, $color="primary", $otherAttr = "", $style=buttonStyle, $size="", $justIcon=false){

  	$spinColor = $spinColor ? " text-$spinColor" : "";
	$spinSize = $spinSize ? " spinner-$spinType-$spinSize" : "";
	
	$spinner = '<span class="spinner-'.$spinType.$spinColor.$spinSize.'" role="status" aria-hidden="true"></span>
	<span class="sr-only">'.$spinText.'</span>';

	return button($text, $color, $otherAttr,$style, $size, true, $justIcon, "", "", "", $spinner);
}
/** spinnerType : grow, border ; 
 * $color : primary, success, danger, warning, info, light, dark;
 * $size : "", sm */
function spinner($type = spinnerType, $color = spinnerColor, $size = "", $id = "" , $otherClass = ""){
	$id = $id ? " id='$id'" : "";
	$otherClass = $otherClass ? " $otherClass " : "";
	if(notIn(toLower($type), ["grow", "border"]))
		$type = spinnerType;
	$color = $color ? " text-$color" : "";
	$size = $size ? " spinner-$type-$size" : "";

	return '<div'.$id.' class="'.$otherClass.'spinner-'.$type.$color.$size.'"><span class="sr-only">Loading...</span></div>';
}
function spinnerForAjaxValidation($id, $otherClass, $type = spinnerType, $color = spinnerColor, $size = ""){
	return spinner($type, $color, $size, $id, $otherClass);
}
function badge($text, $color="primary", $style="", $href = ""){//$style=pill or round
	$style = in($style, ["pill", "round"]) ? " badge-pill" : "";
	$color = ($color != "") ? " badge-$color" : " badge-primary";
	$class = "class='badge$style$color'";
	if($href)
		return "<a href='$href' $class>$text</a>";
	return "<span $class>$text</span>";
}
function badgeLink($text, $href, $color="primary", $style=""){ //$style=pill or round
	return badge($text, $color, $style, $href);
}
function arbitrary($data = "", $endWithThis = ""){
	if(notEmpty($data))
		return $data.$endWithThis;
	$tmp = substr(time(), 6);
	return notEmpty($endWithThis) ? $tmp.$endWithThis : $tmp.rand(0, 10000);
}

function collapse($headBodyIdColorAssArr, $type=""){//$type : a, button, 
	if(in($type,["a", "button"])){
		$result1 = $result2 = "";
		foreach ($headBodyIdColorAssArr as $head => $bodyIdColor){
			$body = get($bodyIdColor, 0);
			$id = arbitrary(get($bodyIdColor, 1));
			$color = get($bodyIdColor, 2);
			$color = empty($color) ? " btn-primary" : " btn-$color";
			$result1 .= "<$type class='btn$color' data-toggle='collapse' href='#$id' aria-expanded='false' aria-controls='$id'>$head</$type>";
			$result2 .= "<div class='collapse' id='$id'><div class='card card-body'>$body</div></div>";
		}
		return "<p>$result1</p>$result2";
	}
	else{
		$accordionId = "pamac".arbitrary();
		$result = "<div id='$accordionId' role='tablist' aria-multiselectable='true' class='card-collapse'>";
		$tmp = true;
		$show = " show";
		foreach ($headBodyIdColorAssArr as $head => $bodyIdColor){
			$body = get($bodyIdColor, 0);
			$id = arbitrary(get($bodyIdColor, 1));
			$result .= "<div class='card card-plain'>
				<div class='card-header' role='tab' id='h$id'>
					<a data-toggle='collapse' data-parent='#$accordionId' href='#$id' aria-expanded='$tmp' aria-controls='$id'>$head<i class='material-icons'>keyboard_arrow_down</i></a>
				</div>
				<div id='$id' class='collapse$show' role='tabpanel' aria-labelledby='he$id'><div class='card-body'>$body</div></div>
			</div>";
			$tmp = false;
			$show = "";
		}
		return "$result</div>";
	}
}
function dropdown($buttonText, $labelHrefAssArr, $color="", $split = false, $up = false, $buttonOrA = "button", $notButton = false, $right = true, $alignRight = true){
	$id = arbitrary();
	
	if($notButton){
		$buttonOrA = "a";
		$type = "";
		$aClassStyle = " class='btn btn-link' style='padding:10px;'";
		$tmpText = "<div class='ripple-container'></div>";
		$rightOrLeft = " dropdown-menu-".(($right or $right == "right") ? "right" : "left");
		$split = false;
		$dropdownToggle = " data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'";
		$btnGroupUp = $alignRight ? " class='text-right'" : "";
		$marginTop = " style='margin-top:-15px;'";
	}else{
		$marginTop = "";
		$buttonOrA = (in($buttonOrA, ["a", "button"])) ? $buttonOrA : "button";
		$type = " type='button'";
		$aClassStyle = "";
		$color = $color == "" ? "btn-primary" : "btn-$color";
		$up = $up ? " dropup" : "";
		$btnGroupUp = " class='btn-group$up'";
	
		$rightOrLeft = "";
		$tmpText = "<span class='sr-only'>Toggle Dropdown</span>";
	
		if($split){
			$split = is_string($split) ? "btn-$split" : $color;
			$split = "<$buttonOrA$type class='btn $split dropdown-toggle dropdown-toggle-split' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
				$tmpText
			</$buttonOrA>";
			$dropdownToggle = " class='btn $color'";
		}else {
			$dropdownToggle = " class='btn $color dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'";
			$split = "";
		}
	}	
	$result = "<div$btnGroupUp$marginTop><$buttonOrA$type$aClassStyle id='$id'$dropdownToggle>$buttonText$tmpText</$buttonOrA>";
	$result .= "$split<div class='dropdown-menu$rightOrLeft' aria-labelledby='$id'>";
	foreach ($labelHrefAssArr as $label => $href) {
		if("hr" == $href && in("hr", $label))
			$result .= "<div class='dropdown-divider'></div>";
		else
			$result .= "<a class='dropdown-item' href='$href'>$label</a>";
	}
	return $result."</div></div>";
}
function dropdown2($textOrIcon, $labelHrefAssArr, $dropRight = true, $alignRight = true){
	return dropdown($textOrIcon, $labelHrefAssArr, "", false, false, "a", true, $dropRight, $alignRight);
}

function dropdownOfNavbar($textOrIcon, $dMdBLockText = "",$labelHrefAssArr, $color="", $right = true){
	$id = arbitrary();
	$rightOrLeft = ($right or $right == "right") ? "right" : "left";
	$color = $color == "" ? "" : " btn-$color"; 
	ob_start(); ?>
	<li class="nav-item dropdown">
	<a class="nav-link" href="javascript:;" id="<?=$id?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$textOrIcon?>
		<p class="d-lg-none d-md-block"><?=$dMdBLockText?></p>
		<div class="ripple-container"></div>
	</a>
	<div class="dropdown-menu dropdown-menu-<?=$rightOrLeft?>" aria-labelledby="<?=$id?>"><?php
		foreach ($labelHrefAssArr as $label => $href) {
			if("hr" == $href && in("hr", $label))
				echo "<div class='dropdown-divider'></div>";
			else
				echo "<a class='dropdown-item' href='$href'>$label</a>";
		}?>
	</div></li><?php
	return ob_get_clean();
}
function navWithBar($headBodyIdColorAssArr, $prefixId="", $style="", $barColor="", $buttonColor="", $short = false, $card=true, $headUpper=true){
	$card0 = $card ? "card " : "";
	$cardPlain = " card-plain";
	if($headUpper){
		$card0 = "card ";
		$cardPlain = "";
	}
	if($card && ! $headUpper)
		$card0 = "";
	if(! $card && ! $headUpper){
		$card0 = "card ";
		$cardPlain = " card-plain";
	}
	$resultHead = $resultBody = "";
	foreach ($headBodyIdColorAssArr as $head0 => $bodyIdColor){
		$head = $head0;
		$body = get($bodyIdColor, "body");
		$id = "navWithBar".arbitrary($prefixId.get($bodyIdColor, "id"));
		$color = get($bodyIdColor, "color");
		$active = get($bodyIdColor, "active");
		if($active=="1" || $active=="active" || $active=="true" || $active==true){
			$active = " active";
		}else{
			$active = "";
		}
		/*$color = empty($color) ? " btn-primary" : " btn-$color";*/
		$tmp = substr(trim($head), 0, 10);
		if(! (in("button", $tmp) || in("<a", $tmp) || in("< a", $tmp)))
			$head = "<a class='nav-link$active' href='#$id' data-toggle='tab'>$head</a>";
		$resultHead .= "<li class='nav-item'>$head</li>";
		$resultBody .= "<div class='tab-pane$active' id='$id'><p>$body</p></div>";
	}
	$resultHead ="<div class='nav-tabs-navigation'><div class='nav-tabs-wrapper'><ul class='nav nav-tabs' data-tabs='tabs'>$resultHead</ul></div></div>";
	
	$resultBody = "<div class='card-body'><div class='tab-content text-center'>$resultBody</div></div>";
	
	$barColor = $barColor ? " card-header-$barColor" : " card-header-primary";
	$buttonColor = $buttonColor ? $buttonColor : "primary";
	if($style && in($style, ["pills", "pill", "round", "icon", "icons"])){
		if(in($style, ["icon", "icons"]))
			$icon = " nav-pills-icons";
		else $icon = "";
		$style = " nav-pills$icon nav-pills-$buttonColor";
	}
	if($short)
		$short = "nav ";
	else
		$short = "";
	$result =  "<div class='$card0$short"."card-nav-tabs$cardPlain'>
		<div class='card-header$barColor$style'>
			$resultHead
		</div>
		$resultBody
	</div>";
	if($card && ! $headUpper){
		return card($result);}
	else
		return $result;
}
function nav($headBodyIdColorAssArr, $prefixId="", $style="round", $colomne=[], $buttonColor="", $card=true, $navAlign="", $bodyAlign=""){ // "", "square", ("round", "pill", "pills"), upper, uppercase
	$resultHead = $resultBody = "";
	$upperLowerUcf = ""; // à revenir ici 
	foreach (["upper", "lower", "ucf", "ucfirst", "capital"] as $value) {
		if(in($value, $style)){
			if(in($value, ["upper", "lower"]))
				$upperLowerUcf = "to".ucfirst($value);
			else
				$upperLowerUcf = "ucfirst";
		}
	}
	foreach ($headBodyIdColorAssArr as $head0 => $bodyIdColor){
		$head = $head0;
		$body = get($bodyIdColor, "body");
		$id = "nav".arbitrary($prefixId.get($bodyIdColor, "id"));
		$color = get($bodyIdColor, "color");
		$active = get($bodyIdColor, "active");
		if($active=="1" || $active=="active" || $active=="true" || $active==true){
			$active = " active";
			$ariaExpanded = "true";
		}else{
			$active = "";
			$ariaExpanded = "false";
		}
		$disabled = get($bodyIdColor, "disabled");
		$disabled = ($disabled=="1" || $disabled=="disabled" || $disabled=="true" || $disabled==true) ? " disabled" : "";
		/*$color = empty($color) ? " btn-primary" : " btn-$color";*/
		$tmp = substr(trim($head), 0, 10);
		if(! (in("button", $tmp) || in("<a", $tmp) || in("< a", $tmp))){
			if($upperLowerUcf)
				$head = $upperLowerUcf($head);
			$head = "<a class='nav-link$active$disabled' href='#$id' data-toggle='tab'  role='tablist' aria-expanded='$ariaExpanded'>$head</a>";}
		$resultHead .= "<li class='nav-item'>$head</li>";
		$resultBody .= "<div class='tab-pane$active' id='$id' aria-expanded='$ariaExpanded'><p>$body</p></div>";
	}
	$icon = " nav-pills-icons";
	$buttonColor = "";
	if($style){
		$tmp = toArray($style);
		$style = "";
		if( $upperLowerUcf or in($tmp, ["round", "pill", "pills"]))
			$icon = "";
		if( ! $upperLowerUcf){
			$buttonColor == "" ? " nav-pills-primary" : " nav-pills-".$buttonColor ;
			$style = " nav-pills$icon$buttonColor";
		}
	}
	if(notEmpty($colomne)){
		$flexColumn = " flex-column";
		$colomne[0] = "<div class='row'><div class='col-md-".get($colomne, 0)."'>";
		$colomne[1] = "</div><div class='col-md-".get($colomne, 1)."'>";
		$colomne[2] = "</div></div>";
	}
	else{
		$flexColumn = "";
		$colomne = toArray($colomne);
		$colomne[0] = "";
		$colomne[1] = "";
		$colomne[2] = ""; 
	}
	if($navAlign && $navAlign == "center")
		$navAlign = " justify-content-center";
	elseif($navAlign && in($navAlign, ["right", "end"]))
		$navAlign = " justify-content-end";
	else
		$navAlign = "";
	if($bodyAlign && $bodyAlign == "center")
		$bodyAlign = " text-center";
	elseif($bodyAlign && in($bodyAlign, ["right", "end"]))
		$bodyAlign = " text-right";
	else
		$bodyAlign = "";
	
	$resultHead ="<ul class='nav$navAlign$style$flexColumn' role='tablist'>$resultHead</ul>";
	
	$resultBody = "<div class='tab-content tab-space$bodyAlign'>$resultBody</div>";
	
	$result = "$colomne[0]$resultHead$colomne[1]$resultBody$colomne[2]";
	
	if($card)
		return card($result);
	else
		return $result;
}
/**
 * $type : twitter, facebook, google, linkedin, pinterest, youtube, tumblr, github, behance, dribbble, reddit. 
 */
function buttonSocial($text, $type, $otherAttr = "", $style=buttonStyle, $size="", $mini=false, $justIcon=false, $socialSquare="square"){
	return button($text, "", $otherAttr, $style, $size, $mini, $justIcon, $type, $socialSquare);
}

function base64_encode_image ($filename) {
	if ($filename) {
		$imgbinary = fread(fopen($filename, "r"), filesize($filename));
		return base64_encode($imgbinary);
	}
}
function base64_encode_image_data ($filename) {
	if ($filename) {
		$filetype = last($filename, ".");
		return 'data:image/' . $filetype . ';base64,' . base64_encode_image ($filename);
	}
}
/** Return ['jpg','jpeg','png','gif','bmp','svg', 'JPG','JPEG','PNG','GIF','BMP','SVG'] */
function imageTypes2($without_StringOrArrayOrObject = [], $stringSeparator = ",", $withUpperCase = true){
	$result = ['jpg','jpeg','png','gif','bmp','svg'];
	return privateFileTypes($result, $without_StringOrArrayOrObject, $stringSeparator, $withUpperCase);
}

/** Return ['mp3','wma','wav','og', 'MP3','WMA','WAV','OG']*/
function audioTypes($without_StringOrArrayOrObject = [], $stringSeparator = ",", $withUpperCase = true){
	$result = ['mp3','wma','wav','og'];
	return privateFileTypes($result, $without_StringOrArrayOrObject, $stringSeparator, $withUpperCase);
}
/** Return ['avi','wmv','mov','mpg', 'AVI','WMV','MOV','MPG'] */
function videoTypes($without_StringOrArrayOrObject = [], $stringSeparator = ",", $withUpperCase = true){
	$result = ['avi','wmv','mov','mpg'];
	return privateFileTypes($result, $without_StringOrArrayOrObject, $stringSeparator, $withUpperCase);
}
/** Return ['c','php','java','css','html','xml', 'py', 'sql', 'js', 'C','PHP','JAVA','CSS','HTML','XML', 'PY', 'SQL', 'JS']*/
function sourceCodeTypes($without_StringOrArrayOrObject = [], $stringSeparator = ",", $withUpperCase = true){
	$result = ['c','php','java','css','html','xml', 'py', 'sql', 'js'];
	return privateFileTypes($result, $without_StringOrArrayOrObject, $stringSeparator, $withUpperCase);
}
/** Return ['pdf','doc', 'docx','ppt','pptx', 'pps', 'ppsx', 'odt','xls', 'xlsx', 'csv', 'txt','rtf', 'PDF','DOC', 'DOCX','PPT','PPTX', 'PPS', 'PPSX', 'ODT','XLS', 'XLSX', 'CSV', 'TXT','RTF' ] */
function documentTypes($without_StringOrArrayOrObject = [], $stringSeparator = ",", $withUpperCase = true){
	$result = ['pdf','doc', 'docx','ppt','pptx', 'pps', 'ppsx', 'odt','xls', 'xlsx', 'csv', 'txt','rtf'];
	return privateFileTypes($result, $without_StringOrArrayOrObject, $stringSeparator, $withUpperCase);
}
function privateFileTypes($types, $without_StringOrArrayOrObject = [], $stringSeparator = ",", $withUpperCase = true){
	$result = [];

	if($withUpperCase)
		foreach ($types as $value)
			$types [] = strtoupper($value);

	if(! is_array($without_StringOrArrayOrObject))
		$without_StringOrArrayOrObject = toArray($without_StringOrArrayOrObject, $stringSeparator);
	foreach ($types as $value) {
		if(! in($value, toUpper($without_StringOrArrayOrObject)) and ! in($value, toLower($without_StringOrArrayOrObject))  )
			$result[] = $value;
	}
	return $result;
}
//TODO pour quoi je ne gère pas l'ID
function uploadImage($name, $label="Fichier", $title = "", $removeLabel = "Enlever",
		$changeLabel = "Changer", $accept ="image/png,image/jpeg,image/jpg,image/gif",
		$labelBtnColor="btn-info", $removeBtnColor="btn-warning", $textColor="", $ajaxValidation = ""){
	$accept = tern($accept, "image/png,image/jpeg,image/jpg,image/gif"); 
	uploadFile($name, $label, $title, $removeLabel, $changeLabel, $accept, $labelBtnColor, $removeBtnColor, $textColor, $ajaxValidation);
}
//TODO pour quoi je ne gère pas l'ID
function uploadFile($name, $label="Fichier", $title = "", 
	$removeLabel = "Enlever", $changeLabel = "Changer", $accept ="", 
	$labelBtnColor="btn-info", $removeBtnColor="btn-warning", $textColor="", $ajaxValidation = ""){ //TODO internationaliser Fichier Enlever Changer
	//changeKeysPost()
	$removeLabel = tern($removeLabel, "Enlever"); 
	$changeLabel = tern($changeLabel, "Changer");
	$labelBtnColor= tern($labelBtnColor, "btn-info");
	$removeBtnColor= tern($removeBtnColor, "btn-warning");
	formLabel($name, $label);
	$accept = $accept == "" ? "" : ' accept="'.$accept.'"';

	$textColor = $textColor == "" ? "" : " color:$textColor;";
	if(in("btn", $labelBtnColor, "", false)){
		$btnColor = "";
		$labelBtnColor = " $labelBtnColor ";
	}else{
		$btnColor = ' style="background-color:'.$labelBtnColor.';'.$textColor.'" ';
		$labelBtnColor = "";
	}
	if($title)
		echo '<p style="margin-bottom:5px" class="'.$name.'TitleForm" id="'.$name.'TitleForm">'.$title.'</p>';	?>
	<div class="input-group image-preview <?= has_error($name)?>" id="uploadfile<?= $name?>" style="margin-left:-11px">
		<span onclick="uploadFileOpen('<?=$name?>', '<?=$label?>', '<?=$changeLabel?>', '<?=msgs['preview']?>', '<?=msgs['noFile']?>')" class="input-group-btn">
			<div class="btn btn-round<?=$labelBtnColor?>image-preview-input"<?= $btnColor?> id="uploadfileinput<?= $name?>" style="padding-left:.2rem; padding-right:.3rem;">
        <i class="material-icons">attach_file</i>
        <span class="image-preview-input-title" id="uploadfileinputtitle<?= $name?>"><?= $label ?></span>
      <input <?=$ajaxValidation?> type="file"<?= $accept ?> name="<?= $name ?>" value="<?= old($name) ?>" id="uploadfilefile<?= $name?>"/> <!-- rename it -->
			</div>
		</span><?php
	
		if(in("btn", $removeBtnColor, "", false)){
			$btnColor = "";
			$removeBtnColor = " $removeBtnColor ";
		}else{
			$btnColor = '<style="background-color:'.$removeBtnColor.';'.$textColor.'">';
			$removeBtnColor = "";
		}?>
	
		<span class="input-group-btn" style="padding:0; margin-top:-5px; margin-left:-10px;">
			<!-- image-preview-clear button -->
			<div class="btn btn-round<?= $removeBtnColor?> image-preview-clear" style="padding-left:.2rem; padding-right:.3rem; display:none;"<?= $btnColor?> id="uploadfileclear<?= $name?>">
            <i class="material-icons">clear</i><?= $removeLabel?>
      </div>
			<!-- image-preview-input -->
		</span>
		<input <?=$ajaxValidation?> type="text" <?= has_error($name, true) ? '' : '"'?> class="form-control image-preview-filename<?= has_error($name, true) ? ' error' : ''?>" disabled="disabled" id="uploadfilefilename<?= $name?>"<?= has_error($name, true) ? ' aria-invalid="true"' : ''?>> <!-- don't give a name === doesn't send on POST/GET -->
		<?= getError($name) ?>
	</div>
	<script type="text/javascript" defer>
		<?php require_once "tools/assets/js/uploadFile.js" ?>
	</script>

	<?php
}
//http://cdn.ckeditor.com/
function editorLinkForHead(){
	$tmp = session('editorLink');
	if(notEmpty($tmp)){
		switch ($tmp) {
			case 'basic': ?>
				<script src="https://cdn.ckeditor.com/<?=editor4V?>/basic/ckeditor.js"></script><?php
				break;
			case 'standard': ?>
				<script src="https://cdn.ckeditor.com/<?=editor4V?>/standard/ckeditor.js"></script><?php
				break;
			case 'standard-all': ?>
				<script src="https://cdn.ckeditor.com/<?=editor4V?>/standard-all/ckeditor.js"></script><?php
				break;
			case 'full': ?>
				<script src="https://cdn.ckeditor.com/<?=editor4V?>/full/ckeditor.js"></script><?php
				break;
			case 'full-all': ?>
				<script src="https://cdn.ckeditor.com/<?=editor4V?>/full-all/ckeditor.js"></script><?php
				break;
			case 'classic': ?>
				<script src="https://cdn.ckeditor.com/ckeditor5/<?=editor5V?>/classic/ckeditor.js"></script><?php
				break;
			case 'inline': ?>
				<script src="https://cdn.ckeditor.com/ckeditor5/<?=editor5V?>/inline/ckeditor.js"></script><?php
				break;
			case 'balloon': ?>
				<script src="https://cdn.ckeditor.com/ckeditor5/<?=editor5V?>/balloon/ckeditor.js"></script><?php
				break;
			case 'balloon-block': ?>
				<script src="https://cdn.ckeditor.com/ckeditor5/<?=editor5V?>/balloon-block/ckeditor.js"></script><?php
				break;
			case 'decoupled-document': ?>
				<script src="https://cdn.ckeditor.com/ckeditor5/<?=editor5V?>/decoupled-document/ckeditor.js"></script><?php
				break;
		}
		unsetSession('editorLink');
	}
}
function redefinedCss(){?>
  <style type="text/css">
	.alert.alert-with-icon {
		margin-top: 15px; /*43px*/
	}
    .icon-spin {
      display: inline-block;
      -webkit-animation: spin 2s infinite linear;
      animation: spin 2s infinite linear;
      margin: 0 8px;
    }
    .block2{
      margin-bottom: 12px;
    }
  </style><?php
}
function uploadFileCssHead(){?>
	<style type="text/css">
    .image-preview-input {
      position: relative;
      overflow: hidden;
      margin: 0px;
      color: #333;
      background-color: #fff;
      border-color: #ccc;
    }
    .image-preview-input input[type=file] {
      position: absolute;
      top: 0;
      right: 0;
      margin: 0;
      padding: 0;
      font-size: 20px;
      cursor: pointer;
      opacity: 0;
      filter: alpha(opacity=0);
    }
    .image-preview-input-title {
      margin-left:0px;
    }
  </style><?php
}
function jsForTemplateHead(){?>
  <script>
    function closeFlashy(id, fade="fadeOutDown", delay=3000) {
      $("#"+id).addClass(fade);
      setTimeout(function(){
        $("#"+id).remove();
      }, delay);
    }
    function closeAfterDelay(functionName, delay=0, ...args) {
      setTimeout(functionName, delay, args);
	}
	//Date and time functions
	function isDate(data){
		var date = new Date(data);
		return (date instanceof Date && !isNaN(date.valueOf()));
	}
	function isTime(time){
		return (isDate("1970-01-01 " + time) && time.indexOf(":") != -1 );
	}
	function isDateTime(dateTime){
		return (isDate(dateTime) && dateTime.indexOf(":") != -1);
	}
	function isNumeric(n){
		return !isNaN(parseFloat(n)) && isFinite(n);
	}
  </script>
  <?php
	editorLinkForHead();
	if(session('ionRangeSliderJs')){
    	echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css"/>';
		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"></script>';
		echo '<script src="tools/assets/js/ionRangeSliderJs.js"></script>';
		unsetSession('ionRangeSliderJs');
	}
	if(session('typeahead')){
		echo '<script src="tools/assets/js/plugins/typeahead.bundle.js" type="text/javascript"></script>';
		unsetSession('typeahead');
	}
	if(scrollRevealDefaultImport || session('scrollReveal')){
		echo '<script src="https://unpkg.com/scrollreveal"></script>';
		initAddedScrollEffects();
		unsetSession('scrollReveal');
	}
}
function jsForTemplateFooter(){
	if(notEmpty($recapArr = session("recaptcha"))){
		echo "<script src='https://www.google.com/recaptcha/api.js?render=<?=recaptchaPublicKey?>'></script>";
		foreach($recapArr as $recap)
			echo grecaptcha(submitName, $recap);
		unsetSession("recaptcha");
	}
	if(notEmpty($tooltipArr = session("tooltip"))){
		echo '<script src="tools/assets/js/core/popper.min.js"></script>
		<script>';
		foreach($tooltipArr as $tooltip){
			$id = $tooltip[0];
			if($tooltip[2])
				echo "$('#$id').tooltip('$tooltip[2]');";
			echo "$('#$id').tooltip('$tooltip[1]');";
		}
		echo "</script>";
		unsetSession("tooltip");
	}
	if(notEmpty(($swiperArr = session("swiper")))){
		//sd(lorem());
		echo '<script>';
		foreach($swiperArr as $swiper){
			echo $swiper;
		}
		echo "</script>";
		unsetSession("swiper");
	}
	if(notEmpty($scrollEffectArr = session('scrollEffectArr'))){
		echo '<script>';
		foreach($scrollEffectArr as $scrollEffect){
			echo $scrollEffect;
		}
		echo "</script>";
		unsetSession('scrollEffectArr');
	}
}
function cssForTemplate(){
	uploadFileCssHead();
	redefinedCss();
	if(notEmpty(session("swiper"))){
		echo '<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>';
		echo '<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css"/>';}	?>

	<style>
    .swiper-container {
      width: 100%;
      height: 100%;
    }

    .swiper-slide {
      text-align: center;
      font-size: 18px;
      background: #fff;

      /* Center slide text vertically */
      display: -webkit-box;
      display: -ms-flexbox;
      display: -webkit-flex;
      display: flex;
      -webkit-box-pack: center;
      -ms-flex-pack: center;
      -webkit-justify-content: center;
      justify-content: center;
      -webkit-box-align: center;
      -ms-flex-align: center;
      -webkit-align-items: center;
      align-items: center;
    }
  </style><?php
}

function test(){
	$swipe = new Swiper(".gallery-thumbscube, effect: 'flip', pagination");
	//echobr($swipe->getCode());

	$swipe2 = new Swiper();
    $swipe2->setClass(".gallery-thumbscube");
	$swipe2->cube(true, 15); 
	$swipe2->pagination(); 
	$swipe2->addSimpleAtt("slidesPerView: 2,spaceBetween: 50, direction: 'vertical'"); 
	
	echobr($swipe2->getCode());
}

/**
 * $other : thumbs, zoom, nested
 * $idFieldName : it es for order and can be void
 * La partie Css se trouve dans la fonction: cssForTemplate de view&ctrTools.php
 */
function buildCarousselle($tableName, $srcFieldName, $altFieldName = "", $others = "", $whereAssoArray = array(), $parentFieldName = "parent", $rtl = false, $hToV = false, $idFieldName = "id", $isImg = false){

	$orderBy = $parentFieldName.ternary2($idFieldName, ",");
	$rtl = ternary($rtl, ' dir="rtl"', "");
	$thumbsdiv=$thumbsOpen=$thumbsClose="";
	$thumbCase="";
	$data = model()->get($tableName, $whereAssoArray, $orderBy);
	 
	$parents = parents($data);
	$rootsId = getKeys($parents)[0];

	if(is_string($others))
		$others = toArray($others);
	$class2 = "";
	//if(get($parents, $routeId)){
	if(in("thumbs", $others)){
		$thumbsOpen= ' <div class="swiper-container gallery-thumbs"> <div class="swiper-wrapper">';
		$thumbsClose= '</div></div>';
		$class2 .= " gallery-top";
	}
	if(in("nested", $others) || $hToV){
		$class2 .= " swiper-container-".($hToV?'v':'h');
		$levelLimit = 2;
	}else $levelLimit = 1;
	if(in("zoom", $others))
		$zoom = true;
	else $zoom = false;	
	?>

	<div class="swiper-container<?=$class2.$rtl?>" style="height:200px">
	<div class="swiper-wrapper"><?php

	foreach($parents[$rootsId] as $node){
		if($isImg)
			$imgOrText = " src='$node[$srcFieldName]' alt='$node[$altFieldName]'";
		else
			$imgOrText = $node[$srcFieldName];

		if($thumbsOpen && $node[$parentFieldName] == $rootsId)
			$thumbsdiv .= "<img class='swiper-slide'$imgOrText>";
    	
		$thumbCase=$thumbsOpen.$thumbsdiv.$thumbsClose;
		$level = 1;
		insertChildren($data, $node, $parents, $level, $zoom, $hToV, $levelLimit, $parentFieldName, $idFieldName, $rootsId, $srcFieldName, $altFieldName, $isImg);		
	}
	//}?>
	</div>
	<div class="swiper-pagination swiper-pagination-<?=$hToV?'v':'h'?>"></div>
	<div class="swiper-button-next swiper-button-black"></div>
    <div class="swiper-button-prev swiper-button-black"></div>
	</div> 
	
	<?=$thumbCase?>
<?php
}
function insertChildren($data, $node, $parents, $level, $zoom, $hToV, $levelLimit, $parentFieldName, $idFieldName, $rootsId, $srcOrTextFieldName, $altFieldName, $isImg){
	if($level > $levelLimit)
		return;
	$level ++;
	$id = $node[$idFieldName];
	$isChild = $node[$parentFieldName] > $rootsId;
	$isParent = isset($parents[$id]);
	if($isParent && $level <= $levelLimit) { ?>
		<div class="swiper-slide"> 
		<div class="swiper-container swiper-container-<?=$hToV?"h":"v"?>"> 
		<div class="swiper-wrapper"> <?php
			foreach($parents[$id] as $fils){
				insertChildren($data, $node, $parents, $level, $zoom, $hToV, $levelLimit, $parentFieldName, $idFieldName, $rootsId, $srcOrTextFieldName, $altFieldName, $isImg);
			}?>
		</div> 
		<div class="swiper-pagination swiper-pagination-<?=$hToV?"h":"v"?>"></div>
		</div>
		</div> 
		<?php
	}else{
		if($isImg)
			$imgOrText = "src='$node[$srcOrTextFieldName]' alt='$node[$altFieldName]'";
		else
			$imgOrText = $node[$srcOrTextFieldName];

		echo '<div class="swiper-slide">';
		if($zoom)
			echo '<div class="swiper-zoom-container">'.$imgOrText.'</div>';
		else
			echo $imgOrText;
		echo '</div>';
	}
}
/**
 * Ex1 : $swipe = new Swipe(".gallery-thumbscube, effect: 'flip', pagination");
 *		echobr($swipe->getCode());
 * Ex2 : $swipe2 = new Swipe("flip, pagination, navigation");
 * 		echobr($swipe2->getCode());
 * Ex3 : $swipe2 = new Swipe("cube, pagination, navigation");
 * 		echobr($swipe2->getCode());
 * Ex4 : $swipe4 = new Swipe();
 *		$swipe4->setClass(".gallery-thumbscube");
 *		$swipe4->cube(true, 15); 
 *		$swipe4->pagination(); 
 *		$swipe4->addSimpleAtt("slidesPerView: 2,spaceBetween: 50, direction: 'vertical'"); 
 *		echobr($swipe4->getCode());
	
 *Classes possible: .swiper-container | .swiper-container-h' | '.swiper-container-v' | .gallery-top | .gallery-thumbs | 

 simpleAtt : 
 *slidesPerView: 2
 *spaceBetween: 50, 
 *direction: 'vertical'
 *effect: 'flip' | 'fade',
 *speed: 600,
 *centeredSlides: true, 
 *freeMode: true,  //fluide
 *grabCursor: true, // grab Cursor main...
 *loop: true,
 *parallax: true,
 *debugger: true, // Enable debugger
 *lazy: true, // Enable lazy loading
 *slidesPerColumn: 2,    
 *slidesPerGroup: 3,
 *loopFillGroupWithBlank: true,
 *keyboard: {enabled: true},
 *autoHeight: true, //enable auto height

 méthodes : ... 

 */

class Swiper{ 
	
	public function __construct(){
		if(notEmpty($arg = func_get_args())){
			$code_ = $this->builder($arg);
			if(in("container-v", $this->class))
				$swiper = "swiperV";
			elseif(in("container-h", $this->class))
				$swiper = "swiperH";
			else
				$swiper = "swiper";
			$this->code = "var $swiper=new Swiper('".$this->class."',{".$code_."});";
			sessionAppendArr("swiper", $this->code);
			return $this->code;
		}
	}
	public function addSimpleAtt():string {
		return $this->simpleAtt .= $this->builder(func_get_args());
	}
	public function simpleAtt():string {
		return $this->simpleAtt .= $this->builder(func_get_args());
	}
	private function builder($arg):string {
		$code_ = "";
		foreach ($arg as $value) {
			foreach (toArray($value) as $val2){
				if($val2[0]==".")
					$this->class = $val2;
				else if(in(":", $val2))
					$code_ .= $val2.",";
				else if(in($val2, ["fade", "flip"] ))
					$code_ .= "effect:'$val2',";
				else if(in($val2, ["vertical", "horizontal"] ))
					$code_ .= "direction:'$val2',";
				else
					$code_ .= $this->$val2();
			}
		}
		return $code_;
	}
	private function slidesPerView(int $slidesPerView = 1){
		return "slidesPerView:$slidesPerView,";
	}	
	private function spaceBetween(int $spaceBetween = 20){
		return "spaceBetween:$spaceBetween,";
	}
	private function direction($direction = "horizontal"){
		return "direction:'".trimPlus($direction, "'")."',";
	}
	private function effect($effect = "flip"){
		return "effect:'".trimPlus($effect, "'")."',";
	}
	private function centeredSlides(bool $centeredSlides = true){
		return $centeredSlides ? "centeredSlides:true," : "";
	}
	private function freeMode($freeMode = true){
		return $freeMode ? "freeMode:true," : "";
	}
	private function grabCursor($grabCursor = true){
		return $grabCursor ? "grabCursor:true," : "";
	}
	private function loop($loop = true){
		return $loop ? "loop:true," : "";
	}
	private function speed($speed = "600"){
		return "speed:$speed,";
	}
	private function parallax($parallax = true){
		return $parallax ? "parallax:true," : "";
	}
	private function debugger($debugger = true){
		return $debugger ? "debugger:true," : "";
	}
	private function lazy($lazy = true){
		return $lazy ? "lazy:true," : "";
	}

	
	private function slidesPerColumn($slidesPerColumn = 2){
		return "slidesPerColumn:$slidesPerColumn,";
	}
	private function slidesPerGroup($slidesPerGroup = 3){
		return "slidesPerGroup:$slidesPerGroup,";
	}
	private function loopFillGroupWithBlank($loopFillGroupWithBlank = true){
		return $loopFillGroupWithBlank ? "loopFillGroupWithBlank:true," : "";
	}
	private function autoHeight($autoHeight = true){
		return $autoHeight ? "autoHeight:true," : "";
	}
	private function keyboard($keyboard = true){
		return $keyboard ? "keyboard:{enabled: true}," : "";
	}

	public function setClass(string $class = ".swiper-container"){
		$this->class = $class;
	}
   // public function __call($method, $args){}
	public $class = ".swiper-container";
	public $code = "";
	public $thumbs = ""; 
	public $cube = ""; 
	public $pagination = ""; 
	public $paginDynamic = "";
	public $paginCustom = "";
	public $paginProgressbar = "";
	public $navigation = "";
	public $effectCoverflow = "";
	public $responsive = "";
	public $autoplay = "";
	public $mousewheel = "";
	public $simpleAtt = "";

	public function getCode($buildAgain = false):string {
		$attrs = get_object_vars($this);
		unset($attrs["class"]);
		if($buildAgain || $this->code == "") {
			if(in("container-v", $this->class))
				$swiper = "swiperV";
			elseif(in("container-h", $this->class))
				$swiper = "swiperH";
			else
				$swiper = "swiper";
			$this->code = "var $swiper=new Swiper('".$this->class."',{";
			$this->code .= trimPlus(toString($attrs, ","))."});";
			sessionAppendArr("swiper", $this->code);
		}
		return $this->code;
	}

	public function thumbs(){
		return $this->thumbs = "thumbs:{swiper: galleryThumbs}";
	}
	public function cube(bool $shadow = true, int $shadowOffset = 20, float $shadowScale = 0.94, bool $grabCursor = true, bool $slideShadows = true):string{
		$result = "effect:'cube',";
		if($grabCursor)	$result .= "grabCursor:true,";
		$result .= "cubeEffect:{";
		if($shadow)	$result .= "shadow:true,";
		if($slideShadows)	$result .= "slideShadows:true,";
		$result .= "shadowOffset:$shadowOffset,shadowScale:$shadowScale},";
		return $this->cube = $result;
	}
	public function pagination(bool $clickable = true):string{
		$result = "pagination:{el:'.swiper-pagination',";
		if($clickable)
			$result .= "clickable:true,";
		return $this->pagination = $result.= "},";
	}
	public function pagin(bool $clickable = true):string{
		return $this->pagination($clickable);
	}
	public function paginDynamic(bool $clickable = true):string{
		$result = "pagination:{el:'.swiper-pagination',dynamicBullets: true,";
		if($clickable)
			$result .= "clickable:true,";
		return $this->paginDynamic = $result.= "},";
	}
	/**
	 * On peut customiser cette classe css :swiper-pagination-bullet
	 */
	public function paginCustom(bool $clickable = true):string{
		$result = "pagination:{el:'.swiper-pagination',";
		if($clickable)
			$result .= "clickable:true,";
		$result .= "renderBullet: function(index, className) {return'<span class=\"'+className+'\">'+(index + 1)+'</span>';},";
		return $this->paginCustom = $result;
	}
	public function paginBullet(bool $clickable = true):string{
		return $this->paginCustom($clickable);
	}
	public function paginProgressbar():string{
		return $this->paginProgressbar = "pagination:{el:'.swiper-pagination',type:'progressbar'},";
	}
	public function navigation():string{
		return $this->navigation = "navigation:{nextEl:'.swiper-button-next',prevEl: '.swiper-button-prev'},";
	}
	public function effectCoverflow(int $rotate = 50, int $stretch = 0, int $depth = 100, int $modifier = 1, bool $slideShadows = true):string{
		return $this->effectCoverflow = "effect:'coverflow',grabCursor:true,centeredSlides:true,slidesPerView:'auto',coverflowEffect:{rotate:$rotate,stretch:$stretch,depth: $depth,modifier:$modifier,slideShadows:Boolean($slideShadows),},";
	}
	public function coverflow(int $rotate = 50, int $stretch = 0, int $depth = 100, int $modifier = 1, bool $slideShadows = true):string{
		return $this->effectCoverflow($rotate, $stretch, $depth, $modifier, $slideShadows);
	}
	public function responsive($slidesPerView = 1, $spaceBetween = 10, $slidesPerView320 = 2, $spaceBetween320 = 10, $slidesPerView640 = 2, $spaceBetween640 = 20, $slidesPerView768 = 4, $spaceBetween768 = 40, $slidesPerView1024 = 5, $spaceBetween1024 = 50):string{
		return $this->responsive = "breakpoints:{slidesPerView: $slidesPerView,spaceBetween:$spaceBetween,320:{slidesPerView:$slidesPerView320,spaceBetween: $spaceBetween320},640:{slidesPerView: $slidesPerView640,spaceBetween:$spaceBetween640},768: {slidesPerView:$slidesPerView768,spaceBetween: $spaceBetween768},1024: {slidesPerView:$slidesPerView1024,spaceBetween:$spaceBetween1024}},";
	}
	public function autoplay(int $delay = 2500, bool $disableOnInteraction = false):string{
		return $this->autoplay = "autoplay:{delay: $delay, disableOnInteraction: Boolean($disableOnInteraction)},";
	}
	public function mousewheel(bool $forceToAxis = true, $mousewheel = true):string{
		return $this->mousewheel = "mousewheel:{mousewheel:Boolean($mousewheel),forceToAxis:Boolean($forceToAxis)},";
	}
	/**
	 *  Scrollbar progress on bottom
	 */	
	public function scrollbar(bool $hide = true):string{
		return $this->scrollbar = "scrollbar:{<div class='swiper-scrollbar'></div>el:'.swiper-scrollbar',hide: Boolean($hide)},";
	}
}

function js($fctName, $arg1 = "", $arg2 = ""){?> <!-- TODO on peut ajouter d'autres parametres ... -->
	<script>
		<?php
		if($arg1)
			see("$fctName('".escaper($arg1, "'")."')");
		elseif($arg2)
			see("$fctName('".escaper($arg1, "'")."', '".escaper($arg2, "'")."')");
		else
			see("$fctName()");		
		?>
	</script><?php
}
function jsAlert($arg1 = "", $arg2 = ""){
	js("alert", $arg1, $arg2);
}
function alert($arg1 = "", $arg2 = ""){
	js("alert", $arg1, $arg2);
}
function str_replace2($from, $to, $content, int $limit = 1){
    $from = '/'.preg_quote($from, '/').'/';
    return preg_replace($from, $to, $content, $limit);
}
function str_ireplace2($from, $to, $content, int $limit = 1){
    $from = '/'.preg_quote($from, '/').'/i';
    return preg_replace($from, $to, $content, $limit);
}

// outputs '123def abcdef abcdef'
/**
 * PapeAdamaMboup => _pape_adama_mboup and papeAdamaMboup => pape_adama_mboup
 */
function camelToSnakeCase(string $str){
	$str = toArrayUpper($str);
	$result = ctype_upper($str[0][0]) ? "_" : "";
	$result .= implode("_", toLower($str));
	echo $result;
}
//_pape_adama_mboup => PapeAdamaMboup and pape_adama_mboup => papeAdamaMboup
function snakeToCamelCase(string $str){
	$str = toArray($str, "_");
	$result = "";
	$i = 0;
	foreach ($str as $value) {
		$i++;
		if( ! notEmpty($value))
			continue;
		if($i == 1)
			$result .= $value;
		else $result .= ucfirst($value);
	}
	return $result;
}
function makeRoute($url, $ctrlOrView, $name="", $verbose = true){
	$ctrlOrView = snakeToCamelCase($ctrlOrView);
	$name = snakeToCamelCase($name);
	$route = "\nroutePost(".'"'.str_replace('"', '\"', $url).'"'.", '$ctrlOrView', '$name');";
	$routeFile = fopen("routes.php", "a");
	fwrite($routeFile, $route);
	fclose($routeFile);
	if($verbose){
		$title = "The route (in <b>routes.php</b>) is created";
		card2($title, $route);
	}
}
function makeModel($modelOrDbtabName, $mdlPkg = "", $superModelComplet = "", $verbose = true){
	$superModelComplet = tern($superModelComplet, defaultSuperModel).".php";
	$superModelComplet = str_replace(".php.php", ".php", $superModelComplet);
	$mdlPkg = tern($mdlPkg, mdlPkg);

	$modelName = mdlPrefix.ucfirst(snakeToCamelCase($modelOrDbtabName));
	$model = 
'<?php

require_once("'.$superModelComplet.'");

class '.$modelName.' extends '.first(last($superModelComplet, "/"), ".php").' {
	
	protected $table = "'.$modelOrDbtabName.'";

}

?>';

	$modelFile = fopen($mdlPkg.$modelName.".php", "w");
	fwrite($modelFile, $model);
	fclose($modelFile);
	if($verbose){
		$model = htmlentities($model);
		$model = str_replace("
", "<br>", $model);
		$model = str_replace("	", nbspOb(6), $model);
		$title = "The model <b>$mdlPkg$modelName.php</b> is created";
		card2($title, $model);
	}
}
function makeView($viewName, $viewPkg = "", $verbose = true){
	$viewPkg = tern($viewPkg, viewPkg);

	$viewName = str_replace(".php", "", $viewName);
	$viewName = snakeToCamelCase($viewName);
	$viewName = viewPrefix.$viewName;
	$viewName = str_replace(viewPrefix.viewPrefix, viewPrefix, $viewName);
	$view = 
'<?php

	echo $data;
	
?>';

	$viewFile = fopen($viewPkg.$viewName.".php", "w");
	fwrite($viewFile, $view);
	fclose($viewFile);
	if($verbose){
		$view = htmlentities($view);
		$view = str_replace("
", "<br>", $view);
		$view = str_replace("	", nbspOb(6), $view);
		$title = "The view <b>$viewPkg$viewName.php</b> is created";
		card2($title, $view);
	}
}
/** si $ctrlOrDbtabName == "user" alors c'est une génération d'authentification et de régistration TODO lang */
function makeController($ctrlOrDbtabName, $ctrlPkg = "",
	$superCtrlComplet = "", $viewName = "",
	$modelNameComplet = "", $forAuth = false, $verbose = true){
  $ctrlOrDbtabName = ucfirst(snakeToCamelCase($ctrlOrDbtabName));
  if($forAuth)
    $authRegistration = file_get_contents("tools/ModelOfUserCtrl.php");
  else
    $authRegistration = "";
	
	$superCtrlComplet = tern($superCtrlComplet, defaultSuperController);
	$superCtrlComplet = str_replace(".php.php", ".php", $superCtrlComplet.".php");
	$superCtrlClass = first(last($superCtrlComplet, "/"), ".php");
	
  $reqModel = tern($modelNameComplet, mdlPkg.mdlPrefix.$ctrlOrDbtabName);
	$reqModel = str_replace(".php.php", ".php", $reqModel.'.php');
	$reqModel = str_replace(mdlPrefix.mdlPrefix, mdlPrefix, $reqModel);
	$modelClass = first(last($reqModel, "/"), ".php");

	$viewName = tern($viewName, lcfirst($ctrlOrDbtabName));
	
	$ctrlPkg = tern($ctrlPkg, ctrlPkg);
	
	$ctrlName = str_replace(ctrlPrefix.ctrlPrefix, ctrlPrefix, ctrlPrefix.$ctrlOrDbtabName);
  
	$modelOfCtrl = file_get_contents("tools/ModelOfCtrl.php");
	$modelOfCtrl = str_replace(
		["_Controller_", "_Controller2_", "_Model_", "_Model2_", "_viewName_", "_newController_", "_authencation_registration_"],
		[$superCtrlComplet, $superCtrlClass, $reqModel, $modelClass, $viewName, $ctrlName, $authRegistration]	,
		$modelOfCtrl);
	

	$ctrlFile = fopen($ctrlPkg.$ctrlName.".php", "w");
	fwrite($ctrlFile, $modelOfCtrl);
	fclose($ctrlFile);

	if($verbose){
		$modelOfCtrl = htmlentities($modelOfCtrl);
		$modelOfCtrl = str_replace("
", "<br>", $modelOfCtrl);
		$modelOfCtrl = str_replace("	", nbspOb(6), $modelOfCtrl);
		$title = "The controller <b>$ctrlPkg$ctrlName.php</b> is created";
		card2($title, $modelOfCtrl);
	}
}
function makeDbMvcr2($tableIdInTablesInfo){
	makeDbMvcr([$tableIdInTablesInfo]);
}

function makeDbMvcr($idStartOrIdList = "", $idEnd = "", 
	$crudsdf = ["create", "read", "update", "delete", "show", "download", "print"], 
	$tablesInfo = "", $verbose = true){

	$tablesInfo = tern($tablesInfo, "tables_info");
	
	ob_start();
	empty([]);
	if(is_array($idStartOrIdList) && notEmpty($idStartOrIdList)){
		foreach ($idStartOrIdList as $idTable) {
			$tableInfo = model()->first_obj($tablesInfo, $idTable);
			makeDbTabMvcr($tableInfo, $crudsdf, false, $verbose);		
		}
	}else{
		$limit = ternary(notEmpty($idStartOrIdList), ($idStartOrIdList-1));
		if(notEmpty($idEnd)){
			if(notEmpty($idStartOrIdList)){
				$idEnd = $idEnd - ($idStartOrIdList-1);
				$limit .= ", ".$idEnd;
			}else
				$limit = "0, ".$idEnd;
		}
		
		foreach (model()->get_obj($tablesInfo, [], "", $limit) as $tableInfo) {
			makeDbTabMvcr($tableInfo, $crudsdf, false, $verbose);		
		}
	}
	$data = ob_get_clean();
	controller()->view("view", $data);
}
function makeDbTabMvcr($tableInfo, $crudsdf = [], $forAuth = false, $verbose = true){
  $tabName = get($tableInfo, "name");
  makeModel($tabName, "", "", $verbose);
	makeView($tabName, "", $verbose);
	makeController($tabName, "", "", "", "", $forAuth, $verbose);

	$crudsdf = ternary(notEmpty($crudsdf), $crudsdf,
    ["create", "read", "update", "delete", "show", "download", "print"]);
  $libelle = getLibelle($tableInfo);
	foreach ($crudsdf as $one) {
		makeRoute(url(msgs[$one], $libelle), 
			$tabName."/".$one, $tabName.ucfirst($one), $verbose);			
  }
  if($forAuth){
    foreach (["registration", "logIn", "logOut"] as $one) { //TODO mettre dans config.php
      makeRoute(url(msgs[$one], ""), 
        $tabName."/".$one, $tabName.ucfirst($one), $verbose);			
    }
  }
}

/**$data doit etre trie sur parent dans l'ordre croissant.
Min of $levelLimit = 1  */
function buildMenu($tableName, $routeId, $levelLimit = 100, $whereAssoArray = array(),  $orderBy = "parent, id"){
	$data = model()->get($tableName, $whereAssoArray, $orderBy);?>
	<ul class="nav"><?php

	$userTypeId = userTypeId();
	$parents = parents($data);
	//if(get($parents, $routeId)){
	foreach($parents[$routeId] as $node){
		
		$level = 1;
		insertNode($data, $node, $parents, $level, $levelLimit, $userTypeId, "");

	}
	//}?>
	
	</ul><?php
}
function insertNode($data, $node, $parents, $level, $levelLimit, $userTypeId, $ml0){
	if($level > $levelLimit)
		return;
	$userTidL = $node['user_type_id_list'];
	if($userTidL && $userTypeId && in($userTypeId, $userTidL, ","))
		return;
	$level ++;
	$id = $node['id'];
	$isChild = $node['parent'] > 0;
	$plus8 = 8;
	$isParent = isset($parents[$id]);?>
	<li class="nav-item"> <?php
	if($isParent && $level <= $levelLimit) { 
		if($isChild)
			$plus8 = 0;
		?>
		<a data-toggle="collapse" href="#nodeParent<?=$id?>" class="nav-link collapsed mt-0<?=$ml0?>" aria-expanded="false">
			<i class="<?= get($node, 'icon_type', '', 'material-icons')?>"><?= get($node, 'icon_name')?></i>
			<p> <?= $node['libelle']?> 
				<b class="caret"></b>
			</p>
		</a>
		<div class="collapse" id="nodeParent<?=$id?>" aria-expanded="false" style="padding-left:0px; margin-left:<?= (subMenuPaddingLeft+$plus8)?>px; border-left:<?= menuBorderLeft?>px solid">
			<ul class="nav mt-0"><?php
			foreach($parents[$id] as $fils){
				insertNode($data, $fils, $parents, $level, $levelLimit, $userTypeId, " ml-2");
			}
			?>
			</ul>
		</div><?php
	}else{
		$href = str_replace(["'", '"'], "", $node['link']);
		if(in("route(", $href)){
			$href = route(between($href, "(", ")"));
		}
		elseif( ( $href0 = route($href) ) )
			$href = $href0;
		?>
		<a class="nav-link mt-0<?=$ml0?>" href="<?= $href?>">
			<i class="<?= get($node, 'icon_type', '', 'material-icons')?>"><?= get($node, 'icon_name')?></i>
			<p><?= $node['libelle']?></p>
		</a><?php				
	} ?>
	</li> <?php
}
function parents($data, $parentField = "parent"){
	$parents = [];
	foreach($data as $node)
		$parents[$node[$parentField]][] = $node;
	return $parents;
}


function addAuthToTemplate($templatePath = "tools/template.php", $keyWordInTemplate = "<!-- auth_and_registration -->"){
  $str='
		          <?php //authentification and registration part
              if(userId()){ ?>
                <li class="nav-item dropdown">
                  <a class="nav-link" href="javascript:;" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="material-icons">person</i>
                    <p class="d-lg-none d-md-block">
                      Account
                    </p>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                    <a class="dropdown-item" href="#">Profile</a>
                    <a class="dropdown-item" href="#">Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?=route("userLogOut")?>"><?=msgs["logOut"]?></a>
                  </div>
                </li> <?php 
              }else{  ?>
                <li class="nav-item dropdown">
                  <a href="<?=route("userLogIn")?>" class="nav-link">
                    <?=msgs["logIn"]?>
                  </a>
                </li><?php
                if(registrationFree){ ?>
                  <li class="nav-item dropdown">
                    <a href="<?=route("userRegistration")?>" class="nav-link">
                      <?=msgs["registration"]?>
                    </a>
                  </li><?php
                } 
              } ?>
  ';  
  $templateOld = file_get_contents($templatePath);

  //sdc($templatePath, htmlspecialchars ($templateOld));
  $template = fopen($templatePath, "w");
	fwrite($template, str_replace($keyWordInTemplate, $str, $templateOld));
	fclose($template);
}
function createTablesInfo($tablesInfo = ""){
  $tablesInfo = tern($tablesInfo, "tables_info");
  $sql = "CREATE TABLE IF NOT EXISTS `$tablesInfo`  (
	`id` int(11) NULL,
	`id_field_name` varchar(50) DEFAULT 'id',
	`name` varchar(50) NOT NULL DEFAULT '',
	`libelle` varchar(255) NULL DEFAULT '',
	`libelle_in_plural` varchar(255) NULL DEFAULT '',
	`libelle_abbr` varchar(50) DEFAULT '',
	`ajax_validation` varchar(50) DEFAULT NULL,
	`icon_name` varchar(50) DEFAULT '',
	`icon_type` enum('','material-icons','fa-icons') DEFAULT '',
	`field_not_to_show_list_for_create` text DEFAULT '',
	`field_not_to_show_list_for_read` text NULL,
	`field_not_to_show_list_for_show` text NULL,
	`other_tab` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'fields in other tables (to make joins)',
	`line_number` enum('','#','N°') DEFAULT '#' COMMENT '0 = no (false), 1 = yes (true)',
	`search_zone` enum('0','1') DEFAULT '1' COMMENT '0 = no (false), 1 = yes (true)',
	`total_formul` varchar(255) DEFAULT NULL,
	`range_field_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'for research (date, time, int, real... type of fields)',
	`table_class` varchar(255) DEFAULT NULL,
	`number_of_lines_per_page` smallint(6) DEFAULT 20,
	`create_route_name` varchar(50) DEFAULT NULL,
	`read_route_name` varchar(50) DEFAULT NULL,
	`show_route_name` varchar(50) DEFAULT '',
	`show_type` enum('','blank','modal') DEFAULT '',
	`update_route_name` varchar(50) DEFAULT '',
	`update_type` enum('','blank','modal') DEFAULT '',
	`delete_route_name` varchar(50) DEFAULT '',
	`download_route_name` varchar(50) DEFAULT '',
	`print_route_name` varchar(50) DEFAULT '',
	`print_type` enum('','blank','modal') DEFAULT '',
	`stat_desc_col_list` varchar(255) DEFAULT NULL COMMENT 'ex : table_name.id, prix ...',
	`update_back_to_tab_list` enum('0','1') DEFAULT NULL COMMENT '0 = no (false), 1 = yes (true)',
	`user_type_id_list_for_create` varchar(30) DEFAULT NULL,
	`user_type_id_list_for_read` varchar(30) DEFAULT NULL,
	`user_type_id_list_for_show` varchar(30) DEFAULT NULL,
	`user_type_id_list_for_update` varchar(30) DEFAULT NULL,
	`user_type_id_list_for_delete` varchar(30) DEFAULT NULL,
	`user_type_id_list_for_download` varchar(30) DEFAULT NULL,
	`user_type_id_list_for_print` varchar(30) DEFAULT NULL,
	`typeahead_search_fields` varchar(1000) DEFAULT NULL,
	`typeahead_display` varchar(250) DEFAULT NULL,
	`typeahead_temp_sugg` varchar(500) DEFAULT NULL,
	`typeahead_temp_empty` varchar(500) DEFAULT NULL,
	`add_create_infos` varchar(200) DEFAULT NULL
  ) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;";
  model()->query($sql);	
}
function createFieldsInfo($fieldsInfo = ""){
  $fieldsInfo = tern($fieldsInfo, "fields_info");
  $sql = "
  CREATE TABLE IF NOT EXISTS `$fieldsInfo` (
	`id` int(4) NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '0' COMMENT 'on peut mettre le nom de la table suivi de celui du champs',
	`label` text,
	`htlm_type` enum('button','checkbox','color','date','datetime-local','editorBasic','editorFull','editorFullAll','editorStandard','editorStandardAll','email','file','hidden','image','month','number','password','radio','range','reset','search','select','submit','tel','text','textarea','time','url','week') NOT NULL DEFAULT 'text',
	`select_first_option` varchar(100) DEFAULT NULL COMMENT '{".'"option_value", "option_text"}. Ex. {"0","-- Choisir"}'."',
	`dependance_field` varchar(100) DEFAULT '',
	`default_select_where` varchar(500) DEFAULT '',
	`placeholder` varchar(255) DEFAULT NULL,
	`col_number` enum('','1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL DEFAULT '',
	`read_label_col_number` enum('','1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL DEFAULT '3',
	`read_value_col_number` enum('','1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL DEFAULT '4',
	`new_line` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT '0 : pas de nouvelle ligne, laisser juste l''organisation générale; 1: se mettre sur une novelle ligne; 2',
	`description` varchar(255) DEFAULT NULL,
	`title` text COMMENT 'an eventuel tible of the field',
	`fieldset` enum('','start','end','start end') DEFAULT '',
	`fieldset_legend` text,
	`fieldset_other` varchar(500) NOT NULL,
	`html_id` varchar(50) DEFAULT NULL COMMENT 'id of the input tag',
	`html_class` varchar(100) DEFAULT NULL COMMENT 'class of the input tag. Can take several classes',
	`other_html` varchar(255) DEFAULT NULL,
	`ajax_param` varchar(500) DEFAULT NULL COMMENT 'Ex : ".'"ajax.php", "id="+ $(this).val(), "funtionName". Ce qui donnera : ajax("ajax.php", "id="+ $(this).val(), "funtionName"). Car la signature de la \\r\\nfunction est : ajax(urlOrRoute, data, successFunction, type="post") '."',
	`icon` varchar(255) DEFAULT NULL,
	`php_js_rule` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `Index 2` (`name`)
  ) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=latin1 COMMENT='Json is used to take into account the internalization'";
  model()->query($sql);	
}
/**
 *  Ex1 : onchange="<?= ajax("{'id': $(this).val()}", ajaxServiceFromDepart)?>"
 * ajax est ici une fonction php et parametre n'ont pas besoin de guillemets.
 *  Ex 2 : "pam <?php echo date(Y-m-d)?>"
 */

function evalStrContainingFct(string $str, $strIfError = "", $toEscapeCharList = ""){
	$str = str_replace("<?php echo", "<?=", trimPlus($str));
	while($str1 = between($str, "<?=", "?>", false)){
		$str0 = "<?=".$str1."?>";

		$str1 = trim(trimPlus($str1, ";"));
		$str1 = str_replace("$", "_dolar_", $str1); //pour enlever les dolar jquery
		if(first($str1, "(")) // en cas d'appelle de fonction
		$str1 = callFunctionFromString($str1);
		else
		notice($strIfError);
		$str1 = str_replace("_dolar_", "$", $str1); //pour remttre les dolar jquery
		if($toEscapeCharList){
		$str1 = escaper($str1, $toEscapeCharList);
		}

		$str = str_replace($str0, $str1, $str);
	}
	return $str;
}
function makeAuth($tablesInfo = "", $templatePath = "tools/template.php", 
	$keyWordInTemplate = "<!-- auth_and_registration -->", $verbose = true){
    
	$tablesInfo = tern($tablesInfo, "tables_info");

	addAuthToTemplate($templatePath, $keyWordInTemplate);

	$sql = "CREATE TABLE IF NOT EXISTS `user` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `first_name` varchar(100) NOT NULL DEFAULT '',
      `last_name` varchar(100) NOT NULL DEFAULT '',
      `login` varchar(255) NOT NULL DEFAULT '',
      `password` varchar(100) NOT NULL DEFAULT '',
      `user_type` tinyint(2) DEFAULT NULL,
      `tel_verified_at` timestamp NULL DEFAULT NULL,
      `email_verified_at` timestamp NULL DEFAULT NULL,
      `two_factor_secret` varchar(255) DEFAULT NULL,
      `two_factor_recovery_code` varchar(255) DEFAULT NULL,
      `remember_token` varchar(255) DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `Index 2` (`login`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

    CREATE TABLE IF NOT EXISTS `user_type` (
      `id` tinyint(2) NOT NULL AUTO_INCREMENT,
      `libelle` varchar(255) NOT NULL,
      `description` text,
      PRIMARY KEY (`id`),
      UNIQUE KEY `Index 2` (`libelle`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

    INSERT INTO `user_type` (`libelle`, `description`) VALUES
      ('Aministrateur', NULL),
      ('Simple', NULL);

	  CREATE TABLE IF NOT EXISTS `menu` (
      `id` tinyint(2) NOT NULL AUTO_INCREMENT,
      `libelle` varchar(255) NOT NULL,
      `parent` int,
      `link` text,
      PRIMARY KEY (`id`),
      UNIQUE KEY `Index 2` (`libelle`)
    ) 
  ";
  model()->query($sql);

  createTablesInfo($tablesInfo);	//if not existes

  $sql = "INSERT INTO `tables_info` (`id_field_name`, `name`, `libelle`, `libelle_in_plural`, `libelle_abbr`, `ajax_validation`, `icon_name`, `icon_type`, `field_not_to_show_list_for_create`, `field_not_to_show_list_for_read`, `field_not_to_show_list_for_show`, `other_tab`, `line_number`, `search_zone`, `total_formul`, `range_field_list`, `table_class`, `number_of_lines_per_page`, `create_route_name`, `read_route_name`, `show_route_name`, `show_type`, `update_route_name`, `update_type`, `delete_route_name`, `download_route_name`, `print_route_name`, `print_type`, `stat_desc_col_list`, `update_back_to_tab_list`, `user_type_id_list_for_create`, `user_type_id_list_for_read`, `user_type_id_list_for_show`, `user_type_id_list_for_update`, `user_type_id_list_for_delete`, `user_type_id_list_for_download`, `user_type_id_list_for_print`, `typeahead_search_fields`, `typeahead_display`, `typeahead_temp_sugg`, `typeahead_temp_empty`, `add_create_infos`) VALUES
  ('id', 'user', 'utilisateur', 'utilisateurs', '', NULL, '', '', 'id, tel_verified_at, email_verified_at, two_factor_secret, two_factor_recovery_code, remember_token, created_at, updated_at', '', '', '', '', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, '1', '3', '3', '3', '3', '3', '3', '3', NULL, NULL, NULL, NULL, NULL);";
 //TODO revenir ici et ajouter aussi user_type
 	model()->query($sql);

	$tableInfo = model()->first_obj($tablesInfo, ["name"=>"user"]);
  if(empty($tableInfo)){
	  throw new Exception("On n'a pas pu trouver les informations concernant la table user dans tables_info", 1);
  }
  ob_start();
	makeDbTabMvcr($tableInfo, [], true, $verbose);
  $data = ob_get_clean();
	controller()->view("view", $data);
}
function url(string $cruds, $tableLibelle){
	return trimPlus(ternary(msgs['crudWordFistOnRoute'],
		$cruds."-".$tableLibelle, $tableLibelle."-".$cruds), "-");
}
function model(){
	require_once ("tools/Model.php");	
	return Model::getInstance();
}
function controller($controllerNameComplet = "", $viewName=""){
	return objet(tern($controllerNameComplet, "tools/Controller.php"), "", "", $viewName);	
}
function objet($classNameComplet = "", $param1 = "", $param2 = "", $param3 = ""){
	$className = first(last($classNameComplet, "/"), ".php");
	require_once($classNameComplet);

	if(notEmpty($param3))
		return new $className($param1, $param2, $param3);
	elseif(notEmpty($param2))
		return new $className($param1, $param2);

	if(notEmpty($param1))
		return new $className($param1);
	else
		return new $className();	
}
  
function callFunctionFromString(string $str){ //TODO tableau pas encore pris en compte $str
	$fctName = first($str, "(");
	$param = between($str, "(", "", false);
	$param = trimPlus($param, ")");
	return ob()->$fctName($param);	
}
function callProcedureFromString(string $str){ //TODO tableau pas encore pris en compte $str
	$fctName = first($str, "(");
	$tmp = "";
	try {
		$param = between(trim($str), "(", "", false);
		$param = trimPlus($param, ")");
		if(notEmpty($param))
			echo ob()->$fctName($param);
		else
			echo ob()->$fctName();
		return true;
	} catch (\Throwable $th) {
		notice($th);
		return false;
	}
}
  /** Echo + retour à la ligne. ie que echoln.*/
function echobr($arg = ""){
	echo $arg."<br>";
}
/** Echo + retour à la ligne. ie que echobr.*/
function echoln($arg = ""){
	echobr($arg);
}
/** Echo + retour à la ligne. ie que echoln.*/
function println(){
	print0(func_get_args() );
}
/**
 * Affiche une concaténation des parametres en les séparant par une virgule + espace et avec un retour à la ligne à la fin. Vous pouvez mettre autant de parametres que vous voulez*/
function printV(){
	print0(func_get_args(), ", ", "");
}
/**
 * Affiche une concaténation des parametres en les séparant par un point virgule + espace et avec un retour à la ligne à la fin. Vous pouvez mettre autant de parametres que vous voulez
 */
function printP(){
	print0(func_get_args(), "; ", "");
}
/**
 * Affiche une concaténation des parametres en les séparant par un espace et avec un retour à la ligne à la fin. Vous pouvez mettre autant de parametres que vous voulez*/
function printE(){
	print0(func_get_args(), " ", "");
}
function print0($arg, $sep= ", ", $br="<br>"){
	$n = count($arg);
	$str = $arg[0];
	for ($i=1; $i < $n; $i++) {
		$str .= $sep . $arg[$i];
	}
	echo $str.$br;
}
/////////////////////////////////////

/**
  * La dimension d'un tableau, d'un objet, d'un tableau de tableaux, d'un tableau d'objet ... Rmq : la dimension d'un objet est 1.

  * Ex : 

  * dim ([1, 2, 3]) = dim([1=>1, 2=>1, 3=>1]) = 1

  * dim([ [1, 2, 3], [1, 2, 3], [1, 2, 3] ]) = 2

  * dim("chaine de caractères") = 0
*/
function dim($data){
	$dim = 0;
	while(is_array($data) or is_object($data) ){
		if(is_object($data))
			$data = (array) $data;
		$data = get($data, get(array_keys($data), 0));
		$dim ++;
	}
	return $dim;
}
function guillemetSwiper(string $str, $quot = '"'){
  return quotSwiper($str, $quot);
}
function quotSwiper(string $str, $quot = '"'){
  if(in($quot, $str))
    return str_replace($quot, guillemet, $str, $n);
  else
    return str_replace(guillemet, $quot, $str, $n);
}
/**
	* Transforme un tableau (de dimension 1), un objet ou un tableau d'objet en un talbeau associatif ou en un talbeau de talbeaux associatifs.
	* Ex 1 :

	* $array1 = ["Pape", "Adama", "MBOUP"];

	* $array2 = ["name", "middleN", "lastN"];

	* toAssArray($array1, $array2); Donne : 

	* Array ( [name] => Pape, [middleN] => Adama, [lastN] => MBOUP )

	* toAssArray($array1); Donne : 

	* Array( [Pape] => Pape, [Adama] => Adama,[MBOUP] => MBOUP )

	* toAssArray($array1, 0); ou toAssArray($array1, [1]);Donne : 

	* Array( [0] => Pape, [1] => Adama, [0] => MBOUP )

	* toAssArray($array1, 1); ou toAssArray($array1, [1]);Donne : 

	* Array ( [1] => Pape, [2] => Adama, [3] => MBOUP )

	* Ex 2 : toAssArray($tableauDobjet); donne un tableau de tableaux associatifs
 */
function toAssArray($data, $keys = []){
	$assArray = [];
	$dim = dim($data);
	if(count($keys) != 0 and $dim == 1){
	  $n = count($data); $n1 = count($keys);
	  $i = 0;
	  if($n == $n1 and is_array($keys)){
		foreach ($data as $key=>$value)
		  $assArray[$keys[$i++]] = $value;
	  }else if($n1 != 0){
		if(is_array($keys) and is_int($keys[0]))
		  $j = intval($keys[0]);
		else if(is_int($keys)) 
		  $j = intval($keys);
		if(isset($j))
		  foreach ($data as $key=>$value)
			$assArray[$j++] = $value;
		else notice("Le second parametre doit être un entier ou un array de même taille que le premier");
	  }else{
		notice("Le deuxième parametre est soit rien, soit un entier, soit un tableau de même taille que le premier parametre !<br>
		Sans oublier que le premier parametre doit être un tableau de dimension 1 si on envoie deux parametres !");
	  }
	}else{
	  if(is_object($data)){
      foreach ($data as $key=>$value)
        $assArray[$key] = $value;
	  }else if($dim == 1){
      foreach ($data as $key=>$value)
        $assArray[$value] = $value;
	  }else if($dim == 2 and is_object(first($data))){
      foreach ($data as $key=>$value){
        $assArray[$key] = [];
        foreach ($value as $key1=>$value1)
        $assArray[$key][$key1] = $value1;
      }
	  }else
		return notice("Cette fonction ne marche que pour les tableaus de dimension 1 ou pour les tableaux d'objets !");
	}
	return $assArray;
}
/**
 * Transforme, en un tableau, un json, un objet ou un string contenant une liste d'éléments séparés par défaut par une virgule. Cette fonction enlève les doubles espaces et les espaces au tour du sépérateur.
 * Ex 1 : 
 * 
 * $string &nbsp;&nbsp;= "prenom, nom, age";
 * 
 * $string2 = "prenom; nom; age";
 * 
 * toArray($string); et toArray($string2, ";"); donnent tous les deux :
 * 
 * Array([0] => prenom [1] => nom [2] => age)
 * 
 * TODO doc pour objet en param
 */
function toArray($stringObjectOrJson, $separator = ","){
	if( ! notEmpty($stringObjectOrJson))
		return [];

	if(is_object($stringObjectOrJson))
		return (array) $stringObjectOrJson;

	if(is_array($stringObjectOrJson))
		return toAssArray($stringObjectOrJson);

	$result = json_decode($stringObjectOrJson);
	if(json_last_error() === JSON_ERROR_NONE){

		if(dim($result) > 1)
		return toAssArray($result);
		else
		return (array)$result;

	}
	
	$n = 1;
	while ($n > 0) {
		$stringObjectOrJson = str_replace("  ", " ", $stringObjectOrJson, $n);
	}
  $stringObjectOrJson = str_replace([$separator." ", " ".$separator], $separator, $stringObjectOrJson);
  return explode($separator, $stringObjectOrJson);
}
function toArrayOfArray($stringObjectOrJson, $separator = ","){
	$result = [];
	foreach (toArray($stringObjectOrJson, $separator)
		as $key => $value) {
		if( ! is_array($value))
			$result[$key] = toArray($value, $separator);
	}
	return $result;
}

/** Transforme les arguments (parametres) en un array. */
function toArrayFromArgs(){
	$result = [];
	foreach (func_get_args() as $value)
    	$result[] = $value;
	return $result;
}
function toArrayUpper($str=false){ 
	return explodeUpper($str);
} 
function explodeUpper($str=false){ 
	if($str) 
		return explode(' ',trim(preg_replace('#([A-Z])#',' $1', $str)));
} 
function otherTabToAssArr($otherTab){
	$otherTabTmp = [];
    foreach ($otherTab as $key => $value) {
      if(is_int($key))
        $otherTabTmp[$value] = [];
      else{
        if(is_array($value))
          $otherTabTmp[$key] = $value;
        else
          $otherTabTmp[$key] = toArray($value);
      }
    }
    return $otherTabTmp;
}
/**
 *Retourne une portion d'un tableau (arg 1) suivant une liste de clés (arg 2), ou d'objet (arg 1) suivant une liste d'attributs (arg 2). Arg 2 est une chaine de caractères avec comme séparateur virgule par défaut. Pour le tableau retourné il est possible que les clés soient entiers successifs (indices) à partir d'un entier donné en 3ième parametre. 
 * @see tronquePostChangeKeys() 
 * @see changeKeysOfPost()
 * @see tronquePost()
 * @see changeKeys()
 * @see post()
 */
function tronque($arrayOrObject, $keys, $keyFrom_int = "", $keysSeparator=","){
  $result = []; $is_object = false;
  if(is_string($keys))
  	$keys = toArray($keys, $keysSeparator);
  if($keyFrom_int)
	$i = intval($keyFrom_int);
  if(is_object($arrayOrObject)){
	(array) $arrayOrObject;}
  foreach ($keys as $value) {
    if($keyFrom_int)
      $result[$i++] = $arrayOrObject[$value];
    else
      $result[$value] = $arrayOrObject[$value];
  }
  if($is_object)
  	return (object) $result;
  return $result;
}
/**
 *Retourne une portion du tableau $_POST suivant une liste de clés (arg 1) qui est une chaine de caractères avec comme séparateur virgule par défaut. Pour le tableau retourné il est possible que les clés soient entiers successifs (indices) à partir d'un entier donné en 2ième parametre.
 * @see tronquePostChangeKeys() 
 * @see changeKeysOfPost()
 * @see changeKeys()
 * @see tronque()
 * @see post() 
 */
function tronquePost($keys, $keyFrom_int = "", $keysSeparator=","){
  $result = [];
  if(is_string($keys))
  	$keys = toArray($keys, $keysSeparator);
  if($keyFrom_int)
    $i = intval($keyFrom_int);
  foreach ($keys as $value) {
    if($keyFrom_int)
      $result[$i++] = $_POST[$value];
    else
      $result[$value] = $_POST[$value];
  }
  return $result;
}
/**
 *Retourne une portion du tableau ou d'un objet (arg 1) en changeant le nom des clés suivant une liste de clés ou d'attributs (arg 2) qui est une chaine de caractères avec comme séparateur virgule par défaut.
 * @see tronquePostChangeKeys() 
 * @see changeKeysOfPost()
 * @see tronque()
 * @see post() 
 */
function changeKeys($arrayOrObject, $keyAndNewKeys, $keysSeparator=","){
	$isObject = is_object($arrayOrObject);
  	$result = [];
	if(is_string($keyAndNewKeys)){
		$keyAndNewKeys = toArray($keyAndNewKeys, $keysSeparator);
		$n = count($keyAndNewKeys);
		for ($i=0; $i < $n; $i = $i+2) {
			$tmp = isset($keyAndNewKeys[$i+1]) ? $arrayOrObject[trim($keyAndNewKeys[$i+1])] : "";

			$result[$keyAndNewKeys[$i]] = $tmp;
		}
	}
	else{
		foreach ($keyAndNewKeys as $key => $value) {
			$result[$value] = $arrayOrObject[$key];
		}
	}
	if($isObject) 
		return (object)$result;
	else return $result;
}
/**
 *Alias de changeKeysOfPost() et detronquePostChangeKeys(). A partir du $_POST, récupère quelques éléments en changeant leurs clés (util pour une requete préparée). Le parametre peut être soit une string (avec séparateur virgule) soit un tableau simple. Le 1er élément du parametre est associé à la valeur dans $_POST correspondant au 2ème ... ie : ($data[1er élément] = $_POST[2ième élément]), $data[3ième élément] = $_POST[4ième élément] etc.
 * Ex : 
 * 
 * Soient :
 * 
 * $sql = "select * from classes where id = :id and date between :dd and :df"
 * 
 * $_POST = [ [datedebut] => 2019-11-14 [datefin] => 2019-11-14 [id_classe] => 8 [autre] => autreVal ]
 * 
 * Alors :
 * 
 * $data = changeKeysPost("id, id_classe, dd, datedebut, df, datefin") donne :
 * 
 * $data -> [ [id] => 8 [dd] => 2019-11-14 [df] => 2019-11-14 ], exactement comme c'est prévu dans la requete préparée $sql.
 * 
 * ie : dans $data, id est associé à la valeur correspondant à id_classe dans $_POST; dd à datedebut dans $_POST et df à datefin dans $_POST. 
 * 
 * @see tronquePostChangeKeys() 
 * @see changeKeysOfPost()
 * @see post() 
 */
function changeKeysPost($keyAndNewKeys, $keysSeparator = ","){
  return tronquePostChangeKeys($keyAndNewKeys, $keysSeparator);
}
/** Alias de changeKeysPost() et de tronquePostChangeKeys().
 * @see changeKeysPost()
 * @see tronquePostChangeKeys()
 */
function changeKeysOfPost($keyAndNewKeys, $keysSeparator = ","){
	return tronquePostChangeKeys($keyAndNewKeys, $keysSeparator);
}
/**
 * A partir du $_POST, récupère quelques éléments en changeant leurs clés (util pour une requete préparée). Le parametre peut être soit une string (avec séparateur virgule) soit un tableau simple. Le 1er élément du parametre est associé à la valeur dans $_POST correspondant au 2ème ... ie : ($data[1er élément] = $_POST[2ième élément]), $data[3ième élément] = $_POST[4ième élément] etc.
 * Ex : 
 * 
 * Soient :
 * 
 * $sql = "select * from classes where id = :id and date between :dd and :df"
 * 
 * $_POST = [ [datedebut] => 2019-11-14 [datefin] => 2019-11-14 [id_classe] => 8 [autre] => autreVal ]
 * 
 * Alors :
 * 
 * $data = tronquePostChangeKeys("id, id_classe, dd, datedebut, df, datefin") donne :
 * 
 * $data -> [ [id] => 8 [dd] => 2019-11-14 [df] => 2019-11-14 ], exactement comme c'est prévu dans la requete préparée $sql.
 * 
 * ie : dans $data, id est associé à la valeur correspondant à id_classe dans $_POST; dd à datedebut dans $_POST et df à datefin dans $_POST. 
 * 
 * @see changeKeysPost()
 * @see tronquePostChangeKeys()
 * @see post() 
 */
function tronquePostChangeKeys($keyAndNewKeys, $keysSeparator = ","){
	$result = [];
	if($keyAndNewKeys){
      	if(is_string($keyAndNewKeys)){
		  	$keyAndNewKeys = toArray($keyAndNewKeys, $keysSeparator);
			$n = count($keyAndNewKeys);
			for ($i=0; $i < $n; $i= $i+2) {
				$tmp = isset($keyAndNewKeys[$i+1]) ? $_POST[trim($keyAndNewKeys[$i+1])] : "";
				$result[trim($keyAndNewKeys[$i])] = $tmp;
			}
		}else{
			foreach ($keyAndNewKeys as $key => $value) {// TODO  à tester pour le throw
				throw new Exception("The key <b>$key</b> doent existe in \$_POST");
				$result[$value] = $_POST[$key];
			}
		}
	}
	return $result;
}
function unsetValues(&$arrayOrObject, $values){
	//TODO voir aussi array_diff_key($arrayOrObject, array_flip($values))
	if( ! is_array($values))
		$values = [$values];
	$isObject = false;
	if(is_Object($arrayOrObject)){
		$arrayOrObject = (array) $arrayOrObject;
		$isObject = true;
	}
	$key = "";
	foreach ($values as $value) {
		$ok = false;
		foreach ($arrayOrObject as $key => $value2) {
			if($value == $value2){
				$ok = true;
				break;
			}
		}
		if($ok)
			unset($arrayOrObject[$key]);
	}
	if($isObject)
		$arrayOrObject = (object) $arrayOrObject;
}
/**
 * Retourne la liste des clés pour un array ou un objet ou la clé numéro n (indice) en précisant l'indice de cette dernière.
 * @see array_keys()
 */
function getKeys($arrayOrObject, int $indis=null){
	if(!is_array($arrayOrObject))
		$arrayOrObject = toArray($arrayOrObject);
	if(isset($indis))
		return array_keys($arrayOrObject)[$indis];
	return array_keys($arrayOrObject);
}
/**
 * Alias of getKeys
 * @see getKeys()
 * @see array_keys()
 */
function getFields($arrayOrObject, int $indis=null){
	return getKeys($arrayOrObject, $indis);
}
//TODO faire aussi pour getWhereKeyEndsWith et getWhereKeyContains
/**
 * Retourne la première valeur dont la clé commence par $subKey
 */
function getWhereKeyStartsWith($arrayOrObject, $subKey, $withWoleKey = false, $caseSensitive = false, $trim = true){
	if(is_object($arrayOrObject))
		$arrayOrObject = (array) $arrayOrObject;
	$woleKey = "";
	$value = ""; //unitule pour les actuelle version de php (2021)
	foreach ($arrayOrObject as $key => $value){
		if(startsWith($key, [$subKey], $caseSensitive, $trim)){
			$woleKey = $key;
			break;
		}
	}
	if( ! $woleKey)
		return false;
	return $withWoleKey ? [$woleKey, $value] : $value;
		
}
function getValues($arrayOrObject, int $indis=null){
	if(is_object($arrayOrObject))
		$arrayOrObject = (array) $arrayOrObject;
	if(isset($indis))
		return array_values($arrayOrObject)[$indis];
	return array_values($arrayOrObject);
}
function getKeyFromValue($arrayOrObject, $value, $strictValue = true, $strictType = false){//TODO tester avec object
	if($strictValue){
		if($strictType){
			foreach ($arrayOrObject as $key => $oneValue) {
				if($oneValue === $value)
					return $key;
			}
		}else{
			foreach ($arrayOrObject as $key => $oneValue) {
				if($oneValue == $value)
					return $key;
			}
		}
	}else{
		foreach ($arrayOrObject as $key => $oneValue) {
			if(in($oneValue, $value))
				return $key;
		}
	}
	return "";
}
function getLabel($arrayObjectOrJson, $default="", $labelOrLibelle = "label"){
	$label = get($arrayObjectOrJson, $labelOrLibelle);
	$label2 = get($label, lang);
	$label = ternary(notEmpty($label2), $label2, $label);
	return ucfirst(notEmpty($label) ? $label : $default);
}//TODO faire pour les autres éléments de field_info
function getLibelle($arrayObjectOrJson, $default=""){
	return getLabel($arrayObjectOrJson, $default, "libelle");
}
function getLibelleInPlural($arrayObjectOrJson, $default=""){
	return getLabel($arrayObjectOrJson, $default, "libelle_in_plural");
}
function get($arrayObjectOrJson, $key1, $key2 = "", $emptyOrDefault = "", $strict=true){
	return get3Keys($arrayObjectOrJson, $key1, $key2, "", $strict, $emptyOrDefault);
}
function get3Keys($arrayObjectOrJson, $key1, $key2 = "", $key3 = "", $strict=true, $emptyOrDefault = ""){
	$result = $emptyOrDefault;
	if(is_array($arrayObjectOrJson)){
	  if(isset($arrayObjectOrJson[$key1]))
		$result = $arrayObjectOrJson[$key1];
	  elseif($strict)
		return $emptyOrDefault;
	}
	elseif(is_object($arrayObjectOrJson)){
	  if(isset($arrayObjectOrJson->$key1))
		$result = $arrayObjectOrJson->$key1;
	  elseif($strict)
		return $emptyOrDefault;
	}
	else{ // JSON
	  $arrayObjectOrJson = json_decode($arrayObjectOrJson);
	  if(json_last_error() === JSON_ERROR_NONE){
		if(isset($arrayObjectOrJson->$key1))
			$result = ($arrayObjectOrJson->$key1);
		elseif($strict)
		  return $emptyOrDefault;
	  }
	}
	if(notEmpty($key2)){
		$emptyTmp = $strict ? $emptyOrDefault : $result;
		$result = get3Keys($result, $key2, "", "", $strict, $emptyTmp);
		
		if(notEmpty($key3)){
			$emptyTmp = $strict ? $emptyOrDefault : $result;
			$result = get3Keys($result, $key3, "", "", $strict, $emptyTmp);
		}
	}
	return $result;
}
/*
function get3Keys($arrayObjectOrJson, $key1, $key2 = "", $key3 = "", $strict=true, $empty = null){
  $result = $empty;
  if(is_array($arrayObjectOrJson)){
	if(isset($arrayObjectOrJson[$key1]))
	  $result = $arrayObjectOrJson[$key1];
	elseif($strict)
	  return $empty;
  }
  elseif(is_object($arrayObjectOrJson)){
	if(isset($arrayObjectOrJson->$key1))
	  $result = $arrayObjectOrJson->$key1;
	elseif($strict)
	  return $empty;
  }
  else{ // JSON
    $arrayObjectOrJson = json_decode($arrayObjectOrJson);
	if(json_last_error() === JSON_ERROR_NONE){
	  if(isset($arrayObjectOrJson->$key1))
      	$result = ($arrayObjectOrJson->$key1);
	  elseif($strict)
		return $empty;
	}
  }
  if(notEmpty($key2)){
    if(! is_array($result))
      $result = toArray($result);
    if(isset($result[$key2]))
      $result = $result[$key2];
    elseif($strict){
	  return $empty;
      //throw new Exception(msgAdapter(msgs["keyMissing"], $key2)." ".msgs["functionName"]."get() with these arguments : ".ob()->see(func_get_args()));
    }
    if(notEmpty($key3)){
      if(! is_array($result))
        $result = toArray($result);
      if(isset($result[$key3]))
        return $result[$key3];
      elseif($strict){
		return $empty;
        //throw new Exception(msgAdapter(msgs["keyMissing"], $key3)." ".msgs["functionName"]."get() with these arguments : ".ob()->see(func_get_args()));
      }
	}
    return $result;
  }else {
    return $result;
  }
}*/

/** 
 * Met les valeurs d'un array ou d'un objet dans une string avec séparateur virgule (par défaut).
 * @see implode()
 *  */
function getConstant($name){
	return defined($name) ? constant($name) : null;
}
function toString($arrayOrObject, $separator=", "){
	return implode($separator, (array) $arrayOrObject);
}
/**
* Si c'est un Json en parametre alors cette fonction peut retourner un tableau d'objets.*/
function toObject($stringArrayOrJson){
	if(is_array($stringArrayOrJson))
		return (object) $stringArrayOrJson;
	else
		return json_decode($stringArrayOrJson);
}
function toArrayOfObject($array){
	$result = [];
	foreach ($array as $key => $value) {
		$result[$key] = (object) $value;
	}
	return $result;
}
function toJson($array, $keys = []){
	$result = '';
	if(notEmpty($keys))
		$array = toAssArray($array, $keys);
	return json_encode($array);
}
/**Ex1 : 
 * toSqlConcat("prenom nom, table.tel (col3 , col4)")
 * return : CONCAT ( `table`.`prenom`, ' ', `nom`, ',', `patient`.`tel`, ' (', `col3`, ',', `col4`, ')' )
 * Ex2 : 
 * toSqlConcat("prenom nom, patient.tel (col3 , col4)", "infos")
 * return : CONCAT ( `table`.`prenom`, ' ', `nom`, ',', `patient`.`tel`, ' (', `col3`, ',', `col4`, ')') as infos
 */
function toSqlConcat($str, $as = ""){ // (nom)
    $str = trimPlus($str);//prenom nom , patient.tel (col3 , col4 )
    $str = str_replace([',', '(', '( ', ' )', ') '], [', ', ' (', '(', ')', ')'], $str);
    $str = trimPlus($str);//prenom nom, patient.tel (col3, col4)
    $str = str_replace([".", ", ", " (", ")"], ["`.`", "`, ', ', `", "`, ' (', `", "`, ')'"], $str);  //prenom nom`, `patient`.`tel` (`col3`, `col4`)
    $str = str_replace([" "], ["`,' ', `"], $str);
			//prenom`,' ', `nom`,`,' ', ``patient`.`tel``,' ', `(`col3`,`,' ', ``col4`)
    $str = str_replace(["`,' ', ``", "``,' ', `", "`,' ', `'", "'`,' ', `("], [" `", "` ", " '", "' ("], $str);
	//table`.`prenom`, ' ', `nom`, ',', `patient`.`tel`, ' (', `col3`, ',', `col4`, ')'
	if(first($str) != "'")
		$str = "`".$str;
	if(last($str) != "'")
		$str = $str."`";
	//`table`.`prenom`, ' ', `nom`, ',', `patient`.`tel`, ' (', `col3`, ',', `col4`, ')')

	$as = ternary($as, " as $as", "");
	return "CONCAT( $str )$as";
	// CONCAT ( `table`.`prenom`, ' ', `nom`, ',', `patient`.`tel`, ' (', `col3`, ',', `col4`, ')') as infos
  }


/**
 * Affiche les valeurs d'un array ou d'un objet entre guillemets, séparés par une virgule et le tout entre crochets. Util TODO
 * 
 * Exemple de retour : ['valeur1', 'valeur2', 'valeur3', ]
 * 
 * @see buildArray()
 */
function arrayOfStrWithCrochet($arrayOrObject, $br=false){
  $br = ($br) ? "<br>" : "";
  echo "[".$br;
  foreach ($arrayOrObject as $value) {
    echo $br."'".$value."', ";
  }
	echo $br."]";
}
/**
 * Test 1- si un motif est dans une chaine ou 2- si un élément est dans un tableau. Cas 1 : si c'est une chaine contenant des éléments séparés par une virgule ou autre caractère, il est possible de le préciser au niveau du 3ème parametre; et dans ce cas la recherche sera strictement le motif. Cas 2 : si le 3ème parametre (...strict) est true alors le type est aussi testé.
 *   
 * Ex : 
 * 
 * Soit la chaine $str = "1500, 2555, 5555" :
 * 
 * in(0, $str) donne true (la position de la 1ere occurence, comme strpo($str, 0)).
 * 
 * in("0", $str) donne aussi true.
 * 
 * in(0, $str, ",") et in(0, $str, ",") donnent false.
 * 
 * in(1500, $str), in(1500, $str), in(1500, $str, ",") et in(1500, $str, ",") donnent true.
 * 
 * 
 * Soit le tableau $str2 = [1500, 2555, 5555] :
 * 
 * in(1500, $str2) donne true
 * 
 * in($str0, 0) donne false
 * 
 */
function in($toFind, $stringOrArray, $strSepStrictOrOnlyFields = "", $caseSensitive = true, $allOrAnd = true, $whole = false){
	if($toFind === "")
		return false;
	if(! $caseSensitive){
		if(is_bool($strSepStrictOrOnlyFields) and $strSepStrictOrOnlyFields)
			$numericToString = false;
		else $numericToString = true;
		$toFind = toLower($toFind, $numericToString);
		if(is_array($strSepStrictOrOnlyFields))
			$onlyFields = $strSepStrictOrOnlyFields;
		else
			$onlyFields = [];
		$stringOrArray = toLower($stringOrArray, $numericToString, $onlyFields);
	}

	if(is_array($strSepStrictOrOnlyFields)){ // util pour filtrer les résultats de bd...
		if(is_array($toFind)) 
			notice("$toFind ne peut pas etre un tableau dans ce cas ci");
		if($allOrAnd) //and
			foreach($strSepStrictOrOnlyFields as $value){
				if(in($toFind, $stringOrArray[$value], "", $caseSensitive, $allOrAnd, $whole))
					return true;
			}
		else{
			foreach($strSepStrictOrOnlyFields as $value){
				if(notIn($toFind, $stringOrArray[$value], "", $caseSensitive, $allOrAnd, $whole))
					return false;
			}
			return true;
		}
		return false;
	}
	if(is_object($toFind))
		$toFind = array($toFind);
	if(is_object($stringOrArray))
		$stringOrArray = array($stringOrArray);

	if(is_array($toFind)){ //la recherche concerne les valeurs et pas les clés
		// if( ! is_array($stringOrArray))
		// 	$stringOrArray = toArray($stringOrArray, $strSepStrictOrOnlyFields) ; //return false; TODO touché
		
		if($allOrAnd){ //all //TODO à fusionner peut etre avec le else
			foreach ($toFind as $value) {
				if(! in($value, $stringOrArray, $strSepStrictOrOnlyFields, $caseSensitive, $whole))
					return false;
			}
			return true;
		}
		else{
			foreach ($toFind as $value) {
				if(in($value, $stringOrArray, $strSepStrictOrOnlyFields, $caseSensitive, $whole))
					return true;
			}
			return false;
		}
	}
	if(is_array($stringOrArray)){
		if( ! is_bool($strSepStrictOrOnlyFields))
			$strSepStrictOrOnlyFields = false;
		return inArray($toFind, $stringOrArray, $strSepStrictOrOnlyFields, $whole);
	}
	if( $strSepStrictOrOnlyFields == "" )
		return $whole ? ($toFind == $stringOrArray) :
			(is_int(strpos($stringOrArray, $toFind."")) ? true : false); //car 0 est false
	else{
		$strict = false;
		if(is_bool($strSepStrictOrOnlyFields)){
			$strSepStrictOrOnlyFields = ",";
			$strict = $strSepStrictOrOnlyFields;
		}
		$stringOrArray = toArray($stringOrArray, $strSepStrictOrOnlyFields);
		return inArray($toFind, $stringOrArray, $strict, $whole);
		// foreach ($stringOrArray as $value)
		// 	if($value == $toFind)
		// 		return true;
	}
	return false;
}

function notIn($toFind, $stringOrArray, $strSepStrictOrOnlyFields = "", $caseSensitive = true, $all = true, $whole = true){
	return ! in($toFind, $stringOrArray, $strSepStrictOrOnlyFields, $caseSensitive, $all, $whole);
}
function inArray($toFind, $array, $strict = false, $caseSensitive = true, $whole = true){
	if($whole && ! $caseSensitive)
		return in_array($toFind, $array, $strict);
	
	if(notEmpty($toFind)) //
		foreach($array as $value){
			if(is_int(strpos($value,
			$toFind."")))
				return true;
		}
	return false;
}
function getKeyFromPartOfKeyOrValue($toFind, array $array){
	//$array = ["sexe"=>[], "id_sexe->sexe2"=>[], "age", "id_age->age", "id_sport->sport"=>["id", "libelle", "prix", "heure"]];
	$keys = array_keys($array);
	foreach ($keys as $oneKey) {
		if(in($toFind, $oneKey))
			return $oneKey; 
		else if(in($toFind, get($array, $oneKey)))
			return get($array, $oneKey); 
	}
}
function getWholeWord($toFind, $string, $n = 1){// valid dans ... validate ...
	$pos = 0;
	$len = strlen($toFind);
	for ($i=0; $i < $n; $i++) { 
		$string = substr($string, $pos);
		$pos = strpos($string, $toFind);
		if(is_int($pos))
			$pos += $len;
		else
			break;
	}
	if(is_int($pos)){
		$pos = $pos - $len;
		$result = last(preg_split("#\W#", substr($string, 0, $pos))).preg_split("#\W#", substr($string, $pos))[0];
		return $result;
	}
	return null;
}
function notEmpty($stringOrArrayOrObject, $zeroIsNotEmpty = true){
  if(is_object($stringOrArrayOrObject))
    $stringOrArrayOrObject = (array) $stringOrArrayOrObject;
  
  if(is_array($stringOrArrayOrObject))
    return ! empty($stringOrArrayOrObject);
  else if($zeroIsNotEmpty)
    return isset(((string)$stringOrArrayOrObject)[0]);
  else return (bool) $stringOrArrayOrObject;
}
function empty2($stringOrArrayOrObject, $zeroIsNotEmpty = true){
	return ! notEmpty($stringOrArrayOrObject, $zeroIsNotEmpty);
}

//la recherche concerne les valeurs et pas les clés
// function ins(array $toFind, array $array, $strict = false, $caseSensitive = false){
// 	if($caseSensitive){
// 		$toFind = toLower($toFind);
// 		$array = toLower($array);
// 	}
// 	foreach ($toFind as $value) {
// 		if(! in($value, $array, $strict))
// 			return false;
// 	}
// 	return true;
// }
//ça converti les valeurs et pas les clés
function toLower($stringOrarrayOrObject, $numericToString = true, $onlyFields = []){
	return privateToLowerUpper($stringOrarrayOrObject, "mb_strtolower", $numericToString, $onlyFields);
}
//ça converti les valeurs et pas les clés
function toUpper($stringOrarrayOrObject, $numericToString = true, $onlyFields = []){
	return privateToLowerUpper($stringOrarrayOrObject, "mb_strtoupper", $numericToString, $onlyFields);
}
function privateToLowerUpper($stringOrarrayOrObject, string $strtolowerOrStrtoupper, $numericToString = true, $onlyFields = []){
	if(is_object($stringOrarrayOrObject))
		$stringOrarrayOrObject = array($stringOrarrayOrObject);
	if(is_array($stringOrarrayOrObject)){
		// $result = [];
		if(empty($onlyFields))
			$keys = getKeys($stringOrarrayOrObject);
		else
			$keys = $onlyFields;
		foreach ($keys as $key){
			$value = $stringOrarrayOrObject[$key];
			if( ! $numericToString && is_numeric($value) )
				$stringOrarrayOrObject[$key] = $value;
			else
				$stringOrarrayOrObject[$key] = $strtolowerOrStrtoupper($value);
		}
		return $stringOrarrayOrObject;
	}
	else 
		return $strtolowerOrStrtoupper($stringOrarrayOrObject);
}
//TODO phpdoc
function filter($arrayOrObject, $filterKey, $filterValues="", $onely = false){
	$result = array();
	$itsHim = false;
	$filterKey = (string) $filterKey;
	$filterValues = (string) $filterValues;
	foreach ($arrayOrObject as $key => $value) {
		if(is_object($value)){
			$value = (array) $value;}
		if( ( $key == $filterKey and
			  ( $filterValues == "" or in($value, $filterValues, ",") )
			) //pour un tableau associatif simple
			or
		    ( isset($value[$filterKey]) and 
			  ( $filterValues == "" or in($value[$filterKey], $filterValues, ",") )
			) //pour un objet ou un tableau contenant des tableaux associatifs
		){
			$itsHim = true;
		}
		
		if($itsHim){
			if($onely)
				$result[$key] = $value;
		}
		else{
			if( $onely == false)
				$result[$key] = $value;
		}

		$itsHim = false;
		unset($value);
	}
	return $result;
}
function escaper($data, $toEscapeList){
  $result = "";
  if(is_string($data))
    return addcslashes ($data, $toEscapeList);
  $isObject = false;
  if(is_object($data)){
    $isObject = true;
    $data = (object)$data;
  }
  if(is_array($data)){
    $result = [];
    foreach ($data as $key => $value){
      $result[$key] = addcslashes ($value, $toEscapeList);
    }
  }
  if($isObject)
    $result = (object)$result;
  return $result;
}
function splitSlash($str){
	return toArray($str, "/");
}
function split($str, $separator = ""){
	return toArray($str, $separator);
}
function splitV($str){
	return toArray($str, ",");
}
function splitE($str){
	return toArray($str, " ");
}
function splitP($str){
	return toArray($str, ";");
}

/////////////////////////////////////
function getFormNumber(){
  $number = "";
  if(isset($_POST["pamFormNumber"])){
    foreach ($_POST as $fieldName => $value){
      if(in("pamFormNumber", $fieldName) and "pamFormNumber" !=  $fieldName){
        return $value;
  } } }
  return $number;
}
function formNumberInput($number = ""){
	if($number){
		input("hidden", "", "pamFormNumber$number", $number);
		input("hidden", "", "pamFormNumber", "", "pamFormNumber$number"."_");
	}
}
function formCheckboxAjuster($number=""){
  if(isset($_POST["pamCheckboxInfos_nearJson".$number])){
    $checkboxInfos = toArray(quotSwiper($_POST["pamCheckboxInfos_nearJson".$number]));
    foreach ($checkboxInfos as $fieldName => $values){
      $result = "";
      foreach ($values as $oneValue){
        if(isset($_POST[$oneValue.$number])){
          $result .= $_POST[$oneValue.$number].",";
          unset($_POST[$oneValue.$number]);          
        }
      }
      $_POST[$fieldName] = trimPlus($result, ",");
    }
  } 
  unset($_POST["pamCheckboxInfos_nearJson".$number]);
}
function delFormNumber($number){
  if(isset($_POST["pamFormNumber"]) or isset($_POST["pamFormNumber".$number])){
    $tmp = [];
    foreach ($_POST as $fieldName => $value) {
      $tmp[trimPlus($fieldName, $number)] = $value;
    }
    $_POST = $tmp;
    $tmp = [];
    foreach ($_FILES as $fieldName => $value) {
      $tmp[trimPlus($fieldName, $number)] = $value;
    }
    $_FILES = $tmp;
  }  
  unset($_POST["pamFormNumber"]); 
}
function post($keys = "", $without = false, $keysSeparator = ","){
	if($keys == "" and ! $without) return $_POST;
	if($keys == "" and $without) return [];
	if(is_string($keys))
    	$keys = toArray($keys, $keysSeparator);
	$result = [];
	if($without){
		$result = $_POST;
		foreach($keys as $key)
			unset($result[$key]);
	}else
		if(count($keys) == 1)
			return isset($_POST[$keys[0]]) ? $_POST[$keys[0]] : null;
		foreach($keys as $key)
			$result[$key] = $_POST[$key];

	return $result;
}
function getPost($keys = "", $without = false, $keysSeparator = ","){ //alias de post
	post($keys, $without, $keysSeparator);
}
/** Utile pour convertir certaine donnée tell que le mot de passe... TODO*/
function setPost($fields, $values){ //cette fonction doit etre appelé après goodPost()
	$fields = splitV($fields);
	$values = splitV($values);
	$n = count($fields);
	if($n != count($values)) return "Number of fields and number of values doesn't match";
	
	for ($i=0; $i < $n; $i++) {
		$_POST[$fields[$i]] = $values[$i];
	}
}
function toHrefParam($paramOrAssoArray, $valueIfParamOrAssoArray = "", $withGET = false, $withPOST = false): string{
	if(is_string($paramOrAssoArray)){
		$paramOrAssoArray = [$paramOrAssoArray => $valueIfParamOrAssoArray];
	}else if(is_array($valueIfParamOrAssoArray))
		$paramOrAssoArray = array_merge($paramOrAssoArray, $valueIfParamOrAssoArray);
	
	if($withGET)
		$paramOrAssoArray = array_merge($_GET, $paramOrAssoArray);
	if($withPOST)
		$paramOrAssoArray = array_merge($paramOrAssoArray, $_POST);
	if(isset($paramOrAssoArray['qs']) && is_array($paramOrAssoArray['qs']))
		$paramOrAssoArray['qs'] = implode(',', $paramOrAssoArray['qs']);
	unset($paramOrAssoArray['tokencsrf']);
	unset($paramOrAssoArray['pamhopo']);
	unset($paramOrAssoArray['pamboup']);
	unset($paramOrAssoArray['url']);

	return "&".http_build_query($paramOrAssoArray);
}
function toHttp($array = []){
	if(empty($array))
		$array = $_GET;
	unset($array['url']);
	return trimPlus(route()."&".http_build_query($array), "&");
}
function toLink($array = []){
	return toHttp($array);
}
function toHrefParamWithGET($paramOrAssoArray, $valueIfParamOrAssoArray = ""){
	return toHrefParam($paramOrAssoArray, $valueIfParamOrAssoArray, true);
}
function toHrefParamWithPOST($paramOrAssoArray, $valueIfParamOrAssoArray = ""){
	return toHrefParam($paramOrAssoArray, $valueIfParamOrAssoArray, false, true);
}
function toHrefParamWithGETandPOST($paramOrAssoArray, $valueIfParamOrAssoArray = ""){
	return toHrefParam($paramOrAssoArray, $valueIfParamOrAssoArray, true, true);
}
function htmlTableSort(string $sortKey, string $label, array $data, $route=""): string{
	$sort = get($data, msgs["htlmTableSortKey"]);
	$direction = get($data, msgs["htlmTableDirKey"]);
	$icon = "";
	if($sort === $sortKey || in("$sortKey.", $sort))
		$icon = $direction === "asc" ? icon("expand_less") : icon("expand_more");
	$url = $route.toHrefParamWithGETandPOST([
		'sort' => $sortKey,
		'dir' => ($direction === 'asc' && ($sort === $sortKey || in("$sortKey.", $sort))) ? 'desc' : 'asc']);
	return <<<HTML
	<a href="$url">$label$icon</a>
HTML;
}
function csrf(){
	return '<input type="hidden" name="tokencsrf" value="'.csrfToken().'" />';
}
function checkCsrf($duration){
	if( ! post("tokencsrf") )
		throw new Exception(msgs["csrf"]);
	else if( ($duration >= csrfExpirationDelay) or
			( post("tokencsrf") != csrfToken() ) ){
		throw new Exception(msgs["csrfExpiration"]);
	}
}
function checkAndManageLoginDelay($duration){
	if(loginExpirationDelay && $duration >= loginExpirationDelay && notEmpty(userId()) and ! isset($_GET["isNotBackLink"])){
		// sd($lastGetTmp);
		$oldInfo["route"] = route();
		$oldInfo["get"] = $_GET;
		$oldInfo["post"] = $_POST;
		$oldInfo["file"] = $_FILES;
		session("expirationOldInfo", $oldInfo);
		logOut();
		if($oldInfo["route"] != route("userLogIn"))
			back(route("userLogIn"));
	}
}
function honeyPot(){ // TODO on peut le rendre aléatoire ...
	echo '<input type="hidden" name="pamhopo"/>';
}
function checkHoneyPot(){
	if(post("pamhopo")){
		if(devMode)
			throw new Exception(msgs["honeyPotDevMode"]);
		else
			throw new Exception(msgs["honeyPotProdMode"]);
	}
}

function seec($data=""){
	echo "<center>";
	see(func_get_args());
	echo "</center>";
}
function seecs($data=""){
	seec(func_get_args());
	sleep(2);
}
function sees($data=""){
	see(func_get_args());
	sleep(2);
}
function sdc($data=""){
	echo "<center>";
	sd(func_get_args());
	echo "</center>";
}
function see($data=""){
  $data = func_get_args();
  $n = count($data);
  $i = 0;
  ob_start();
  echo "<pre>";
  if(empty($data)) echo "seeeeeeeeeeeeee";
  foreach ($data as $value) {$i++;
	echo b("$i) /$n");br();
    print_r($value); // seeDump($value);
	br();
  }
  echo "</pre>";
//   $data = ob_get_clean();
//   //controller()->view("view", $data);
//   echo $data;
}
function seeDump($data){
	ob_start();
	var_dump($data);
	$pam = ob_get_clean();
	$pam = str_replace(
		['["', '"]=>'."\n  ", ' "', "\"\n", "=> NULL", "=>  "],
		['["<b>', '</b>"]=> ', '    <b>"', "\"</b>\n", "=> <b>NULL</b>", "=> "],
		$pam);
	return str_replace(["=>  "], ["=> "], $pam);
}
function sd($data=""){
	$data = func_get_args();
	$n = count($data);
	$i = 0;
	echo "<pre>";
	if(empty($data)) echo "seeeeeeeeeeeeee";
	foreach ($data as $value) {$i++;
	  echo b("$i) /$n");br();
	  echo seeDump($value);
	  br();
	}
	echo "</pre>";
	die;
}

function colomnToLine(array $array){
	return lineToColumn($array);
}
function lineToColumn(array $array){
	$result=array();
	foreach($array as $key => $line)
		foreach($line as $j => $value)
			$result[$j][$key] = $value;
	return $result;
}
function seeMatrix($tableau) {
    echo '<table border=1>';
    foreach($tableau as $ligne) {
        echo '<tr>';
        foreach($ligne as $element)
            echo "<td>$element</td>";
        echo '</tr>';
    }
    echo '</table>';
}
function dateFormat($date){ 
	if( ! is_object($date))
		$date = new DateTime($date);
	return $date->format(dateFormat);
}
function dateTimeFormat($date){ 
	if( ! is_object($date))
		$date = new DateTime($date);
	return $date->format(dateTimeFormat);
}
function timeFormat($date){ 
	if( ! is_object($date))
		$date = new DateTime($date);
	return $date->format(timeFormat);
}
function numberFormat($value, $ifInNumberOfDecimalIsNull = delDecimalPartIfItsNull){
	if(is_int($value)){
		$ifInNumberOfDecimalIsNull = $ifInNumberOfDecimalIsNull ? 0 : numberOfDecimal;
		$value = number_format($value, $ifInNumberOfDecimalIsNull, dec_point, thousands_sep);
	}else
		$value = number_format($value, numberOfDecimal, dec_point, thousands_sep);
	return $value;
}
function unbreakableSpace($value){
	return $value = str_replace(" ", "&#160;", $value);
}
function popup($page, $width = 900, $height = 650, $left = 2){ // TODO popup?>  
    <!-- <SCRIPT LANGUAGE='JavaScript'>
        width = <?php //echo $width ?>;
        height = <?= $height ?>;    
        if(window.innerWidth){
            var left = (window.innerWidth-width)/<?php //echo $left ?>;
            var top = (window.innerHeight-height)/2;
        }else{
            var left = (document.body.clientWidth-width)/<?php //echo $left ?>;
            var top = (document.body.clientHeight-height)/2;
        }
        window.open('<?php //echo $page?>','', 
        ' top='+top+', left='+left+', width='+width+', height='+height+', resible=no');
        //window.open('facturepdf.php','_blank');
    </SCRIPT>  --><?php
}
function getMoisAnnee($date, $delimiter="-", $lang="fr"){ //TODO gérer le cas arabe et anglais
	$dates = explode($delimiter, $date);
	if($lang == "fr")
	  switch($dates[1]){	
		case 1 : return "Janvier ".$dates[0];	
		case 2 : return "Fevrier ".$dates[0];	
		case 3 : return "Mars ".$dates[0];	
		case 4 : return "Avril ".$dates[0];	
		case 5 : return "Mai ".$dates[0];	
		case 6 : return "Juin ".$dates[0];	
		case 7 : return "Juillet ".$dates[0];	
		case 8 : return "Août ".$dates[0];	
		case 9 : return "Septembre ".$dates[0];	
		case 10 : return "Octobre ".$dates[0];	
		case 11 : return "Novembre ".$dates[0];	
		case 12 : return "Décembre ".$dates[0];
	  }
}
function scrollEffect($className='scrEffDefault'){
	session('scrollReveal', "azerty");
	return $className;
}
function scrEff($className='scrEffDefault'){
	return scrollEffect($className);
}
/**
 * https://scrollrevealjs.org/
 */
function addScrollEffect($target, $optionsArrObjOrJson = ""){
	$value = 'ScrollReveal().reveal("' . $target.'"';
	if (is_array($optionsArrObjOrJson) || is_object($optionsArrObjOrJson)) {
		$optionsArrObjOrJson = toJson($optionsArrObjOrJson);
	}
	elseif ( notEmpty($optionsArrObjOrJson)){
		if(! isJson($optionsArrObjOrJson)){
			notice("$optionsArrObjOrJson n'est pas un Json ou un array");
			return;
		}
		$value .= ','.$optionsArrObjOrJson;
	}
	sessionAppendArr('scrollEffectArr', $value.= ");");
}
function pl(int $pl, $data, $tag = "div"){
	return ob()->tag("$tag class=' pl-$pl'", $data);
}
function insert_into_tables_info($tablename,$tableColumns, $tableListe= array()){

	if(in_array($tableColumns, $tableListe)){
	}
	$sql = "INSERT INTO `tables_info` (`id_field_name`, `name`, `libelle`, `libelle_in_plural`, `libelle_abbr`, `ajax_validation`, `icon_name`, `icon_type`, `field_not_to_show_list_for_create`, `field_not_to_show_list_for_read`, `field_not_to_show_list_for_show`, `other_tab`, `line_number`, `search_zone`, `total_formul`, `range_field_list`, `table_class`, `number_of_lines_per_page`, `create_route_name`, `read_route_name`, `show_route_name`, `show_type`, `update_route_name`, `update_type`, `delete_route_name`, `download_route_name`, `print_route_name`, `print_type`, `stat_desc_col_list`, `update_back_to_tab_list`, `user_type_id_list_for_create`, `user_type_id_list_for_read`, `user_type_id_list_for_show`, `user_type_id_list_for_update`, `user_type_id_list_for_delete`, `user_type_id_list_for_download`, `user_type_id_list_for_print`, `typeahead_search_fields`, `typeahead_display`, `typeahead_temp_sugg`, `typeahead_temp_empty`, `add_create_infos`) VALUES
	('id', 'user', 'utilisateur', 'utilisateurs', '', NULL, '', '', 'id, tel_verified_at, email_verified_at, two_factor_secret, two_factor_recovery_code, remember_token, created_at, updated_at', '', '', '', '', '1', NULL, NULL, NULL, 20, NULL, NULL, '', '', '', '', '', '', '', '', NULL, '1', '3', '3', '3', '3', '3', '3', '3', NULL, NULL, NULL, NULL, NULL);";
   //TODO revenir ici et ajouter aussi user_type
	   model()->query($sql);
}

// function yeee(){
// 	//pour toutes les table 
// 	$model = model();
// 	 if(post()){
	 
// 	 	card("", see(post()));
// 	 }
	
// 	ob_start();
// 	$fieldsInfo = $model->getWithNameAsKey_obj("fields_info");
// 	$tablesInfo = $model->getWithNameAsKey_obj("tables_info");
// 	$tables = $model->getTables_obj(false);
// 	see(get($tables, "donnee_clinique"));
// 	// $fieldsInfoColNames = getKeys($fieldsInfo);
	

// 	foreach($tables as $tableName => $infos){
// 	  if(in($tableName, ["fields_info", "tables_info", "user", "user_type", "menu"]))
// 		continue;

// 	  $cols = "";
// 	  foreach($infos as $colName => $colFiels){
// 		$fullNameChecked = get($fieldsInfo, "$tableName.$colName") ? "checked" : "";
// 		$checked = get($fieldsInfo, $colName) ? "checked" : "";

// 		$cols .= 
// 		"<tr>".
// 			"<td>".ob()->input("radio", $colName, $tableName."[$colName]", $colName, $colName, $colName,"","", $checked)."</td>".
// 			"<td>".ob()->input("radio", "$tableName.$colName", $tableName."[$colName]", "$tableName.$colName", "$tableName.$colName","","","", $fullNameChecked)."</td>".
// 		"</tr>";
// 	  }
// 	  $aa[$tableName] = [
// 		ob()->input("checkbox", $tableName, $tableName, "","","","","", "checked")
// 		.pl(4, "<table class='table-hover table-striped'>$cols</table>")
// 	  ];
// 	}//die;
// 	echo "<form action='' method='post'>";
// 	submit("pam", "Soumettre");
// 	echo collapse($aa, "", "",true);
// 	echo "</form>";

// 	card("", ob_get_clean());
//   }


?>