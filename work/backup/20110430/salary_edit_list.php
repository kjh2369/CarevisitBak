<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code  = $_SESSION['userCenterCode'];
	$year  = $_REQUEST['year']  != '' ? $_REQUEST['year']  : date('Y', mktime());

	$sql = "select ifnull(max(cast(right(salary_yymm, 2) as unsigned)), 0)
			  from salary_basic
			 where org_no = '$code'";
	$max_month = $conn->get_data($sql);

	$month = $_REQUEST['month'] != '' ? $_REQUEST['month'] : ($max_month > 0 ? $max_month : date('m', mktime()));
	$month = ($month < 10 ? '0' : '').intval($month);

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

	$init_year = $myF->year();
?>

<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function search(){
	var f = document.f;

	f.submit();
}

function list(p_page, p_month){
	var f = document.f;

	if (p_month != undefined) f.month.value = p_month;

	f.page.value  = p_page;

	f.submit();
}

function salary_edit(p_kind, p_jumin){
	var f = document.f;

	f.kind.value  = p_kind;
	f.jumin.value = p_jumin;
	f.action = 'salary_edit_2.php';
	f.submit();
}
-->
</script>

<form name="f" method="post">

<div class="title">급여조정</div>

<table class="my_table my_border">
	<colgroup>
		<col width="35px">
		<col width="40px">
		<col width="35px">
		<col span="2">
	</colgroup>
	<tbody>
		<tr>
			<th>년도</th>
			<td>
				<select name="year" style="width:auto;" onchange="list(1, <?=intval(date('m', mktime()));?>);">
				<?
					for($i=$init_year[0]; $i<=$init_year[1]; $i++){ ?>
						<option value="<?=$i;?>" <? if($year == $i){?>selected<?} ?>><?=$i;?></option><?
					}
				?>
				</select>
			</td>
			<th>월별</th>
			<td class="left last">
			<?
				$sql = "select distinct cast(right(salary_yymm, 2) as unsigned)
						  from salary_basic
						 where org_no = '$code'
						   and salary_yymm like '$find_year%'";
				$conn->query($sql);
				$conn->fetch();
				$row_count = $conn->row_count();

				for($i=1; $i<=12; $i++){
					if ($i - 1 < $row_count){
						$row = $conn->select_row($i-1);
					}else{
						$row = null;
					}

					$class = 'my_month ';

					if ($i == $row[0]){
						if ($i == intval($month)){
							$class .= 'my_month_y ';
						}else{
							$class .= 'my_month_g ';
						}
						$link	= '<a href="#" onclick="list('.$page.','.$i.');">'.$i.'월</a>';
					}else{
						if ($i == intval($month)){
							$class .= 'my_month_y ';
						}else{
							$class .= 'my_month_1 ';
						}
						$link	= '<a style="cursor:default;"><span style="color:#7c7c7c;">'.$i.'월</span></a>';
					}

					$margin_right = '2px';

					if ($i == 12){
						$margin_right = '0';
					}?>
					<div class="<?=$class;?>" style="float:left; margin-right:<?=$margin_right;?>;"><?=$link;?></div><?
				}

				$conn->row_free();
			?>
			</td>
			<td class="right last">
				<span class="btn_pack m icon"><span class="pdf"></span><button type="button" onclick="_payslip(document.f.code.value, document.f.kind.value, document.f.year.value, document.f.month.value, document.f.member.value);">명세서</button></span>
				<span class="btn_pack m icon"><span class="excel"></span><button type="button" onclick="alert('준비중입니다.');">엑셀</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%; border-bottom:none;">
	<colgroup>
		<col width="40px">
		<col width="101px">
		<col width="50px">
		<col width="50px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">요양보호사</th>
			<th class="head" rowspan="2">근무<br>일수</th>
			<th class="head" rowspan="2">근무<br>시간</th>
			<th class="head" rowspan="2">총급여액</th>
			<th class="head" rowspan="2">공제금액</th>
			<th class="head" rowspan="2">실수령액</th>
			<th class="head" rowspan="2">기본급</th>
			<th class="head" rowspan="2">주휴</th>
			<th class="head" rowspan="2">보전수당</th>
			<th class="head last">수당</th>
		</tr>
		<tr>
			<th class="head last">(휴,야,목,간)</th>
		</tr>
	</thead>
	<tbody>
	<?
		$wsl = "where salary_basic.org_no      = '$code'
		          and salary_basic.salary_yymm = '$year$month'";

		$sql = "select count(distinct salary_basic.salary_jumin)
				  from salary_basic $wsl";

		$total_count = $conn->get_data($sql);

		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:list',
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
			$pageCount = 1;
		}

		$pageCount = (intVal($pageCount) - 1) * $item_count;

		$sql = "select salary_basic.salary_jumin as jumin
				,      m02_yname as name
				,      m02_mkind as kind
				,      salary_basic.work_cnt as work_cnt
				,      salary_basic.work_time as work_time
				,      salary_amt.total_amt as total_amt
				,      salary_amt.deduct_amt as deduct_amt
				,      salary_amt.diff_amt as diff_amt
				,      salary_basic.base_pay as base_pay
				,      salary_basic.weekly_pay as weekly_pay
				,      salary_basic.bojeon_pay as bojeon_pay
				,      salary_basic.bath_pay +
					   salary_basic.nursing_pay +
					   salary_basic.prolong_pay +
					   salary_basic.night_pay +
					   salary_basic.holiday_pay +
					   salary_basic.holiday_prolong_pay +
					   salary_basic.holiday_night_pay as sudang_pay
				  from salary_basic
				 inner join salary_amt
					on salary_amt.org_no       = salary_basic.org_no
				   and salary_amt.salary_yymm  = salary_basic.salary_yymm
				   and salary_amt.salary_jumin = salary_basic.salary_jumin
				 inner join m02yoyangsa
					on m02_ccode  = salary_basic.org_no
				   and m02_yjumin = salary_basic.salary_jumin $wsl
				 order by name, jumin
				 limit $pageCount, $item_count";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);?>
				<tr>
					<td class="center"		><?=$pageCount + ($i + 1);?></td>
					<td class="left"		><a href="#" onclick="salary_edit('<?=$row['kind'];?>','<?=$ed->en($row['jumin']);?>');"><?=$row['name'];?></a></td>
					<td class="right"		><?=$row['work_cnt'];?></td>
					<td class="right"		><?=$row['work_time'];?></td>
					<td class="right"		><?=number_format($row['total_amt']);?></td>
					<td class="right"		><?=number_format($row['deduct_amt']);?></td>
					<td class="right"		><?=number_format($row['diff_amt']);?></td>
					<td class="right"		><?=number_format($row['base_pay']);?></td>
					<td class="right"		><?=number_format($row['weekly_pay']);?></td>
					<td class="right"		><?=number_format($row['bojeon_pay']);?></td>
					<td class="right last"	><?=number_format($row['sudang_pay']);?></td>
				</tr><?
			}
		}else{?>
			<tr>
				<td class="center last" colspan="11">::<?=$myF->message('nodata','N');?>::</td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="bottom last" colspan="11">
				<div style="text-align:left;">
					<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($total_count);?></div>
					<div style="width:100%; text-align:center;"><?
					if ($row_count > 0){
						$paging = new YsPaging($params);
						$paging->printPaging();
					}?>
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code"   value="<?=$code;?>">
<input type="hidden" name="kind"   value="">
<input type="hidden" name="month"  value="<?=$month;?>">
<input type="hidden" name="jumin"  value="">
<input type="hidden" name="page"   value="<?=$page;?>">
<input type="hidden" name="member" value="<?=$ed->en('all');?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>