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

		변수

	*********************************************************/
	$preYYMM    = explode('-', $myF->dateAdd('month', -1, $year.'-'.$month.'-01', 'Y-m-d'));
	$preLastday = $myF->lastDay($preYYMM[0], $preYYMM[1]);
	$nextYYMM   = explode('-', $myF->dateAdd('month', 1, $year.'-'.$month.'-01', 'Y-m-d'));
	$weekHeight = 130;


	/*********************************************************

		휴일일정

	*********************************************************/
	$sql = 'select mdate as date
			,      holiday_name as nm
			  from tbl_holiday
			 where left(mdate, 6) in (\''.$year.$month.'\', \''.$preYYMM[0].$preYYMM[1].'\', \''.$nextYYMM[0].$nextYYMM[1].'\')';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$holiday[$row['date']] = $row['nm'];
	}

	$conn->row_free();


	ob_start();


	echo '<table class=\'my_table\' style=\'width:100%; height:100%;\'>
			<colgroup>
				<col width=\''.(100/7).'%\' span=\'7\'>
			</colgroup>
			<thead>
				<tr>
					<th class=\'head bold\'><span style=\'color:#ff0000;\'>일</span></th>
					<th class=\'head bold\'>월</th>
					<th class=\'head bold\'>화</th>
					<th class=\'head bold\'>수</th>
					<th class=\'head bold\'>목</th>
					<th class=\'head bold\'>금</th>
					<th class=\'head bold last\'><span style=\'color:#0000ff;\'>토</span></th>
				</tr>
			</thead>
			<tbody>';

	/*********************************************************
		1일과 말일의 요일을 구한다.
		*****************************************************/
		$startWeekday = date('w', strtotime($year.'-'.$month.'-01'));
		$endWeekday   = date('w', strtotime($year.'-'.$month.'-'.$lastday));
	/********************************************************/


	/*********************************************************
		1일이전 요일까지 빈공간을 채운다.
		*********************************************************/
		$data = getCalendar($conn, $code, $preYYMM[0].$preYYMM[1]);

		for($i=0; $i<$startWeekday; $i++){
			if ($i == 0) echo '<tr>';

			if ($i > 0 && $i % 7 == 0){
				/*********************************************************
					토요일
				*********************************************************/
				$clsLast = ' last';
			}else{
				$clsLast = '';
			}

			#전달의 일자
			$day   = $preLastday - $startWeekday + ($i + 1);
			#일색상
			$color = $myF->_weekColor($i, 'soft');
			#등록일
			$regDt = $preYYMM[0].$preYYMM[1].($day < 10 ? '0' : '').$day;

			echo '<td id=\'this_'.$regDt.'\'
					  class=\'top center'.$clsLast.'\'
					  style=\'height:'.$weekHeight.'px;\'>'.getDayData($regDt, $color, $day, $data[$day], $holiday[$regDt]).'</td>';
		}
	/********************************************************/


	$data = getCalendar($conn, $code, $year.$month);

	for($i=1; $i<=$lastday; $i++){
		if ($startWeekday > 0 && $startWeekday % 6 == 0){
			/*********************************************************
				토요일
			*********************************************************/
			$clsLast = ' last';
		}else{
			$clsLast = '';
		}


		if ($startWeekday == 0){
			echo '<tr>';
		}else{
			if ($startWeekday % 7 == 0){
				$startWeekday = 0;
				echo '</tr>';
				echo '<tr>';
			}
		}

		#일색상
		$color = $myF->_weekColor($startWeekday % 7);


		if ($today == $year.$month.($i < 10 ? ' ' : '').$i){
			$bgcolor = '#ffffd9';
		}else{
			$bgcolor = '#ffffff';
		}

		#등록일
		$regDt = $year.$month.($i < 10 ? '0' : '').$i;

		echo '<td id=\'this_'.$regDt.'\'
				  class=\'top center '.$clsLast.'\'
				  style=\'height:'.$weekHeight.'px; background-color:'.$bgcolor.';\'>'.getDayData($regDt, $color, $i, $data[$i], $holiday[$regDt]).'</td>';
		$startWeekday ++; //요일증가
	}


	/*********************************************************
		말일부터 빈공간을 채운다.
		*****************************************************/
		$data = getCalendar($conn, $code, $nextYYMM[0].$nextYYMM[1]);
		$day  = 0;

		for($i=$endWeekday+1; $i<7; $i++){
			if ($i % 6 == 0){
				/*********************************************************
					토요일
				*********************************************************/
				$clsLast = ' last';
			}else{
				$clsLast = '';
			}

			#다음달일자
			$day ++;

			#일색상
			$color = $myF->_weekColor($i, 'soft');

			#등록일
			$regDt = $nextYYMM[0].$nextYYMM[1].($day < 10 ? '0' : '').$day;

			echo '<td id=\'this_'.$regDt.'\'
					  class=\'top center '.$clsLast.'\'
					  style=\'height:'.$weekHeight.'px;\'>'.getDayData($regDt, $color, $day, $data[$day], $holiday[$regDt]).'</td>';
		}
	/********************************************************/
	echo '</tr>';


	echo '	</tbody>
			<tfoot>
				<tr>
					<td class=\'center bottom last\' colspan=\'7\'>&nbsp;</td>
				</tr>
			</tfoot>
		  </table>';


	$html = ob_get_contents();

	ob_end_clean();

	echo $html;


	include_once('../inc/_db_close.php');


	function getDayData($date, $color, $day, $data = null, $holiday = null){
		$html = '<div class=\'left\' style=\'width:99%; color:'.$color.';\'>'.$day.(!empty($holiday) ? ' ['.$holiday.']' : '').'</div>';

		if (is_array($data)){
			$cnt = 0;

			foreach($data as $i => $row){
				if ($i < 3){
					if ($i < 2){
						$style = 'border-bottom:1px solid #efefef;';
					}else{
						$style = 'border-bottom:1px solid #ffffff;';
					}

					if ($row['fulltime'] == 'Y'){
						$time   = '';
						$style .= 'font-weight:bold;';
					}else{
						$time = $row['from'] * 30;
						$hour = floor($time / 60);
						$hour = ($hour < 10 ? '0' : '').$hour;
						$min  = $time % 60;
						$min  = ($min < 10 ? '0' : '').$min;
						$time = $hour.':'.$min.' ';
					}

					$html .= '<div class=\'left nowrap\' style=\'width:110px; cursor:pointer; '.$style.'\' title=\''.$row['subject'].'\' onclick=\'_viewCalendar(this,"'.$row['code'].'","'.$row['yymm'].'","'.$row['seq'].'","'.$row['no'].'","popup");\'>'.$time.$row['subject'].'</div>';
				}else{
					$cnt ++;
				}
			}
			$i ++;
		}else{
			$i = 0;
		}

		for($j=$i; $j<3; $j++){
			$html .= '<div class=\'left\' style=\'width:auto; border-bottom:1px solid #ffffff;\'>&nbsp;</div>';
		}

		$html .= '<div class=\'center\' style=\'border-top:1px dotted #cccccc; width:99%; margin-top:5px;\'>
					<div style=\'float:left; width:auto; margin-left:3px; margin-top:3px; cursor:pointer;\' onclick=\'_regCalendar(this,"'.$code.'","'.$date.'","0","0","popup");\'><img src=\'../image/btn/btn_add_out.gif\' onmouseover=\'this.src="../image/btn/btn_add_over.gif";\' onmouseout=\'this.src="../image/btn/btn_add_out.gif";\'></div>';

		if ($cnt > 0){
			$html .= '<div style=\'float:right; width:auto; margin-right:5px; cursor:pointer;\' onclick=\'_viewCalendar(document.getElementById("this_'.$date.'"),"'.$row['code'].'","'.$row['yymm'].'","'.$row['seq'].'","'.$date.'","list");\'>'.$cnt.'건</div>
					  <div style=\'float:right; width:auto; margin-top:7px; margin-right:5px;\'><img src=\'../image/calendar_cnt.gif\'></div>';
		}

		$html .= '</div>';

		return $html;
	}



	/*********************************************************

		데이타 조회

	*********************************************************/
	function getCalendar($conn, $code, $yymm){
		$sql = 'select cld_seq
				,      cld_no
				,      cld_dt
				,	   cld_from
				,      cld_to
				,      cld_fulltime
				,      cld_subject
				,      cld_contents
				,      cld_reg_nm
				  from calendar
				 where org_no   = \''.$code.'\'
				   and cld_yymm = \''.$yymm.'\'
				   and del_flag = \'N\'
				 order by cld_from';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$tmp      = explode(':', $row['cld_from']);
			$fromTime = ($tmp[0] * 60 + $tmp[1]) / 30;

			$tmp    = explode(':', $row['cld_to']);
			$toTime = ($tmp[0] * 60 + $tmp[1]) / 30;

			$day = intval(substr($row['cld_dt'], 8, 2));

			$id = sizeof($data[$day]);

			$data[$day][$id] = array('code'		=>$code
									,'yymm'		=>$yymm
									,'seq'		=>$row['cld_seq']
									,'no'		=>$row['cld_no']
									,'date'		=>$row['cld_dt']
									,'from'		=>$fromTime
									,'to'		=>$toDate
									,'fulltime'	=>$row['cld_fulltime']
									,'subject'	=>stripslashes($row['cld_subject'])
									,'contents'	=>stripslashes($row['cld_contents'])
									,'writer'	=>$row['cld_reg_nm']);

			$id ++;
		}

		$conn->row_free();

		return $data;
	}
?>