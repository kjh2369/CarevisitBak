<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	//기관
	$orgNo = $_SESSION['userCenterCode'];

	//TODAY
	$today = Date('Ymd');

	//대상자 조회
	$sql = 'SELECT	DISTINCT
					plan.jumin
			,		plan.sub_cd
			,		lvl.app_no
			FROM	plan_change_request AS plan
			LEFT	JOIN client_his_lvl AS lvl
					ON   lvl.org_no = plan.org_no
					AND  lvl.jumin = plan.jumin
					AND  lvl.from_dt <= plan.date
					AND  lvl.to_dt >= plan.date
			WHERE	plan.org_no		= \''.$orgNo.'\'
			AND		plan.svc_cd		= \'0\'
			AND		plan.date		= \''.$today.'\'
			AND		plan.result_yn	= \'Y\'
			AND		plan.send_yn	= \'Y\'
			AND		plan.complete_yn= \'N\'
			AND		plan.del_flag	= \'N\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	$conn->row_free();

	//공단일정조회

	include_once('../inc/_footer.php');
?>