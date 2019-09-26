<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$mon	= IntVal($_POST['month']);
	$afterMon	= IntVal($_POST['month'])+1;
	$month	= ($mon < 10 ? '0' : '').$mon;
	$afterMonth	= ($afterMon < 10 ? '0' : '').$afterMon;
	
	

	$sql = 'SELECT *
			FROM   footing_mg
			WHERE  org_no = \''.$orgNo.'\'
			AND    yymm   = \''.$year.$month.'\'';
	$data = $conn -> get_array($sql);
		
	if($afterMonth=='13'){
		$year = $year+1;
		$afterMonth = 1;
	}

	$sql = 'SELECT *
			FROM   footing_mg
			WHERE  org_no = \''.$orgNo.'\'
			AND    yymm   = \''.$year.$afterMonth.'\'';
	$data2 = $conn -> get_array($sql);
	
	$sql = 'SELECT jumin
			FROM   member
			WHERE  org_no = \''.$orgNo.'\'
			AND    code   = \''.$_SESSION['userCode'].'\'';
	$memSsn = $conn -> get_data($sql);

	
	$sql = 'SELECT m02_jikwon_gbn
			 FROM   m02yoyangsa
			 WHERE  m02_ccode = \''.$orgNo.'\'
			 AND    m02_yjumin = \''.$memSsn.'\'';
	$memGbn = $conn -> get_data($sql);

	// 스마트폰 업무 구분
	$smart_gbn['M'] = 'N'; //관리자
	$smart_gbn['Y'] = 'N'; //요양보호사
	$smart_gbn['W'] = 'N'; //사회복지사

	if ($memGbn == 'B'){
		$smart_gbn['M'] = 'Y';
		$smart_gbn['Y'] = 'Y';
		$smart_gbn['W'] = 'Y';
	}if ($memGbn == 'A'){
		$smart_gbn['Y'] = 'Y';
		$smart_gbn['M'] = 'Y';
	}if ($memGbn == 'C'){
		$smart_gbn['M'] = 'Y';
		$smart_gbn['W'] = 'Y';
	}if ($memGbn == 'D'){
		$smart_gbn['Y'] = 'Y';
		$smart_gbn['W'] = 'Y';
	}else{
		if ($memGbn != ''){
			$smart_gbn[$memGbn] = 'Y';
		}
	}

			
	


	?>
	
		<tr>
			<th class="head sum last" colspan="6">공단엑셀 업로드</th>
		</tr>
		<tr>
			<th>1.일정계획</th>
			<td class="center"><a href="../longtermcare/plan_excel.php" target="_blank">바로가기</a></td>
			<td class="center"><?=$data['plan_dt']!=''? 'Y' : 'N';?></td>
			<td class="center"><?=$data['plan_dt']?></td>
			<td class="center"><?=$data2['plan_dt']?></td>
			<td class="last"></td>
		</tr>
		<tr>
			<th>2.청구내역</th>
			<td class="center"><a href="../longtermcare/charge_excel.php" target="_blank">바로가기</a></td>
			<td class="center"><?=$data['charge_dt']!=''? 'Y' : 'N';?></td>
			<td class="center"><?=$data['charge_dt']?></td>
			<td class="center"><?=$data2['charge_dt']?></td>
			<td class="last" rowspan="4"></td>
		</tr>
		<tr>
			<th>3.청구내역상세</th>
			<td class="center"><a href="../longtermcare/fixdtl_excel.php" target="_blank">바로가기</a></td>
			<td class="center"><?=$data['charge_s_dt']!=''? 'Y' : 'N';?></td>
			<td class="center"><?=$data['charge_s_dt']?></td>
			<td class="center"><?=$data2['charge_s_dt']?></td>
			<td class="last"></td>
		</tr>
		<tr>
			<th>4.청구확정내역</th>
			<td class="center"><a href="../longtermcare/fixed_excel.php" target="_blank">바로가기</a></td>
			<td class="center"><?=$data['charge_e_dt']!=''? 'Y' : 'N';?></td>
			<td class="center"><?=$data['charge_e_dt']?></td>
			<td class="center"><?=$data2['charge_e_dt']?></td>
			<td class="last"></td>
		</tr>
		<tr>
			<th>5.RFID전송내역</th>
			<td class="center"><a href="../longtermcare/rfid_excel.php" target="_blank">바로가기</a></td>
			<td class="center"><?=$data['rfid_dt']!=''? 'Y' : 'N';?></td>
			<td class="center"><?=$data['rfid_dt']?></td>
			<td class="center"><?=$data2['rfid_dt']?></td>
			<td class="last"></td>
		</tr>
		<tr>
			<th class="head sum">본인부담금 계산</th>
			<td class="center"><a href="../sugupja/client_expense.php" target="_blank">바로가기</a></td>
			<td class="center"><?=$data['expense_dt']!=''? 'Y' : 'N';?></td>
			<td class="center"><?=$data['expense_dt']?></td>
			<td class="center"><?=$data2['expense_dt']?></td>
			<td class="last"></td>
		</tr>
		<tr>
			<th >1. 영수증 및 명세서 조회 및 출력</th>
			<td class="center"><a href="../expenses/expenses.php?mode=report" target="_blank">바로가기</a></td>
			<td class="last" colspan="4"></td>
		</tr>
		<tr>
			<th class="head sum">급여일괄 계산</th>
			<td class="center"><a href="../salaryNew/salary_finish_confirm.php?mode=2" target="_blank">바로가기</a></td>
			<td class="center"><?=$data['salary_dt']!=''? 'Y' : 'N';?></td>
			<td class="center"><?=$data['salary_dt']?></td>
			<td class="center"><?=$data2['salary_dt']?></td>
			<td class="last"></td>
		</tr>
		<tr>
			<th >1. 급여조정 및 명세</th>
			<td class="center"><a href="../salaryNew/salary_edit_list.php" target="_blank">바로가기</a></td>
			<td class="last" colspan="4"></td>
		</tr>
		<tr>
			<th class="head sum last" colspan="6">기타</th>
		</tr>
		<tr>
			<th >1. 일정표출력(대상자)</th>
			<td class="center"><a href="../iljung/iljung_print_new.php?mode=101" target="_blank">바로가기</a></td>
			<td class="last" colspan="5" rowspan="5"></td>
		</tr>
		<tr>
			<th >2. 일정표출력(요양보호사)</th>
			<td class="center"><a href="../iljung/iljung_print_new.php?mode=102" target="_blank">바로가기</a></td>
		</tr><?
		if($gHostNm == 'www' || $smart_gbn['M'] == 'Y'){ ?>
			<tr>
				<th >3. 수급자(대상자) 등록</th>
				<td class="center"><a href="../sugupja/client_new.php" target="_blank">바로가기</a></td>
			</tr>
			<tr>
				<th >4. 직원등록</th>
				<td class="center"><a href="../yoyangsa/mem_reg.php" target="_blank">바로가기</a></td>
			</tr>
			<tr>
				<th >5. w4c업로드용 및 급여대장</th>
				<td class="center"><a href="../salaryNew/salary_report_svc2.php" target="_blank">바로가기</a></td>
			</tr><?
		} ?>

	<?

	Unset($data);

	include_once('../inc/_db_close.php');
?>