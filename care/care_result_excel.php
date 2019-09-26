<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$orgNm	= $_SESSION['userCenterName'];
	$gbn	= $_SESSION['userLevel'];
	$jumin	= $_SESSION['userSSN'];
	$SR		= $_POST['SR'];
	$fromDt	= str_replace('-','',$_POST['from']);
	$toDt	= str_replace('-','',$_POST['to']);
	$order	= $_POST['order'];

	header( "Content-type: application/vnd.ms-excel;charset=UTF-8");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=carevisit_excel_".date('Ymd').".xls" );
?>
<div style="text-align:center; font-size:20px;"><?=$orgNm;?> 실적내용</div>
<div style="text-align:right; font-size:13px;">조회기간 : <?=$myF->dateStyle($fromDt,'.');?> ~ <?=$myF->dateStyle($toDt,'.');?></div>
<table border="1">
	<tr style="height:30px;">
		<th style="width:50px; background-color:#EAEAEA;">No</th>
		<th style="width:100px; background-color:#EAEAEA;">일자</th>
		<th style="width:60px; background-color:#EAEAEA;">시간</th>
		<th style="width:100px; background-color:#EAEAEA;">고객명</th>
		<th style="width:100px; background-color:#EAEAEA;">생년월일</th>
		<th style="width:50px; background-color:#EAEAEA;">성별</th>
		<th style="width:150px; background-color:#EAEAEA;">서비스</th>
		<th style="width:150px; background-color:#EAEAEA;">자원</th>
		<th style="width:100px; background-color:#EAEAEA;">담당</th>
		<th style="width:200px; background-color:#EAEAEA;">내용</th>
		<th style="width:100px; background-color:#EAEAEA;">비고</th>
	</tr><?
	$sql = 'SELECT	t01_jumin AS jumin
			,		t01_sugup_date AS date
			,		t01_sugup_fmtime AS time
			,		t01_sugup_seq AS seq
			,		m03_name AS name
			,		suga.suga_nm AS suga_nm
			,		t01_yname1 AS customer
			,		t01_yname2 AS member
			,		t01_status_gbn AS stat
			,		result.content AS cont
			,		result.picture AS pic
			,		result.no AS idx
			,		IFNULL(mst_jumin.jumin,t01_jumin) AS real_jumin
			FROM	t01iljung
			INNER	JOIN	m03sugupja
					ON		m03_ccode = t01_ccode
					AND		m03_mkind = \'6\'
					AND		m03_jumin = t01_jumin
			INNER	JOIN	mst_jumin
					ON		mst_jumin.org_no = t01_ccode
					AND		mst_jumin.gbn = \'1\'
					AND		mst_jumin.code = t01_jumin
			INNER	JOIN	care_suga AS suga
					ON		suga.org_no = t01_ccode
					AND		suga.suga_sr= t01_mkind
					AND		CONCAT(suga.suga_cd,suga.suga_sub) = t01_suga_code1
					AND		REPLACE(suga.from_dt,\'-\',\'\')<= t01_sugup_date
					AND		REPLACE(suga.to_dt,\'-\',\'\')	>= t01_sugup_date
			LEFT	JOIN	care_result AS result
					ON		result.org_no	= t01_ccode
					AND		result.org_type	= t01_mkind
					AND		result.jumin	= t01_jumin
					AND		result.date		= t01_sugup_date
					AND		result.time		= t01_sugup_fmtime
					AND		result.seq		= t01_sugup_seq
					AND		result.del_flag	= \'N\'
			WHERE	t01_ccode = \''.$orgNo.'\'
			AND		t01_mkind = \''.$SR.'\'
			AND		t01_sugup_date >= \''.$fromDt.'\'
			AND		t01_sugup_date <= \''.$toDt.'\'
			AND		t01_del_yn = \'N\'';

	if ($gbn != 'C'){
		$sql .= '
			AND		t01_yoyangsa_id2 = \''.$jumin.'\'';
	}

	if ($order == '1'){
		$sql .= ' ORDER BY date,time,name';
	}else if ($order == '2'){
		$sql .= ' ORDER BY member,date,time';
	}else if ($order == '3'){
		$sql .= ' ORDER BY suga_nm,date,time';
	}else if ($order == '4'){
		$sql .= ' ORDER BY name,date,time';
	}

	$weekly = Array(0=>'<span style="color:#FF0000;">일</span>',1=>'월',2=>'화',3=>'수',4=>'목',5=>'금',6=>'<span style="color:#0000FF;">토</span>');

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	if ($rowCnt > 0){
		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$date	= $myF->dateStyle($row['date'],'.');
			$week	= Date('w',StrToTime($date));

			//성별
			$gender = SubStr($row['real_jumin'],6,1);

			if ($gender){
				if ($gender % 2 == 1){
					$gender = '<span style="color:BLUE;">남</span>';
				}else{
					$gender = '<span style="color:RED;">여</span>';
				}
			}?>
			<tr>
				<td style="text-align:center;"><?=$no;?></td>
				<td style="text-align:center;"><?=$date;?>(<?=$weekly[$week];?>)</td>
				<td style="text-align:center;"><?=$myF->timeStyle($row['time']);?></td>
				<td style="text-align:left;"><?=$row['name'];?></td>
				<td style="text-align:center;"><?=$myF->issToBirthday($row['real_jumin'],'.');?></td>
				<td style="text-align:center;"><?=$gender;?></td>
				<td style="text-align:left;"><?=$row['suga_nm'];?></td>
				<td style="text-align:left;"><?=$row['customer'];?></td>
				<td style="text-align:left;"><?=$row['member'];?></td>
				<td style="text-align:left;"><?=StripSlashes($row['cont']);?></td>
				<td style="text-align:center;"></td>
			</tr><?
			$no ++;
		}
	}else{?>
		<tr>
			<td style="text-align:center;" colspan="11">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	$conn->row_free();
?>
</table>
<?
	include_once('../inc/_db_close.php');
?>