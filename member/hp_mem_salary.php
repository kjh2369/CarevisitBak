<?
	
	include_once("../inc/_db_open.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_mySalary.php");
	include_once("../inc/_ed.php");
	include_once('../salaryNew/salary_const.php');
	
	$gHome = '..';

	$code  = $_GET['code'];
	$year  = $_GET['year'];
	$month = ($_GET['month'] < 10 ? '0'.$_GET['month'] : $_GET['month']);
	$jumin  = $ed->de($_GET['jumin']);
	
	
	// 4대보험 요율 정보
	include_once('../salaryNew/salary_ins.php');

	// 급여정보
	include_once('../salaryNew/salary_pay_list.php');

	// 마감처리여부
	$close_yn = $conn->get_closing_salary($code, $year.$month);

	$m_age = $myF->man_age($jumin);


	if ($close_yn != 'Y'){
		$edit_mode = 1;
	}else{
		$edit_mode = 2;
	}

	/*
	//개인회원급여조회
	if ($_SESSION['userLevel'] == 'P'){
		$edit_mode = 3;
		$title = '급여내역';
	}else {
		$title = '급여조정';
	}
	*/
	$title = '급여조정';


	//포괄임금제 적용여부
	$sql = 'SELECT	extra_yn
			,		deal_bipay_yn
			,		deal_hourly_yn
			FROM	salary_option
			WHERE	org_no			= \''.$code.'\'
			AND		salary_yymm		= \''.$year.$month.'\'
			AND		salary_jumin	= \''.$jumin.'\'';

	$row	= $conn->get_array($sql);

	$lsExtraYn		= $row['extra_yn'];
	$lsDealBipayYn	= $row['deal_bipay_yn'];	//처우개선비 비급여 처리 여부
	$lsDealHourlyYn	= $row['deal_hourly_yn'];	//처우개선비 시급포함 여부

	if ($lsDealBipayYn == 'Y'){
		$lsDealType	= 'B';
	}else if ($lsDealHourlyYn == 'Y'){
		$lsDealType	= 'H';
	}else if ($lsDealHourlyYn == 'X'){
		$lsDealType	= 'X';
	}

	if ($lsExtraYn != 'Y'){
		$lsExtraYn  = 'N';
	}

	//주 5,6일근무
	$sql = 'SELECT m00_fixed_days
			  FROM m00center
			 WHERE m00_mcode  = \''.$code.'\'
			   AND m00_del_yn = \'N\'
			 LIMIT 1';

	$tmpFixedDays = $conn->get_data($sql);

	//주5일제는 평균근로일수 20일 6일제 평균근로일수 24일로 계산실행
	if ($tmpFixedDays == 5){
		$liFixedWordDays = 20;
	}else{
		$liFixedWordDays = 24;
	}

	/*********************************************************

		연차 사용 기관정보

	*********************************************************/
		$sql = 'select m00_weeklyin_yn as weekly
				,      m00_annual_yn as yn
				,      m00_annualin_yn as inYn
				  from m00center
				 where m00_mcode = \''.$code.'\'
				 limit 1';

		$tmpArray = $conn->get_array($sql);
		$annualInPayYN = $tmpArray['inYn'];
		$weeklyInPayYN = $tmpArray['weekly'];
		unset($tmpArray);



	$sql = "select m02_yname as name
			,      m02_rank_pay as pay
			,      m02_ykmbohum_umu as annuity_yn
			,      m02_ykuksin_mpay as annuity_pay
			  from m02yoyangsa
			 where m02_ccode  = '$code'
			   and m02_yjumin = '$jumin'
			 order by m02_mkind
			 limit 1";

	$member = $conn->get_array($sql);

	$name	  = $member['name'];
	$rank_pay = $member['pay'];
	$liAnnuityYn  = $member['annuity_yn'];
	$liAnnuityPay = $member['annuity_pay'];

	$sql = "select salary_basic.work_cnt
			,      salary_basic.work_time
			,      salary_basic.weekly_cnt
			,      salary_basic.annual_days
			,      salary_basic.paid_cnt
			,      salary_basic.bath_cnt
			,      salary_basic.nursing_cnt

			,      salary_basic.prolong_hour
			,      salary_basic.night_hour
			,      salary_basic.holiday_hour
			,      salary_basic.holiday_prolong_hour
			,      salary_basic.holiday_night_hour
			,      salary_basic.prolong_hour + salary_basic.night_hour + salary_basic.holiday_hour + salary_basic.holiday_prolong_hour + salary_basic.holiday_night_hour as tot_sudang_hour

			,      salary_basic.base_pay
			,      salary_basic.base_max
			,      salary_basic.weekly_pay
			,      salary_basic.annual_pay
			,      salary_basic.paid_pay
			,      salary_basic.bath_pay
			,      salary_basic.nursing_pay
			,      salary_basic.meal_pay
			,      salary_basic.car_keep_pay
			,      salary_basic.bojeon_pay";

	$sql .= ",      salary_basic.meal_pay + salary_basic.car_keep_pay + salary_basic.bojeon_pay ".($weeklyInPayYN == 'Y' ? "+ salary_basic.weekly_pay" : "").($annualInPayYN == 'Y' ? "+ salary_basic.annual_pay" : "")." as bojeon_max";

	$sql .= ",     salary_basic.base_pay
				 + salary_basic.weekly_pay
				 + salary_basic.annual_pay
				 + salary_basic.meal_pay
				 + salary_basic.car_keep_pay
				 + salary_basic.bojeon_pay
				 + salary_basic.paid_pay
				 + salary_basic.bath_pay
				 + salary_basic.nursing_pay
				 + salary_basic.dementia_pay
				 + ".($lsDealBipayYn != "Y" && $lsDealHourlyYn != "Y" ? "salary_basic.deal_pay" : "0")." as tot_basic_pay";

	//주휴 및 연차의 시급에 포함 여부에 따른 보전수당금액
	//$sql .= ",      salary_basic.meal_pay + salary_basic.car_keep_pay + salary_basic.bojeon_pay ".($weeklyInPayYN == 'Y' ? "+ salary_basic.weekly_pay" : "").($annualInPayYN == 'Y' ? "+ salary_basic.annual_pay" : "")." as bojeon_max";

	//$sql .= ",     salary_basic.base_pay + salary_basic.weekly_pay + salary_basic.annual_pay + salary_basic.meal_pay + salary_basic.car_keep_pay + salary_basic.bojeon_pay + salary_basic.paid_pay + salary_basic.bath_pay + salary_basic.nursing_pay as tot_basic_pay";

	$sql .=",      salary_basic.prolong_pay
			,      salary_basic.night_pay
			,      salary_basic.holiday_pay
			,      salary_basic.holiday_prolong_pay
			,      salary_basic.holiday_night_pay
			,      salary_basic.rank_pay
			,      salary_basic.prolong_pay + salary_basic.night_pay + salary_basic.holiday_pay + salary_basic.holiday_prolong_pay + salary_basic.holiday_night_pay as tot_sudang_pay

			,      salary_basic.pension_amt
			,      salary_basic.health_amt
			,      salary_basic.care_amt
			,      salary_basic.employ_amt
			,      salary_basic.pension_amt + salary_basic.health_amt + salary_basic.care_amt + salary_basic.employ_amt as tot_ins_pay

			,      salary_basic.tax_amt_1
			,      salary_basic.tax_amt_2
			,      salary_basic.tax_amt_1 + salary_basic.tax_amt_2 as tot_tax_pay

			,      salary_amt.basic_total_amt
			,      salary_amt.addon_total_amt
			,      salary_amt.total_amt
			,      salary_amt.basic_deduct_amt
			,      salary_amt.addon_deduct_amt
			,      salary_amt.deduct_amt
			,      salary_amt.diff_amt

			,      salary_basic.expense_days
			,      salary_basic.expense_hours
			,      salary_basic.expense_pay

			,      salary_basic.bipay_addpay
			,	   salary_basic.real_pay

			,	   salary_basic.paye_yn

			,      salary_other.min_wages_pay

			,      salary_basic.deal_pay
			,		salary_basic.dementia_pay

			  from salary_basic

			  left join salary_amt
				on salary_amt.org_no       = salary_basic.org_no
			   and salary_amt.salary_yymm  = salary_basic.salary_yymm
			   and salary_amt.salary_jumin = salary_basic.salary_jumin

			  left join salary_other
			    on salary_other.org_no       = salary_basic.org_no
			   and salary_other.salary_yymm  = salary_basic.salary_yymm
			   and salary_other.salary_jumin = salary_basic.salary_jumin
			 where salary_basic.org_no       = '$code'
			   and salary_basic.salary_yymm  = '$year$month'
			   and salary_basic.salary_jumin = '$jumin'";

	$salary = $conn->get_array($sql);

	if ($lsExtraYn == 'Y'){
		$salary['tot_basic_pay'] += $salary['tot_sudang_pay'];
	}

	/*********************************************************

		주휴 최대 갯수

	*********************************************************/
		$sql = 'select salary_weekly
				  from salary_mem_data
				 where org_no        = \''.$code.'\'
				   and salary_yymm   = \''.$year.$month.'\'
				   and salary_jumin  = \''.$jumin.'\'
				   and salary_use_yn = \'Y\'';

		$intWeekly = $conn->get_data($sql);

		if (is_null($intWeekly)){
			$sql = 'select m02_weekly_holiday
					  from m02yoyangsa
					 where m02_ccode  = \''.$code.'\'
					   and m02_yjumin = \''.$jumin.'\'
					   and m02_del_yn = \'N\'
					 limit 1';

			$intWeekly = $conn->get_data($sql);
		}

		$intWeekdayCnt = sizeof($myF->sunday_list($year, $month, $intWeekly));


	/*********************************************************

		연차 사용가능한 최대 갯수

	*********************************************************/
		$sql = 'select sum(annual_days) as days
				 from salary_basic
				where org_no               = \''.$code.'\'
				  and left(salary_yymm, 4) = \''.$year.'\'
				  and salary_jumin         = \''.$jumin.'\'';
		$annualUseCnt  = $conn->get_data($sql);
		$annualUseCnt -= $salary['annual_days'];

		if ($annualUseCnt < 0) $annualUseCnt = 0;

		$annualMaxCnt = 15 - $annualUseCnt;



	//과세
	$sql = "select sum(salary_pay)
			  from salary_addon_pay
			 where org_no       = '$code'
			   and salary_yymm  = '$year$month'
			   and salary_jumin = '$jumin'
			   and salary_tax   != 'Y'";
	$taxPay = $conn -> get_data($sql);
	
	//비과세
	$sql = "select sum(salary_pay)
			  from salary_addon_pay
			 where org_no       = '$code'
			   and salary_yymm  = '$year$month'
			   and salary_jumin = '$jumin'
			   and salary_tax   = 'Y'";
	$unTaxPay = $conn -> get_data($sql);
	

