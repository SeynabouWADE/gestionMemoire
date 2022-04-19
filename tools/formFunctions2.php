<?php
function select($label, $name, $options, $id="", $describetion="", $otherClass = "", $othersAtt="", $labelClassFloatingOr=true, $icon="", $title="", $buttonNextToField = ""){
	$multiSelect = "";
	if(in('[', $name)){
		$multiSelect = $name;
		$name = first($name, '[');
	}
	formLabel($name, $label);
	if($labelClassFloatingOr)	$labelClassFloatingOr="bmd-label-floating ";
	if($id == "") $id = $name;
	$describeId = "descpam".$id;
	$describetionAtt = $describetion ? ' aria-describedby="'.$describeId.'"'  : '';
	$describetion = $describetion ? '<small id="'.$describeId.'" class="form-text text-muted" style="line-height:1.2em">'.$describetion.'</small>' : '';
	
	$inputOrForm = "form-group";
	if($icon){
		$inputOrForm = "input-group";
		$icon = '<span class="input-group-addon">'.$icon.'</span>';
	}
	if($title)
		echo '<div style="margin:8px 0 -10px 0" class="'.$name.'TitleForm" id="'.$name.'TitleForm">'.$title.'</div>';	
	
	if($othersAtt){
		$othersAtt0 = toArray($othersAtt, "/");
		$othersAtt = $othersAtt0[0];
		$buttonNextToField .= get($othersAtt0, 1);
		$othersAtt = " ".evalStrContainingFct($othersAtt);
	}	
	$buttonNextToField = buttonNextToField($buttonNextToField, $id);
	
	echo $buttonNextToField[0]; //addRow; ?>
	<div id="block1<?=$id?>" class="<?=$inputOrForm?> bmd-form-group <?= has_error($name)?>" > <?= $icon ?>
		<label for="<?= $id ?>" <?= 'class="bmd-label-static '.$name.'TitleForm" id="'.$name.'TitleForm"'?>>
			<?= $label ?>
		</label>
		<select name="<?= $multiSelect ? $multiSelect : $name ?>" id="<?= $id ?>" <?= $multiSelect ? 'multiple ':' '?>class="form-control selectpicker<?= has_error($name, true) ? ' error' : ''?><?= $otherClass ? ' '.$otherClass : ''?> "<?= $othersAtt?><?= has_error($name, true) ? ' aria-invalid="true"' : ''?><?=$describetionAtt?> data-style="btn btn-link">
			<?= $options ?>
		</select>
		<?=$describetion?>
		<?= getError($name, "", "", $id) ?>
	</div><?php
	echo $buttonNextToField[1]; //addButton_RowEnd
	echo $buttonNextToField[2]; //$block2;

}
//TODO le checked par défaut il faut le mettre dans $othersAtt suivant des conditions...
/** $buttonNextToField : type = "", _blank or modale*/
function input($type, $label, $name, $defaulValue="", $id="", $placeholder="", $describetion="", $otherClass="", $othersAtt="", $labelClassFloatingOr=true, $icon="", $title="", $radioCheckDefaulf="", $name2 = "", $buttonNextToField = []){ //TODO voir est ce que title est utile. Peut etre au moins ne pas l'afficher si c'est vide
	/*if(! $name2)*/	$name2 = $name;
	$type = trim(toLower($type));
	if($type == "hidden"){
		echo "<input type='hidden' name='$name2' id='".tern($id, $name)."' value='$defaulValue'>";
		return;
	}
	if($title)
		$title = '<div style="margin:-.3rem 0 0 -.7px" class="'.$name.'TitleForm" id="'.$name.'TitleForm">'.$title.'</div>';
	$placeholder = $placeholder ? ' placeholder="'.$placeholder.'"' : '';
	$otherClass = $otherClass ? ' '.$otherClass  : '';

	if(is_bool($labelClassFloatingOr))
		$labelClassFloatingOr = $labelClassFloatingOr ? "bmd-label-floating " : "";	//"label-floating " : "";
	else $labelClassFloatingOr = $labelClassFloatingOr." ";
	
	if($placeholder and $labelClassFloatingOr)
		$labelClassFloatingOr = str_replace("floating", "static", $labelClassFloatingOr);

	$nameLabel = $name."LabelForm";
	$inputOrForm = "form-group ";
	formLabel($name, $label); //session
	if($icon){
		$inputOrForm = "input-group ";
		$icon = '<span class="input-group-addon">'.$icon.'</span>';
		// <div class="input-group-prepend">
		// 	<span class="input-group-text">
		// 		<i class="material-icons">group</i>
		// 	</span>
		// </div>
	}

	/* if(notEmpty($buttonNextToField)){
		$addRow = '<div class="row"><div class="col-md-10 col-10">';
		$addButton_RowEnd = '</div><div class="col-md-2 col-2 pl-0 pt-2">'.a(buttonMini(iconFa("plus"), "secondary"), $buttonNextToField[0], "", get($buttonNextToField, 1), 'onclick="addToCreate(this, \''.$id.'\')"')."</div></div>";
	} */
	
	if($othersAtt){
		$othersAtt0 = toArray($othersAtt, "/");
		$othersAtt = $othersAtt0[0];
		if(empty2($buttonNextToField))
			$buttonNextToField = get($othersAtt0, 1);
		else if(isset($buttonNextToField[0]))
			$buttonNextToField[0] .= get($othersAtt0, 1);
		else {
			$buttonNextToField .= get($othersAtt0, 1);
		}
		$othersAtt = " ".evalStrContainingFct($othersAtt);
	}//sd($othersAtt);
		
	if($type == "checkbox" or $type == "radio"){
		if( ! $icon)
			$inputOrForm = "form-check";
	
		$checked = "";
		
		/* if($type == "checkbox" && $radioCheckDefaulf) 
		 	$checked = " checked"; */// TODO retester avec checkbox et valeurs par défaut

		if( ! $defaulValue)	$defaulValue = $name;
		else {
			if(is_string($defaulValue))
				$defaulValue = toArray($defaulValue);
				
			if(old($name) and in(old($name), $defaulValue)){
				$checked = " checked";
				$defaulValue = old($name);
				$othersAtt = str_replace("checked", "", strtolower($othersAtt));
			}else{
				$defaulValue = $defaulValue[0];
				if(empty(oldPost())){
					if(is_string($radioCheckDefaulf))
						$radioCheckDefaulf = toArray($radioCheckDefaulf);
					if(in($defaulValue, $radioCheckDefaulf))
						$checked = " checked";
				}
			}
		}
		if(! $id){
			$id = $name;	
			if($type == "radio")
				$id .= urlSanitizer($defaulValue);	
		}
		if($id){
			$describeId = $id;
		}/* else
		 	$describeId = "descpam".str_replace(" ", "", $defaulValue);*/
		$describetionAtt = $describetion ? ' aria-describedby="'.$describeId.'"'  : '';
		$describetion = $describetion ? '<small id="'.$describeId.'" class="form-text text-muted" style="line-height:1.2em">'.$describetion.'</small>' : '';

		echo $title;
		
		$buttonNextToField = buttonNextToField($buttonNextToField, $id);
		echo $buttonNextToField[0]; //addRow; ?>
		<div id="block1<?=$id?>" class="<?= $inputOrForm?> <?= has_error($name)?>">
			<?= $icon ?>
			<label <?= "class='form-check-label $nameLabel'"?>>
			  <input <?php
				echo "type='$type' name='$name2' value='$defaulValue' id='$id'$checked$othersAtt";
				echo ' class="form-check-input'; echo has_error($name, true) ? ' error'.$otherClass.'" aria-invalid="true"' : ' '.trim($otherClass).'"' 
			  ?><?=$describetionAtt?>/><?= tern($label, "&nbsp;")?>
				<span class="<?= $type == "radio" ?	"circle" : "form-check-sign"?>">
					<span class="check"></span>
				</span>
			</label><?=$describetion?>
			<?= getError($name, "-5px", "", $id) ?>
		</div> <?php
		echo $buttonNextToField[1]; //addButton_RowEnd
		echo $buttonNextToField[2]; //$block2;
		return;
	}
	if($type == "submit"){
		submit($name, $defaulValue, "btn btn-fill btn-wd", $othersAtt);
		return;
	}
	
	if( ! $id)
		$id = $name;

	$buttonNextToField = buttonNextToField($buttonNextToField, $id);

	$describeId = "descpam".$id;
	$describetionAtt = $describetion ? ' aria-describedby="'.$describeId.'"'  : '';
	$describetion = $describetion ? '<small style="margin: 0 0 -.3rem 0" id="'.$describeId.'" class="form-text text-muted" style="line-height:1.2em">'.$describetion.'</small>' : '';

	$oldValue = ternary($type != "password", old($name, $defaulValue), "");
	
	echo $title;	
	echo $buttonNextToField[0]; //addRow; ?>
	<div id="block1<?=$id?>" class="<?= $inputOrForm.has_error($name) ?>">
		<?= $icon ?>
		<label for="<?= $id ?>" class="control-label <?= $labelClassFloatingOr.$nameLabel?>" id="<?= $nameLabel?>" ><?= $label ?></label>
		<input type="<?= $type ?>" 
		name="<?= $name2 ?>" id="<?= $id ?>" value="<?= $oldValue?>" class="form-control<?php echo has_error($name, true) ? ' error' : ""; echo $otherClass;?>"<?= $othersAtt ?><?= has_error($name, true) ? ' aria-invalid="true"' : '' ?><?=$placeholder?><?=$describetionAtt?>> 
		<?=$describetion?>
		<?= getError($name, "", "", $id) ?>
	</div> <?php
	echo $buttonNextToField[1]; //addButton_RowEnd
	echo $buttonNextToField[2]; //$block2;
}
function submit($name, $value="", $class = "btn btn-primary btn-wd", $othersAtt=""){
	//btn btn-next btn-fill btn-success btn-wd
	if(csrfControl)
		echo csrf();
	if(honeyPotControl)
		echo honeyPot(); ?>
	<input type="submit" name="<?= $name ?>" id="<?= $name ?>" value="<?= $value ?>" class="<?= $class ?>"<?= $othersAtt ?>/>
	<?php
}
function submitGet($name, $value="", $class = "btn btn-primary btn-wd", $othersAtt=""){
	//btn btn-next btn-fill btn-success btn-wd ?>
	<input type="submit" name="<?= $name ?>" name="<?= $name ?>" value="<?= $value ?>" class="<?= $class ?>" <?= $othersAtt ?>/>
	<?php
}
//TODO penser à mettre textarea dynamique (Grafikart)
function textarea($label, $name, $defaulValue="", $id="", $placeholder="", $describetion="", $otherClass="", $othersAtt="", $labelClassFloatingOr=true, $icon="", $title="", $buttonNextToField = ""){
	  
	formLabel($name, $label);
	if(is_bool($labelClassFloatingOr))
		$labelClassFloatingOr = ($labelClassFloatingOr ? 'bmd-label-floating ' : "");	//"label-floating " : "";
	else $labelClassFloatingOr = $labelClassFloatingOr." ";
	if($placeholder and $labelClassFloatingOr)
		$labelClassFloatingOr = str_replace("floating", "static", $labelClassFloatingOr);
	if($title)
		$title = '<div class="'.$name.'TitleForm" id="'.$name.'TitleForm">'.$title.'</div>';
	if($labelClassFloatingOr){
		$label = '<label class="'.$labelClassFloatingOr.$name.'LabelForm" id="'.$name.'LabelForm">'.$label.'</label>';
	}
	if($id == "") $id = $name;

	$placeholder = $placeholder ? ' placeholder="'.$placeholder.'"'  : '';
	$describeId = "descpam".$id;
	$describetionAtt = $describetion ? ' aria-describedby="'.$describeId.'"'  : '';
	$describetion = $describetion ? '<small id="'.$describeId.'" class="form-text text-muted" style="line-height:1.2em">'.$describetion.'</small>' : '';

	$inputOrForm = "form-group";
	if($icon){ //TODO tester les formulaires avec les icons
		$inputOrForm = "input-group";
		$icon = '<span class="input-group-addon">'.$icon.'</span>';
	}
	
	$class = 'class="form-control'.(has_error($name, true) ? ' error' : '').($otherClass ? ' '.$otherClass : '').'"';

	if($othersAtt){
		$othersAtt0 = toArray($othersAtt, "/");
		$othersAtt = $othersAtt0[0];
		$buttonNextToField .= get($othersAtt0, 1);
		$othersAtt = " ".evalStrContainingFct($othersAtt);
	}	
	$buttonNextToField = buttonNextToField($buttonNextToField, $id);

	echo  $title;
	echo $buttonNextToField[0]; //addRow; ?>
	<div id="block1<?=$id?>" class="<?= $inputOrForm?> <?= has_error($name) ?>">
		<?= $icon ?><?= $label ?>		
		<textarea name="<?=$name?>" id="<?=$id?>" <?=$class.$othersAtt?><?= has_error($name, true) ? ' aria-invalid="true"' : ''?><?=$placeholder?><?=$describetionAtt?>><?= old($name, $defaulValue)?></textarea>
		<?=$describetion?>
		<?= getError($name, "", "", $id) ?>
	</div> <?php
	echo $buttonNextToField[1]; //addButton_RowEnd
	echo $buttonNextToField[2]; //$block2;
}

