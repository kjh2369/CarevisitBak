<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");

	$orgNo  = $_GET['code'];
	$year  = $_GET['year'];
	$month = $_GET['month'];
	$appNo = $_GET['appNo'];
	$printYn = $_GET['printYn'];
	if($printYn == 'Y'){
		$border = "border='1'";
	}
	
	//횡성
	if($orgNo == 'drcare'){
		if($year.$month <= '201612'){ 
			$orgNo = '34273000017';
		} 
	}



if($printYn != 'Y'){ ?>
	<div id="divStart"></div><?
} ?>
<table class='list_type' <?=$border;?> >
	<colgroup>
		<col width="60px">
		<col width="60px">
		<col width="75px">
		<col width="60px">
		<col width="90px">
		<col width="45px">
		<col width="45px">
		<col width="75px">
		<col width="75px">
		<col width="160px">
		<col width="90px">
		<col width="40px">
	</colgroup>
	<tr>
		<th >구분</th>
		<th>수급자<br style="mso-data-placement:same-cell;">성&nbsp;&nbsp;&nbsp;명</th>
		<th>인정번호</th>
		<th 요양<br style="mso-data-placement:same-cell;">요원</th>
		<th >핸드폰<br style="mso-data-placement:same-cell;">번&nbsp;&nbsp;&nbsp;호</th>
		<th >서비스<br style="mso-data-placement:same-cell;">구&nbsp;&nbsp;&nbsp;분</th>
		<th >총시간</th>
		<th >서비스<br style="mso-data-placement:same-cell;">시&nbsp;&nbsp;&nbsp;작</th>
		<th >서비스<br style="mso-data-placement:same-cell;">종&nbsp;&nbsp;&nbsp;료</th>
		<th >특정내역</th>
		<th >오&nbsp;&nbsp;&nbsp;류<br style="mso-data-placement:same-cell;">정정일</th>
		<th >사용<br style="mso-data-placement:same-cell;">여부</th>
	</tr><?

	$sql = 'SELECT	send_gbn
			,		name
			,		app_no
			,		mem_nm
			,		mem_hp
			,		svc_gbn
			,		proc_time
			,		DATE_FORMAT(from_dt,\'%Y-%m-%d\') AS from_dt
			,		TIME_FORMAT(from_tm,\'%h:%i:%s\') AS from_tm
			,		DATE_FORMAT(to_dt,\'%Y-%m-%d\') AS to_dt
			,		TIME_FORMAT(to_tm,\'%h:%i:%s\') AS to_tm
			,		dtl_1
			,		dtl_2
			,		dtl_3
			,		dtl_4
			,		DATE_FORMAT(err_dt,\'%Y-%m-%d\') AS err_dt
			,		TIME_FORMAT(err_tm,\'%h:%i:%s\') AS err_tm
			,		use_yn
			FROM	lg2cv
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm = \''.$year.$month.'\'';

	if ($appNo){
		$sql .= '
			AND		app_no = \''.$appNo.'\'';
	}

	$sql .= '
			ORDER	BY from_dt, from_tm, to_dt, to_tm';
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		switch($row['send_gbn']){
			case '02':
				$row['send_gbn'] = '자동전송';
				break;

			case '01':
				$row['send_gbn'] = '시작만전송';
				break;

			case '03':
				$row['send_gbn'] = '오류수정';
				break;

			case '04':
				$row['send_gbn'] = '직접입력';
				break;

			case '99':
				$row['send_gbn'] = '기타';
				break;
		}

		switch($row['svc_gbn']){
			case '200':
				$row['svc_gbn'] = '요양';
				break;

			case '500':
				$row['svc_gbn'] = '목욕';
				break;

			case '800':
				$row['svc_gbn'] = '간호';
				break;
		}

		if ($row['mem_hp']) $row['mem_hp'] = SubStr($row['mem_hp'],0,3).'-****-'.SubStr($row['mem_hp'],StrLen($row['mem_hp'])-4,StrLen($row['mem_hp']));

		$row['dtl_1'] = SubStr('000'.$row['dtl_1'],StrLen('000'.$row['dtl_1'])-3,StrLen('000'.$row['dtl_1']));
		$row['dtl_2'] = SubStr('000'.$row['dtl_2'],StrLen('000'.$row['dtl_2'])-3,StrLen('000'.$row['dtl_2']));
		$row['dtl_3'] = SubStr('000'.$row['dtl_3'],StrLen('000'.$row['dtl_3'])-3,StrLen('000'.$row['dtl_3']));
		$row['dtl_4'] = SubStr('000'.$row['dtl_4'],StrLen('000'.$row['dtl_4'])-3,StrLen('000'.$row['dtl_4']));?>
		<tr>
			<td ><?=$row['send_gbn'];?></td>
			<td ><?=$row['name'];?></td>
			<td ><?=$row['app_no'];?></td>
			<td ><?=$row['mem_nm'];?></td>
			<td ><?=$row['mem_hp'];?></td>
			<td ><?=$row['svc_gbn'];?></td>
			<td ><?=$row['proc_time'];?>분</td>
			<td ><?=$row['from_dt'];?></br><?=$row['from_tm'];?></td>
			<td ><?=$row['to_dt'];?></br><?=$row['to_tm'];?></td>
			<td >정서지원:<?=$row['dtl_1'];?>분/신체활동:<?=$row['dtl_2'];?>분/</br>일상생활:<?=$row['dtl_3'];?>분/개인활동:<?=$row['dtl_4'];?>분</td>
			<td ><?=$row['err_dt'];?></br><?=$row['err_tm'];?></td>
			<td ><?=$row['use_yn'];?></td>
		</tr><?
	}

	$conn->row_free();	
	$conn -> close();

	?>
</table>
<?
if($printYn != 'Y'){ ?>
	<div id="divLast"></div><?
} ?>