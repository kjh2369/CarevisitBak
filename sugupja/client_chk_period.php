<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$conn->fetch_type = 'assoc';

	$code   = $_POST['code'];
	$jumin  = $_POST['jumin'];
	$svcCd  = $_POST['svcCd'];
	$seq    = $_POST['seq'];
	$fromDt = $_POST['from'];
	$toDt   = $_POST['to'];
	$reCont = $_POST['reCont'];


	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);


	$sql = 'select from_dt
			,      to_dt
			  from client_his_svc
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			   and svc_cd = \''.$svcCd.'\'
			   and seq    < \''.$seq.'\'
			 order by seq desc
			 limit 1';

	$row = $conn->get_array($sql);

	if (is_array($row)){
		if ($row['to_dt'] > $fromDt ){
			$conn->close();
			echo '1.이전의 계약과 중복됩니다.';
			exit;
		}
	}

	if ($svcCd != 'A' && $svcCd != 'B' && $svcCd != 'C'){
		$sql = 'select svc_cd
				,      from_dt
				,      to_dt
				  from client_his_svc
				 where org_no   = \''.$code.'\'
				   and jumin    = \''.$jumin.'\'
				   and to_dt   >= \''.$fromDt.'\'
				   and to_dt   <= \''.$toDt.'\'
				   and svc_cd  != \''.$svcCd.'\'
				   and svc_cd  != \'A\'
				   and svc_cd  != \'B\'
				   and svc_cd  != \'C\'
				   and svc_cd  != \'R\'
				   and svc_cd  != \'6\'';

		if ($svcCd == '3')
			$sql .= ' and svc_cd != \'4\'';
		else if ($svcCd == '4')
			$sql .= ' and svc_cd != \'3\'';

		$sql .= ' order by seq desc
				  limit 1';

		$row = $conn->get_array($sql);

		if (is_array($row)){
			$conn->close();

			$svcNm  = $conn->_svcNm($row['svc_cd']);
			$fromDt = $myF->dateStyle($row['from_dt'],'.');
			$toDt   = $myF->dateStyle($row['to_dt'],'.');

			echo '2.'.$svcNm.'의 계약내역과 중복됩니다.';
			exit;
		}

		$sql = 'select svc_cd
				,      from_dt
				,      to_dt
				  from client_his_svc
				 where org_no   = \''.$code.'\'
				   and jumin    = \''.$jumin.'\'
				   and svc_cd  != \'A\'
				   and svc_cd  != \'B\'
				   and svc_cd  != \'C\'
				   and svc_cd  != \'R\'
				   and svc_cd  != \'6\'';

		if ($svcCd == '3')
			$sql .= ' and svc_cd != \'4\'';
		else if ($svcCd == '4')
			$sql .= ' and svc_cd != \'3\'';

		$sql .= '  and from_dt <= \''.$fromDt.'\'
				   and to_dt   >= \''.$fromDt.'\'
				   and concat(svc_cd,\'_\',seq) != \''.$svcCd.'_'.$seq.'\'
				 order by seq desc
				 limit 1';

		$row = $conn->get_array($sql);

		if (is_array($row)){
			$conn->close();

			$svcNm  = $conn->_svcNm($row['svc_cd']);
			$fromDt = $myF->dateStyle($row['from_dt'],'.');
			$toDt   = $myF->dateStyle($row['to_dt'],'.');

			echo '3.'.$svcNm.'의 계약내역과 중복됩니다.';
			exit;
		}


		$sql = 'select svc_cd
				,      from_dt
				,      to_dt
				  from client_his_svc
				 where org_no   = \''.$code.'\'
				   and jumin    = \''.$jumin.'\'
				   and svc_cd  != \'A\'
				   and svc_cd  != \'B\'
				   and svc_cd  != \'C\'
				   and svc_cd  != \'R\'
				   and svc_cd  != \'6\'';

		if ($svcCd == '3')
			$sql .= ' and svc_cd != \'4\'';
		else if ($svcCd == '4')
			$sql .= ' and svc_cd != \'3\'';

		$sql .= '  and from_dt <= \''.$toDt.'\'
				   and to_dt   >= \''.$toDt.'\'
				   and concat(svc_cd,\'_\',seq) != \''.$svcCd.'_'.$seq.'\'
				 order by seq desc
				 limit 1';

		$row = $conn->get_array($sql);

		if (is_array($row)){
			$conn->close();

			$svcNm  = $conn->_svcNm($row['svc_cd']);
			$fromDt = $myF->dateStyle($row['from_dt'],'.');
			$toDt   = $myF->dateStyle($row['to_dt'],'.');

			echo '4.'.$svcNm.'의 계약내역과 중복됩니다.';
			exit;
		}
	}

	//일자의 일정여부 확인
	if ($reCont != 'Y'){
		/*
		 * 2012.06.26 임시로 막는다.
		if (!empty($fromDt)){
			$sql = 'select count(*)
					  from t01iljung
					 where t01_ccode      = \''.$code.'\'
					   and t01_mkind      = \''.$svcCd.'\'
					   and t01_jumin      = \''.$jumin.'\'
					   and t01_sugup_date < \''.str_replace('-','',$fromDt).'\'
					   and t01_del_yn     = \'N\'';

			$liCnt = $conn->get_data($sql);

			if ($liCnt > 0){
				$conn->close();
				echo '5.'.$myF->dateStyle($fromDt,'.').'이전의 일정이 있으면 계약기간을 변경할 수 없습니다.';
				exit;
			}
		}

		if (!empty($toDt)){
			$sql = 'select count(*)
					  from t01iljung
					 where t01_ccode      = \''.$code.'\'
					   and t01_mkind      = \''.$svcCd.'\'
					   and t01_jumin      = \''.$jumin.'\'
					   and t01_sugup_date > \''.str_replace('-','',$toDt).'\'
					   and t01_del_yn     = \'N\'';

			$liCnt = $conn->get_data($sql);

			if ($liCnt > 0){
				$conn->close();
				echo '6.'.$myF->dateStyle($toDt,'.').'이후의 일정이 있으면 계약기간을 변경할 수 없습니다.';
				exit;
			}
		}
		*/
	}

	include_once('../inc/_db_close.php');
?>