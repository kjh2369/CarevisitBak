<table class="my_table" style="margin:20px 10px 10px 10px; border:2px solid #0e69b0;">
	<colgroup>
		<col width="60px">
		<col width="70px">
		<col width="90px">
		<col width="90px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">마감월</th>
			<th class="head" colspan="2">실적</th>
			<th class="head" colspan="2">급여계산</th>
		</tr>
		<tr>
			<th class="head">마감여부</th>
			<th class="head">마감일자</th>
			<th class="head">계산일자</th>
			<th class="head">마감여부</th>
		</tr>
	</thead>
	<tbody>
	<?
		$today = date('Y-m-d', mktime());

		$month_b = $myF->dateAdd('month', -1, $year.'-'.($month<10?'0':'').$month.'-01', 'Ym');
		$month_n = $myF->dateAdd('month',  1, $year.'-'.($month<10?'0':'').$month.'-01', 'Ym');

		$sql = "select cast(right(closing_yymm, 2) as unsigned)  as yymm
				,      act_cls_flag
				,      act_cls_dt_from
				,      act_cls_ent_dt
				,      act_bat_conf_flag
				,      act_bat_conf_dt
				,      act_bat_can_flag
				,      act_bat_can_dt
				,      salary_cls_flag
				,      salary_bat_calc_flag
				,      salary_bat_calc_dt
				,      salary_bat_can_flag
				,      salary_bat_can_dt
				,      salary_cls_dt_from
				,      salary_cls_ent_dt
				  from closing_progress
				 where org_no       = '$code'
				   and closing_yymm between '$month_b' and '$month_n'";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				if ($i == 1){
					$class_bold = 'my_bold';
				}else{
					$class_bold = '';
				}

				if ($row['act_cls_flag'] == 'Y'){
					$closing_str    = '<font color="ff0000">마감</font>';
					$closing_reg_dt = $row['act_cls_ent_dt'];
				}else{
					$closing_str    = '-';
					$closing_reg_dt = '&nbsp;';
				}

				if ($row['act_bat_conf_flag'] == 'Y' && $row['act_bat_can_flag'] == 'N'){
					$conf_dt = $row['act_bat_conf_dt'];
				}else{
					$conf_dt = '-';
				}

				if ($row['salary_bat_calc_flag'] == 'Y' && $row['salary_bat_can_flag'] == 'N'){
					$calc_dt = $row['salary_bat_calc_dt'];
				}else{
					$calc_dt = '-';
				}

				if ($row['salary_cls_flag'] == 'Y'){
					$salary_str = '마감';
				}else{
					$salary_str    = '-';
				}?>
				<tr>
					<td class="center <?=$class_bold;?>"><?=$row['yymm'];?>월</td>
					<td class="center <?=$class_bold;?>"><?=$closing_str;?></td>
					<td class="center <?=$class_bold;?>"><?=$conf_dt;?></td>
					<td class="center <?=$class_bold;?>"><?=$calc_dt;?></td>
					<td class="center <?=$class_bold;?>"><?=$salary_str;?></td>
				</tr><?
			}
		}else{?>
			<tr>
				<td class="center last" colspan="8"><?=$myF->message('nodata', 'N');?></td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
</table>