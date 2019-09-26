<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);
	
	if (!$title) exit;

	$name	= $_POST['txtName'];
	$telno	= str_replace('-','',$_POST['txtTelno']);
	$addr	= $_POST['txtAddr'];
	$grdNm	= $_POST['txtGrdNm'];
	$mpGbn	= $_POST['cboMPGbn'];
	$statGbn= $_POST['cboStatGbn'];

	//재가지원 고객정보
	$sql = 'SELECT	jumin
			,		care_org_no
			,		care_org_nm
			,		care_no
			,		care_pic_nm
			,		care_telno
			,		care_lvl
			FROM	client_his_care
			WHERE	org_no = \''.$code.'\'
			ORDER	BY jumin,seq';

	$arrCare = $conn->_fetch_array($sql,'jumin');

	$careLvl = Array('1'=>'1등급', '2'=>'2등급', '3'=>'3등급', '4'=>'4등급', '5'=>'5등급', '7'=>'등급 외 A,B', '9'=>'일반');

	$itemCnt = 20;
	$pageCnt = 10;
	$page = $_REQUEST['page'];

	if (Empty($page)){
		$page = 1;
	}

	/*
		$sql = 'SELECT	COUNT(DISTINCT m03_jumin)
				FROM	m03sugupja
				INNER	JOIN client_his_svc
						ON		org_no	= m03_ccode
						AND		svc_cd	= \''.$sr.'\'
						AND		jumin	= m03_jumin
				WHERE	m03_ccode = \''.$code.'\'
				AND		m03_mkind = \'6\'';
	 */

	$sql = 'SELECT	COUNT(DISTINCT m03_jumin) AS cnt
			FROM	m03sugupja
			INNER	JOIN	client_his_svc AS svc
					ON		svc.org_no	= m03_ccode
					AND		svc.svc_cd	= \''.$sr.'\'
					AND		svc.jumin	= m03_jumin
			LEFT	JOIN	client_his_svc AS his
					ON		his.org_no	 = svc.org_no
					AND		his.svc_cd	 = svc.svc_cd
					AND		his.jumin	 = svc.jumin
					AND		his.from_dt <= NOW()
					AND		his.to_dt	>= NOW()
			WHERE	m03_ccode	= \''.$code.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_del_yn	= \'N\'';

	if ($name){
		$sql .= '
			AND		m03_name >= \''.$name.'\'';
	}

	if ($telno){
		$sql .= '
			AND		CONCAT(m03_tel,\'_\',m03_hp) LIKE \'%'.$telno.'%\'';
	}

	if ($addr){
		$sql .= '
			AND		CONCAT(m03_juso1,\'_\',m03_juso2) LIKE \'%'.$addr.'%\'';
	}

	if ($grdNm){
		$sql .= '
			AND		m03_yboho_name >= \''.$grdNm.'\'';
	}

	if ($mpGbn){
		$sql .= '
			AND		IFNULL(his.mp_gbn,\'N\') = \''.$mpGbn.'\'';
	}

	if ($statGbn){
		$sql .= '
			AND		IFNULL(his.svc_stat,\'9\') = \''.$statGbn.'\'';
	}

	$totCnt = $conn->get_data($sql);


	//재가지원 일반접수
	if ($debug && $sr == 'S'){
		//일반접수 수
	}


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
		f.action = '../care/care.php?sr=<?=$sr;?>&type=81';
		f.submit();
	}

	function lfReg(jumin){
		if (!jumin) jumin = '';

		$('#jumin').val(jumin);

		var f = document.f;

		f.action = '../care/care.php?sr=<?=$sr;?>&type=82';
		f.submit();
	}

	function lfPrint(){
		var f = document.f;

		f.action = '../care/care_client_excel.php?sr=<?=$sr;?>';
		f.submit();
	}
</script>
<div class="title title_border">대상자조회(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="90px">
		<col width="60px">
		<col width="90px">
		<col width="60px">
		<col width="50px">
		<col width="60px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">고객명</th>
			<td><input id="txtName" name="txtName" type="text" value="<?=$name;?>" style="width:100%;" onkeydown="if(event.keyCode==13){lfSearch();}"></td>
			<th class="head">주소</th>
			<td colspan="5"><input id="txtAddr" name="txtAddr" type="text" value="<?=$addr;?>" style="width:100%;"></td>
			<td class="left last" rowspan="2">
				<span class="btn_pack m"><a href="#" onclick="lfSearch(); return false;">조회</a></span>
				<span class="btn_pack m"><span class="add"></span><a href="#" onclick="lfReg(); return false;">등록</a></span>
				<span class="btn_pack m"><span class="excel"></span><button onclick="lfPrint();">출력</button></span>
			</td>
		</tr>
		<tr>
			<th class="head">연락처</th>
			<td><input id="txtTelno" name="txtTelno" type="text" value="<?=$telno;?>" class="phone"></td>
			<th class="head">보호자명</th>
			<td><input id="txtGrdNm" name="txtGrdNm" type="text" value="<?=$grdNm;?>" style="width:100%;"></td>
			<th class="head">관리구분</th>
			<td>
				<select name="cboMPGbn" style="width:auto;">
					<option value="">전체</option>
					<option value="Y" <?=$mpGbn == 'Y' ? 'selected' : '';?>>중점관리</option>
					<option value="N" <?=$mpGbn == 'N' ? 'selected' : '';?>>일반</option>
				</select>
			</td>
			<th class="head">상태구분</th>
			<td>
				<select name="cboStatGbn" style="width:auto;">
					<option value="">전체</option>
					<option value="1" <?=$statGbn == '1' ? 'selected' : '';?>>사용</option>
					<option value="9" <?=$statGbn == '9' ? 'selected' : '';?>>중지</option>
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
		<col width="80px">
		<col width="300px">
		<col width="90px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">대상자명</th>
			<th class="head">생년월일</th>
			<th class="head">등급</th>
			<th class="head">주소</th>
			<th class="head">연락처</th>
			<th class="head">관리구분</th>
			<th class="head">상태구분</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody><?
		$sql = 'SELECT	mst.m03_jumin AS jumin
				,		MAX(svc.seq) AS seq
				,		mst.m03_name AS name
				,		mst.m03_juso1 AS addr
				,		mst.m03_juso2 AS addr_dtl
				,		mst.m03_tel AS phone
				,		mst.m03_hp AS mobile
				,		IFNULL(jumin.jumin, m03_jumin) AS real_jumin
				,		IFNULL(his.mp_gbn,\'N\') AS mp
				,		IFNULL(his.svc_stat,\'9\') AS stat
				FROM	m03sugupja AS mst
				INNER	JOIN	client_his_svc AS svc
						ON		svc.org_no = mst.m03_ccode
						AND		svc.jumin = mst.m03_jumin
						AND		svc.svc_cd = \''.$sr.'\'
				LEFT	JOIN	mst_jumin AS jumin
						ON		jumin.org_no= m03_ccode
						AND		jumin.gbn	= \'1\'
						AND		jumin.code	= m03_jumin
				LEFT	JOIN	client_his_svc AS his
						ON		his.org_no	 = mst.m03_ccode
						AND		his.jumin	 = mst.m03_jumin
						AND		his.svc_cd	 = \''.$sr.'\'
						AND		his.from_dt <= NOW()
						AND		his.to_dt	>= NOW()
				WHERE	mst.m03_ccode = \''.$code.'\'
				AND		mst.m03_mkind = \'6\'
				AND		mst.m03_del_yn = \'N\'';

		if ($name){
			$sql .= '
				AND		mst.m03_name >= \''.$name.'\'';
		}

		if ($telno){
			$sql .= '
				AND		CONCAT(mst.m03_tel,\'_\',mst.m03_hp) LIKE \'%'.$telno.'%\'';
		}

		if ($addr){
			$sql .= '
				AND		CONCAT(mst.m03_juso1,\'_\',mst.m03_juso2) LIKE \'%'.$addr.'%\'';
		}

		if ($grdNm){
			$sql .= '
				AND		mst.m03_yboho_name >= \''.$grdNm.'\'';
		}

		if ($mpGbn){
			$sql .= '
				AND		IFNULL(his.mp_gbn,\'N\') = \''.$mpGbn.'\'';
		}

		if ($statGbn){
			$sql .= '
				AND		IFNULL(his.svc_stat,\'9\') = \''.$statGbn.'\'';
		}

		$sql .= '
				GROUP	BY mst.m03_jumin
				ORDER	BY name
				LIMIT	'.$pageCount.','.$itemCnt;
		
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($row['stat'] == '1'){
				$row['stat'] = '사용';
			}else if ($row['stat'] == '7'){
				$row['stat'] = '<span style="color:BLUE;">미등록</span>';
			}else if ($row['stat'] == '9'){
				$row['stat'] = '<span style="color:RED;">중지</span>';
			}?>
			<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<td class="center"><?=$pageCount + ($i + 1);?></td>
				<td class="left"><a href="#" onclick="lfReg('<?=$ed->en($row['jumin']);?>'); return false;"><?=($row['name'] ? $row['name'] : '이름없음');?></a></td>
				<td class="center"><?=$myF->issToBirthday($row['real_jumin'],'.');?></td>
				<td class="center"><div class="left"><?=$careLvl[$arrCare[$row['jumin']]['care_lvl']];?></div></td>
				<td class="left"><div class="nowrap" style="width:300px;"><?=$row['addr'].' '.$row['addr_dtl'];?></div></td>
				<td class="center"><?=$myF->phoneStyle($row['phone'] ? $row['phone'] : $row['mobile'],'.');?></td>
				<td class="center"><?=$row['mp'] == 'Y' ? '중점관리' : '일반';?></td>
				<td class="center"><?=$row['stat'];?></td>
				<td class="left last"></td>
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
<input id="jumin" name="jumin" type="hidden" value="">
<?
	include_once('../inc/_db_close.php');
?>