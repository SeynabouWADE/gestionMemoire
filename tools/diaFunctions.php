<?php
function transformValue($array = [])
{
    $result = "";
    $result .= '
		<table>
		<head>
		<tr>';
    foreach ($array as $service) {
        $result .= '<td>' . $service . '</td>	</tr></head>
			<tbody>
			<tr>';
        foreach ($service as $key => $value) {
            '<td>' . $key . '=>' . $value . '</td>';
        }
        '</tr>';
    }
    '</tbody></table>';
    return $result;
}
function countGroupByFields($data, $onlyFields = [], $withoutFields = [])
{
    if (empty2($onlyFields))
        $onlyFields = getKeys(first($data));
    if (notEmpty($withoutFields))
        unsetValues($onlyFields, $withoutFields);
    foreach ($onlyFields as $field) {
        $result[$field] = [];
    }
    foreach ($data as $patient) {
        foreach ($onlyFields as $field) {
            $val = get($patient, $field);
            $result[$field][$val] =
                tern(get($result[$field], $val), 0) + 1;
        }
    }
    return $result;
}
function groupByFields($data, $onlyFields = [], $withoutFields = [])
{
    if (empty2($onlyFields))
        $onlyFields = getKeys(first($data));
    if (notEmpty($withoutFields))
        unsetValues($onlyFields, $withoutFields);
    foreach ($onlyFields as $field) {
        $result[$field] = [];
    }
    foreach ($data as $patient) {
        foreach ($onlyFields as $field) {
            $val = get($patient, $field);
            $result[$field][$val][] = $patient;
        }
    }
    return $result;
}
function globalDataCounted($data, $onlyFields = [], $withoutFields = [])
{

    if (empty2($onlyFields))
        $onlyFields = getKeys($data);
    if (notEmpty($withoutFields))
        unsetValues($onlyFields, $withoutFields);
    foreach ($onlyFields as $field) {
        $result[$field] = [];
    }
    foreach ($onlyFields as $field) {
        foreach (get($data, $field) as $subField => $data2)
            $result[$field][$subField] = countGroupByFields($data2, [], $withoutFields);
    }
    return $result;
}
function statForm($tableName, $title, $inputFieldName, $outputFieldName, $withoutFields, $submitButtonName)
{
    // je veux recuperer les colonnes de la table données clinique, mais le premier 'donnee_clinique' c'est pas logique mais sans lui ça ne marche pas
   // $statsTabColumns = getKeys(toArray($model->getTables_obj($tableName))[$tableName]);
   $statsTabColumns = getKeys($tableName);
   
   if ($withoutFields != '') {

        foreach ($withoutFields as $Onefield) {
            if (in_array($Onefield, $statsTabColumns)) {
                if (($key = array_search($Onefield, $statsTabColumns)) !== false) {
                    unset($statsTabColumns[$key]);
                }
            }
        }
    }
    $statsTabColumns = changeKeys($statsTabColumns, $statsTabColumns);
    ob_start(); ?>
    <form method="post" action="#">
        <div class="row">
            <div class="col-md-3">
                <?php
                select("Selectionner une variable explicative", $inputFieldName, options($statsTabColumns, $inputFieldName, "", "")); ?>
            </div>
            <div class="col-md-7">
                <?php
                select("Selectionner une ou des variable(s) à expliquer", $outputFieldName . "[]", options($statsTabColumns, $outputFieldName . "[]"), ""); ?>
            </div>
            <div class="col-sm">
                <?php submit($submitButtonName, "soumettre"); //"btn btn-fill btn-wd col-md-2 btn-primary"  
                ?>
            </div>
        </div>
    </form>
<?php $data = ob_get_clean();
    return  card("", $data, "", "", "", "border:2px solid rgb(159,82,176) ", "");
}
function show2()
{
    $this->view("stat", a("show2", route("statShow")), "", "Salam");
}

/**
 * Cette fonction permet d'avoir une barre de legende en fonction des couleurs.
 * Elle prend un seul element en paramètre la hauteur de la barre.  
 */
