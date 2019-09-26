<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code  = $_SESSION['userCenterCode'];
	$year  = $_REQUEST['year'];
	$month = $_REQUEST['month'];

	#if (empty($year))  $year = date('Y', mktime());
	#if (empty($month)) $month = date('m', mktime());

	if (empty($year) || empty($month)){
		$sql = "select max(salary_yymm)
				  from salary_basic
				 where org_no = '$code'";
		$maxYYMM = $conn->get_data($sql);

		if (!empty($maxYYMM)){
			$year  = substr($maxYYMM, 0, 4);
			$month = substr($maxYYMM, 4, 2);
		}
	}else{
		$month = ($month < 10 ? '0' : '').intval($month);
	}

	$itemCnt = 20;
	$pageCnt = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

	$init_year = $myF->year();

	$showGbn = $_POST['showGbn'];

	if (empty($showGbn)) $showGbn = 'N';
?>

<script src="../js/work.js" type="text/javascript"></script>
<script src="./salary.js" type="text/javascript"></script>
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

-->
</script>

<form name="f" method="post">

<div class="title" style="width:auto; float:left;">급여조정</div>
<div style="width:auto; font-weight:bold; margin-top:8px; text-align:right;">(급여조정은 급여일괄계산 후 가능합니다.)</div>

<table class="my_table my_border">
	<colgroup>
		<col width="35px">
		<col width="40px">
		<col width="35px">
		<col width="470px">
		<col>
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
						   and salary_yymm like '$year%'";

				$conn->query($sql);
				$conn->fetch();
				$row_count = $conn->row_count();
				$row_i = 0;

				for($i=1; $i<=12; $i++){
					if ($row_i < $row_count){
						$row = $conn->select_row($row_i);
					}else{
						$row = null;
					}

					$class = 'my_month ';

					if (intval($i) == intval($row[0])){
						$row_i ++;
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
						//$link	= '<a style="cursor:default;"><span style="color:#7c7c7c;">'.$i.'월</span></a>';
						$link	= '<a href="#" onclick="alert(\''.$year.'년 '.$i.'월은 아직 급여일괄계산이 실행되지 않았습니다.\');" style="color:#7c7c7c;">'.$i.'월</a>';
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
				<div style="float:right; width:auto; margin-left:5px;"><span class="btn_pack m icon"><span class="pdf"></span><button id="btnReceipt" type="button" onclick="_payslip(document.f.code.value, document.f.kind.value, document.f.year.value, document.f.month.value, document.f.member.value);">명세서</button></span></div>
				<div style="float:right; width:auto;"><span class="btn_pack m"><button id="btnShowGbn" type="button" onclick="$('#showGbn').val('<?=$showGbn != 'Y' ? 'Y' : 'N';?>'); list($('#page').val(), $('#month').val());"><?=$showGbn != 'Y' ? '전체 조회' : '급여대상자 조회';?></button></span></div>
				<!--span class="btn_pack m icon"><span class="excel"></span><button type="button" onclick="alert('준비중입니다.');">엑셀</button></span-->
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
			<th class="head last">(휴,야,목,간,비)</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = 'select count(*)
				  from (
					   select m02_ccode as code
					   ,      min(m02_mkind) as kind
					   ,      m02_yjumin as m_cd
					   ,      m02_yname as m_nm
						 from m02yoyangsa
						where m02_ccode    = \''.$code.'\'
						  and left(m02_yipsail, 6) <= \''.$year.$month.'\'
						  and case when left(m02_ytoisail, 6) != \'\' then m02_ytoisail else \'999999\' end >= \''.$year.$month.'\'
						group by m02_ccode, m02_yjumin, m02_yname
					   ) as m
				  '.($showGbn == 'Y' ? 'left' : 'inner').' join salary_basic
					on salary_basic.org_no       = m.code
				   and salary_basic.salary_yymm  = \''.$year.$month.'\'
				   and salary_basic.salary_jumin = m.m_cd';

		$totalCnt = $conn->get_data($sql);


		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($totalCnt < (intVal($page) - 1) * $itemCnt) $page = 1;

		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:list',
			'curPageNum'	=> $page,
			'pageVar'		=> 'page',
			'extraVar'		=> '',
			'totalItem'		=> $totalCnt,
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

		$pageCount = $page;

		if ($pageCount == ""){
			$pageCount = 1;
		}

		$pageCount = (intVal($pageCount) - 1) * $itemCnt;

		$sql = 'select code as code
				,      m_cd as m_cd
				,      m_nm as m_nm
				,      kind as kind
				,      work_cnt as work_cnt
				,      work_time as work_time
				,      weekly_pay
						+ paid_pay
						+ bath_pay
						+ nursing_pay
						+ prolong_pay
						+ night_pay
						+ holiday_pay
						+ holiday_prolong_pay
						+ holiday_night_pay
						+ base_pay
						+ meal_pay
						+ car_keep_pay
						+ bojeon_pay
						+ rank_pay
						+ expense_pay
						+ year_pay
						+ (select ifnull(sum(salary_pay), 0)
							 from salary_addon_pay
							where org_no       = m.code
							  and salary_yymm  = \''.$year.$month.'\'
							  and salary_jumin = m.m_cd
							  and salary_type = \'1\') as total_amt
				,      pension_amt
						+ health_amt
						+ care_amt
						+ employ_amt
						+ tax_amt_1
						+ tax_amt_2
						+ (select ifnull(sum(salary_pay), 0)
							 from salary_addon_pay
							where org_no       = m.code
							  and salary_yymm  = \''.$year.$month.'\'
							  and salary_jumin = m.m_cd
							  and salary_type = \'2\') as deduct_amt
				,      base_pay as base_pay
				,      weekly_pay as weekly_pay
				,      bojeon_pay as bojeon_pay
				,      bath_pay
					 + nursing_pay
					 + prolong_pay
					 + night_pay
					 + holiday_pay
					 + holiday_prolong_pay
					 + holiday_night_pay
					 + expense_pay as sudang_pay
				  from (
					   select m02_ccode as code
					   ,      min(m02_mkind) as kind
					   ,      m02_yjumin as m_cd
					   ,      m02_yname as m_nm
						 from m02yoyangsa
						where m02_ccode    = \''.$code.'\'
						  and left(m02_yipsail, 6) <= \''.$year.$month.'\'
						  and case when left(m02_ytoisail, 6) != \'\' then m02_ytoisail else \'999999\' end >= \''.$year.$month.'\'
						group by m02_ccode, m02_yjumin, m02_yname
					   ) as m
				  '.($showGbn == 'Y' ? 'left' : 'inner').' join salary_basic
					on salary_basic.org_no       = m.code
				   and salary_basic.salary_yymm  = \''.$year.$month.'\'
				   and salary_basic.salary_jumin = m.m_cd
				 order by m_nm
				 limit '.$pageCount.','.$itemCnt;

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				if ($row['total_amt'] > 0){
					$link  = '<a href="#" onclick="_salary_edit(\''.$row['kind'].'\',\''.$ed->en($row['m_cd']).'\');">'.$row['m_nm'].'</a>';
					$style = '';
				}else{
					$link  = '<span style="cursor:default;">'.$row['m_nm'].'</span>';
					$style = 'background-color:#f7f7f7;';
				}

				$html .= '<tr>
							<td class="center" style="'.$style.'">'.($pageCount + ($i + 1)).'</td>
							<td class="left" style="'.$style.'">'.$link.'</td>
							<td class="right" style="'.$style.'">'.number_format($row['work_cnt']).'</td>
							<td class="right" style="'.$style.'">'.number_format($row['work_time']).'</td>
							<td class="right" style="'.$style.'">'.number_format($row['total_amt']).'</td>
							<td class="right" style="'.$style.'">'.number_format($row['deduct_amt']).'</td>
							<td class="right" style="'.$style.'">'.number_format($row['total_amt']-$row['deduct_amt']).'</td>
							<td class="right" style="'.$style.'">'.number_format($row['base_pay']).'</td>
							<td class="right" style="'.$style.'">'.number_format($row['weekly_pay']).'</td>
							<td class="right" style="'.$style.'">'.number_format($row['bojeon_pay']).'</td>
							<td class="right last" style="'.$style.'">'.number_format($row['sudang_pay']).'</td>
						  </tr>';

				$total['work_cnt']   += $row['work_cnt'];
				$total['work_time']  += $row['work_time'];
				$total['total_amt']  += $row['total_amt'];
				$total['deduct_amt'] += $row['deduct_amt'];
				$total['diff_amt']   += $row['diff_amt'];
				$total['base_pay']   += $row['base_pay'];
				$total['weekly_pay'] += $row['weekly_pay'];
				$total['bojeon_pay'] += $row['bojeon_pay'];
				$total['sudang_pay'] += $row['sudang_pay'];
			}
		}else{
			$html = '<tr>
						<td class="center last" colspan="11">'.$myF->message('nodata','N').'</td>
					 </tr>';
		}

		if ($row_count > 0){
			$html = '<tr>
						<td class="center sum" colspan="2">합계</td>
						<td class="right sum">'.number_format($total['work_cnt']).'</td>
						<td class="right sum">'.number_format($total['work_time']).'</td>
						<td class="right sum">'.number_format($total['total_amt']).'</td>
						<td class="right sum">'.number_format($total['deduct_amt']).'</td>
						<td class="right sum">'.number_format($total['total_amt']-$total['deduct_amt']).'</td>
						<td class="right sum">'.number_format($total['base_pay']).'</td>
						<td class="right sum">'.number_format($total['weekly_pay']).'</td>
						<td class="right sum">'.number_format($total['bojeon_pay']).'</td>
						<td class="right sum">'.number_format($total['sudang_pay']).'</td>
					 </tr>'.$html;
		}

		$conn->row_free();

		echo $html;
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="bottom last" colspan="11">
				<div style="text-align:left;">
					<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($totalCnt);?></div>
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

<input type="hidden" id="code" name="code"   value="<?=$code;?>">
<input type="hidden" id="kind" name="kind"   value="">
<input type="hidden" id="month" name="month"  value="<?=$month;?>">
<input type="hidden" id="jumin" name="jumin"  value="">
<input type="hidden" id="page" name="page"   value="<?=$page;?>">
<input type="hidden" id="member" name="member" value="<?=$ed->en('all');?>">
<input type="hidden" id="showGbn" name="showGbn" value="<?=$showGbn;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>