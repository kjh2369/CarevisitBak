<?
	include_once('../inc/_header.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_body_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/jw_myFun.php');
	include_once('../inc/_ed.php');

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code  = $_SESSION['userCenterCode'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$day   = $_POST['day'];
	$limit = $_GET['limit'];
	$gubun = $_GET['gubun'];
	$today = date('d', mktime());
	$yymm  = date('Y-m', mktime());
	$find_yoy_name		= $_REQUEST['find_yoy_name'];
	$find_dept          = $_REQUEST['find_dept'];
	$find_su_name		= $_REQUEST['find_su_name'];
	$find_center_kind		= $_REQUEST['find_center_kind'];

	switch($gubun){
		case 'client':
			$title = '수급자';
			break;
		case 'member':
			$title = '직원';
			break;
		default:
			include_once("../inc/_http_home.php");
	}

	if ($limit == 1) $day = null;

	if (empty($year))  $year  = date('Y', mktime());
	if (empty($month)) $month = date('m', mktime());

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

	if ($_SESSION['userStmar'] == 'Y'){
		$member = $_SESSION['userSSN'];
	}else{
		$member = 'all';
	}

	$init_year = $myF->year();
?>
<form name="f" method="post">

<script language='javascript'>
<!--
var f = document.f;

function set_month(month, limit){
	if (month > 0){
		f.month.value = (parseInt(month, 10) < 10 ? '0' : '')+parseInt(month, 10);
	}

	if (limit == '1'){
		if (f.year.value + '-' + f.month.value == '<?=$yymm;?>'){
			var day = <?=$today;?>;
		}else{
			var day = 31;
		}
	}else{
		var day = f.day.value;
	}

	set_list(day);
}

function set_list(day){
	var load  = document.getElementById('layer_load');

	show_loading(load);

	var URL   = 'iljung_result_list.php';
	var param = {'code':f.code.value,'year':f.year.value,'month':f.month.value,'day':day,'limit':'<?=$limit;?>','gubun':'<?=$gubun;?>', 'find_name':f.find_name.value, 'find_type':f.find_type.value};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var result = document.getElementById('result');

				result.innerHTML = responseHttpObj.responseText;

				for(var i=1; i<=12; i++){
					var mon = (i < 10 ? '0' : '')+i;
					var obj = document.getElementById('m_'+mon);

					if (mon == f.month.value){
						obj.className = 'my_month my_month_y';
					}else{
						obj.className = 'my_month my_month_1';
					}
				}

				var select_i = 0;

				if (f.year.value + '-' + f.month.value == '<?=$yymm;?>'){
					for(var i=0; i<f.day.length; i++){
						if (f.day.options[i].value > <?=$today;?>){
							f.day.options[i].disabled = true;
						}else{
							f.day.options[i].disabled = false;
							select_i = i;
						}
					}
				}else if (f.year.value + '-' + f.month.value > '<?=$yymm;?>'){
					for(var i=0; i<f.day.length; i++){
						f.day.options[i].disabled = true;
						select_i = i;
					}
				}else{
					for(var i=0; i<f.day.length; i++){
						f.day.options[i].disabled = false;
						select_i = i;
					}
				}

				select_i = parseInt(day, 10) - 1;

				f.day.options[select_i].selected = true;

				load.innerHTML = '';
			}
		}
	);
}

function set_detail(jumin, day){
	var body = document.getElementById('layer_body');
	var list = document.getElementById('layer_list');
	var load = document.getElementById('layer_load');
	var tbl  = document.getElementById('my_table');
	var t = __getObjectTop(tbl) - 2;
	var l = __getObjectLeft(tbl) - 1;
	var w = tbl.offsetWidth + 1;
	var h = document.body.offsetHeight - t - 150;

	var obj = document.getElementById('day');

	show_loading(load);

	var URL = 'iljung_result_detail.php';
	var param = {'code':f.code.value,'jumin':jumin,'year':f.year.value,'month':f.month.value,'day':day,'gubun':'<?=$gubun;?>'};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				list.style.top    = t;
				list.style.left   = l;
				list.style.width  = w;
				list.style.height = h;
				list.innerHTML = '<div style=\'width:100%; height:100%; background-color:#ffffff; border:2px solid #0e69b0;\'>'+responseHttpObj.responseText+'</div>';
				load.innerHTML = '';

				var scroll = document.getElementById('scroll_body');

				scroll.style.height = h;

				__init_form(f);
			}
		}
	);
}

