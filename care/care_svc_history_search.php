<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $_POST['jumin'];
	$name = $_POST['name'];
	$from = $myF->dateStyle($_POST['from']);
	$to = $myF->dateStyle($_POST['to']);
	$SR = $_POST['SR'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if (!$jumin || !$name){
		exit;
	}

	$jumin = SubStr($jumin,0,7);

	$sql = 'SELECT	DISTINCT
					org_no
			,		svc_cd
			,		from_dt
			,		to_dt
			FROM	(
					SELECT	org_no
					,		jumin
					,		svc_cd
					,		from_dt
					,		to_dt
					FROM	client_his_svc
					WHERE	LEFT(jumin,7) = \''.SubStr($jumin,0,7).'\'
					AND		svc_cd != \''.$SR.'\'
					AND		svc_cd != \'3\'
					UNION	ALL
					SELECT	org_no
					,		jumin
					,		svc_cd
					,		from_dt
					,		to_dt
					FROM	care_svc_his
					WHERE	LEFT(jumin,7) = \''.SubStr($jumin,0,7).'\'
				) AS svc
			INNER	JOIN	m03sugupja
					ON		m03_ccode = org_no
					AND		m03_jumin = jumin
					AND		m03_name  = \''.$name.'\'
			ORDER	BY from_dt DESC, to_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['svc_cd'] == '0'){
			$svcNm = '재가요양';
		}else if ($row['svc_cd'] == '1'){
			$svcNm = '가사간병';
		}else if ($row['svc_cd'] == '2'){
			$svcNm = '노인돌봄';
		}else if ($row['svc_cd'] == '3'){
			$svcNm = '산모신생아';
		}else if ($row['svc_cd'] == '4'){
			$svcNm = '장애인활동보조';
		}else if ($row['svc_cd'] == 'A'){
			$svcNm = '산모유료(비급여)';
		}else if ($row['svc_cd'] == 'B'){
			$svcNm = '병원간병(비급여)';
		}else if ($row['svc_cd'] == 'C'){
			$svcNm = '기타비급여(비급여)';
		}else if ($row['svc_cd'] == 'S'){
			$svcNm = '재가지원';
		}else if ($row['svc_cd'] == 'R'){
			$svcNm = '자원연계';
		}else{
			$svcNm = '기타';
		}

		if (($from >= $row['from_dt'] && $from <= $row['to_dt']) ||
			($to >= $row['from_dt'] && $to <= $row['to_dt'])){
			$duplicate = true;
		}else{
			$duplicate = false;
		}

		if ($orgNo == $row['org_no']){
			$orgStr = '<span style="color:BLUE;">본기관</span>';
		}else{
			$orgStr = '<span style="color:RED;">타기관</span>';
		}?>
		<tr>
			<td class="center"><div class="left <?=($duplicate ? 'bold' : '');?>"><?=$svcNm;?></div></td>
			<td class="center"><span class="<?=($duplicate ? 'bold' : '');?>"><?=$myF->dateStyle($row['from_dt'],'.');?></span></td>
			<td class="center"><span class="<?=($duplicate ? 'bold' : '');?>"><?=$myF->dateStyle($row['to_dt'],'.');?></span></td>
			<td class="center"><span class="<?=($duplicate ? 'bold' : '');?>"><?=$orgStr;?></span></td>
			<td class="center"><span class="bold" style="color:red;"><?=($duplicate ? 'Y' : '');?></span></td>
			<td class="center"></td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>