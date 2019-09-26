<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	/*
	 * 기능		: 기관등록 / 수정
	 * 작성자	: 김재용
	 * 일자		: 2011.03.21
	 */

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

	$find_center_code	= $_SESSION["userLevel"] == 'A' ? $_REQUEST['find_center_code'] : $_SESSION["userCenterCode"]; //기관기호
	$find_center_name	= $_REQUEST['find_center_name'];															   //기관명
	$find_yoy_name		= $_REQUEST['find_yoy_name'];																   //직원명
	$find_yoy_phone		= str_replace('-', '', $_REQUEST['find_yoy_phone']);										   //연락처
	$find_yoy_stat		= $_REQUEST['find_yoy_stat'] != '' ? $_REQUEST['find_yoy_stat'] : '1';						   //고용상태
	$find_dept          = $_REQUEST['find_dept'];																	   //부서
	$find_yoy_ssn		= str_replace('-', '', $_REQUEST['find_yoy_ssn']);											   //주민번호
	$sst				= $_REQUEST['sst'];																				//db필드명 비교 변수
	$sod				= $_REQUEST['sod'];																				//오름(asc),내림(desc) 정렬 플래그
	$sfl				= $_REQUEST['sfl'];																				//DB(필드명)

	if($find_yoy_ssn != ''){
		if(strlen($find_yoy_ssn) > 6){
			$find_ssn = subStr($find_yoy_ssn, 0, 6).'-'.subStr($find_yoy_ssn, 6, 7);
		}else {
			$find_ssn = $find_yoy_ssn;
		}
	}


	if (empty($find_dept)) $find_dept = 'all';

	#직원명,주민번호,연락처 있을 시 이용상태 전체 조회
	//if($find_yoy_name != '' or $find_yoy_phone != '' or $find_yoy_ssn != '') $find_yoy_stat = 'all';

	//검색변수들을 $find변수의 배열에 담는다.
	$find = array(
					'1'=>$sst
				,	'2'=>$sod
				,	'3'=>$sfl
				,	'4'=>$find_center_kind
				,	'5'=>$find_center_name
				,	'6'=>$find_yoy_name
				,	'7'=>$find_yoy_ssn
				,	'8'=>$find_yoy_phone
				,	'9'=>$find_yoy_stat
				,	'10'=>$page
			);

	/*********************************************************

		동거급여 여부

	*********************************************************/
	$sql = 'select mh_jumin as jumin
			  from mem_hourly
			 where org_no      = \''.$find_center_code.'\'
			   and del_flag    = \'N\'
			   and mh_from_dt <= \''.date('Ym').'\'
			   and mh_to_dt   >= \''.date('Ym').'\'
			   and mh_type    != \'0\'
			   and mh_svc      = \'12\'';

	$arrHourlyFamily = $conn->_fetch_array($sql, 'jumin');

?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script language='javascript'>
<!--

	function __getSsn(target){

		var varSsn;

		varSsn = target.value;

		if(varSsn.length > 6){
			varSsn = varSsn.substring(0,6)+'-'+varSsn.substring(6,13);
		}else {
			varSsn = varSsn;
		}

		target.value = varSsn;
	}

