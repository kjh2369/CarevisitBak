<?
	$date = $myF->dateStyle($no, '.');
	$weekday = $myF->weekday($date);

	echo '<div id=\'this_popup\'
			   style=\'width:350px; height:259px; border:2px solid #cccccc; background-color:#ffffff;\'>

			<div style=\'height:39px; border-bottom:1px solid #cccccc;\'>
				<div style=\'float:left; width:auto; font-weight:bold;\'>
					<div style=\'float:left; width:auto; margin-left:10px; margin-top:12px;\'><img src=\'../image/bg_icon_1.gif\'></div>
					<div class=\'nowrap\' style=\'float:left; width:auto; margin-left:5px; margin-top:8px;\'>'.$date.'('.$weekday.')</span>
				</div>
				<div style=\'float:right; width:auto;\'><img src=\'../image/close.gif\' style=\'margin-top:10px; margin-right:10px;\' onclick=\'_regPopupClose();\'></div>
			</div>
		  </div>';

	echo '<table style=\'width:100%;\'>
			<colgroup>
				<col width=\'110px\'>
				<col width=\'70px\'>
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class=\'head\' style=\'background-color:#efefef; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc;\'>시간</th>
					<th class=\'head\' style=\'background-color:#efefef; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc;\'>작성자</th>
					<th class=\'head\' style=\'background-color:#efefef; border-bottom:1px solid #cccccc; border-right:0;\'>제목</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class=\'center top\' style=\'border:0;\' colspan=\'3\'>
						<div style=\'overflow-x:hidden; overflow-y:scroll; width:100%; height:195px;\'>
							<table class=\'my_table\' style=\'width:100%; border:0;\'>
								<colgroup>
									<col width=\'110px\'>
									<col width=\'70px\'>
									<col>
								</colgroup>
								<tbody>';

								$sql = 'select cld_yymm as yymm
										,      cld_seq as seq
										,      cld_no as no
										,      cld_from as from_time
										,      cld_to as to_time
										,      timediff(cld_to, cld_from) as proctime
										,      cld_reg_nm as reg_nm
										,      cld_fulltime as fulltime
										,      cld_subject as subject
										,      cld_contents as contents
										,      cld_reg_nm as reg_nm
										  from calendar
										 where org_no   = \''.$code.'\'
										   and cld_dt   = \''.$date.'\'
										   and del_flag = \'N\'
										 group by cld_from, cld_to, cld_fulltime, cld_subject, cld_reg_nm';


								$conn->query($sql);
								$conn->fetch();

								$rowCount = $conn->row_count();

								for($i=0; $i<$rowCount; $i++){
									$row = $conn->select_row($i);

									if ($row['fulltime'] == 'Y'){
										$time = '종일일정';
									}else{
										$temptime = explode(':', $row['proctime']);
										$time  = substr($row['from_time'], 0, 5);
										$time .= '('.(intval($temptime[0]) > 0 ? intval($temptime[0]).'시간 ' : '').(intval($temptime[1]) > 0 ? intval($temptime[1]).'분' : '').')';
									}

									$reg_nm  = $row['reg_nm'];
									$subject = stripslashes($row['subject']);

									echo '<tr>
											<td class=\'center\' style=\'border-left:0;\'><div class=\'left\'>'.$time.'</div></td>
											<td class=\'center\' style=\'border-left:0;\'><div class=\'left\'>'.$reg_nm.'</div></td>
											<td class=\'center\'><div class=\'nowrap\' style=\'width:136px; text-align:left;><a href=\'#\' onclick=\'_viewCalendar(this,"'.$code.'","'.$row['yymm'].'","'.$row['seq'].'","'.$row['no'].'","popup");\'>'.$subject.'</a></div></td>
										  </tr>';
								}

								$conn->row_free();

	echo '						</tbody>
							  </table>
						</div>
					</td>
				</tr>
			</tbody>
		  </table>';
?>