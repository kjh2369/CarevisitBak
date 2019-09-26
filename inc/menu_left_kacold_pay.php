<?
	include_once('../inc/_root_return.php');
	$menu = $_GET['menu'];
	$typeSR = $_SESSION['userTypeSR'];
?>
<div id="left_box">
	<h2>사용요금안내</h2>
	<ul id="sidnav">
		<li><a>사용요금안내</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../claim/claim_list.php?sr=<?=$typeSR;?>&menu=<?=$menu;?>'; return false;">사용요금안내</a></li>
				<li><a href="#" onClick="location.href='../claim/pay_in.php?sr=<?=$typeSR;?>&menu=<?=$menu;?>'; return false;">사용료 납부내역</a></li>
			</ul>
		</li>
		<li class="top_line"><a>다운로드</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../_center/carevisit_contract.php?sr=<?=$typeSR;?>&type=1&menu=<?=$menu;?>'; return false;">신규계약서</a></li>
				<li><a href="#" onClick="location.href='../_center/kacold_contract.php?sr=<?=$typeSR;?>&menu=<?=$menu;?>'; return false;">신규계약서(재가지원)</a></li>
				<li><a href="#" onClick="location.href='../_center/download.php?sr=<?=$typeSR;?>&downType=2&menu=<?=$menu;?>'; return false;">자동이체동의서(PDF)</a></li>
				<li><a href="#" onClick="location.href='../_center/download.php?sr=<?=$typeSR;?>&downType=3&menu=<?=$menu;?>'; return false;">사업자등록증(케어비짓)</a></li>
				<li><a href="#" onClick="location.href='../_center/download.php?sr=<?=$typeSR;?>&downType=4&menu=<?=$menu;?>'; return false;">통장사본(케어비짓)</a></li>
			</ul>
		</li>
		<li class="top_line"><a>업로드</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../claim/center_doc.php?sr=<?=$typeSR;?>&type=1&menu=<?=$menu;?>'; return false;">계약서/등록증 등록</a></li>
			</ul>
		</li>
	</ul>
</div>