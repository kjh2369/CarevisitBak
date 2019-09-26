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

	//$month = (intval($month) < 10 ? '0' : '').intval($month);

	if (empty($wrt_mode)) $wrt_mode = 'html';

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

		function set_month(month, limit){
			if (month > 0){
				var month = (parseInt(month, 10) < 10 ? '0' : '')+parseInt(month, 10);
			}else{
				var month = f.month.value;
			}

			f.month.value = month;
			f.wrt_mode.value = '';
			f.action = 'result_plan_conf.php?limit='+limit;
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

		function show_detail(id){
			var index = 0;

			while(true){
				var row = document.getElementsByName('my_row_'+index+'[]');
				var display = 'none';

				if (row.length < 1) break;
				if (id == row[0].id || id == 'all') display = '';

				for(var i=0; i<row.length; i++)
					row[i].style.display = display;

				index ++;
			}
		}

		window.onload = function(){
			__init_form(document.f);
		}

		-->
		</script>

		<form name="f" method="post"><?
	}
?>

<div class="title title_border">계획,실적 차이 리스트</div>

<?
	if ($wrt_mode == 'excel'){
	}else{?>
		<table id="my_table" class="my_table" style="width:100%;">
			<colgroup>
				<col width="40px">
				<col width="80px">
				<col width="445px">
				<col width="175px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="head">년도</th>
					<td class="last">
						<select name="year" style="width:auto;"><?
						for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
							<option value="<?=$i;?>" <? if($i == $year){echo 'selected';} ?>><?=$i;?></option><?
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
					</td>
					<td class="right last">
						<span class="btn_pack m"><button type="button" onclick="show_detail('all');">전체</button></span>
						<span class="btn_pack m"><button type="button" onclick="excel();">엑셀</button></span>
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
		<col width="90px">
		<col width="70px">
		<col width="70px">
		<col width="80px">
		<col width="60px">
		<col width="70px">
		<col width="70px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">서비스</th>
			<th class="head" rowspan="2">수급자</th>
			<th class="head" rowspan="2">일자</th>
			<th class="head" colspan="4">계획</th>
			<th class="head" colspan="3">실적</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">시간</th>
			<th class="head">제공</th>
			<th class="head">금액</th>
			<th class="head">상태</th>
			<th class="head">시간</th>
			<th class="head">제공</th>
			<th class="head">금액</th>
		</tr>
	</thead>
	<tbody>
	<?
		ob_start();

		$sql = 'select t01_sugup_date as dt
				,      t01_sugup_fmtime as from_time
				,      t01_sugup_totime as to_time
				,      case t01_status_gbn when \'0\' then \'<span style="color:#ff0000;">미수행</span>\'
										   when \'9\' then \'대기중\'
										   when \'5\' then \'<span style="color:#0000ff;">수행중</span>\'
										   when \'1\' then \'<span style="color:#006600;">완료</span>\' else \'-\' end as stat_gbn
				,      t01_svc_subcode as svc_cd
				,      t01_conf_fmtime as conf_from
				,      t01_conf_totime as conf_to
				,      case t01_svc_subcode when \'200\' then \'방문요양\'
											when \'500\' then \'방문목욕\'
											when \'800\' then \'방문간호\' else \'-\' end as svc_nm
				,      t01_jumin as c_cd
				,      m03_name as c_nm
				,      t01_suga_tot as suga_pay
				,      case t01_status_gbn when \'1\' then t01_conf_suga_value else 0 end as conf_pay
				  from t01iljung
				 inner join m03sugupja
					on m03_ccode = t01_ccode
				   and m03_mkind = t01_mkind
				   and m03_jumin = t01_jumin
				 where t01_ccode           = \''.$code.'\'
				   and t01_sugup_date     >= \''.$year.$month.'01\'
				   and t01_sugup_date     <= \''.$year.$month.$day.'\'
				   and t01_sugup_soyotime != t01_conf_soyotime
				   and t01_del_yn          = \'N\'
				 order by svc_cd, c_nm';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($tmp_cd != $row['c_cd'].'_'.$row['svc_cd']){
				$tmp_cd  = $row['c_cd'].'_'.$row['svc_cd'];

				$dtl_i = 0;
				$sub_i = sizeof($sub);
				$sub[$sub_i] = $row;
			}else{
				$sub[$sub_i]['suga_pay'] += $row['suga_pay'];
				$sub[$sub_i]['conf_pay'] += $row['conf_pay'];
			}

			$dtl[$tmp_cd][$dtl_i] = $row;
			$dtl_i ++;
		}

		$conn->row_free();

		$row_count = sizeof($sub);

		if (is_array($sub)){
			foreach($sub as $index => $client){
				$row_id = 'my_row_'.$index.'[]';

				$dtl_cnt = sizeof($dtl[$client['c_cd'].'_'.$client['svc_cd']]);

				if ($wrt_mode == 'excel')
					$dispaly = '';
				else
					$dispaly = 'none';

				foreach($dtl[$client['c_cd'].'_'.$client['svc_cd']] as $dtl_index => $detail){
					echo '<tr id=\''.$row_id.'\' style=\'display:'.$dispaly.';\'>';
					echo '<td class=\'center\'>'.($dtl_index + 1).'</td>';
					echo '<td class=\'center\'>'.$detail['svc_nm'].'</td>';
					echo '<td class=\'left\'>'.$detail['c_nm'].'</td>';
					echo '<td class=\'center\'>'.$detail['dt'].'</td>';
					echo '<td class=\'center\'>'.$myF->timeStyle($detail['from_time']).'</td>';
					echo '<td class=\'center\'>'.$myF->timeStyle($detail['to_time']).'</td>';
					echo '<td class=\'right\'>'.number_format($detail['suga_pay']).'</td>';
					echo '<td class=\'center\'>'.$detail['stat_gbn'].'</td>';
					echo '<td class=\'center\'>'.$myF->timeStyle($detail['conf_from']).'</td>';
					echo '<td class=\'center\'>'.$myF->timeStyle($detail['conf_to']).'</td>';
					echo '<td class=\'right\'>'.number_format($detail['conf_pay']).'</td>';
					echo '<td class=\'last\'>&nbsp;</td>';
					echo '</tr>';
				}


				$bgcolor  = 'background-color:#efefef;';
				$border_b = 'border-bottom:2px solid #c9c9c9;';

				echo '<tr style=\''.$bgcolor.'\'>';
				echo '<td class=\'center bold\' style=\''.$border_b.'\' rowspan=\'2\'>소계</td>';
				echo '<td class=\'center bold\' style=\''.$border_b.'\' rowspan=\'2\'>'.$client['svc_nm'].'</td>';

				if ($wrt_mode == 'excel')
					echo '<td class=\'left\' style=\''.$border_b.'\' rowspan=\'2\'>'.$client['c_nm'].'</td>';
				else
					echo '<td class=\'left\' style=\''.$border_b.'\' rowspan=\'2\'><a href=\'#\' onclick=\'show_detail("'.$row_id.'");\'>'.$client['c_nm'].'</a></td>';

				echo '<td class=\'center bold\'>차이합계</td>';
				echo '<td class=\'right bold\' colspan=\'2\'></td>';
				echo '<td class=\'right bold\'>'.number_format($client['suga_pay']).'</td>';
				echo '<td class=\'center bold\'>-</td>';
				echo '<td class=\'center bold\' colspan=\'2\'>&nbsp;</td>';
				echo '<td class=\'right bold\'>'.number_format($client['conf_pay']).'</td>';
				echo '<td class=\'right bold last\' style=\''.$border_b.'\' rowspan=\'2\'>'.$dtl_cnt.'건</td>';
				echo '</tr>';


				$sql = 'select sum(t01_suga_tot) as suga_pay
						,      sum(case t01_status_gbn when \'1\' then t01_conf_suga_value else 0 end) as conf_pay
						  from t01iljung
						 inner join m03sugupja
							on m03_ccode = t01_ccode
						   and m03_mkind = t01_mkind
						   and m03_jumin = t01_jumin
						 where t01_ccode           = \''.$code.'\'
						   and t01_sugup_date     >= \''.$year.$month.'01\'
						   and t01_sugup_date     <= \''.$year.$month.$day.'\'
						   and t01_jumin           = \''.$client['c_cd'].'\'
						   and t01_svc_subcode     = \''.$client['svc_cd'].'\'
						   and t01_del_yn          = \'N\'
						 group by t01_svc_subcode, t01_jumin';

				$sum = $conn->get_array($sql);

				echo '<tr style=\''.$bgcolor.'\'>';
				echo '<td class=\'center bold\' style=\''.$border_b.'\'>수가합계</td>';
				echo '<td class=\'right bold\' style=\''.$border_b.'\' colspan=\'2\'></td>';
				echo '<td class=\'right bold\' style=\''.$border_b.'\'>'.number_format($sum['suga_pay']).'</td>';
				echo '<td class=\'center bold\' style=\''.$border_b.'\'>-</td>';
				echo '<td class=\'center bold\' style=\''.$border_b.'\' colspan=\'2\'>&nbsp;</td>';
				echo '<td class=\'right bold\' style=\''.$border_b.'\'>'.number_format($sum['conf_pay']).'</td>';
				echo '</tr>';

				unset($sum);
			}
		}

		unset($sub);

		$html = ob_get_contents();

		ob_end_clean();

		echo $html;
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