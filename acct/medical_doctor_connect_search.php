<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once("../inc/_page_list.php");
	include_once("../inc/_nhcs_db.php");

	$licenceNo	= $_POST['licenceNo'];
	$doctorNm	= $_POST['doctorNm'];
	$orgMdNm    = $_POST['orgMdNm'];
	
	$page	= $_POST['page'];

	$itemCnt = 10;
	$pageCnt = 10;

	if (!$page) $page = 1;

	
	$sl = 'SELECT	md.doctor_licence_no
		   ,		md.medical_org_no
		   ,		md.cntrct_dt
			FROM	medical_org_doctor AS md
			LEFT	JOIN (
						SELECT	DISTINCT
								doctor_licence_no AS licence_no
						,	    doctor_name As doctor_nm
						FROM	doctor 
					) AS doc
					ON	doc.licence_no = md.doctor_licence_no 
			LEFT	JOIN (
						SELECT	DISTINCT
								medical_org_no AS org_no
						,	    medical_org_name As org_nm
						FROM	medical_org 
					) AS mo
					ON		mo.org_no = md.medical_org_no';
	
	$sl .= ' WHERE del_flag = \'N\'';
	/*
	if($licenceNo)		$sl .= '   AND doctor_licence_no >= \''.$licenceNo.'\'';
	if($doctorNm)		$sl .= '   AND doc.doctor_nm like \'%'.$doctorNm.'%\'';
	if($orgMdNm)		$sl .= '   AND mo.org_nm like \'%'.$orgMdNm.'%\'';
	*/

	$sql = 'SELECT	COUNT(DISTINCT doctor_licence_no, medical_org_no, cntrct_dt)
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


	$sql = 'SELECT	mo.org_no
		   ,		mo.org_nm
		   ,		doc.licence_no
		   ,		doc.doctor_nm
		   ,		md.cntrct_dt
		   ,		md.retire_dt
			FROM	medical_org_doctor AS md
			LEFT	JOIN (
						SELECT	DISTINCT
								doctor_licence_no AS licence_no
						,	    doctor_name As doctor_nm
						FROM	doctor
						WHERE   del_flag = \'N\'
					) AS doc
					ON	doc.licence_no = md.doctor_licence_no 
			LEFT	JOIN (
						SELECT	DISTINCT
								medical_org_no AS org_no
						,	    medical_org_name As org_nm
						FROM	medical_org 
						WHERE   del_flag = \'N\'
					) AS mo
					ON		mo.org_no = md.medical_org_no';
	
	$sql .= ' WHERE del_flag = \'N\'';
	
	if($orgNo)		$sql .= '   AND mc.org_no >= \''.$orgNo.'\'';
	if($orgNm)		$sql .= '   AND mst.org_nm like \'%'.$orgNm.'%\'';
	if($orgMdNn)	$sql .= '   AND mc.medical_org_name like \'%'.$orgMdNn.'%\'';
	
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
			<td class="left"><?=$row['licence_no'];?></td>
			<td class="left"><?=$row['doctor_nm'];?></td>
			<td class="center"><?=$row['org_no'];?></td>
			<td class="left"><?=$row['org_nm'];?></td>
			<td class="center"><div class="nowrap" style="width:200px;"><?=$myF->dateStyle($row['cntrct_dt'],'.');?> ~ <?=$myF->dateStyle($row['retire_dt'],'.');?> </div></td>
			<td class="left last">&nbsp;<span id="btnSave" class="btn_pack m" ><button onclick="lfReg('<?=$ed->en($row['licence_no']);?>','<?=$ed->en($row['org_no']);?>','<?=$row['cntrct_dt']?>');">수정</button></span> <span id="btnSave" class="btn_pack m" ><button onclick="lfDel('<?=$row['licence_no']?>','<?=$row['org_no']?>','<?=$row['cntrct_dt']?>');">삭제</button></span>&nbsp;</td>
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