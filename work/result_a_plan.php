<?
	$wrt_mode = $_POST['wrt_mode'];

	if ($wrt_mode == 'excel'){
		include_once("../inc/_db_open.php");
	}else{
		include_once("../inc/_header.php");
		include_once("../inc/_body_header.php");
	}

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code  = $_SESSION['userCenterCode'];
	$kind  = '0';
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$day   = $_POST['day'];

	while(true){
		if (!is_numeric(strpos($day, ',-')) &&
			!is_numeric(strpos($day, '-,')) &&
			!is_numeric(strpos($day, '--'))) break;

		if (is_numeric(strpos($day, ',-'))) $day = str_replace(',-', '-', $day);
		if (is_numeric(strpos($day, '-,'))) $day = str_replace('-,', '-', $day);
		if (is_numeric(strpos($day, '--'))) $day = str_replace('--', '-', $day);
	}

	if (empty($year))  $year  = date('Y', mktime());
	if (empty($month)) $month = date('m', mktime());

	$month = (intval($month) < 10 ? '0' : '').intval($month);

	if (empty($wrt_mode)) $wrt_mode = 'html';

	$init_year = $myF->year();

	if ($wrt_mode == 'excel'){
		header( "Content-type: application/vnd.ms-excel" );
		header( "Content-type: charset=utf-8" );
		header( "Content-Disposition: attachment; filename=test.xls" );
		header( "Content-Description: test" );
		header( "Pragma: no-cache" );
		header( "Expires: 0" );
	}

	if ($wrt_mode == 'excel'){
	}else{?>
		<script src="../js/work.js" type="text/javascript"></script>
		<script language='javascript'>
		<!--

		function set_month(month){
			var f = document.f;

			f.month.value = month;
			f.submit();
		}

		function excel(){
			var f = document.f;

			f.wrt_mode.value = 'excel';
			f.submit();
		}

		function set_date(date){
			var str_dt = document.getElementById('str_svc_dt_'+date);
			var obj_dt = document.getElementById('svc_dt_'+date);

			if (obj_dt.value == 'Y'){
				str_dt.className = 'my_box my_box_1';
				obj_dt.value = 'N';
			}else{
				str_dt.className = 'my_box my_box_2';
				obj_dt.value = 'Y';
			}
		}

		function day_search(){
			var f = document.f;

			f.submit();
		}

		window.onload = function(){
			__init_form(document.f);
		}

		-->
		</script>

		<form name="f" method="post"><?
	}
?>

<div class="title title_border">계획, 실적비교</div>

<?
	if ($wrt_mode == 'excel'){
	}else{?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="50px;">
				<col width="80px;">
				<col>
				<col width="80px;">
			</colgroup>
			<tbody>
				<tr>
					<th>년월</th>
					<td class="last">
						<select name="year" style="width:auto;">
						<?
							for($i=$init_year[0]; $i<=$init_year[1]; $i++){
								echo '<option value=\''.$i.'\' '.($i == $year ? 'selected' : '').'>'.$i.'</option>';
							}
						?>
						</select>년
					</td>
					<td class="last" style="padding-top:1px;">
					<?
						for($i=1; $i<=12; $i++){
							$class = 'my_month ';

							if ($i == intval($month)){
								$class .= 'my_month_y ';
								$color  = 'color:#000000;';
							}else{
								$class .= 'my_month_1 ';
								$color  = 'color:#666666;';
							}

							$text = '<a href="#" onclick="set_month('.$i.');">'.$i.'월</a>';

							if ($i == 12){
								$style = 'float:left;';
							}else{
								$style = 'float:left; margin-right:3px;';
							}

							echo '<div class=\''.$class.'\' style=\''.$style.'\'>'.$text.'</div>';
						}
					?>
					</td>
					<td class="right last">
						<span class="btn_pack m icon"><span class="excel"></span><button type="button" onclick="excel();">엑셀</button></span>
					</td>
				</tr>
				<tr>
					<th>일자</th>
					<td class="last" colspan="3">
						<input name="day" type="text" value="<?=$day;?>" class="no_string" style="width:200px; margin-right:0;" onkeydown="__onlyNumber(this, ',-');">
						<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="day_search();">조회</button></span> * 입력 예) 1, 3, 5-7
					</td>
				</tr>
			</tbody>
		</table><?
	}

	if ($wrt_mode == 'excel'){?>
		<table class="my_table" border="1"><?
	}else{?>
		<table class="my_table" style="width:100%;"><?
	}
