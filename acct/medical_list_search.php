<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_nhcs_db.php');
	
	$orgNo	= $_POST['orgNo'];
	$orgNm	= $_POST['orgNm'];
	$orgCeo = $_POST['orgCeo'];

	$page	= $_POST['page'];

	$itemCnt = 10;
	$pageCnt = 10;

	if (!$page) $page = 1;

	
	
	$sql = 'SELECT	COUNT(*)
			FROM	medical_org
			WHERE   del_flag = \'N\'';
	
	if($orgNo) $sql .= ' AND medical_org_no >= \''.$orgNo.'\'';

	if($orgNm) $sql .= ' AND medical_org_name >= \''.$orgNm.'\'';

	if($orgCeo) $sql .= ' AND ceo_name >= \''.$orgCeo.'\'';

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

	
	$sql = 'SELECT	*
			FROM	medical_org
			WHERE   del_flag = \'N\'';
	
	if($orgNo) $sql .= ' AND medical_org_no >= \''.$orgNo.'\'';

	if($orgNm) $sql .= ' AND medical_org_name >= \''.$orgNm.'\'';

	if($orgCeo) $sql .= ' AND ceo_name >= \''.$orgCeo.'\'';
	
	
	$sql .= ' LIMIT	'.$pageCount.','.$itemCnt;

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	
	$org_cnt = 0;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		
		/*
		if ($i % 2 == 0){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		}
		*/
		?>
		<tr style="background-color:#<?=$bgcolor;?>;">
			<td class="center"><?=$i+1;?></td>
			<td class="left"><?=$row['medical_org_no'];?></td>
			<td class="left"><div class="nowrap" style="width:150px;"><a href="#" onclick="lfReg('<?=$ed->en($row['medical_org_no']);?>');"><?=$row['medical_org_name'];?></a></div></td>
			<td class="left"><?=$row['ceo_name'];?></td>
			<td class="left "><?=$myF->phoneStyle($row['telno_loc_org'],'.');?></td>
			<td class="left "><?=$myF->phoneStyle($row['telno_mob_ceo'],'.');?></td>
			<td class="left last">&nbsp;<!--span id="btnSave" class="btn_pack m" ><button onclick="lfDel('<?=$row['org_no']?>','<?=$row['seq']?>');">삭제</button></span-->&nbsp;</td>
		</tr><?

		
	}

	$conn->row_free();

	$paging = new YsPaging($params);
	$pageList = $paging->returnPaging();

	?>

<?
	include_once('../inc/_db_close.php');
?>
<script type="text/javascript">
	$('#ID_ROW_PAGELIST').html('<?=$pageList;?>');
</script>