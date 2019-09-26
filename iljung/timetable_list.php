<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_page_list.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$time1 = $myF->getMtime();
	$orgNo	= $_SESSION['userCenterCode'];

	//상태
	$stat = Array(
			'1'=>'<span style="color:blue;">완료</span>'
		,	'5'=>'<span style="color:red;">수행중</span>'
		,	'9'=>'<span style="color:black;">대기</span>'
	);

	//수가정보
	$sql = 'SELECT	m01_mcode2 AS cd
			,		m01_suga_cont AS nm
			,		m01_suga_value AS cost
			,		m01_sdate AS from_dt
			,		m01_edate AS to_dt
			FROM	m01suga
			WHERE	m01_mcode          = \'goodeos\'
			UNION	ALL
			SELECT	m11_mcode2 AS cd
			,		m11_suga_cont AS nm
			,		m11_suga_value AS cost
			,		m11_sdate AS from_dt
			,		m11_edate AS to_dt
			FROM	m11suga
			WHERE	m11_mcode          = \'goodeos\'
			UNION	ALL
			SELECT	service_code AS cd
			,		service_gbn AS nm
			,		service_cost AS cost
			,		service_from_dt AS from_dt
			,		service_to_dt AS to_dt
			FROM	suga_service
			WHERE	org_no = \'goodeos\'
			UNION	ALL
			SELECT	DISTINCT
					CONCAT(suga_cd,suga_sub) AS code
			,		suga_nm AS name
			,		suga_cost AS cost
			,		REPLACE(from_dt,\'-\',\'\') AS from_dt
			,		REPLACE(to_dt,\'-\',\'\') AS to_dt
			FROM	care_suga
			WHERE	org_no	= \''.$orgNo.'\'';

	$suga = $conn->_fetch_array($sql);

	$clientName	= $_POST['clientName'];
	$memberName	= $_POST['memberName'];
	$fromDt		= str_replace('-', '', $_POST['fromDt']);
	$toDt		= str_replace('-', '', $_POST['toDt']);
	$orderBy	= $_POST['orderBy'];
	$chkSvc		= Explode(chr(1), $_POST['chkSvc']);

	if (!$fromDt && !$toDt){
		 $fromDt = Date('Ymd');
	}

	if (!$fromDt) $fromDt = $toDt;
	if (!$toDt) $toDt = $fromDt;

	$itemCnt = 20;
	$pageCnt = 10;
	$page = $_POST['page'];

	if ($page != 'ALL'){
		if (Empty($page)){
			$page = 1;
		}
	}

	//기본쿼리
	foreach($chkSvc as $svcIdx => $svcList){
		if ($svcList){
			if (Is_Numeric(StrPos($svcList,'_'))){
				$tmp = Explode('_',$svcList);
				$svcCd = $tmp[0];
				$subCd = $tmp[1];
			}else{
				$svcCd = $svcList;
				$subCd = '';
			}

			if (!Empty($query)){
				$query .= ' UNION ALL ';
			}

			$query .= '	SELECT	t01_mkind			AS svc_cd
						,		t01_svc_subcode		AS sub_cd
						,		t01_sugup_date		AS dt
						,		t01_sugup_fmtime	AS from_time
						,		t01_sugup_totime	AS to_time
						,		t01_suga_code1		AS suga_cd
						,		t01_jumin			AS jumin
						,		t01_yoyangsa_id1	AS mem_cd1
						,		t01_yoyangsa_id2	AS mem_cd2
						,		t01_mem_nm1			AS mem_nm1
						,		t01_mem_nm2			AS mem_nm2
						,		t01_status_gbn		AS stat
						FROM	t01iljung
						WHERE	t01_ccode		= \''.$orgNo.'\'
						AND		t01_mkind		= \''.$svcCd.'\'
						AND		t01_del_yn		= \'N\'
						AND		t01_sugup_date >= \''.$fromDt.'\'
						AND		t01_sugup_date <= \''.$toDt.'\'';

			if ($subCd){
				$query .= '
						AND		t01_svc_subcode	= \''.$subCd.'\'';
			}

			if ($memberName){
				$query .= '
						AND		(t01_mem_nm1 = \''.$memberName.'\'
						OR		 t01_mem_nm2 = \''.$memberName.'\')';
			}
		}
	}

	if ($page != 'ALL'){
		$sql = 'SELECT	COUNT(*)
				FROM	('.$query.') AS iljung
				INNER	JOIN	m03sugupja AS mst
						ON		m03_ccode = \''.$orgNo.'\'
						AND		m03_mkind = iljung.svc_cd
						AND		m03_jumin = iljung.jumin';

		if ($clientName){
			$sql .= '
				WHERE	m03_name = \''.$clientName.'\'';
		}

		$totCnt = $conn->get_data($sql);

		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($totCnt < (IntVal($page) - 1) * $itemCnt){
			$page = 1;
		}

		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:lfSearch',
			'curPageNum'	=> $page,
			'pageVar'		=> 'page',
			'extraVar'		=> '',
			'totalItem'		=> $totCnt,
			'perPage'		=> $pageCnt,
			'perItem'		=> $itemCnt,
			'prevPage'		=> '[이전]',
			'nextPage'		=> '[다음]',
			'prevPerPage'	=> '[이전'.$pageCnt.'페이지]',
			'nextPerPage'	=> '[다음'.$pageCnt.'페이지]',
			'firstPage'		=> '[처음]',
			'lastPage'		=> '[끝]',
			'pageCss'		=> 'page_list_1',
			'curPageCss'	=> 'page_list_2'
		);

		$pageCount = (intVal($page) - 1) * $itemCnt;
	}else{
		$pageCount = 0;
	}

	$sql = 'SELECT	dt
			,		from_time
			,		to_time
			,		suga_cd
			,		mst.m03_name AS name
			,		mem_cd1, mem_cd2
			,		mem_nm1
			,		mem_nm2
			,		stat
			FROM	('.$query.') AS iljung
			INNER	JOIN	m03sugupja AS mst
					ON		m03_ccode = \''.$orgNo.'\'
					AND		m03_mkind = iljung.svc_cd
					AND		m03_jumin = iljung.jumin';

	if ($clientName){
		$sql .= '
			WHERE	m03_name = \''.$clientName.'\'';
	}

	$sql .= '
			ORDER	BY dt '.$orderBy.', from_time '.$orderBy;

	if ($page != 'ALL'){
		$sql .= '
			LIMIT	'.$pageCount.','.$itemCnt;
	}

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	if ($rowCnt > 0){
		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($i % 2 == 0){
				$bgcolor = '#FFFFFF';
			}else{
				$bgcolor = '#EFEFEF';
			}

			//수가찾기
			$sugaNm = '';

			foreach($suga as $arr){
				if ($row['suga_cd'] == $arr['cd'] && $row['dt'] >= $arr['from_dt'] && $row['dt'] <= $arr['to_dt']){
					$sugaNm = $arr['nm'];
					break;
				}
			}?>
			<tr style="cursor:default; background-color:<?=$bgcolor;?>;" onmouseover="this.style.backgroundColor='#D9E5FF';" onmouseout="this.style.backgroundColor='<?=$bgcolor;?>';">
				<td class="center"><?=$pageCount + ($i + 1);?></td>
				<td class="center"><?=$myF->dateStyle($row['dt'],'.');?></td>
				<td class="center"><?=$myF->timeStyle($row['from_time']);?>~<?=$myF->timeStyle($row['to_time']);?></td>
				<td class="center"><div class="left nowrap" style="width:150px;"><?=$sugaNm;?></div></td>
				<td class="center"><div class="left nowrap" style="width:90px;"><?=$row['name'];?></div></td>
				<td class="center"><div class="left nowrap" style="width:250px;"><?=$row['mem_nm1'].'('.$myF->issToBirthday($row['mem_cd1'], '.').')'.($row['mem_nm2'] ? ' / '.$row['mem_nm2'].'('.$myF->issToBirthday($row['mem_cd2'], '.').')' : '');?></div></td>
				<td class="center"><?=$stat[$row['stat']];?></td>
				<td class="center last">&nbsp;</td>
			</tr><?
		}

		$conn->row_free();

		$time2 = $myF->getMtime();
		$time  = $time2 - $time1;

		if ($page != 'ALL'){
			$paging = new YsPaging($params);
			$pageList = $paging->returnPaging();
		}?>
		<tr>
			<td class="center bottom last" colspan="20"><?=$pageList;?></td>
		</tr><?
	}else{?>
		<tr>
			<td class="center bottom last" colspan="20">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	include_once('../inc/_db_close.php');
?>