function colorPalette($valuesArr = progressBarColor, $terminalOrientation="", $terminalPosition="top", $barHeight = "10px")
{   
    
    $isOnTop=ternary($terminalPosition=="top", '<strong class="float-left font-weight-bold">'.ternary($terminalOrientation=="inverted", "Valeure maximale", "Valeure minimale" ).'</strong><strong class="float-right font-weight-bold">'.ternary($terminalOrientation=="inverted", "Valeure minimale", "Valeure maximale" ).'</strong><br>', '');
    $isOnBottom=ternary($terminalPosition=="bottom", '<strong class="float-left font-weight-bold; mt-0">'.ternary($terminalOrientation=="inverted", "Valeure maximale", "Valeure minimale" ).'</strong><strong class="float-right font-weight-bold">'.ternary($terminalOrientation=="inverted", "Valeure minimale", "Valeure maximale" ).'</strong>', '');
    $result =    $isOnTop.'<div class="progress" style="height:' . $barHeight . '; margin:0">';
    $n = count($valuesArr);
    foreach ($valuesArr as $OneColor) {
        $result .= '<div class="progress-bar" role="progressbar" style="width: ' . (100 / $n) . '%; background-color:' . $OneColor . '" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>';
    }
    return $result .= '  </div>'.$isOnBottom;
}
/**
 * Cette fonction permet d'obtenir une bare de couleur progressive en fonction des pourcentage, 
 * on peut aussi lui attribuer different valeurs a savoir la couleur, la hauteur.
 * On peut aussi choisir d'afficher la valeur absolue qui lui est associée ou le pourcentage de progression de la barre
 */
function progressBar($percentage, $color = "#68FF33", $height = "", $showValue = true, $value = "", $valueSuffixe = " ", $margin = "0px") // Margin= "top right bottom left
{
    $margin = ternary2($margin, "margin:", ";");
    if ($color[0] == "#") {
        $styleColor = $color;
        $progressColor = "";
    } else {
        $styleColor = "";
        $progressColor = $color;
    }
    $showValue = $showValue ? ' <div class="row justify-content-center"><span class="font-weight-bold">' . ternary($value != "",  $value, $percentage) . $valueSuffixe . '</span></div>' : "";
    return '
<div  style="width:100%">' . $showValue . '
    <div class="progress" style="height:' . $height . ';' . $margin . '">
        <div class="progress-bar progress-bar-' . $progressColor . '" role="progressbar" aria-valuenow="' . $percentage . '"aria-valuemin="0" aria-valuemax="100" style="width:' . $percentage . '%; background-color:' . $styleColor . '">
        </div>
    </div>
</div>';
}
/**
 * Un moyen d'avoir un rendu plus robuste que notre fonction dataCompare ci dessous
 * L'objectif est d'aller recuperer la colone otherTab de l'enregistrement correspondant a la table en cours 
 * dans tables_info apres une serie de transfomration on doit obtenir au final un tableau associatif ayant en clé les colones de la tables  en cours et en value les tables associés a ces colones
 */
function tablesInfosOtherTabValues($getTableName)
{
    $model = model();
    $otherTab = getKeys(toArray($model->get("tables_info", ["name" => $getTableName])[0]["other_tab"], ",")); // Acceder a ohter tab de la table en cours d'utilisation
    $valeurs = $cles = [];
    foreach ($otherTab as $key => $value) {
        $value2 = toArray($value, "->");
        array_push($valeurs, $value2[0]);
        if (array_key_exists(1, $value2)) {
            array_push($cles, $value2[1]);
        } else {
            array_push($cles, $value2[0]);
        }
    } 

    return  array_keys_replace($cles, $valeurs);
}
/**
 * Construction du other tab; dans la requete principale de notre table, ceci nous permet juste de recuperer les libelle sur les entetes des colones au lieu des id
 * On s'assure d'abord que la variable explicative soit une clé etrangere
 * Si oui on construit son other tab pour l'inserer dans la requete
 * Si non on retourne une chaine vide
 */
