<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$orgNm = $_SESSION['userCenterName'];
	$date = Date('YmdHis');

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=carevisit_$date.xls" );
	header( "Content-Description: test" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	$sr = $_POST['sr'];
	$year = $_POST['year'];
	$month = IntVal($_POST['month']);
	$month = ($month < 10 ? '0' : '').$month;
	$fromDay = IntVal($_POST['fromDay']);
	$toDay = IntVal($_POST['toDay']);
	$fromDay = ($fromDay < 10 ? '0' : '').$fromDay;
	$toDay = ($toDay < 10 ? '0' : '').$toDay;
	$service = $_POST['service'];
	$resource = $_POST['resource'];
	$order = $_POST['order'];

	if ($fromDay < 1) $fromDay = $toDay;
	if ($toDay < 1) $toDay = $fromDay;

	$fromDt	= str_replace('-','',$_POST['from']);
	$toDt	= str_replace('-','',$_POST['to']);

	if (!$fromDt) $fromDt = $year.$month.$fromDay;
	if (!$toDt) $toDt = $year.$month.$toDay;

	//$title = $year.'년 '.IntVal($month).'월 '.$orgNm;
	$title = $orgNm;

	if ($sr == 'S'){
		$title .= ' 재가지원 ';
	}else if ($sr == 'R'){
		$title .= ' 자원연계 ';
	}else{
		exit;
	}

	$title .= '실적관리';
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div style="font-size:20px; margin:20px;"><?=$title;?></div>
<div style="font-size:13px; text-align:right;">조회기간 : <?=$myF->dateStyle($fromDt,'.');?> ~ <?=$myF->dateStyle($toDt,'.');?></div>
<table border="1">
	<tr>
		<th style="width:50px; background-color:#EAEAEA;">No</th>
		<th style="width:50px; background-color:#EAEAEA;">일자</th>
		<th style="width:70px; background-color:#EAEAEA;">시간</th>
		<th style="width:70px; background-color:#EAEAEA;">고객명</th>
		<th style="width:80px; background-color:#EAEAEA;">생년월일</th>
		<th style="width:50px; background-color:#EAEAEA;">성별</th>
		<th style="width:200px; background-color:#EAEAEA;">서비스</th>
		<th style="width:200px; background-color:#EAEAEA;">자원</th>
		<th style="width:50px; background-color:#EAEAEA;">실적</th>
		<th style="width:150px; background-color:#EAEAEA;">비고</th>
	</tr><?
	$sql = 'SELECT	DISTINCT
					t01_jumin AS jumin
			,		m03_name AS name
			,		t01_sugup_date AS date
			,		t01_sugup_fmtime AS from_time
			,		t01_sugup_totime AS to_time
			,		t01_sugup_soyotime AS proctime
			,		t01_sugup_seq AS seq
			,		t01_status_gbn AS stat
			,		t01_suga_code1 AS suga_cd
			,		suga.suga_nm AS suga_nm
			,		t01_yoyangsa_id1 AS res_cd
			,		t01_yname1 As res_nm
			,		t01_suga_tot AS suga_cost
			,		IFNULL(mst_jumin.jumin,t01_jumin) AS real_jumin
			FROM	t01iljung
			INNER	JOIN m03sugupja
					ON	m03_ccode = t01_ccode
					AND	m03_jumin = t01_jumin
			INNER	JOIN	mst_jumin
					ON		mst_jumin.org_no = t01_ccode
					AND		mst_jumin.gbn = \'1\'
					AND		mst_jumin.code = t01_jumin
			INNER	JOIN	care_suga AS suga
					ON		suga.org_no	= t01_ccode
					AND		suga.suga_sr= \''.$sr.'\'
					AND		CONCAT(suga.suga_cd,suga.suga_sub) = t01_suga_code1
					AND		REPLACE(suga.from_dt,\'-\',\'\')<= t01_sugup_date
					AND		REPLACE(suga.to_dt,\'-\',\'\')	>= t01_sugup_date
			WHERE	t01_ccode		 = \''.$orgNo.'\'
			AND		t01_mkind		 = \''.$sr.'\'
			AND		t01_sugup_date	>= \''.$fromDt.'\'
			AND		t01_sugup_date	<= \''.$toDt.'\'
			AND		t01_del_yn		 = \'N\'';

	if ($service){
		$sql .= '
			AND		t01_suga_code1 = \''.$service.'\'';
	}

	if ($resource){
		$sql .= '
			AND		t01_yoyangsa_id1 = \''.$resource.'\'';
	}

	if ($order == '1'){
		$sql .= ' ORDER BY date,from_time,to_time';
	}else if ($order == '2'){
		$sql .= ' ORDER BY name,date,from_time,to_time';
	}else if ($order == '3'){
		$sql .= ' ORDER BY suga_nm,date,from_time,to_time';
	}else if ($order == '4'){
		$sql .= ' ORDER BY res_nm,date,from_time,to_time';
	}

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		//성별
		$gender = SubStr($row['real_jumin'],6,1);
		if ($gender) $gender = $gender % 2;
		if ($gender == 1){
			$gender = '<span style="color:BLUE;">남</span>';
		}else if ($gender == 0){
			$gender = '<span style="color:RED;">여</span>';
		}?>
		<tr>
			<td style="text-align:center;"><?=$no;?></td>
			<td style="text-align:center;"><?=IntVal(Date('d',StrToTime($row['date'])));?></td>
			<td style="text-align:center;"><?=$myF->timeStyle($row['from_time']);?></td>
			<td style="text-align:center;"><?=$row['name'];?></td>
			<td style="text-align:center;"><?=$myF->issToBirthday($row['real_jumin'],'.');?></td>
			<td style="text-align:center;"><?=$gender;?></td>
			<td><?=$row['suga_nm'];?></td>
			<td><?=$row['res_nm'];?></td>
			<td style="text-align:center;"><?=$row['stat'] == '1' ? 'Y' : '';?></td>
			<td>&nbsp;</td>
		</tr><?

		$no ++;
	}

	$conn->row_free();?>
</table>
<?
	include_once('../inc/_db_close.php');
?>