<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	
	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}



	$code	= $_POST['code'];
	$kind	= $_POST['kind'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$type   = $_POST['type'] != '' ? $_POST['type'] : 'Y';
	
	
	include_once("../salaryNew/salary_ins.php");
	
	/*********************************************************
	 *	보험가입 이력과 신고급여액
	 *********************************************************/
	$sql = 'SELECT	jumin
			,		annuity_yn
			,		health_yn
			,		employ_yn
			,		sanje_yn
			,		monthly
			,		paye_yn
			FROM	mem_insu
			WHERE	org_no = \''.$code.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.($month < 10 ? '0'.$month : $month).'\'
			AND		DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.($month < 10 ? '0'.$month : $month).'\'';


	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		/*********************************************************
		 *	보험가입 보수금액 이력
		 *********************************************************/
		$sql = 'SELECT	monthly
				FROM	mem_insu_monthly
				WHERE	org_no	 = \''.$code.'\'
				AND		jumin	 = \''.$row['jumin'].'\'
				AND		yymm	<= \''.$year.($month < 10 ? '0'.$month : $month).'\'
				ORDER	BY yymm DESC
				LIMIT	1';

		$liMonthly = $conn->get_data($sql);

		$memMonRegAmt[$row['jumin']]['annuity'] = $liMonthly; //$row['monthly'];
		$memMonRegAmt[$row['jumin']]['health']  = 0;
		$memMonRegAmt[$row['jumin']]['employ']  = 0;
		$memMonRegAmt[$row['jumin']]['sanje']   = 0;
		
		
		$memMonRegAmt[$row['jumin']]['annuityYN'] = ($memMonRegAmt[$row['jumin']]['annuityYN'] == 'Y' ? 'Y' : $row['annuity_yn']);
		$memMonRegAmt[$row['jumin']]['healthYN']  = ($memMonRegAmt[$row['jumin']]['healthYN'] == 'Y' ? 'Y' : $row['health_yn']);
		$memMonRegAmt[$row['jumin']]['employYN']  = ($memMonRegAmt[$row['jumin']]['employYN'] == 'Y' ? 'Y' : $row['employ_yn']);
		$memMonRegAmt[$row['jumin']]['sanjeYN']   = ($memMonRegAmt[$row['jumin']]['sanjeYN'] == 'Y' ? 'Y' : $row['sanje_yn']);
		$memMonRegAmt[$row['jumin']]['PAYEYN']    = ($memMonRegAmt[$row['jumin']]['PAYEYN'] == 'Y' ? 'Y' : $row['paye_yn']);
	}

	$conn->row_free();
	
	//4대보험 기준 금액
	$sql = 'SELECT	insu_gbn
			FROM	center_comm
			WHERE	org_no = \''.$code.'\'';

	$centerInsuGbn	= $conn->get_data($sql);
	
	
?>

<script src="../js/claim.js" type="text/javascript"></script>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function list(){
	var f = document.f;

	f.submit();
}

function before(){
	var f = document.f;

	f.action = 'center_amt_list.php';
	f.submit();
}

-->
</script>

<form name="f" method="post">

<div class="title">4대보험료내역</div>

