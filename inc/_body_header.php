<?
	define(__BODY__, TRUE);

	//$gDomain
	//$gHostNm

	$IsTopMenu = false;

	include_once("../inc/_menuTopKacold.php");


?>
<div id="container">
	<div id="container_box"><?
		if($mainYn != 'Y'){ ?>
				<div id="aside">
					<div id="left"><?
					if ($_SESSION['adminFlag'] == 'Y'){
						include_once('../inc/_menu_left.php');
					}else{
						include_once('../inc/_menuLeft.php');
					}?>
					</div>
				</div>
			<div id="content" ><?
		}?>