function close_detail(){
	var body = document.getElementById('layer_body');
	var list = document.getElementById('layer_list');

	list.innerHTML = '';

	list.style.width  = 0;
	list.style.height = 0;

	body.style.width  = 0;
	body.style.height = 0;
}

function show_loading(load){
	load.style.left   = (document.body.offsetWidth - 500) / 2;
	load.style.top    = (document.body.offsetHeight - 70) / 2;
	load.style.width  = 500;
	load.style.height = 70;
	load.innerHTML    = '<div style=\'width:100%; height:100%; text-align:center; font-size:13pt; font-weight:bold; background-color:#ffffff; border:2px solid #cccccc; padding-top:20px;\'>데이타를 읽는중입니다. 잠시 기다려 주십시오.</div>';
}

function excel(jumin, day){
	var f = document.f;

	f.jumin.value = jumin;
	f.day.value = day;
	f.gubun.value = '<?=$gubun?>';

	f.action = 'iljung_result_excel.php';
	f.submit();
}

window.onload = function(){
	//set_list(<?=$today;?>);
}

-->
</script>

<div class="title title_border">수급내역(<?=$title;?>)</div>

<table id="my_table" class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col width="445px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년도</th>
			<td class="last">
				<select name="year" style="width:auto;"><?
				for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
					<option value="<?=$i;?>" <? if($i == $year){echo 'selected';} ?>><?=$i;?></option><?
				}?>
				</select>년
			</td>
			<td class="last" style="padding-top:1px;">
			<?
				for($i=1; $i<=12; $i++){
					$class = 'my_month ';

					if ($i == intval($month)){
						$class .= 'my_month_y ';
						$color  = 'color:#000000;';
					}else{
						$class .= 'my_month_1 ';
						$color  = 'color:#000000;';
					}

					$link = '<a href="#" style="'.$color.'" onclick="set_month('.$i.', 1);">'.$i.'월</a>';

					if ($i == 12){
						$style = 'float:left;';
					}else{
						$style = 'float:left; margin-right:2px;';
					}?>
					<div id="m_<?=($i < 10 ? '0' : '').$i;?>" class="<?=$class;?>" style="<?=$style;?>"><?=$link;?></div><?
				}
			?>
			</td>
			<td class="last">
				<select name="day" style="width:auto;" onchange="set_month(0,2);">
				<?
					for($i=1; $i<=31; $i++){
						echo '<option value=\''.$i.'\' '.($limit_day ? ($i > $today ? 'disabled=\'true\'' : '') : '').' '.($i == $day ? 'selected' : '').'>'.$i.'</option>';
					}
				?>
				</select>
				일 까지
			</td>
		</tr>
	</tbody>
</table>
<?
	/*------------------------------------------*/
	# 찾기 직원(직원명,부서) 고객(수급자명,서비스 찾기)
	/*------------------------------------------*/
	echo _find_person($conn, $code, $find_name, $find_type, $gubun, 'set_month(0, 2)');
?>
<div id="result"></div>

<input name="code" type="hidden" value="<?=$code;?>">
<input name="month" type="hidden" value="<?=$month;?>">

<div id="layer_body" style="z-index:0; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:#ffffff; position:absolute; top:0px; left:0px; height:0px; width:0px;"></div>
<div id="layer_list" style="z-index:1; left:0; top:0; position:absolute; color:#000000; text-align:left;"></div>
<div id="layer_load" style="z-index:1; left:0; top:0; position:absolute; color:#000000; text-align:left;"></div>

</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>