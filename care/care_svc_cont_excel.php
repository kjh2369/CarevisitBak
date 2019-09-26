<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	$orgNo	= $_SESSION['userCenterCode'];
	$orgNm	= $_SESSION['userCenterName'];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$month	= ($month < 10 ? '0' : '').$month;

	//대상자 조회
	$sql = 'SELECT	jumin
			,		m03_name AS nm
			FROM	client_his_svc
			INNER	JOIN m03sugupja
					ON   m03_ccode	= org_no
					AND  m03_mkind	= \'6\'
					AND  m03_jumin	= jumin
					AND  m03_del_yn	= \'N\'
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \''.$SR.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$year.$month.'\'
			ORDER	BY nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$jumin = $row['jumin'];

		if (!$client[$jumin]){
			 $client[$jumin]['name'] = $row['nm'];
		}
	}

	$conn->row_free();
	

	//서비스 조회
	$sql = 'SELECT	t01_suga_code1 AS suga_cd
			,		suga.suga_nm AS suga_nm
			,		t01_jumin AS jumin
			FROM	t01iljung
			INNER	JOIN	care_suga AS suga
					ON		suga.org_no = t01_ccode
					AND		suga.suga_sr = t01_mkind
					AND		CONCAT(suga_cd,suga_sub) = t01_suga_code1
					AND		REPLACE(suga.from_dt, \'-\', \'\') <= t01_sugup_date
					AND		REPLACE(suga.to_dt, \'-\', \'\') >= t01_sugup_date
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_mkind	= \''.$SR.'\'
			AND		t01_del_yn	= \'N\'
			AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
			ORDER	BY suga_nm, jumin';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$code = $row['suga_cd'];
		$jumin = $row['jumin'];

		if (!$suga[$code]){
			 $suga[$code]['name'] = $row['suga_nm'];
		}

		$data[$code][$jumin] ++;
	}

	$conn->row_free();
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div><?=$year;?>년 <?=IntVal($month);?>월 <?=$orgNm;?> 서비스내역</div>
<table border="1">
	<tr>
		<th style="width:150px; background-color:#EFEFEF;">서비스</th><?
		foreach($client as $code => $name){
			$str = '';

			foreach($name as $i => $s){
				$str .= $s.'<br>';
			}?>
			<th style="width:25px; vertical-align:top; background-color:#EFEFEF;"><?=$str;?></th><?
		}?>
		<th style="width:150px; background-color:#EFEFEF;">비고</th>
	</tr><?
	foreach($suga as $code => $sugaArr){?>
		<tr>
			<td style="text-align:left;"><?=$sugaArr['name'];?></td><?
			foreach($client as $jumin => $str){
				$val = $data[$code][$jumin];
				if ($val < 1) $val = '';?>
				<td style="text-align:center; color:BLUE;"><?=$val;?></td><?
			}?>
			<td>&nbsp;</td>
		</tr><?
	}?>
</table>
<?
	include_once('../inc/_db_close.php');
?>