?>

<?

if($_GET['homepage'] == 'fw'){ 
	$cl = '#82bf41';
	$cl2 = '#4c9400';
?>
	<style>
		.write_type,.write_type th,.write_type td{border:0;}  
		.write_type{width:100%; border-top:2px solid #4c9400; table-layout:fixed;}  
		.write_type caption{display:none}
		.write_type th{padding:5px 0 5px 0px; border-bottom:1px solid #82bf41; border-right:1px solid #82bf41; background:#92d050 ; color:#000; font-weight:bold; text-align:left; font-weight:normal;}
		.write_type th {text-align:center;}
		.write_type th label	{display:block;}
		.write_type th img{vertical-align:middle;}
		.write_type td{padding:5px 0 4px 15px; border-bottom:1px solid #82bf41;}  
		.write_type td select{border:1px solid #cccccc; vertical-align:middle; height:20px; line-height:18px; _height:16px; font-size:12px; padding:2px;}
		.t_right { text-align:right; padding-right:10px !important;}
	</style>
<?
}else {
	$cl = '#cccccc';
	$cl2 = '#cccccc'; ?>
 <?
}


if (($lsDealBipayYn == 'Y' || $lsDealHourlyYn == 'Y') && $salary['deal_pay'] > 0){
	$div_deal = '<div style="width:auto; text-align:right; padding:10px; float:left; font-weight:bold;">
			※급여에 <span style="color:#0000ff;">처우개선비</span>(<span style="color:#ff0000;"><?=number_format($salary[deal_pay]);?></span>)가 포함되어 있습니다.
		</div>';
}

$html = '';

$html .= '<input name="jumin" id="jumin" type="hidden" value="'.$ed->en($jumin).'">';

$html .= '<table class="write_type" style="width:100%;">
			<colgroup>
				<col width="45px">
				<col width="45px">
				<col width="65px">
				<col width="60px">
				<col width="60px">
				<col width="60px">
				<col width="60px">
				<col width="60px">
				<col width="60px">
				<col width="60px">
				<col width="60px">
				<col>
			</colgroup>
			<tr>
				<th>구분</th>
				<th colspan="11" style="text-align:center;">기본근무(기본급여)</th>
			</tr>
			<tr>
				<th rowspan="2">횟수<br/>시간</th>
				<th>근무</br>일수</th>
				<th>근무</br>시간</th>
				<th>주휴</br>일수</th>
				<th>연차</br>일수</th>
				<th>식대</br>보조</th>
				<th>차량</br>유지비</th>
				<th>업무</br>수당</th>
				<th>유급</br>일수</th>
				<th>목욕</br>횟수</th>
				<th>간호</br>횟수</th>
				<th>합계(A)</th>
			</tr>
			<tr>
				<td class="t_right">'.$salary['work_cnt'].'</td>
				<td class="t_right">'.$salary['work_time'].'</td>
				<td class="t_right">'.$salary['weekly_cnt'].'</td>
				<td class="t_right">'.$salary['annual_days'].'</td>
				<td>-</td>
				<td>-</td>
				<td>-</td>
				<td class="t_right">'.$salary['paid_cnt'].'</td>
				<td class="t_right">'.$salary['bath_cnt'].'</td>
				<td class="t_right">'.$salary['nursing_cnt'].'</td>
				<td>-</td>
			</tr>
			<tr>
				<th>금액</th>
				<td>-</td>
				<td class="t_right">'.number_format($salary['base_pay']).'</td>
				<td class="t_right">'.number_format($salary['weekly_pay']).'</td>
				<td class="t_right">'.number_format($salary['annual_pay']).'</td>
				<td class="t_right">'.number_format($salary['meal_pay']).'</td>
				<td class="t_right">'.number_format($salary['car_keep_pay']).'</td>
				<td class="t_right">'.number_format($salary['bojeon_pay']).'</td>
				<td class="t_right">'.number_format($salary['paid_pay']).'</td>
				<td class="t_right">'.number_format($salary['bath_pay']).'</td>
				<td class="t_right">'.number_format($salary['nursing_pay']).'</td>
				<td rowspan="3">'.number_format($salary['tot_basic_pay']).'</td>
			</tr>
			<tr>
				<th colspan="8" rowspan="2">'.$div_deal.'</th>
				<th>처우</br>개선비</th>
				<th>근무</br>수당</th>
				<th>치매</br>수당</th>
			</tr>
			<tr>
				<td class="t_right">'.number_format($salary['deal_pay']).'</td>
				<td class="t_right">'.number_format($salary['tot_sudang_pay']).'</td>
				<td class="t_right">'.number_format($salary['dementia_pay']).'</td>
			</tr>
		</table>';
	
	$html .= '<table class="write_type" style="width:100%; margin-top:10px;">
				<colgroup>
					<col width="45px">
					<col width="45px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col>
				</colgroup>
				<tr>
					<th rowspan="2">구분</th>
					<th colspan="5">초과근무</th>
					<th rowspan="2">합계</th>
					<th colspan="4">보험항목</th>
					<th rowspan="2">합계(C)</th>
				</tr>
				<tr>
					<th>연장</th>
					<th>야간</th>
					<th>휴일</th>
					<th>휴일연장</th>
					<th>휴일야간</th>
					<th>국민연금</th>
					<th>건강보험</th>
					<th>장기요양</th>
					<th>고용보험</th>
				</tr>
				<tr>
					<th>시간</th>
					<td class="t_right">'.$salary['prolong_hour'].'</td>
					<td class="t_right">'.$salary['night_hour'].'</td>
					<td class="t_right">'.$salary['holiday_hour'].'</td>
					<td class="t_right">'.$salary['holiday_prolong_hour'].'</td>
					<td class="t_right">'.$salary['holiday_night_hour'].'</td>
					<td>-</td>
					<td class="t_right">'.$ins_rate['worker_annuity'].'</td>
					<td class="t_right">'.$ins_rate['worker_health'].'</td>
					<td class="t_right">'.$ins_rate['worker_oldcare'].'</td>
					<td class="t_right">'.$ins_rate['worker_employ'].'</td>
					<td>-</td>
				</tr>
				<tr>
					<th>금액</th>
					<td class="t_right">'.$salary['prolong_pay'].'</td>
					<td class="t_right">'.$salary['night_pay'].'</td>
					<td class="t_right">'.$salary['holiday_pay'].'</td>
					<td class="t_right">'.$salary['holiday_prolong_pay'].'</td>
					<td class="t_right">'.$salary['holiday_night_pay'].'</td>
					<td class="t_right">'.$salary['tot_sudang_pay'].'</td>
					<td class="t_right">'.$salary['pension_amt'].'</td>
					<td class="t_right">'.$salary['health_amt'].'</td>
					<td class="t_right">'.$salary['care_amt'].'</td>
					<td class="t_right">'.$salary['employ_amt'].'</td>
					<td class="t_right">'.$salary['tot_ins_pay'].'</td>
				</tr>
			</table>';
	
	$tax_amt = $salary['total_amt']-($salary['meal_pay']+$salary['car_keep_pay'])-$unTaxPay;
	$untax_amt = $unTaxPay+$salary['meal_pay']+$salary['car_keep_pay'];

	$html .= '<table class="write_type" style="width:100%; margin-top:10px;">
				<colgroup>
					<col width="45px">
					<col width="45px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col>
				</colgroup>
				<tr>
					<th colspan="3">급여총액(A+E+G)</th>
					<th rowspan="2">공제총액</br>(C+D+F)</th>
					<th rowspan="2" style="border-right:2px solid '.$cl2.';">차인지급액</th>
					<th colspan="3">소득세</th>
				</tr>
				<tr>
					<th>총액</th>
					<th>과세</th>
					<th>비과세</th>
					<th>갑근세</th>
					<th>주민세</th>
					<th>합계(D)</th>
				</tr>
				<tr>
					<td class="t_right">'.$salary['total_amt'].'</td>
					<td class="t_right">'.$tax_amt.'</td>
					<td class="t_right">'.$untax_amt.'</td>
					<td class="t_right">'.$salary['deduct_amt'].'</td>
					<td class="t_right" style="border-right:2px solid '.$cl2.';">'.$salary['diff_amt'].'</td>
					<td class="t_right">'.$salary['tax_amt_1'].'</td>
					<td class="t_right">'.$salary['tax_amt_2'].'</td>
					<td class="t_right">'.$salary['tot_tax_pay'].'</td>
				</tr>
			</table>';
	/*
	$html .= '<table class="write_type" style="width:100%; margin-top:10px;">
				<colgroup>
					<col width="45px">
					<col width="45px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="60px">
					<col>
				</colgroup>
				<tr>
					<th rowspan="3">비과세</th>
					<th colspan="4">비급여[실비]</th>
					<th rowspan="2">실비지급</th>
					<th rowspan="2">비고</th>
				</tr>
				<tr>
					<th>일수</th>
					<th>시간</th>
					<th>금액</th>
					<th>추가비용</th>
				</tr>
				<tr>
					<td>'.number_format($salary['expense_days']).'</td>
					<td>'.number_format($salary['expense_hours']).'</td>
					<td>'.number_format($salary['expense_pay']).'</td>
					<td>'.number_format($salary['bipay_addpay']).'</td>
					<td>'.number_format($salary['real_pay']).'</td>
				</tr>
			</table>';
	
	$html .= '<table class="write_type" style="width:100%; margin-top:10px;">
				<colgroup>
					<col width="100px">
					<col width="100px">
					<col width="100px">
					<col width="100px">
				</colgroup>
				<tr>
					<th colspan="2">지급수당(E)</th>
					<th colspan="2">공제항목(F)</th>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>';
	*/

	echo $html;

	unset($html);

	$conn->close();
?>
