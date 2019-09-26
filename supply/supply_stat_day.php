<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_body_header.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$init_year	= $myF->year();
	$code		= $_SESSION['userCenterCode'];
	$year		= $_POST['year']  != '' ? $_POST['year']  : intval(date('Y', mktime()));
	$month		= $_POST['month'] != '' ? $_POST['month'] : intval(date('m', mktime()));
	$month		= (intval($month) < 10 ? '0' : '').intval($month);
	$level		= $_POST['level'];
	$jumin		= $ed->de($_POST['jumin']);


	
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function set_month(month){
	var f = document.f;

	f.month.value = month;
	f.jumin.value = f.client.value;
	f.submit();
}

window.onload = function(){
}
-->
</script>

<div class="title">수급내역현황(일별)</div>

<form name="f" method="post">
<?
	include_once('supply_cal.php');
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="60px">
		<col>
		<col width="80px">
		<col width="80px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
		<col width="65px">
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">일자</th>
			<th class="head" rowspan="2">계획급여액</th>
			<th class="head" rowspan="2">실적급여액</th>
			<th class="head" rowspan="2">차액<br>(실적-계획)</th>
			<th class="head" rowspan="2">본인<br>부담액</th>
			<th class="head" colspan="2">방문요양</th>
			<th class="head" colspan="2">방문목욕</th>
			<th class="head last" colspan="2">방문간호</th>
		</tr>
		<tr>
			<th class="head">계획</th>
			<th class="head">실적</th>
			<th class="head">계획</th>
			<th class="head">실적</th>
			<th class="head">계획</th>
			<th class="head last">실적</th>
		</tr>
	</thead>
	<tbody>
	<?
		if ($lbTestMode){
			$sql = 'select jumin
					,	   cast(date_format(t01_sugup_date, \'%m\') as unsigned) as mm
					,      cast(date_format(t01_sugup_date, \'%d\') as unsigned) as dd
					,      weekday(date_format(t01_sugup_date, \'%Y-%m-%d\')) as weekday
					,      sum(t01_suga_tot) as plan_amt
					,      sum(case when t01_status_gbn = \'1\' then t01_conf_suga_value else 0 end) as request_amt
					,      sum(case when t01_status_gbn = \'1\' then t01_conf_suga_value else 0 end - t01_suga_tot) as diff_amt
					,      sum(case when t01_status_gbn = \'1\' then t01_conf_suga_value else 0 end * bonin_yul / 100) as my_amt
					,      sum(case when t01_svc_subcode = \'200\' then t01_suga_tot else 0 end) as plan_care_amt
					,      sum(case when t01_svc_subcode = \'200\' and t01_status_gbn = \'1\' then t01_conf_suga_value else 0 end) as request_care_amt
					,      sum(case when t01_svc_subcode = \'500\' then t01_suga_tot else 0 end) as plan_bath_amt
					,      sum(case when t01_svc_subcode = \'500\' and t01_status_gbn = \'1\' then t01_conf_suga_value else 0 end) as request_bath_amt
					,      sum(case when t01_svc_subcode = \'800\' then t01_suga_tot else 0 end) as plan_nursing_amt
					,      sum(case when t01_svc_subcode = \'800\' and t01_status_gbn = \'1\' then t01_conf_suga_value else 0 end) as request_nursing_amt
					  from (
						   select m03_ccode as code
						   ,      min(m03_mkind)
						   ,      m03_jumin as jumin
						   ,      m03_name as name
						   ,      ifnull(lvl.lvl, \'9\') as ylvl
						   ,      ifnull(amt.amt, 0) as kupyeo_max
						   ,      case when ifnull(clm.amt, 0) > 0 then clm.amt else ifnull(amt.amt, 0) end as kupyeo_1
						   ,      ifnull(kind.rate, case when lvl.lvl is null then 100 else 15 end) as bonin_yul
						   ,      svc.from_dt as sdate
						   ,      svc.to_dt as edate
							 from m03sugupja as mst
						    inner join (
								  select jumin
								  ,      date_format(min(from_dt), \'%Y%m%d\') as from_dt
								  ,      date_format(max(to_dt),   \'%Y%m%d\') as to_dt
								   from client_his_svc
								  where org_no = \''.$code.'\'
								    and svc_cd = \'0\'
								    and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
								    and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
								  group by jumin
								  ) as svc
							   on svc.jumin = mst.m03_jumin
							 left join (
								  select jumin
								  ,      min(level) as lvl
								    from client_his_lvl
								   where org_no = \''.$code.'\'
									 and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
									 and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\' '.$wsl.'
								  group by jumin
								  ) as lvl
							   on lvl.jumin = mst.m03_jumin
							 left join (
								  select m91_code as cd
								  ,      m91_kupyeo as amt
								    from m91maxkupyeo
								   where left(m91_sdate, 6) <= \''.$year.$month.'\'
									 and left(m91_edate, 6) >= \''.$year.$month.'\'
								  ) as amt
							   on amt.cd = lvl.lvl
							 left join (
								  select jumin
								  ,      seq
								  ,      kind
								  ,      rate
								    from client_his_kind
								   where org_no = \''.$code.'\'
									 and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
									 and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
								   order by jumin, seq desc
								  ) as kind
							   on kind.jumin = mst.m03_jumin
							 left join (
								  select jumin
								  ,      amt
								    from client_his_limit
								   where org_no = \''.$code.'\'
									 and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
									 and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
								   order by jumin, seq desc
								  ) as clm
							   on clm.jumin = mst.m03_jumin
						    where m03_ccode = \''.$code.'\'
						    group by m03_jumin
						   ) as t
					 inner join t01iljung
						on t01_ccode = \''.$code.'\'
					   and t01_mkind = \'0\'
					   and t01_sugup_date like \''.$year.$month.'%\'
					   and t01_sugup_date between sdate and edate
					   and t01_del_yn = \'N\'
					   and t01_jumin = jumin
					   and t01_jumin = \''.$jumin.'\'
					 group by t01_sugup_date
					 order by t01_sugup_date';
		}else{
			$sql = "select cast(date_format(t01_sugup_date, '%m') as unsigned) as mm
					,      cast(date_format(t01_sugup_date, '%d') as unsigned) as dd
					,      weekday(date_format(t01_sugup_date, '%Y-%m-%d')) as weekday
					,      sum(t01_suga_tot) as plan_amt
					,      sum(case when t01_status_gbn = '1' then t01_conf_suga_value else 0 end) as request_amt
					,      sum(case when t01_status_gbn = '1' then t01_conf_suga_value else 0 end - t01_suga_tot) as diff_amt
					,      sum(case when t01_status_gbn = '1' then t01_conf_suga_value else 0 end * bonin_yul / 100) as my_amt
					,      sum(case when t01_svc_subcode = '200' then t01_suga_tot else 0 end) as plan_care_amt
					,      sum(case when t01_svc_subcode = '200' and t01_status_gbn = '1' then t01_conf_suga_value else 0 end) as request_care_amt
					,      sum(case when t01_svc_subcode = '500' then t01_suga_tot else 0 end) as plan_bath_amt
					,      sum(case when t01_svc_subcode = '500' and t01_status_gbn = '1' then t01_conf_suga_value else 0 end) as request_bath_amt
					,      sum(case when t01_svc_subcode = '800' then t01_suga_tot else 0 end) as plan_nursing_amt
					,      sum(case when t01_svc_subcode = '800' and t01_status_gbn = '1' then t01_conf_suga_value else 0 end) as request_nursing_amt
					  from (
						   select m03_jumin as jumin
						   ,      m03_name as name
						   ,      m03_ylvl as ylvl
						   ,      m03_skind as skind
						   ,      m03_bonin_yul as bonin_yul
						   ,      m03_kupyeo_max as kupyeo_max
						   ,      m03_kupyeo_1 as kupyeo_1
						   ,      m03_kupyeo_2 as kupyeo_2
						   ,      m03_sdate as sdate
						   ,      m03_edate as edate
							 from (
								  select m03_jumin
								  ,      m03_name
								  ,      m03_ylvl
								  ,      m03_skind
								  ,      m03_bonin_yul
								  ,      m03_kupyeo_max
								  ,      m03_kupyeo_1
								  ,      m03_kupyeo_2
								  ,      m03_sdate
								  ,      m03_edate
									from m03sugupja
								   where m03_ccode = '$code'
									 and m03_mkind = '0'
									 and m03_jumin = '$jumin'
								   union all
								  select m31_jumin
								  ,      m03_name
								  ,      m31_level
								  ,      m31_kind
								  ,      m31_bonin_yul
								  ,      m31_kupyeo_max
								  ,      m31_kupyeo_1
								  ,      m31_kupyeo_2
								  ,      m31_sdate
								  ,      m31_edate
									from m31sugupja
								   inner join m03sugupja
									  on m03_ccode = m31_ccode
									 and m03_mkind = m31_mkind
									 and m03_jumin = m31_jumin
								   where m31_ccode = '$code'
									 and m31_mkind = '0'
									 and m31_jumin = '$jumin'
								  ) as t
							where '$year$month' between left(m03_sdate, 6) and left(m03_edate, 6)
						   ) as t
					 inner join t01iljung
						on t01_ccode = '$code'
					   and t01_mkind = '0'
					   and t01_sugup_date like '$year$month%'
					   and t01_sugup_date between sdate and edate
					   and t01_del_yn = 'N'
					   and t01_jumin = '$jumin'
					 group by t01_sugup_date
					 order by t01_sugup_date";
		}

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){?>
			<tr>
				<td class="sum right" colspan="2">계</td>
				<td class="sum right" id="tot_plan_amt">0</td>
				<td class="sum right" id="tot_request_amt">0</td>
				<td class="sum right" id="tot_diff_amt">0</td>
				<td class="sum right" id="tot_my_amt">0</td>
				<td class="sum right" id="tot_plan_care_amt">0</td>
				<td class="sum right" id="tot_request_care_amt">0</td>
				<td class="sum right" id="tot_plan_bath_amt">0</td>
				<td class="sum right" id="tot_request_bath_amt">0</td>
				<td class="sum right" id="tot_plan_nursing_amt">0</td>
				<td class="sum right last" id="tot_request_nursing_amt">0</td>
			</tr><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				switch($row['weekday']){
				case 0:
					$weekday = '월';
					break;
				case 1:
					$weekday = '화';
					break;
				case 2:
					$weekday = '수';
					break;
				case 3:
					$weekday = '목';
					break;
				case 4:
					$weekday = '금';
					break;
				case 5:
					$weekday = '<font color="0000ff">토</font>';
					break;
				case 6:
					$weekday = '<font color="ff0000">일</font>';
					break;
				}
				

				$sql ='select rate
						 from client_his_kind
						where org_no = \''.$code.'\'
						  and jumin  = \''.$row['jumin'].'\'
						  and date_format(from_dt, \'%Y%m%d\') <= \''.$year.$row['mm'].($row['dd'] < 10 ? '0'.$row['dd']:$row['dd']).'\'
						  and date_format(to_dt,   \'%Y%m%d\') >= \''.$year.$row['mm'].($row['dd'] < 10 ? '0'.$row['dd']:$row['dd']).'\'';
				$kind = $conn -> get_data($sql);
				
				$myAmt = round($row['request_amt'] * $kind['rate'] / 100);
				

				?>
				<tr>
					<td class="center"><?=$i+1;?></td>
					<td class="center"><?=$row['mm'];?>/<?=$row['dd'];?>(<?=$weekday;?>)</td>
					<td class="right"><?=number_format($row['plan_amt']);?></td>
					<td class="right"><?=number_format($row['request_amt']);?></td>
					<td class="right"><?=number_format($row['diff_amt']);?></td>
					<td class="right"><?=number_format($row['my_amt']);?></td>
					<td class="right"><?=number_format($row['plan_care_amt']);?></td>
					<td class="right"><?=number_format($row['request_care_amt']);?></td>
					<td class="right"><?=number_format($row['plan_bath_amt']);?></td>
					<td class="right"><?=number_format($row['request_bath_amt']);?></td>
					<td class="right"><?=number_format($row['plan_nursing_amt']);?></td>
					<td class="right last"><?=number_format($row['request_nursing_amt']);?></td>
				</tr><?

				$tot_plan_amt				+= $row['plan_amt'];
				$tot_request_amt			+= $row['request_amt'];
				$tot_diff_amt				+= $row['diff_amt'];
				$tot_my_amt					+= $row['my_amt'];
				$tot_plan_care_amt			+= $row['plan_care_amt'];
				$tot_request_care_amt		+= $row['request_care_amt'];
				$tot_plan_bath_amt			+= $row['plan_bath_amt'];
				$tot_request_bath_amt		+= $row['request_bath_amt'];
				$tot_plan_nursing_amt		+= $row['plan_nursing_amt'];
				$tot_request_nursing_amt	+= $row['request_nursing_amt'];
			}
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="center last bottom" colspan="11">
			<?
				if ($row_count > 0){
					echo '&nbsp;';
				}else{
					echo $myF->message('nodata', 'N');
				}
			?>
			</td>
		</tr>
	</tbody>
</table>

<script language='javascript'>
	try{
		document.getElementById('tot_plan_amt').innerHTML				= '<?=number_format($tot_plan_amt);?>';
		document.getElementById('tot_request_amt').innerHTML			= '<?=number_format($tot_request_amt);?>';
		document.getElementById('tot_diff_amt').innerHTML				= '<?=number_format($tot_diff_amt);?>';
		document.getElementById('tot_my_amt').innerHTML					= '<?=number_format($myF->cutOff($tot_my_amt));?>';
		document.getElementById('tot_plan_care_amt').innerHTML			= '<?=number_format($tot_plan_care_amt);?>';
		document.getElementById('tot_request_care_amt').innerHTML		= '<?=number_format($tot_request_care_amt);?>';
		document.getElementById('tot_plan_bath_amt').innerHTML			= '<?=number_format($tot_plan_bath_amt);?>';
		document.getElementById('tot_request_bath_amt').innerHTML		= '<?=number_format($tot_request_bath_amt);?>';
		document.getElementById('tot_plan_nursing_amt').innerHTML		= '<?=number_format($tot_plan_nursing_amt);?>';
		document.getElementById('tot_request_nursing_amt').innerHTML	= '<?=number_format($tot_request_nursing_amt);?>';
	}catch(e){
	}
</script>

<input type="hidden" name="month" value="<?=$month;?>">
<input type="hidden" name="level" value="<?=$level;?>">
<input type="hidden" name="jumin" value="<?=$ed->en($jumin);?>">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>