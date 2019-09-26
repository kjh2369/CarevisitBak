<?
	include_once('../inc/_root_return.php');
	$menu = $_GET['menu'];
?>
<div id="left_box" style="">
	<h2>기관관리</h2>
	<ul id="sidnav">
		<li><a>기관관리</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../center/center_reg.php?menu=<?=$menu;?>'; return false;">기관조회</a></li>
				<li><a href="#" onClick="location.href='../dept/dept_list.php?menu=<?=$menu;?>'; return false;">부서관리</a></li>
				<li><a href="#" onClick="location.href='../job/job_list.php?menu=<?=$menu;?>'; return false;">직무관리</a></li>
				<li><a href="#" onClick="location.href='../pos/pos.php?menu=<?=$menu;?>'; return false;">직위관리</a></li>
				<!--li><a href="#" onClick="location.href='../center/holiday_list.php?menu=<?=$menu;?>'; return false;">기관약정휴일</a></li-->
				<li><a href="#" onClick="changePassword(); return false;">비밀번호관리</a></li>
			</ul>
		</li>
		<li class="top_line"><a>직원관리</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../counsel/mem_counsel.php?menu=<?=$menu;?>'; return false;">초기상담기록지</a></li>
				<li><a href="#" onClick="location.href='../yoyangsa/counsel_member.php?menu=<?=$menu;?>'; return false;">과정상담리스트</a></li>
				<li><a href="#" onClick="location.href='../yoyangsa/mem_reg.php?menu=<?=$menu;?>'; return false;">직원등록</a></li>
				<li><a href="#" onClick="location.href='../yoyangsa/mem_list.php?menu=<?=$menu;?>'; return false;">직원조회</a></li>
				<li><a href="#" onClick="location.href='../yoyangsa/mem_app.php?menu=<?=$menu;?>'; return false;">직원평가관리</a></li>
				<li><a href="#" onClick="location.href='../yoyangsa/mem_4insu.php?menu=<?=$menu;?>';return false;">4대보험가입내역(현재)</a></li>
			</ul>
		</li>
		<!--li class="top_line"><a>커뮤니티</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../goodeos/board_list.php?menu=<?=$menu;?>&board_type=1'; return false;">케어비지트</a></li>
			</ul>
		</li-->
	</ul>
</div>