<?
	include_once('../inc/_root_return.php');
?>
<div id="left_box">
	<h2>지사관리</h2>
	<ul id="s_gnb">
		<li><a style="cursor:default;">지사관리</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../branch/branch_reg.php?mode=<?=_COM_;?>&menu=company';">본사관리</a></li>
				<li><a href="#" onclick="location.href='../branch/inchange_reg.php?mode=<?=_COM_;?>&menu=company';">담당자등록</a></li>
				<li><a href="#" onclick="location.href='../branch/inchange_list.php?mode=<?=_COM_;?>&menu=company';">담당자조회</a></li>
			</ul>
		</li>
	</ul>
</div>