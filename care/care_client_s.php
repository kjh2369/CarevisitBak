<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);
	
	

	$name	= $_POST['txtName'];
	$telno	= str_replace('-','',$_POST['txtTelno']);
	$addr	= $_POST['txtAddr'];
	$addrMent = $_POST['txtAddrMent'];
	$grdNm	= $_POST['txtGrdNm'];
	$mpGbn	= $_POST['cboMPGbn'];
	$statGbn= $_POST['cboStatGbn'] != '' ? $_POST['cboStatGbn'] : '1';
	$gender = $_POST['optGender'];
	$income = $_POST['cboIncome'];
	$generation = $_POST['cboGeneration'];
	
	$rcptFrom = $_POST['txtRcptFrom'];
	$rcptTo = $_POST['txtRcptTo'];
	
	
	//재가지원 고객정보
	$sql = 'SELECT	jumin
			,		care_org_no
			,		care_org_nm
			,		care_no
			,		care_pic_nm
			,		care_telno
			FROM	client_his_care
			WHERE	org_no = \''.$code.'\'
			ORDER	BY jumin,seq';

	$arrCare = $conn->_fetch_array($sql,'jumin');
	
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'IG\'
			AND		use_yn	= \'Y\'';
	$inGbn = $conn->_fetch_array($sql,'code');

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'GR\'
			AND		use_yn	= \'Y\'';
	$geGbn = $conn->_fetch_array($sql,'code');

	/** 기본쿼리 *******************************************************/
		$bql = '/* 대상자 */
				SELECT	case when IFNULL(c.mp_gbn,\'N\') = \'Y\' then 1 else 2 end as mgbn
				,       0 AS gbn
				,		m03_jumin AS jumin
				,		m03_name AS name
				,		m03_tel AS phone
				,		m03_hp AS mobile
				,		m03_post_no AS postno
				,		m03_juso1 AS addr
				,		m03_juso2 AS addr_dtl
				,		m03_yboho_name AS grd_nm
				,		MAX(b.seq) AS seq
				,		IFNULL(c.mp_gbn,\'N\') AS mp_gbn
				,		IFNULL(c.svc_stat,\'9\') AS stat
				,		IFNULL(d.jumin, m03_jumin) AS real_jumin
				,		e.addr_ment
				,		e.reg_dt
				,       f.income_gbn
				,		f.income_other
				,		f.generation_gbn
				,		f.generation_other
				FROM	m03sugupja
				INNER	JOIN	client_his_svc AS b
						ON		b.org_no = m03_ccode
						AND		b.svc_cd = \''.$sr.'\'
						AND		b.jumin	 = m03_jumin
				LEFT	JOIN	client_his_svc AS c
						ON		c.org_no	 = m03_ccode
						AND		c.svc_cd	 = \''.$sr.'\'
						AND		c.jumin		 = m03_jumin
						AND		DATE_FORMAT(c.from_dt, \'%Y%m%d\')	<= DATE_FORMAT(NOW(),\'%Y%m%d\')
						AND		DATE_FORMAT(c.to_dt, \'%Y%m%d\')	>= DATE_FORMAT(NOW(),\'%Y%m%d\')
				LEFT	JOIN	mst_jumin AS d
						ON		d.org_no = m03_ccode
						AND		d.gbn	 = \'1\'
						AND		d.code	 = m03_jumin
				LEFT	JOIN	client_option AS e
						ON		e.org_no	= m03_ccode
						AND		e.jumin		= m03_jumin
				LEFT JOIN ( SELECT   IPIN, income_gbn, income_other, generation_gbn, generation_other
							FROM     hce_interview              
							WHERE    org_no = \''.$code.'\'
							AND      org_type = \''.$sr.'\'                  
							ORDER BY rcpt_seq desc ) as f
				ON		f.IPIN	= m03_key
				WHERE	m03_ccode	= \''.$code.'\'
				AND		m03_mkind	= \'6\'
				AND		m03_del_yn	= \'N\'
				GROUP	BY m03_jumin

				UNION	ALL

				/* 일반접수 */
				SELECT	3 as mgbn
				,		a.normal_seq
				,		a.jumin
				,		a.name
				,		a.phone
				,		a.mobile
				,		a.postno
				,		a.addr
				,		a.addr_dtl
				,		a.grd_nm
				,		NULL, NULL, NULL, a.jumin
				,		a.addr_ment
				,		a.reg_dt
				,		f.income_gbn
				,		f.income_other
				,		f.generation_gbn
				,		f.generation_other
				FROM	care_client_normal AS a
				LEFT	JOIN	m03sugupja
						ON		m03_ccode = a.org_no
						AND		m03_mkind = \'6\'
						AND		m03_jumin = a.jumin
				LEFT JOIN ( SELECT   IPIN, income_gbn, income_other, generation_gbn, generation_other
							FROM     hce_interview              
							WHERE    org_no = \''.$code.'\'
							AND      org_type = \''.$sr.'\'      
						    AND      rcpt_seq= \'-1\') as f
				ON		f.IPIN	= m03_key
				WHERE	a.org_no	= \''.$code.'\'
				AND		a.normal_sr	= \''.$sr.'\'
				AND		a.del_flag	= \'N\'
				AND		a.link_IPIN	IS NULL
				AND		m03_jumin	IS NULL';

	
	$itemCnt = 20;
	$pageCnt = 10;
	$page = $_REQUEST['page'];

	if (Empty($page)){
		$page = 1;
	}

	$wsl = '';

	if ($name){
		$wsl .= '
			AND		name >= \''.$name.'\'';
	}

	if ($telno){
		$wsl .= '
			AND		CONCAT(phone,\'_\',mobile) LIKE \'%'.$telno.'%\'';
	}

	if ($addr){
		$wsl .= '
			AND		CONCAT(addr,\'_\',addr_dtl) LIKE \'%'.$addr.'%\'';
	}

	if ($grdNm){
		$wsl .= '
			AND		grd_nm >= \''.$grdNm.'\'';
	}
	
	if ($mpGbn && ($mpGbn != 'X')){
		$wsl .= '
			AND		IFNULL(mp_gbn,\'X\') = \''.$mpGbn.'\'';
	}

	
	if ($statGbn != 'all'){
		$wsl .= '
			AND		IFNULL(stat,\'X\') = \''.$statGbn.'\'';
	}

	
	if ($addrMent){
		$wsl .= '
			AND		addr_ment LIKE \'%'.$addrMent.'%\'';
	}

	if ($rcptFrom && $rcptTo){
		$wsl .= '
			AND		reg_dt BETWEEN \''.$rcptFrom.'\' AND \''.$rcptTo.'\'';
	}
	
	if ($gender){
		$wsl .= ' AND substr(real_jumin,7,1) = \''.$gender.'\'';
	}

	if ($income){
		$wsl .= ' AND income_gbn = \''.$income.'\'';
	}

	if ($generation){
		$wsl .= ' AND generation_gbn = \''.$generation.'\'';
	}

	$sql = 'SELECT	COUNT(DISTINCT jumin) AS cnt
			FROM	('.$bql.') AS a
			WHERE	gbn IS NOT NULL'.$wsl;
	
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
		f.action = '../care/care.php?sr=<?=$sr;?>&type=81';
		f.submit();
	}

	function lfReg(jumin,gbn){
		if (!jumin) jumin = '';

		$('#jumin').val(jumin);
		$('#gbn').val(gbn);

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
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">고객명</th>
			<td><input id="txtName" name="txtName" type="text" value="<?=$name;?>" style="width:100%;" onkeydown="if(event.keyCode==13){lfSearch();}"></td>
			<th class="head">주소</th>
			<td colspan="2"><input id="txtAddr" name="txtAddr" type="text" value="<?=$addr;?>" style="width:100%;"></td>
			<th class="head">관리주소</th>
			<td colspan="2"><input id="txtAddrMent" name="txtAddrMent" type="text" value="<?=$addrMent;?>" style="width:100%;"></td>
			<th class="head">접수일자</th>
			<td class="last" colspan="2">
				<input id="txtRcptFrom" name="txtRcptFrom" type="text" value="<?=$rcptFrom;?>" class="date"> ~
				<input id="txtRcptTo" name="txtRcptTo" type="text" value="<?=$rcptTo;?>" class="date">
			</td>
		</tr>
		<tr>
			<th class="head">보호자명</th>
			<td ><input id="txtGrdNm" name="txtGrdNm" type="text" value="<?=$grdNm;?>" style="width:100%;" ></td>
			<th class="head">연락처</th>
			<td colspan="2"><input id="txtTelno" name="txtTelno" type="text" value="<?=$telno;?>" class="phone"></td>
			
			<th class="head">관리구분</th>
			<td colspan="2">
				<select name="cboMPGbn" style="width:auto;">
					<option value="">전체</option>
					<option value="Y" <?=$mpGbn == 'Y' ? 'selected' : '';?>>중점관리</option>
					<option value="N" <?=$mpGbn == 'N' ? 'selected' : '';?>>일반</option>
					<option value="X" <?=$mpGbn == 'X' ? 'selected' : '';?>>일반접수</option>
				</select>
			</td>
			<th class="head">경제상항</th>
			<td>
				<select name="cboIncome" style="width:75px;"><?
					$sql = 'SELECT	code,name
							FROM	hce_gbn
							WHERE	type	= \'IG\'
							AND		use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();
					
					echo '<option value="">전체</option>';

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);
					
						echo '<option value="'.$row['code'].'" '.($row['code'] == $income ? 'selected' : '').'>'.$row['name'].'</option>';	
					}

					$conn->row_free();
				?>	
				</select>
			</td>
			<td class="left last" colspan="4" rowspan="2">
				<span class="btn_pack m"><a href="#" onclick="lfSearch(); return false;">조회</a></span>
				<span class="btn_pack m"><span class="add"></span><a href="#" onclick="lfReg(); return false;">등록</a></span>
				<span class="btn_pack m"><span class="excel"></span><button onclick="lfPrint();">출력</button></span>
			</td>
		</tr>
		<tr>
			<th class="head">상태구분</th>
			<td>
				<select name="cboStatGbn" style="width:auto;">
					<option value="all" <?=$statGbn == 'all' ? 'selected' : '';?> >전체</option>
					<option value="1" <?=$statGbn == '1' ? 'selected' : '';?>>사용</option>
					<option value="9" <?=$statGbn == '9' ? 'selected' : '';?>>중지</option>
				</select>
			</td>
			<th class="head">성별</th>
			<td colspan="5">
				<input name="optGender" type="radio" class="radio" value=""  checked />전체
				<input name="optGender" type="radio" class="radio" value="1" <?=$gender == '1' ? 'checked' : '';?> />남
				<input name="optGender" type="radio" class="radio" value="2" <?=$gender == '2' ? 'checked' : '';?> />여
				
				<!--select name="cboGender" style="width:auto;">
					<option value="">전체</option>
					<option value="1" <?=$gender == '1' ? 'selected' : '';?>>남</option>
					<option value="2" <?=$gender == '2' ? 'selected' : '';?>>여</option>
				</select-->
			</td>
			<th class="head">세대유형</th>
			<td>
				<select name="cboGeneration" style="width:auto;"><?
					$sql = 'SELECT	code,name
							FROM	hce_gbn
							WHERE	type	= \'GR\'
							AND		use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();
					
					echo '<option value="">전체</option>';

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);
					
						echo '<option value="'.$row['code'].'" '.($row['code'] == $generation ? 'selected' : '').'>'.$row['name'].'</option>';	
					}

					$conn->row_free();
				?>	
				</select>
			</td>
		</tr>
		
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="35px">
		<col width="70px">
		<col width="70px">
		<col width="160px">
		<col width="90px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">대상자명</th>
			<th class="head">생년월일</th>
			<th class="head">주소</th>
			<th class="head">연락처</th>
			<th class="head">관리구분</th>
			<th class="head">상태구분</th>
			<th class="head">경제상항</th>
			<th class="head">세대유형</th>
			<th class="head last">관리주소</th>
		</tr>
	</thead>
	<tbody><?
		$sql = 'SELECT	*
				FROM	('.$bql.') AS a
				WHERE	gbn IS NOT NULL'.$wsl;

		if($gHostNm.$gDomain == 'cn.kacold.net'){
			$sql .= '
					ORDER	BY name
					LIMIT	'.$pageCount.','.$itemCnt;
		}else {
			if($name){
				$sql .= '
						ORDER	BY name
						LIMIT	'.$pageCount.','.$itemCnt;
			}else {
				$sql .= '
						ORDER	BY mgbn,name
						LIMIT	'.$pageCount.','.$itemCnt;
			}
		}
		
		/*
		if($debug){
			echo nl2br($sql); 
			exit;
		}
		*/

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($row['gbn'] > '0'){
				$key = $row['jumin'];
			}else{
				$key = $row['gbn'];
			}

			if ($row['mp_gbn'] == 'Y'){
				$row['mp_gbn'] = '중점관리';
			}else if ($row['mp_gbn'] == 'N'){
				$row['mp_gbn'] = '일반';
			}else{
				$row['mp_gbn'] = '일반접수';
			}
			
			if ($row['stat'] == '1'){
				$row['stat'] = '사용';
			}else if ($row['stat'] == '7'){
				$row['stat'] = '<span style="color:BLUE;">미등록</span>';
			}else if ($row['stat'] == '9'){
				$row['stat'] = '<span style="color:RED;">중지</span>';
			}
			
			if($row['income_gbn'] == '1'){
				$inGbn[$row['income_gbn']]['name'] = '기초';
			}

			?>
			<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<td class="center"><?=$pageCount + ($i + 1);?></td>
				<td class="left"><a href="#" onclick="lfReg('<?=$ed->en($row['jumin']);?>','<?=$row['gbn'];?>'); return false;"><?=($row['name'] ? $row['name'] : '이름없음');?></a></td>
				<td class="center"><?=$myF->issToBirthday($row['real_jumin'],'.');?></td>
				<td class="left"><div class="nowrap" style="width:150px;"><?=$row['addr'].' '.$row['addr_dtl'];?></div></td>
				<td class="center"><?=$myF->phoneStyle($row['phone'] ? $row['phone'] : $row['mobile'],'.');?></td>
				<td class="center"><?=$row['mp_gbn'];?></td>
				<td class="center"><?=$row['stat'];?></td>
				<td class="center"><?=$row['income_gbn'] != '9' ? $inGbn[$row['income_gbn']]['name'] : $row['income_other'];?></td>
				<td class="center"><?=$row['generation_gbn'] != '9' ? $geGbn[$row['generation_gbn']]['name'] : $row['generation_other'];?></td>
				<td class="left last"><?=$row['addr_ment'];?></td>
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
<input id="gbn" name="normalSeq" type="hidden" value="">
<?
	include_once('../inc/_db_close.php');
?>