function buttonNextToField($buttonNextToField, $id){

	$addRow = $addButton_RowEnd = $block2 = "";
	
	if(notEmpty($buttonNextToField)){
		$addRow = '<div class="row"><div class="col-md-10 col-10">';
		$addButton_RowEnd = '</div><div class="col-md-2 col-2 pl-0 pt-2">';
		if(is_array($buttonNextToField)){// for typeahead
			$addButton_RowEnd .= a(buttonMini(iconFa("plus"), "secondary"), $buttonNextToField[0], "", get($buttonNextToField, 1), 'onclick="addToCreate(this, \''.$id.'\')"');
		}else{
			$evalTmp = evalStrContainingFct($buttonNextToField);
			$evalTmp = str_replace("blockId", $id, $evalTmp);
			$addButton_RowEnd .= $evalTmp;
		}
		$addButton_RowEnd .= "</div></div>";
		
		$block2 = '<div id="block2'.$id.'" class="block2"></div>';
	}
	return [$addRow, $addButton_RowEnd, $block2];
}
function showLabel($label, $fieldInfos){
	// sd($fieldInfos);
	return $label;
} 
function showValue($value, $fieldInfos){
	$type = get($fieldInfos, "htlm_type");
	if($type == "date")
		$value = dateFormat($value);
	return $value;
} 
?>