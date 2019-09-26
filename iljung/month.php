<?
	include_once('../inc/_header.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$kind  = '0';
	$year  = $_POST['year'];
	$month = $_POST['month'];

	if (empty($year)) $year = date('Y', mktime());
	if (empty($month)) $month = date('m', mktime());

	if ($year.$month == date('Ym', mktime())){
		$from_time = date('dHi', mktime());
	}else{
		$from_time = '010000';
	}

	$to_time = '999999';

	$init_year = $myF->year();
?>
<form name="f" method="post">

<script language='javascript'>
<!--
var f = document.f;

function set_month(month){
	var month = (parseInt(month, 10) < 10 ? '0' : '')+parseInt(month, 10);

	f.month.value = month;
	f.submit();
}

function set_detail(jumin){
	var body = document.getElementById('layer_body');
	var list = document.getElementById('layer_list');

	var tbl = document.getElementById('my_table');
	var t = __getObjectTop(tbl) - 2;
	var l = __getObjectLeft(tbl) - 1;
	var w = tbl.offsetWidth + 1;
	var h = document.body.offsetHeight - t - 50;

	var obj = document.getElementById('day');

	if (obj != null)
		var day = obj.value;
	else
		var day = '';

	/*
	body.style.left   = 0;
	body.style.top    = 0;
	body.style.width  = document.body.offsetWidth;
	body.style.height = document.body.offsetHeight;
	*/

	list.style.left   = (document.body.offsetWidth - 500) / 2;
	list.style.top    = (document.body.offsetHeight - 70) / 2;
	list.style.width  = 500;
	list.style.height = 70;
	list.innerHTML    = '<div style=\'width:100%; height:100%; text-align:center; font-size:13pt; font-weight:bold; background-color:#ffffff; border:2px solid #cccccc; padding-top:20px;\'>데이타를 읽는중입니다. 잠시 기다려 주십시오.</div>';

	var URL = 'month_detail.php';
	var param = {'code':f.code.value,'kind':f.kind.value,'jumin':jumin,'year':f.year.value,'month':f.month.value,'day':day};
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

				var scroll = document.getElementById('scroll_body');

				scroll.style.height = h - 80;

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

-->
</script>

<div class="title title_border">수급내역(수급자)</div>

<table id="my_table" class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="95px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년도</th>
			<td class="last">
				<select name="year" style="width:auto;"><?
				for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
					<option value="<?=$i;?>" <? if($i == $year){echo 'selected';} ?>><?=$i;?>년</option><?
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
						$color  = 'color:#666666;';
					}

					$link = '<a href="#" style="'.$color.'" onclick="set_month('.$i.');">'.$i.'월</a>';

					if ($i == 12){
						$style = 'float:left;';
					}else{
						$style = 'float:left; margin-right:2px;';
					}?>
					<div class="<?=$class;?>" style="<?=$style;?>"><?=$link;?></div><?
				}
			?>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="45px">
		<col width="70px">
		<col width="50px">
		<col width="80px">
		<col width="60px" span="2">
		<col width="80px">
		<col width="60px" span="2">
		<col width="80px">
		<col width="60px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">수급자</th>
			<th class="head" rowspan="2">등급</th>
			<th class="head" colspan="3">계획</th>
			<th class="head" colspan="3">실적</th>
			<th class="head" colspan="3">차이</th>
			<th class="head last" rowspan="2">비고</th>
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
			<th class="head">간호</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select m03_name as nm
				,      t01_jumin as ssn
				,      LVL.m81_name as lvl_name
				,      sum(case when t01_svc_subcode = '200' then t01_sugup_soyotime else 0 end) as plan200
				,      sum(case when t01_svc_subcode = '500' then 1 else 0 end) as plan500
				,      sum(case when t01_svc_subcode = '800' then 1 else 0 end) as plan800
				,      sum(case when t01_svc_subcode = '200' and t01_status_gbn = '1' then t01_conf_soyotime else 0 end) as conf200
				,      sum(case when t01_svc_subcode = '500' and t01_status_gbn = '1' then 1 else 0 end) as conf500
				,      sum(case when t01_svc_subcode = '800' and t01_status_gbn = '1' then 1 else 0 end) as conf800
				  from t01iljung
				 inner join m03sugupja
					on m03_ccode = t01_ccode
				   and m03_mkind = t01_mkind
				   and m03_jumin = t01_jumin
				  left join m81gubun as LVL
					on LVL.m81_gbn  = 'LVL'
				   and LVL.m81_code = case when m03_mkind = '0' or m03_mkind = '4' then m03_ylvl else '' end
				 where t01_ccode    = '$code'
				   and concat(t01_sugup_date, t01_sugup_fmtime) between '$year$month$from_time' and '$year$month$to_time'
				   and t01_del_yn   = 'N'
				 group by m03_name, t01_jumin, LVL.m81_name
				 order by m03_name";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			echo '<tr>';
			echo '<td><div class=\'center\'>'.($i+1).'</div></td>';
			echo '<td><div class=\'left\'><a href=\'#\' onclick=\'set_detail("'.$ed->en($row['ssn']).'");\'>'.$row['nm'].'</a></div></td>';
			echo '<td><div class=\'center\'>'.$row['lvl_name'].'</div></td>';
			echo '<td><div class=\'center\'>'.$myF->getMinToHM($row['plan200']).'</div></td>';
			echo '<td><div class=\'center\'>'.$myF->numberFormat($row['plan500'],'회').'</div></td>';
			echo '<td><div class=\'center\'>'.$myF->numberFormat($row['plan800'],'회').'</div></td>';
			echo '<td><div class=\'center\'>'.$myF->getMinToHM($row['conf200']).'</div></td>';
			echo '<td><div class=\'center\'>'.$myF->numberFormat($row['conf500'],'회').'</div></td>';
			echo '<td><div class=\'center\'>'.$myF->numberFormat($row['conf800'],'회').'</div></td>';
			echo '<td><div class=\'center\' style=\''.($row['conf200']-$row['plan200'] < 0 ? 'color:#ff0000;' : '').'\'>'.$myF->getMinToHM($row['conf200']-$row['plan200']).'</div></td>';
			echo '<td><div class=\'center\' style=\''.($row['conf500']-$row['plan500'] < 0 ? 'color:#ff0000;' : '').'\'>'.$myF->numberFormat($row['conf500']-$row['plan500'],'회').'</div></td>';
			echo '<td><div class=\'center\' style=\''.($row['conf800']-$row['plan800'] < 0 ? 'color:#ff0000;' : '').'\'>'.$myF->numberFormat($row['conf800']-$row['plan800'],'회').'</div></td>';
			echo '<td class=\'last\'><div class=\'center\'></div></td>';
			echo '</tr>';
		}

		$conn->row_free();
	?>
	</tbody>
</table>

<input name="code" type="hidden" value="<?=$code;?>">
<input name="kind" type="hidden" value="<?=$kind;?>">
<input name="month" type="hidden" value="<?=$month;?>">

<div id="layer_body" style="z-index:0; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:#ffffff; position:absolute; top:0px; left:0px; height:0px; width:0px;"></div>
<div id="layer_list" style="z-index:1; left:0; top:0; position:absolute; color:#000000; text-align:left;"></div>

</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>