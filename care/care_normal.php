<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$name = $_POST['txtName'];
	$kindGbn = $_POST['cboKindGbn'];
	$telno   = $_POST['txtTelno'];
	$addr    = $_POST['txtAddr'];
	$grdNm   = $_POST['txtGrdNm'];
	$reasonGbn = $_POST['reasonGbn'];

	$itemCnt = 20;
	$pageCnt = 10;
	$page = $_REQUEST['page'];

	if (Empty($page)){
		$page = 1;
	}

	$sql = 'SELECT	COUNT(*)
			FROM	care_client_normal
			WHERE	org_no		= \''.$code.'\'
			AND		normal_sr	= \''.$sr.'\'';

	if ($name) $sql .= ' AND name >= \''.$name.'\'';
	if ($kindGbn) $sql .= ' AND kind_gbn = \''.$kindGbn.'\'';

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
?>
<script type="text/javascript">
	function lfSearch(page){
		var f = document.f;

		if (!page) page = 1;

		f.page.value = page;
		f.action = '../care/care.php?sr=<?=$sr;?>&type=83';
		f.submit();
	}

	function lfReg(seq){
		if (!seq) seq = '';

		$('#seq').val(seq);

		var f = document.f;

		f.action = '../care/care.php?sr=<?=$sr;?>&type=84';
		f.submit();
	}
</script>
<div class="title title_border">일반접수조회(<?=$title;?>)</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="80px">
		<col width="50px" span="2">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">고객명</th>
			<td><input id="txtName" name="txtName" type="text" value="<?=$name;?>" style="width:100%;"></td>
			<th class="head">연락처</th>
			<td><input id="txtTelno" name="txtTelno" type="text" value="<?=$telno;?>" class="phone"></td>
			<th class="head">주소</th>
			<td ><input id="txtAddr" name="txtAddr" type="text" value="<?=$addr;?>" style="width:100%;"></td>
			<td class="left last" rowspan="2">
				<span class="btn_pack m"><a href="#" onclick="lfSearch(); return false;">조회</a></span>
				<span class="btn_pack m"><a href="#" onclick="lfReg(); return false;">등록</a></span>
			</td>
		</tr>
		<tr>
			<th class="head">보호자명</th>
			<td><input id="txtGrdNm" name="txtGrdNm" type="text" value="<?=$grdNm;?>" style="width:100%;"></td>
			<th class="head">유형</th>
			<td>
				<select id="cboKindGbn" name="cboKindGbn" style="width:auto;">
					<option value="" selected>전체</option>
					<option value="1" <?=$kindGbn == '1' ? 'selected' : '';?>>수급자</option>
					<option value="2" <?=$kindGbn == '2' ? 'selected' : '';?>>차상위</option>
					<option value="3" <?=$kindGbn == '3' ? 'selected' : '';?>>150%</option>
				</select>
			</td>
			<th class="head">관리구분</th>
			<td>
				<select id="reasonGbn" name="reasonGbn" style="width:auto;">
					<option value="">전체</option>
					<option value="01" <?=$reasonGbn == '01' ? 'selected' : '';?>>서비스대상등록</option>
					<option value="02" <?=$reasonGbn == '02' ? 'selected' : '';?>>타기관이전</option>
					<option value="03" <?=$reasonGbn == '03' ? 'selected' : '';?>>종결</option>
					<option value="99" <?=$reasonGbn == '99' ? 'selected' : '';?>>기타</option>
				</select>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<col width="200px">
		<col width="90px">
		<col width="150px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">대상자명</th>
			<th class="head">생년월일</th>
			<th class="head">주소</th>
			<th class="head">연락처</th>
			<th class="head">처리결과</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody><?
		$sql = 'SELECT	jumin
				,		normal_seq
				,		name
				,		addr
				,		addr_dtl
				,		phone
				,		mobile
				,		rst_reason
				,		grd_nm
				,		grd_telno
				FROM	care_client_normal
				WHERE	org_no		= \''.$code.'\'
				AND		normal_sr	= \''.$sr.'\'';

		if ($name) $sql .= ' AND name >= \''.$name.'\'';
		if ($addr) $sql .= ' AND CONCAT(addr,\'_\',addr_dtl) LIKE \'%'.$addr.'%\'';
		if ($telno) $sql .= 'AND CONCAT(phone,\'_\',mobile) LIKE \'%'.$telno.'%\'';
		if ($kindGbn) $sql .= ' AND kind_gbn = \''.$kindGbn.'\'';

		if ($reasonGbn) $sql .= ' AND rst_reason = \''.$reasonGbn.'\'';
		if ($grdNm) $sql .= ' AND grd_nm >= \''.$grdNm.'\'';


		$sql .= '
				ORDER	BY name
				LIMIT	'.$pageCount.','.$itemCnt;

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($row['rst_reason'] == '01'){
				$rstReason = '서비스대상등록';
			}else if ($row['rst_reason'] == '02'){
				$rstReason = '타기관이전';
			}else if ($row['rst_reason'] == '03'){
				$rstReason = '종결';
			}else if ($row['rst_reason'] == '99'){
				$rstReason = '기타';
			}else{
				$rstReason = '';
			}?>
			<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<td class="center"><?=$pageCount + ($i + 1);?></td>
				<td class="left"><a href="#" onclick="lfReg('<?=$row['normal_seq'];?>'); return false;"><?=($row['name'] ? $row['name'] : '이름없음');?></a></td>
				<td class="center"><?=$myF->issToBirthday($row['jumin'],'.');?></td>
				<td class="left"><div class="nowrap" style="width:98%;"><?=$row['addr'].' '.$row['addr_dtl'];?></div></td>
				<td class="center"><?=$myF->phoneStyle($row['phone'] ? $row['phone'] : $row['mobile'],'.');?></td>
				<td class="left"><?=$rstReason;?></td>
				<td class="left last"><?=$row['grd_nm'];?> / <?=$myF->phoneStyle($row['grd_telno'],'.');?></td>
			</tr><?
		}

		$conn->row_free();

		if ($rowCnt == 0){?>
			<tr>
				<td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}else{?>
			<tr>
				<td class="center bottom last" colspan="20"><?
					$paging = new YsPaging($params);
					$paging->printPaging();?>
				</td>
			</tr><?
		}?>
	</tbody>
</table>
<input id="page" name="page" type="hidden" value="<?=$page;?>">
<input id="seq" name="seq" type="hidden" value="">
<?
	include_once('../inc/_db_close.php');
?>