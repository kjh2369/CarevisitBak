<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	//기관
	$orgNo = $_SESSION['userCenterCode'];

	//TODAY
	$today = Date('Ymd');

	$reasonGbn = $_POST['reason'];

	//서비스
	$subNm = Array('200'=>'요양','500'=>'목욕','800'=>'간호');

	//사유
	$reason = Array('01'=>'천재지변','02'=>'응급상황','03'=>'자격변동 처리 지연','04'=>'기타사유');

	$sql = 'SELECT	jumin
			,		m03_name AS name
			,		time
			,		seq
			,		idx
			,		plan_from
			,		plan_to
			,		plan_mem_cd1
			,		plan_mem_nm1
			,		plan_mem_cd2
			,		plan_mem_nm2
			,		request_type
			,		from_time
			,		to_time
			,		sub_cd
			,		mem_cd1
			,		mem_nm1
			,		mem_cd2
			,		mem_nm2
			,		result_yn
			,		result_dt
			,		send_yn
			,		send_dt
			,		complete_yn
			,		complete_dt
			,		reason_gbn
			,		reason_str
			,		error_yn
			,		error_msg
			FROM	plan_change_request
			INNER	JOIN	m03sugupja
					ON		m03_ccode	= org_no
					AND		m03_mkind	= svc_cd
					AND		m03_jumin	= jumin
			WHERE	org_no	= \''.$orgNo.'\'
			AND		svc_cd	= \'0\'
			AND		date	= \''.$today.'\'
			AND		del_flag= \'N\'';

	if ($reasonGbn){
		$sql .= '
			AND		reason_gbn = \''.$reasonGbn.'\'';
	}

	$sql .= '
			ORDER	BY time,seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$note = '';

		if ($row['complete_yn'] == 'Y'){
			$stat = '완료';
		}else if ($row['send_yn'] == 'Y'){
			$stat = '전송';
		}else if ($row['result_yn'] == 'Y'){
			$stat = '대기';
		}else if ($row['idx'] > 0){
			$stat = '접수';
		}else{
			$stat = '';
		}

		if ($row['error_yn'] == 'Y'){
			$stat = '<span style="color:RED;">에러</span>';
			$note = $row['error_msg'];
		}

		if ($row['request_type'] == '1'){
			$requestType  = '변경';
		}else if ($row['request_type'] == '1'){
			$requestType  = '추가';
		}else if ($row['request_type'] == '1'){
			$requestType  = '삭제';
		}

		if ($row['sub_cd'] == '200'){
			$subCd = '001';
		}else if ($row['sub_cd'] == '500'){
			$subCd = '002';
		}else if ($row['sub_cd'] == '800'){
			$subCd = '003';
		}

		if ($i % 2 == 0){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		}?>
		<tr style="background-color:#<?=$bgcolor;?>;">
			<td class="center"><?=$i+1;?></td>
			<td class="center"><div class="left"><?=$row['name'];?></div></td>
			<td class="center"><?=$subNm[$row['sub_cd']];?></td>
			<td class="center"><?=$myF->timeStyle($row['plan_from']);?></td>
			<td class="center"><?=$myF->timeStyle($row['plan_to']);?></td>
			<td class="center"><div class="left"><?=$row['plan_mem_nm1'].($row['plan_mem_nm2'] ? '/'.$row['plan_mem_nm2'] : '');?></div></td>
			<td class="center"><?=$requestType;?></td>
			<td class="center"><?=$myF->timeStyle($row['from_time']);?></td>
			<td class="center"><?=$myF->timeStyle($row['to_time']);?></td>
			<td class="center"><div class="left"><?=$row['mem_nm1'].($row['mem_nm2'] ? '/'.$row['mem_nm2'] : '');?></div></td>
			<td class="center"><div class="left"><?=$reason[$row['reason_gbn']];?></div></td>
			<td class="center"><?=$stat;?></td>
			<td class="center last"><div class="left nowrap" style="width:100px;" title="<?=$note;?>"><?=$note;?></div></td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>