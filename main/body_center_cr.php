<?
	include_once('../inc/_http_uri.php');

	$imgpath = $gHostImgPath.'/img_emplem_'.$_SESSION['userArea'].'.jpg';
	if (!is_file($imgpath)) $imgpath = $gHostImgPath.'/img_emplem.jpg';

	$cipath = $gHostImgPath.'/ciL_'.$_SESSION['userArea'].'.png';
	if (!is_file($cipath)) $cipath = $gHostImgPath.'/ciL.png';
?>
<div style="clear:both; width:1024px; height:540px; background:url(<?=$gHostImgPath.'/bg_body.jpg';?>) no-repeat;">
	<div style="float:left; width:510px;">
		<div style="clear:both; position:relative; width:390px; height:73px; left:80px; top:185px; background:url(<?=$cipath;?>) no-repeat;"></div>
	</div>
	<div style="float:left; width:510px;">
		<div style="clear:both; position:relative; width:407px; height:188px; right:-50px; top:50px; background:url(<?=$gHostImgPath.'/bg_notice.png';?>) no-repeat;">
			<div style="float:left; position:relative; width:91px; height:16px; left:30px; top:21px; background:url(<?=$gHostImgPath.'/title_notice.png';?>) no-repeat;"></div>
			<div style="float:right; position:relative; width:42px; height:10px; right:30px; top:23px; background:url(<?=$gHostImgPath.'/btn_more.png';?>) no-repeat;"></div>
			<div style="clear:both; position:relative; width:250px; left:130px; top:50px;"></div>
		</div>
		<div style="clear:both; position:relative; width:407px; height:188px; right:-50px; top:60px; background:url(<?=$gHostImgPath.'/bg_data.png';?>) no-repeat;">
			<div style="float:left; position:relative; width:91px; height:16px; left:30px; top:21px; background:url(<?=$gHostImgPath.'/title_data.png';?>) no-repeat;"></div>
			<div style="float:right; position:relative; width:42px; height:10px; right:30px; top:23px; background:url(<?=$gHostImgPath.'/btn_more.png';?>) no-repeat;"></div>
			<div style="clear:both; position:relative; width:250px; left:130px; top:50px;"></div>
		</div>
	</div>
</div>