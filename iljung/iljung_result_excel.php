<?php
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	header( "Content-type: charset=utf-8" );
	header("Content-type: application/vnd.ms-excel; charset=utf-8");
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Content-Transfer-Encoding: binary" );
	header( "Content-Description: PHP4 Generated Data" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );
?>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
<?
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

?>
<style>
	.head{
		background-color:#efefef;
		border:0.5pt solid #000000;
		font-family:굴림;
	}

	.center{
		text-align:center;
		font-family:굴림;
	}
	.left{
		text-align:left;
		font-family:굴림;
	}
	.right{
		text-align:right;
		background-color:#ffffff;
		font-family:굴림;
	}
</style>

<?
$sql2 = "select m00_cname
				  from m00center
				 where m00_mcode = '$code'";
		$c_name = $conn -> get_data($sql2);

		$r_dt = date('Y.m.d',mktime());

?>
<table>
	<tr>
		<td colspan="5" style="text-align:center; font-size:15pt; font-weight:bold;"><?=$year?>년<?=$month?>월 서비스 내역서<br></td>
	</tr>
	<tr><td></td></tr>
	<tr>
		<td colspan="3" style="text-align:left; font-size:12pt; font-weight:bold;">기관명 : <?=$c_name?></td>
		<td colspan="2" style="text-align:right; font-size:12pt; font-weight:bold;">일자 : <?=$r_dt?></td>
	</tr>
</table>
<table border="1">
	<thead>
		<tr>
			<th class="head">일자</th>
			<th class="head">시작시간~종료시간</th>
			<th class="head">서비스유형</th>
			<th class="head">근무시간</th>
			<th class="head">담당자</th>
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
					,      cast(date_format(conf_date, '%d') as unsigned) as dt
					,      conf_start_time as from_time
					,      conf_to_time as to_time
					,      proctime
					,      conf_time
					  from (
						   select t01_ccode as center_code
						   ,      t01_mkind as center_kind
						   ,      t01_svc_subcode as svc_cd
						   ,      t01_jumin as client_code
						   ,      t01_yoyangsa_id1 as member_code
						   ,      t01_conf_date as conf_date
						   ,      t01_conf_fmtime as conf_start_time
						   ,      t01_conf_totime as conf_to_time
						   ,      t01_conf_soyotime as proctime
						   ,      case when t01_status_gbn = '1' then case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_conf_soyotime end else 0 end as conf_time
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
						   ,      t01_yoyangsa_id1 as member_code
						   ,      t01_conf_date as conf_date
						   ,      t01_conf_fmtime as conf_start_time
						   ,      t01_conf_totime as conf_to_time
						   ,      t01_conf_soyotime as proctime
						   ,      case when t01_status_gbn = '1' then case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_conf_soyotime end else 0 end as conf_time
							 from t01iljung
							where t01_ccode        = '$code'
							  and t01_yoyangsa_id1 = '$jumin'
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
					 order by conf_date, conf_start_time, conf_to_time";
		}


		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			echo '<tr>';
			echo '<td class=\'center\'>'.$row['dt'].'일</td>';
			echo '<td class=\'center\'>'.$myF->timeStyle($row['from_time']).' ~ '.$myF->timeStyle($row['to_time']).'</td>';
			echo '<td class=\'left\'>'.$conn->kind_name_svc($row['svc_cd']).'</td>';
			echo '<td class=\'left\'>'.$row['proctime'].(!empty($row['proctime']) ? '분' : '').'</td>';
			echo '<td class=\'left\'>'.$row['client_name'].$row['mem_main'].(!empty($row['mem_sub']) ? ' / '.$row['mem_sub'] : '').'</td>';
			echo '</tr>';
		}

		$conn->row_free();
	?>
	</tbody>
</table>

<?
	include_once('../inc/_db_close.php');
?>