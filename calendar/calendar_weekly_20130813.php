<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code	= $_POST['code'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$week	= $_POST['week'];
	$fromDt	= $_POST['fromDt'];
	$toDt	= $_POST['toDt'];
	$lastday= $myF->lastDay($year, $month);
	$today	= date('Ymd', mktime());

	/*********************************************************

		변수

	*********************************************************/
	$preYYMM    = explode('-', $myF->dateAdd('month', -1, $year.'-'.$month.'-01', 'Y-m-d'));
	$preLastday = $myF->lastDay($preYYMM[0], $preYYMM[1]);
	$nextYYMM   = explode('-', $myF->dateAdd('month', 1, $year.'-'.$month.'-01', 'Y-m-d'));

	echo $fromDt.'/'.$toDt;

	//일정조회
	/*
	SELECT cld_seq
,      cld_yymm
,      cld_no
,      cld_dt
,      cld_from
,      cld_to
,      cld_fulltime
,      cld_subject
,      cld_contents
,      cld_reg_nm
FROM   calendar
WHERE  org_no = '1234'
AND    cld_dt >= '2013-07-28'
AND    cld_dt <= '2013-08-03'
AND    del_flag = 'N'
	*/


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

	$conn->row_free();?>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="110px" span="7">
		</colgroup>
		<tr>
			<th class="head">시간</th><?
			$calTime	= mktime(0, 0, 1, $month-1, 1, $year);
			$lastDay	= date('t', $calTime);			//총일수 구하기
			$weekly = array("일","월","화","수","목","금","토");
			$color = array("red","black","black","black","black","black","blue");

			if($fromDt == 1){
			}else {
				if($week == 1){
					for($i=$fromDt; $i<=$lastDay; $i++){
						$date = $year.(($month-1)<10?0:'').($month-1).($i<10?0:'').$i;
						$w = date('w', strtotime($date));?>
						<th class="head">
							<span style="color:#a9b3ef;"><?=$i;?></span> (<span style="color:<?=$color[$w];?>"><?=$weekly[$w];?></span>)
						</th><?
					}

					$fromDt = 1;
				}
			}

			$calTime2	= mktime(0, 0, 1, $month, 1, $year);
			$lastDay2	= date('t', $calTime2);			//총일수 구하기

			if($fromDt > $toDt){
				$toDt = $lastDay2;
			}

			for($i=$fromDt; $i<=$toDt; $i++){
				$date = $year.$month.($i<10?0:'').$i;
				$w = date('w', strtotime($date));

				if ($i == $toDt){
					$cls = 'last';
				}else{
					$cls = '';
				}?>
				<th class="head <?=$cls;?>">
					<span><?=$i;?></span> (<span style="color:<?=$color[$w];?>"><?=$weekly[$w];?></span>)
				</th><?
			}

			if ($fromDt > $_POST['toDt']){
				$fromDt = 1;

				for($i=$fromDt; $i<=$_POST['toDt']; $i++){
					$date = $year.(($month+1)<10?0:'').($month+1).($i<10?0:'').$i;
					$w = date('w', strtotime($date));

					if ($i == $toDt){
						$cls = 'last';
					}else{
						$cls = '';
					}?>
					<th class="head <?=$cls;?>">
						<span style="color:#a9b3ef;"><?=$i;?></span> (<span style="color:<?=$color[$w];?>"><?=$weekly[$w];?></span>)
					</th><?
				}
			}?>
		</tr>
		<tr>
			<th class="center">종일일정</th><?
				$fromDt = $_POST['fromDt'];

				if($fromDt == 1){
				}else {
					if($week == 1){
						$data = getCalendar($conn, $code, $year.($month<10?0:'').($month-1), $fromDt, $lastDay, '');

						for($i=$fromDt; $i<=$lastDay; $i++){
							$day = ($i<10?'0':'').$i;?>
							<td valign="top" onclick="_regCalendar(this,'<?=$code;?>','<?=$year.$month.$day;?>','0','0','popup');"><?=getDayData($regDt, $color, $i, $data[$i]['full'], $holiday[$regDt])?></td><?
						}
						$fromDt = 1;
					}
				}

				if($fromDt > $toDt){
					$toDt = $lastDay2;
				}

				$data = getCalendar($conn, $code, $year.$month, $fromDt, $toDt, '');
				for($i=$fromDt; $i<=$toDt; $i++){
					if ($i == $toDt){
						$cls = 'last';
					}else{
						$cls = '';
					}
					$day = ($i<10?'0':'').$i;?>
					<td class="<?=$cls;?>" valign="top" onclick="_regCalendar(this,'<?=$code;?>','<?=$year.$month.$day;?>','0','0','popup');"><?=getDayData($regDt, $color, $i, $data[$i]['full'], $holiday[$regDt])?></td><?
				}

				if($fromDt > $_POST['toDt']){
					if ($i == $toDt){
						$cls = 'last';
					}else{
						$cls = '';
					}

					$data = getCalendar($conn, $code, $year.$month, $fromDt, $_POST['toDt'], '');
					$fromDt = 1;

					$day = ($i<10?'0':'').$i;

					for($i=$fromDt; $i<=$_POST['toDt']; $i++){ ?>
						<td class="<?=$cls;?>" valign="top" onclick="_regCalendar(this,'<?=$code;?>','<?=$year.$month.$day;?>','0','0','popup');"><?=getDayData($regDt, $color, $i, $data[$i]['full'], $holiday[$regDt])?></td><?
					}
				}?>
		</tr><?

		for($j=0; $j<24; $j++){
			$time = $j;

			if ($j == 0){
				$str = '오전 12시';
			}else if ($j == 12){
				$str = '오후 12시';
			}else{
				$str = ($j > 12 ? $j - 12 : $j).'시';
			}
			for($k=0; $k<2; $k++){
				$hm = $j * 60 + $k * 30;?>
				<tr><?
					if ($k == 0){
						$border = '';?>
						<th class="center" rowspan="2"><div style="text-align:right; margin-right:5px;"><?=$str;?></div></th><?
					}else{
						$border = 'border-top:1px dashed #ffffff;';
					}

					$fromDt = $_POST['fromDt'];

					if($fromDt == 1){
					}else {
						if($week == 1){
							//$data = getCalendar($conn, $code, $year.($month<10?0:'').($month-1), $fromDt, $lastDay, $j);
							for($i=$fromDt; $i<=$lastDay; $i++){
								$day = ($i<10?'0':'').$i;?>
								<td day="<?=$i;?>" hm="<?=$hm;?>" valign="top" style="cursor:default; <?=$border;?>"></td><?
							}
							$fromDt = 1;
						}
					}

					if($fromDt > $toDt){
						$toDt = $lastDay2;
					}

					//$data = getCalendar($conn, $code, $year.$month, $fromDt, $toDt, $j);
					for($i=$fromDt; $i<=$toDt; $i++){
						if ($i == $toDt){
							$cls = 'last';
						}else{
							$cls = '';
						}

						$day = ($i<10?'0':'').$i;?>
						<td day="<?=$i;?>" hm="<?=$hm;?>" class="<?=$cls;?>" valign="top" style="cursor:default; <?=$border;?>"></td><?
					}


					if($fromDt > $_POST['toDt']){
						//$data = getCalendar($conn, $code, $year.$month, $fromDt, $_POST['toDt'], $j);
						$fromDt = 1;
						for($i=$fromDt; $i<=$_POST['toDt']; $i++){
							if ($i == $toDt){
								$cls = 'last';
							}else{
								$cls = '';
							}

							$day = ($i<10?'0':'').$i;?>
							<td day="<?=$i;?>" hm="<?=$hm;?>" class="<?=$cls;?>" valign="top" style="cursor:default; <?=$border;?>"></td><?
						}
					}?>
				</tr><?
			}
		}?>
		<tfoot>
			<tr><td class="bottom last"></td></tr>
		</tfoot>
	</table>
<?
	include_once('../inc/_db_close.php');


	function getDayData($date, $color, $day, $data = null, $holiday = null){
		if (is_array($data)){
			$cnt = 0;

			foreach($data as $i => $row){
				$style  = 'border-bottom:1px solid #ffffff;';
				$time   = '';
				$style .= 'font-weight:bold;';

				$html .= '<div class=\'left nowrap\' style=\'width:110px; cursor:pointer;'.$style.'\' title=\''.$row['subject'].'\' onclick=\'_viewCalendar(this,"'.$row['code'].'","'.$row['yymm'].'","'.$row['seq'].'","'.$row['no'].'","popup");\'>'.$time.$row['subject'];

				$html .= '</div>';
			}

		}else{
			$i = 0;
		}

		return $html;
	}


	/*********************************************************

		데이타 조회

	*********************************************************/
	function getCalendar($conn, $code, $yymm, $fromDt, $toDt, $times){

		if($times != ''){
			$fulltime = 'N';
		}else {
			$fulltime = 'Y';
		}

		$sql = 'select cld_seq
				,	   cld_yymm
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
				   and cld_dt >= \''.$yymm.($fromDt<10?0:'').$fromDt.'\'
				   and cld_dt <= \''.$yymm.($toDt<10?0:'').$toDt.'\'
				   and cld_fulltime = \''.$fulltime.'\'
				   and del_flag = \'N\'
				 order by cld_from';
		//echo nl2br($sql);
		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();


		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$tmp_from   = explode(':', $row['cld_from']);
			$fromTime   = ($tmp_from[0] * 60 + $tmp_from[1]) / 30;
			$fromHour   = $tmp_from[0];
			$tmp_to     = explode(':', $row['cld_to']);
			$toTime     = ($tmp_to[0] * 60 + $tmp_to[1]) / 30;
			$toHour		= $tmp_to[0];
			$time = $row['cld_from'] * 30;
			$min  = $time % 60;
			$min  = ($min < 10 ? '0' : '').$min;

			//$time = $hour.':'.$min.' ';

			//echo intval($fromHour).'/'.$times.'//';

			if($times != ''){
				for($j=intval($fromHour); $j<=intval($toHour); $j++){
					if($j == $times){
						$hour = $j;
					}
				}
			}else {
				$hour = 'full';
			}

			$day = intval(substr($row['cld_dt'], 8, 2));

			$id = sizeof($data[$day][$hour]);

			$data[$day][$hour][$id] = array('code'		=>$code
														,'yymm'		=>$row['cld_yymm']
														,'seq'		=>$row['cld_seq']
														,'no'		=>$row['cld_no']
														,'date'		=>$row['cld_dt']
														,'from'		=>$fromTime
														,'to'		=>$toTime
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




