<?
	/*
	$list = $conn->kind_list($mCode);

	for($i=0; $i<sizeof($list); $i++){
		echo "<option value='".$list[$i]['code']."' $selected>".$list[$i]['name']."</option>";
	}

	unset($list);
	*/

	for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
	?>
		<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $_REQUEST["mKind"]){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
	<?
	}
?>