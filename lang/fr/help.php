<?php

$backWithInfo = "Retourne vers la page précédante avec les anciennes infomations du formulaire, sinon ne fait rien";
$optionsDoc = 'Génère une liste d\'option pour le champ select de formulaire

'.b('Ex 1 :').'

$fruits = ["Mangue", "Orange", "Banane"];

'.b('1 : options($fruits);').'
'.b('2 : options($fruits, "", "", 3);').'

'.b('Result 1 :').'
&lt;option value="Mangue"&gt;Mangue&lt;/option&gt;
&lt;option value="Orange"&gt;Orange&lt;/option&gt;
&lt;option value="Banane"&gt;Banane&lt;/option&gt;

'.b('Result 2 :').'
&lt;option value="Mangue"&gt;Mangue&lt;/option&gt;
&lt;option value="Orange"&gt;Orange&lt;/option&gt;
&lt;option value="Banane" selected &gt;Banane&lt;/option&gt;

'.b("Ex 2 : ").'

$fruits = [
    ["id"=>1, "libelle"=>"Mangue", "prix"=>250, "qte"=>10],
    ["id"=>2, "libelle"=>"Orange", "prix"=>200, "qte"=>11],
    ["id"=>3, "libelle"=>"Banane", "prix"=>150, "qte"=>15]
];

'.b('options($fruits, "id", "libelle, prix", 2);').'

'.b('Result :').'
&lt;option value="1"&gt;Mangue 250&lt;/option&gt;
&lt;option value="2" selected&gt;Orange 200&lt;/option&gt;
&lt;option value="3"&gt;Banane 150&lt;/option&gt;

'.b('Paramettres').'
@param mixed $datas
@param mixed $selectName
@param string $id_val
@param string $fielList
@param string $defaulValueId
';

    //key1 [and key2] => [syntaxe/signature, retour, documentation]
    $helpMsg = [
        'options' =>
           ['options($datas, $selectName, $id_val="", $fielList="", $defaulValueId="")',
            'string', $optionsDoc],
        'optime' =>
           ['optime($datas, $selectName, $id_val="", $fielList="", $defaulValueId="")',
            'string', $optionsDoc],
        
        'validation' => [
            'between' => [
                "between:min,max",
                $backWithInfo,
                "The field under validation must have a size between the given min and max. Strings, numerics, arrays, and files are evaluated in the same fashion as the <a href='...'>size</a> rule."],
            'min' => [
                "min:value",
                $backWithInfo,
                "The field under validation must have a size between the given min and max. Strings, numerics, arrays, and files are evaluated in the same fashion as the <a href='...'>size</a> rule."],
        ]
    ];