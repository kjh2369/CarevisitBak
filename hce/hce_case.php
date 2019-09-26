<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	사례접수일지
	 *********************************************************/

	$orgNo = $_SESSION['userCenterCode'];

	//접수방법
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CT\'
			AND		use_yn = \'Y\'';

	$rctGbn = $conn->_fetch_array($sql,'code');

	$itemCnt = 20;
	$pageCnt = 10;
	$page = $_REQUEST['page'];

	if (Empty($page)){
		$page = 1;
	}

	$sql = 'SELECT	COUNT(*)
			FROM	hce_elder
			WHERE	org_no = \''.$orgNo.'\'';
	$totCnt = $conn->get_data($sql);

	// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
	if ($totCnt < (IntVal($page) - 1) * $itemCnt){
		$page = 1;
	}

	$params = array(
		'curMethod'		=> 'post',
		'curPage'		=> 'javascript:_listMember',
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
?>
<script type="text/javascript">
	function lfNew(){
		location.href = '../hce/hce.php?type=2';
		return false;
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="right bottom last">
				<span class="btn_pack m"><span class="add"></span><button type="button" class="bold" onclick="return lfNew();">신규</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="60px">
		<col width="90px">
		<col width="40px">
		<col width="50px">
		<col width="70px">
		<col width="60px">
		<col width="90px">
		<col width="60px">
		<col width="40px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" colspan="3">대상자</th>
			<th class="head" rowspan="2">접수<br>방법</th>
			<th class="head" rowspan="2">접수<br>일자</th>
			<th class="head" colspan="2">의뢰인</th>
			<th class="head" rowspan="2">접수자</th>
			<th class="head" rowspan="2">종결<br>여부</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">성명</th>
			<th class="head">연락처</th>
			<th class="head">차수</th>
			<th class="head">성명</th>
			<th class="head">연락처</th>
		</tr>
	</thead>
	<tbody><?
		$sql = 'SELECT	rct.counsel_type
				,		rct.rcpt_dt
				,		rct.hce_ssn
				,		rct.hce_seq
				,		rct.hce_elder_nm
				,		rct.client_telno_loc
				,		rct.reqor_nm
				,		rct.reqor_telno_loc
				,		rct.rcver_nm
				,		mst.IPIN_hcelder AS IPIN
				FROM	hce_elder AS mst
				INNER	JOIN hce_receipt AS rct
						ON rct.org_no		= mst.org_no
						AND rct.org_type	= mst.org_type
						AND rct.hce_ssn		= mst.hce_ssn
						AND rct.hce_seq		= mst.hce_seq
				WHERE	mst.org_no		= \''.$orgNo.'\'
				AND		mst.del_flag	= \'N\'
				ORDER	BY hce_elder_nm, rcpt_dt
				LIMIT	'.$pageCount.','.$itemCnt;

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = 100;//$conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			//$row = $conn->select_row($i);

			if ($row['end_flag'] == 'Y'){
				$endStr = '<span style="color:red;">종결</span>';
			}else{
				$endStr = '미결';
			}?>
			<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<td class="center"><?=$pageCount + ($i + 1);?></td>
				<td class="center"><a href="#" onclick="lfSetTarget('<?=$ed->en($row['hce_ssn']);?>','<?=$row['hce_seq'];?>'); return false;"><?=$row['hce_elder_nm'];?></a></td>
				<td class="center"><?=$myF->phoneStyle($row['client_telno_loc'],'.');?></td>
				<td class="center"><?=$row['hce_seq'];?></td>
				<td class="center"><?=$rctGbn[$row['counsel_type']]['name'];?></td>
				<td class="center"><?=$myF->dateStyle($row['rcpt_dt'],'.');?></td>
				<td class="center"><?=$row['reqor_nm'];?></td>
				<td class="center"><?=$myF->phoneStyle($row['reqor_telno_loc'],'.');?></td>
				<td class="center"><?=$row['rcver_nm'];?></td>
				<td class="center"><?=$endStr;?></td>
				<td class="center last"></td>
			</tr><?
		}

		$conn->row_free();

		if ($rowCnt == 0){?>
			<tr>
				<td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}?>
	</tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" colspan="20"><?
				if ($rowCnt > 0){
					$paging = new YsPaging($params);
					$paging->printPaging();
				}?>
			</td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>