<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_body_header.php");

	$init_year	= $myF->year();
	$code		= $_SESSION['userCenterCode'];
	$year		= $_POST['year']  != '' ? $_POST['year']  : intval(date('Y', mktime()));
	$month		= $_POST['month'] != '' ? $_POST['month'] : intval(date('m', mktime()));
	$month		= (intval($month) < 10 ? '0' : '').intval($month);
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

<div class="title">수급내역현황(전체)</div>

<form name="f" method="post">
<?
	include_once('supply_cal.php');
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="60px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2" title="구분을 클릭하시면 등급별 상세내역이 출력됩니다."><u>구분</u></th>
			<th class="head" rowspan="2">인원수</th>
			<th class="head" rowspan="2">공단한도금액</th>
			<th class="head" colspan="2">계획</th>
			<th class="head" colspan="2">실적</th>
			<th class="head last" colspan="2">대비수급율</th>
		</tr>
		<tr>
			<th class="head">공단청구금액</th>
			<th class="head">본인부담금액</th>
			<th class="head">공단청구금액</th>
			<th class="head">본인부담금액</th>
			<th class="head">공단청구금액</th>
			<th class="head last">본인부담금액</th>
		</tr>
	</thead>
	<tbody>
	<?
		// 초기화
		$sql = "select m81_code, m81_name
				  from m81gubun
				 where m81_gbn = 'LVL'
				 order by m81_code";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$data[$i] = init_data($row['m81_code'], $row['m81_name']);
		}

		$conn->row_free();

		$data_count = sizeof($data);

		$sql = "select jumin
				,      name
				,      ylvl
				,      public_limit_amt
				,      sum(plan_public_amt) as plan_public_amt
				,      sum(plan_my_amt) as plan_my_amt
				,      sum(result_public_amt) as result_amt
				,      case when sum(result_public_amt) > public_limit_amt then public_limit_amt else sum(result_public_amt) end as result_public_amt
				,      case when sum(result_public_amt) > public_limit_amt then sum(result_public_amt) - public_limit_amt else 0 end + sum(result_my_amt) as result_my_amt
				  from (
					   select jumin
					   ,      name
					   ,      t01_svc_subcode as svc_code
					   ,      ylvl
					   ,      kupyeo_1 as public_limit_amt
					   ,      sum(t01_suga_tot) as plan_public_amt
					   ,      sum(t01_suga_tot * bonin_yul / 100) - (sum(t01_suga_tot * bonin_yul / 100) % 10) as plan_my_amt
					   ,      sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value else 0 end) as result_amt
					   ,      sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) - (sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) - (sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) % 10)) as result_public_amt
					   ,      sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) - (sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end then t01_conf_suga_value * bonin_yul / 100 else 0 end) % 10) + sum(case when t01_status_gbn = '1' and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end and t01_bipay_umu = 'Y' then t01_conf_suga_value else 0 end) as result_my_amt
						 from (
							  select m03_jumin as jumin
							  ,      m03_name as name
							  ,      m03_ylvl as ylvl
							  ,      m03_bonin_yul as bonin_yul
							  ,      m03_kupyeo_1 as kupyeo_1
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
				 group by jumin, name, ylvl, public_limit_amt
				 order by name";


		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$index = 0;

			for($j=0; $j<$data_count; $j++){
				if ($data[$j]['code'] == $row['ylvl']){
					$index = $j;
					break;
				}
			}

			if ($temp_jumin != $row['jumin']){
				$temp_jumin  = $row['jumin'];
				$data[$index]['count'] ++;
				$data[$index]['public_limit_amt']	+= $row['public_limit_amt'];
			}
			$data[$index]['plan_public_amt']	+= $row['plan_public_amt'];
			$data[$index]['plan_my_amt']		+= $row['plan_my_amt'];
			$data[$index]['result_public_amt']	+= $row['result_public_amt'];
			$data[$index]['result_my_amt']		+= $row['result_my_amt'];
			$data[$index]['result_my_amt']		+= $row['result_bipay'];
		}

		$conn->row_free();

		for($i=0; $i<$data_count; $i++){
			if ($data[$i]['plan_public_amt'] > 0) $data[$i]['public_rate']= round($data[$i]['result_public_amt'] / $data[$i]['plan_public_amt'] * 100, 1);
		 	if ($data[$i]['plan_my_amt'] > 0) $data[$i]['my_rate']	= round($data[$i]['result_my_amt'] / $data[$i]['plan_my_amt'] * 100, 1);
		}

		for($i=0; $i<$data_count; $i++){?>
			<tr>
				<td class="center"><a href="#" onclick="go_level('<?=$data[$i]['code'];?>');"><?=$data[$i]['gubun'];?></a></td>
				<td class="center"><?=$data[$i]['count'];?></td>
				<td class="right"><?=number_format($data[$i]['public_limit_amt']);?></td>
				<td class="right"><?=number_format($data[$i]['plan_public_amt']);?></td>
				<td class="right"><?=number_format($data[$i]['plan_my_amt']);?></td>
				<td class="right"><?=number_format($data[$i]['result_public_amt']);?></td>
				<td class="right"><?=number_format($data[$i]['result_my_amt']);?></td>
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
			<td class="right sum"><?=number_format($tot_result_public_amt);?></td>
			<td class="right sum"><?=number_format($tot_result_my_amt);?></td>
			<td class="right sum"><?=number_format(round($tot_result_public_amt / ($tot_plan_public_amt != 0 ? $tot_plan_public_amt : 1) * 100, 1), 1);?>%</td>
			<td class="right sum last"><?=number_format(round($tot_result_my_amt / ($tot_plan_my_amt != 0 ? $tot_plan_my_amt : 1) * 100, 1), 1);?>%</td>
		</tr>
		<tr>
			<td class="last bottom" colspan="9">&nbsp;</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="month" value="<?=$month;?>">
<input type="hidden" name="level" value="">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");

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