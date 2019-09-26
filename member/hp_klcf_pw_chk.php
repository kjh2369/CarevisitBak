<?

include_once("../inc/_db_open.php");
include_once("../inc/_ed.php");

/***************************************

정보나눔회 회원가입 시 비밀번호 중복체크

*****************************************/


# 아이디를 소문자로 치환한다.
	$ucode = strtolower($ed->de($_GET['ucode']));
	$upass = strtolower($ed->de($_GET['upass']));
	
	$ucode = str_replace('@', '', $ucode);
	$upass = str_replace('@', '', $upass);

	#직원 아이디조회

	$sql = 'select *
			  from m97user
			 where m97_user  = \''.$ucode.'\'
			   and m97_pass = \''.$upass.'\'';
	$work = $conn->get_array($sql);

	$userID    = $work['m97_id'];
	
	echo $userID;

include_once("../inc/_db_close.php");

?>