<?
	include_once('../inc/_root_return.php');
	$menu = $_GET['menu'];
	$typeSR = $_SESSION['userTypeSR'];
?>
<div id="left_box">
	<h2>일정관리</h2>
	<ul id="sidnav">
		<li><a>서비스묶음관리</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=SVC_CATEGORY&menu=<?=$menu;?>'; return false;">카테고리관리</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=SVC_GROUP_LIST&menu=<?=$menu;?>'; return false;">묶음조회</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=SVC_GROUP_REG&menu=<?=$menu;?>'; return false;">묶음등록</a></li>
			</ul>
		</li>
		<li class="top_line"><a>일정관리</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=ILJUNG_SVC_GROUP&menu=<?=$menu;?>'; return false;">일정관리(서비스묶음)</a></li>
				<li><a href="#" onClick="location.href='../iljung/iljung_list.php?sr=<?=$typeSR;?>&mode=6&menu=<?=$menu;?>'; return false;">일정관리(대상자별)</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=91&menu=<?=$menu;?>'; return false;">일정관리(서비스별)</a></li>
				<li><a href="#" onClick="location.href='../iljung/iljung_print_new.php?sr=<?=$typeSR;?>&mode=105&menu=<?=$menu;?>'; return false;">일정표출력(대상자별)</a></li>
				<li><a href="#" onClick="location.href='../iljung/iljung_memo_list.php?menu=<?=$menu;?>'; return false;">메모관리</a></li>
			</ul>
		</li>
	</ul>
</div>