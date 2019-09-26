<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$mCode = $_POST['mCode'];
	$cName = $_POST['cName'];
	$branch = $_POST['branch'];
	$person = $_POST['person'];

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

?>

<table class="my_table my_border">
<colgroup>
	<col width="20px;">
	<col width="150px;">
	<col width="60px;">
	<col width="70px;">
	<col width="200px;">
	<col width="60px;">
	<col width="65px;">
	<col>
</colgroup>
<thead>
	<tr>
		<th class="center"><input name="checkAll" type="checkbox" class="checkbox" onClick="__checkMyValue('check[]', this.checked);"></th>
		<th class="center">지사명</th>
		<th class="center">담당자명</th>
		<th class="center">기관코드</th>
		<th class="center">기관명</th>
		<th class="center">대표자명</th>
		<th class="center">연결일자</th>
		<th class="center">직원/수급자/일정</th>
	</tr>
</thead>
<tbody>


<?
	$sql = "select count(*)
			from b02center
		   inner join b00branch
		      on b00_code = b02_branch
		   inner join b01person
			  on b01_branch = b02_branch
		     and b01_code   = b02_person
		   inner join m00center
			  on m00_mcode = b02_center
		     and m00_mkind = b02_kind
		   where b02_branch != ''";

		if ($mCode != ''){
			$sql .= " and b02_center like '%$mCode%'";
		}
		if ($cName != ''){
			$sql .= " and m00_strore_nm like '%$cName%'";
		}
		if ($branch != ''){
			$sql .= " and b02_branch = '$branch'";
		}
		if ($person != ''){
			$sql .= " and b02_person = '$person'";
		}
			$sql .= " order by b02_branch, b01_name";

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
			,      m00_strore_nm as centerName
			,      m00_mkind as centerKind
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
			,     (select count(distinct t01_jumin)
					 from t01iljung
					where t01_ccode  = b02_center
					  and t01_mkind  = b02_kind
					  and t01_del_yn = 'N'
					  and t01_sugup_date like '$date%') as i_count
			  from b02center
			 inner join b00branch
			    on b00_code = b02_branch
			 inner join b01person
			    on b01_branch = b02_branch
			   and b01_code   = b02_person
			 inner join m00center
			    on m00_mcode = b02_center
			   and m00_mkind = b02_kind
			 where b02_branch != ''";


		if ($mCode != ''){
		$sql .= " and b02_center like '%$mCode%'";
		}
		if ($cName != ''){
		$sql .= " and m00_strore_nm like '%$cName%'";
		}
		if ($branch != ''){
		$sql .= " and b02_branch = '$branch'";
		}
		if ($person != ''){
			$sql .= " and b02_person = '$person'";
		}
		$sql .= " order by b02_branch, b01_name";

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
					<td>
						<?=$row['y_count'];?> /
						<?=$row['s_count'];?> /
						<?=$row['i_count'];?>
					</td>
				</tr>
			<?
		}
		$conn->row_free();
	}else{
	?>
		<tr>
			<td class="center" colspan="9">::검색된 데이타가 없습니다.::</td>
		</tr>
	<?
	}

	echo $total_conunt;
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
<input name="code"	type="hidden" value="<?=$find_center_code;?>">
<input name="kind"	type="hidden" value="">
<input name="page"	type="hidden" value="<?=$page;?>">
<input name="jumin"	type="hidden" value="">

<?
	include_once("../inc/_db_close.php");
?>
<script language="javascript">
	function _b2c_center_list(page){
	var f = document.f;

	f.page.value = page;
	f.action = 'b2c_center_list.php';
	f.submit();
	}
</script>
