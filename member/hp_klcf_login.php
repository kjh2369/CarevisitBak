<?

include_once("../inc/_db_open.php");
include_once("../inc/_ed.php");

# 아이디를 소문자로 치환한다.
	$user_id = strtolower($ed->de($_GET['user_id']));
	$user_pw = $ed->de($_GET['user_pw']);
	
	$sql = 'select *
			  from m97user
			 where m97_user = \''.$user_id.'\'
			   and m97_pass = \''.$user_pw.'\'';
	$work = $conn->get_array($sql);

	$userID    = $work['m97_id'];
	$userCode  = $work['m97_user'];
	$userPass  = $work['m97_pass'];
	
	$sql = 'select count(*)
			  from m00center
			 where m00_mcode = \''.$userID.'\'
			   and m00_domain = \'klcf.kr\'
			   and m00_del_yn = \'N\'';
	$center_umu = $conn->get_data($sql);	


	#업무관리시스템 접속 및 2012평가준비자료 테스트기관 접속
	
	echo $ed->en($userID).'/'.$ed->en($userPass).'/'.$center_umu.'/'.$ed->en('klcf1').'/'.$ed->en('1111');

include_once("../inc/_db_close.php");

?>