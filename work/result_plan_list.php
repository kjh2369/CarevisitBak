<?
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
	$limit = $_GET['limit'];
	$today = date('d', mktime());

	if ($limit == 1) $day = null;

	if (empty($year))  $year  = date('Y', mktime());
	if (empty($month)) $month = date('m', mktime());

	$lastday = $myF->lastDay($year, $month);

	if ($year.$month == date('Ym', mktime())){
		$from_time = '010000';
		$limit_day = true;
	}else if ($year.$month > date('Ym', mktime())){
		$from_time = '99999999999999';
		$limit_day = true;
	}else{
		$from_time = '010000';
		$limit_day = false;
	}

	if (empty($day)){
		if ($limit_day)
			$day = date('d', mktime());
		else
			$day = $lastday;
	}else{
	}

	$day = (intval($day) < 10 ? '0' : '').intval($day);

	if ($year.$month == date('Ym', mktime())){
		$to_time = $day.date('Hi', mktime());
	}else if ($year.$month > date('Ym', mktime())){
		$to_time = '99999999999999';
	}else{
		$to_time = $day.'9999';
	}

	if ($_SESSION['userStmar'] == 'Y'){
		$member = $_SESSION['userSSN'];
	}else{
		$member = 'all';
	}

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
		/*
		function set_month(month){
			var f = document.f;

			f.month.value = month;
			f.submit();
		}
		*/

		function set_month(month, limit){
			if (month > 0){
				var month = (parseInt(month, 10) < 10 ? '0' : '')+parseInt(month, 10);
			}else{
				var month = f.month.value;
			}

			f.month.value = month;
			f.wrt_mode.value = '';
			f.action = 'result_plan_list.php?limit='+limit;
			f.submit();
		}

		function excel(){
			var f = document.f;

			f.wrt_mode.value = 'excel';
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

<div class="title title_border">계획>실적 List</div>
<?
	if ($wrt_mode == 'excel'){
	}else{?>
		<table id="my_table" class="my_table" style="width:100%;">
			<colgroup>
				<col width="40px">
				<col width="95px">
				<col width="445px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th>년도</th>
					<td class="last">
						<select name="year" style="width:auto;"><?
						for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
							<option value="<?=$i;?>" <? if($i == $year){echo 'selected';} ?>><?=$i;?>년</option><?
						}?>
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

							$link = '<a href="#" style="'.$color.'" onclick="set_month('.$i.',1);">'.$i.'월</a>';

							if ($i == 12){
								$style = 'float:left;';
							}else{
								$style = 'float:left; margin-right:2px;';
							}?>
							<div class="<?=$class;?>" style="<?=$style;?>"><?=$link;?></div><?
						}
					?>
					</td>
					<td class="left last">
						대비기준일자
						<select name="day" style="width:auto;" onchange="set_month(0,2);">
						<?
							for($i=1; $i<=31; $i++){
								echo '<option value=\''.$i.'\' '.($limit_day ? ($i > $today ? 'disabled=\'true\'' : '') : '').' '.($i == $day ? 'selected' : '').'>'.$i.'</option>';
							}
						?>
						</select>
						일 까지
						<span class="btn_pack m icon" style="margin-left:30px"><span class="excel"></span><button type="button" onclick="excel();">엑셀</button></span>
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
		<col width="40px;">
		<col width="80px;">
		<col width="80px;">
		<col width="60px;">
		<col width="70px;">
		<col width="160px;">
		<col width="80px;">
		<col width="40px;">
		<col width="40px;">
		<col width="80px;">
		<col width="40px;">
		<col>
	</colgroup>
	<tr>
		<th class="head" rowspan="2">순번</th>
		<th class="head" rowspan="2">일자</th>
		<th class="head" rowspan="2">실적시간</th>
		<th class="head" rowspan="2">서비스명</th>
		<th class="head" rowspan="2">수급자</th>
		<th class="head" rowspan="2">요양보호사</th>
		<th class="head" colspan="3">계획시간</th>
		<th class="head" colspan="2">실적시간</th>
		<th class="head" rowspan="2">차이</th>
	</tr>
	<tr>
		<th class="head">시간</th>
		<th class="head">제공</th>
		<th class="head">상태</th>
		<th class="head">시간</th>
		<th class="head">제공</th>
	</tr>
	<tbody>
	<?

		$day_cnt = sizeof($day);

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
				   and concat(t01_sugup_date, t01_sugup_fmtime) between '".$year.$month.$from_time."' and '".$year.$month.$to_time."'
				   and t01_sugup_soyotime > ifnull(t01_conf_soyotime, 0)
				   and t01_del_yn          = 'N'";

		$sl3 = " order by dt, conf_from, conf_to";

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
			$minus = $row['plan_time'] - $row['conf_time'];


			echo '<tr>';
			echo '<td class=\'center\'>'.($i+1).'</td>';
			echo '<td class=\'center\'>'.$myF->dateStyle($row['dt'],'.').'</td>';
			echo '<td class=\'left\'>'.$conf_time.'</td>';
			echo '<td class=\'left\'>'.$row['svc_nm'].'</td>';
			echo '<td class=\'left\'>'.$row['client_nm'].'</td>';
			echo '<td class=\'left\'>'.$mem_nm .'</td>';
			echo '<td class=\'center\'>'.$plan_time.'</td>';
			echo '<td class=\'right\'>'.$row['plan_time'].'분</td>';
			echo '<td class=\'center\'>'.$stat.'</td>';
			echo '<td class=\'center\'>'.$conf_time.'</td>';
			echo '<td class=\'right\'>'.($row['conf_time'] != '' ? $row['conf_time'] : '0').'분</td>';
			echo '<td class=\'right\'>'.$minus.'</td>';
			echo '</tr>';

		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
		<?
			if ($row_count > 0){
				echo '<td class=\'left bottom last\' colspan=\'12\'>'.$myF->message($row_count, 'N').'</td>';
			}else{
				echo '<td class=\'center bottom last\' colspan=\'12\'>'.$myF->message('nodata', 'N').'</td>';
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
