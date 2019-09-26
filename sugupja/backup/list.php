<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");


	if ($_SESSION["userLevel"] == "A"){
		$mCode = $_REQUEST["mCode"];
	}else{
		$mCode = $_SESSION["userCenterCode"];
	}

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

	$find_center_code   = $_REQUEST['find_center_code'];
	$find_center_name   = $_REQUEST['find_center_name'];
	$find_su_name		= $_REQUEST['find_su_name'];
	$find_su_phone		= $_REQUEST['find_su_phone'];
	$find_su_stat       = $_REQUEST['find_su_stat'] != '' ? $_REQUEST['find_su_stat'] : '1';
?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>

<form name="f" method="post">
<div class="title">수급자 조회</div>
<table class="my_table my_border">
	<colgroup>
		<col width="120px">
		<col width="130px">
		<col width="100px">
		<col width="100px">
		<col width="80px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호/승인번호</th>
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
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_list_center('1');">조회</button></span>
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_reg_center('');">등록</button></span>
			</td>
		</tr>
		<tr>
			<th>수급자명</th>
			<td>
				<input name="find_su_name" type="text" value="<?=$find_yoy_name;?>" style="width:120px;" onkeyup="if(event.keyCode==13){_list_center('<?=$page;?>');}" onFocus="this.select();">
			</td>
			<th>연락처</th>
			<td>
				<input name="find_su_phone" type="text" value="<?=$myF->phoneStyle($find_yoy_phone);?>" maxlength="11" class="phone" onKeyDown="if(event.keyCode!=13){__onlyNumber(this);}else{_list_center('<?=$page;?>');}" onkeyup="if(event.keyCode==13){_list_center('<?=$page;?>');}" onfocus="__replace(this, '-', '');" onblur="__getPhoneNo(this);">
			</td>
			<th>수급상태</th>
			<td class="last">
				<select name="find_su_stat">
					<option value="all">-전체-</option>
					<option value="1" <?=$find_su_stat == "1" ? "selected" : "";?>>수급중</option>
					<option value="2" <?=$find_su_stat == "2" ? "selected" : "";?>>계약해지</option>
					<option value="3" <?=$find_su_stat == "3" ? "selected" : "";?>>보류</option>
					<option value="4" <?=$find_su_stat == "4" ? "selected" : "";?>>사망</option>
					<option value="5" <?=$find_su_stat == "5" ? "selected" : "";?>>타기관 이전</option>
					<option value="6" <?=$find_su_stat == "6" ? "selected" : "";?>>등외판정</option>
					<option value="7" <?=$find_su_stat == "7" ? "selected" : "";?>>입원</option>
				</select>
			</td>
			<td class="other"></td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border" style="margin-top:-1px;">
	<colgroup>
		<col width="50px">
		<?
			if ($_SESSION["userLevel"] == "A"){?>
				<col width="110px"><?
			}
		?>
		<col width="70px">
		<col width="100px">
		<col width="110px">
		<col width="170px">
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<?
				if ($_SESSION["userLevel"] == "A"){?>
					<th class="head">기관명</th><?
				}
			?>
			<th class="head">수급자명</th>
			<th class="head">연락처</th>
			<th class="head">대표요양보호사</th>
			<th class="head">담당요양보호사</th>
			<th class="head">수급현황</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$wsl = "";
		if ($_SESSION["userLevel"] == "A"){
			if ($find_center_code != '') $wsl .= " and m03_ccode like '$find_center_code%'";
			if ($find_center_name != '') $wsl .= " and m00_cname like '%$find_center_name%'";

		}else{
			$wsl .= " and m03_ccode = '".$_SESSION["userCenterCode"]."'";
		}

		if ($find_su_name  != '') $wsl .= " and m03_name like '%$find_su_name%'";
		if ($find_su_phone != '') $wsl .= " and m03_hp like '%$find_su_phone%'";
		if ($find_su_stat  != 'all') $wsl .= " and m03_sugup_status = '$find_su_stat'";

		$sql = "select count(*)
				  from m03sugupja
				 inner join m00center
					on m00_mcode = m03_ccode
				   and m00_mkind = m03_mkind
				 where m03_ccode is not null $wsl";
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


		$sql = "select m03_ccode
				,      m03_mkind
				,      m00_cname
				,      m03_jumin
				,      m03_name
				,      m03_hp
				,      m03_yoyangsa1_nm
				,      m03_yoyangsa2_nm
				,      m03_yoyangsa3_nm
				,      m03_yoyangsa4_nm
				,      m03_yoyangsa5_nm
				,      m03_sugup_status
				  from m03sugupja
				 inner join m00center
					on m00_mcode = m03_ccode
				   and m00_mkind = m03_mkind
				 where m03_ccode is not null $wsl
				 limit $pageCount, $item_count";

		$conn->query($sql);
		$row = $conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				$yoyangsa = $row['m03_yoyangsa2_nm'].($row['m03_yoyangsa3_nm'] != '' ? ',' :'').$row['m03_yoyangsa3_nm'].($row['m03_yoyangsa4_nm'] != '' ? ',' :'').$row['m03_yoyangsa4_nm'].($row['m03_yoyangsa5_nm'] != '' ? ',' :'').$row['m03_yoyangsa5_nm'];
				?>
				<tr>
					<td class="center"><?=$pageCount + ($i + 1);?></td>
					<?
						if ($_SESSION["userLevel"] == "A"){?>
							<td class="left"><?=left($row["m00_cname"], 9);?></td><?
						}
					?>
					<td class="left"><a href="#" onClick="_reg_sugupReg('<?=$row["m03_ccode"];?>','<?=$row["m03_mkind"];?>','<?=$ed->en($row["m03_jumin"]);?>');"><?=$row["m03_name"];?></a></td>
					<td class="center"><?=getPhoneStyle($row["m03_hp"]);?></td>
					<td class="left"><?=$row['m03_yoyangsa1_nm'];?></td>
					<td class="left" title="<?=$yoyangsa;?>"><?=left($yoyangsa, 15);?></td>
					<td class="center"><?=$definition->SugupjaStatusGbn($row['m03_sugup_status']);?></td>
					<td class="other"></td>
				</tr>
			<?
			}
		}else{
			echo "<tr><td style='text-align:center;' colspan='8'>::검색된 데이타가 없습니다.::</td></tr>";
		}

		$conn->row_free();
	?>
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
<input name="mCode"	type="hidden" value="">
<input name="mKind"	type="hidden" value="">
<input name="mJumin" type="hidden" value="">
<input name="page"	type="hidden" value="<?=$page;?>">
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>

<script language="javaScript">

function _reg_sugupReg(code,mKind,mJumin){
	document.f.mCode.value = code;
	document.f.mKind.value = mKind;
	document.f.mJumin.value = mJumin;
	document.f.action = 'reg.php';
	document.f.submit();
}
</script>