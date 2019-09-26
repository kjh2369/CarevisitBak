<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once("../inc/_page_list.php");

	$orgNo		= $_POST['orgNo'];
	$orgNm		= $_POST['orgNm'];
	$orgMdNm    = $_POST['orgMdNm'];
	$fromDt		= $_POST['fromDt'];
	$toDt		= $_POST['toDt'];
	
	$page	= $_POST['page'];

	$itemCnt = 10;
	$pageCnt = 10;

	if (!$page) $page = 1;

	
	$sl = 'SELECT	mc.org_no
		   ,		mst.org_nm
		   ,		mc.medical_org_no
		   ,		mc.from_dt
			FROM	medical_connect AS mc
			INNER	JOIN (
						SELECT	DISTINCT
								m00_mcode AS org_no
						,	    m00_store_nm As org_nm
						FROM	m00center 
					) AS mst
					ON		mst.org_no = mc.org_no';
	
	$sl .= ' WHERE del_flag = \'N\'';
	
	if($orgNo)		$sl .= '   AND mc.org_no >= \''.$orgNo.'\'';
	if($orgNm)		$sl .= '   AND mst.org_nm like \'%'.$orgNm.'%\'';
	if($orgMdNm)	$sl .= '   AND mc.medical_org_name like \'%'.$orgMdNm.'%\'';


	$sql = 'SELECT	COUNT(DISTINCT org_no, medical_org_no, from_dt)
			FROM	('.$sl.') AS a';
	
	$totCnt = $conn -> get_data($sql);

	
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


	$sql = 'SELECT	mc.org_no
			,	    mc.medical_org_no
			,		mst.org_nm
			,	    mc.medical_org_name as md_org_nm
			,		mc.from_dt
			,		mc.to_dt
			FROM	medical_connect AS mc
			INNER	JOIN (
						SELECT	DISTINCT
								m00_mcode AS org_no
						,	    m00_store_nm As org_nm
						FROM	m00center
					) AS mst
					ON		mst.org_no = mc.org_no';
	
	$sql .= ' WHERE del_flag = \'N\'';
	
	if($orgNo)		$sql .= '   AND mc.org_no >= \''.$orgNo.'\'';
	if($orgNm)		$sql .= '   AND mst.org_nm like \'%'.$orgNm.'%\'';
	if($orgMdNm)	$sql .= '   AND mc.medical_org_name like \'%'.$orgMdNm.'%\'';
	
	$sql .= ' LIMIT	'.$pageCount.','.$itemCnt;
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	
	$org_cnt = 0;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		
		
		
		if ($i % 2 == 0){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		}?>
		<tr style="background-color:#<?=$bgcolor;?>;">
			<td class="center"><?=$i+1;?></td>
			<td class="center"><?=$row['org_no'];?></td>
			<td class="left"><?=$row['org_nm'];?></td>
			<td class="left"><div class="nowrap" style="width:150px;"><?=$row['md_org_nm'];?></div></td>
			<td class="center"><div class="nowrap" style="width:200px;"><?=$myF->dateStyle($row['from_dt'],'.');?> ~ <?=$myF->dateStyle($row['to_dt'],'.');?> </div></td>
			<td class="left last">&nbsp;<span id="btnSave" class="btn_pack m" ><button onclick="lfReg('<?=$row['org_no']?>','<?=$row['medical_org_no']?>','<?=$row['from_dt']?>');">수정</button></span> <span id="btnSave" class="btn_pack m" ><button onclick="lfDel('<?=$row['org_no']?>','<?=$row['medical_org_no']?>','<?=$row['from_dt']?>');">삭제</button></span>&nbsp;</td>
		</tr><?

		
	}

	$conn->row_free();

	$paging = new YsPaging($params);
	$pageList = $paging->returnPaging();

	?>

<?
	unset($tot_pay);

	include_once('../inc/_db_close.php');
?>
<script type="text/javascript">
	$('#ID_ROW_PAGELIST').html('<?=$pageList;?>');
</script>