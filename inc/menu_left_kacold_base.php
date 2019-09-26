<?
	include_once('../inc/_root_return.php');
	$menu = $_GET['menu'];
	$typeSR = $_SESSION['userTypeSR'];
?>
<div id="left_box">
	<h2>기초자료</h2>
	<ul id="sidnav">
		<li><a>기초자료</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=1&menu=<?=$menu;?>'; return false;">서비스관리</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=71&menu=<?=$menu;?>'; return false;">자원관리</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=11&menu=<?=$menu;?>'; return false;">자원연결</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=21&menu=<?=$menu;?>'; return false;">사업계획</a></li>
			</ul>
		</li>
	</ul>
</div>