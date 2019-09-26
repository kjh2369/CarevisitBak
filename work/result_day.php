<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_body_header.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code  = $_SESSION["userCenterCode"]; //$_REQUEST['code'] != '' ? $_REQUEST['code'] : $_SESSION["userCenterCode"];
	$kind  = $conn->center_kind($code);
	$year  = $_REQUEST['year']  != '' ? $_REQUEST['year']  : date('Y', mktime());
	$month = $_REQUEST['month'] != '' ? $_REQUEST['month'] : date('m', mktime());
	$month = (intval($month) < 10 ? '0' : '').intval($month);
	$day   = $_REQUEST['day']   != '' ? $_REQUEST['day']   : date('d', mktime());
	$mode  = 1;

	$init_year = $myF->year();

	$close_yn = $conn->get_closing_act($code, $year.$month);

	if ($close_yn == 'Y'){
		$msg = '※ '.$year.'년 '.intval($month).'월은 마감이 완료되었습니다.';
	}else{
		$msg = '';
	}
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function month_search(month){
	var f = document.f;

	f.month.value = month;
	f.action = 'result_day.php';
	f.submit();
}

function month_detail(day){
	var f = document.f;

	f.day.value = day;
	f.action = 'result_detail.php';
	f.submit();
}

function lfDetail(day){
	var f = document.f;

	f.day.value = day;
	f.action = 'result_detail_day.php';
	f.submit();
}

function show_file_upload(top,day){
	var body = document.getElementById('body_con');
	var obj  = document.getElementById('file_show');
	var str  = document.getElementById('curr_day');
	var tl   = __getObjectLeft(body);
	var w    = 450; //obj.offsetWidth;
	var h    = obj.offsetHeight;
	var t    = __getObjectTop(top) + top.offsetHeight - 10;
	var l    = __getObjectLeft(top) - 10;

	if (body.offsetWidth - (l - tl) < w){
		l = tl + (body.offsetWidth - w);
	}

	document.getElementById('day').value = day;

	obj.style.top     = t;
	obj.style.left    = l;
	obj.style.width   = w;
	obj.style.display = '';
}

function set_csv(){
	var f = document.f;

	if (f.csv.value == ''){
		alert('입력할 CSV파일을 선택하여 주십시오.');
		return;
	}

	var exp = f.csv.value.split('.');
		exp = exp[exp.length - 1];
		exp = exp.toLowerCase();

	if (exp != 'csv'){
		alert('입력할 CSV파일을 선택하여 주십시오.');
		return;
	}

	f.action = 'result_day_csv_upload.php';
	f.submit();
}
-->
</script>

<div class="title" style="width:auto; float:left;">일 실적 등록(수급자)</div>
<div style="width:auto; font-weight:bold; margin-top:9px; text-align:right;"><?=$msg;?></div>

<form name="f" method="post" enctype="multipart/form-data">

<table class="my_table my_border">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년월</th>
			<td class="last">
				<select name="year" style="width:auto;">
				<?
					for($i=$init_year[0]; $i<= $init_year[1]; $i++){?>
						<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?></option>><?
					}
				?>
				</select>년
				<input name="month" type="hidden" value="<?=$month;?>">
			</td>
			<td class="left last"><!--span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="search();">조회</button></span-->
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

					$text = '<a href="#" style="'.$color.'" onclick="month_search(\''.$i.'\');">'.$i.'월</a>';

					if ($i == 12){
						$style = 'float:left;';
					}else{
						$style = 'float:left; margin-right:3px;';
					}?>
					<div class="<?=$class;?>" style="<?=$style;?>"><?=$text;?></div><?
				}
			?>
			</td>
		</tr>
	</tbody>
</table>

