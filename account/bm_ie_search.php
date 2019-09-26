<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];


	/** 급여계산여부 *******************************************************/
		$sql = 'SELECT	salary_yymm AS yymm
				,		COUNT(salary_jumin) AS cnt
				FROM	salary_basic
				WHERE	org_no = \''.$orgNo.'\'
				AND		LEFT(salary_yymm,4) = \''.$year.'\'
				GROUP	BY salary_yymm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$m = IntVal(SubStr($row['yymm'],4));

			//급여 계산된 요양보호사 수
			$data[$m]['SALARY']['CNT'] = $row['cnt'];
		}

		$conn->row_free();


	/** 매출 *******************************************************/
		$sql = 'SELECT	yymm
				,		wek1_plan + wek2_plan + wek3_plan + wek4_plan AS plan_pay
				,		wek1_conf + wek2_conf + wek3_conf + wek4_conf AS conf_pay
				,		longterm_amt  + expense_amt AS sales_amt
				FROM	ie_bm_close_amt
				WHERE	org_no = \''.$orgNo.'\'
				AND		LEFT(yymm,4) = \''.$year.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$m = IntVal(SubStr($row['yymm'],4));

			//계획금액
			$data[$m]['SALES']['PLAN'] = $row['plan_pay'];

			//실적금액
			$data[$m]['SALES']['CONF'] = $row['conf_pay'];

			//공단청구 + 본인부담
			$data[$m]['SALES']['SALES'] = $row['sales_amt'];
		}

		$conn->row_free();


	/** 기타 *******************************************************/
		$sql = 'SELECT	yymm
				,		SUM(amt) AS amt
				FROM	ie_bm_other_in
				WHERE	org_no = \''.$orgNo.'\'
				AND		LEFT(yymm,4) = \''.$year.'\'
				GROUP	BY yymm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$m = IntVal(SubStr($row['yymm'],4));

			//기타
			$data[$m]['SALES']['OTHER'] = $row['amt'];
		}

		$conn->row_free();


	/** 가구수 *******************************************************/
		$sql = 'SELECT	yymm
				,		care_cnt + care_bath_cnt + bath_cnt AS cnt
				FROM	ie_bm_close_client
				WHERE	org_no = \''.$orgNo.'\'
				AND		LEFT(yymm,4) = \''.$year.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$m = IntVal(SubStr($row['yymm'],4));

			//기타
			$data[$m]['FAMILY']['CNT'] = $row['cnt'];
		}

		$conn->row_free();


	/** 요양보호사 급여 *******************************************************/
		$sql = 'SELECT	yymm
				,		SUM(re_n_amt + re_y_amt + re_amt + insu_com_amt) AS salary
				,		SUM(pay_60up + pay_60down) AS mem_pay
				FROM	ie_bm_close_member
				WHERE	org_no = \''.$orgNo.'\'
				AND		LEFT(yymm,4) = \''.$year.'\'
				GROUP	BY yymm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$m = IntVal(SubStr($row['yymm'],4));

			//급여 + 퇴직충당금 + 4대보험회사분
			$data[$m]['YOY']['SALARY'] = $row['salary'];

			//급여
			$data[$m]['YOY']['PAY'] = $row['mem_pay'];
		}

		$conn->row_free();


	/** 정직원 급여 *******************************************************/
		$sql = 'SELECT	yymm
				,		SUM(salary + insu_amt + retire_amt) AS salary
				,		SUM(salary) AS mg_pay
				FROM	ie_bm_salary
				WHERE	org_no = \''.$orgNo.'\'
				AND		LEFT(yymm,4) = \''.$year.'\'
				GROUP	BY yymm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$m = IntVal(SubStr($row['yymm'],4));

			//급여 + 퇴직충당금 + 4대보험회사분
			$data[$m]['MEM']['SALARY'] = 0; //$row['salary'];

			//급여
			$data[$m]['MEM']['PAY'] = 0; //$row['mg_pay'];
		}

		$conn->row_free();


	/** 장애인 채용관리 *******************************************************/
		$sql = 'SELECT	yymm
				,		allot_amt
				,		deduct_amt
				FROM	ie_bm_disps
				WHERE	org_no = \''.$orgNo.'\'
				AND		LEFT(yymm,4) = \''.$year.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$m = IntVal(SubStr($row['yymm'],4));

			//분담금액
			$data[$m]['EXPENSE']['ALLOT'] = $row['allot_amt'];

			//공제금
			$data[$m]['EXPENSE']['DEDUCT'] = $row['deduct_amt'];
		}

		$conn->row_free();


	/** 일반경비 *******************************************************/
		$sql = 'SELECT	yymm
				,		SUM(amt) AS amt
				FROM	ie_bm_charge
				WHERE	org_no = \''.$orgNo.'\'
				AND		LEFT(yymm,4) = \''.$year.$month.'\'
				GROUP	BY yymm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$m = IntVal(SubStr($row['yymm'],4));

			//경비
			$data[$m]['EXPENSE']['CHARGE'] = $row['amt'];
		}

		$conn->row_free();


	/** 개인간병 가구수 *******************************************************/
		$sql = 'SELECT	CASE WHEN from_dt <= \''.$year.'01\' THEN \''.$year.'01\' ELSE from_dt END AS from_dt
				,		CASE WHEN to_dt >= \''.$year.'12\' THEN \''.$year.'12\' ELSE to_dt END AS to_dt
				,		psn_cnt
				FROM	ie_bm_psnurse
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		LEFT(from_dt,4) <= \''.$year.'\'
				AND		LEFT(to_dt,4)	>= \''.$year.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$ym = $row['from_dt'];
			while(true){
				if ($ym > $row['to_dt']) break;

				$m = IntVal(SubStr($ym,4));
				$data[$m]['FAMILY']['NURSE'] = $row['psn_cnt'];
				$ym = $myF->dateAdd('month', 1, $myF->dateStyle($ym.'01'), 'Ym');
			}
		}

		$conn->row_free();


	/** 목표금액 ****************************************/
		$sql = 'SELECT	*
				FROM	ie_bm_target
				WHERE	org_no	= \''.$orgNo.'\'
				AND		year	= \''.$year.'\'';

		$row = $conn->get_array($sql);

		for($i=1; $i<=12; $i++){
			$data[$i]['TARGET']['amt'] = $row['amt'.$i];
		}


	/*********************************************************/


	//print_r($data);


	for($i=1; $i<=12; $i++){
		//매출액
		if ($data[$i]['SALES']['SALES'] > 0){
			$sales = $data[$i]['SALES']['SALES']; // + $data[$i]['SALES']['OTHER'];
		}else if ($data[$i]['SALARY']['CNT'] > 0){
			$sales = $data[$i]['SALES']['CONF']; // + $data[$i]['SALES']['OTHER'];
		}else{
			$sales = $data[$i]['SALES']['PLAN']; // + $data[$i]['SALES']['OTHER'];
		}

		//기타
		$other = $data[$i]['SALES']['OTHER'];

		//가구수
		$family = $data[$i]['FAMILY']['CNT'] + $data[$i]['FAMILY']['NURSE'];

		//cost1
		$cost1 = $data[$i]['YOY']['SALARY'];

		//차액
		$diffAmt = $sales + $other - $cost1;

		//비율
		//$diffRate = @Round($diffAmt / $sales * 100,2);
		//인건비비율
		$diffRate = @Round($cost1 / $sales * 100,2);

		//cost2
		$cost2	= $data[$i]['MEM']['SALARY']
				+ Floor(($data[$i]['YOY']['PAY'] + $data[$i]['MEM']['PAY']) * 0.8 / 100)
				+ Floor(($data[$i]['YOY']['PAY'] + $data[$i]['MEM']['PAY']) * 0.04 / 100)
				+ Floor(($data[$i]['YOY']['PAY'] + $data[$i]['MEM']['PAY']) * 0.5 / 100)
				+ $data[$i]['EXPENSE']['ALLOT']
				- $data[$i]['EXPENSE']['DEDUCT']
				+ $data[$i]['EXPENSE']['CHARGE'];

		//영업이익
		$profit = $diffAmt - $cost2;

		//영업이익율
		$profitRate = @Round($profit / $sales * 100,2);

		//목표금액
		$targetAmt = $data[$i]['TARGET']['amt'];

		//달성률
		$targetRate = @Round(($sales + $other) / $targetAmt * 100, 2);


		$total['SALES'] += $sales;
		$total['OTHER'] += $other;
		$total['FAMILY'] += $family;
		$total['COST1'] += $cost1;
		$total['DIFF_AMT'] += $diffAmt;
		$total['COST2'] += $cost2;
		$total['PROFIT'] += $profit;
		$total['TARGET_AMT'] += $targetAmt;


		$totAmt = ($sales + $other ? number_format($sales + $other) : '');
		$sales = ($sales > 0 ? number_format($sales) : '');
		$other = ($other > 0 ? number_format($other) : '');
		$family = ($family > 0 ? number_format($family) : '');
		$cost1 = ($cost1 > 0 ? number_format($cost1) : '');
		$diffAmt = ($diffAmt > 0 ? number_format($diffAmt) : '');
		$diffRate = ($diffRate != 0 ? $diffRate.'%' : '');
		$cost2 = ($cost2 > 0 ? number_format($cost2) : '');
		$profit = ($profit != 0 ? number_format($profit) : '');
		$profitRate = ($profitRate != 0 ? $profitRate.'%' : '');
		$targetAmt = ($targetAmt > 0 ? number_format($targetAmt) : '');
		$targetRate = ($targetRate != 0 ? $targetRate.'%' : '');

		?>

		<tr>
			<td class="center"><?=$i;?>월</td>
			<td><div class="right"><?=$targetAmt;?></div></td>
			<td><div class="right"><?=$sales;?></div></td>
			<td><div class="right"><?=$other;?></div></td>
			<td><div class="right"><?=$totAmt;?></div></td>
			<td><div class="right" style="font-weight:bold;">&nbsp;<?=$targetRate;?></div></td>
			<td><div class="right"><?=$family;?></div></td>
			<td><div class="right"><?=$cost1;?></div></td>
			<td><div class="right"><?=$diffAmt;?></div></td>
			<td><div class="right" style="font-weight:bold;">&nbsp;<?=$diffRate;?></div></td>
			<td><div class="right"><?=$cost2;?></div></td>
			<td><div class="right"><?=$profit;?></div></td>
			<td><div class="right" style="font-weight:bold;">&nbsp;<?=$profitRate;?></div></td>
			<td class="last"><?
				if ($sales){?>
					<div class="left" style="padding-top:2px; padding-bottom:1px;">
						<span class="btn_pack small"><button onclick="lfExcel('M','<?=$i;?>');">정산</button></span>
						<span class="btn_pack small"><button onclick="lfExcel('T','<?=$i;?>');">ERP&nbsp;</button></span>
					</div><?
				}?>
			</td>
		</tr><?
	}

	$total['DIFF_RATE'] = @Round($total['DIFF_AMT'] / $total['SALES'] * 100,2);
	$total['PROFIT_RATE'] = @Round($total['PROFIT'] / $total['SALES'] * 100,2);
	$total['TARGET_RATE'] = @Round($total['SALES'] / $total['TARGET_AMT'] * 100,2);?>
	<!--CUT_LINE-->
	<tr>
		<td class="center" style="background-color:#EAEAEA;">합계</td>
		<td class="" style="background-color:#EAEAEA;"><div class="right">&nbsp;<?=$total['TARGET_AMT'] != 0 ? number_format($total['TARGET_AMT']) : '';?></div></td>
		<td class="" style="background-color:#EAEAEA;"><div class="right">&nbsp;<?=$total['SALES'] != 0 ? number_format($total['SALES']) : '';?></div></td>
		<td class="" style="background-color:#EAEAEA;"><div class="right">&nbsp;<?=$total['OTHER'] != 0 ? number_format($total['OTHER']) : '';?></div></td>
		<td class="" style="background-color:#EAEAEA;"><div class="right">&nbsp;<?=$total['SALES'] + $total['OTHER'] != 0 ? number_format($total['SALES'] + $total['OTHER']) : '';?></div></td>
		<td class="" style="background-color:#EAEAEA; font-weight:bold;"><div class="right">&nbsp;<?=$total['TARGET_RATE'] != 0 ? $total['TARGET_RATE'].'%' : '';?></div></td>
		<td class="" style="background-color:#EAEAEA;"><div class="right">&nbsp;<?=$total['FAMILY'] != 0 ? number_format($total['FAMILY']) : '';?></div></td>
		<td class="" style="background-color:#EAEAEA;"><div class="right">&nbsp;<?=$total['COST1'] != 0 ? number_format($total['COST1']) : '';?></div></td>
		<td class="" style="background-color:#EAEAEA;"><div class="right">&nbsp;<?=$total['DIFF_AMT'] != 0 ? number_format($total['DIFF_AMT']) : '';?></div></td>
		<td class="" style="background-color:#EAEAEA; font-weight:bold;"><div class="right">&nbsp;<?=$total['DIFF_RATE'] != 0 ? $total['DIFF_RATE'].'%' : '';?></div></td>
		<td class="" style="background-color:#EAEAEA;"><div class="right">&nbsp;<?=$total['COST2'] != 0 ? number_format($total['COST2']) : '';?></div></td>
		<td class="" style="background-color:#EAEAEA;"><div class="right">&nbsp;<?=$total['PROFIT'] != 0 ? number_format($total['PROFIT']) : '';?></div></td>
		<td class="" style="background-color:#EAEAEA; font-weight:bold;"><div class="right">&nbsp;<?=$total['PROFIT_RATE'] != 0 ? $total['PROFIT_RATE'].'%' : '';?></div></td>
		<td class="last" style="background-color:#EAEAEA;"></td>
	</tr><?


	Unset($data);
	Unset($total);

	include_once('../inc/_db_close.php');
?>