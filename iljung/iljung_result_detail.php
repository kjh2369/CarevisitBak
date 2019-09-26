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
	$gubun = $_POST['gubun'];
	$today = date('d', mktime());

	$lastday = $myF->lastDay($year, $month);

	if ($year.$month == date('Ym', mktime())){
		$from_time = '01';
		$limit_day = true;
	}else if ($year.$month > date('Ym', mktime())){
		$from_time = '01';
		$limit_day = true;
	}else{
		$from_time = '01';
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
		$to_time = $day;
	}else if ($year.$month > date('Ym', mktime())){
		$to_time = '31';
	}else{
		$to_time = $day;
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
				<span class="btn_pack m icon"><span class="excel"></span><button type="button" onFocus="this.blur();" onClick="excel('<?=$ed->en($jumin);?>',document.getElementById('date').value);">엑셀</button></span>
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

							if ($gubun == 'client'){
								$sl1 = "select cast(date_format(t01_sugup_date, '%d') as unsigned) as dt
										,      t01_mkind as kind
										,      t01_conf_fmtime as from_time
										,      t01_conf_totime as to_time
										,      t01_conf_soyotime - case when t01_svc_subcode = '200' and t01_sugup_soyotime >= 270 then 30 else 0 end as proctime
										,      t01_svc_subcode as svc_cd
										,      t01_jumin as ssn
										,      t01_yname1 as mem_main
										,      t01_yname2 as mem_sub
										,      case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_sugup_soyotime - case when t01_svc_subcode = '200' and t01_sugup_soyotime >= 270 then 30 else 0 end end as plan_time
										,      case when t01_status_gbn = '1' then case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_conf_soyotime - case when t01_svc_subcode = '200' and t01_conf_soyotime >= 270 then 30 else 0 end end else 0 end as conf_time
										  from t01iljung
										 where t01_ccode    = '$code'
										   and t01_jumin    = '$jumin'
										   and t01_sugup_date between '$year$month$from_time' and '$year$month$to_time'
										   and t01_del_yn   = 'N'";

								$sl3 = " order by dt, from_time, to_time, kind";

								$sql = $sl1.$sl2;

								$sql = "select distinct * from ( $sql ) as t $sl3";
							}else{
								$sql = "select center_code
										,      center_kind
										,      member_code
										,      m02_yname as member_name
										,      m03_name as client_name
										,      svc_cd
										,      cast(date_format(plan_date, '%d') as unsigned) as dt
										,      plan_start_time as from_time
										,      plan_to_time as to_time
										,      proctime
										,      plan_time
										,      conf_time
										  from (
											   select t01_ccode as center_code
											   ,      t01_mkind as center_kind
											   ,      t01_svc_subcode as svc_cd
											   ,      t01_jumin as client_code
											   ,      t01_yoyangsa_id1 as member_code
											   ,      t01_conf_date as plan_date
											   ,      t01_conf_fmtime as plan_start_time
											   ,      t01_conf_totime as plan_to_time
											   ,      t01_conf_soyotime - case when t01_svc_subcode = '200' and t01_sugup_soyotime >= 270 then 30 else 0 end as proctime
											   ,      case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_sugup_soyotime - case when t01_svc_subcode = '200' and t01_sugup_soyotime >= 270 then 30 else 0 end end as plan_time
											   ,      case when t01_status_gbn = '1' then case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_conf_soyotime - case when t01_svc_subcode = '200' and t01_conf_soyotime >= 270 then 30 else 0 end end else 0 end as conf_time
												 from t01iljung
												where t01_ccode        = '$code'
												  and t01_yoyangsa_id1 = '$jumin'
												  and t01_del_yn       = 'N'
												  and concat(t01_sugup_date, t01_sugup_fmtime) between '$year$month$from_time' and '$year$month$to_time'
												group by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime, t01_yoyangsa_id1
												union all
											   select t01_ccode as center_code
											   ,      t01_mkind as center_kind
											   ,      t01_svc_subcode as svc_cd
											   ,      t01_jumin as client_code
											   ,      t01_yoyangsa_id2 as member_code
											   ,      t01_conf_date as plan_date
											   ,      t01_conf_fmtime as plan_start_time
											   ,      t01_conf_totime as plan_to_time
											   ,      t01_conf_soyotime as proctime
											   ,      case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_sugup_soyotime end as plan_time
											   ,      case when t01_status_gbn = '1' then case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_conf_soyotime end else 0 end as conf_time
												 from t01iljung
												where t01_ccode        = '$code'
												  and t01_yoyangsa_id2 = '$jumin'
												  and t01_del_yn       = 'N'
												  and concat(t01_sugup_date, t01_sugup_fmtime) between '$year$month$from_time' and '$year$month$to_time'
												group by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime, t01_yoyangsa_id2
											   ) as t
										 inner join m02yoyangsa
											on m02_ccode  = center_code
										   and m02_mkind  = ".$conn->_member_kind()."
										   and m02_yjumin = member_code
										 inner join m03sugupja
										    on m03_ccode = center_code
										   and m03_mkind = ".$conn->_client_kind()."
										   and m03_jumin = client_code
										 order by plan_date, plan_start_time, plan_to_time";
							}
							
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

								echo '<td class=\'center last\'><div class=\'left\'>'.(!empty($row['client_name']) ? '수급자 : ' : '').$row['client_name'].(!empty($row['mem_main']) ? '담당 : ' : '').$row['mem_main'].(!empty($row['mem_sub']) ? ' / '.$row['mem_sub'] : '').'</div></td>';
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
<input name="jumin" type="hidden" value="">
<input name="day" type="hidden" value="">
<input name="gubun" type="hidden" value="">

<?
	include_once('../inc/_db_close.php');
?>