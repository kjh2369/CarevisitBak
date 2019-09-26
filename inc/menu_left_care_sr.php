<?
	include_once('../inc/_root_return.php');

	$typeSR = $_GET['sr'];

	if ($typeSR == 'S'){
		$menuTitle = '재가지원';
	}else if ($typeSR == 'R'){
		$menuTitle = '자원연계';
	}
?>
<div id="left_box">
	<h2><?=$menuTitle;?></h2>
	<ul id="s_gnb">
		<li class="top_line">
			<a style="cursor:default;"><?=$menuTitle;?></a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../acct/acct.php?sr=<?=$typeSR;?>&type=71&menu=care_sr'; return false;">서비스관리</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=2&menu=care_sr'; return false;">서비스단위관리</a></li>
			</ul>
		</li>
	</ul>
</div>