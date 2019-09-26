<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	$conn->fetch_type = 'assoc';

	$code	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$svcCd	= $_POST['svcCd'];
	$cho	= $_POST['cho'];

	//고객 등급
	$sql = 'SELECT	CONCAT(jumin,\'_\',svc_cd) AS idx
			,		jumin
			,		svc_cd
			,		app_no
			,		level
			FROM	client_his_lvl
			WHERE	org_no	= \''.$code.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\')	<= \''.$year.$month.'\'
			AND		DATE_FORMAT(to_dt,\'%Y%m\')		>= \''.$year.$month.'\'';

	if ($svcCd == 'ALL'){
	}else{
		$sql .= '	AND	svc_cd	= \''.$svcCd.'\'';
	}

	$arrLvl	= $conn->_fetch_array($sql,'idx');

	//고객 구분
	$sql = 'SELECT	jumin
			,		kind
			,		rate
			FROM	client_his_kind
			WHERE	org_no = \''.$code.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\')	<= \''.$year.$month.'\'
			AND		DATE_FORMAT(to_dt,\'%Y%m\')		>= \''.$year.$month.'\'';

	$arrGbn	= $conn->_fetch_array($sql,'jumin');

	//고객 대상조회
	$sql = 'SELECT	svc.jumin
			,		m03_name AS name
			,		svc.svc_cd
			,		svc.svc_stat
			,		svc.svc_reason
			FROM	client_his_svc AS svc
			INNER	JOIN	m03sugupja
					ON		m03_ccode	= svc.org_no
					AND		m03_mkind	= svc.svc_cd
					AND		m03_jumin	= svc.jumin
			WHERE	svc.org_no	= \''.$code.'\'
			AND		DATE_FORMAT(svc.from_dt,\'%Y%m\')	<= \''.$year.$month.'\'
			AND		DATE_FORMAT(svc.to_dt,\'%Y%m\')		>= \''.$year.$month.'\'';

	if ($svcCd == 'ALL'){
	}else{
		$sql .= '	AND	svc.svc_cd	= \''.$svcCd.'\'';
	}

	if ($cho == '전체'){
	}else if ($cho == '그외'){
	}else{
		$fromCho	= $cho;

		if ($cho == '가'){
			$toCho	= '나';
		}else if ($cho == '나'){
			$toCho	= '다';
		}else if ($cho == '다'){
			$toCho	= '라';
		}else if ($cho == '라'){
			$toCho	= '마';
		}else if ($cho == '마'){
			$toCho	= '바';
		}else if ($cho == '바'){
			$toCho	= '사';
		}else if ($cho == '사'){
			$toCho	= '아';
		}else if ($cho == '아'){
			$toCho	= '자';
		}else if ($cho == '자'){
			$toCho	= '차';
		}else if ($cho == '차'){
			$toCho	= '카';
		}else if ($cho == '카'){
			$toCho	= '타';
		}else if ($cho == '타'){
			$toCho	= '파';
		}else if ($cho == '파'){
			$toCho	= '하';
		}else if ($cho == '하'){
			$toCho	= '';
		}

		$sql .= '	AND	LEFT(m03_name,1) >= \''.$fromCho.'\'';

		if (!Empty($toCho)){
			$sql .= '	AND	LEFT(m03_name,1) <= \''.$toCho.'\'';
		}
	}

	$sql .= '	ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt	= $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row	= $conn->select_row($i);

		$data	.= $ed->en($row['jumin']).chr(2);
		$data	.= $row['name'].chr(2);
		$data	.= $row['svc_cd'].chr(2);
		$data	.= $row['svc_stat'].chr(2);
		$data	.= $arrLvl[$row['jumin'].'_'.$row['svc_cd']]['app_no'].chr(2);
		$data	.= $arrLvl[$row['jumin'].'_'.$row['svc_cd']]['level'].chr(2);
		$data	.= $arrGbn[$row['jumin']]['kind'].chr(2);
		$data	.= $arrGbn[$row['jumin']]['rate'].chr(2);

		$data	.= chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>