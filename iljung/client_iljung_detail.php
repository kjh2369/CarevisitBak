<?php
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$kind  = $_POST['kind'];
	$jumin = $ed->de($_POST['jumin']);
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$day   = $_POST['day'];
	$today = date('d', mktime());

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

	$colgrp = '<col width=\'45px\'>
			   <col width=\'35px\'>
			   <col width=\'140px\'>
			   <col width=\'150px\'>
			   <col width=\'80px\'>
			   <col width=\'80px\'>
			   <col width=\'80px\'>
			   <col>';
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="50px">
		<col width="40px">
		<col>
		<col width="200px">
	</colgroup>
	<tbody>
		<tr>
			<th>년월</th>
			<td class="left"><?=$year;?>.<?=$month;?></td>
			<th>일자</th>
			<td class="last">
				<select name="date" style="width:auto;">
				<?
					for($i=1; $i<=$lastday; $i++){
						echo '<option value=\''.$i.'\' '.($limit_day ? ($i > $today ? 'disabled=\'true\'' : '') : '').' '.($i == $day ? 'selected' : '').'>'.$i.'</option>';
					}
				?>
				</select>
				일 까지
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="set_detail('<?=$ed->en($jumin);?>',document.getElementById('date').value);">조회</button></span>
			</td>
			<td class="right last">
				[<a href="#" onclick="close_detail();">닫기</a>]
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup><?=$colgrp;?></colgroup>
	<thead>
		<tr>
			<th class="head">일자</th>
			<th class="head">시간</th>
			<th class="head">일정</th>
			<th class="head">서비스</th>
			<th class="head">계획</th>
			<th class="head">실적</th>
			<th class="head">차이</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top last" colspan="12">
				<div id="scroll_body" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgrp;?></colgroup>
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

							$sl1 = "select cast(date_format(t01_sugup_date, '%d') as unsigned) as dt
									,      t01_mkind as kind
									,      t01_sugup_fmtime as from_time
									,      t01_sugup_totime as to_time
									,      t01_sugup_soyotime as proctime
									,      t01_svc_subcode as svc_cd
									,      m03_name as nm
									,      t01_jumin as ssn
									,      LVL.m81_name as lvl_name
									,      case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_sugup_soyotime end as plan_time
									,      case when t01_status_gbn = '1' then case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_conf_soyotime end else 0 end as conf_time
									  from t01iljung
									 inner join m03sugupja
										on m03_ccode = t01_ccode
									   and m03_mkind = t01_mkind
									   and m03_jumin = t01_jumin
									  left join m81gubun as LVL
										on LVL.m81_gbn  = 'LVL'
									   and LVL.m81_code = case when m03_mkind = '0' or m03_mkind = '4' then m03_ylvl else '' end
									 where t01_ccode    = '$code'
									   and t01_jumin    = '$jumin'
									   and concat(t01_sugup_date, t01_sugup_fmtime) between '$year$month$from_time' and '$year$month$to_time'
									   and t01_del_yn   = 'N'";

							$sl3 = " order by dt, from_time, to_time, kind";

							/*
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
							*/
							$sql .= $sl1.$sl2;

							$sql = "select distinct * from ( $sql ) as t $sl3";

							$conn->query($sql);
							$conn->fetch();

							$row_count = $conn->row_count();

							for($i=0; $i<$row_count; $i++){
								$row = $conn->select_row($i);

								echo '<tr>';
								echo '<td class=\'center\'>'.$row['dt'].'일</td>';
								echo '<td class=\'center\'>'.intval(substr($row['from_time'],0,2)).'시</td>';
								echo '<td class=\'center\'><div class=\'left\'>'.$myF->timeStyle($row['from_time']).' ~ '.$myF->timeStyle($row['to_time']).' ('.$row['proctime'].'분)'.'</div></td>';
								echo '<td class=\'center\'><div class=\'left\'>'.$conn->kind_name_svc($row['svc_cd']).'</div></td>';

								if ($row['svc_cd'] == '500' || $row['svc_cd'] == '800'){
									echo '<td class=\'center\'><div class=\'right\'>'.$myF->numberFormat($row['plan_time'],'회').'</div></td>';
									echo '<td class=\'center\'><div class=\'right\'>'.$myF->numberFormat($row['conf_time'],'회').'</div></td>';
									echo '<td class=\'center\' style=\''.($row['conf_time']-$row['plan_time'] < 0 ? 'color:#ff0000;' : '').'\'><div class=\'right\'>'.$myF->numberFormat($row['conf_time']-$row['plan_time'],'회').'</div></td>';
								}else{
									echo '<td class=\'center\'><div class=\'right\'>'.$myF->getMinToHM($row['plan_time']).'</div></td>';
									echo '<td class=\'center\'><div class=\'right\'>'.$myF->getMinToHM($row['conf_time']).'</div></td>';
									echo '<td class=\'center\' style=\''.($row['conf_time']-$row['plan_time'] < 0 ? 'color:#ff0000;' : '').'\'><div class=\'right\'>'.$myF->getMinToHM($row['conf_time']-$row['plan_time'],'회').'</div></td>';
								}

								echo '<td class=\'center last\'></td>';
								echo '</tr>';
							}

							$conn->row_free();
						?>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>