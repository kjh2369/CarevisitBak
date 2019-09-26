<?

	include_once('../inc/_header.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');
	include_once("../inc/_page_list.php");
	include_once("../inc/_db_open.php");

	$mCode = $_POST['mCode'];													//기관기호
	$cName = $_POST['cName'];													//기관명
	$branch = $_POST['branch'];													//지사명	
	$person = $_POST['person'];													//담당자명
	$find_cont_date     = $_REQUEST['find_cont_date'];							//계약
	$find_cont_no_date  = $_REQUEST['find_cont_no_date'];						//미계약
	$find_from_yymm     = str_replace('-', '', $_REQUEST['find_from_yymm']);	//연결시작년월
	$find_to_yymm       = str_replace('-', '', $_REQUEST['find_to_yymm']);		//연결종료년월

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	$comDomain = $myF->_domain();
	$companyCD = $conn->_company_code($comDomain);


	if (!is_numeric($page)) $page = 1;
?>
<link href="../css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/branch.js"></script>
<script>
	$(document).ready(function(){
		__init_form(document.f);
	});

	function copy_val(obj){
		document.getElementById('find_to_yymm').value = obj.value;
	}
</script>
<table>
	<tr>
		<td class="title" colspan=8>지사/기관연결</td>
	</tr>
	</table>
	<form name="f" method="post">
	<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="50px">
		<col width="50px">
		<col width="50px">
		<col width="40px">
		<col width="210px">
		<col >
	</colgroup>
	<tr>
		<th class="head">기관코드</td>
		<td class="center">
			<input name="mCode" type="text" value="<?=$mCode?>" style="width:100px; ime-mode:disabled;" maxLength="15" onKeyDown="if(event.keyCode!=13){__onlyNumber(this);}else{_b2c_center_list('<?=$page;?>');}" onkeyup="if(event.keyCode==13){_list_center('<?=$page;?>');}" onFocus="this.select();">
		</td>
		<th class="head">기관명</td>
		<td class="center">
			<input name="cName" type="text" value="<?=$cName?>" style="width:100px;" maxLength="30" onkeypress="if(event.keyCode==13){_b2c_center_list('<?=$page;?>');}" onFocus="this.select();">
		</td>
		<th class="head">지사</td>
		<td class="left">
			<select name="branch" onChange="_getPerson(this.value);" style="margin:0;">
			<option value="">-지사선택-</option>
			<?
				ob_start();
				
				//지사테이블 조회
				$sql = "select b00_code, b00_name
						  from b00branch
						 where b00_domain = '".$gDomain."'
						 order by b00_name";
				$conn->query($sql);
				$conn->fetch();
				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					echo "<option value='".$row[0]."' ".($row[0] == $branch ? "selected" : "").">".$row[1].'['.$row[0].']'."</option>";
				}
				$conn->row_free();
				$opton = ob_get_contents();
				ob_end_clean();

				echo $opton;
			?>
			</select>
			<select name="person" style="margin:0;">
			<?
				ob_start();
				
				//담당자테이블 조회
				$sql = "select b01_code, b01_name
						  from b01person
						 where b01_branch = '".$branch."'
						 order by b01_name";
				$conn->query($sql);
				$conn->fetch();
				$rowCount = $conn->row_count();


				echo "<option value=''>-담당자-</option>";

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					echo "<option value='".$row[0]."' ".($row[0] == $person ? "selected" : "").">".$row[1]."</option>";

				}

				$conn->row_free();
				$opton = ob_get_contents();
				ob_end_clean();

				echo $opton;
			?>
			</select>
		</td>
		<td class="left" style="border-right:0px margin:0;">
			<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_b2c_center_list('1');">조회</button></span>
			<span class="btn_pack m icon"><span class="excel"></span><button name="btnExcel" type="button" onFocus="this.blur();" onClick="_brachExcel();">엑셀</button></span>
		</td>
		<td class="left" style="border-right:0px margin:0;">
			<span class="btn_pack m icon"><span class="add"></span><button onFocus="this.blur();" onClick="_b2cCenterAdd();">추가</button></span>
			<span class="btn_pack m icon"><span class="delete"></span><button onFocus="this.blur();" onClick="_b2cCenterDelete();">삭제</button></span>
		</td>
	</tr>
	<tr>
		<th>연결년월</th>
		<td colspan="2">
			<input class="yymm" name="find_from_yymm" type="text" value="<?=substr($find_from_yymm, 0, 4).'-'.substr($find_from_yymm, 4, 2);?>" maxlength="6" onchange="copy_val(this);"> - <input class="yymm" name="find_to_yymm" type="text" value="<?=substr($find_to_yymm, 0, 4).'-'.substr($find_to_yymm, 4, 2);?>" maxlength="6" >
		</td>
		<td class="left last" colspan="2">
			<input name="find_cont_date" type="checkbox" class="checkbox" value="Y" <? if($find_cont_date == 'Y'){?>checked<?} ?>>계약
			<input name="find_cont_no_date" type="checkbox" class="checkbox" value="Y" <? if($find_cont_no_date == 'Y'){?>checked<?} ?>>미계약
		</td>
		<td class="last" colspan="5"></td>
	</tr>
	<tr>
	<td colspan="8" class="last bottom">
		<table class="my_table" style='width:100%; border-bottom:none;'>
			<colgroup>
				<col width="20px;">
				<col width="115px;">
				<col width="60px;">
				<col width="100px;">
				<col width="220px;">
				<col width="60px;">
				<col width="65px;">
				<col width="65px;">
				<col width="45px">
				<col width="45px">
				<col width="45px">
			</colgroup>
			<thead>
				<tr>
					<th class="head"><input name="checkAll" type="checkbox" class="checkbox" onClick="__checkMyValue('check[]', this.checked);"></th>
					<th class="head">지사명</th>
					<th class="head">담당자명</th>
					<th class="head">기관코드</th>
					<th class="head">기관명</th>
					<th class="head">대표자명</th>
					<th class="head">연결일자</th>
					<th class="head">계약일자</th>
					<th class="head">직원</th>
					<th class="head">수급자</th>
					<th class="last head">일정</th>
				</tr>
			</thead>
			<tbody>


		<?
			$sql = "select count(*)
					from b02center
				   inner join b00branch
					  on b00_code   = b02_branch
					 and b00_domain = '".$comDomain."'
				   inner join b01person
					  on b01_branch = b02_branch
					 and b01_code   = b02_person
				   inner join m00center
					  on m00_mcode = b02_center
					 and m00_mkind = b02_kind
				   where b02_branch != ''";

				if ($mCode != ''){
					$sql .= " and b02_center like '%$mCode%'";		//기관기호 검색
				}
				if ($cName != ''){
					$sql .= " and m00_store_nm like '%$cName%'";	//기관명 검색
				}
				if ($branch != ''){
					$sql .= " and b02_branch = '$branch'";			//지사명 검색
				}	
				if ($person != ''){
					$sql .= " and b02_person = '$person'";			//담당자명 검색
				}

				if ($find_cont_date   == 'Y' and $find_cont_no_date   == 'Y'){		
				//계약,미계약 둘다 체크했을 시
				}else {
					//그 외일 경우 
					if ($find_cont_date   == 'Y') $sql .= " and ifnull(m00_cont_date, '') != ''";		//계약 기관 검색
					if ($find_cont_no_date   == 'Y') $sql .= " and ifnull(m00_cont_date, '') = ''";		//미계약 기관 검색
				}

				if ($find_from_yymm != '') $sql .= " and left(b02_date, 6) between '$find_from_yymm' and '".($find_to_yymm != '' ? $find_to_yymm : '999912')."'";		//연결년월 검색

					$sql .= " order by b02_branch, b01_name";
				

				$total_count = $conn->get_data($sql);

			// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
				if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

				$params = array(
					'curMethod'		=> 'post',
					'curPage'		=> 'javascript:_b2c_center_list',
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

			$date = date('Ym', mktime());



			$sql = "select b02_branch as branchCode
					,      b00_name as branchName
					,      b01_name as personName
					,      b02_center as centerCode
					,      m00_store_nm as centerName
					,      m00_mkind as centerKind
					,      m00_cont_date as contDate
					,      case m00_mkind when '0' then '재가요양기관'
										  when '1' then '가사간병'
										  when '2' then '노인돌봄'
										  when '3' then '산모신생아'
										  when '4' then '장애인 활동보조' else '-' end as centerType
					,	   m00_mname as manager
					,      b02_date as date
					,      b02_other as other
					,     (select count(*)
							 from m02yoyangsa
							where m02_ccode        = b02_center
							  and m02_mkind        = b02_kind
							  and m02_ygoyong_stat = '1') as y_count
					,     (select count(*)
							 from m03sugupja
							where m03_ccode        = b02_center
							  and m03_mkind        = b02_kind
							  and m03_sugup_status = '1') as s_count
					,     /*(select count(distinct t01_jumin)
							 from t01iljung
							where t01_ccode  = b02_center
							  and t01_mkind  = b02_kind
							  and t01_del_yn = 'N'
							  and t01_sugup_date like '$date%')*/ 0 as i_count
					  from b02center
					 inner join b00branch
						on b00_code = b02_branch
					   and b00_domain = '".$comDomain."'
					 inner join b01person
						on b01_branch = b02_branch
					   and b01_code   = b02_person
					 inner join m00center
						on m00_mcode = b02_center
					   and m00_mkind = b02_kind
					 where b02_branch != ''";


				if ($mCode != ''){
					$sql .= " and b02_center like '%$mCode%'";		//기관기호 검색
				}
				if ($cName != ''){
					$sql .= " and m00_store_nm like '%$cName%'";	//기관명 검색
				}
				if ($branch != ''){
					$sql .= " and b02_branch = '$branch'";			//지사명 검색
				}	
				if ($person != ''){
					$sql .= " and b02_person = '$person'";			//담당자명 검색
				}

				if ($find_cont_date   == 'Y' and $find_cont_no_date   == 'Y'){		
				//계약,미계약 둘다 체크했을 시
				}else {
					//그 외일 경우 
					if ($find_cont_date   == 'Y') $sql .= " and ifnull(m00_cont_date, '') != ''";		//계약 기관 검색
					if ($find_cont_no_date   == 'Y') $sql .= " and ifnull(m00_cont_date, '') = ''";		//미계약 기관 검색
				}

				if ($find_from_yymm != '') $sql .= " and left(b02_date, 6) between '$find_from_yymm' and '".($find_to_yymm != '' ? $find_to_yymm : '999912')."'";		//연결년월 검색

				$sql .= " order by branchName, centerName";

				$sql .= " limit $pageCount, $item_count";

			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();

			if ($rowCount > 0){
				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					?>
						<tr>
							<td class="center"><input name="check[]" type="checkbox" class="checkbox" value="<?=$row['centerCode'];?>_<?=$row['centerKind'];?>"></td>
							<td class="left"><?=$row['branchName'];?>[<?=$row['branchCode'];?>]</td>
							<td class="left"><?=$row['personName'];?></td>
							<td class="left"><?=$row['centerCode'];?></td>
							<td class="left"><a href="#" onClick="_b2cCenterAdd('<?=$row['centerCode'];?>', '<?=$row['centerKind'];?>')"><?=$row['centerName'];?></a></td>
							<td class="left"><?=$row['manager'];?></td>
							<td class="center"><?=$myF->dateStyle($row['date'], '.');?></td>
							<td class="center"><?=$myF->dateStyle($row['contDate'], '.');?></td>
							<td class="right"><?=$row['y_count'];?></td>
							<td class="right"><?=$row['s_count'];?></td>
							<td class="last right"><?=$row['i_count'];?></td>
						</tr>
					<?
				}
				$conn->row_free();
			}else{
			?>
				<tr>
					<td class="last center" colspan="9">::검색된 데이타가 없습니다.::</td>
				</tr>
			<?
			}

		?>
		</tbody>
		</table>

		<input name="code"	type="hidden" value="<?=$mCode;?>">
		<input name="kind"	type="hidden" value="">
		<input name="page"	type="hidden" value="<?=$page;?>">
		<input name="jumin"	type="hidden" value="">
	</td>
