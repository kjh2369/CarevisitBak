<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_hce.php');

	$type = $_GET['type'];
	$sr   = $_GET['sr'];
	$IPIN = $hce->IPIN;
?>
<style type="text/css">
	body{margin:0; padding:0; border:0; line-height:1.5em; padding:10px;}
	img { vertical-align:top; border:none;}

	/* left_menu */
	.N_snb{width:180px; margin:0; padding:0; border:1px solid #d4d4d4;}
	.N_snb ul.S_navi{width:180px; margin:0; padding:0;  list-style:none; overflow:hidden;}
	.N_snb ul.S_navi li{width:180px; height:30px; border-bottom:1px solid #d4d4d4; font-family: '굴림',Gulim,Dotum,AppleGothic,Sans-serif;  font-size:10pt; color:#4a4a4a; }
	.N_snb ul.S_navi li a:link, .N_snb ul.S_navi li a:visited {width:180px; height:30px; line-height:30px; display:block; text-decoration:none; color:#4a4a4a; padding-left:18px;}
	.N_snb ul.S_navi li a:hover, .N_snb ul.S_navi li a:focus, .N_snb ul.S_navi li a:active {color:#fff; font-weight:bold; background-color:#0e69b0;}
	.N_snb ul.S_navi li a.click, .N_snb ul.S_navi li a.click:visited{ color:#fff; font-weight:bold; background-color:#0e69b0;}
	div.S_bg{position:relative; width:180px;height:220px; background: url(../image/bg_case_left.jpg) left bottom no-repeat;}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		try{
			var top = $('#divMenuList').offset().top;
			var height = $(this).height();

			var h = height - top - 10 - $('.S_bg').height();

			$('#divMenuList').height(h);
		}catch(e){
			alert(e);
		}

		lfShowMenu('<?=$IPIN;?>');
	});

	function lfSelMenu(obj){
		var parent = $('.S_navi');

		$('a',parent).removeClass();
		$(obj).addClass('click');
	}

	function lfShowMenu(IPIN){
		if (IPIN){
			$('.menu').show();
			$('#divMenuList').css('border-bottom','1px solid #CCCCCC');
		}else{
			//$('.menu').hide();
		}
	}

	function lfHideMenu(){
		$('.menu').hide();
	}
</script>
<div class="N_snb">
	<div id="divMenuList" style="height:100px; overflow-x:hidden; overflow-y:auto;">
		<ul class="S_navi">
			<li><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=1" target="frmBody" onclick="lfSelMenu(this);" class="click">사례접수일지</a></li>
			<li><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=11" target="frmBody" onclick="lfSelMenu(this);" class="">사례접수등록</a></li>
			<li class="menu" style="display:none;"><a id="btnLeftItvw" href="../hce/hce_body.php?sr=<?=$sr;?>&type=21" target="frmBody" onclick="lfSelMenu(this);" class="">초기면접기록지</a></li>
			<li class="menu" style="display:none;"><a id="btnLeftIspt" href="../hce/hce_body.php?sr=<?=$sr;?>&type=31" target="frmBody" onclick="lfSelMenu(this);" class="">사정기록지</a></li>
			<li class="menu" style="display:none;"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=41" target="frmBody" onclick="lfSelMenu(this);" class="">선정기준표</a></li>
			<li class="menu" style="display:none;"><a id="btnLeftMeet" href="../hce/hce_body.php?sr=<?=$sr;?>&type=51" target="frmBody" onclick="lfSelMenu(this);" class="">사례회의록</a></li>
			<li class="menu" style="display:none;"><a id="btnLeftPlan" href="../hce/hce_body.php?sr=<?=$sr;?>&type=61" target="frmBody" onclick="lfSelMenu(this);" class="">서비스계획서</a></li>
			<li class="menu" style="display:none;"><a id="btnLeftCont" href="../hce/hce_body.php?sr=<?=$sr;?>&type=71" target="frmBody" onclick="lfSelMenu(this);" class="">이용 안내 및 동의서</a></li>
			<li class="menu" style="display:none;"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=81" target="frmBody" onclick="lfSelMenu(this);" class="">과정상담</a></li>
			<li class="menu" style="display:none;"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=91" target="frmBody" onclick="lfSelMenu(this);" class="">연계 및 의뢰서</a></li>
			<li class="menu" style="display:none;"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=101" target="frmBody" onclick="lfSelMenu(this);" class="">모니터링 기록지</a></li>
			<li class="menu" style="display:none;"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=111" target="frmBody" onclick="lfSelMenu(this);" class="">재사정기록지</a></li>
			<li class="menu" style="display:none;"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=121" target="frmBody" onclick="lfSelMenu(this);" class="">서비스 종결 안내서</a></li><?
			if ($sr == 'S'){?>
				<li class="menu" style="display:none;"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=141" target="frmBody" onclick="lfSelMenu(this);" class="">제공평가서</a></li><?
			}?>
			<li class="menu" style="display:none; border-bottom:none;"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=131" target="frmBody" onclick="lfSelMenu(this);" class="">사례평가서</a></li>
		</ul>
	</div>
	<div class="S_bg"></div>
</div>
<?
	include_once('../inc/_footer.php');
?>