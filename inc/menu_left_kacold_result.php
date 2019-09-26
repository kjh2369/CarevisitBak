<?
	include_once('../inc/_root_return.php');
	$menu = $_GET['menu'];
	$typeSR = $_SESSION['userTypeSR'];
?>
<div id="left_box">
	<h2>실적관리</h2>
	<ul id="sidnav">
		<li><a>실적관리</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=RESULT_REG&menu=<?=$menu;?>'; return false;">실적조회</a></li><?
				if ($typeSR == 'R'){?>
					<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=41&menu=<?=$menu;?>'; return false;">실적관리</a></li><?
				}?>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=43&menu=<?=$menu;?>'; return false;">실적마감</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=SVC_USE_STAT&menu=<?=$menu;?>'; return false;">서비스이용현황</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=SVC_USE_YEAR&menu=<?=$menu;?>'; return false;">서비스이용현황(월별)</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=SVC_OPERATE_STAT&menu=<?=$menu;?>'; return false;">서비스운영현황(일자별)</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=USER_SVC_STAT&menu=<?=$menu;?>'; return false;">이용자별 서비스현황</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=CLIENT_FIND&menu=<?=$menu;?>'; return false;">대상자조회</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=TGT_CONF&menu=<?=$menu;?>'; return false;">대상자별실적</a></li>
			</ul>
		</li>
		<li><a style="cursor:default;" class="top_line">기록지관리</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=WORK_LOG_ITEM&menu=<?=$menu;?>'; return false;">업무일지 항목관리</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=WORK_LOG&menu=<?=$menu;?>'; return false;">업무일지 조회 및 작성</a></li>
			</ul>
		</li>
	</ul>
</div>