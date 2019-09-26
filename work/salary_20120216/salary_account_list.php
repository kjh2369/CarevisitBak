<?
	if($amt_mode == 'excel'){
		include_once("../inc/_db_open.php");
	}else {
		include_once("../inc/_header.php");
		include_once("../inc/_http_uri.php");
		include_once("../inc/_page_list.php");
		include_once("../inc/_body_header.php");
		include_once("../inc/_ed.php");
	}
	include_once("../inc/_myFun.php");

	$code		= $_SESSION['userCenterCode'];
	$year		= $_POST['year']  != '' ? $_POST['year']  : intval(date('Y', mktime()));
	$month		= $_POST['month'] != '' ? $_POST['month'] : intval(date('m', mktime()));
	$month		= (intval($month) < 10 ? '0' : '').intval($month);

	$init_year = $myF->year();

	if($amt_mode == 'excel'){
		header( "Content-type: application/vnd.ms-excel" );
		header( "Content-type: charset=utf-8" );
		header( "Content-Disposition: attachment; filename=test.xls" );
		header( "Content-Description: test" );
		header( "Pragma: no-cache" );
		header( "Expires: 0" );
	}

if($amt_mode == 'excel'){
}else {	?>
	<script src="../js/work.js" type="text/javascript"></script>
	<script language='javascript'>
	<!--
	function set_month(month){
		var f = document.f;
		f.amt_mode.value = '';
		f.month.value = month;
		f.submit();
	}

	function excel(){

		var f = document.f;

		f.amt_mode.value = 'excel';
		f.submit();

	}
	-->
	</script>
<form name="f" method="post"><?
}

$sql = "select m00_cname
		  from m00center
		 where m00_mcode = '$code'";
$c_name = $conn->get_data($sql);

if($amt_mode == 'excel'){ ?>
	<div style="width:auto; font-size:15pt; font-weight:bold; text-align:center;"><?=$year?>년 <?=$month?>월 급여계좌이체대장</div>
	<div style="font-size:11pt;">기관명 : <?=$c_name?></div><?
}else { ?>
	<div class="title" style="width:auto; float:left;">급여계좌이체대장</div>
	<table class="my_table my_border">
		<colgroup>
			<col width="35px">
			<col width="40px">
			<col>
			<col width="150px">
		</colgroup>
		<tbody>
			<tr>
				<th class="center">년도</th>
				<td>
					<select name="year" style="width:auto;">
					<?
						for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
							<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?></option><?
						}
					?>
					</select>년
				</td>
				<td class="left last" colspan="5">
				<?
					for($i=1; $i<=12; $i++){
						$class = 'my_month ';

						if ($i == intval($month)){
							$class .= 'my_month_y ';
							$color  = 'color:#000000;';
						}else{
							$class .= 'my_month_1 ';
							$color  = 'color:#666666;';
						}

						$text   = '<a href="#" onclick="set_month('.$i.');">'.$i.'월</a>';

						if ($i == 12){
							$style = 'float:left;';
						}else{
							$style = 'float:left; margin-right:2px;';
						}?>
						<div class="<?=$class;?>" style="<?=$style;?>"><?=$text;?></div><?
					}
				?>
				</td>
				<td class="right last">
					<span class="btn_pack m icon"><span class="excel"></span><button type="button" onclick="excel();">엑셀</button></span>
				</td>
			</tr>
		</tbody>
	</table><?
}

if($amt_mode == 'excel'){ ?>
	<table class="my_table" border="1"><?
}else { ?>
	<table class="my_table" style="width:100%;"><?
}?>
	<colgroup>
		<col width="40px">
		<col width="90px">
		<col width="90px">
		<col width="90px">
		<col width="80px">
		<col width="105px">
		<col width="80px">
		<col width="75px">
		<col width="75px">
		<col width="75px">
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">성명</th>
			<th class="head" rowspan="2">생년월일</th>
			<th class="head" rowspan="2">연락처</th>
			<th class="head" colspan="3">이체은행</th>
			<th class="head" rowspan="2">급여합계</th>
			<th class="head" rowspan="2">공제합계</th>
			<th class="head last" rowspan="2">차인지급액</th>
		</tr>
		<tr>
			<th class="head">은행명</th>
			<th class="head">계좌번호</th>
			<th class="head">예금주</th>
		</tr>
	</thead>
	<tbody><?
		$sql = 'select code
				,      m_cd
				,      m_nm
				,      kind
				,	   m_tel
				,      m_acct
				,	   m_bank
				,	   m_no
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
				  from (
					   select m02_ccode as code
					   ,      min(m02_mkind) as kind
					   ,      m02_yjumin as m_cd
					   ,      m02_yname as m_nm
					   ,	  m02_ytel as m_tel
					   ,      m02_ybank_holder as m_acct
					   ,	  m02_ybank_name as m_bank
					   ,	  m02_ygyeoja_no as m_no
						 from m02yoyangsa
						where m02_ccode    = \''.$code.'\'
						  and left(m02_yipsail, 6) <= \''.$year.$month.'\'
						  and case when left(m02_ytoisail, 6) != \'\' then m02_ytoisail else \'999999\' end >= \''.$year.$month.'\'
						group by m02_ccode, m02_yjumin, m02_yname
					   ) as m
				 inner join salary_basic
					on salary_basic.org_no       = m.code
				   and salary_basic.salary_yymm  = \''.$year.$month.'\'
				   and salary_basic.salary_jumin = m.m_cd
				 order by m_nm';

		$conn -> query($sql);
		$conn -> fetch();
		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$html .= '<tr>
						<td class="center">'.($i+1).'</td>
						  <td class="left">'.$row['m_nm'].'</td>
						  <td class="center">'.$myF->issToBirthday($row['m_cd'],'.').'</td>
						  <td class="left">'.$myF->phoneStyle($row['m_tel'],'.').'</td>
						  <td class="left">'.$row['m_bank'].'</td>
						  <td class="left" style="mso-number-format:\@">'.$row['m_no'].'</td>
						  <td class="left">'.$row['m_acct'].'</td>
						  <td class="right">'.number_format($row['total_amt']).'</td>
						  <td class="right">'.number_format($row['deduct_amt']).'</td>
						  <td class="right last">'.number_format($row['total_amt']-$row['deduct_amt']).'</td>
					  </tr>';

			$total['total_amt']   += $row['total_amt'];
			$total['deduct_amt'] += $row['deduct_amt'];
		}

		if ($rowCount == 0){
			$html = '<tr><td class=\'center last\' colspan=\'10\'>'.$myF->message('nodata','N').'</td></tr>';
		}else{
			$html = '<tr>
						<td class="right sum" colspan="7">합계</td>
						<td class="right sum">'.number_format($total['total_amt']).'</td>
						<td class="right sum">'.number_format($total['deduct_amt']).'</td>
						<td class="right sum last">'.number_format($total['total_amt']-$total['deduct_amt']).'</td>
					 </tr>'.$html;
		}

		echo $html;
		?>
	</tbody>
	<?
		if($amt_mode != 'excel'){
			echo '<tfoot>
					<tr>
						<td class="bottom last"></td>
					</tr>
				  </tfoot>';
		}
	?>
</table><?
if($amt_mode == 'excel'){
}else {?>
	<input type="hidden" name="code"   value="<?=$code;?>">
	<input type="hidden" name="month"  value="<?=$month;?>">
	<input type="hidden" name="amt_mode"  value="">
	</form><?

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
}
?>