<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code    = $_POST['code'];
	$year    = $_POST['year'];
	$month   = $_POST['month'];
	$lastday = $myF->lastDay($year, $month);
	$today   = date('Ymd', mktime());


	/*********************************************************

		데이타

	*********************************************************/
	$sql = 'select cld_yymm
			,      cld_seq
			,      cld_no
			,      cld_dt
			,	   cld_from
			,      cld_to
			,      timediff(cld_to, cld_from) as proctime
			,      cld_fulltime
			,      cld_subject
			,      cld_contents
			,      cld_reg_nm
			  from calendar
			 where org_no   = \''.$code.'\'
			   and cld_yymm = \''.$year.$month.'\'
			   and del_flag = \'N\'
			 order by cld_from';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$day = intval(substr($row['cld_dt'], 8, 2));

		$id = sizeof($data[$day]);

		$data[$day][$id] = array('code'		=>$code
								,'yymm'		=>$row['cld_yymm']
								,'seq'		=>$row['cld_seq']
								,'no'		=>$row['cld_no']
								,'date'		=>$row['cld_dt']
								,'from'		=>substr($row['cld_from'], 0, 5)
								,'to'		=>substr($row['cld_to'], 0, 5)
								,'proctime'	=>$row['proctime']
								,'fulltime'	=>$row['cld_fulltime']
								,'subject'	=>stripslashes($row['cld_subject'])
								,'contents'	=>stripslashes($row['cld_contents'])
								,'writer'	=>$row['cld_reg_nm']);

		$id ++;
	}

	$conn->row_free();


	ob_start();

	echo '<table class=\'my_table\' style=\'width:100%;\'>
			<colgroup>
				<col width=\'70px\'>
				<col width=\'50px\'>
				<col width=\'150px\'>
				<col width=\'70px\'>
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class=\'head\'>일자</th>
					<th class=\'head\'>추가</th>
					<th class=\'head\'>시간</th>
					<th class=\'head\'>작성자</th>
					<th class=\'head last\'>제목</th>
				</tr>
			</thead>
			<tbody>';

	for($i=1; $i<=$lastday; $i++){
		$date    = $month.($i < 10 ? '.0' : '.').$i;
		$regDt   = $year.'-'.str_replace('.', '-', $date);
		$weekday = $myF->weekday($regDt);

		switch($weekday){
			case '일':
				$weekday = '<span style=\'color:#ff0000;\'>'.$weekday.'</span>';
				break;
			case '토':
				$weekday = '<span style=\'color:#0000ff;\'>'.$weekday.'</span>';
				break;
			default:
		}

		if ($year.str_replace('.', '', $date) == $today){
			$bgcolor = 'ffffd9';
		}else{
			$bgcolor = 'ffffff';
		}

		echo '<tr>
				<th class=\'left\'>
					<div style=\'float:left; width:auto;\' class=\'bold\'>'.$date.'</div>
					<div style=\'float:left; width:auto; margin-left:10px;\'>'.$weekday.'</div>
				</th>
				<th class=\'center\'><img src=\'../image/btn/btn_add_out.gif\' style=\'cursor:pointer;\' onclick=\'_regCalendar(this,"'.$code.'","'.$regDt.'","0","0","popup");\' onmouseover=\'this.src="../image/btn/btn_add_over.gif";\' onmouseout=\'this.src="../image/btn/btn_add_out.gif";\'></th>
				<td class=\'center last\' colspan=\'3\' style=\'background-color:'.$bgcolor.';\'>

					<table class=\'my_table\' style=\'width:100%;\'>
						<colgroup>
							<col width=\'150px\'>
							<col width=\'70px\'>
							<col>
						</colgroup>
						<tbody>';

						if (is_array($data[$i])){
							$cnt = sizeof($data[$i]);
							foreach($data[$i] as $j => $row){
								if ($j + 1 == $cnt){
									$class = ' bottom';
								}else{
									$class = '';
								}

								if ($row['fulltime'] == 'Y'){
									$fromtime = '종일일정';
								}else{
									$temptime = explode(':', $row['proctime']);
									$fromtime = $row['from'].'('.(intval($temptime[0]) > 0 ? intval($temptime[0]).'시간 ' : '').(intval($temptime[1]) > 0 ? intval($temptime[1]).'분' : '').')';
								}

								echo '<tr>
										<td class=\'center'.$class.'\'><div class=\'left\'>'.$fromtime.'</div></td>
										<td class=\'center'.$class.'\'><div class=\'left\'>'.$row['writer'].'</div></td>
										<td class=\'center'.$class.' last\'><div class=\'left\' style=\'width:auto;\'><a href=\'#\' onclick=\'_viewCalendar(this,"'.$row['code'].'","'.$row['yymm'].'","'.$row['seq'].'","'.$row['no'].'","popup"); return false;\'>'.stripslashes($row['subject']).'</a></div></td>
									  </tr>';
							}
						}

		echo '			</tbody>
					</table>

				</td>
			  </tr>';
	}

	echo '	</tbody>
			<tfoot>
				<tr>
					<td class=\'left bottom last\' colspan=\'5\'>&nbsp;</td>
				</tr>
			</tfoot>
		  </table>';


	$html = ob_get_contents();

	ob_end_clean();

	echo $html;


	include_once('../inc/_db_close.php');
?>