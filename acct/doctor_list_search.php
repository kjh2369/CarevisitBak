<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_nhcs_db.php');
	
	$doctorNm	= $_POST['doctorNm'];
	$licenceNo	= $_POST['licenceNo'];

	$page	= $_POST['page'];

	$itemCnt = 10;
	$pageCnt = 10;

	if (!$page) $page = 1;

	
	
	$sql = 'SELECT	COUNT(*)
			FROM	doctor
			WHERE   del_flag = \'N\'';
	
	if($orgNo) $sql .= ' AND doctor_licence_no >= \''.$licenceNo.'\'';

	if($orgNm) $sql .= ' AND doctor_name >= \''.$doctorNm.'\'';

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
			FROM	doctor
			WHERE   del_flag = \'N\'';
	
	if($orgNo) $sql .= ' AND doctor_licence_no >= \''.$licenceNo.'\'';

	if($orgNm) $sql .= ' AND doctor_name >= \''.$doctorNm.'\'';
	
	
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
			<td class="left"><a href="#" onclick="lfReg('<?=$ed->en($row['doctor_licence_no']);?>');"><?=$row['doctor_name'];?></a></td>
			<td class="left"><?=$row['doctor_licence_no'];?></td>
			<td class="left "><?=$myF->phoneStyle($row['telno_loc'],'.');?></td>
			<td class="left "><?=$myF->phoneStyle($row['telno_mob'],'.');?></td>
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