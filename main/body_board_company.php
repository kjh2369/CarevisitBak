<?
	include_once('../inc/_myFun.php');

	if ($gDomain == 'kacold.net'){
		$imgpath = $gHostImgPath.'/board/caption_7.gif';
		if (!is_file($imgpath)) $imgpath = '../image/caption_7.gif';
	}else if ($gDomain == 'vaerp.com'){
		$imgpath = '../image/caption_7_vaerp_com.gif';
	}else{
		$imgpath = '../image/caption_7.gif';
	}
?>
<div class="tmp_margin_1">
	<div style="width:auto; float:left; padding-left:5px;"><img src="<?=$imgpath;?>"></div>
	<div style="width:auto; float:right; padding-right:5px;"><img src="../image/more.gif" style="cursor:pointer;" onclick="__go_menu('other','../goodeos/notice_list.php');"></div>
</div>
<div class="tmp_margin_3">
	<div style="width:auto; float:left; padding-top:5px;"><img src="../image/board_1.jpg"></div>
	<div style="width:auto; float:left;"><?echo board_list($conn, 'company', $gDomainID);?></div>
</div>