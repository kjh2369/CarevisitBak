<?
	include_once('../inc/_root_return.php');
	$menu = $_GET['menu'];
	$typeSR = $_SESSION['userTypeSR'];
?>
<div id="left_box">
	<h2>대상자관리</h2>
	<!--
	<ul id="sidnav">
		<li><a style="cursor:default;">일반접수자관리</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=83&menu=<?=$menu;?>'; return false;">일반접수조회</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=84&menu=<?=$menu;?>'; return false;">일반접수등록</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=INTERVIEW_LIST_N&menu=<?=$menu;?>'; return false;">초기상담기록지</a></li>
			</ul>
		</li>
	</ul>
	-->
	<ul id="sidnav">
		<li class="top_line"><a>대상자관리</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=81&menu=<?=$menu;?>'; return false;">대상자조회</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=82&menu=<?=$menu;?>'; return false;">대상자등록</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=INTERVIEW_LIST&menu=<?=$menu;?>'; return false;">초기상담기록지</a></li>
			</ul>
		</li><?
		//지시서요청 완료 조회
		$sql= 'SELECT count(*)
				 FROM medical_request
				WHERE org_no = \''.$_SESSION['userCenterCode'].'\'
				  AND complete_yn = \'Y\'
				  AND cancel_yn = \'N\'
				  AND del_flag = \'N\'';
		$mcRqCnt = $conn -> get_data($sql);
		if ($mcRqCnt > 0){?>
			<li class="top_line"><a>방문간호</a>
				<ul id="sub_menu">
					<li><a href="#" onClick="location.href='../doctor_nurse/visit_nurse_order.php?sr=<?=$typeSR;?>&menu=<?=$menu;?>'; return false;">방문간호지시서</a></li>
					<li><a href="#" onClick="location.href='../doctor_nurse/req_iljung.php?sr=<?=$typeSR;?>&menu=<?=$menu;?>'; return false;">방문간호 변경요청</a></li>
					<li><a href="#" onClick="location.href='../doctor_nurse/comu_list.php?sr=<?=$typeSR;?>&menu=<?=$menu;?>'; return false;">방문간호 커뮤니티</a></li>
				</ul>
			</li><?
		}?>
	</ul>
</div>