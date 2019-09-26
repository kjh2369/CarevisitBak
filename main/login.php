<?
	include_once('../inc/_myFun.php');

	$imgpath = $gHostImgPath.'/login/ci.gif';

	if ($gDomain == 'kacold.net'){
		$imgpath = $gHostImgPath.'/top/ci.png';
		if (!is_file($imgpath)) $imgpath = $gHostImgPath.'/top/ci.png';
	}

?>
<link href="../css/login.css" rel="stylesheet" type="text/css">
	<style type="text/css">
		body {
		margin-left:10px;
		text-align: center;
		min-width: 1024px;
	}

	#login_box {
		width: 770px;
		margin: 0 auto;
		text-align: left;
	}
</style>
<div id="login_box">
	<!--div id="lg_ci"><img src="<?=$imgpath;?>"></div-->
	<!--div id="lg_cs" style="width:372px;"><?
		if ($gDomain == 'kacold.net'){?>
			<div style="width:auto; float:right;"><img src='../image/btn_remote.png' alt='원격지원' style='cursor:pointer;' onclick="window.open('http://939.co.kr/goodeos/','HELP_WIN','left=0,top=0,width=830,height=580,toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');"></div><?
		}else{?>
			<div style="width:auto; float:left;"><img src='../image/btn_remote.png' alt='원격지원' style='cursor:pointer;' onclick="window.open('http://939.co.kr/goodeos/','HELP_WIN','left=0,top=0,width=830,height=580,toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');"></div>
			<div style="width:auto; float:left; margin-left:5px;"><img src="<?=$gHostImgPath;?>/login/btn_counsel.gif" onclick="counsel();"/></div><?
		}?>
	</div-->

	<form name="login" method="post" style="margin-top:200px;">
	<div id="lg_title"><img src="<?=$gHostImgPath;?>/login/bg_title.gif"></div>
	<div id="lg_box1">
		<div id="lg_box2">
			<div id="lg_login">
				<ul>
					<li><label><img src="../img/login/txt_id.gif"><input type="text"	name="uCode" onkeydown="if(event.keyCode == 13 && this.value != ''){document.getElementById('uPass').focus();}" style="ime-mode:inactive;"></input></label></li>
					<li><label><img src="../img/login/txt_pw.gif"><input type="password"name="uPass" onkeydown="if(event.keyCode == 13 && this.value != ''){_Login();}"></input></label></li>
				</ul>
				<div class="lg_btn"><a href="#" onclick="_Login();"><img src="../img/login/btn_login.gif"></a></div>

			</div>
			<div id="lg_notice">
				<div class="nt_title">
					<h2><img src="<?=$gHostImgPath;?>/login/notice_title.gif"></h2>
					<!--a class="lg_more" href="#" onclick="alert('준비중입니다.');"><img src="../img/more.gif"></a-->
				</div>
				<div class="nt_txt">
					<img src="../img/login/notice_img.gif">
					<ul>
						<li>등록된 공지사항이 없습니다.</li>
					</ul>
				</div>
			</div>
		<div clear="clear"></div>
		</div>
		<div id="lg_box3">
			<img src="<?=$gHostImgPath;?>/login/banner.jpg">
		</div>
				<!-- copy -->
		<div id="lg_box4">
			<div class="lg_down"><img src="<?=$gHostImgPath;?>/login/copy_txt_down.gif"><a href="../img/login/IE8-WindowsXP-x86-KOR.exe"></a></div><?
			if ($gDomain == 'carevisit.net'){?>
				<!--
				<div class="lg_copy"><img src="<?=$gHostImgPath;?>/login/copy.gif"></div>
				--><?
			}?>
		</div>
	</div>
	</form>
	<div clear="clear"></div>
	<!--<div style="text-align:center;"><a href="#" onclick="_mem_login(document.login);">로그인</a></div>-->
</div>
