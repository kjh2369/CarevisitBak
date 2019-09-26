<?

include_once("../inc/_db_open.php");
include_once("../inc/_ed.php");

/***************************************/

#klcf 업무바로가기 로그인

/***************************************/

# 아이디, 패스워드를 소문자로 치환한다.
	$user_id = strtolower($ed->de($_GET['user_id']));
	$user_id = str_replace('@', '', $user_id);
	$user_pw = $ed->de($_GET['user_pw']);
	
	
	$sql = 'select count(*)
			  from m97user
			 where m97_user = \''.$user_id.'\'';
	$id_cnt = $conn->get_data($sql);

	if($id_cnt > 0){
		$sql = 'select *
				  from m97user
				 where m97_user = \''.$user_id.'\'
				   and m97_pass = \''.$user_pw.'\'';
		$work = $conn->get_array($sql);
		
		$userID    = $work['m97_id'];
		$userCode  = $work['m97_user'];
		$userPass  = $work['m97_pass'];

		if(!empty($userID )){
			
			$sql = 'select m00_domain
					  from m00center
					 where m00_mcode = \''.$userCode.'\'';
			$domain = $conn -> get_data($sql);

			echo '1/'.$ed->en($userID).'/'.$ed->en($userPass).'/'.$domain;		#업무관리시스템 접속

		}else {
			echo '2';	#비밀번호 잘못된 경우
		}
	}else {

		echo '9';	#비사용기관일 경우

	}
	

include_once("../inc/_db_close.php");

?>