function otherTabInSqlRequest($tableName, $inputTag)
{
    $otherTab = tablesInfosOtherTabValues($tableName);
    $otherTabValue = ["libelle"];
    if (in_array($inputTag, getKeys($otherTab))) {
        $otherTabkey = $inputTag . '->' . $otherTab[$inputTag];
        return [$otherTabkey => $otherTabValue];
    } else {
        return "";
    }
}
/**
 * Cette fonction prend en parametre un tableau. Son objectif est de parcourir le tableau et de verifier s'il y'a une correspondance entre les clés et les tables de la base de donnée.
 * Si oui elle nous retourne la table correspondante et les données.
 */
function getAssocTablesData($data)
{
    $model = model();

    foreach ($data as $key => $where) {
        if (dim($where) == 1) {
            $Alldata[$key] = $model->getWithIdAsKey_obj($key, $where); // On recupere l'ensemble des prelevement de la base de données
        }
        if (dim($where) == 2) {
            foreach ($where as $key2 => $where2) {
                $Alldata[$key][$key2] = $model->getWithIdAsKey_obj($key, $where2); // On recupere l'ensemble des prelevement de la base de données
            }
        }
    }

    return $Alldata;
}

/** $data notre objet global(Ensemble des patients), $params les parametres que l'on veut recuperer (Ex: Prélevements, Sérvices, Hyperthermie).
 *  On parcours les données recuperés pour chaque object et on recupere son libellé associé que l'on ajoute dans le tableau $temptab.
 *  Chaque attribut n'est ajouté qu'une seul fois pas de doublons dans cette table.
 */
function getBdKeysValues($data, array $params)
{
    $temptab = [];
    foreach ($params as $tablescolum) {
        $subtable = [];
        foreach ($data as $value) {
            if (isset($value[$tablescolum]) && !in_array($value[$tablescolum], $subtable)) {
                array_push($subtable, $value[$tablescolum]);
            }
        }
        array_push($temptab, $subtable);
    }

    return array_keys_replace($temptab,  $params);
}
/**
 * prends en parametre deux tableaux
 * $array tableau dans le quel on veux remplacer les index par des valeur associatives
 * $keys tableau contenant les nouvelles clés que l'on veux injecter dans $array
 */
function array_keys_replace($array, $keys)
{
    foreach ($keys as $key => $value) {
        if (isset($array[$key])) {
            $array[$value] = $array[$key];
            unset($array[$key]);
        }
    }
    return $array;
}
/**
 * On compare les prelevements existant dans la table prelevement que l'on compare avec les prelevements des patients et on enleve 
 * les prelevements qui n'existent pas au niveau des patients.
 * Cette fonction a des limites pour satisfaire mes besoins parce que:
 * 1- La generalisation est Quasi impossible car on doit avoir des clé homogenes sinon la fonction
 * sera trop longue pour des futilités
 * 2- IL se peut que certains parametres proviennent de la meme table Ex: Fibrenemie, vs, sida, Cancer, etc.. qui proviennent de la table n_o_n
 * IL n'en prendra qu'un les autres, so need to find anohter way.
 * Aulieu d'attquer les tables associés a la tables etudie je vais essayer de passer dans othertab de la table etudié pour recuperer
 * les parametres et revenir sur les tables associées 
 */
