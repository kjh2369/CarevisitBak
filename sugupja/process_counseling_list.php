<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_page_list.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];

	$counselTypeStr = Array(
			'VISIT'	=>'고객방문상담'
		,	'PHONE'	=>'전화상담'
		,	'STRESS'=>'불만 및 고충처리'
		,	'CASE'	=>'사례관리회의'
		,	'STATE'	=>'상태변화일지'
	);

	$clientName	= $_POST['clientName'];
	$memberName	= $_POST['memberName'];
	$counselType= $_POST['counselType'];
	$fromDt		= $_POST['fromDt'];
	$toDt		= $_POST['toDt'];
	$orderBy	= $_POST['orderBy'];

	if (!$fromDt) $fromDt = $toDt;
	if (!$toDt) $toDt = $fromDt;

	$itemCnt = 20;
	$pageCnt = 10;
	$page = $_POST['page'];

	if (Empty($page)){
		$page = 1;
	}

	if ($counselType == 'ALL' || $counselType == 'VISIT'){
		$query .= ($query ? ' UNION	ALL ' : '');
		$query .= '	SELECT	\'VISIT\' AS type
					,		visit_yymm as yymm
					,		visit_dt AS dt
					,		visit_seq AS seq
					,		visit_c_cd AS jumin
					,		visit_m_nm AS mem_nm
					FROM	counsel_client_visit
					WHERE	org_no	= \''.$orgNo.'\'
					AND		del_flag= \'N\'';
	}

	if ($counselType == 'ALL' || $counselType == 'PHONE'){
		$query .= ($query ? ' UNION	ALL ' : '');
		$query .= '	SELECT	\'PHONE\' AS type
					,		phone_yymm as yymm
					,		phone_dt AS dt
					,		phone_seq AS seq
					,		phone_c_cd AS jumin
					,		phone_m_nm AS mem_nm
					FROM	counsel_client_phone
					WHERE	org_no	= \''.$orgNo.'\'
					AND		del_flag= \'N\'';
	}

	if ($counselType == 'ALL' || $counselType == 'STRESS'){
		$query .= ($query ? ' UNION	ALL ' : '');
		$query .= '	SELECT	\'STRESS\' AS type
					,		stress_yymm as yymm
					,		stress_dt AS dt
					,		stress_seq AS seq
					,		stress_c_cd AS jumin
					,		stress_m_nm AS mem_nm
					FROM	counsel_client_stress
					WHERE	org_no	= \''.$orgNo.'\'
					AND		del_flag= \'N\'';
	}

	if ($counselType == 'ALL' || $counselType == 'CASE'){
		$query .= ($query ? ' UNION	ALL ' : '');
		$query .= '	SELECT	\'CASE\' AS type
					,		case_yymm as yymm
					,		case_dt AS dt
					,		case_seq AS seq
					,		case_c_cd AS jumin
					,		case_m_nm AS mem_nm
					FROM	counsel_client_case
					WHERE	org_no	= \''.$orgNo.'\'
					AND		del_flag= \'N\'';
	}

	if ($counselType == 'ALL' || $counselType == 'STATE'){
		$query .= ($query ? ' UNION	ALL ' : '');
		$query .= '	SELECT	\'STATE\' AS type
					,		left(replace(reg_dt,\'-\',\'\'), 6) as yymm
					,		reg_dt AS dt
					,       0
					,		jumin
					,		reg_nm AS mem_nm
					FROM	counsel_client_state
					WHERE	org_no = \''.$orgNo.'\'';
	}

	$sql = 'SELECT	DISTINCT
					counsel.type
			,	    counsel.yymm
			,		counsel.dt
			,		counsel.seq
			,		mst.jumin AS jumin
			,		mst.name AS name
			,		counsel.mem_nm
			FROM	('.$query.') AS counsel
			INNER	JOIN	(
						SELECT	DISTINCT m03_jumin AS jumin
						,		m03_name AS name
						FROM	m03sugupja
						WHERE	m03_ccode = \''.$orgNo.'\'
					) AS mst
					ON		mst.jumin = counsel.jumin';

	if ($clientName){
		$wsl .= ($wsl ? ' AND ' : ' WHERE ').'	mst.name like \''.$clientName.'%\'';
	}

	if ($memberName){
		$wsl .= ($wsl ? ' AND ' : ' WHERE ').'	counsel.mem_nm like \''.$memberName.'%\'';
	}

	if ($fromDt && $toDt){
		$wsl .= ($wsl ? ' AND ' : ' WHERE ').'	counsel.dt >= \''.$fromDt.'\'';
		$wsl .= '	AND	counsel.dt <= \''.$toDt.'\'';
	}

	$sql .= $wsl;
	$wsl  = '';
	
	$conn->query($sql);
	$conn->fetch();

	$totCnt = $conn->row_count();

	$conn->row_free();

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

	$sql = 'SELECT	DISTINCT
					counsel.type
			,		counsel.yymm
			,		counsel.dt
			,		counsel.seq
			,		mst.jumin AS jumin
			,		mst.name AS name
			,		counsel.mem_nm
			FROM	('.$query.') AS counsel
			INNER	JOIN	(
						SELECT	DISTINCT m03_jumin AS jumin
						,		m03_name AS name
						FROM	m03sugupja
						WHERE	m03_ccode = \''.$orgNo.'\'
					) AS mst
					ON		mst.jumin = counsel.jumin';

	if ($clientName){
		$wsl .= ($wsl ? ' AND ' : ' WHERE ').'	mst.name like \''.$clientName.'%\'';
	}

	if ($memberName){
		$wsl .= ($wsl ? ' AND ' : ' WHERE ').'	counsel.mem_nm like \''.$memberName.'%\'';
	}

	if ($fromDt && $toDt){
		$wsl .= ($wsl ? ' AND ' : ' WHERE ').'	counsel.dt >= \''.$fromDt.'\'';
		$wsl .= '	AND	counsel.dt <= \''.$toDt.'\'';
	}

	$sql .= $wsl;
	$wsl  = '';

	$sql .= '
			ORDER	BY dt '.$orderBy.'
			LIMIT	'.$pageCount.','.$itemCnt;
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($i % 2 == 0){
			$bgcolor = '#FFFFFF';
		}else{
			$bgcolor = '#EFEFEF';
		}
		
		$yymm = $row['yymm'];
		
		?>
		<tr style="cursor:default; background-color:<?=$bgcolor;?>;" onmouseover="this.style.backgroundColor='#D9E5FF';" onmouseout="this.style.backgroundColor='<?=$bgcolor;?>';">
			<td class="center"><?=$pageCount + ($i + 1);?></td>
			<td class="center"><?=$myF->dateStyle($row['dt'],'.');?></td>
			<td class="center"><div class="left nowrap" style="width:90px;"><?=$row['name'];?></div></td>
			<td class="center"><div class="left nowrap" style="width:90px;"><?=$row['mem_nm'];?></div></td>
			<td class="center"><div class="left nowrap" style="width:130px;"><?=$counselTypeStr[$row['type']];?></div></td>
			<td class="center last">
				<div class="left">
					<span class="btn_pack m"><button type="button" onclick="lfReg('<?=$row['type'];?>','<?=$orgNo?>','<?=$ed->en($row['jumin']);?>','<?=$yymm;?>','<?=number_format($row['seq']);?>', '<?=$row['dt'];?>');">수정</button></span>
					<span class="btn_pack m"><button type="button" onclick="lfShow('<?=$orgNo?>','<?=number_format($row['seq']);?>','<?=$ed->en($row['jumin']);?>','<?=strtolower($row['type']);?>','<?=$yymm;?>', '<?=$row['dt'];?>');">출력</button></span>
					<span class="btn_pack m"><button type="button" onclick="_client_proc_counsel_delete('<?=strtolower($row['type']);?>','<?=$yymm;?>','<?=number_format($row['seq']);?>','<?=$ed->en($row['jumin'])?>','<?=$row['dt'];?>', 'Y');">삭제</button></span>
				</div>
			</td>
		</tr><?
	}

	$conn->row_free();

	$paging = new YsPaging($params);
	$pageList = $paging->returnPaging();?>
	<tr>
		<td class="center bottom last" colspan="6"><?=$pageList;?></td>
	</tr><?

	include_once('../inc/_db_close.php');
?>