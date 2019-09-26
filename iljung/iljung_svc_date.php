<?
	########################################################
	#
	# 제공요일 및 일자
	#
	########################################################

	if ($mode != 'IN'){
		$mWeek = date('w',mktime(0,0,0,$month,$day,$year));
	}

	if ($mode == 'IN' || $mode == 'PATTERN'){
		$sql = "select m02_ygunmu_mon"
			 . ",      m02_ygunmu_tue"
			 . ",      m02_ygunmu_wed"
			 . ",      m02_ygunmu_thu"
			 . ",      m02_ygunmu_fri"
			 . ",      m02_ygunmu_sat"
			 . ",      m02_ygunmu_sun"
			 . ",      m02_ysugang"
			 . "  from m02yoyangsa"
			 . " where m02_ccode  = '".$code
			 . "'  and m02_mkind  = '".$kind
			 . "'  and m02_yjumin = '".$yoy1
			 . "'";
		$conn->query($sql);
		$row = $conn->fetch();

		$weekDay[1] = $row['m02_ygunmu_mon'] == 'Y' ? 'checked' : '';
		$weekDay[2] = $row['m02_ygunmu_tue'] == 'Y' ? 'checked' : '';
		$weekDay[3] = $row['m02_ygunmu_wed'] == 'Y' ? 'checked' : '';
		$weekDay[4] = $row['m02_ygunmu_thu'] == 'Y' ? 'checked' : '';
		$weekDay[5] = $row['m02_ygunmu_fri'] == 'Y' ? 'checked' : '';
		$weekDay[6] = $row['m02_ygunmu_sat'] == 'Y' ? 'checked' : '';
		$weekDay[0] = $row['m02_ygunmu_sun'] == 'Y' ? 'checked' : '';
		$conn->row_free();
	}else{
		$weekDay[1] = 'disabled=true';
		$weekDay[2] = 'disabled=true';
		$weekDay[3] = 'disabled=true';
		$weekDay[4] = 'disabled=true';
		$weekDay[5] = 'disabled=true';
		$weekDay[6] = 'disabled=true';
		$weekDay[0] = 'disabled=true';
		$weekDay[$mWeek] = 'checked onClick="this.checked=true;"';
	}

	if ($wrt_mode == 1){
		echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.'px;\'>';
		echo '	<colgroup>
					<col width=\'40px\'>
					<col>
				</colgroup>';
		echo '	<thead>';
		echo '		<tr>
						<th class=\'head bold\' colspan=\'2\'>서비스제공</th>
					</tr>';
		echo '	</thead>';
		echo '	<tbody>';
		echo '		<tr>
						<th class=\'head\'>요일</th>
						<td class=\'bold\'>';
						echo '<input name=\'weekDay1\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' onclick=\'_set_svc_week();\' '.$weekDay[1].'>월';
						echo '<input name=\'weekDay2\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' onclick=\'_set_svc_week();\' '.$weekDay[2].'>화';
						echo '<input name=\'weekDay3\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' onclick=\'_set_svc_week();\' '.$weekDay[3].'>수';
						echo '<input name=\'weekDay4\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' onclick=\'_set_svc_week();\' '.$weekDay[4].'>목';
						echo '<input name=\'weekDay5\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' onclick=\'_set_svc_week();\' '.$weekDay[5].'>금';
						echo '<input name=\'weekDay6\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' onclick=\'_set_svc_week();\' '.$weekDay[6].'><font color=\'#0000ff\'>토</font>';
						echo '<input name=\'weekDay0\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' onclick=\'_set_svc_week();\' '.$weekDay[0].'><font color=\'#ff0000\'>일</font>';
				echo '	</td>
					</tr>';
		echo '		<tr>
						<th class=\'head\'>일자</th>
						<td>';
						$lastDay = $myF->lastDay($year,$month);

						for($i=1; $i<=$lastDay; $i++){
							$week = date("w", strtotime($year.'-'.$month.'-'.($i<10?'0':'').$i));

							switch($week){
								case 6:
									$fontColor = '#0000ff';
									break;
								case 0:
									$fontColor = '#ff0000';
									break;
								default:
									$fontColor = '#000000';
							}

							echo '<div id="str_svc_dt_'.$i.'" class="my_box my_box_1" style="float:left; margin-left:2px;" style="cursor:pointer; color:'.$fontColor.';" onclick="_set_svc_dt(\''.$i.'\');">'.$i.'</div>';
							echo '<input name="svc_dt_'.$i.'" type="hidden" value="N">';
						}
				echo '	</td>
					</tr>';
		echo '	</tbody>';
		echo '</table>';
	}else{
		for($i=0; $i<=6; $i++) $weekDay[$i] = 'N';

		$weekDay[$mWeek] = 'Y';

		echo '<input name=\'weekDay1\' type=\'hidden\' value=\''.$weekDay[1].'\'>';
		echo '<input name=\'weekDay2\' type=\'hidden\' value=\''.$weekDay[2].'\'>';
		echo '<input name=\'weekDay3\' type=\'hidden\' value=\''.$weekDay[3].'\'>';
		echo '<input name=\'weekDay4\' type=\'hidden\' value=\''.$weekDay[4].'\'>';
		echo '<input name=\'weekDay5\' type=\'hidden\' value=\''.$weekDay[5].'\'>';
		echo '<input name=\'weekDay6\' type=\'hidden\' value=\''.$weekDay[6].'\'>';
		echo '<input name=\'weekDay0\' type=\'hidden\' value=\''.$weekDay[0].'\'>';
	}

	echo '<input name=\'svc_last_day\' type=\'hidden\' value=\''.$lastDay.'\'>';
	echo '<input name=\'svc_in_type\' type=\'hidden\' value=\'weekday\'>';
?>