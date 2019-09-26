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

	if ($_SERVER['PHP_SELF'] != '/work/salary_table.php'){
		$colspan	= '2';
		$my_table	= '';
		$my_border	= '1';
	}else{
		$colspan	= '1';
		$my_table	= 'my_table';
		$my_border	= '0';
	}

	if ($my_table == ''){?>
		<style>
			.head{
				background-color:#efefef;
			}
			.head_l{
				text-align:left;
				background-color:#efefef;
			}
			.head_r{
				text-align:right;
				background-color:#efefef;
			}
			.head_c{
				text-align:center;
				background-color:#efefef;
			}
			.filed_l{
				text-align:left;
				background-color:#ffffff;
			}
			.filed_r{
				text-align:right;
				background-color:#ffffff;
			}
			.filed_c{
				text-align:center;
				background-color:#ffffff;
			}
			.bottom{
			}
		</style><?
	}

	if ($my_table == 'my_table'){?>
		<div style="overflow-x:hidden; overflow-y:scroll; width:854px; height:100%;">
			<table class="<?=$my_table;?>" style="border-bottom:none;" border="<?=$my_border;?>">
				<tbody>
					<tr>
						<th class="head" style="width:150px;" colspan="<?=$colspan;?>">&nbsp;</th>
						<th class="head" style="width:131px;" colspan="<?=$colspan;?>">합계</th>
						<td class="left top last bottom" style="padding:0;">
							<table class="<?=$my_table;?>" style="border-bottom:none;" border="<?=$my_border;?>">
								<tbody>
									<tr><?	// 직원명
										$index = 0;
										for($i=0; $i<$mem_cnt; $i++){
											if ($i % $col_cnt == 0) $index ++;?>
											<th id="id_1_<?=$index;?>[]" class="head" style="width:<?=$col_width+11;?>px; padding:0; <? if($index > 1){?>display:none;<?} ?>" colspan="<?=$colspan;?>"><a href="#" onclick="_salary_edit('<?=$kind;?>','<?=$ed->en($mem[$i]['member_cd']);?>');"><?=$mem[$i]['member_nm'];?></a></th><?
										}?>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<?
			for($i=0; $i<$mem_cnt; $i++){
				$total_amt += $mem[$i]['total_amt'];
			}
		?>

		<div id="table_scroll" style="overflow-x:hidden; overflow-y:scroll; width:854px; height:100px;">
		<table class="<?=$my_table;?>" style="<? if($my_table != ''){?>border-bottom:none;<?} ?>" border="<?=$my_border;?>">
			<tbody>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>" style="width:150px;" colspan="2">급여총액(A + B + E)</th>
					<td class="right" style="width:132px;" colspan="2"><?=number_format($total_amt);?></td>
					<td class="left top last bottom" style="padding:0;" rowspan="<?=27+$addon_count[1]+2+$addon_count[2]+1;?>">
						<table class="<?=$my_table;?>" style="border-bottom:none;" border="<?=$my_border;?>">
							<tbody>
								<tr><?	// 급여총액
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['total_amt']);?></td><?
										$total_amt += $mem[$i]['total_amt'];
									}?>
								</tr>
								<tr><?	// 공제총액
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['deduct_amt']);?></td><?
										$deduct_amt += $mem[$i]['deduct_amt'];
									}?>
								</tr>
								<tr><?	// 차인지급액
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['diff_amt']);?></td><?
										$diff_amt += $mem[$i]['diff_amt'];
									}?>
								</tr>

								<tr><?	// 합계(A)
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['tot_basic_pay']);?></td><?
										$tot_basic_pay += $mem[$i]['tot_basic_pay'];
									}?>
								</tr>
								<tr><?	// 근무일수
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['work_cnt']);?></td><?
										$work_cnt += $mem[$i]['work_cnt'];
									}?>
								</tr>
								<tr><?	// 근무시간/기본급
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width1;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['work_time']);?></td>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width2;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['base_pay']);?></td><?
										$work_time += $mem[$i]['work_time'];
										$base_pay += $mem[$i]['base_pay'];
									}?>
								</tr>
								<tr><?	// 주휴일수/수당
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width1;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['weekly_cnt']);?></td>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width2;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['weekly_pay']);?></td><?
										$weekly_cnt += $mem[$i]['weekly_cnt'];
										$weekly_pay += $mem[$i]['weekly_pay'];
									}?>
								</tr>
								<tr><?	// 유급일수/수당
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width1;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['paid_cnt']);?></td>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width2;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['paid_pay']);?></td><?
										$paid_cnt += $mem[$i]['paid_cnt'];
										$paid_pay += $mem[$i]['paid_pay'];
									}?>
								</tr>
								<tr><?	// 목욕횟수/수당
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width1;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['bath_cnt']);?></td>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width2;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['bath_pay']);?></td><?
										$bath_cnt += $mem[$i]['bath_cnt'];
										$bath_pay += $mem[$i]['bath_pay'];
									}?>
								</tr>
								<tr><?	// 간호횟수/수당
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width1;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['nursing_cnt']);?></td>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width2;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['nursing_pay']);?></td><?
										$nursing_cnt += $mem[$i]['nursing_cnt'];
										$nursing_pay += $mem[$i]['nursing_pay'];
									}?>
								</tr>
								<tr><?	// 식대보조비
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['meal_pay']);?></td><?
										$meal_pay += $mem[$i]['meal_pay'];
									}?>
								</tr>
								<tr><?	// 차량유지비
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['car_keep_pay']);?></td><?
										$car_keep_pay += $mem[$i]['car_keep_pay'];
									}?>
								</tr>
								<tr><?	// 보전수당
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['bojeon_pay']);?></td><?
										$bojeon_pay += $mem[$i]['bojeon_pay'];
									}?>
								</tr>

								<tr><?	// 합계(B)
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['tot_sudang_pay']);?></td><?
										$tot_sudang_pay += $mem[$i]['tot_sudang_pay'];
									}?>
								</tr>
								<tr><?	// 연장
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width1;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['prolong_hour'], 1);?></td>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width2;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['prolong_pay']);?></td><?
										$prolong_hour += $mem[$i]['prolong_hour'];
										$prolong_pay += $mem[$i]['prolong_pay'];
									}?>
								</tr>
								<tr><?	// 야간
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width1;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['night_hour'], 1);?></td>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width2;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['night_pay']);?></td><?
										$night_hour += $mem[$i]['night_hour'];
										$night_pay += $mem[$i]['night_pay'];
									}?>
								</tr>
								<tr><?	// 휴일
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width1;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['holiday_hour'], 1);?></td>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width2;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['holiday_pay']);?></td><?
										$holiday_hour += $mem[$i]['holiday_hour'];
										$holiday_pay += $mem[$i]['holiday_pay'];
									}?>
								</tr>
								<tr><?	// 휴야
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width1;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['holiday_prolong_hour'], 1);?></td>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width2;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['holiday_prolong_pay']);?></td><?
										$holiday_prolong_hour += $mem[$i]['holiday_prolong_hour'];
										$holiday_prolong_pay += $mem[$i]['holiday_prolong_pay'];
									}?>
								</tr>
								<tr><?	// 휴연
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width1;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['holiday_night_hour'], 1);?></td>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width2;?>px; <? if($index > 1){?>display:none;<?} ?>"><?=number_format($mem[$i]['holiday_night_pay']);?></td><?
										$holiday_night_hour += $mem[$i]['holiday_night_hour'];
										$holiday_night_pay += $mem[$i]['holiday_night_pay'];
									}?>
								</tr>

								<tr><?	// 합계(C)
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['tot_ins_pay']);?></td><?
										$tot_ins_pay += $mem[$i]['tot_ins_pay'];
									}?>
								</tr>
								<tr><?	// 국민연금
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['pension_amt']);?></td><?
										$pension_amt += $mem[$i]['pension_amt'];
									}?>
								</tr>
								<tr><?	// 건강보험
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['health_amt']);?></td><?
										$health_amt += $mem[$i]['health_amt'];
									}?>
								</tr>
								<tr><?	// 장기요양
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['care_amt']);?></td><?
										$care_amt += $mem[$i]['care_amt'];
									}?>
								</tr>
								<tr><?	// 고용보험
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['employ_amt']);?></td><?
										$employ_amt += $mem[$i]['employ_amt'];
									}?>
								</tr>

								<tr><?	// 합계(D)
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['tot_tax_pay']);?></td><?
										$tot_tax_pay += $mem[$i]['tot_tax_pay'];
									}?>
								</tr>
								<tr><?	// 갑근세
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['tax_amt_1']);?></td><?
										$tax_amt_1 += $mem[$i]['tax_amt_1'];
									}?>
								</tr>
								<tr><?	// 주민세
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['tax_amt_2']);?></td><?
										$tax_amt_2 += $mem[$i]['tax_amt_2'];
									}?>
								</tr>

								<tr><?	// 합계(E)
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($addon_pay[$mem[$i]['member_cd']][1]['total']+(is_numeric($mem[$i]['rank_pay']) ? $mem[$i]['rank_pay'] : 0));?></td><?
										$addon_pay_1 += (is_numeric($mem[$i]['rank_pay']) ? $mem[$i]['rank_pay'] : 0);
										$addon_pay_1 += $addon_pay[$mem[$i]['member_cd']][1]['total'];
									}?>
								</tr>
								<tr><?	// 직급수당
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($mem[$i]['rank_pay']);?></td><?
										$rank_pay += $mem[$i]['rank_pay'];
									}?>
								</tr><?
								for($j=0; $j<$addon_count[1]; $j++){?>
									<tr><?
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($addon_pay[$mem[$i]['member_cd']][1][$addon_caption[1][$j]['index']]);?></td><?
										$tot_addon[1][$addon_caption[1][$j]['index']] += $addon_pay[$mem[$i]['member_cd']][1][$addon_caption[1][$j]['index']];
										//$addon_pay_1 += $addon_pay[$mem[$i]['member_cd']][1][$addon_caption[1][$j]['index']];
										$tot_addon_pay[1][$mem[$i]['member_cd']]	  += $addon_pay[$mem[$i]['member_cd']][1][$addon_caption[1][$j]['index']];
									}?>
									</tr><?
								}?>

								<tr><?	// 합계(F)
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($addon_pay[$mem[$i]['member_cd']][2]['total']);?></td><?
										$addon_pay_2 += $addon_pay[$mem[$i]['member_cd']][2]['total'];
									}?>
								</tr><?
								for($j=0; $j<$addon_count[2]; $j++){?>
									<tr><?
									$index = 0;
									for($i=0; $i<$mem_cnt; $i++){
										if ($i % $col_cnt == 0) $index ++;?>
										<td id="id_1_<?=$index;?>[]" class="right <? if($j+1 == $addon_count[2]){?>bottom<?} ?>" style="width:<?=$col_width;?>px; <? if($index > 1){?>display:none;<?} ?>" colspan="2"><?=number_format($addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][$j]['index']]);?></td><?
										$tot_addon[2][$addon_caption[2][$j]['index']] += $addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][$j]['index']];
										//$addon_pay_2 += $addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][$j]['index']];
										$tot_addon_pay[2][$mem[$i]['member_cd']]	  += $addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][$j]['index']];
									}?>
									</tr><?
								}?>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>" style="" colspan="2">공제총액(C + D + F)</th>
					<td class="right" style="" colspan="2"><?=number_format($deduct_amt);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>" colspan="2">차인지급액</th>
					<td class="right" colspan="2"><?=number_format($diff_amt);?></td>
				</tr>

				<tr>
					<th class="<? if($my_table == 'my_table'){?>center<?}else{?>head<?} ?>" style="" rowspan="10">기<br>본<br>근<br>무</th>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>" style="">합계(A)</th>
					<td class="right" style="" colspan="2"><?=number_format($tot_basic_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">근무일수</th>
					<td class="right" colspan="2"><?=number_format($work_cnt);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">근무시간/기본급</th>
					<td class="right" style=""><?=number_format($work_time);?></td>
					<td class="right" style=""><?=number_format($base_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">주휴일수/수당</th>
					<td class="right"><?=number_format($weekly_cnt);?></td>
					<td class="right"><?=number_format($weekly_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">유급일수/수당</th>
					<td class="right"><?=number_format($paid_cnt);?></td>
					<td class="right"><?=number_format($bath_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">목욕횟수/수당</th>
					<td class="right"><?=number_format($bath_cnt);?></td>
					<td class="right"><?=number_format($work_cnt);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">간호횟수/수당</th>
					<td class="right"><?=number_format($work_cnt);?></td>
					<td class="right"><?=number_format($work_cnt);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">식대보조비</th>
					<td class="right" colspan="2"><?=number_format($meal_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">차량유지비</th>
					<td class="right" colspan="2"><?=number_format($car_keep_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">보전수당</th>
					<td class="right" colspan="2"><?=number_format($bojeon_pay);?></td>
				</tr>

				<tr>
					<th class="<? if($my_table == 'my_table'){?>center<?}else{?>head<?} ?>" rowspan="6">초<br>과<br>근<br>무</th>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">합계(B)</th>
					<td class="right" colspan="2"><?=number_format($tot_sudang_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">연장시간/수당</th>
					<td class="right"><?=number_format($prolong_hour);?></td>
					<td class="right"><?=number_format($prolong_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">야간시간/수당</th>
					<td class="right"><?=number_format($night_hour);?></td>
					<td class="right"><?=number_format($night_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">휴일시간/수당</th>
					<td class="right"><?=number_format($holiday_hour);?></td>
					<td class="right"><?=number_format($holiday_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">휴연시간/수당</th>
					<td class="right"><?=number_format($holiday_prolong_hour);?></td>
					<td class="right"><?=number_format($holiday_prolong_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">휴야시간/수당</th>
					<td class="right"><?=number_format($holiday_night_hour);?></td>
					<td class="right"><?=number_format($holiday_night_pay);?></td>
				</tr>

				<tr>
					<th class="<? if($my_table == 'my_table'){?>center<?}else{?>head<?} ?>" rowspan="5">보<br>험<br>항<br>목</th>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">합계(C)</th>
					<td class="right" colspan="2"><?=number_format($tot_ins_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">국민연금</th>
					<td class="right" colspan="2"><?=number_format($pension_amt);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">건강보험</th>
					<td class="right" colspan="2"><?=number_format($health_amt);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">장기요양</th>
					<td class="right" colspan="2"><?=number_format($care_amt);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">고용보헙</th>
					<td class="right" colspan="2"><?=number_format($employ_amt);?></td>
				</tr>

				<tr>
					<th class="<? if($my_table == 'my_table'){?>center<?}else{?>head<?} ?>" rowspan="3">소<br>득<br>세</th>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">합계(D)</th>
					<td class="right" colspan="2"><?=number_format($tot_tax_pay);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">갑근세</th>
					<td class="right" colspan="2"><?=number_format($tax_amt_1);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">주민세</th>
					<td class="right" colspan="2"><?=number_format($tax_amt_2);?></td>
				</tr>

				<tr>
					<th class="<? if($my_table == 'my_table'){?>center<?}else{?>head<?} ?>" rowspan="<?=$addon_count[1]+2;?>">지<br>급</th>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">합계(E)</th>
					<td class="right" colspan="2"><?=number_format($addon_pay_1);?></td>
				</tr>
				<tr>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">직급수당</th>
					<td class="right" colspan="2"><?=number_format($rank_pay);?></td>
				</tr>
				<?
					//tot_addon
					for($i=0; $i<$addon_count[1]; $i++){?>
						<tr>
							<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>"><?=$addon_caption[1][$i]['subject'];?></th>
							<td class="right" colspan="2"><?=number_format($tot_addon[1][$addon_caption[1][$i]['index']]);?></td>
						</tr><?
					}
				?>

				<tr>
					<th class="<? if($my_table == 'my_table'){?>center bottom<?}else{?>head<?} ?>" rowspan="<?=$addon_count[2]+1;?>">공<br>제</th>
					<th class="<? if($my_table == 'my_table'){?>left<?}else{?>head<?} ?>">합계(F)</th>
					<td class="right" colspan="2"><?=number_format($addon_pay_2);?></td>
				</tr>
				<?
					for($i=0; $i<$addon_count[2]; $i++){
						$class_bottom = '';
						if ($my_table == 'my_table'){
							if ($i+1 == $addon_count[2]){
								$class_bottom = 'bottom';
							}
						}?>
						<tr>
							<th class="<? if($my_table == 'my_table'){?>left <?=$class_bottom;}else{?>head<?} ?>"><?=$addon_caption[2][$i]['subject'];?></th>
							<td class="right <? if($i+1 == $addon_count[2]){?>bottom<?} ?>" colspan="2"><?=number_format($tot_addon[2][$addon_caption[2][$i]['index']]);?></td>
						</tr><?
					}
				?>
			</tbody>
		</table>
		</div><?
	}else{?>
		<table border="1">
			<tr>
				<td class="head_c" rowspan="3">&nbsp;</td>
				<td class="head_c" rowspan="3">급여총액<br>(A + B + E)</td>
				<td class="head_c" rowspan="3">공제총액<br>(C + D + F)</td>
				<td class="head_c" rowspan="3" style="border-right:2px solid #000000;">차인지급액</td>
				<td class="head_c" colspan="15" style="border-right:2px solid #000000;">기본근무</td>
				<td class="head_c" colspan="11" style="border-right:2px solid #000000;">초과근무</td>
				<td class="head_c" colspan="5" style="border-right:2px solid #000000;">보험항목</td>
				<td class="head_c" colspan="3" style="border-right:2px solid #000000;">소득세</td>
				<td class="head_c" colspan="<?=$addon_count[1]+2;?>" style="border-right:2px solid #000000;">지급수당</td>
				<td class="head_c" colspan="<?=$addon_count[2]+1;?>">공제항목</td>
			</tr>
			<tr>
				<td class="head_c" rowspan="2">합계(A)</td>
				<td class="head_c" colspan="3">근무수</td>
				<td class="head_c" colspan="2">주휴</td>
				<td class="head_c" colspan="2">유급</td>
				<td class="head_c" colspan="2">목욕</td>
				<td class="head_c" colspan="2">간호</td>
				<td class="head_c" rowspan="2">식대보조비</td>
				<td class="head_c" rowspan="2">차량유지비</td>
				<td class="head_c" rowspan="2" style="border-right:2px solid #000000;">보전수당</td>
				<td class="head_c" rowspan="2">합계(B)</td>
				<td class="head_c" colspan="2">연장</td>
				<td class="head_c" colspan="2">야간</td>
				<td class="head_c" colspan="2">휴일</td>
				<td class="head_c" colspan="2">휴연</td>
				<td class="head_c" colspan="2" style="border-right:2px solid #000000;">휴야</td>
				<td class="head_c" rowspan="2">합계(C)</td>
				<td class="head_c" rowspan="2">국민연금</td>
				<td class="head_c" rowspan="2">건강보험</td>
				<td class="head_c" rowspan="2">장기요양</td>
				<td class="head_c" rowspan="2" style="border-right:2px solid #000000;">고용보험</td>
				<td class="head_c" rowspan="2">합계(D)</td>
				<td class="head_c" rowspan="2">갑근세</td>
				<td class="head_c" rowspan="2" style="border-right:2px solid #000000;">주민세</td>
				<td class="head_c" rowspan="2">합계(E)</td>
				<td class="head_c" rowspan="2" <? if($addon_count[1] == 0){?>style="border-right:2px solid #000000;"<?} ?>>직급수당</td>
				<?
					for($i=0; $i<$addon_count[1]; $i++){?>
						<td class="head_c" rowspan="2" <? if($i+1==$addon_count[1]){?>style="border-right:2px solid #000000;"<?} ?>><?=($addon_caption[1][$i]['subject'] != '' ? $addon_caption[1][$i]['subject'] : '&nbsp;');?></td><?
					}
				?>
				<td class="head_c" rowspan="2">합계(F)</td>
				<?
					for($i=0; $i<$addon_count[2]; $i++){?>
						<td class="head_c" rowspan="2"><?=($addon_caption[2][$i]['subject'] != '' ? $addon_caption[2][$i]['subject'] : '&nbsp;');?></td><?
					}
				?>
			</tr>
			<tr>
				<td class="head_c">일수</td>
				<td class="head_c">시간</td>
				<td class="head_c">기본급</td>
				<td class="head_c">일수</td>
				<td class="head_c">수당</td>
				<td class="head_c">일수</td>
				<td class="head_c">수당</td>
				<td class="head_c">횟수</td>
				<td class="head_c">수당</td>
				<td class="head_c">횟수</td>
				<td class="head_c">수당</td>
				<td class="head_c">시간</td>
				<td class="head_c">수당</td>
				<td class="head_c">시간</td>
				<td class="head_c">수당</td>
				<td class="head_c">시간</td>
				<td class="head_c">수당</td>
				<td class="head_c">시간</td>
				<td class="head_c">수당</td>
				<td class="head_c">시간</td>
				<td class="head_c" style="border-right:2px solid #000000;">수당</td>
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
				<td class="head_c" style="border-bottom:2px solid #000000;">합계</td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($total_amt);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($deduct_amt);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000; border-right:2px solid #000000;"><?=number_format($diff_amt);?></td>

				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($tot_basic_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($work_cnt);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($work_time);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($base_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($weekly_cnt);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($weekly_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($paid_cnt);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($paid_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($bath_cnt);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($bath_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($nursing_cnt);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($nursing_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($meal_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($car_keep_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000; border-right:2px solid #000000;"><?=number_format($bojeon_pay);?></td>

				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($tot_sudang_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($prolong_hour);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($prolong_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($night_hour);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($night_hour);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($holiday_hour);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($holiday_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($holiday_prolong_hour);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($holiday_prolong_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($holiday_night_hour);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000; border-right:2px solid #000000;"><?=number_format($holiday_night_pay);?></td>

				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($tot_ins_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($pension_amt);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($health_amt);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($care_amt);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000; border-right:2px solid #000000;"><?=number_format($employ_amt);?></td>

				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($tot_tax_pay);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($tax_amt_1);?></td>
				<td class="filed_r" style="border-bottom:2px solid #000000; border-right:2px solid #000000;"><?=number_format($tax_amt_2);?></td>

				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($addon_pay_1);?></td>
				<td class="filed_r" <? if($addon_count[1] == 0){?>style="border-bottom:2px solid #000000; border-right:2px solid #000000;"<?} ?>><?=number_format($rank_pay);?></td><?
				for($j=0; $j<$addon_count[1]; $j++){?>
					<td class="filed_r" <? if($i+1==$addon_count[1]){?>style="border-bottom:2px solid #000000; border-right:2px solid #000000;"<?} ?>><?=number_format($tot_addon[1][$addon_caption[1][$j]['index']]);?></td><?
				}?>
				<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($addon_pay_2);?></td><?
				for($j=0; $j<$addon_count[2]; $j++){?>
					<td class="filed_r" style="border-bottom:2px solid #000000;"><?=number_format($tot_addon[2][$addon_caption[2][$j]['index']]);?></td><?
				}?>
			</tr><?
			for($i=0; $i<$mem_cnt; $i++){?>
				<tr>
					<td class="head"><?=$mem[$i]['member_nm'];?></td>
					<td class="filed_r"><?=number_format($mem[$i]['total_amt']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['deduct_amt']);?></td>
					<td class="filed_r" style="border-right:2px solid #000000;"><?=number_format($mem[$i]['diff_amt']);?></td>

					<td class="filed_r"><?=number_format($mem[$i]['tot_basic_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['work_cnt']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['work_time']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['base_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['weekly_cnt']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['weekly_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['paid_cnt']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['paid_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['bath_cnt']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['bath_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['nursing_cnt']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['nursing_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['meal_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['car_keep_pay']);?></td>
					<td class="filed_r" style="border-right:2px solid #000000;"><?=number_format($mem[$i]['bojeon_pay']);?></td>

					<td class="filed_r"><?=number_format($mem[$i]['tot_sudang_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['prolong_hour']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['prolong_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['night_hour']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['night_hour']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['holiday_hour']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['holiday_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['holiday_prolong_hour']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['holiday_prolong_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['holiday_night_hour']);?></td>
					<td class="filed_r" style="border-right:2px solid #000000;"><?=number_format($mem[$i]['holiday_night_pay']);?></td>

					<td class="filed_r"><?=number_format($mem[$i]['tot_ins_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['pension_amt']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['health_amt']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['care_amt']);?></td>
					<td class="filed_r" style="border-right:2px solid #000000;"><?=number_format($mem[$i]['employ_amt']);?></td>

					<td class="filed_r"><?=number_format($mem[$i]['tot_tax_pay']);?></td>
					<td class="filed_r"><?=number_format($mem[$i]['tax_amt_1']);?></td>
					<td class="filed_r" style="border-right:2px solid #000000;"><?=number_format($mem[$i]['tax_amt_2']);?></td>

					<td class="filed_r"><?=number_format($tot_addon_pay[1][$mem[$i]['member_cd']]);?></td>
					<td class="filed_r" <? if($addon_count[1] == 0){?>style="border-right:2px solid #000000;"<?} ?>><?=number_format($mem[$i]['rank_pay']);?></td><?
					for($j=0; $j<$addon_count[1]; $j++){?>
						<td class="filed_r" <? if($i+1==$addon_count[1]){?>style="border-right:2px solid #000000;"<?} ?>><?=number_format($addon_pay[$mem[$i]['member_cd']][1][$addon_caption[1][$j]['index']]);?></td><?
					}?>
					<td class="filed_r"><?=number_format($tot_addon_pay[2][$mem[$i]['member_cd']]);?></td><?
					for($j=0; $j<$addon_count[2]; $j++){?>
						<td class="filed_r"><?=number_format($addon_pay[$mem[$i]['member_cd']][2][$addon_caption[2][$j]['index']]);?></td><?
					}?>
				</tr><?
			}
			?>
		</table><?
	}

	unset($addon_caption);
	unset($addon_index);
	unset($addon_count);
	unset($addon_pay);
	unset($addon_pay_count);
	unset($tot_addon);
?>