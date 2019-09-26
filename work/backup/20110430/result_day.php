<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_body_header.php");

	$code  = $_REQUEST['code'] != '' ? $_REQUEST['code'] : $_SESSION["userCenterCode"];
	$kind  = $conn->center_kind($code);
	$year  = $_REQUEST['year']  != '' ? $_REQUEST['year']  : date('Y', mktime());
	$month = $_REQUEST['month'] != '' ? $_REQUEST['month'] : date('m', mktime());
	$day   = $_REQUEST['day']   != '' ? $_REQUEST['day']   : date('d', mktime());
	$mode  = 1;

	$init_year = $myF->year();
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function search(){
	var f = document.f;

	f.action = 'result_day.php';
	f.submit();
}

function detail(day){
	var f = document.f;

	f.day.value = day;
	f.action = 'result_detail.php';
	f.submit();
}
-->
</script>

<div class="title">일 실적 등록(수급자)</div>

<form name="f" method="post">

<table class="my_table my_border">
	<colgroup>
		<col width="40px">
		<col width="150px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년월</th>
			<td>
				<select name="year" style="width:auto;">
				<?
					for($i=$init_year[0]; $i<= $init_year[1]; $i++){?>
						<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?></option>><?
					}
				?>
				</select>년
				<select name="month" style="width:auto;">
				<?
					for($i=1; $i<=12; $i++){
						$mon = ($i<10?'0':'').$i; ?>
						<option value="<?=$mon;?>" <? if($mon == $month){?>selected<?} ?>><?=$mon;?></option><?
					}
				?>
				</select>월
			</td>
			<td class="left last">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="search();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table bottom" style="width:100%; border-bottom:none;">
	<colgroup>
		<col span="7">
	</colgroup>
	<thead>
		<tr>
			<th class="head">일</th>
			<th class="head">월</th>
			<th class="head">화</th>
			<th class="head">수</th>
			<th class="head">목</th>
			<th class="head">금</th>
			<th class="head last">토</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select sum(case when substring(t01_sugup_date, 7, 8) = '01' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_01
				,      sum(case when substring(t01_sugup_date, 7, 8) = '02' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_02
				,      sum(case when substring(t01_sugup_date, 7, 8) = '03' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_03
				,      sum(case when substring(t01_sugup_date, 7, 8) = '04' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_04
				,      sum(case when substring(t01_sugup_date, 7, 8) = '05' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_05
				,      sum(case when substring(t01_sugup_date, 7, 8) = '06' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_06
				,      sum(case when substring(t01_sugup_date, 7, 8) = '07' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_07
				,      sum(case when substring(t01_sugup_date, 7, 8) = '08' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_08
				,      sum(case when substring(t01_sugup_date, 7, 8) = '09' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_09
				,      sum(case when substring(t01_sugup_date, 7, 8) = '10' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_10
				,      sum(case when substring(t01_sugup_date, 7, 8) = '11' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_11
				,      sum(case when substring(t01_sugup_date, 7, 8) = '12' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_12
				,      sum(case when substring(t01_sugup_date, 7, 8) = '13' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_13
				,      sum(case when substring(t01_sugup_date, 7, 8) = '14' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_14
				,      sum(case when substring(t01_sugup_date, 7, 8) = '15' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_15
				,      sum(case when substring(t01_sugup_date, 7, 8) = '16' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_16
				,      sum(case when substring(t01_sugup_date, 7, 8) = '17' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_17
				,      sum(case when substring(t01_sugup_date, 7, 8) = '18' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_18
				,      sum(case when substring(t01_sugup_date, 7, 8) = '19' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_19
				,      sum(case when substring(t01_sugup_date, 7, 8) = '20' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_20
				,      sum(case when substring(t01_sugup_date, 7, 8) = '21' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_21
				,      sum(case when substring(t01_sugup_date, 7, 8) = '22' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_22
				,      sum(case when substring(t01_sugup_date, 7, 8) = '23' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_23
				,      sum(case when substring(t01_sugup_date, 7, 8) = '24' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_24
				,      sum(case when substring(t01_sugup_date, 7, 8) = '25' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_25
				,      sum(case when substring(t01_sugup_date, 7, 8) = '26' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_26
				,      sum(case when substring(t01_sugup_date, 7, 8) = '27' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_27
				,      sum(case when substring(t01_sugup_date, 7, 8) = '28' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_28
				,      sum(case when substring(t01_sugup_date, 7, 8) = '29' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_29
				,      sum(case when substring(t01_sugup_date, 7, 8) = '30' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_30
				,      sum(case when substring(t01_sugup_date, 7, 8) = '31' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_31

				,      sum(case when substring(t01_sugup_date, 7, 8) = '01' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_01
				,      sum(case when substring(t01_sugup_date, 7, 8) = '02' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_02
				,      sum(case when substring(t01_sugup_date, 7, 8) = '03' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_03
				,      sum(case when substring(t01_sugup_date, 7, 8) = '04' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_04
				,      sum(case when substring(t01_sugup_date, 7, 8) = '05' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_05
				,      sum(case when substring(t01_sugup_date, 7, 8) = '06' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_06
				,      sum(case when substring(t01_sugup_date, 7, 8) = '07' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_07
				,      sum(case when substring(t01_sugup_date, 7, 8) = '08' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_08
				,      sum(case when substring(t01_sugup_date, 7, 8) = '09' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_09
				,      sum(case when substring(t01_sugup_date, 7, 8) = '10' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_10
				,      sum(case when substring(t01_sugup_date, 7, 8) = '11' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_11
				,      sum(case when substring(t01_sugup_date, 7, 8) = '12' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_12
				,      sum(case when substring(t01_sugup_date, 7, 8) = '13' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_13
				,      sum(case when substring(t01_sugup_date, 7, 8) = '14' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_14
				,      sum(case when substring(t01_sugup_date, 7, 8) = '15' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_15
				,      sum(case when substring(t01_sugup_date, 7, 8) = '16' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_16
				,      sum(case when substring(t01_sugup_date, 7, 8) = '17' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_17
				,      sum(case when substring(t01_sugup_date, 7, 8) = '18' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_18
				,      sum(case when substring(t01_sugup_date, 7, 8) = '19' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_19
				,      sum(case when substring(t01_sugup_date, 7, 8) = '20' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_20
				,      sum(case when substring(t01_sugup_date, 7, 8) = '21' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_21
				,      sum(case when substring(t01_sugup_date, 7, 8) = '22' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_22
				,      sum(case when substring(t01_sugup_date, 7, 8) = '23' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_23
				,      sum(case when substring(t01_sugup_date, 7, 8) = '24' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_24
				,      sum(case when substring(t01_sugup_date, 7, 8) = '25' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_25
				,      sum(case when substring(t01_sugup_date, 7, 8) = '26' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_26
				,      sum(case when substring(t01_sugup_date, 7, 8) = '27' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_27
				,      sum(case when substring(t01_sugup_date, 7, 8) = '28' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_28
				,      sum(case when substring(t01_sugup_date, 7, 8) = '29' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_29
				,      sum(case when substring(t01_sugup_date, 7, 8) = '30' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_30
				,      sum(case when substring(t01_sugup_date, 7, 8) = '31' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_31

				,      sum(case when substring(t01_sugup_date, 7, 8) = '01' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_01
				,      sum(case when substring(t01_sugup_date, 7, 8) = '02' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_02
				,      sum(case when substring(t01_sugup_date, 7, 8) = '03' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_03
				,      sum(case when substring(t01_sugup_date, 7, 8) = '04' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_04
				,      sum(case when substring(t01_sugup_date, 7, 8) = '05' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_05
				,      sum(case when substring(t01_sugup_date, 7, 8) = '06' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_06
				,      sum(case when substring(t01_sugup_date, 7, 8) = '07' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_07
				,      sum(case when substring(t01_sugup_date, 7, 8) = '08' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_08
				,      sum(case when substring(t01_sugup_date, 7, 8) = '09' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_09
				,      sum(case when substring(t01_sugup_date, 7, 8) = '10' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_10
				,      sum(case when substring(t01_sugup_date, 7, 8) = '11' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_11
				,      sum(case when substring(t01_sugup_date, 7, 8) = '12' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_12
				,      sum(case when substring(t01_sugup_date, 7, 8) = '13' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_13
				,      sum(case when substring(t01_sugup_date, 7, 8) = '14' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_14
				,      sum(case when substring(t01_sugup_date, 7, 8) = '15' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_15
				,      sum(case when substring(t01_sugup_date, 7, 8) = '16' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_16
				,      sum(case when substring(t01_sugup_date, 7, 8) = '17' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_17
				,      sum(case when substring(t01_sugup_date, 7, 8) = '18' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_18
				,      sum(case when substring(t01_sugup_date, 7, 8) = '19' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_19
				,      sum(case when substring(t01_sugup_date, 7, 8) = '20' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_20
				,      sum(case when substring(t01_sugup_date, 7, 8) = '21' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_21
				,      sum(case when substring(t01_sugup_date, 7, 8) = '22' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_22
				,      sum(case when substring(t01_sugup_date, 7, 8) = '23' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_23
				,      sum(case when substring(t01_sugup_date, 7, 8) = '24' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_24
				,      sum(case when substring(t01_sugup_date, 7, 8) = '25' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_25
				,      sum(case when substring(t01_sugup_date, 7, 8) = '26' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_26
				,      sum(case when substring(t01_sugup_date, 7, 8) = '27' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_27
				,      sum(case when substring(t01_sugup_date, 7, 8) = '28' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_28
				,      sum(case when substring(t01_sugup_date, 7, 8) = '29' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_29
				,      sum(case when substring(t01_sugup_date, 7, 8) = '30' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_30
				,      sum(case when substring(t01_sugup_date, 7, 8) = '31' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_31

				,      sum(case when substring(t01_sugup_date, 7, 8) = '01' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_01
				,      sum(case when substring(t01_sugup_date, 7, 8) = '02' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_02
				,      sum(case when substring(t01_sugup_date, 7, 8) = '03' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_03
				,      sum(case when substring(t01_sugup_date, 7, 8) = '04' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_04
				,      sum(case when substring(t01_sugup_date, 7, 8) = '05' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_05
				,      sum(case when substring(t01_sugup_date, 7, 8) = '06' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_06
				,      sum(case when substring(t01_sugup_date, 7, 8) = '07' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_07
				,      sum(case when substring(t01_sugup_date, 7, 8) = '08' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_08
				,      sum(case when substring(t01_sugup_date, 7, 8) = '09' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_09
				,      sum(case when substring(t01_sugup_date, 7, 8) = '10' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_10
				,      sum(case when substring(t01_sugup_date, 7, 8) = '11' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_11
				,      sum(case when substring(t01_sugup_date, 7, 8) = '12' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_12
				,      sum(case when substring(t01_sugup_date, 7, 8) = '13' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_13
				,      sum(case when substring(t01_sugup_date, 7, 8) = '14' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_14
				,      sum(case when substring(t01_sugup_date, 7, 8) = '15' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_15
				,      sum(case when substring(t01_sugup_date, 7, 8) = '16' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_16
				,      sum(case when substring(t01_sugup_date, 7, 8) = '17' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_17
				,      sum(case when substring(t01_sugup_date, 7, 8) = '18' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_18
				,      sum(case when substring(t01_sugup_date, 7, 8) = '19' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_19
				,      sum(case when substring(t01_sugup_date, 7, 8) = '20' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_20
				,      sum(case when substring(t01_sugup_date, 7, 8) = '21' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_21
				,      sum(case when substring(t01_sugup_date, 7, 8) = '22' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_22
				,      sum(case when substring(t01_sugup_date, 7, 8) = '23' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_23
				,      sum(case when substring(t01_sugup_date, 7, 8) = '24' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_24
				,      sum(case when substring(t01_sugup_date, 7, 8) = '25' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_25
				,      sum(case when substring(t01_sugup_date, 7, 8) = '26' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_26
				,      sum(case when substring(t01_sugup_date, 7, 8) = '27' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_27
				,      sum(case when substring(t01_sugup_date, 7, 8) = '28' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_28
				,      sum(case when substring(t01_sugup_date, 7, 8) = '29' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_29
				,      sum(case when substring(t01_sugup_date, 7, 8) = '30' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_30
				,      sum(case when substring(t01_sugup_date, 7, 8) = '31' and t01_status_gbn = 'C' then 1 else 0 end) as day_000_31

				  from t01iljung
				 where t01_ccode = '$code'
				   and t01_mkind = '$kind'
				   and t01_sugup_date like '$year$month%'
				   and t01_del_yn = 'N'";

		$conn->query($sql);
		$data = $conn->fetch();
		$conn->row_free();

		$cnt['200'] = 0;
		$cnt['500'] = 0;
		$cnt['800'] = 0;
		$cnt['000'] = 0;

		$cal_time   = mktime(0, 0, 1, $month, 1, $year);
		$today      = date('Ymd', mktime());
		$last_day   = date('t', $cal_time);
		$start_week = date('w', strtotime(date('Y-m', $cal_time).'-01'));
		$total_week = ceil(($last_day + $start_week) / 7);
		$last_week  = date('w', strtotime(date('Y-m', $cal_time).'-'.$last_day));
		$day        = 1;

		// 총 주 수에 맞춰서 세로줄 만들기
		for($i=1; $i<=$total_week; $i++){?>
			<tr><?
			// 총 가로칸 만들기
			for ($j=0; $j<=6; $j++){
				switch($j){
				case 0:
					$class_td  = '';
					$class_day = 'my_week_sun';
					break;
				case 6:
					$class_td  = 'last';
					$class_day = 'my_week_sat';
					break;
				default:
					$class_td  = '';
					$class_day = 'my_week';
					break;
				}

				$content  = '';
				$temp_day = (($day < 10) ? '0' : '').$day;

				if ($today >= $year.$month.$temp_day){
					$font_color = '';
				}else{
					$font_color = 'color:#cccccc;';
				}

				if ($data['day_200_'.$temp_day] > 0){
					$content .= '요양 : '.$data['day_200_'.$temp_day].'건<br>';
					$cnt['200'] ++;
				}
				if ($data['day_500_'.$temp_day] > 0){
					$content .= '목욕 : '.$data['day_500_'.$temp_day].'건<br>';
					$cnt['500'] ++;
				}
				if ($data['day_800_'.$temp_day] > 0){
					$content .= '간호 : '.$data['day_800_'.$temp_day].'건<br>';
					$cnt['800'] ++;
				}
				if ($data['day_000_'.$temp_day] > 0){
					$content .= '<span style="color:#ff0000;">에러 : '.$data['day_000_'.$temp_day].'건</span><br>';
					$cnt['000'] ++;
				}

				if (!(($i == 1 && $j < $start_week) || ($i == $total_week && $j > $last_week))){?>
					<td class="<?=$class_td;?>">
						<table style="width:100%; height:100%;">
							<colgroup>
								<col width="20%">
								<col width="80%">
							</colgroup>
							<tbody>
								<tr>
									<th class="no_border top right <?=$class_day;?>"><?=$day;?></th>
									<td class="no_border top left"><a href="#" onclick="detail('<?=$temp_day;?>');" style="<?=$font_color;?>"><?=$content;?></a></td>
								</tr>
							</tbody>
						</table>
					</td><?
					$day ++;
				}else{?>
					<td class="<?=$class_td;?>">&nbsp;</td><?
				}
			}?>
			</tr><?
		}

		unset($data);
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="left bottom last" colspan="7">
				<span>요양 : <?=$cnt['200'];?>건 / 목욕 : <?=$cnt['500'];?>건 / 간호 : <?=$cnt['800'];?>건 / <font color="#ff0000">에러 : <?=$cnt['000'];?>건</font></span>
			</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code" value="<?=$code;?>">
<input type="hidden" name="kind" value="<?=$kind;?>">
<input type="hidden" name="day"  value="<?=$day;?>">
<input type="hidden" name="mode" value="<?=$mode;?>">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>