<table class="my_table my_border">
	<colgroup>
		<col width="50px">
		<col width="70px">
		<col width="90px">
		<col width="140px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년월</th>
			<td class="left"><?=$year;?>년 <?=$month;?>월</td>
			<th>부담금내역조회</th>
			<td>
				<select name="type" style="width:auto;">
					<option value="all">전체</option>
					<option value="Y"  <? if($type == 'Y'){?>selected<?} ?>>유</option>
				</select>
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="list();">조회</button></span>
			</td>
			<td class="right last">
				<span class="btn_pack m"><button type="button" onclick="printCenter('<?=$code;?>','<?=$kind;?>','<?=$year;?>','<?=$month?>','<?=$type;?>');">출력</button></span>
				<img src="../image/btn_prev.png" style="cursor:pointer;" onclick="before();">
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col width="70px">
		<col width="65px">
		<col width="65px">
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col width="70px">
		<col width="65px">
		<col width="65px">
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan=2>요양사명</th>
			<th class="head" colspan=5>요양보호사 부담</th>
			<th class="head" colspan=6>기관 부담</th>
			<th class="sum last head" rowspan=2>합계</th>
		</tr>
		<tr>
			<th class="head">국민연금</th>
			<th class="head">건강보험</th>
			<th class="head">장기요양</th>
			<th class="head">고용보험</th>
			<th class="sum head">소계</th>
			<th class="head">국민연금</th>
			<th class="head">건강보험</th>
			<th class="head">장기요양</th>
			<th class="head">고용보험</th>
			<th class="head">산재보험</th>
			<th class="sum head">소계</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select m02_yname as name
				,	   m02_yjumin as ssn
				,      salary_basic.meal_pay
					 + salary_basic.car_keep_pay
					 + (select ifnull(sum(salary_pay), 0)
						  from salary_addon_pay
						 where org_no       = salary_basic.org_no
						   and salary_yymm  = salary_basic.salary_yymm
						   and salary_jumin = salary_basic.salary_jumin
						   and salary_type  = '1'
						   and salary_tax   != 'Y') as untax_pay
				,	   salary_basic.weekly_pay
					 + salary_basic.paid_pay
					 + salary_basic.bath_pay
					 + salary_basic.nursing_pay
					 + salary_basic.prolong_pay
					 + salary_basic.night_pay
					 + salary_basic.holiday_pay
					 + salary_basic.holiday_prolong_pay
					 + salary_basic.holiday_night_pay
					 + salary_basic.base_pay
					 + salary_basic.meal_pay
					 + salary_basic.car_keep_pay
					 + salary_basic.bojeon_pay
					 + salary_basic.rank_pay
					 + salary_basic.expense_pay
					 + salary_basic.annual_pay
					 + salary_basic.dementia_pay
					 + CASE WHEN IFNULL(salary_option.deal_bipay_yn,'N') != 'Y' AND IFNULL(salary_option.deal_hourly_yn,'N') != 'Y' THEN salary_basic.deal_pay ELSE 0 END
					 + (select ifnull(sum(salary_pay), 0)
						  from salary_addon_pay
						 where org_no       = salary_basic.org_no
						   and salary_yymm  = salary_basic.salary_yymm
						   and salary_jumin = salary_basic.salary_jumin
						   and salary_type  = '1') as tot_pay
				,      salary_basic.prolong_pay + salary_basic.night_pay + salary_basic.holiday_pay + salary_basic.holiday_prolong_pay + salary_basic.holiday_night_pay as tot_sudang_pay
				,      salary_basic.salary_jumin as jumin
				,      salary_basic.pension_amt as person_pension
				,      salary_basic.health_amt as person_health
				,      salary_basic.care_amt as person_care
				,      salary_basic.employ_amt as person_employ
				,      salary_basic.pension_amt +
					   salary_basic.health_amt +
					   salary_basic.care_amt +
					   salary_basic.employ_amt as person_tot
				,      salary_center_amt.pension_amt as center_pension
				,      salary_center_amt.health_amt as center_health
				,      salary_center_amt.care_amt as center_care
				,      salary_center_amt.employ_amt as center_employ
				,      salary_center_amt.sanje_amt as center_sanje
				,      salary_center_amt.pension_amt +
					   salary_center_amt.health_amt +
					   salary_center_amt.care_amt +
					   salary_center_amt.employ_amt +
					   salary_center_amt.sanje_amt as center_tot
				  from salary_basic
				 inner join salary_center_amt
					on salary_center_amt.org_no       = salary_basic.org_no
				   and salary_center_amt.salary_yymm  = salary_basic.salary_yymm
				   and salary_center_amt.salary_jumin = salary_basic.salary_jumin
				 inner join m02yoyangsa
					on m02_ccode = salary_basic.org_no
				   and m02_mkind = '0'
				   and m02_yjumin = salary_basic.salary_jumin
				   and m02_del_yn = 'N'
				  LEFT JOIN	salary_option
					   ON	salary_option.org_no		= salary_basic.org_no
					   AND	salary_option.salary_yymm	= salary_basic.salary_yymm
					   AND	salary_option.salary_jumin	= salary_basic.salary_jumin";

		//퇴사자 걸러내기
		$sql .= " INNER JOIN (
						SELECT jumin
						,      employ_stat
						  FROM mem_his
						 WHERE org_no = '".$code."'
						   AND DATE_FORMAT(join_dt, '%Y%m') <= '".$year.($month < 10 ? '0'.$month : $month)."'
						   AND DATE_FORMAT(ifnull(quit_dt,'9999-12-31'), '%Y%m') >= '".$year.($month < 10 ? '0'.$month : $month)."'
						) AS his
					 ON his.jumin = salary_basic.salary_jumin";

		$sql .= " where salary_basic.org_no = '$code'
				    and salary_basic.salary_yymm = '".$year.($month < 10 ? '0'.$month : $month)."'";

		if($type == 'Y'){
			$sql .= "and salary_basic.pension_amt
					   + salary_basic.health_amt
					   + salary_basic.care_amt
					   + salary_basic.employ_amt
					   + salary_center_amt.pension_amt
					   + salary_center_amt.health_amt
					   + salary_center_amt.care_amt
					   + salary_center_amt.employ_amt
					   + salary_center_amt.sanje_amt > 0";
		}
		$sql .= " GROUP BY jumin 
		          ORDER BY name  ";

		//if($debug) echo nl2br($sql);
		
		$conn->fetch_type = 'assoc';
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$total = $row['person_tot'] + $row['center_tot'];

			$taxPay = $row['tot_pay'] - $row['untax_pay'];
			
			if($year.($month < 10 ? '0'.$month : $month) >= '201904'){
				//초과근무수당 월기준금액 이하 시 20만원까지 비과세 처리 
				$liDurunuriAmt = 2100000;
				
				
				if($taxPay <= $liDurunuriAmt){
					if($row['tot_sudang_pay'] > 200000){
						$taxPay -= 200000;
					}else {
						$taxPay -= $row['tot_sudang_pay'];
					}
				}
				
			}
			
			
			if ($centerInsuGbn[0] == '1'){
				//과세금액
				$annuityPay = $taxPay;
			}else if ($centerInsuGbn[0] == '2'){
				//보수신고급여
				$annuityPay = $memMonRegAmt[$row['ssn']]['annuity'];

			}else{
			
				if ($memMonRegAmt[$row['ssn']]['annuity'] > 0){
					$annuityPay = $memMonRegAmt[$row['ssn']]['annuity'];
				}else{
					$annuityPay = $taxPay; 
				}
			}

			if($year.($month < 10 ? '0'.$month : $month) >= '201802'){
				//기준소득월액은 최저 28만원 설정
				if($taxPay < 280000){
					$healthPay = 280000;
				}else {
					$healthPay = $taxPay;
				}
			}else {
				$healthPay = $taxPay;
			}

			
			if($row['person_pension'] > 0){
				$workerAnnuity = $myF->cutOff($annuityPay * $ins_rate['worker_annuity'] * 0.01);
				$centerAnnuity = $myF->cutOff($annuityPay * $ins_rate['center_annuity'] * 0.01);
				$annuityRate = @round(100-intval(($row['person_pension']/$workerAnnuity)*100), -1);
			}else {
				$annuityRate = 0;
			}

			if($annuityRate > 0){
				$aRate = '<span style="color:blue;">('.$annuityRate.')</span>';
			}else {
				$aRate = '';
			}
			
			if($row['person_health'] > 0){
				$workerHealth = $myF->cutOff($healthPay * $ins_rate['worker_health'] * 0.01);
				$centerHealth = $myF->cutOff($healthPay * $ins_rate['center_health'] * 0.01);
				$healthRate = round(100-intval(($row['person_health']/$workerHealth)*100), -1);
			}else {
				$healthRate = 0;
			}

			if($healthRate > 0){
				$hRate = '<span style="color:blue;">('.$healthRate.')</span>';
			}else {
				$hRate = '';
			}

			if($row['person_care'] > 0){
				$workerOldcare = $myF->cutOff($workerHealth * $ins_rate['worker_oldcare'] * 0.01);
				$centerOldcare = $myF->cutOff($centerHealth * $ins_rate['center_oldcare'] * 0.01);
				$oldcareRate = round(100-intval(($row['person_care']/$workerOldcare)*100), -1);
			}else {
				$oldcareRate = 0;
			}


			if($row['person_employ'] > 0){
				$workerEmploy = $myF->cutOff($taxPay * $ins_rate['worker_employ'] * 0.01);
				$centerEmploy = $myF->cutOff($taxPay * $ins_rate['center_employ'] * 0.01);
				$employRate = round(100-intval(($row['person_employ']/$workerEmploy)*100), -1);
			}else {
				$employRate = 0;
			}

			if($employRate > 0){
				$eRate = '<span style="color:blue;">('.$employRate.')</span>';
			}else {
				$eRate = '';
			}
			
			

		?>


			<tr>
				<td class="left"><?=$row['name'];?></td>
				<td class="right"><?=number_format($row['person_pension']).$aRate;?></td>
				<td class="right"><?=number_format($row['person_health']).$hRate;?></td>
				<td class="right"><?=number_format($row['person_care']);?></td>
				<td class="right"><?=number_format($row['person_employ']).$eRate;?></td>
				<td class="sum right"><?=number_format($row['person_tot']);?></td>
				<td class="right"><?=number_format($row['center_pension']).$aRate;?></td>
				<td class="right"><?=number_format($row['center_health']).$hRate;?></td>
				<td class="right"><?=number_format($row['center_care']);?></td>
				<td class="right"><?=number_format($row['center_employ']).$eRate;?></td>
				<td class="right"><?=number_format($row['center_sanje']);?></td>
				<td class="sum right"><?=number_format($row['center_tot']);?></td>
				<td class="sum right last"><?=number_format($total);?></td>
			</tr><?

				$tot_person_pension	+= $row['person_pension'];
				$tot_person_health	+= $row['person_health'];
				$tot_person_care	+= $row['person_care'];
				$tot_person_employ	+= $row['person_employ'];
				$tot_person_tot	+= $row['person_tot'];
				$tot_center_pension	+= $row['center_pension'];
				$tot_center_health	+= $row['center_health'];
				$tot_center_care	+= $row['center_care'];
				$tot_center_employ	+= $row['center_employ'];
				$tot_center_sanje	+= $row['center_sanje'];
				$tot_center_tot	+= $row['center_tot'];
				$tot_total	+= $total;

		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="sum center">합계</td>
			<td class="sum right"><?=number_format($tot_person_pension);?></td>
			<td class="sum right"><?=number_format($tot_person_health);?></td>
			<td class="sum right"><?=number_format($tot_person_care);?></td>
			<td class="sum right"><?=number_format($tot_person_employ);?></td>
			<td class="sum right"><?=number_format($tot_person_tot);?></td>
			<td class="sum right"><?=number_format($tot_center_pension);?></td>
			<td class="sum right"><?=number_format($tot_center_health);?></td>
			<td class="sum right"><?=number_format($tot_center_care);?></td>
			<td class="sum right"><?=number_format($tot_center_employ);?></td>
			<td class="sum right"><?=number_format($tot_center_sanje);?></td>
			<td class="sum right"><?=number_format($tot_center_tot);?></td>
			<td class="sum right last"><?=number_format($tot_total);?></td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<td class="left last bottom" colspan="5"><?=$myF->message($row_count, 'N');?></td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code"   value="<?=$code;?>">
<input type="hidden" name="kind"   value="<?=$kind;?>">
<input type="hidden" name="year"   value="<?=$year;?>">
<input type="hidden" name="month"  value="<?=$month;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>