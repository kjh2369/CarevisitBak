
<div class="tmp_margin_1">
	<div style="width:auto; float:left; padding-left:5px;"><img src="../image/caption_2<?=$gDomain == 'vaerp.com' ? '_vaerp_com' : '';?>.gif"></div>
	<div style="width:auto; float:right; padding-right:5px;"><img src="../image/more.gif" style="cursor:pointer;" onclick="__go_menu('other','../goodeos/board_list.php?board_type=1&<?=$_SESSION['userLevel'] == 'A' ? 'menu=goodeos' : 'menuTopId=I';?>');"></div>
</div>
<div class="tmp_margin_3">
	<div style="width:auto; float:left;"><img src="../image/board_4.jpg"></div>
	<div style="width:auto; float:left;"><?echo board_list($conn, '1', 8);?></div>
</div>