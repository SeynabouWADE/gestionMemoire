<?php
function select($label, $name, $id="", $options, $otherClass = "", $othersAtt="", $floating=true, $icon="", $title=""){ 
		
	if($floating)	$floating="label-floating";
	if($id == "") $id = $name;
	$inputOrForm = "form-group";
	if($icon){
		$inputOrForm = "input-group";
		$icon = '<span class="input-group-addon">'.$icon.'</span>';
	}
	if($title)
		echo'<div style="margin:8px 0 -15px 0" class="'.$name.'TitleForm" id="'.$name.'TitleForm">'.$title.'ml</div>';	?>
	<div class="<?= $inputOrForm." ".$floating?> <?= has_error($name)?>" > <?= $icon ?>
		<label for="<?= $id ?>" <?= 'class="control-label '.$name.'TitleForm" id="'.$name.'TitleForm"'?>>
			<?= $label ?>
		</label>
		<select name="<?= $name ?>" id="<?= $id ?>" class="form-control<?= has_error($name, true) ? ' error' : ''?><?= $otherClass ? ' '.$otherClass : ''?>" <?= $othersAtt?><?= has_error($name, true) ? ' aria-invalid="true"' : ''?>>
			<?= $options ?>
		</select>
		<?= getError($name) ?>
	</div>  <?php
	formLabel($name, $label);
}
//TODO le checked par défaut il faut le mettre dans $othersAtt suivant des conditions...
function input($type, $label, $name, $defaulValue="", $id="", $placeholder="", $describetion="", $otherClass="", $othersAtt="", $labelClassFloatingOr=true, $icon="", $title="", $radioCheckDefaulf=""){ //TODO voir est ce que title est utile. Peut etre au moins ne pas l'afficher si c'est vide
	if($title)
		$title = '<div style="margin:8px 0 -15px 0" class="'.$name.'TitleForm" id="'.$name.'TitleForm">'.$title.'</div>';
	$placeholder = $placeholder ? ' placeholder="'.$placeholder.'"'  : '';
	$otherClass = $otherClass ? ' '.$otherClass  : '';
	if(is_bool($labelClassFloatingOr))
		$labelClassFloatingOr = $labelClassFloatingOr ? "bmd-label-floating " : "";	//"label-floating " : "";	
	$nameLabel = $name."LabelForm";
	$type = trim(strtolower($type));
	$inputOrForm = "form-group ";
	formLabel($name, $label); //session
	if($icon){
		$inputOrForm = "input-group ";
		$icon = '<span class="input-group-addon">'.$icon.'</span>';
	}
	
	if($type == "checkbox" or $type == "radio"){
		$checked = "";
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
		if($type != "radio" and ! $id)
			$id = $name;	
		
		if($id){
			$describeId = $id;
			$id = " id='$id'";
		}else
			$describeId = "descpam".str_replace(" ", "", $defaulValue);
		$describetionAtt = $describetion ? ' aria-describedby="'.$describeId.'"'  : '';
		$describetion = $describetion ? '<br><small id="'.$describeId.'" class="form-text text-muted">'.$describetion.'</small>' : '';
		echo $title;	?>
		<div class="<?= $type ?> <?= has_error($name) ?>">
			<?= $icon ?>
			<label <?= "class='$nameLabel' id='$nameLabel'"?>>
			  <input <?php
				echo "type='$type' name='$name' value='".$defaulValue."'".$id.$checked." ".$othersAtt;
				echo has_error($name, true) ? ' class="error'.$otherClass.'" aria-invalid="true"' : ' class="'.trim($otherClass).'"' 
			  ?><?=$describetionAtt?>/>
				<?= $label ?>
			</label><?=$describetion?><br>
			<?= getError($name) ?>
		</div> <?php
		return;
	}
	if($type == "submit"){
		submit($name, $defaulValue, "btn btn-fill btn-wd", $othersAtt);
		return;
	}
	
	if( ! $id)
		$id = $name;

	$describeId = "descpam".$id;
	$describetionAtt = $describetion ? ' aria-describedby="'.$describeId.'"'  : '';
	$describetion = $describetion ? '<small id="'.$describeId.'" class="form-text text-muted">'.$describetion.'</small>' : '';
	
	echo $title;	?>
	<div class="<?= $inputOrForm.$labelClassFloatingOr.has_error($name) ?>">
		<?= $icon ?>
		<label for="<?= $id ?>" class="control-label <?= $nameLabel?>" id="<?= $nameLabel?>"><?= $label ?></label>
		<input type="<?= $type ?>" name="<?= $name ?>" id="<?= $id ?>" value="<?= old($name, $defaulValue)?>" class="form-control<?php echo has_error($name, true) ? ' error' : ""; echo $otherClass;?>"<?= $othersAtt ?><?= has_error($name, true) ? ' aria-invalid="true"' : ''?><?=$placeholder?><?=$describetionAtt?>/><?=$describetion?>
		<?= getError($name, "","", "#f44336", "13px") ?>
	</div> <?php
}
function submit($name, $value="", $class = "btn btn-fill btn-wd", $othersAtt=""){
	//btn btn-next btn-fill btn-success btn-wd
	if(csrfControl)
		echo csrf();
	if(honeyPotControl)
		echo honeyPot(); ?>

	<input type="submit"  name="<?= $name ?>" value="<?= $value ?>" class="<?= $class ?>" <?= $othersAtt ?>/>
	<?php
}
//TODO penser à mettre texterea dynamique (Grafikart)
function textarea($label, $name, $id="", $defaulValue="", $otherClass="", $othersAtt="", $floating=true, $icon="", $title=""){
	if($title)
		$title = '<div style="margin:8px 0 -15px 0" class="'.$name.'TitleForm" id="'.$name.'TitleForm">'.$title.'</div>';
	if($floating){
		$floating = " label-floating";
		$label = '<label class="control-label '.$name.'LabelForm" id="'.$name.'LabelForm">'.$label.'</label>';
	}else{
		$floating = "";
		$label = '<label class="'.$name.'LabelForm" id="'.$name.'LabelForm">'.$label.'</label>';
	}

	if($id == "") $id = $name;
	$inputOrForm = "form-group";
	if($icon){ //TODO tester les formulaire avec les icons
		$inputOrForm = "input-group";
		$icon = '<span class="input-group-addon">'.$icon.'</span>';
	}
	
	$class = 'class="form-control'.(has_error($name, true) ? ' error' : '').($otherClass ? ' '.$otherClass : '').'"';
	$othersAtt = $othersAtt ? " $othersAtt" : "";
	?>
	<?= $title?>
	<div class="<?= $inputOrForm.$floating ?><?= has_error($name) ?>">
		<?= $icon ?>
		<?= $label ?>		
		<textarea name="<?= $name ?>" id="<?= $id ?>" <?=$class.$othersAtt?><?= has_error($name, true) ? ' aria-invalid="true"' : ''?>><?= old($name, $defaulValue)?></textarea>
		<?= getError($name) ?>
	</div> <?php
	formLabel($name, $label);
}
?>