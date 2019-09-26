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

	$init_year = $myF->year();
?>

<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function table_list(month){
	var f = document.f;

	f.month.value = month;
	f.submit();
}

-->
</script>

<form name="f" method="post">

<div class="title">급여대장</div>

<table class="my_table my_border">
	<colgroup>
		<col width="35px">
		<col width="40px">
		<col width="35px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">년도</th>
			<td>
				<select name="year" style="width:auto;" onchange="list(1, <?=intval(date('m', mktime()));?>);">
				<?
					for($i=$init_year[0]; $i<=$init_year[1]; $i++){ ?>
						<option value="<?=$i;?>" <? if($year == $i){?>selected<?} ?>><?=$i;?></option><?
					}
				?>
				</select>
			</td>
			<th class="head">월별</th>
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
						$link	= '<a href="#" onclick="table_list('.$i.');">'.$i.'월</a>';
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
		</tr>
	</tbody>
</table>
<?
	$sql = "select m02_yname as member_nm
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
			   and m02_yjumin = salary_basic.salary_jumin
			  left join salary_amt
				on salary_amt.org_no       = salary_basic.org_no
			   and salary_amt.salary_yymm  = salary_basic.salary_yymm
			   and salary_amt.salary_jumin = salary_basic.salary_jumin
			 where salary_basic.org_no       = '$code'
			   and salary_basic.salary_yymm  = '$year$month'
			 order by m02_yname";

	$conn->query($sql);
	$conn->fetch();
	$mem_cnt = $conn->row_count();

	for($i=0; $i<$mem_cnt; $i++){
		$mem[$i] = $conn->select_row($i);
	}

	$conn->row_free();

	$sql = "select distinct
				   salary_type
			,      salary_index
			,      salary_subject
			  from salary_addon_pay
			 where org_no      = '$code'
			   and salary_yymm = '$year$month'
			 order by salary_type, salary_index";

	$conn->query($sql);
	$conn->fetch();
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
			,      salary_pay
			  from salary_addon_pay
			 where org_no      = '$code'
			   and salary_yymm = '$year$month'
			 order by salary_jumin, salary_type, salary_index";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$addon_pay[$row['salary_jumin']][$row['salary_type']][$row['salary_index']] = $row['salary_pay'];
		$addon_pay[$row['salary_jumin']][$row['salary_type']]['total'] += $row['salary_pay'];
	}

	$conn->row_free();