function DataCompare(array $AssociativesTables, array $Comparaison, $otherTab)
{
    $secondTab = [];
    $fourthTable = [];
    $fifthTable = [];
    $otherTabTable = [];
    $assocTab = [];
    foreach ($AssociativesTables as $key => $value) {
        foreach ($otherTab as $keyOtherTab => $valueOtherTab) {
            if ($key == $valueOtherTab && isset($Comparaison[$keyOtherTab])) {
                array_push($otherTabTable, $keyOtherTab);
                foreach ($value as $key2 => $value1) {
                    if (!in_array($key2, $Comparaison[$keyOtherTab])) {
                        unsetValues($value, $value1);
                    }
                }
                array_push($secondTab, $value);
            }
        }
    }
    foreach ($Comparaison as $ComparaisonKey => $ComparaisonValue) {
        if (!isset($otherTab[$ComparaisonKey])) {
            array_push($otherTabTable, $ComparaisonKey);
            $ComparaisonValue = array_Keys_replace($ComparaisonValue, $ComparaisonValue);
            $i = 0;
            foreach ($ComparaisonValue as  $ComparaisonSubvalue) {
                $ComparaisonSubvalue = toArray($ComparaisonSubvalue);
                array_push($fourthTable, array_Keys_replace($ComparaisonSubvalue, ['libelle']));
                $i++;
                if ($i == count($ComparaisonValue)) {
                    foreach ($fourthTable as $key => $value) {
                        array_push($assocTab, $value['libelle']);
                    }
                    $fourthTable = array_Keys_replace($fourthTable, $assocTab);
                    array_push($fifthTable, $fourthTable);
                    $i = 0;
                    $fourthTable = $assocTab = [];
                }
            }
        }
    }
    foreach ($fifthTable as $key => $value) {
        array_push($secondTab, $value);
    }
    return array_keys_replace($secondTab, $otherTabTable);
}
/**
 * La fonction addcolumn2, permet d'ajouter les libelles associés au cardinalités du tableau,
 * On Affiche le tableau des données et une ceci fait, on cole a gauche les prelevements et en haut les services
 */