//-->
</script>
<form name="f" method="post">
<div class="title">직원조회</div>
<table class="my_table my_border">
	<colgroup>
		<col width="55px">
		<col width="100px">
		<col width="55px">
		<col width="70px">
		<col width="55px">
		<col width="50px">
		<col width="55px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관기호</th>
			<td>
			<?
				if ($_SESSION["userLevel"] == "A"){
				?>	<input name="find_center_code" type="text" value="<?=$find_center_code;?>" maxlength="15" class="no_string" style="width:120px;" onKeyDown="if(event.keyCode!=13){__onlyNumber(this);}else{_list_center('<?=$page;?>');}" onkeyup="if(event.keyCode==13){_list_center('<?=$page;?>');}" onFocus="this.select();"><?
				}else{
				?>	<span style="padding-left:5px;"><?=$_SESSION["userCenterCode"];?></span><?
				}
			?>
			</td>
			<th class="center">기관명</th>
			<td colspan="3">
			<?
				if ($_SESSION["userLevel"] == "A"){
				?>	<input name="find_center_name" type="text" value="<?=$find_center_name;?>" maxlength="20" onkeypress="if(event.keyCode==13){_list_center('<?=$page;?>');}" style="width:100%;" onFocus="this.select();"><?
				}else{
				?>	<span style="padding-left:5px;"><?=$_SESSION["userCenterName"];?></span><?
				}
			?>
			</td>
			<th class="center">주민번호</th>
			<td>
				<input name="find_yoy_ssn" type="text" value="<?=$find_ssn;?>" maxlength="13" onKeyDown="if(event.keyCode!=13){__onlyNumber(this);}else{_listMember('<?=$page;?>');}" class="phone"  style="ime-mode:disabled;" onfocus="__replace(this, '-', '');" onblur="__getSsn(this);">
			</td>
			<td class="right other" style="padding-left:5px; vertical-align:top; padding-top:2px;">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_listMember('1');">조회</button></span>
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_mem_reg('<?=$find_center_code;?>','','');">등록</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">직원명</th>
			<td>
				<input name="find_yoy_name" type="text" value="<?=$find_yoy_name;?>" style="width:100%;" onkeyup="if(event.keyCode==13){_listMember('<?=$page;?>');}" onFocus="this.select();">
			</td>
			<th class="center">연락처</th>
			<td>
				<input name="find_yoy_phone" type="text" value="<?=$myF->phoneStyle($find_yoy_phone);?>" maxlength="11" class="phone" onKeyDown="if(event.keyCode!=13){__onlyNumber(this);}else{_listMember('<?=$page;?>');}" onkeyup="if(event.keyCode==13){_listMember('<?=$page;?>');}" style="ime-mode:disabled;" onfocus="__replace(this, '-', '');" onblur="__getPhoneNo(this);">
			</td>
			<th class="center">부서</th>
			<td>
			<?
				echo '<select name=\'find_dept\' style=\'width:auto;\'>';
				echo '<option value=\'all\' '.($find_dept == 'all' ? 'selected' : '').'>전체</option>';

				$sql = "select dept_cd, dept_nm
						  from dept
						 where org_no   = '$find_center_code'
						   and del_flag = 'N'
						 order by order_seq";

				$conn->query($sql);
				$conn->fetch();

				$row_count = $conn->row_count();

				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);

					echo '<option value=\''.$row['dept_cd'].'\' '.($find_dept == $row['dept_cd'] ? 'selected' : '').'>'.$row['dept_nm'].'</option>';
				}

				$conn->row_free();

				echo '<option value=\'-\' '.($find_dept == '-' ? 'selected' : '').'>미등록</option>';
				echo '</select>';
			?>
			</td>
			<th class="center">고용상태</th>
			<td class="last">
				<select name="find_yoy_stat" style="width:auto;">
					<option value="all">전체</option>
					<option value="1" <?=$find_yoy_stat == "1" ? "selected" : "";?>>재직</option>
					<option value="2" <?=$find_yoy_stat == "2" ? "selected" : "";?>>휴직</option>
					<option value="9" <?=$find_yoy_stat == "9" ? "selected" : "";?>>퇴사</option>
				</select>
			</td>
			<td class="right other">
				<span class="btn_pack m icon" style="margin-left:5px;"><span class="excel"></span><button name="btnExcel" type="button" onFocus="this.blur();" onClick="_yoy_chk_popup('<?=$find_center_name?>','<?=$find_yoy_name?>','<?=$find_yoy_phone?>','<?=$find_yoy_stat?>','<?=$find_dept?>','<?=$find_ssn?>');">엑셀</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<?
			if ($_SESSION["userLevel"] == "A"){
			?>	<col width="220px"><?
			}
		?>
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="90px">
		<col width="80px">
		<col width="40px">
		<col width="40px">
		<col width="40px">
		<col width="65px">
		<col width="65px">
		<col width="50px">
		<col width="40px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<?
				if ($_SESSION["userLevel"] == "A"){ ?>
					<th class="head">기관명</th>
			<?
				}
			?>
			<th class="head"><?=align('m02_yname','','desc', $find)?><span style="font-weight:bold;">직원명</span></th>
			<th class="head">생년월일</th>
			<th class="head">부서</th>
			<th class="head">연락처</th>
			<th class="head">고용형태</th>
			<th class="head">동거</th>
			<th class="head">재가</th>
			<th class="head">바우처</th>
			<th class="head"><?=align('m02_yipsail', '', '', $find)?><span style="font-weight:bold;">입사일자</span></th>
			<th class="head"><?=align('m02_ytoisail', '', '', $find)?><span style="font-weight:bold;">퇴사일자</span></th>
			<th class="head">자격증</th>
			<th class="head">치매</th>
			<th class="head last">스마트폰</th>
		</tr>
	</thead>
	<tbody>
	<?
		$wsl = "";
		$wsl2 = "";

		if ($_SESSION["userLevel"] == "A"){
			if ($find_center_code != '') $wsl .= " and m02_ccode like '$find_center_code%'";
			if ($find_center_name != '') $wsl .= " and m00_cname like '%$find_center_name%'";
		}else{
			$wsl .= " and m02_ccode = '$find_center_code'";
		}

		if ($find_yoy_ssn   != '')    $wsl .= " and m02_yjumin like '%$find_yoy_ssn%'";
		if ($find_yoy_name  != '')    $wsl .= " and m02_yname >= '$find_yoy_name'";
		if ($find_yoy_phone != '')    $wsl .= " and left(m02_ytel, '".strlen($find_yoy_phone)."') = '$find_yoy_phone'";
		if ($find_yoy_stat  != 'all') $wsl .= " and m02_ygoyong_stat = '$find_yoy_stat'";
		if ($find_dept      != 'all') $wsl .= " and m02_dept_cd = '".str_replace('-','',$find_dept)."'";
		//if ($find_dept      != 'all') $wsl .= " and m02_dept_cd = '$find_dept'";

		if (!$sst) {
			$wsl2 .= ' order by m02_yname';
		}else {
			$wsl2 .= " order by $sst $sod ";
		}


		/*
		$sql = "select count(*)
				  from m02yoyangsa
				 left join m00center
					on m00_mcode = m02_ccode
				   and m00_mkind = m02_mkind
				 where m02_ccode is not null
				   and m02_mkind = (select min(m00_mkind) from m00center where m00_mcode = m02_ccode)
				   and m02_del_yn = 'N' $wsl";
		*/
		$sql = 'SELECT COUNT(*)
				  FROM (
					   SELECT MIN(m02_mkind) AS kind
					   ,      m02_yjumin AS jumin
						 FROM m02yoyangsa
						WHERE m02_ccode IS NOT NULL '.$wsl.'
						GROUP BY m02_yjumin
					   ) AS mem';
		$total_count = $conn->get_data($sql);

		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:_listMember',
			'curPageNum'	=> $page,
			'pageVar'		=> 'page',
			'extraVar'		=> '',
			'totalItem'		=> $total_count,
			'perPage'		=> $page_count,
			'perItem'		=> $item_count,
			'prevPage'		=> '[이전]',
			'nextPage'		=> '[다음]',
			'prevPerPage'	=> '[이전'.$page_count.'페이지]',
			'nextPerPage'	=> '[다음'.$page_count.'페이지]',
			'firstPage'		=> '[처음]',
			'lastPage'		=> '[끝]',
			'pageCss'		=> 'page_list_1',
			'curPageCss'	=> 'page_list_2'
		);

		$pageCount = $page;

		if ($pageCount == ""){
			$pageCount = "1";
		}

		$pageCount = (intVal($pageCount) - 1) * $item_count;
		
		$sql = 'select jumin
				,	   join_dt
				,	   quit_dt
				from   mem_his
				where  org_no  = \''.$find_center_code.'\'';
		$mem = $conn -> _fetch_array($sql, 'jumin');
		


		$sql = "select m02_ccode
				,      min(m02_mkind) as m02_mkind
				,      m02_key
				,      m02_yjumin
				,      m00_cname
				,      m02_yname
				,      dept.dept_nm
				,      m02_ytel
				,	   m02_ins_from_date
				,	   m02_ins_yn
				,	   m02_yipsail
				,	   m02_ytoisail
				,	   case m02_ygoyong_kind when '1' then '정규직'
											 when '2' then '계약직'
											 when '3' then '60시간 이상'
											 when '4' then '60시간 미만'
											 when '5' then '특수근로' else ' ' end as m02_ygoyong_kind
				,	   case m02_yfamcare_umu when 'Y' then 'Y'
											 when 'N' then ' ' else '-' end as m02_yfamcare_umu
				,      case m02_ygoyong_stat when '1' then '재직'
				                             when '2' then '휴직'
											 when '9' then '퇴사' else '-' end as m02_ygoyong_stat
				,      case m02_jikwon_gbn when 'Y' then '요'
				                           when 'M' then '관'
										   when 'W' then '사'
										   when 'A' then '관 + 요' else ' ' end as m02_jikwon_gbn

				,     (select case when count(*) > 0 then 'Y' else ' ' end
				         from m02yoyangsa as temp_y
						where temp_y.m02_ccode = m02yoyangsa.m02_ccode
						  and temp_y.m02_mkind = '0'
						  and temp_y.m02_yjumin = m02yoyangsa.m02_yjumin
						  and temp_y.m02_del_yn = 'N') as care_yn

				,     (select case when count(*) > 0 then 'Y' else ' ' end
						 from m02yoyangsa as temp_y
						where temp_y.m02_ccode = m02yoyangsa.m02_ccode
						  and temp_y.m02_mkind >= '1'
						  and temp_y.m02_mkind <= '4'
						  and temp_y.m02_yjumin = m02yoyangsa.m02_yjumin
						  and temp_y.m02_del_yn = 'N') as voucher_yn

				,      case m02_y4bohum_umu when 'Y' then 'Y' else ' ' end as m02_y4bohum_umu

				,     (select license_gbn
						 from counsel_license
					    where org_no      = m02yoyangsa.m02_ccode
						  and license_ssn = m02yoyangsa.m02_yjumin
						order by license_dt desc
						limit 1) as license_nm
				,     (select dementia_yn
						 from mem_option
					    where org_no = m02yoyangsa.m02_ccode
						 and  mo_jumin = m02yoyangsa.m02_yjumin) as dementia_yn
				  from m02yoyangsa
				  left join m00center
					on m00_mcode = m02_ccode
				   and m00_mkind = m02_mkind
				  left join dept
				    on dept.org_no  = m02_ccode
				   and dept.dept_cd = m02_dept_cd
				 where m02_ccode is not null
				   and m02_del_yn = 'N' $wsl
				 group by m02_yjumin
				 $wsl2
				 limit $pageCount, $item_count";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				if (!empty($row['m02_yname']))
					$memNm = $row['m02_yname'];
				else
					$memNm = '이름없음';?>
				<tr>
					<td class="center"><?=$pageCount + ($i + 1);?></td>
					<?
					if ($_SESSION["userLevel"] == "A"){
						?>	<td class="left"><?=$row['m00_cname'];?></td><?
						}
					?>
					<td class="left"><div class="nowrap" style="width:70px;" title="<?=$memNm;?>"><a href="#" onclick="_mem_reg('<?=$row['m02_ccode']?>','<?=$row['m02_mkind']?>','<?=$ed->en($row['m02_yjumin']);?>');"><?=$memNm;?></a></div></td>
					<td class="center"><?=$myF->issToBirthday($row['m02_yjumin'],'.');?></td>
					<td class="left"><div class="nowrap" style="width:60px;" title="<?=$row['dept_nm'];?>"><?=$row['dept_nm'];?></div></td>
					<td class="left"><?=$myF->phoneStyle($row['m02_ytel']);?></td>
					<td class="center"><?=$row['m02_ygoyong_kind'];?></td>
					<td class="center"><?=!empty($arrHourlyFamily[$row['m02_yjumin']]) ? 'Y' : '';?></td>
					<td class="center"><?=$row['care_yn'];?></td>
					<td class="center"><?=$row['voucher_yn'];?></td>
					<td class="center"><?=$myF->dateStyle($mem[$row['m02_yjumin']]['join_dt'],'.');?></td>
					<td class="center"><?=$myF->dateStyle($mem[$row['m02_yjumin']]['quit_dt'],'.');?></td>
					<td class="left"><div class="nowrap" style="width:45px;"><?=$row['license_nm'];?></div></td>
					<td class="center"><?=$row['dementia_yn'];?></td>
					<td class="center last"><?=$row['m02_jikwon_gbn'];?></td>
				</tr>
			<?
			}
		}else{
		?>	<tr>
				<td class="center last" colspan="12">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}

		$conn->row_free();

	?>
	</tbody>
