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

	$find_center_code	= $_SESSION["userLevel"] == 'A' ? $_REQUEST['find_center_code'] : $_SESSION["userCenterCode"];
	$find_center_name	= $_REQUEST['find_center_name'];
	$find_yoy_name		= $_REQUEST['find_yoy_name'];
	$find_yoy_phone		= str_replace('-', '', $_REQUEST['find_yoy_phone']);
	$find_yoy_stat		= $_REQUEST['find_yoy_stat'] != '' ? $_REQUEST['find_yoy_stat'] : '1';
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
//엑셀
function excel(){
	var f = document.f;

	f.action = 'yoy_list_excel.php';
	f.submit();
}
//-->
</script>
<form name="f" method="post">
<div class="title">직원조회</div>
<table class="my_table my_border">
	<colgroup>
		<col width="100px">
		<col width="130px">
		<col width="100px">
		<col width="100px">
		<col width="80px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td>
			<?
				if ($_SESSION["userLevel"] == "A"){
				?>	<input name="find_center_code" type="text" value="<?=$find_center_code;?>" maxlength="15" class="no_string" style="width:120px;" onKeyDown="if(event.keyCode!=13){__onlyNumber(this);}else{_list_center('<?=$page;?>');}" onkeyup="if(event.keyCode==13){_list_center('<?=$page;?>');}" onFocus="this.select();"><?
				}else{
				?>	<span style="padding-left:5px;"><?=$_SESSION["userCenterCode"];?></span><?
				}
			?>
			</td>
			<th>기관명</th>
			<td colspan="3">
			<?
				if ($_SESSION["userLevel"] == "A"){
				?>	<input name="find_center_name" type="text" value="<?=$find_center_name;?>" maxlength="20" onkeypress="if(event.keyCode==13){_list_center('<?=$page;?>');}" style="width:100%;" onFocus="this.select();"><?
				}else{
				?>	<span style="padding-left:5px;"><?=$_SESSION["userCenterName"];?></span><?
				}
			?>
			</td>
			<td class="other" style="padding-left:5px; vertical-align:top; padding-top:2px;">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_list_member('1');">조회</button></span>
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_reg_member('<?=$find_center_code;?>','','');">등록</button></span>
			</td>
		</tr>
		<tr>
			<th>직원명</th>
			<td>
				<input name="find_yoy_name" type="text" value="<?=$find_yoy_name;?>" style="width:120px;" onkeyup="if(event.keyCode==13){_list_center('<?=$page;?>');}" onFocus="this.select();">
			</td>
			<th>연락처</th>
			<td>
				<input name="find_yoy_phone" type="text" value="<?=$myF->phoneStyle($find_yoy_phone);?>" maxlength="11" class="phone" onKeyDown="if(event.keyCode!=13){__onlyNumber(this);}else{_list_center('<?=$page;?>');}" onkeyup="if(event.keyCode==13){_list_center('<?=$page;?>');}" onfocus="__replace(this, '-', '');" onblur="__getPhoneNo(this);">
			</td>
			<th>고용상태</th>
			<td class="last">
				<select name="find_yoy_stat" style="width:auto;">
					<option value="all">전체</option>
					<option value="1" <?=$find_yoy_stat == "1" ? "selected" : "";?>>활동</option>
					<option value="2" <?=$find_yoy_stat == "2" ? "selected" : "";?>>휴직</option>
					<option value="9" <?=$find_yoy_stat == "9" ? "selected" : "";?>>퇴사</option>
				</select>
			</td>	
			<td class="other"><span class="btn_pack m icon" style="margin-left:5px;"><span class="excel"></span><button name="btnExcel" type="button" onFocus="this.blur();" onClick="excel();">엑셀</button></span></td>
		</tr>
	</tbody>