</tr>
</table>

<div style="text-align:left; border:none;">
	<div style="border:none; position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($total_count);?></div>
	<div style="border:none; width:100%; text-align:center;">
	<?
		$paging = new YsPaging($params);
		$paging->printPaging();
	?>
</div>

</form>
<?
	include_once("../inc/_db_close.php");
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
<script language="javascript">
/*
_centerList(document.f.mCode.value, document.f.cName.value, document.f.branch.value,'');

// 센터조회
function _centerList(p_mCode, p_cName, p_branch, p_person){
	var URL = 'b2c_center_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_mCode,
				cName:p_cName,
				branch:p_branch,
				person:p_person
			},
			onSuccess:function (responseHttpObj) {
				document.getElementById('myBody').innerHTML = responseHttpObj.responseText;
			}
		}
	);
}
*/
// 선택한 지사의 담당자리스트
function _getPerson(p_branch){
	var target  = document.f.person;
	var URL = '../inc/_find_person_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				branch:p_branch
			},
			onSuccess:function (responseHttpObj) {
				var request = responseHttpObj.responseText;

				target.innerHTML = '';

				var list = request.split(';;');

				__setSelectBox(target, '', '-담당자-');

				for(var i=0; i<list.length - 1; i++){
					var value = list[i].split('//');

					__setSelectBox(target, value[0], value[1]);
				}
			}
		}
	);
}

function __setSelectBox(object, value, text){
	var option = null;

	option = document.createElement("option");
	option.value = value;
	option.text  = text;
	object.add(option);
}

function _b2c_center_list(page){
	var f = document.f;

	f.page.value = page;
	f.action = 'branch2center.php';
	f.submit();
}

function _brachExcel(){
	var f = document.f;

	f.action = 'branch_excel.php';
	f.submit();
}

</script>