</table>
<div style="text-align:left;">
	<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($total_count);?></div>
	<div style="width:100%; text-align:center;">
	<?
		$paging = new YsPaging($params);
		$paging->printPaging();
	?>
	</div>
</div>
<input name="code"	type="hidden" value="<?=$find_center_code;?>">
<input name="kind"	type="hidden" value="">
<input name="page"	type="hidden" value="<?=$page;?>">
<input name="jumin"	type="hidden" value="">
<!-- 오름,내림차순정렬 변수 담기-->
<input name="sst"			type="hidden" value="<?=$sst?>">
<input name="sod"			type="hidden" value="<?=$sod?>">
<input name="sfl"			type="hidden" value="<?=$sfl?>">
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");


	//오름차순,내림차순 정렬
	function align($col, $query_string='', $flag='asc', $find){

		$q1 = "sst=$col";
		if ($flag == 'asc'){	//오름차순
			$q2 = 'sod=asc';
			if ($find[1] == $col){
				if ($find[2] == 'asc'){
					$q2 = 'sod=desc';
				}
			}
		}else {					//내림차순
			$q2 = 'sod=desc';
			if ($find[1] == $col){
				if ($find[2] == 'desc'){
					$q2 = 'sod=asc';
				}
			}
		}

		return "<a href='$_SERVER[PHP_SELF]?$query_string&$q1&$q2&sfl=$find[1]&find_center_kind=$find[4]&find_yoy_name=$find[6]&find_yoy_ssn=$find[7]&find_yoy_phone=$find[8]&find_yoy_stat=$find[9]&page=$find[10]'>";
	}

?>