function addColumn2(array $data, $secondParameter)
{

    echo "<table style='width:20%;' class='table'>";
    foreach ($data as $key => $value) {
        if (getType($value) != 'array') {
            $value = toArray($value);
            echo "<tr><td>" . $value[$secondParameter] . "</td></tr>";
        } else {
            echo "<tr><td>" . $value[$secondParameter] . "</td></tr>";
        }
    }
    echo "</table>";
}
function tableTags($data, $attributs = "", $defaulAttr = "border='collapse'")
{
    return "<table $defaulAttr $attributs>$data</table>";
}
function trTags($data, $attributs = "")
{
    return "<tr $attributs >$data</tr>";
}
function addRow2($data, $attributs = "", $tdAddStyle = "")
{
    $row = "<tr $attributs>";
    if (getType($data) == "array") {
        foreach ($data as $value) {
            foreach ($value as $key => $value2) {
                if (notempty($key)) {
                    $row .= "<td style=" . $tdAddStyle . ">" . $key . "</td>";
                }
            }
        }
    } elseif (getType($data) === "string") {
        $row .= "<td style='$tdAddStyle'>" . $data . "</td>";
    }
    $row .= "</tr>";
    return $row;
}
function printmarraytable(array $data)
{
    echo "<table border class='table'>";
    foreach ($data as $key => $value) {
        echo "<tr><td>" . $key . "</td>";
        if (is_array($value) || is_object($value)) {
            echo "<td>" . printmarraytable($value) . "</td>";
        } else {
            echo "<td>" . $value . "</td></tr>";
        }
    }
    echo "</table>";
}
function progressBarLine($array, $i, $j, $statArray, $sameLength = true)
{
    $value = $array[$i][$j];
    if ($sameLength) {
        $ptg = 100;
    } else {
        $ptg = intval($array[$i][$j] * 100  / $statArray[0][$i][1]);
    }
    $colorIndex1 = $array[$i][$j] * (count(progressBarColor) - 1) / $statArray[0][$i][1];
    return progressBar($ptg, progressBarColor[$colorIndex1], "5px", true, $value);
}
function progressBarColumn($array, $i, $j, $statArray, $sameLength = true)
{
    $value = $array[$i][$j];
    if ($sameLength) {
        $ptg = 100;
    } else {
        $ptg = intval($array[$i][$j] * 100  / $statArray[1][$j][1]);
    }
    $colorIndex2 = $value * (count(progressBarColor) - 1) / $statArray[1][$j][1];
    return progressBar($ptg, progressBarColor[$colorIndex2], "5px", true, $value);
}
function progressBarLineAndColumn($array, $i, $j, $statArray, $sameLength = true)
{
    $value = $array[$i][$j];
    if ($sameLength) {
        $ptgLine = 100;
        $ptgColumn = 100;
    } else {
        $ptgLine = intVal($array[$i][$j] * 100  / $statArray[0][$i][1]);
        $ptgColumn = intVal($array[$i][$j] * 100  / $statArray[1][$j][1]);
    }
    $colorIndex1 = $value * (count(progressBarColor) - 1) / $statArray[0][$i][1];
    $colorIndex2 = $value * (count(progressBarColor) - 1) / $statArray[1][$j][1];	
    return progressBar($ptgLine, progressBarColor[$colorIndex1], "5px", false) . "" . progressBar($ptgColumn, progressBarColor[$colorIndex2], "5px", true, $value);
}
function stylizeArray($array, $fctName, $otherData = null)
{
    $n = count($array);
    for ($i = 0; $i < $n; $i++) {
        if (is_array($array[$i])) {
            $m = count($array[$i]);
            for ($j = 0; $j < $m; $j++) {
                if ($otherData != null)
                    $array[$i][$j] = $fctName($array, $i, $j, $otherData);
                else
                    $array[$i][$j] = $fctName($array, $i, $j);
            }
        } else {
            if ($otherData != null)
                $array[$i] = $fctName($array, $i, $otherData);
            else
                $array[$i] = $fctName($array, $i);
        }
    }
    return $array;
}
function statOfArray($mat)
{
    $B = array();
    $n = count($mat);
    for ($i = 0; $i < $n; $i++) {
        $B[$i][0] = $mat[$i][0];
        $B[$i][1] = $mat[$i][0];
        $B[$i][2] = 0;
        $m = count($mat[$i]);
        for ($j = 0; $j < $m; $j++) {
            if ($mat[$i][$j] < $B[$i][0]) {
                $B[$i][0] = $mat[$i][$j];
            }
            if ($mat[$i][$j] > $B[$i][1]) {
                $B[$i][1] = $mat[$i][$j];
            }
            $B[$i][2] = $B[$i][2] + $mat[$i][$j];
        }
        $B[$i][3] = 1.0 * $B[$i][2] / $m;
    }
    return $B;
}
function seeMatrice(array $data)
{
    echo "
<table border='collapse' class='table'><tr class='blank_row'></tr>";
    foreach ($data as $value) {
        echo "<tr style='white-space: nowrap;'>";
        foreach ($value as $val2) {
            echo "<td style='white-space:;width:7%'>$val2</td>";
        }
        echo '</tr>';
    }
    echo "</table>";
}
/**
 * Cette fonction gere l'affichage des données dans le tableau  il faut beaucoup travailler ici
 * Ex:  peltier fustec susi 
 * urine  10      30      20
 * selles 20      2      34
 *   ''   ''     ''     ''
 * Vous allez essayer de placer les libelle(prelevements et services) au niveau du tableau de données
 * 
 */