?>
<div id="scroll_1" style="overflow-x:scroll; overflow-y:scroll; width:100%; height:600px;">
<table class="my_table" style="width:100%; border-bottom:none;">
	<colgroup>
		<col width="32px">
		<col width="100px">
		<col width="35px">
		<col width="65px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head" colspan="2">&nbsp;</th>
			<th class="head" colspan="2">합계</th>
			<td class="center top last bottom" rowspan="<?=29+$addon_count[1]+2+$addon_count[2]+1;?>">
				<div id="scroll_2" style="overflow-x:scroll; overflow-y:hidden; width:587px; height:100%;">
					<table id="my_tbl" class="my_table" style="width:<?=$mem_cnt * 100;?>; border-bottom:none;">
						<colgroup>
						<?
							for($i=0; $i<$mem_cnt; $i++){?>
								<col width="35px">
								<col width="65px"><?
							}
						?>
						</colgroup>
						<tbody>
							<tr><?	// 직원명
								for($i=0; $i<$mem_cnt; $i++){?>
									<th class="head <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=$mem[$i]['member_nm'];?></th><?
								}?>
							</tr>
							<tr><?	// 급여총액
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['total_amt']);?></td><?
									$total_amt += $mem[$i]['total_amt'];
								}?>
							</tr>
							<tr><?	// 공제총액
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['deduct_amt']);?></td><?
									$deduct_amt += $mem[$i]['deduct_amt'];
								}?>
							</tr>
							<tr><?	// 차인지급액
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['diff_amt']);?></td><?
									$diff_amt += $mem[$i]['diff_amt'];
								}?>
							</tr>

							<tr><?	// 합계(A)
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['tot_basic_pay']);?></td><?
									$tot_basic_pay += $mem[$i]['tot_basic_pay'];
								}?>
							</tr>
							<tr><?	// 근무일수
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['work_cnt']);?></td><?
									$work_cnt += $mem[$i]['work_cnt'];
								}?>
							</tr>
							<tr><?	// 근무시간/기본급
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right"><?=number_format($mem[$i]['work_time']);?></td>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>"><?=number_format($mem[$i]['base_pay']);?></td><?
									$work_time += $mem[$i]['work_time'];
									$base_pay += $mem[$i]['base_pay'];
								}?>
							</tr>
							<tr><?	// 주휴일수/수당
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right"><?=number_format($mem[$i]['weekly_cnt']);?></td>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>"><?=number_format($mem[$i]['weekly_pay']);?></td><?
									$weekly_cnt += $mem[$i]['weekly_cnt'];
									$weekly_pay += $mem[$i]['weekly_pay'];
								}?>
							</tr>
							<tr><?	// 유급일수/수당
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right"><?=number_format($mem[$i]['paid_cnt']);?></td>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>"><?=number_format($mem[$i]['paid_pay']);?></td><?
									$paid_cnt += $mem[$i]['paid_cnt'];
									$paid_pay += $mem[$i]['paid_pay'];
								}?>
							</tr>
							<tr><?	// 목욕횟수/수당
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right"><?=number_format($mem[$i]['bath_cnt']);?></td>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>"><?=number_format($mem[$i]['bath_pay']);?></td><?
									$bath_cnt += $mem[$i]['bath_cnt'];
									$bath_pay += $mem[$i]['bath_pay'];
								}?>
							</tr>
							<tr><?	// 간호횟수/수당
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right"><?=number_format($mem[$i]['nursing_cnt']);?></td>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>"><?=number_format($mem[$i]['nursing_pay']);?></td><?
									$nursing_cnt += $mem[$i]['nursing_cnt'];
									$nursing_pay += $mem[$i]['nursing_pay'];
								}?>
							</tr>
							<tr><?	// 식대보조비
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['meal_pay']);?></td><?
									$meal_pay += $mem[$i]['meal_pay'];
								}?>
							</tr>
							<tr><?	// 차량유지비
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['car_keep_pay']);?></td><?
									$car_keep_pay += $mem[$i]['car_keep_pay'];
								}?>
							</tr>
							<tr><?	// 보전수당
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['bojeon_pay']);?></td><?
									$bojeon_pay += $mem[$i]['bojeon_pay'];
								}?>
							</tr>

							<tr><?	// 합계(B)
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['tot_sudang_pay']);?></td><?
									$tot_sudang_pay += $mem[$i]['tot_sudang_pay'];
								}?>
							</tr>
							<tr><?	// 연장
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right"><?=number_format($mem[$i]['prolong_hour']);?></td>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>"><?=number_format($mem[$i]['prolong_pay']);?></td><?
									$prolong_hour += $mem[$i]['prolong_hour'];
									$prolong_pay += $mem[$i]['prolong_pay'];
								}?>
							</tr>
							<tr><?	// 야간
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right"><?=number_format($mem[$i]['night_hour']);?></td>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>"><?=number_format($mem[$i]['night_pay']);?></td><?
									$night_hour += $mem[$i]['night_hour'];
									$night_pay += $mem[$i]['night_pay'];
								}?>
							</tr>
							<tr><?	// 휴일
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right"><?=number_format($mem[$i]['holiday_hour']);?></td>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>"><?=number_format($mem[$i]['holiday_pay']);?></td><?
									$holiday_hour += $mem[$i]['holiday_hour'];
									$holiday_pay += $mem[$i]['holiday_pay'];
								}?>
							</tr>
							<tr><?	// 휴야
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right"><?=number_format($mem[$i]['holiday_prolong_hour']);?></td>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>"><?=number_format($mem[$i]['holiday_prolong_pay']);?></td><?
									$holiday_prolong_hour += $mem[$i]['holiday_prolong_hour'];
									$holiday_prolong_pay += $mem[$i]['holiday_prolong_pay'];
								}?>
							</tr>
							<tr><?	// 휴연
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right"><?=number_format($mem[$i]['holiday_night_hour']);?></td>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>"><?=number_format($mem[$i]['holiday_night_pay']);?></td><?
									$holiday_night_hour += $mem[$i]['holiday_night_hour'];
									$holiday_night_pay += $mem[$i]['holiday_night_pay'];
								}?>
							</tr>

							<tr><?	// 합계(C)
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['tot_ins_pay']);?></td><?
									$tot_ins_pay += $mem[$i]['tot_ins_pay'];
								}?>
							</tr>
							<tr><?	// 국민연금
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['pension_amt']);?></td><?
									$pension_amt += $mem[$i]['pension_amt'];
								}?>
							</tr>
							<tr><?	// 건강보험
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['health_amt']);?></td><?
									$health_amt += $mem[$i]['health_amt'];
								}?>
							</tr>
							<tr><?	// 장기요양
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['care_amt']);?></td><?
									$care_amt += $mem[$i]['care_amt'];
								}?>
							</tr>
							<tr><?	// 고용보험
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['employ_amt']);?></td><?
									$employ_amt += $mem[$i]['employ_amt'];
								}?>
							</tr>

							<tr><?	// 합계(D)
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['tot_tax_pay']);?></td><?
									$tot_tax_pay += $mem[$i]['tot_tax_pay'];
								}?>
							</tr>
							<tr><?	// 갑근세
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['tax_amt_1']);?></td><?
									$tax_amt_1 += $mem[$i]['tax_amt_1'];
								}?>
							</tr>
							<tr><?	// 주민세
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['tax_amt_2']);?></td><?
									$tax_amt_2 += $mem[$i]['tax_amt_2'];
								}?>
							</tr>

							<tr><?	// 합계(E)
								$addon_pay_1 = (is_numeric($mem[$i]['rank_pay']) ? $mem[$i]['rank_pay'] : 0);

								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($addon_pay[$mem[$i]['member_cd']][1]['total']);?></td><?
									$addon_pay_1 += $addon_pay[$mem[$i]['member_cd']][1]['total'];
								}?>
							</tr>
							<tr><?	// 직급수당
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($mem[$i]['rank_pay']);?></td><?
									$rank_pay += $mem[$i]['rank_pay'];
								}?>
							</tr><?
							for($j=0; $j<$addon_count[1]; $j++){?>
								<tr><?
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($addon_pay[$mem[$i]['member_cd']][1][$addon_caption[1][$j]['index']]);?></td><?
									$tot_addon[1][$addon_caption[1][$j]['index']] += $addon_pay[$mem[$i]['member_cd']][1][$addon_caption[1][$j]['index']];
									$addon_pay_1 += $addon_pay[$mem[$i]['member_cd']][1][$addon_caption[1][$j]['index']];
								}?>
								</tr><?
							}?>

							<tr><?	// 합계(E)
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($addon_pay[$mem[$i]['member_cd']][2]['total']);?></td><?
									$addon_pay_2 += $addon_pay[$mem[$i]['member_cd']][2]['total'];
								}?>
							</tr><?
							for($j=0; $j<$addon_count[2]; $j++){?>
								<tr><?
								for($i=0; $i<$mem_cnt; $i++){?>
									<td class="right <? if($i+1 == $mem_cnt){?>last<?} ?>" colspan="2"><?=number_format($addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][$j]['index']]);?></td><?
									$tot_addon[2][$addon_caption[2][$j]['index']] += $addon_pay[$mem[$i]['member_cd']][2][$addon_caption[1][$j]['index']];
									$addon_pay_2 += $addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][$j]['index']];
								}?>
								</tr><?
							}?>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<th class="left" colspan="2">급여총액(A + B + E)</th>
			<td class="right" colspan="2"><?=number_format($total_amt);?></td>
		</tr>
		<tr>
			<th class="left" colspan="2">공제총액(C + D + F)</th>
			<td class="right" colspan="2"><?=number_format($deduct_amt);?></td>
		</tr>
		<tr>
			<th class="left" colspan="2">차인지급액</th>
			<td class="right" colspan="2"><?=number_format($diff_amt);?></td>
		</tr>

		<tr>
			<th class="center" rowspan="10">기<br>본<br>근<br>무</th>
			<th class="left">합계(A)</th>
			<td class="right" colspan="2"><?=number_format($tot_basic_pay);?></td>
		</tr>
		<tr>
			<th class="left">근무일수</th>
			<td class="right" colspan="2"><?=number_format($work_cnt);?></td>
		</tr>
		<tr>
			<th class="left">근무시간/기본급</th>
			<td class="right"><?=number_format($work_time);?></td>
			<td class="right"><?=number_format($base_pay);?></td>
		</tr>
		<tr>
			<th class="left">주휴일수/수당</th>
			<td class="right"><?=number_format($weekly_cnt);?></td>
			<td class="right"><?=number_format($weekly_pay);?></td>
		</tr>
		<tr>
			<th class="left">유급일수/수당</th>
			<td class="right"><?=number_format($paid_cnt);?></td>
			<td class="right"><?=number_format($bath_pay);?></td>
		</tr>
		<tr>
			<th class="left">목욕횟수/수당</th>
			<td class="right"><?=number_format($bath_cnt);?></td>
			<td class="right"><?=number_format($work_cnt);?></td>
		</tr>
		<tr>
			<th class="left">간호횟수/수당</th>
			<td class="right"><?=number_format($work_cnt);?></td>
			<td class="right"><?=number_format($work_cnt);?></td>
		</tr>
		<tr>
			<th class="left">식대보조비</th>
			<td class="right" colspan="2"><?=number_format($meal_pay);?></td>
		</tr>
		<tr>
			<th class="left">차량유지비</th>
			<td class="right" colspan="2"><?=number_format($car_keep_pay);?></td>
		</tr>
		<tr>
			<th class="left">보전수당</th>
			<td class="right" colspan="2"><?=number_format($bojeon_pay);?></td>
		</tr>

		<tr>
			<th class="center" rowspan="6">초<br>과<br>근<br>무</th>
			<th class="left">합계(B)</th>
			<td class="right" colspan="2"><?=number_format($tot_sudang_pay);?></td>
		</tr>
		<tr>
			<th class="left">연장시간/수당</th>
			<td class="right"><?=number_format($prolong_hour);?></td>
			<td class="right"><?=number_format($prolong_pay);?></td>
		</tr>
		<tr>
			<th class="left">야간시간/수당</th>
			<td class="right"><?=number_format($night_hour);?></td>
			<td class="right"><?=number_format($night_pay);?></td>
		</tr>
		<tr>
			<th class="left">휴일시간/수당</th>
			<td class="right"><?=number_format($holiday_hour);?></td>
			<td class="right"><?=number_format($holiday_pay);?></td>
		</tr>
		<tr>
			<th class="left">휴연시간/수당</th>
			<td class="right"><?=number_format($holiday_prolong_hour);?></td>
			<td class="right"><?=number_format($holiday_prolong_pay);?></td>
		</tr>
		<tr>
			<th class="left">휴야시간/수당</th>
			<td class="right"><?=number_format($holiday_night_hour);?></td>
			<td class="right"><?=number_format($holiday_night_pay);?></td>
		</tr>

		<tr>
			<th class="center" rowspan="5">보<br>험<br>항<br>목</th>
			<th class="left">합계(C)</th>
			<td class="right" colspan="2"><?=number_format($tot_ins_pay);?></td>
		</tr>
		<tr>
			<th class="left">국민연금</th>
			<td class="right" colspan="2"><?=number_format($pension_amt);?></td>
		</tr>
		<tr>
			<th class="left">건강보험</th>
			<td class="right" colspan="2"><?=number_format($health_amt);?></td>
		</tr>
		<tr>
			<th class="left">장기요양</th>
			<td class="right" colspan="2"><?=number_format($care_amt);?></td>
		</tr>
		<tr>
			<th class="left">고용보헙</th>
			<td class="right" colspan="2"><?=number_format($employ_amt);?></td>
		</tr>

		<tr>
			<th class="center" rowspan="3">소<br>득<br>세</th>
			<th class="left">합계(D)</th>
			<td class="right" colspan="2"><?=number_format($tot_tax_pay);?></td>
		</tr>
		<tr>
			<th class="left">갑근세</th>
			<td class="right" colspan="2"><?=number_format($tax_amt_1);?></td>
		</tr>
		<tr>
			<th class="left">주민세</th>
			<td class="right" colspan="2"><?=number_format($tax_amt_2);?></td>
		</tr>

		<tr>
			<th class="center" rowspan="<?=$addon_count[1]+2;?>">지<br>급<br>수<br>당</th>
			<th class="left">합계(E)</th>
			<td class="right" colspan="2"><?=number_format($addon_pay_1);?></td>
		</tr>
		<tr>
			<th class="left">직급수당</th>
			<td class="right" colspan="2"><?=number_format($rank_pay);?></td>
		</tr>
		<?
			for($i=0; $i<$addon_count[1]; $i++){?>
				<tr>
					<th class="left"><?=$addon_caption[1][$i]['subject'];?></th>
					<td class="right" colspan="2"><?=number_format($tot_addon[1][$addon_caption[1][$i]['index']]);?></td>
				</tr><?
			}
		?>

		<tr>
			<th class="center" rowspan="<?=$addon_count[2]+1;?>">공<br>제<br>항<br>목</th>
			<th class="left">합계(F)</th>
			<td class="right" colspan="2"><?=number_format($addon_pay_2);?></td>
		</tr>
		<?
			for($i=0; $i<$addon_count[2]; $i++){?>
				<tr>
					<th class="left"><?=$addon_caption[2][$i]['subject'];?></th>
					<td class="right" colspan="2"><?=number_format($tot_addon[2][$addon_caption[2][$i]['index']]);?></td>
				</tr><?
			}
		?>


		<tr>
			<td class="bottom left last" colspan="2">&nbsp;</td>
			<td class="bottom right" colspan="2">&nbsp;</td>
		</tr>
	</tbody>
</table>
</div>

<input type="hidden" name="code" value="<?=$code;?>">
<input type="hidden" name="month" value="<?=$month;?>">

</form>

<script>
	var my_tbl = document.getElementById('my_tbl');
	var scroll_1 = document.getElementById('scroll_1');
	var scroll_2 = document.getElementById('scroll_2');

</script>

<?
	unset($addon_caption);
	unset($addon_index);
	unset($addon_count);
	unset($addon_pay);
	unset($addon_pay_count);
	unset($tot_addon);

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>