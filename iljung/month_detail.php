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

	if ($year.$month == date('Ym', mktime())){
		$from_time = date('dHi', mktime());
	}else{
		$from_time = '010000';
	}

	$to_time = '999999';

	while(true){
		if (!is_numeric(strpos($day, ',-')) &&
			!is_numeric(strpos($day, '-,')) &&
			!is_numeric(strpos($day, '--'))) break;

		if (is_numeric(strpos($day, ',-'))) $day = str_replace(',-', '-', $day);
		if (is_numeric(strpos($day, '-,'))) $day = str_replace('-,', '-', $day);
		if (is_numeric(strpos($day, '--'))) $day = str_replace('--', '-', $day);
	}

	$colgrp = '<col width=\'45px\'>
			   <col width=\'35px\'>
			   <col width=\'140px\'>
			   <col width=\'80px\'>
			   <col width=\'60px\' span=\'2\'>
			   <col width=\'80px\'>
			   <col width=\'60px\' span=\'2\'>
			   <col width=\'80px\'>
			   <col width=\'60px\'>
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
				<input name="day" type="text" value="<?=$day;?>" class="no_string" style="width:200px; margin-right:0;" onkeydown="__onlyNumber(this, ',-');">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="set_detail('<?=$ed->en($jumin);?>');">조회</button></span> * 입력 예) 1, 3, 5-7
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
			<th class="head" rowspan="2">일자</th>
			<th class="head" rowspan="2">시간</th>
			<th class="head" rowspan="2">일정</th>
			<th class="head" colspan="3">계획</th>
			<th class="head" colspan="3">실적</th>
			<th class="head last" colspan="3">차이</th>
		</tr>
		<tr>
			<th class="head">요양</th>
			<th class="head">목욕</th>
			<th class="head">간호</th>
			<th class="head">요양</th>
			<th class="head">목욕</th>
			<th class="head">간호</th>
			<th class="head">요양</th>
			<th class="head">목욕</th>
			<th class="head last">간호</th>
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
									,      t01_sugup_fmtime as from_time
									,      t01_sugup_totime as to_time
									,      t01_sugup_soyotime as proctime
									,      m03_name as nm
									,      t01_jumin as ssn
									,      LVL.m81_name as lvl_name
									,      case when t01_svc_subcode = '200' then t01_sugup_soyotime else 0 end as plan200
									,      case when t01_svc_subcode = '500' then 1 else 0 end as plan500
									,      case when t01_svc_subcode = '800' then 1 else 0 end as plan800
									,      case when t01_svc_subcode = '200' and t01_status_gbn = '1' then t01_conf_soyotime else 0 end as conf200
									,      case when t01_svc_subcode = '500' and t01_status_gbn = '1' then 1 else 0 end as conf500
									,      case when t01_svc_subcode = '800' and t01_status_gbn = '1' then 1 else 0 end as conf800
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

							$sl3 = " order by dt, nm";

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

								echo '<tr>';
								echo '<td class=\'center\'>'.$row['dt'].'일</td>';
								echo '<td class=\'center\'>'.intval(substr($row['from_time'],0,2)).'시</td>';
								echo '<td class=\'center\'><div class=\'left\'>'.$myF->timeStyle($row['from_time']).' ~ '.$myF->timeStyle($row['to_time']).' ('.$row['proctime'].'분)'.'</div></td>';
								echo '<td class=\'center\'>'.$myF->getMinToHM($row['plan200']).'</td>';
								echo '<td class=\'center\'>'.$myF->numberFormat($row['plan500'],'회').'</td>';
								echo '<td class=\'center\'>'.$myF->numberFormat($row['plan800'],'회').'</td>';
								echo '<td class=\'center\'>'.$myF->getMinToHM($row['conf200']).'</td>';
								echo '<td class=\'center\'>'.$myF->numberFormat($row['conf500'],'회').'</td>';
								echo '<td class=\'center\'>'.$myF->numberFormat($row['conf800'],'회').'</td>';
								echo '<td class=\'center\' style=\''.($row['conf200']-$row['plan200'] < 0 ? 'color:#ff0000;' : '').'\'>'.$myF->getMinToHM($row['conf200']-$row['plan200']).'</td>';
								echo '<td class=\'center\' style=\''.($row['conf500']-$row['plan500'] < 0 ? 'color:#ff0000;' : '').'\'>'.$myF->numberFormat($row['conf500']-$row['plan500'],'회').'</td>';
								echo '<td class=\'center last\' style=\''.($row['conf800']-$row['conf800'] < 0 ? 'color:#ff0000;' : '').'\'>'.$myF->numberFormat($row['conf800']-$row['conf800'],'회').'</td>';
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