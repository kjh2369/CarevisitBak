<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once("../inc/_page_list.php");

	$CompleteGbn = $_POST['CompleteGbn'];
	$CancelGbn = $_POST['CancelGbn'];
	$orderBy = $_POST['orderBy'];
	$fromDt  = $_POST['fromDt'];
	$toDt    = $_POST['toDt'];

	$page	= $_POST['page'];

	$itemCnt = 10;
	$pageCnt = 10;

	if (!$page) $page = 1;

	
	$sl = 'SELECT	mr.seq
			,		mr.org_no
			FROM	medical_request AS mr
			INNER	JOIN (
						SELECT	DISTINCT
								m00_mcode AS org_no
						,	    m00_store_nm As org_nm
						,		m00_ctel AS tel
						,       mobile
						FROM	m00center
					    LEFT JOIN mst_manager as mg
						ON mg.org_no = m00_mcode 
					) AS mst
					ON		mst.org_no = mr.org_no';
	
	$sl .= ' WHERE del_flag = \'N\'';
	
	//완료구분
	if($CompleteGbn == 'Y'){
		$sl .= ' AND complete_yn = \'Y\'';
	}else if($CompleteGbn == 'N'){
		$sl .= ' AND complete_yn = \'N\'';
	}

	//취소구분
	if($CencelGbn == 'Y'){
		$sl .= ' AND cancel_yn = \'Y\'';
	}else if($CencelGbn == 'N'){
		$sl .= ' AND cancel_yn = \'N\'';
	}

	if ($orderBy == '1'){
		$sl .= ' ORDER	BY insert_dt DESC';
	}else if ($orderBy == '2'){
		$sl .= ' ORDER	BY org_nm';
	}
	$sql = 'SELECT	COUNT(DISTINCT org_no, seq)
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


	$sql = 'SELECT	mr.seq
			,		mr.org_no
			,	    mr.request_area as area
			,		left(mr.insert_dt, 10) as dt
			,	    mr.complete_yn as com_yn
			,		mst.org_nm
			,		mst.tel
			,	    mst.mobile
			FROM	medical_request AS mr
			INNER	JOIN (
						SELECT	DISTINCT
								m00_mcode AS org_no
						,	    m00_store_nm As org_nm
						,		m00_ctel AS tel
						,       mobile
						FROM	m00center
					    LEFT JOIN mst_manager as mg
						ON mg.org_no = m00_mcode 
					) AS mst
					ON		mst.org_no = mr.org_no';
	
	$sql .= ' WHERE del_flag = \'N\'';
	
	$sql .= '   AND insert_dt between \''.$fromDt.'\' and \''.$toDt.'\'';

	//완료구분
	if($CompleteGbn == 'Y'){
		$sql .= ' AND complete_yn = \'Y\'';
	}else if($CompleteGbn == 'N'){
		$sql .= ' AND complete_yn = \'N\'';
	}

	//취소구분
	if($CancelGbn == 'Y'){
		$sql .= ' AND cancel_yn = \'Y\'';
	}else if($CancelGbn == 'N'){
		$sql .= ' AND cancel_yn = \'N\'';
	}

	if ($orderBy == '1'){
		$sql .= ' ORDER	BY insert_dt DESC';
	}else if ($orderBy == '2'){
		$sql .= ' ORDER	BY org_nm';
	}
	
	$sql .= ' LIMIT	'.$pageCount.','.$itemCnt;

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	
	$org_cnt = 0;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		
		
		if($row['area'] == '01'){
			$area = '서울-서대문';
		}else if($row['area'] == '02'){
			$area = '서울-은평,강동';
		}else if($row['area'] == '03'){
			$area = '부산-동래';
		}else if($row['area'] == '04'){
			$area = '대구-달서';
		}else if($row['area'] == '05'){
			$area = '인천-부평';
		}else if($row['area'] == '06'){
			$area = '광주-서구';
		}else if($row['area'] == '07'){
			$area = '경기-일산';
		}else if($row['area'] == '08'){
			$area = '경기-광명';
		}else if($row['area'] == '09'){
			$area = '경남-창원';
		}else if($row['area'] == '10'){
			$area = '세종시-조치원읍';
		}else if($row['area'] == '11'){
			$area = '충북-청주';
		}else if($row['area'] == '12'){
			$area = '경남-함양';
		}else if($row['area'] == '13'){
			$area = '경북-김천,상주';
		}else if($row['area'] == '14'){
			$area = '강원-삼척';
		}else if($row['area'] == '15'){
			$area = '강원-원주,횡성';
		}else if($row['area'] == '16'){
			$area = '전남-목포,영암';
		}
		

		if ($i % 2 == 0){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		}?>
		<tr style="background-color:#<?=$bgcolor;?>;">
			<td class="center"><?=$i+1;?></td>
			<td class="center"><?=str_replace('-','.',$row['dt']);?></td>
			<td class="left"><div class="nowrap" style="width:150px;"><a href="#" onclick="lfReg('<?=$row['org_no'];?>','<?=$row['seq']?>');"><?=$row['org_nm'];?></a></div></td>
			<td class="left"><div class="nowrap" style="width:100px;"><?=$area;?></div></td>
			<td class="left "><div class="nowrap" style="width:80px;"><?=$myF->phoneStyle($row['tel'],'.');?></div></td>
			<td class="left "><div class="nowrap" style="width:120px;"><?=$myF->phoneStyle($row['phone'],'.');?></div></td>
			<td class="center"><?=($row['com_yn'] != 'N' ? 'Y' : '');?></td>
			<td class="left last">&nbsp;<!--span id="btnSave" class="btn_pack m" ><button onclick="lfDel('<?=$row['org_no']?>','<?=$row['seq']?>');">삭제</button></span-->&nbsp;</td>
			<input id="code_<?=$i?>" name="code" type="hidden" value="<?=$row['org_no'];?>">
			<input id="seq_<?=$i?>" name="seq" type="hidden"   value="<?=$row['seq'];?>">
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