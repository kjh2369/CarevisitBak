<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $ed->de($_POST['jumin']);

	$conn->begin();

	//수급자정보
	$sql = 'delete
			  from m03sugupja
			 where m03_ccode = \''.$code.'\'
			   and m03_jumin = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '1';
		 exit;
	}

	//서비스계약내역
	$sql = 'delete
			  from client_his_svc
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '2';
		 exit;
	}

	//고객등급내역
	$sql = 'delete
			  from client_his_lvl
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '3';
		 exit;
	}

	//수급자구분내역
	$sql = 'delete
			  from client_his_kind
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '4';
		 exit;
	}

	//청구한도내역
	$sql = 'delete
			  from client_his_limit
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '5';
		 exit;
	}

	//가사간병
	$sql = 'delete
			  from client_his_nurse
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '6';
		 exit;
	}

	//노인돌봄
	$sql = 'delete
			  from client_his_old
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '7';
		 exit;
	}

	//산모신생아
	$sql = 'delete
			  from client_his_baby
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '8';
		 exit;
	}

	//장애인활동지원
	$sql = 'delete
			  from client_his_dis
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '9';
		 exit;
	}

	//기타유료
	$sql = 'delete
			  from client_his_other
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '10';
		 exit;
	}

	//추천인
	$sql = 'delete
			  from client_recom
			 where org_no   = \''.$code.'\'
			   and cr_jumin = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '11';
		 exit;
	}

	//산모추가급여
	$sql = 'delete
			  from client_svc_addpay
			 where org_no  = \''.$code.'\'
			   and svc_ssn = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '12';
		 exit;
	}

	//패턴
	$sql = 'delete
			  from pattern
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '13';
		 exit;
	}

	//가족요양보호사
	$sql = 'delete
			  from client_family
			 where org_no   = \''.$code.'\'
			   and cf_jumin = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '14';
		 exit;
	}

	//옵션
	$sql = 'delete
			  from client_option
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '15';
		 exit;
	}

	$conn->commit();

	echo 'Y';

	include_once('../inc/_db_close.php');
?>