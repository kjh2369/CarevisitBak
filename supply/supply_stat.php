<?
	if ($_POST['orgNo']){
		include_once("../inc/_db_open.php");
	}else{
		include_once("../inc/_header.php");
		include_once("../inc/_body_header.php");
	}
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$init_year	= $myF->year();

	if ($_SESSION['userLevel'] == 'A'){
		$code = $_POST['orgNo'];
	}else{
		$code = $_SESSION['userCenterCode'];
	}
	$year	= $_POST['year']  != '' ? $_POST['year']  : intval(date('Y', mktime()));
	$month	= $_POST['month'] != '' ? $_POST['month'] : intval(date('m', mktime()));
	$month	= (intval($month) < 10 ? '0' : '').intval($month);
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function set_month(month){
	var f = document.f;

	f.month.value = month;
	f.submit();
}

function go_level(level){
	var f = document.f;

	f.level.value = level;
	f.action = 'supply_stat_month.php';
	f.submit();
}

window.onload = function(){
}
-->
</script>

<form name="f" method="post"><?
if ($_SESSION['userLevel'] == 'C'){?>
	<div class="title">수급내역현황(전체)</div><?
	include_once('supply_cal.php');
}?>
<div class="title title_border">장기요양 등급별</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="60px">
		<col width="76px" span="8">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2" title="구분을 클릭하시면 등급별 상세내역이 출력됩니다."><u>구분</u></th>
			<th class="head" rowspan="2">인원수</th>
			<th class="head" rowspan="2">공단한도금액</th>
			<th class="head" colspan="3">계획</th>
			<th class="head" colspan="3">실적</th>
			<th class="head last" colspan="2">대비수급율</th>
		</tr>
		<tr>
			<th class="head">공단청구</th>
			<th class="head">본인부담</th>
			<th class="head">합계</th>
			<th class="head">공단청구</th>
			<th class="head">본인부담</th>
			<th class="head">합계</th>
			<th class="head">공단청구</th>
			<th class="head last">본인부담</th>
		</tr>
	</thead>
	<tbody>
	<?
		// 장기요양 등급별
		$data[0] = init_data('1', '1등급');
		$data[1] = init_data('2', '2등급');
		$data[2] = init_data('3', '3등급');
		$data[3] = init_data('4', '4등급');
		$data[4] = init_data('5', '5등급');
		$data[5] = init_data('9', '일반');

		$data_count = sizeof($data);



		//서비스별
		$svc[0] = init_data('200', '방문요양');
		$svc[1] = init_data('500', '방문목욕');
		$svc[2] = init_data('800', '방문간호');
		$svc_cnt = sizeof($svc);




		//수급자구분별
		$kind[0] = init_data('3', '기초');
		$kind[1] = init_data('2', '의료');
		$kind[2] = init_data('4', '경감');
		$kind[3] = init_data('1', '일반');
		$kind_cnt = sizeof($kind);

		if ($lbTestMode){
			$sql = 'select jumin
					,      name
					,      svc_code
					,      ylvl
					,      kind
					,      public_limit_amt
					,      sum(plan_public_amt - plan_my_amt) as plan_public_amt
					,      sum(plan_my_amt) as plan_my_amt
					,      sum(result_public_amt) as result_amt
					,      case when sum(result_public_amt) > public_limit_amt then public_limit_amt else sum(result_public_amt) end as result_public_amt
					,      case when sum(result_public_amt) > public_limit_amt then sum(result_public_amt) - public_limit_amt else 0 end + sum(result_my_amt) as result_my_amt
					  from (
						   select jumin
						   ,      name
						   ,      t01_svc_subcode as svc_code
						   ,      ylvl
						   ,      kind
						   ,      kupyeo_1 as public_limit_amt
						   ,      sum(t01_suga_tot) as plan_public_amt

						   ,      sum(case when IFNULL(t01_bipay_umu,\'N\') != \'Y\' then t01_suga_tot else 0 end * bonin_yul / 100)
							   - (sum(case when IFNULL(t01_bipay_umu,\'N\') != \'Y\' then t01_suga_tot else 0 end * bonin_yul / 100) % 10)
							   +  sum(case when IFNULL(t01_bipay_umu,\'N\')  = \'Y\' then t01_suga_tot else 0 end) as plan_my_amt

						   ,      sum(case when IFNULL(t01_bipay_umu,\'N\') = \'Y\' then t01_suga_tot else 0 end) as plan_bipay

						   ,      sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end then t01_conf_suga_value else 0 end) as result_amt

						   ,      sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') != \'Y\' then t01_conf_suga_value else 0 end)
							   - (sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') != \'Y\' then t01_conf_suga_value * bonin_yul / 100 else 0 end)
							   - (sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') != \'Y\' then t01_conf_suga_value * bonin_yul / 100 else 0 end) % 10)) as result_public_amt

						   ,      sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') != \'Y\' then t01_conf_suga_value * bonin_yul / 100 else 0 end)
							   - (sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') != \'Y\' then t01_conf_suga_value * bonin_yul / 100 else 0 end) % 10)
							   +  sum(case when t01_status_gbn = \'1\' and t01_conf_soyotime > case when t01_svc_subcode = \'200\' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') = \'Y\' then t01_conf_suga_value else 0 end) as result_my_amt
							 from (
								  select m03_ccode as code
								  ,      min(m03_mkind)
								  ,      m03_jumin as jumin
								  ,      m03_name as name
								  ,      ifnull(lvl.lvl, \'9\') as ylvl
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
								    left join (
									     select jumin
									     ,      min(level) as lvl
										   from client_his_lvl
										  where org_no = \''.$code.'\'
										    and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
										    and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
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
					 group by jumin, name, svc_code, ylvl, kind, public_limit_amt
					 order by name';
		}else{
			$sql = "select jumin
					,      name
					,      svc_code
					,      ylvl
					,      kind
					,      public_limit_amt
					,      sum(plan_public_amt - plan_my_amt) as plan_public_amt
					,      sum(plan_my_amt) as plan_my_amt
					,      sum(result_public_amt) as result_amt
					,      case when sum(result_public_amt) > public_limit_amt then public_limit_amt else sum(result_public_amt) end as result_public_amt
					,      case when sum(result_public_amt) > public_limit_amt then sum(result_public_amt) - public_limit_amt else 0 end + sum(result_my_amt) as result_my_amt
					  from (
						   select jumin
						   ,      name
						   ,      t01_svc_subcode as svc_code
						   ,      ylvl
						   ,      kind
						   ,      kupyeo_1 as public_limit_amt
						   /*,      sum(t01_suga_tot) as plan_public_amt*/
						   ,      sum(t01_suga_tot) as plan_public_amt

						   /*,      sum(t01_suga_tot * bonin_yul / 100) - (sum(t01_suga_tot * bonin_yul / 100) % 10) as plan_my_amt*/
						   ,      sum(case when IFNULL(t01_bipay_umu,\'N\') != 'Y' then t01_suga_tot else 0 end * bonin_yul / 100)
							   - (sum(case when IFNULL(t01_bipay_umu,\'N\') != 'Y' then t01_suga_tot else 0 end * bonin_yul / 100) % 10)
							   +  sum(case when IFNULL(t01_bipay_umu,\'N\')  = 'Y' then t01_suga_tot else 0 end) as plan_my_amt

						   ,      sum(case when IFNULL(t01_bipay_umu,\'N\') = 'Y' then t01_suga_tot else 0 end) as plan_bipay

						   /*
						   ,      sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value else 0 end) as result_amt

						   ,      sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') != 'Y' then t01_conf_suga_value else 0 end)
							   - (sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end)
							   - (sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) % 10)) as result_public_amt

						   ,      sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end)
							   - (sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) % 10)
							   +  sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') = 'Y' then t01_conf_suga_value else 0 end) as result_my_amt
						   */
						   ,      sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value else 0 end) as result_amt

						   ,      sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') != 'Y' then t01_conf_suga_value else 0 end)
							   - (sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') != 'Y' then t01_conf_suga_value * bonin_yul / 100 else 0 end)
							   - (sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') != 'Y' then t01_conf_suga_value * bonin_yul / 100 else 0 end) % 10)) as result_public_amt

						   ,      sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') != 'Y' then t01_conf_suga_value * bonin_yul / 100 else 0 end)
							   - (sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') != 'Y' then t01_conf_suga_value * bonin_yul / 100 else 0 end) % 10)
							   +  sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end and IFNULL(t01_bipay_umu,\'N\') = 'Y' then t01_conf_suga_value else 0 end) as result_my_amt
							 from (
								  select m03_jumin as jumin
								  ,      m03_name as name
								  ,      m03_ylvl as ylvl
								  ,      m03_skind as kind
								  ,      m03_bonin_yul as bonin_yul
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
							group by jumin, name, t01_svc_subcode, ylvl, kupyeo_1
						   ) as t
					 group by jumin, name, svc_code, ylvl, kind, public_limit_amt
					 order by name";
		}

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$index  = 0;
			$svc_i  = 0;
			$kind_i = 0;

			//장기요양등급별
			for($j=0; $j<$data_count; $j++){
				if ($data[$j]['code'] == $row['ylvl']){
					$index = $j;
					break;
				}
			}


			//서비스별
			for($j=0; $j<$svc_cnt; $j++){
				if ($svc[$j]['code'] == $row['svc_code']){
					$svc_i = $j;
					break;
				}
			}



			//수급자구분별
			for($j=0; $j<$kind_cnt; $j++){
				if ($kind[$j]['code'] == $row['kind']){
					$kind_i = $j;
					break;
				}
			}




			//수급자 변경
			if ($temp_data != $row['jumin'].'_'.$row['ylvl']){
				$temp_data  = $row['jumin'].'_'.$row['ylvl'];

				// 장기요양 등급별
				$data[$index]['count'] ++;
				$data[$index]['public_limit_amt'] += $row['public_limit_amt'];
			}

			//수급자 변경
			if ($temp_svc != $row['jumin'].'_'.$row['svc_code']){
				$temp_svc  = $row['jumin'].'_'.$row['svc_code'];

				//서비스별
				$svc[$svc_i]['count'] ++;
				//$svc[$svc_i]['public_limit_amt'] += $row['public_limit_amt'];
			}

			//수급자 변경
			if ($temp_kind != $row['jumin'].'_'.$row['kind']){
				$temp_kind  = $row['jumin'].'_'.$row['kind'];

				//수급자구분별
				$kind[$kind_i]['count'] ++;
				$kind[$kind_i]['public_limit_amt'] += $row['public_limit_amt'];
			}

			$data[$index]['plan_public_amt']	+= $row['plan_public_amt'];
			$data[$index]['plan_my_amt']		+= $row['plan_my_amt'];
			$data[$index]['result_public_amt']	+= $row['result_public_amt'];
			$data[$index]['result_my_amt']		+= $row['result_my_amt'];
			$data[$index]['result_my_amt']		+= $row['result_bipay'];

			$svc[$svc_i]['plan_public_amt']		+= $row['plan_public_amt'];
			$svc[$svc_i]['plan_my_amt']			+= $row['plan_my_amt'];
			$svc[$svc_i]['result_public_amt']	+= $row['result_public_amt'];
			$svc[$svc_i]['result_my_amt']		+= $row['result_my_amt'];
			$svc[$svc_i]['result_my_amt']		+= $row['result_bipay'];

			$kind[$kind_i]['plan_public_amt']	+= $row['plan_public_amt'];
			$kind[$kind_i]['plan_my_amt']		+= $row['plan_my_amt'];
			$kind[$kind_i]['result_public_amt']	+= $row['result_public_amt'];
			$kind[$kind_i]['result_my_amt']		+= $row['result_my_amt'];
			$kind[$kind_i]['result_my_amt']		+= $row['result_bipay'];
		}

		$conn->row_free();

		for($i=0; $i<$data_count; $i++){
			if ($data[$i]['plan_public_amt'] > 0) $data[$i]['public_rate'] = round($data[$i]['result_public_amt'] / $data[$i]['plan_public_amt'] * 100, 1);
		 	if ($data[$i]['plan_my_amt']     > 0) $data[$i]['my_rate']	   = round($data[$i]['result_my_amt']     / $data[$i]['plan_my_amt'] * 100, 1);
		}

		$tot_count				= 0;
		$tot_public_limit_amt	= 0;
		$tot_plan_public_amt	= 0;
		$tot_plan_my_amt		= 0;
		$tot_result_public_amt	= 0;
		$tot_result_my_amt		= 0;

		for($i=0; $i<$data_count; $i++){?>
			<tr>
				<td class="center"><a href="#" onclick="go_level('<?=$data[$i]['code'];?>');"><?=$data[$i]['gubun'];?></a></td>
				<td class="center"><?=$data[$i]['count'];?></td>
				<td class="right"><?=number_format($data[$i]['public_limit_amt']);?></td>
				<td class="right"><?=number_format($data[$i]['plan_public_amt']);?></td>
				<td class="right"><?=number_format($data[$i]['plan_my_amt']);?></td>
				<td class="right"><?=number_format($data[$i]['plan_public_amt']+$data[$i]['plan_my_amt']);?></td>
				<td class="right"><?=number_format($data[$i]['result_public_amt']);?></td>
				<td class="right"><?=number_format($data[$i]['result_my_amt']);?></td>
				<td class="right"><?=number_format($data[$i]['result_public_amt']+$data[$i]['result_my_amt']);?></td>
				<td class="right"><?=number_format($data[$i]['public_rate'], 1);?>%</td>
				<td class="right last"><?=number_format($data[$i]['my_rate'], 1);?>%</td>
			</tr><?

			$tot_count				+= $data[$i]['count'];
			$tot_public_limit_amt	+= $data[$i]['public_limit_amt'];
			$tot_plan_public_amt	+= $data[$i]['plan_public_amt'];
			$tot_plan_my_amt		+= $data[$i]['plan_my_amt'];
			$tot_result_public_amt	+= $data[$i]['result_public_amt'];
			$tot_result_my_amt		+= $data[$i]['result_my_amt'];
		}
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="center sum">계</td>
			<td class="center sum"><?=$tot_count;?></td>
			<td class="right sum"><?=number_format($tot_public_limit_amt);?></td>
			<td class="right sum"><?=number_format($tot_plan_public_amt);?></td>
			<td class="right sum"><?=number_format($tot_plan_my_amt);?></td>
			<td class="right sum"><?=number_format($tot_plan_public_amt+$tot_plan_my_amt);?></td>
			<td class="right sum"><?=number_format($tot_result_public_amt);?></td>
			<td class="right sum"><?=number_format($tot_result_my_amt);?></td>
			<td class="right sum"><?=number_format($tot_result_public_amt+$tot_result_my_amt);?></td>
			<td class="right sum"><?=number_format(round($tot_result_public_amt / ($tot_plan_public_amt != 0 ? $tot_plan_public_amt : 1) * 100, 1), 1);?>%</td>
			<td class="right sum last"><?=number_format(round($tot_result_my_amt / ($tot_plan_my_amt != 0 ? $tot_plan_my_amt : 1) * 100, 1), 1);?>%</td>
		</tr>
	</tbody>
</table>

<div class="title title_border">서비스별</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="60px">
		<col width="76px" span="8">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">구분</th>
			<th class="head" rowspan="2">인원수</th>
			<th class="head" rowspan="2">공단한도금액</th>
			<th class="head" colspan="3">계획</th>
			<th class="head" colspan="3">실적</th>
			<th class="head last" colspan="2">대비수급율</th>
		</tr>
		<tr>
			<th class="head">공단청구</th>
			<th class="head">본인부담</th>
			<th class="head">합계</th>
			<th class="head">공단청구</th>
			<th class="head">본인부담</th>
			<th class="head">합계</th>
			<th class="head">공단청구</th>
			<th class="head last">본인부담</th>
		</tr>
	</thead>
	<tbody>
	<?
		for($i=0; $i<$svc_cnt; $i++){
			if ($svc[$i]['plan_public_amt'] > 0) $svc[$i]['public_rate'] = round($svc[$i]['result_public_amt'] / $svc[$i]['plan_public_amt'] * 100, 1);
		 	if ($svc[$i]['plan_my_amt']     > 0) $svc[$i]['my_rate']	 = round($svc[$i]['result_my_amt']     / $svc[$i]['plan_my_amt'] * 100, 1);
		}

		$tot_count				= 0;
		$tot_public_limit_amt	= 0;
		$tot_plan_public_amt	= 0;
		$tot_plan_my_amt		= 0;
		$tot_result_public_amt	= 0;
		$tot_result_my_amt		= 0;

		for($i=0; $i<$svc_cnt; $i++){?>
			<tr>
				<td class="center"><?=$svc[$i]['gubun'];?></td>
				<td class="center"><?=$svc[$i]['count'];?></td>
				<td class="right"><?=number_format($svc[$i]['public_limit_amt']);?></td>
				<td class="right"><?=number_format($svc[$i]['plan_public_amt']);?></td>
				<td class="right"><?=number_format($svc[$i]['plan_my_amt']);?></td>
				<td class="right"><?=number_format($svc[$i]['plan_public_amt']+$svc[$i]['plan_my_amt']);?></td>
				<td class="right"><?=number_format($svc[$i]['result_public_amt']);?></td>
				<td class="right"><?=number_format($svc[$i]['result_my_amt']);?></td>
				<td class="right"><?=number_format($svc[$i]['result_public_amt']+$svc[$i]['result_my_amt']);?></td>
				<td class="right"><?=number_format($svc[$i]['public_rate'], 1);?>%</td>
				<td class="right last"><?=number_format($svc[$i]['my_rate'], 1);?>%</td>
			</tr><?

			$tot_count				+= $svc[$i]['count'];
			$tot_public_limit_amt	+= $svc[$i]['public_limit_amt'];
			$tot_plan_public_amt	+= $svc[$i]['plan_public_amt'];
			$tot_plan_my_amt		+= $svc[$i]['plan_my_amt'];
			$tot_result_public_amt	+= $svc[$i]['result_public_amt'];
			$tot_result_my_amt		+= $svc[$i]['result_my_amt'];
		}

		echo '<tr>
				<td class=\'center sum\'>계</td>
				<td class=\'center sum\'>'.$tot_count.'</td>
				<td class=\'right sum\'>'.number_format($tot_public_limit_amt).'</td>
				<td class=\'right sum\'>'.number_format($tot_plan_public_amt).'</td>
				<td class=\'right sum\'>'.number_format($tot_plan_my_amt).'</td>
				<td class=\'right sum\'>'.number_format($tot_plan_public_amt+$tot_plan_my_amt).'</td>
				<td class=\'right sum\'>'.number_format($tot_result_public_amt).'</td>
				<td class=\'right sum\'>'.number_format($tot_result_my_amt).'</td>
				<td class=\'right sum\'>'.number_format($tot_result_public_amt+$tot_result_my_amt).'</td>
				<td class=\'right sum\'>'.number_format(round($tot_result_public_amt / ($tot_plan_public_amt != 0 ? $tot_plan_public_amt : 1) * 100, 1), 1).'%</td>
				<td class=\'right sum last\'>'.number_format(round($tot_result_my_amt / ($tot_plan_my_amt != 0 ? $tot_plan_my_amt : 1) * 100, 1), 1).'%</td>
			  </tr>';
	?>
	</tbody>
</table>

<div class="title title_border">수급자구분별</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="60px">
		<col width="76px" span="8">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">구분</th>
			<th class="head" rowspan="2">인원수</th>
			<th class="head" rowspan="2">공단한도금액</th>
			<th class="head" colspan="3">계획</th>
			<th class="head" colspan="3">실적</th>
			<th class="head last" colspan="2">대비수급율</th>
		</tr>
		<tr>
			<th class="head">공단청구</th>
			<th class="head">본인부담</th>
			<th class="head">합계</th>
			<th class="head">공단청구</th>
			<th class="head">본인부담</th>
			<th class="head">합계</th>
			<th class="head">공단청구</th>
			<th class="head last">본인부담</th>
		</tr>
	</thead>
	<tbody>
	<?
		for($i=0; $i<$kind_cnt; $i++){
			if ($kind[$i]['plan_public_amt'] > 0) $kind[$i]['public_rate'] = round($kind[$i]['result_public_amt'] / $kind[$i]['plan_public_amt'] * 100, 1);
		 	if ($kind[$i]['plan_my_amt']     > 0) $kind[$i]['my_rate']	   = round($kind[$i]['result_my_amt']     / $kind[$i]['plan_my_amt'] * 100, 1);
		}

		$tot_count				= 0;
		$tot_public_limit_amt	= 0;
		$tot_plan_public_amt	= 0;
		$tot_plan_my_amt		= 0;
		$tot_result_public_amt	= 0;
		$tot_result_my_amt		= 0;

		for($i=0; $i<$kind_cnt; $i++){?>
			<tr>
				<td class="center"><?=$kind[$i]['gubun'];?></td>
				<td class="center"><?=$kind[$i]['count'];?></td>
				<td class="right"><?=number_format($kind[$i]['public_limit_amt']);?></td>
				<td class="right"><?=number_format($kind[$i]['plan_public_amt']);?></td>
				<td class="right"><?=number_format($kind[$i]['plan_my_amt']);?></td>
				<td class="right"><?=number_format($kind[$i]['plan_public_amt']+$kind[$i]['plan_my_amt']);?></td>
				<td class="right"><?=number_format($kind[$i]['result_public_amt']);?></td>
				<td class="right"><?=number_format($kind[$i]['result_my_amt']);?></td>
				<td class="right"><?=number_format($kind[$i]['result_public_amt']+$kind[$i]['result_my_amt']);?></td>
				<td class="right"><?=number_format($kind[$i]['public_rate'], 1);?>%</td>
				<td class="right last"><?=number_format($kind[$i]['my_rate'], 1);?>%</td>
			</tr><?

			$tot_count				+= $kind[$i]['count'];
			$tot_public_limit_amt	+= $kind[$i]['public_limit_amt'];
			$tot_plan_public_amt	+= $kind[$i]['plan_public_amt'];
			$tot_plan_my_amt		+= $kind[$i]['plan_my_amt'];
			$tot_result_public_amt	+= $kind[$i]['result_public_amt'];
			$tot_result_my_amt		+= $kind[$i]['result_my_amt'];
		}

		echo '<tr>
				<td class=\'center sum\'>계</td>
				<td class=\'center sum\'>'.$tot_count.'</td>
				<td class=\'right sum\'>'.number_format($tot_public_limit_amt).'</td>
				<td class=\'right sum\'>'.number_format($tot_plan_public_amt).'</td>
				<td class=\'right sum\'>'.number_format($tot_plan_my_amt).'</td>
				<td class=\'right sum\'>'.number_format($tot_plan_public_amt+$tot_plan_my_amt).'</td>
				<td class=\'right sum\'>'.number_format($tot_result_public_amt).'</td>
				<td class=\'right sum\'>'.number_format($tot_result_my_amt).'</td>
				<td class=\'right sum\'>'.number_format($tot_result_public_amt+$tot_result_my_amt).'</td>
				<td class=\'right sum\'>'.number_format(round($tot_result_public_amt / ($tot_plan_public_amt != 0 ? $tot_plan_public_amt : 1) * 100, 1), 1).'%</td>
				<td class=\'right sum last\'>'.number_format(round($tot_result_my_amt / ($tot_plan_my_amt != 0 ? $tot_plan_my_amt : 1) * 100, 1), 1).'%</td>
			  </tr>';
	?>
	</tbody>
</table>

<input type="hidden" name="month" value="<?=$month;?>">
<input type="hidden" name="level" value="">

</form>

<div></div>

<?
	if ($_SESSION['userLevel'] == 'A'){
	}else{
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
	}

	function init_data($code, $gubun){
		$a['code']				= $code;
		$a['gubun']				= $gubun;
		$a['count']				= 0;
		$a['public_limit_amt']	= 0;
		$a['plan_public_amt']	= 0;
		$a['plan_my_amt']		= 0;
		$a['result_public_amt']	= 0;
		$a['result_my_amt']		= 0;
		$a['public_rate']		= 0;
		$a['my_rate']			= 0;

		return $a;
	}
?>