</table>
<table class="my_table my_border" style="margin-top:-1px;">
	<colgroup>
		<col width="25px">
		<?
			if ($_SESSION["userLevel"] == "A"){
			?>	<col width="220px"><?
			}
		?>
		<col width="90px">
		<col width="100px">
		<col width="65px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="65px">
		<col width="65px">
		<col width="120px">
		<col width="60px">
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
			<th class="head"><span title="요양보호사를 클릭하면 상세한 요양보호사 정보를 조회할 수 있습니다">요양보호사명</span></th>
			<th class="head">연락처</th>
			<th class="head">고용형태</th>
			<th class="head">동거케어</th>
			<th class="head">재가요양</th>
			<th class="head">4대보험</th>
			<th class="head"><span title="가입일자">배상책임</span></th>
			<th class="head">입사일자</th>
			<th class="head">자격증</th>
			<th class="last head">스마트폰</th>
		</tr>
	</thead>
	<tbody>
	<?
		$wsl = "";
		if ($_SESSION["userLevel"] == "A"){
			if ($find_center_code != '') $wsl .= " and m02_ccode like '$find_center_code%'";
			if ($find_center_name != '') $wsl .= " and m00_cname like '%$find_center_name%'";
		}else{
			$wsl .= " and m02_ccode = '$find_center_code'";
		}

		if ($find_yoy_name  != '') $wsl .= " and m02_yname like '%$find_yoy_name%'";
		if ($find_yoy_phone != '') $wsl .= " and m02_ytel like '%$find_yoy_phone%'";
		if ($find_yoy_stat  != 'all') $wsl .= " and m02_ygoyong_stat = '$find_yoy_stat'";

		$sql = "select count(*)
				  from m02yoyangsa
				 left join m00center
					on m00_mcode = m02_ccode
				   and m00_mkind = m02_mkind
				 where m02_ccode is not null
				   and m02_mkind = (select min(m00_mkind) from m00center where m00_mcode = m02_ccode)
				   and m02_del_yn = 'N' $wsl";
		$total_count = $conn->get_data($sql);

		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:_list_center',
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

		$sql = "select m02_ccode
				,      m02_mkind
				,      m02_key
				,      m02_yjumin
				,      m00_cname
				,      m02_yname
				,      m02_ytel
				,	   m02_ins_from_date
				,	   m02_ins_yn
				,	   m02_yipsail
				,	   m99_name
				,	   case m02_ygoyong_kind when '1' then '정규직'
											 when '2' then '60시간미만'
											 when '3' then '60시간이상'
											 when '4' then '기  타'
											 when '5' then '특수근로' else ' ' end as m02_ygoyong_kind
				,	   case m02_yfamcare_umu when 'Y' then 'Y'
											 when 'N' then ' ' else '-' end as m02_yfamcare_umu
				,      case m02_ygoyong_stat when '1' then '활동'
				                             when '2' then '휴직'
											 when '9' then '퇴사' else '-' end as m02_ygoyong_stat
				,      case m02_jikwon_gbn when 'Y' then '요'
				                           when 'M' then '관'
										   when 'A' then '관 + 요' else ' ' end as m02_jikwon_gbn
				/*,      case m02_sign1 when 'Y' then '유' else '무' end as m02_sign1*/
				/*,      case m02_sign2 when 'Y' then '유' else '무' end as m02_sign2*/
				,     (select case when count(*) > 0 then 'Y' else ' ' end from m02yoyangsa as temp_y where temp_y.m02_ccode = real_y.m02_ccode and temp_y.m02_mkind in ('0') and temp_y.m02_yjumin = real_y.m02_yjumin and temp_y.m02_del_yn = 'N') as m02_sign2
				,     (select case when count(*) > 0 then 'Y' else ' ' end from m02yoyangsa as temp_y where temp_y.m02_ccode = real_y.m02_ccode and temp_y.m02_mkind in ('1','2','3','4') and temp_y.m02_yjumin = real_y.m02_yjumin and temp_y.m02_del_yn = 'N') as m02_sign1
				,      case m02_y4bohum_umu when 'Y' then 'Y'
									   when 'N' then ' ' else ' ' end as m02_y4bohum_umu
				  from m02yoyangsa as real_y
				 left join m00center
					on m00_mcode = m02_ccode
				   and m00_mkind = m02_mkind
				  left join m99license
				    on m99_code = m02_yjakuk_kind
				 where m02_ccode is not null
				   and m02_mkind = (select min(m00_mkind) from m00center where m00_mcode = m02_ccode)
				   and m02_del_yn = 'N' $wsl
				 order by m02_yname
				 limit $pageCount, $item_count";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				?>

				<tr>
					<td class="center"><?=$pageCount + ($i + 1);?></td>
					<?
					if ($_SESSION["userLevel"] == "A"){
						?>	<td class="left"><?=$row['m00_cname'];?></td><?
						}
					?>
					<td class="left"><a href="#" onclick="_reg_member('<?=$row['m02_ccode']?>','<?=$row['m02_mkind']?>','<?=$ed->en($row['m02_yjumin']);?>');"><?=$row['m02_yname'];?></a></td>
					<td class="left"><?=$myF->phoneStyle($row['m02_ytel']);?></td>
					<td class="center"><?=$row['m02_ygoyong_kind'];?></td>
					<td class="center"><?=$row['m02_yfamcare_umu'];?></td>
					<td class="center"><?=$row['m02_sign2'];?></td>
					<!--td class="center"><?=$row['m02_sign1'];?></td-->
					<td class="center"><?=$row['m02_y4bohum_umu'];?></td>
					<?
						if($row['m02_ins_from_date'] != null){
							if($row['m02_ins_yn'] == 'Y'){
					?>
							<td class="center"><?=substr($row['m02_ins_from_date'],0,4).'.'.substr($row['m02_ins_from_date'],4,2).'.'.substr($row['m02_ins_from_date'],6,2);?></td>
					<?
							}else { ?>
								<td class="center"> </td><?
							}
						}else{
					?>
							<td class="center"> </td>
					<?
						}

						if($row['m02_yipsail'] != null){
					?>
							<td class="center"><?=substr($row['m02_yipsail'],0,4).'.'.substr($row['m02_yipsail'],4,2).'.'.substr($row['m02_yipsail'],6,2);?></td>
					<?
						}else{
					?>
							<td class="center"> </td>
					<?
						}
					?>
					<td class="center"><?=$row['m99_name'];?></td>
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
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>