function displaySumarryTable($data,  $leftColumnName, $inputTag, $tabReorder, $tableName="")
{
    /**
     * On reorganise les libellés des colones a gauches $reorderedLeftColumnName
     */
    $AssocArray = [];
    $reorderedLeftColumnName = [];
    foreach ($tabReorder as $value) {
        foreach ($leftColumnName as $key => $value2) {
            if ($key === $value && ($key != $inputTag)) {
                foreach ($value2 as $key3 => $value3) {
                    if (is_Array($value3))
                        array_push($AssocArray, $value3['libelle']);
                    else
                        array_push($AssocArray, toArray($value3)['libelle']);
                }
                $value2 = array_Keys_replace(array_values($value2), $AssocArray); //array_values() permet de reconvertire un tableau associatif a un tableau indéx
                array_push($reorderedLeftColumnName, $value2);
                $AssocArray = [];
            }
        }
    }
    $reorderedLeftColumnName = array_Keys_replace($reorderedLeftColumnName, $tabReorder);
    $mat = [];
    foreach ($data as $key => $val) {
        foreach ($val as $key2 => $val2) {
            $i = 0;
            foreach ($val2 as $key3 => $val3) {
                foreach ($leftColumnName as $key => $value) {
                    if ($key != $inputTag) {
                        if (get($val2, $key)) {
                            if ($key3 == $key) {
                                foreach ($value as $key4 => $val4) {
                                    $mat[$i][] = tern(get($val3, $key4), "0");
                                    $i++;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $otherData = [statOfArray($mat), statOfArray(lineToColumn($mat))];
    $bigData = mergeArrayAtSheets(subArray($reorderedLeftColumnName, 3, 'libelle'),stylizeArray($mat, "progressBarLine", $otherData), 2);
    $headCol1 = $inputTag;
    $headCol2 = get(subArray($data, 2), $headCol1);
    foreach ($headCol2 as $key => $value) {
        if (!$key) {
            $headCol2[$key] = $value = "Non classé"; // Pour les clé vides on les met dans le groupe des non classés
        }
    }
    if (isset($mat[0])) {
        echo "<div class='table-responsive'><table class='table table-hover table-striped table-fixed' id=$tableName><tr>";
        echo "<th colspan='2' rowspan='2'></th><th colspan='" . count($headCol2) . "' class='text-center'>" . ucfirst($headCol1) . "</th>";
        echo "</tr>";
        echo "<tr>";
        echo "<th>" . implode("</th><th>", $headCol2) . "</th>";
        echo "</tr>";

        drowTable($bigData, 2, count($mat[0]));
        echo "</table></div>";
    } else {
        echo "<div class='container'>
  <div class='row justify-content-center'>
    <h1 class='text-primary'>Il n'y a pas de données à afficher</h1><i class='material-icons' style='font-size:50px;color:rgb(159,82,176); margin-left:2%'>important_devices</i>
    </span>
  </div>
</div>";
    }
    
}
function yeee()
{
    $otherTab = "";
    $insertIntoField_infos = "";
    $fieldInfosArray = array();
    $htmlType = "";
    $tableChar = array();
    $model = model();
    $dbtables = toArrayOfArray($model->getTables_obj(true));
    $fieldInfos = get($dbtables, 'fields_info2');
    //sd(post());
    if (post()) {
        foreach (post() as $tableName => $table) {
            if (gettype($table) === "array") {
                $otherTab = "";
                $insertIntoField_infos = "";
                foreach ($table as $colone) {
                    if (startsWith(get($dbtables, $tableName)[$colone]["Type"], ['int'])) {
                        if (($colTable = get($dbtables, $colone)) && get($colTable, "libelle")) { //est ce que c'est une table dans la bd
                            $otherTab .= '"' . $colone . '":"libelle",';
                        }
                    }
                }
                $otherTab = ternary2(trimPlus($otherTab), "{", "}");
                /*	seec($tableName.",".ucfirst(str_replace('_', ' ', $tableName)).",".ucfirst(plural($tableName, 's', $tableName)).","
              .snakeToCamelCase($tableName)."Create,".snakeToCamelCase($tableName)."Update,".snakeToCamelCase($tableName)."Show, 
              ".snakeToCamelCase($tableName)."Download,".snakeToCamelCase($tableName)."Print,".snakeToCamelCase($tableName)."Delete, '$otherTab'");
              */
                if (!get($fieldInfos, $colone)) {
                    $insertIntoField_infos = "";
                    if (startsWith(get($dbtables, $tableName)[$colone]["Type"], ['int'])) {
                        $htmlType = "number";
                    } elseif (startsWith(get($dbtables, $tableName)[$colone]["Type"], ['enum'])) {
                        $htmlType = "select";
                    } else {
                        $htmlType = "text";
                    }
                    $insertIntoField_infos = "('" . $colone . "','" . ucfirst(str_replace('_', ' ', $colone)) . "','" . $htmlType . "')";
                    if (!in_array($insertIntoField_infos, $fieldInfosArray)) {
                        array_push($fieldInfosArray, $insertIntoField_infos);
                    }
                }
            }
        }
    }
    //seec(ToString($fieldInfosArray));
    ob_start();
    $fieldsInfo = $model->getWithNameAsKey_obj("fields_info");
    // $fieldsInfoColNames = getKeys($fieldsInfo);


    foreach ($model->getTables_obj(true) as $tableName => $infos) {
        if (in($tableName, ["fields_info", "tables_info", "user", "user_type", "menu"]))
            continue;

        $cols = "";
        foreach ($infos as $colName => $colFiels) {
            $fullNameChecked = get($fieldsInfo, "$tableName.$colName") ? "checked" : "";
            $checked = get($fieldsInfo, $colName) ? "checked" : "";

            $cols .=
                "<tr>" .
                "<td>" . ob()->input("radio", $colName, $tableName . "[$colName]", $colName, $colName, $colName, "", $tableName, $checked) . "</td>" .
                "<td>" . ob()->input("radio", "$tableName.$colName", $tableName . "[$colName]", "$tableName.$colName", "$tableName.$colName", "", "", $tableName, $fullNameChecked) . "</td>" .
                "</tr>";
        }
        $aa[$tableName] = [
            ob()->input("checkbox", $tableName, $tableName, "", $tableName, "", "", "", "checked")
                . pl(4, "<table class='table-hover table-striped'>$cols</table>")
        ];
    } //die;
    echo "<form action='' method='post'>";
    submit("pam", "Soumettre");
    echo collapse($aa, "", "", true);
    echo "</form>";

    card("", ob_get_clean());
}
function subArray($myArray, $MAXDEPTH, $fieldAtMaxDepth = "", $depth = 0)
{
    $depth++;
    $arrayKeys = [];

    if ($depth <= $MAXDEPTH) {
        $keys = array_keys($myArray);
        foreach ($keys as $key) {
            if (is_object($myArray[$key]))
                $myArray[$key] = toArray($myArray[$key]);
            if (is_array($myArray[$key])  && notempty($myArray[$key])) {
                $tmp = subArray($myArray[$key], $MAXDEPTH, $fieldAtMaxDepth, $depth);

                if (is_object($myArray[$key]))
                    $tmp = toArray($tmp);

                if ($depth == $MAXDEPTH) {     // empty($tmp)
                    $arrayKeys[$key] = $key;
                } else
                    $arrayKeys[$key] = $tmp;
            } else if ($depth == $MAXDEPTH && ($fieldAtMaxDepth == "" || $key === $fieldAtMaxDepth)) {
                $arrayKeys[$key] = $myArray[$key];
            }
        }
    }
    return $arrayKeys;
}
function getNumberOfSheets($myArray, &$nbr)
{
    if (!is_array($myArray))
        return 1;
    $arrayKeys = [];
    foreach (array_keys($myArray) as $key) {
        if (is_object($myArray[$key]))
            $myArray[$key] = toArray($myArray[$key]);
        if (is_array($myArray[$key])  && notempty($myArray[$key])) {
            $arrayKeys[$key] = getNumberOfSheets($myArray[$key], $nbr);
        } else {
            $nbr++;
        }
    }
}
function drowTable($myArray, $sheetLevel, $sheetLength = 1, $levelCurr = 1, $levelLast = 1)
{
    $arrayKeys = [];
    foreach (array_keys($myArray) as $key) {

        $rowspan = "";
        $nbrSheet = 1;
        if ($levelCurr < $sheetLevel) {
            $nbrSheet = 0;
            getNumberOfSheets($myArray[$key], $nbrSheet);
            $nbrSheet = $nbrSheet / $sheetLength;
            $rowspan = "";
            if ($nbrSheet > 1)
                $rowspan = " rowspan='$nbrSheet'";


            if ($levelCurr <= $levelLast) {
                echo "<tr>";
            }
        }
        if (is_object($myArray[$key]))
            $myArray[$key] = toArray($myArray[$key]);
        if (is_array($myArray[$key])  && notempty($myArray[$key])) {
            echo "<td$rowspan>$key</td>";
            $levelLast = $levelCurr;
            $levelCurr++;
            $arrayKeys[$key] = drowTable($myArray[$key], $sheetLevel, $sheetLength, $levelCurr, $levelLast);
            $levelCurr--;
            $levelLast = $levelCurr - 1;
            echo "</tr>";
        } else {
            echo "<td$rowspan>$myArray[$key]</td>";
        }
    }
}
function mergeArrayAtSheets($myArray, $valArr, $MAXDEPTH, $depth = 0, &$i = 0)
{
    $depth++;
    $arrayKeys = [];

    if ($depth <= $MAXDEPTH) {
        $keys = array_keys($myArray);
        foreach ($keys as $key) {
            if (is_array($myArray[$key])  && notempty($myArray[$key])) {
                $tmp = mergeArrayAtSheets($myArray[$key], $valArr, $MAXDEPTH, $depth, $i);
                if ($depth == $MAXDEPTH)
                    $arrayKeys[$key] = $valArr[$i++];
                else
                    $arrayKeys[$key] = $tmp;
            } else if ($depth == $MAXDEPTH) {
                $arrayKeys[$key] = $valArr[$i++];
            }
        }
    }
    return $arrayKeys;
}
function getSheet($myArray, $MAXDEPTH, $depth = 0)
{
    $depth++;
    $arrayKeys = [];
    $keys = getKeys($myArray);
    foreach ($keys as $key) {
        $tmp = get($myArray, $key);
        if (is_object($tmp) || (is_array($tmp)  && notEmpty($tmp))) {
            $tmp = toArray($tmp);
        }
        if (is_array($tmp)  && notEmpty($tmp)) {
            if ($depth == $MAXDEPTH)
                $arrayKeys[] = $key;
            getSheet($tmp, $MAXDEPTH, $depth);
        } else if ($depth == $MAXDEPTH) {
            $arrayKeys[] = $key;
        }
    }
    return $arrayKeys;
}

function getWithLevel($data, $level)
{
    $ArrayKeys = [];
    if (dim($data) < $level)
        return [];
    $isObject = false;
    if (is_object($data)) {
        $isObject = true;
        $data = toArray($data);
    }
    foreach ($data as $key => $value) {
        $ArrayKeys[$value] = getWithLevel($data, $level - 1);
    }
    return $ArrayKeys;
}
function getSubData($requestedData, $subData, $primaryKey, $foreignKey, $requestedTableName="", array $params=[]){
    $finalAray=[];
    $model = model();
    $countParams= count($params);
    if($requestedTableName!=""){
        $ohterTabData= tablesInfosOtherTabValues($requestedTableName);
    }
    foreach (toArray($requestedData) as $value1) {
        foreach (toArray($subData) as $value2) {
            if ($value1[$primaryKey] == $value2[$foreignKey]) {
                unset($value2[$foreignKey]);
                if($countParams !=0){
                    $i = 0;
                    foreach($params as $paramValue){
                           //see($value2[$paramValue]);die;
                           if($requestedTableName==""){
                            foreach ($value2 as $key => $oneAttr) {
                                array_push($value1, $oneAttr);
                                $value1[$key] = $value1[$i];
                                unset($value1[$i]);
                                $i++;
                            }
                           }
                          else{
                            foreach($ohterTabData as $ohterTabDataKey=> $ohterTabDataValue){
                                if($paramValue == $ohterTabDataKey){
                                    foreach (toArray($model->get_obj($ohterTabDataValue)) as $OneohterTabRecord) {
                                      if($OneohterTabRecord['id']===$value2[$paramValue]){
                                          array_push($value1,$OneohterTabRecord['libelle']);
                                          if(isset($value1[$i])){
                                            $value1[$paramValue]= $value1[$i];
                                            // see("value1",$value1); die;
                                             unset($value1[$i]);
                                             $i++;
                                          }
                                      
                                      }
                                    }
                                }
                            }
                          }
                       }
                       array_push($finalAray, $value1);
                }
            }
        }
    }
    return $finalAray;
}
?>