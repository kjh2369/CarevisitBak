<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code	= $_SESSION['userCenterCode'];
	$svcNm	= Array('0'=>'재가요양','1'=>'가사간병','2'=>'노인돌봄','3'=>'산모신생아','4'=>'장애인활동지원','A'=>'산모유료','B'=>'병원간병','C'=>'기타비급여');
	$from	= Str_Replace('.','',$_POST['from']);
	$to		= Str_Replace('.','',$_POST['to']);
	$svc	= Explode(chr(1),$_POST['svc']);

	$sql = '';

	foreach($svc as $tmp){
		if (Is_Numeric(StrPos($tmp,'_'))){
			$var = Explode('_',$tmp);
			$svcCd = $var[0];
			$subCd = $var[1];
		}else{
			$svcCd = $tmp;
			$subCd = '';
		}

		$sql .= ($sql ? ' UNION ALL ' : '');
		$sql .= 'SELECT	t01_mkind AS svc_cd
				,		t01_sugup_date AS date
				,		m03_name AS name
				,		t01_sugup_fmtime AS from_time
				,		t01_sugup_totime AS to_time
				,		t01_yname1 AS mem_nm1
				,		t01_yname2 AS mem_nm2
				,		t01_suga_code1
				FROM	t01iljung
				INNER	JOIN	m03sugupja
						ON		m03_ccode = t01_ccode
						AND		m03_mkind = t01_mkind
						AND		m03_jumin = t01_jumin
				WHERE	t01_ccode	= \''.$code.'\'
				AND		t01_mkind	= \''.$svcCd.'\'
				AND		t01_sugup_date >= \''.$from.'\'
				AND		t01_sugup_date <= \''.$to.'\'
				AND		t01_sugup_fmtime != \'\'
				AND		t01_del_yn	= \'N\'';

		if ($subCd){
			$sql .= '
				AND		t01_svc_subcode = \''.$subCd.'\'';
		}
	}

	$sql .= '
			ORDER	BY date,from_time,to_time,name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= 'week='	.Date('w',StrToTime($row['date']));
		$data .= '&svcNm='	.$svcNm[$row['svc_cd']];
		$data .= '&from='	.$row['from_time'];
		$data .= '&to='		.$row['to_time'];
		$data .= '&name='	.$row['name'];
		$data .= '&mem='	.$row['mem_nm1'].($row['mem_nm2'] ? '/'.$row['mem_nm2'] : '');
		$data .= chr(11);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>