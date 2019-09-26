<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$today	= Date('Ymd');
	$gbn	= $_POST['gbn'];

	$sql = 'SELECT	request.jumin
			,		mst.m03_name AS name
			,		request.date
			,		request.time
			,		request.seq
			,		request.idx
			,		plan.t01_suga_code1 AS plan_suga_cd
			,		request.request_type
			,		request.from_time
			,		request.to_time
			,		request.bipay_yn
			,		request.sub_cd
			,		request.suga_cd
			,		request.mem_cd1
			,		request.mem_nm1
			,		request.result_yn
			,		request.result_etc
			,		request.result_dt
			,		request.send_yn
			,		request.send_dt
			,		request.insert_dt
			FROM	plan_change_request AS request
			INNER	JOIN	m03sugupja AS mst
					ON		mst.m03_ccode = request.org_no
					AND		mst.m03_mkind = request.svc_cd
					AND		mst.m03_jumin = request.jumin
			INNER	JOIN	t01iljung AS plan
					ON		plan.t01_ccode = request.org_no
					AND		plan.t01_mkind = request.svc_cd
					AND		plan.t01_jumin = request.jumin
					AND		plan.t01_sugup_date = request.date
					AND		plan.t01_sugup_fmtime = request.time
					AND		plan.t01_sugup_seq = request.seq
					AND		plan.t01_del_yn = \'N\'
			WHERE	request.org_no		= \''.$orgNo.'\'
			AND		request.svc_cd		= \'0\'
			AND		request.date		= \''.$today.'\'';

	if ($gbn == '2'){
		$sql .= '
			AND		request.result_yn	= \'N\'';
	}else if ($gbn == '3'){
		$sql .= '
			AND		request.send_yn		= \'N\'';
	}else if ($gbn == '4'){
		$sql .= '
			AND		request.result_yn	= \'Y\'';
	}else if ($gbn == '5'){
		$sql .= '
			AND		request.send_yn		= \'Y\'';
	}

	$sql .= '
			ORDER	BY time, name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($i % 2 == 0){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		}?>
		<tr style="cursor:default; background-color:#<?=$bgcolor;?>;">
			<td class="center" rowspan="2"></td>
			<td class="center"></td>
			<td class="center" rowspan="2"></td>
			<td class="center" rowspan="2"></td>
			<td class="center"></td>
			<td class="center" rowspan="2"></td>
			<td class="center" rowspan="2"></td>
			<td class="center" rowspan="2"></td>
			<td class="center" rowspan="2"></td>
			<td class="center last" rowspan="2"></td>
		</tr>
		<tr style="cursor:default; background-color:#<?=$bgcolor;?>;">
			<td class="center"></td>
			<td class="center"></td>
		</tr><?
	}

	$conn->row_free();

	if ($rowCnt == 0){?>
		<tr>
			<td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	include_once('../inc/_db_close.php');
?>