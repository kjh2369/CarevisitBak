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
			<th class="head" colspan="3">실적마감</th>
			<th class="head" rowspan="2">실적일괄<br>확정일자</th>
			<th class="head" colspan="3">급여계산</th>
		</tr>
		<tr>
			<th class="head">마감여부</th>
			<th class="head">적용일자</th>
			<th class="head">등록일자</th>
			<th class="head">계산일자</th>
			<th class="head">마감일자</th>
			<th class="head">등록일자</th>
		</tr>
	</thead>
	<tbody>
	<?
		$today = date('Y-m-d', mktime());

		$month_b = $myF->dateAdd('month', -1, $year.'-'.($month<10?'0':'').$month.'-01', 'Ym');
		$month_n = $myF->dateAdd('month',  1, $year.'-'.($month<10?'0':'').$month.'-01', 'Ym');

		$sql = "select cast(right(closing_yymm, 2) as unsigned)  as yymm
				,      act_cls_dt_from
				,      act_cls_ent_dt
				,      act_bat_conf_flag
				,      act_bat_conf_dt
				,      act_bat_can_flag
				,      act_bat_can_dt
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

				if ($today >= $row['act_cls_dt_from']){
					$closing_str = '<font color="ff0000">마감</font>';
				}else{
					$closing_str = '-';
				}?>
				<tr>
					<td class="center <?=$class_bold;?>"><?=$row['yymm'];?>월</td>
					<td class="center <?=$class_bold;?>"><?=$closing_str;?></td>
					<td class="center <?=$class_bold;?>"><?=$row['act_cls_dt_from'];?></td>
					<td class="center <?=$class_bold;?>"><?=$row['act_cls_ent_dt'];?></td>
					<td class="center <?=$class_bold;?>"><? if($row['act_bat_conf_flag'] == 'Y' && $row['act_bat_can_flag'] == 'N'){echo $row['act_bat_conf_dt'];}else{echo '&nbsp;';} ?></td>
					<td class="center <?=$class_bold;?>"><? if($row['salary_bat_calc_flag'] == 'Y' && $row['salary_bat_can_flag'] == 'N'){echo $row['salary_bat_calc_dt'];}else{echo '&nbsp;';} ?></td>
					<td class="center <?=$class_bold;?>"><? if($row['salary_bat_calc_flag'] == 'Y' && $row['salary_bat_can_flag'] == 'N'){echo $row['salary_cls_dt_from'];}else{echo '&nbsp;';} ?></td>
					<td class="center <?=$class_bold;?>"><? if($row['salary_bat_calc_flag'] == 'Y' && $row['salary_bat_can_flag'] == 'N'){echo $row['salary_cls_ent_dt'];}else{echo '&nbsp;';} ?></td>
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