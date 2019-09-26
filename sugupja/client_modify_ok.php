<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = '';
	$change_jumin = '';
	$conn->begin();
	
	//수급자정보
	$sql = 'update m03sugupja
			   set m03_jumin = \''.$change_jumin.'\'
			 where m03_ccode = \''.$code.'\'
			   and m03_jumin = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '1';
		 exit;
	}

	//서비스계약내역
	$sql = 'update client_his_svc
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '2';
		 exit;
	}

	//고객등급내역
	$sql = 'update client_his_lvl
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '3';
		 exit;
	}

	//수급자구분내역
	$sql = 'update client_his_kind
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '4';
		 exit;
	}

	//청구한도내역
	$sql = 'update client_his_limit
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '5';
		 exit;
	}

	//가사간병
	$sql = 'update client_his_nurse
		       set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '6';
		 exit;
	}

	//노인돌봄
	$sql = 'update client_his_old
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '7';
		 exit;
	}

	//산모신생아
	$sql = 'update client_his_baby
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '8';
		 exit;
	}

	//장애인활동지원
	$sql = 'update client_his_dis
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '9';
		 exit;
	}

	//기타유료
	$sql = 'update client_his_other
	           set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '10';
		 exit;
	}

	//추천인
	$sql = 'update client_recom
			   set cr_jumin    = \''.$change_jumin.'\'
			 where org_no   = \''.$code.'\'
			   and cr_jumin = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '11';
		 exit;
	}

	//산모추가급여
	$sql = 'update client_svc_addpay
			   set jumin  = \''.$change_jumin.'\'
			 where org_no  = \''.$code.'\'
			   and svc_ssn = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '12';
		 exit;
	}

	//패턴
	$sql = 'update pattern
	           set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '13';
		 exit;
	}

	//가족요양보호사
	$sql = 'update client_family
			   set jumin  = \''.$change_jumin.'\'
			 where org_no   = \''.$code.'\'
			   and cf_jumin = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '14';
		 exit;
	}

	//옵션
	$sql = 'update client_option
	           set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '15';
		 exit;
	}
	
	//일정
	$sql = 'update t01iljung
			   set t01_jumin = \''.$change_jumin.'\'
			 where t01_ccode = \''.$code.'\'
			   and t01_jumin = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '1';
		 exit;
	}

	$conn->commit();

	echo 'Y';

	include_once('../inc/_db_close.php');
?>