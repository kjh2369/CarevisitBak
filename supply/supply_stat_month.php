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
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function set_month(month){
	var f = document.f;

	f.month.value = month;
	f.submit();
}

function go_client(jumin){
	var f = document.f;

	f.jumin.value = jumin;
	f.action = 'supply_stat_day.php';
	f.submit();
}

window.onload = function(){
}
-->
</script>

<div class="title">수급내역현황(월별)</div>

<form name="f" method="post">
<?
	include_once('supply_cal.php');
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="80px">
		<col width="75px">
		<col width="75px">
		<col width="75px">
		<col width="75px">
		<col width="75px">
		<col width="75px">
		<col width="75px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head" title="수급자명을 클릭하시면 일별 상세내역이 출력됩니다."><u>수급자명</u></th>
			<th class="head">생년월일</th>
			<th class="head">등급(부담율)</th>
			<th class="head">급여한도<br>금액</th>
			<th class="head">공단청구<br>한도금액</th>
			<th class="head">계획급여액</th>
			<th class="head">실적급여액</th>
			<th class="head">차액<br>(실적-계획)</th>
			<th class="head">공단청구<br>금액</th>
			<th class="head last">본인부담<br>금액</th>
		</tr>
	</thead>
	<tbody>
	<?
		if ($lbTestMode){
			if ($level != '')
				$wsl = ' and level = \''.$level.'\'';

			$sql = 'select jumin
					,      name
					,      concat(lvl_name, \'(\', cast(bonin_yul as char), \')\') as lvl
					,      kupyeo_max
					,      kupyeo_1
					,      sum(plan_amt) as plan_amt
					,      sum(result_amt) as result_amt
					,      sum(result_amt) - sum(plan_amt) as diff_amt
					,      case when sum(result_public_amt) > kupyeo_1 then kupyeo_1 else sum(result_public_amt) end as public_amt
					,      case when sum(result_public_amt) > kupyeo_1 then sum(result_public_amt) - kupyeo_1 else 0 end + sum(result_my_amt) as my_amt
					  from (
						   select jumin
						   ,      name
						   ,      t01_svc_subcode as svc_code
						   ,      ylvl
						   ,      case when ylvl = \'9\' then \'일반\' else concat(ylvl, \'등급\') end as lvl_name
						   ,      bonin_yul
						   ,      kupyeo_max
						   ,      kupyeo_1
						   ,      sum(t01_suga_tot) as plan_amt
						   ,      sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end then t01_conf_suga_value else 0 end) as result_amt
						   ,      sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end and ifnull(t01_bipay_umu, \'N\') != \'Y\' then t01_conf_suga_value else 0 end) - (sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) - (sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) % 10)) as result_public_amt
						   ,      sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) - (sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) % 10) + sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end and t01_bipay_umu = \'Y\' then t01_conf_suga_value else 0 end) as result_my_amt
							 from (
								  select m03_ccode as code
								  ,      min(m03_mkind)
								  ,      m03_jumin as jumin
								  ,      m03_name as name
								  ,      ifnull(lvl.lvl, \'9\') as ylvl
								  ,      ifnull(amt.amt, 0) as kupyeo_max
								  ,      case when ifnull(clm.amt, 0) > 0 then clm.amt else ifnull(amt.amt, 0) end as kupyeo_1
								  ,      ifnull(kind.kind, \'1\') as kind
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
								   inner join (
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
							   on t01_ccode         = \''.$code.'\'
							  and t01_mkind         = \'0\'
							  and t01_del_yn        = \'N\'
							  and t01_sugup_date like \''.$year.$month.'%\'
							  and t01_sugup_date between sdate and edate
							  and t01_jumin         = t.jumin
							group by jumin, name, t01_svc_subcode, ylvl, kupyeo_1
						   ) as t
					 group by jumin, name, ylvl, kupyeo_max, kupyeo_1
					 order by name';

		}else{
			if ($level != ''){
				$wsl = "where ylvl = '$level'";
			}

			$sql = "select jumin
					,      name
					,      concat(lvl_name, '(', bonin_yul, ')') as lvl
					,      kupyeo_max
					,      kupyeo_1
					,      sum(plan_amt) as plan_amt
					,      sum(result_amt) as result_amt
					,      sum(result_amt) - sum(plan_amt) as diff_amt
					,      case when sum(result_public_amt) > kupyeo_1 then kupyeo_1 else sum(result_public_amt) end as public_amt
					,      case when sum(result_public_amt) > kupyeo_1 then sum(result_public_amt) - kupyeo_1 else 0 end + sum(result_my_amt) as my_amt
					  from (
						   select jumin
						   ,      name
						   ,      t01_svc_subcode as svc_code
						   ,      ylvl
						   ,      lvl.m81_name as lvl_name
						   ,      bonin_yul
						   ,      kupyeo_max
						   ,      kupyeo_1
						   ,      sum(t01_suga_tot) as plan_amt
						   ,      sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value else 0 end) as result_amt
						   ,      sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) - (sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) - (sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) % 10)) as result_public_amt
						   ,      sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) - (sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) % 10) + sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end and t01_bipay_umu = 'Y' then t01_conf_suga_value else 0 end) as result_my_amt
							 from (
								  select m03_jumin as jumin
								  ,      m03_name as name
								  ,      m03_ylvl as ylvl
								  ,      m03_bonin_yul as bonin_yul
								  ,      k.pay as kupyeo_max
								  ,      case when m03_kupyeo_1 > 0 then m03_kupyeo_1 else k.pay end as kupyeo_1
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
										 ) as t
								   inner join (
										 select m91_code as cd
										 ,      m91_kupyeo as pay
										   from m91maxkupyeo
										  where left(m91_sdate, 6) <= '$year$month'
											and left(m91_edate, 6) >= '$year$month'
										 ) as k
									  on k.cd = t.m03_ylvl
								   where '$year$month' between left(m03_sdate, 6) and left(m03_edate, 6)
								  ) as t
							inner join t01iljung
							   on t01_ccode         = '$code'
							  and t01_mkind         = '0'
							  and t01_del_yn        = 'N'
							  and t01_sugup_date like '$year$month%'
							  and t01_sugup_date between sdate and edate
							  and t01_jumin         = t.jumin
							inner join m81gubun as lvl
							   on m81_gbn  = 'LVL'
							  and m81_code = ylvl $wsl
							group by jumin, name, t01_svc_subcode, ylvl, kupyeo_1
						   ) as t
					 group by jumin, name, concat(lvl_name, '(', bonin_yul, ')'), kupyeo_max, kupyeo_1
					 order by name";
		}
		
		#if($debug) echo nl2br($sql); 

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){?>
			<tr>
				<td class="sum right" colspan="4">계</td>
				<td class="sum right" id="tot_max">0</td>
				<td class="sum right" id="tot_public">0</td>
				<td class="sum right" id="tot_plan">0</td>
				<td class="sum right" id="tot_result">0</td>
				<td class="sum right" id="tot_diff">0</td>
				<td class="sum right" id="tot_public_amt">0</td>
				<td class="sum right last" id="tot_my_amt">0</td>
			</tr><?
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				?>

				<tr>
					<td class="center"><?=$i+1;?></td>
					<td class="left"><a href="#" onclick="go_client('<?=$ed->en($row['jumin']);?>');"><?=$row['name'];?></a></td>
					<td class="center"><?=$myF->issToBirthday($row['jumin'], '.');?></td>
					<td class="left"><?=$row['lvl'];?></td>
					<td class="right"><?=number_format($row['kupyeo_max']);?></td>
					<td class="right"><?=number_format($row['kupyeo_1']);?></td>
					<td class="right"><?=number_format($row['plan_amt']);?></td>
					<td class="right"><?=number_format($row['result_amt']);?></td>
					<td class="right"><?=number_format($row['diff_amt']);?></td>
					<td class="right"><?=number_format($row['public_amt']);?></td>
					<td class="right last"><?=number_format($row['my_amt']);?></td>
				</tr><?

				$tot_max		+= $row['kupyeo_max'];
				$tot_public		+= $row['kupyeo_1'];
				$tot_plan		+= $row['plan_amt'];
				$tot_result		+= $row['result_amt'];
				$tot_diff		+= $row['diff_amt'];
				$tot_public_amt += $row['public_amt'];
				$tot_my_amt		+= $row['my_amt'];
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
		document.getElementById('tot_max').innerHTML		= '<?=number_format($tot_max);?>';
		document.getElementById('tot_public').innerHTML		= '<?=number_format($tot_public);?>';
		document.getElementById('tot_plan').innerHTML		= '<?=number_format($tot_plan);?>';
		document.getElementById('tot_result').innerHTML		= '<?=number_format($tot_result);?>';
		document.getElementById('tot_diff').innerHTML		= '<?=number_format($tot_diff);?>';
		document.getElementById('tot_public_amt').innerHTML = '<?=number_format($tot_public_amt);?>';
		document.getElementById('tot_my_amt').innerHTML		= '<?=number_format($tot_my_amt);?>';
	}catch(e){
	}
</script>

<input type="hidden" name="month" value="<?=$month;?>">
<input type="hidden" name="jumin" value="">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>