<table id="body_con" class="my_table bottom" style="width:100%; border-bottom:none;">
	<colgroup>
		<col width="15%">
		<col width="14%">
		<col width="14%">
		<col width="14%">
		<col width="14%">
		<col width="14%">
		<col width="15%">
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

				,      sum(case when substring(t01_sugup_date, 7, 8) = '01' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_01
				,      sum(case when substring(t01_sugup_date, 7, 8) = '02' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_02
				,      sum(case when substring(t01_sugup_date, 7, 8) = '03' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_03
				,      sum(case when substring(t01_sugup_date, 7, 8) = '04' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_04
				,      sum(case when substring(t01_sugup_date, 7, 8) = '05' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_05
				,      sum(case when substring(t01_sugup_date, 7, 8) = '06' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_06
				,      sum(case when substring(t01_sugup_date, 7, 8) = '07' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_07
				,      sum(case when substring(t01_sugup_date, 7, 8) = '08' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_08
				,      sum(case when substring(t01_sugup_date, 7, 8) = '09' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_09
				,      sum(case when substring(t01_sugup_date, 7, 8) = '10' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_10
				,      sum(case when substring(t01_sugup_date, 7, 8) = '11' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_11
				,      sum(case when substring(t01_sugup_date, 7, 8) = '12' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_12
				,      sum(case when substring(t01_sugup_date, 7, 8) = '13' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_13
				,      sum(case when substring(t01_sugup_date, 7, 8) = '14' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_14
				,      sum(case when substring(t01_sugup_date, 7, 8) = '15' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_15
				,      sum(case when substring(t01_sugup_date, 7, 8) = '16' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_16
				,      sum(case when substring(t01_sugup_date, 7, 8) = '17' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_17
				,      sum(case when substring(t01_sugup_date, 7, 8) = '18' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_18
				,      sum(case when substring(t01_sugup_date, 7, 8) = '19' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_19
				,      sum(case when substring(t01_sugup_date, 7, 8) = '20' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_20
				,      sum(case when substring(t01_sugup_date, 7, 8) = '21' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_21
				,      sum(case when substring(t01_sugup_date, 7, 8) = '22' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_22
				,      sum(case when substring(t01_sugup_date, 7, 8) = '23' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_23
				,      sum(case when substring(t01_sugup_date, 7, 8) = '24' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_24
				,      sum(case when substring(t01_sugup_date, 7, 8) = '25' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_25
				,      sum(case when substring(t01_sugup_date, 7, 8) = '26' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_26
				,      sum(case when substring(t01_sugup_date, 7, 8) = '27' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_27
				,      sum(case when substring(t01_sugup_date, 7, 8) = '28' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_28
				,      sum(case when substring(t01_sugup_date, 7, 8) = '29' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_29
				,      sum(case when substring(t01_sugup_date, 7, 8) = '30' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_30
				,      sum(case when substring(t01_sugup_date, 7, 8) = '31' and cast(t01_svc_subcode as signed) > 20 and cast(t01_svc_subcode as signed) < 30 and t01_status_gbn != 'C' then 1 else 0 end) as day_voucher_31

				,      sum(case when substring(t01_sugup_date, 7, 8) = '01' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_01
				,      sum(case when substring(t01_sugup_date, 7, 8) = '02' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_02
				,      sum(case when substring(t01_sugup_date, 7, 8) = '03' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_03
				,      sum(case when substring(t01_sugup_date, 7, 8) = '04' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_04
				,      sum(case when substring(t01_sugup_date, 7, 8) = '05' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_05
				,      sum(case when substring(t01_sugup_date, 7, 8) = '06' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_06
				,      sum(case when substring(t01_sugup_date, 7, 8) = '07' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_07
				,      sum(case when substring(t01_sugup_date, 7, 8) = '08' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_08
				,      sum(case when substring(t01_sugup_date, 7, 8) = '09' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_09
				,      sum(case when substring(t01_sugup_date, 7, 8) = '10' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_10
				,      sum(case when substring(t01_sugup_date, 7, 8) = '11' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_11
				,      sum(case when substring(t01_sugup_date, 7, 8) = '12' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_12
				,      sum(case when substring(t01_sugup_date, 7, 8) = '13' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_13
				,      sum(case when substring(t01_sugup_date, 7, 8) = '14' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_14
				,      sum(case when substring(t01_sugup_date, 7, 8) = '15' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_15
				,      sum(case when substring(t01_sugup_date, 7, 8) = '16' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_16
				,      sum(case when substring(t01_sugup_date, 7, 8) = '17' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_17
				,      sum(case when substring(t01_sugup_date, 7, 8) = '18' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_18
				,      sum(case when substring(t01_sugup_date, 7, 8) = '19' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_19
				,      sum(case when substring(t01_sugup_date, 7, 8) = '20' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_20
				,      sum(case when substring(t01_sugup_date, 7, 8) = '21' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_21
				,      sum(case when substring(t01_sugup_date, 7, 8) = '22' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_22
				,      sum(case when substring(t01_sugup_date, 7, 8) = '23' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_23
				,      sum(case when substring(t01_sugup_date, 7, 8) = '24' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_24
				,      sum(case when substring(t01_sugup_date, 7, 8) = '25' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_25
				,      sum(case when substring(t01_sugup_date, 7, 8) = '26' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_26
				,      sum(case when substring(t01_sugup_date, 7, 8) = '27' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_27
				,      sum(case when substring(t01_sugup_date, 7, 8) = '28' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_28
				,      sum(case when substring(t01_sugup_date, 7, 8) = '29' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_29
				,      sum(case when substring(t01_sugup_date, 7, 8) = '30' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_30
				,      sum(case when substring(t01_sugup_date, 7, 8) = '31' and cast(t01_svc_subcode as signed) > 30 and cast(t01_svc_subcode as signed) < 40 and t01_status_gbn != 'C' then 1 else 0 end) as day_other_31

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
				 where t01_ccode  = '$code'
				   and t01_del_yn = 'N'
				   and left(t01_sugup_date,6) = '$year$month'";

		$conn->query($sql);
		$data = $conn->fetch();
		$conn->row_free();

		$cnt['200'] = 0;
		$cnt['500'] = 0;
		$cnt['800'] = 0;
		$cnt['vou'] = 0;
		$cnt['oth'] = 0;
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
					if ($msg == ''){
						$font_color = '';
					}else{
						$font_color = 'color:#cccccc;';
					}
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
				if ($data['day_voucher_'.$temp_day] > 0){
					$content .= '바우처 : '.$data['day_voucher_'.$temp_day].'건<br>';
					$cnt['vou'] ++;
				}
				if ($data['day_other_'.$temp_day] > 0){
					$content .= '기타 : '.$data['day_other_'.$temp_day].'건<br>';
					$cnt['oth'] ++;
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
									<th class="no_border top right <?=$class_day;?>"><div class="center"><?=$day;?></div></th>
									<td class="no_border top left">
										<a href="#" onclick="lfDetail('<?=$temp_day;?>');" style="<?=$font_color;?>"><?=$content;?></a>
										<!-- <a href="#" onclick="month_detail('<?=$temp_day;?>');" style="<?=$font_color;?>"><?=$content;?></a> -->
									</td>
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
				<span>요양 : <?=$cnt['200'];?>건 / 목욕 : <?=$cnt['500'];?>건 / 간호 : <?=$cnt['800'];?>건 / 바우처 : <?=$cnt['vou'];?>건 / 기타유료 : <?=$cnt['oth'];?>건 / <font color="#ff0000">에러 : <?=$cnt['000'];?>건</font></span>
			</td>
		</tr>
	</tbody>
</table>
<div id="file_show" style="position:absolute; top:0; left:0; margin:10px; border:3px solid #cccccc; background-color:#ffffff; display:none;">
	<div>
		<div style="width:auto; float:left; padding:5px; font-weight:bold;" id="curr_day">입력할 <font style="color:#ff0000;">CSV파일</font>을 선택하여 주십시오.</div>
		<div style="width:auto; float:right; padding:5px; cursor:pointer;" onclick="document.getElementById('file_show').style.display='none';">X</div>
	</div>
	<div style="width:auto; float:left; padding:5px; font-weight:bold;">CSV 파일명</div>
	<div style="width:auto; float:left; padding:5px;"><input name="csv" type="file" style="width:270px; background-color:#ffffff;"></div>
	<div style="width:auto; float:left; padding:5px;"><span class="btn_pack m"><button type="button" onclick="set_csv();">확인</button></span></div>
</div>

<input type="hidden" name="code" value="<?=$code;?>">
<input type="hidden" name="kind" value="<?=$kind;?>">
<input type="hidden" name="day"  value="<?=$day;?>">
<input type="hidden" name="mode" value="<?=$mode;?>">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>