<?
	$sql = "select m02_yname as member_nm
			,      m02_yipsail as yipsail
			,      salary_basic.salary_jumin as member_cd
			,      salary_basic.work_cnt
			,      salary_basic.work_time
			,      salary_basic.weekly_cnt
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
			,      salary_basic.weekly_pay
			,      salary_basic.paid_pay
			,      salary_basic.bath_pay
			,      salary_basic.nursing_pay
			,      salary_basic.meal_pay
			,      salary_basic.car_keep_pay
			,      salary_basic.bojeon_pay
			,      salary_basic.base_pay + salary_basic.weekly_pay + salary_basic.paid_pay + salary_basic.bath_pay + salary_basic.nursing_pay + salary_basic.meal_pay + salary_basic.car_keep_pay + salary_basic.bojeon_pay as tot_basic_pay

			,      salary_basic.prolong_pay
			,      salary_basic.night_pay
			,      salary_basic.holiday_pay
			,      salary_basic.holiday_prolong_pay
			,      salary_basic.holiday_night_pay
			,      salary_basic.rank_pay
			,      salary_basic.prolong_pay + salary_basic.night_pay + salary_basic.holiday_pay + salary_basic.holiday_prolong_pay + salary_basic.holiday_night_pay + salary_basic.rank_pay as tot_sudang_pay

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

			  from salary_basic
			 inner join m02yoyangsa
				on m02_ccode  = salary_basic.org_no
			   and m02_mkind  = '$kind'
			   and m02_yjumin = salary_basic.salary_jumin
			  left join salary_amt
				on salary_amt.org_no       = salary_basic.org_no
			   and salary_amt.salary_yymm  = salary_basic.salary_yymm
			   and salary_amt.salary_jumin = salary_basic.salary_jumin
			 where salary_basic.org_no       = '$code'
			   and salary_basic.salary_yymm  = '$year$month'
			 order by m02_yname";

	$conn->fetch_type = 'assoc';
	$conn->query($sql);
	$conn->fetch_assoc();
	$mem_cnt = $conn->row_count();
	$max_page = ceil($mem_cnt / $col_cnt);

	for($i=0; $i<$mem_cnt; $i++){
		$mem[$i] = $conn->select_row($i);
		$mem[$i]['total_amt']  = $mem[$i]['tot_basic_pay'] + $mem[$i]['tot_sudang_pay'];
		$mem[$i]['deduct_amt'] = $mem[$i]['tot_ins_pay'] + $mem[$i]['tot_tax_pay'];
	}

	$conn->row_free();

	$sql = "select distinct
				   salary_type
			,      salary_index
			,      salary_subject
			  from salary_addon
			 where org_no = '$code'
			 order by salary_type, salary_index";

	$conn->query($sql);
	$conn->fetch_assoc();
	$row_count = $conn->row_count();

	$addon_index[1] = 0;
	$addon_index[2] = 0;

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$addon_caption[$row['salary_type']][$addon_index[$row['salary_type']]]['index']   = $row['salary_index'];
		$addon_caption[$row['salary_type']][$addon_index[$row['salary_type']]]['subject'] = $row['salary_subject'];

		$addon_index[$row['salary_type']] ++;
	}

	$addon_count[1] = sizeof($addon_caption[1]);
	$addon_count[2] = sizeof($addon_caption[2]);

	$conn->row_free();

	$sql = "select salary_jumin
			,      salary_type
			,      salary_index
			,      salary_subject
			,      salary_pay
			  from salary_addon_pay
			 where org_no      = '$code'
			   and salary_yymm = '$year$month'
			 order by salary_jumin, salary_type, salary_index";

	$conn->query($sql);
	$conn->fetch_assoc();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$addon_pay[$row['salary_jumin']][$row['salary_type']][$row['salary_index']] = $row['salary_pay'];
		$addon_pay[$row['salary_jumin']][$row['salary_type']]['total'] += $row['salary_pay'];

		for($ii=0; $ii<=$mem_cnt; $ii++){
			if ($mem[$ii]['member_cd'] == $row['salary_jumin']){
				if ($row['salary_type'] == 1){
					$mem[$ii]['total_amt'] += $row['salary_pay'];
				}else{
					$mem[$ii]['deduct_amt'] += $row['salary_pay'];
				}
			}
		}
	}

	$conn->row_free();

	for($i=0; $i<=$mem_cnt; $i++){
		$mem[$i]['diff_amt'] = $mem[$i]['total_amt'] - $mem[$i]['deduct_amt'];
	}

	$colspan	= '2';
	$my_table	= '';
	$my_border	= '1';
	
	if ($my_table == ''){?>
		<style>
			.head{
				background-color:#efefef;
				border:0.5pt solid #000000;
				font-family:굴림;
			}
			.head_l{
				text-align:left;
				border:0.5pt solid #000000;
				background-color:#efefef;
				font-family:굴림;
			}
			.head_r{
				text-align:right;
				border:0.5pt solid #000000;
				background-color:#efefef;
				font-family:굴림;
			}
			.head_c{
				text-align:center;
				border:0.5pt solid #000000;
				background-color:#efefef;
				font-family:굴림;
			}
			.filed{
				border-right:0.5pt solid #000000;
				background-color:#ffffff;
				font-family:굴림;
			}
			.filed_l{
				text-align:left;
				border:0.5pt solid #000000;
				background-color:#ffffff;
				font-family:굴림;
			}
			.filed_r{
				text-align:right;
				border-right:0.5pt solid #000000;
				border-bottom:0.5pt solid #000000;
				background-color:#ffffff;
				font-family:굴림;
			}
			.filed_c{
				text-align:center;
				border:0.5pt solid #000000;
				background-color:#ffffff;
				font-family:굴림;
			}
			.bottom{
			}
		</style><?
	}

		$sql = "select m00_cname
				  from m00center
				 where m00_mcode = '$code'
				   and m00_mkind = '$kind'";
		$cm = $conn->get_array($sql);
	?>
	<div align="left" style="font-size:15pt; font-weight:bold;"><?=$year?>년 <?=$month?>월 급여대장</div><br>
	<div align="left" style="font-size:11pt;">기관명 : <?=$cm['m00_cname'];?></div>
	<table style="border-right:0.5pt solid #000000;">
		<tr>
			<td class="head_c" rowspan="6" style="border-bottom:2px solid #000000;">NO</td>
			<td class="head_c" rowspan="6" style="border-bottom:2px solid #000000;" >성명<br>주민등록번호<br>입사일자</td>
			<td class="head_c" rowspan="2" >급여총액<br>(A + B + E)</td>
			<td class="head_c" colspan="6" >기본(A)</td>
			<td class="head_c" colspan="2" >초과근무(B)</td>
			<td class="head_c" rowspan="2" colspan="2" >4대보험</td>
			<td class="head_c" rowspan="2">소득세</td>
			<td class="head_c" colspan="2" >지급수당</td>
		</tr>
		<tr>
			<td class="head_c" rowspan="2">합계(A)</td>
			<td class="head_c" colspan="2" rowspan="2">기본</td>
			<td class="head_c" >보전수당</td>
			<td class="head_c" colspan="2">유급</td>
			<td class="head_c" colspan="2">연장수당</td>
			<td class="head_c" rowspan="2">합계(E)</td>
			<td class="head_c" >직급수당</td>
		</tr>
		<tr>
			<td class="head_c" rowspan="2" >공제총액<br>(C + D + F)</td>
			<td class="head_c" >식대보조비</td>
			<td class="head_c" colspan="2" >목욕</td>
			<td class="head_c" colspan="2" >야간수당</td>
			<td class="head_c" rowspan="2">합계(C)</td>
			<td class="head_c" >국민연금</td>
			<td class="head_c" rowspan="2">갑근세</td>
			<td class="head_c" >기타합계</td>
		</tr>
		<tr>
			<td class="head_c" rowspan="2">합계(B)</td>
			<td class="head_c" >근무일</td>
			<td class="head_c" rowspan="2">주휴</td>
			<td class="head_c" >차량유지비</td>
			<td class="head_c" colspan="2">간호</td>
			<td class="head_c" colspan="2">휴일수당</td>
			<td class="head_c" >건강보험</td>
			<td class="head_c" colspan="2">공제항목(F)</td>
		</tr>
		<tr>
			<td class="head_c" rowspan="2">차인지급액</td>
			<td class="head_c" >년차수당</td>
			<td class="head_c" colspan="2"></td>
			<td class="head_c" colspan="2">휴일연장</td>
			<td class="head_c" rowspan="2">합계(D)</td>
			<td class="head_c" >장기요양</td>
			<td class="head_c" rowspan="2">주민세</td>
			<td class="head_c" rowspan="2">합계(F)</td>
			<td class="head_c" ><?=$addon_caption[2][1]['subject'];?></td>
		</tr>
		<tr>
			<td class="head_c" ></td>
			<td class="head_c" >시간</td>
			<td class="head_c" >기본급</td>
			<td class="head_c" ></td>
			<td class="head_c" >횟수</td>
			<td class="head_c" >수당</td>
			<td class="head_c" >시간</td>
			<td class="head_c" >수당</td>
			<td class="head_c" >고용보험</td>
			<td class="head_c" >기타합계</td>
		</tr>

		<?
			for($i=0; $i<$mem_cnt; $i++){
				$total_amt				+= $mem[$i]['total_amt'];				//급여총액
				$deduct_amt				+= $mem[$i]['deduct_amt'];				//공제총액
				$diff_amt				+= $mem[$i]['diff_amt'];				//차인지급액
				$tot_basic_pay			+= $mem[$i]['tot_basic_pay'];			//합계(A)
				$work_cnt				+= $mem[$i]['work_cnt'];				//근무일수
				$work_time				+= $mem[$i]['work_time'];				//근무시간
				$base_pay				+= $mem[$i]['base_pay'];				//기본급
				$weekly_cnt				+= $mem[$i]['weekly_cnt'];				//주휴일수
				$weekly_pay				+= $mem[$i]['weekly_pay'];				//주휴수당
				$paid_cnt				+= $mem[$i]['paid_cnt'];				//유급일수
				$paid_pay				+= $mem[$i]['paid_pay'];				//유급수당
				$bath_cnt				+= $mem[$i]['bath_cnt'];				//목욕횟수
				$bath_pay				+= $mem[$i]['bath_pay'];				//목욕수당
				$nursing_cnt			+= $mem[$i]['nursing_cnt'];				//간호횟수
				$nursing_pay			+= $mem[$i]['nursing_pay'];				//간호수당
				$meal_pay				+= $mem[$i]['meal_pay'];				//식대보조비
				$car_keep_pay			+= $mem[$i]['car_keep_pay'];			//차량유지비
				$bojeon_pay				+= $mem[$i]['bojeon_pay'];				//보전수당
				$tot_sudang_pay			+= $mem[$i]['tot_sudang_pay'];			//합계(B)
				$prolong_hour			+= $mem[$i]['prolong_hour'];			//연장시간
				$prolong_pay			+= $mem[$i]['prolong_pay'];				//연장수당
				$night_hour				+= $mem[$i]['night_hour'];				//야간시간
				$night_pay				+= $mem[$i]['night_pay'];				//야간수당
				$holiday_hour			+= $mem[$i]['holiday_hour'];			//휴일시간
				$holiday_pay			+= $mem[$i]['holiday_pay'];				//휴일수당
				$holiday_prolong_hour	+= $mem[$i]['holiday_prolong_hour'];	//휴연시간
				$holiday_prolong_pay	+= $mem[$i]['holiday_prolong_pay'];		//휴연수당
				$holiday_night_hour		+= $mem[$i]['holiday_night_hour'];		//휴야시간
				$holiday_night_pay		+= $mem[$i]['holiday_night_pay'];		//휴야수당
				$tot_ins_pay			+= $mem[$i]['tot_ins_pay'];				//합계(C)
				$pension_amt			+= $mem[$i]['pension_amt'];				//국민연금
				$health_amt				+= $mem[$i]['health_amt'];				//건강보험
				$care_amt				+= $mem[$i]['care_amt'];				//장기요양
				$employ_amt				+= $mem[$i]['employ_amt'];				//고용보험
				$tot_tax_pay			+= $mem[$i]['tot_tax_pay'];				//합계(D)
				$tax_amt_1				+= $mem[$i]['tax_amt_1'];				//갑근세
				$tax_amt_2				+= $mem[$i]['tax_amt_2'];				//주민세
				$rank_pay				+= $mem[$i]['rank_pay'];				//직급수당

				
				if ($addon_pay_1 == 0) $addon_pay_1 = (is_numeric($mem[$i]['rank_pay']) ? $mem[$i]['rank_pay'] : 0);	//합계(E)
				$addon_pay_1 += $addon_pay[$mem[$i]['member_cd']][1]['total'];

				for($j=0; $j<$addon_count[1]; $j++){
					$tot_addon[1][$addon_caption[1][$j]['index']] += $addon_pay[$mem[$i]['member_cd']][1][$addon_caption[1][$j]['index']];
					$tot_addon_pay[1][$mem[$i]['member_cd']]	  += $addon_pay[$mem[$i]['member_cd']][1][$addon_caption[1][$j]['index']];
				}

				$addon_pay_2 += $addon_pay[$mem[$i]['member_cd']][2]['total'];	//합계(E)

				for($j=0; $j<$addon_count[2]; $j++){
					$tot_addon[2][$addon_caption[2][$j]['index']] += $addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][$j]['index']];
					$tot_addon_pay[2][$mem[$i]['member_cd']]	  += $addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][$j]['index']];
				}
			}

			
				
		?>
		<tr>
			<td class="head_c" style="border-bottom:2px solid #000000; font-weight:bold;" rowspan="4" colspan="2">합계</td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;" ><?=number_format($total_amt);?></td>
			<td class="filed" rowspan="2" style="text-align:right; font-weight:bold; BORDER-BOTTOM: black 0.5pt dashed"><?=number_format($tot_basic_pay) != 0 ? number_format($tot_basic_pay) : '';?></td>
			<td class="filed" rowspan="2" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($work_cnt) != 0 ? number_format($work_cnt) : '';?></td>
			<td class="filed" rowspan="2" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($weekly_cnt) != 0 ? number_format($weekly_cnt) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($bojeon_pay) != 0 ? number_format($bojeon_pay) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($paid_cnt) != 0 ? number_format($paid_cnt) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($paid_pay) != 0 ? number_format($paid_pay) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($prolong_hour) != 0 ? number_format($prolong_hour) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($prolong_pay) != 0 ? number_format($prolong_pay) : '';?></td>
			<td class="filed_r" rowspan="2" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($tot_ins_pay) != 0 ? number_format($tot_ins_pay) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($pension_amt) != 0 ? number_format($pension_amt) : '';?></td>
			<td class="filed" rowspan="2" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($tax_amt_1) != 0 ? number_format($tax_amt_1) : '';?></td>
			<td class="filed" rowspan="2" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($addon_pay_1) != 0 ? number_format($addon_pay_1) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($rank_pay) != 0 ? number_format($rank_pay) : '';?></td>
		</tr>
		<tr>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($deduct_amt) != 0 ? number_format($deduct_amt) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($meal_pay) != 0 ? number_format($meal_pay) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($bath_cnt) != 0 ? number_format($bath_cnt) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($bath_pay) != 0 ? number_format($bath_pay) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($night_hour) != 0 ? number_format($night_hour) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($night_pay) != 0 ? number_format($night_pay) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($health_amt) != 0 ? number_format($health_amt) : '';?></td>
			<?
			for($j=0; $j<$addon_count[1]; $j++){
				$tot_addon1 += $tot_addon[1][$addon_caption[1][$j]['index']];
			}?>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($tot_addon1) != 0 ? number_format($tot_addon1) : '';?></td>	
		</tr>
		<tr>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($diff_amt) != 0 ? number_format($diff_amt) : '';?></td>
			<td class="filed_r" rowspan="2" style="border-bottom:2px solid #000000; font-weight:bold;"><?=number_format($tot_sudang_pay) != 0 ? number_format($tot_sudang_pay) : '';?></td>
			<td class="filed_r" rowspan="2" style="border-bottom:2px solid #000000; font-weight:bold;"><?=number_format($work_time, 1) != 0 ? number_format($work_time, 1) : '';?></td>
			<td class="filed_r" rowspan="2" style="border-bottom:2px solid #000000; font-weight:bold;"><?=number_format($weekly_pay) != 0 ? number_format($weekly_pay) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($car_keep_pay) != 0 ? number_format($car_keep_pay) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($nursing_cnt) != 0 ? number_format($nursing_cnt) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($nursing_pay) != 0 ? number_format($nursing_pay) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($holiday_hour) != 0 ? number_format($holiday_hour) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($holiday_pay) != 0 ? number_format($holiday_pay) : '';?></td>
			<td class="filed_r" rowspan="2" style="border-bottom:2px solid #000000; font-weight:bold;"><?=number_format($tot_tax_pay) != 0 ? number_format($tot_tax_pay) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($care_amt) != 0 ? number_format($care_amt) : '';?></td>
			<td class="filed_r" rowspan="2" style="border-bottom:2px solid #000000; font-weight:bold;"><?=number_format($tax_amt_2) != 0 ? number_format($tax_amt_2) : '';?></td>
			<td class="filed_r" rowspan="2" style="border-bottom:2px solid #000000; font-weight:bold;"><?=number_format($addon_pay_2) != 0 ? number_format($addon_pay_2) : '';?></td>
			<td class="filed" style="text-align:right; font-weight:bold; border-bottom:black 0.5pt dashed;"><?=number_format($tot_addon[2][$addon_caption[2][0]['index']]) != 0 ? number_format($tot_addon[2][$addon_caption[2][0]['index']]) : '';?></td>
		</tr>
		<tr>
			<td class="filed_r" style="border-bottom:2px solid #000000; font-weight:bold;"></td>
			<td class="filed_r" style="border-bottom:2px solid #000000; font-weight:bold;"></td>
			<td class="filed_r" style="border-bottom:2px solid #000000; font-weight:bold;"></td>
			<td class="filed_r" style="border-bottom:2px solid #000000; font-weight:bold;"></td>
			<td class="filed_r" style="border-bottom:2px solid #000000; font-weight:bold;"><?=number_format($holiday_prolong_hour) != 0 ? number_format($holiday_prolong_hour) : '';?></td>
			<td class="filed_r" style="border-bottom:2px solid #000000; font-weight:bold;"><?=number_format($holiday_prolong_pay) != 0 ? number_format($holiday_prolong_pay) : '';?></td>
			<td class="filed_r" style="border-bottom:2px solid #000000; font-weight:bold;"><?=number_format($employ_amt) != 0 ? number_format($employ_amt) : '';?></td><?
			for($j=1; $j<$addon_count[2]; $j++){
				$tot_addon2 += $tot_addon[2][$addon_caption[2][$j]['index']];
			}?>
			<td class="filed_r" style="border-bottom:2px solid #000000; font-weight:bold;"><?=number_format($tot_addon2) != 0 ? number_format($tot_addon2) : '';?></td>
		</tr><?
		for($i=0; $i<$mem_cnt; $i++){?>
			<tr>
				<td class="head_c" rowspan="4"  style="border-bottom:1pt solid #000000;"><?=$i+1;?></td>
				<td class="head" style="border-bottom:1pt solid #000000;"><?=$mem[$i]['member_nm'];?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['total_amt']);?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['tot_basic_pay']) != 0 ? number_format($mem[$i]['tot_basic_pay']) : '';?></td>
				<td class="filed_r" rowspan="2" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['work_cnt']);?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['weekly_cnt']) != 0 ? number_format($mem[$i]['weekly_cnt']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['bojeon_pay']) != 0 ? number_format($mem[$i]['bojeon_pay']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['paid_cnt']) != 0 ? number_format($mem[$i]['paid_cnt']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['paid_pay']) != 0 ? number_format($mem[$i]['paid_pay']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['prolong_hour']) != 0 ? number_format($mem[$i]['prolong_hour']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['prolong_pay']) != 0 ? number_format($mem[$i]['prolong_pay']) : '';?></td>
				<td class="filed_r" rowspan="2" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['tot_ins_pay']) != 0 ? number_format($mem[$i]['tot_ins_pay']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['pension_amt']) != 0 ? number_format($mem[$i]['pension_amt']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['tax_amt_1']) != 0 ? number_format($mem[$i]['tax_amt_1']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($tot_addon_pay[1][$mem[$i]['member_cd']]) != 0 ? number_format($tot_addon_pay[1][$mem[$i]['member_cd']]) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;" ><?=number_format($mem[$i]['rank_pay']) != 0 ? number_format($mem[$i]['rank_pay']) : '';?></td>
			</tr>	
			<tr>
				<td class="filed" style="text-align:center; border-bottom:black 0.5pt dashed;"><?=substr($mem[$i]['member_cd'],0,6);?>-<?=substr($mem[$i]['member_cd'],6,7);?></td>	
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['deduct_amt']) != 0 ? number_format($mem[$i]['deduct_amt']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['meal_pay']) != 0 ? number_format($mem[$i]['meal_pay']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['bath_cnt']) != 0 ? number_format($mem[$i]['bath_cnt']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['bath_pay']) != 0 ? number_format($mem[$i]['bath_pay']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['night_hour']) != 0 ? number_format($mem[$i]['night_hour']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['night_pay']) != 0 ? number_format($mem[$i]['night_pay']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['health_amt']) != 0 ? number_format($mem[$i]['health_amt']) : '';?></td><?
				for($j=0; $j<$addon_count[2]; $j++){
					$addon_caption1 += $addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][$j]['index']];
				}?>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($addon_caption1) != 0 ? number_format($addon_caption1) : '';?></td>
			</tr>
			<tr>
				<td class="filed_r" rowspan="2" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['diff_amt']);?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['tot_sudang_pay']) != 0 ? number_format($mem[$i]['tot_sudang_pay']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['work_time'], 1) != 0 ? number_format($mem[$i]['work_time'], 1) : ''; ?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['weekly_pay']) != 0 ? number_format($mem[$i]['weekly_pay']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['car_keep_pay']) != 0 ? number_format($mem[$i]['car_keep_pay']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['nursing_cnt']) != 0 ? number_format($mem[$i]['nursing_cnt']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['nursing_pay']) != 0 ? number_format($mem[$i]['nursing_pay']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['holiday_hour']) != 0 ? number_format($mem[$i]['holiday_hour']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['holiday_pay']) != 0 ? number_format($mem[$i]['holiday_pay']) : '';?></td>
				<td class="filed_r" rowspan="2" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['tot_tax_pay']) != 0 ? number_format($mem[$i]['tot_tax_pay']) : '';?></td>
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['care_amt']) != 0 ? number_format($mem[$i]['care_amt']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['tax_amt_2']) != 0 ? number_format($mem[$i]['tax_amt_2']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($tot_addon_pay[2][$mem[$i]['member_cd']]) != 0 ? number_format($tot_addon_pay[2][$mem[$i]['member_cd']]) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;" ><?=$addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][0]['index']]?></td>
			</tr>
			<tr>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
			</tr>



				
				
				
			
				
				
				<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($mem[$i]['base_pay']) != 0 ? number_format($mem[$i]['base_pay']) : '';?></td>
				
				
				
				for($j=0; $j<$addon_count[2]; $j++){?>
					<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][$j]['index']]) != 0 ? number_format($addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][$j]['index']]) : '';?></td><?
				}
				
				
				
				
				
				
				
				
				
					<td class="filed" style="text-align:right; border-bottom:black 0.5pt dashed;"><?=number_format($addon_pay[$mem[$i]['member_cd']][1][$addon_caption[1][$j]['index']]) != 0 ? number_format($addon_pay[$mem[$i]['member_cd']][1][$addon_caption[1][$j]['index']]) : '';?></td><?
				}
				if( $addon_count[1] < $addon_count[2]){
				
					
				}?>
			</tr>
			<tr>
				<td class="filed_r" style="border-bottom:1pt solid #000000; text-align:center" ><?=$myF->dateStyle($mem[$i]['yipsail'],'.');?></td>
				
				
				
				
				
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['holiday_prolong_hour']) != 0 ? number_format($mem[$i]['holiday_prolong_hour']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['holiday_prolong_pay']) != 0 ? number_format($mem[$i]['holiday_prolong_pay']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['holiday_night_hour']) != 0 ? number_format($mem[$i]['holiday_night_hour']) : '';?></td>
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['holiday_night_pay']) != 0 ? number_format($mem[$i]['holiday_night_pay']) : '';?></td>
				
				<td class="filed_r" style="border-bottom:1pt solid #000000;"></td>
				
				<td class="filed_r" style="border-bottom:1pt solid #000000;"><?=number_format($mem[$i]['employ_amt']) != 0 ? number_format($mem[$i]['employ_amt']) : '';?></td>
				
				
				
				if( $addon_count[2] < $addon_count[1]){
					$add_cnt = $addon_count[1] - $addon_count[2];

					for($j=1; $j<=$add_cnt; $j++){ ?>
						<td class="filed_r" style="border-bottom:1pt solid #000000;" ></td><?
					}
				}?>
			</tr>
			<?
		}
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		<?

	unset($addon_caption);
	unset($addon_index);
	unset($addon_count);
	unset($addon_pay);
	unset($addon_pay_count);
	unset($tot_addon);
?>