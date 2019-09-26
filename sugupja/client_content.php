<?
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');

	foreach($laSvcList as $row){
		if ($laUseSvc[$row['code']]['cd'] == $row['code']){
			$lsDisplay = '';
			$lsPadding = '10px';
		}else{
			$lsDisplay = 'none';
			$lsPadding = '';
		}?>
		<div id="loSvc_<?=$row['code'];?>" value="<?=$row['code'].'_'.$row['id'];?>" style="position:; float:left; top:0; left:0; width:427px; background-color:#ffffff; padding:<?=$lsPadding;?>; display:<?=$lsDisplay;?>;"><?
			$__CURRENT_SVC_ID__ = $row['id'];
			$__CURRENT_SVC_CD__ = $row['code'];
			$__CURRENT_SVC_NM__ = $row['name'];
			$lbPop = false;
			include('./client_reg_sub.php');?>
		</div><?
	}?>
	<div style="clear:both;"></div>