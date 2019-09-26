<?
	include_once('../inc/_login.php');
?>
<table>
	<tr>
		<td style="text-align:left;" colspan="6">급여제공 기간 : <?=$myF->dateStyle($fromDt,'.');?> ~ <?=$myF->dateStyle($toDt,'.');?></td>
		<td style="text-align:right;" colspan="6">출력일자 : <?=Date('Y.m.d');?></td>
	</tr>
	<tr>
		<th style="width:40px; border:0.5pt solid BLACK; background-color:#EAEAEA;">구분</th>
		<th style="width:60px; border:0.5pt solid BLACK; background-color:#EAEAEA;">수급자<br style="mso-data-placement:same-cell;">성&nbsp;&nbsp;&nbsp;명</th>
		<th style="width:90px; border:0.5pt solid BLACK; background-color:#EAEAEA;">인정번호</th>
		<th style="width:60px; border:0.5pt solid BLACK; background-color:#EAEAEA;">요양<br style="mso-data-placement:same-cell;">요원</th>
		<th style="width:100px; border:0.5pt solid BLACK; background-color:#EAEAEA;">핸드폰<br style="mso-data-placement:same-cell;">번&nbsp;&nbsp;&nbsp;호</th>
		<th style="width:50px; border:0.5pt solid BLACK; background-color:#EAEAEA;">서비스<br style="mso-data-placement:same-cell;">구&nbsp;&nbsp;&nbsp;분</th>
		<th style="width:60px; border:0.5pt solid BLACK; background-color:#EAEAEA;">총시간</th>
		<th style="width:80px; border:0.5pt solid BLACK; background-color:#EAEAEA;">서비스<br style="mso-data-placement:same-cell;">시&nbsp;&nbsp;&nbsp;작</th>
		<th style="width:80px; border:0.5pt solid BLACK; background-color:#EAEAEA;">서비스<br style="mso-data-placement:same-cell;">종&nbsp;&nbsp;&nbsp;료</th>
		<th style="width:200px; border:0.5pt solid BLACK; background-color:#EAEAEA;">특정내역</th>
		<th style="width:80px; border:0.5pt solid BLACK; background-color:#EAEAEA;">오&nbsp;&nbsp;&nbsp;류<br style="mso-data-placement:same-cell;">정정일</th>
		<th style="width:40px; border:0.5pt solid BLACK; background-color:#EAEAEA;">사용<br style="mso-data-placement:same-cell;">여부</th>
	</tr><?

	$sql = 'SELECT	send_gbn
			,		name
			,		app_no
			,		mem_nm
			,		mem_hp
			,		svc_gbn
			,		proc_time
			,		DATE_FORMAT(from_dt,\'%Y-%m-%d\') AS from_dt
			,		TIME_FORMAT(from_tm,\'%H:%i:%s\') AS from_tm
			,		DATE_FORMAT(to_dt,\'%Y-%m-%d\') AS to_dt
			,		TIME_FORMAT(to_tm,\'%H:%i:%s\') AS to_tm
			,		dtl_1
			,		dtl_2
			,		dtl_3
			,		dtl_4
			,		DATE_FORMAT(err_dt,\'%Y-%m-%d\') AS err_dt
			,		TIME_FORMAT(err_tm,\'%H:%i:%s\') AS err_tm
			,		use_yn
			FROM	lg2cv
			WHERE	org_no	= \''.$orgNo.'\'
			AND		reg_dt	BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'
			AND		use_yn	= \'Y\'';

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
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['send_gbn'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['name'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['app_no'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['mem_nm'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK; mso-number-format:'\@'"><?=$row['mem_hp'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['svc_gbn'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['proc_time'];?>분</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['from_dt'];?><br style="mso-data-placement:same-cell;"><?=$row['from_tm'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['to_dt'];?><br style="mso-data-placement:same-cell;"><?=$row['to_tm'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;">정서지원:<?=$row['dtl_1'];?>분/신체활동:<?=$row['dtl_2'];?>분<br style="mso-data-placement:same-cell;">일상생활:<?=$row['dtl_3'];?>분/인지활동:<?=$row['dtl_4'];?>분</td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['err_dt'];?><br style="mso-data-placement:same-cell;"><?=$row['err_tm'];?></td>
			<td style="text-align:center; border:0.5pt solid BLACK;"><?=$row['use_yn'];?></td>
		</tr><?
	}

	$conn->row_free();?>
</table>