?>

	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="60px">
		<col width="150px">
		<col width="80px">
		<col width="80px">
		<col width="50px">
		<col width="50px">
		<col width="80px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">서비스</th>
			<th class="head" rowspan="2">수급자</th>
			<th class="head" rowspan="2">요양보호사</th>
			<th class="head" rowspan="2">일자</th>
			<th class="head" colspan="3">계획</th>
			<th class="head" colspan="2">실적</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">시간</th>
			<th class="head">제공</th>
			<th class="head">상태</th>
			<th class="head">시간</th>
			<th class="head">제공</th>
		</tr>
	</thead>
	<tbody>
	<?
		$days = explode(',', $day);
		$arr  = $myF->sortArray($days, 0, 1);
		$arr_cnt = sizeof($arr);
		unset($days);

		for($i=0; $i<$arr_cnt; $i++){
			if ($i > 0){
				if ($days[sizeof($days) - 1] != $arr[$i]){
					$days[sizeof($days)] = $arr[$i];
				}
			}else{
				$days[sizeof($days)] = $arr[$i];
			}
		}
		$day_cnt = sizeof($days);

		$sl1 = "select t01_svc_subcode as svc_cd
				,      case t01_svc_subcode when '200' then '방문요양'
											when '500' then '방문목욕'
											when '800' then '방문간호' else '-' end as svc_nm
				,      t01_status_gbn as stat
				,      t01_jumin as client_ssn
				,      m03_name as client_nm
				,      t01_yoyangsa_id1 as member_ssn1
				,      t01_yoyangsa_id2 as member_ssn2
				,      t01_yname1 as member_nm1
				,      t01_yname2 as member_nm2
				,      t01_sugup_date as dt
				,      t01_sugup_fmtime as plan_from
				,      t01_sugup_totime as plan_to
				,      t01_sugup_soyotime as plan_time
				,      t01_conf_fmtime as conf_from
				,      t01_conf_totime as conf_to
				,      t01_conf_soyotime as conf_time
				  from t01iljung
				 inner join m03sugupja
				    on m03_ccode           = t01_ccode
				   and m03_mkind           = t01_mkind
				   and m03_jumin           = t01_jumin
				 where t01_ccode           = '$code'
				   and t01_mkind           = '$kind'
				   and t01_sugup_date   like '$year$month%'
				   and t01_sugup_soyotime != t01_conf_soyotime
				   and t01_del_yn          = 'N'";

		$sl3 = " order by svc_cd, client_nm, member_nm1, dt, plan_from, plan_to";

		$sql = "";

		for($i=0; $i<$day_cnt; $i++){
			if (!empty($days[$i])){
				if (!empty($sql)) $sql .= " union all ";
				if (!is_numeric(strpos($days[$i], '-'))){
					$tmp_day = intval($days[$i]);
					$sl2 = " and cast(date_format(t01_sugup_date, '%d') as unsigned) = '$tmp_day'";
				}else{
					$tmp_day = explode('-',$days[$i]);
					$tmp_day[0] = intval($tmp_day[0]);
					$tmp_day[1] = intval($tmp_day[1]);

					if ($tmp_day[0] > $tmp_day[1]){
						$tmp_dt1 = $tmp_day[1];
						$tmp_dt2 = $tmp_day[0];
					}else{
						$tmp_dt1 = $tmp_day[0];
						$tmp_dt2 = $tmp_day[1];
					}

					$sl2 = " and cast(date_format(t01_sugup_date, '%d') as unsigned) between '$tmp_dt1' and '$tmp_dt2'";
				}
			}
			$sql .= $sl1.$sl2;
		}

		$sql = "select distinct * from ( $sql ) as t $sl3";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($row['svc_cd'] == '500'){
				$mem_nm = $row['member_nm1'].'[정]';

				if (!empty($row['member_nm2'])){
					$mem_nm .= '/'.$row['member_nm2'].'[부]';
				}
			}else{
				$mem_nm = $row['member_nm1'];
			}

			$plan_time = $myF->timeStyle($row['plan_from']).'~'.$myF->timeStyle($row['plan_to']);
			$conf_time = $myF->timeStyle($row['conf_from']).'~'.$myF->timeStyle($row['conf_to']);

			if ($conf_time == '~') $conf_time = '';

			if ($row['stat'] == '1'){
				if ($row['plan_time'] > $row['conf_time']){
					$msg = '계획시간 <span style=\'color:#ff0000; font-weight:bold;\'>></span> 실적시간';
				}else if ($row['plan_time'] < $row['conf_time']){
					$msg = '계획시간 <span style=\'color:#ff0000; font-weight:bold;\'><</span> 실적시간';
				}
			}else{
				$msg = '<span style=\'color:#ff0000; font-weight:bold;\'>실적없음</span>';
			}

			switch($row['stat']){
				case '1':
					$stat = '<span style=\'color:#006600;\'>완료</span>';
					break;
				case '5':
					$stat = '<span style=\'color:#0000ff;\'>수행중</span>';
					break;
				case 'C':
					$stat = '<span style=\'color:#ff0000;\'>에러</span>';
					break;
				case '9':
					$stat = '대기';
					break;
				default:
					$stat = '대기';
			}

			echo '<tr>';
			echo '<td class=\'center\'>'.($i+1).'</td>';
			echo '<td class=\'center\'>'.$row['svc_nm'].'</td>';
			echo '<td class=\'left\'>'.$row['client_nm'].'</td>';
			echo '<td class=\'left\'>'.$mem_nm .'</td>';
			echo '<td class=\'center\'>'.$myF->dateStyle($row['dt'],'.').'</td>';
			echo '<td class=\'center\'>'.$plan_time.'</td>';
			echo '<td class=\'center\'>'.$row['plan_time'].'분</td>';
			echo '<td class=\'center\'>'.$stat.'</td>';
			echo '<td class=\'center\'>'.$conf_time.'</td>';
			echo '<td class=\'center\'>'.$row['conf_time'].'분</td>';
			echo '<td class=\'left last\'>'.$msg.'</td>';
			echo '</tr>';
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
		<?
			if ($row_count > 0){
				echo '<td class=\'left bottom last\' colspan=\'10\'>'.$myF->message($row_count, 'N').'</td>';
			}else{
				echo '<td class=\'center bottom last\' colspan=\'10\'>'.$myF->message('nodata', 'N').'</td>';
			}
		?>
		</tr>
	</tbody>
</table>

<?
	if ($wrt_mode == 'excel'){
	}else{?>
		<input type="hidden" name="code" value="<?=$code;?>">
		<input type="hidden" name="kind" value="<?=$kind;?>">
		<input type="hidden" name="month" value="<?=$month;?>">
		<input type="hidden" name="wrt_mode" value="<?=$wrt_mode;?>">

		</form><?
	}

	if ($wrt_mode == 'excel'){
		include_once("../inc/_db_close.php");
	}else{
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
	}
?>