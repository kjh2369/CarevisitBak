<?
	include_once('../inc/_header.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$day   = $_POST['day'];
	$limit = $_GET['limit'];
	$today = date('d', mktime());

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

	$init_year = $myF->year();
?>
<form name="f" method="post">

<script language='javascript'>
<!--
var f = document.f;

function set_month(month, limit){
	var month = (parseInt(month, 10) < 10 ? '0' : '')+parseInt(month, 10);

	f.month.value = month;
	f.action = 'client_iljung.php?limit='+limit;
	f.submit();
}

function set_detail(jumin, day){
	var body = document.getElementById('layer_body');
	var list = document.getElementById('layer_list');

	var tbl = document.getElementById('my_table');
	var t = __getObjectTop(tbl) - 2;
	var l = __getObjectLeft(tbl) - 1;
	var w = tbl.offsetWidth + 1;
	var h = document.body.offsetHeight - t - 150;

	var obj = document.getElementById('day');

	/*
	list.style.left   = (document.body.offsetWidth - 500) / 2;
	list.style.top    = (document.body.offsetHeight - 70) / 2;
	list.style.width  = 500;
	list.style.height = 70;
	list.innerHTML    = '<div style=\'width:100%; height:100%; text-align:center; font-size:13pt; font-weight:bold; background-color:#ffffff; border:2px solid #cccccc; padding-top:20px;\'>데이타를 읽는중입니다. 잠시 기다려 주십시오.</div>';
	*/
	var URL = 'client_iljung_detail.php';
	var param = {'code':f.code.value,'jumin':jumin,'year':f.year.value,'month':f.month.value,'day':day};
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

-->
</script>

<div class="title title_border">수급내역(수급자)</div>

<table id="my_table" class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="95px">
		<col width="445px">
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

					$link = '<a href="#" style="'.$color.'" onclick="set_month('.$i.', 1);">'.$i.'월</a>';

					if ($i == 12){
						$style = 'float:left;';
					}else{
						$style = 'float:left; margin-right:2px;';
					}?>
					<div class="<?=$class;?>" style="<?=$style;?>"><?=$link;?></div><?
				}
			?>
			</td>
			<td class="last">
				<select name="day" style="width:auto;" onchange="set_month(<?=$month;?>, 2);">
				<?
					for($i=1; $i<=$lastday; $i++){
						echo '<option value=\''.$i.'\' '.($limit_day ? ($i > $today ? 'disabled=\'true\'' : '') : '').' '.($i == $day ? 'selected' : '').'>'.$i.'</option>';
					}
				?>
				</select>
				일 까지
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="45px">
		<col width="70px">
		<col width="50px">
		<col width="150px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head"><u title="수급자명을 클릭하시면 상세내역을 보실수 있습니다.">수급자</u></th>
			<th class="head">등급</th>
			<th class="head">서비스</th>
			<th class="head">계획</th>
			<th class="head">실적</th>
			<th class="head">차이</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select m03_name as nm
				,      t01_jumin as ssn
				,      LVL.m81_name as lvl_name
				,      t01_svc_subcode as svc_cd
				,      sum(case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_sugup_soyotime end) as plan_time
				,      sum(case when t01_status_gbn = '1' then case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_conf_soyotime end else 0 end) as conf_time
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
				 group by m03_name, t01_jumin, LVL.m81_name, t01_svc_subcode
				 order by m03_name, t01_mkind, t01_svc_subcode";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($tmp_ssn != $row['ssn']){
				if (!empty($tmp_ssn)){
					$tmp_seq ++;
					$html[$tmp_i][0] = '<td rowspan=\''.$tmp_rows.'\'><div class=\'center\'>'.($tmp_seq).'</div></td>';
					$html[$tmp_i][1] = '<td rowspan=\''.$tmp_rows.'\'><div class=\'left\'><a href=\'#\' onclick=\'set_detail("'.$ed->en($tmp_ssn).'",document.getElementById("day").value);\'>'.$tmp_nm.'</a></div></td>';
					$html[$tmp_i][2] = '<td rowspan=\''.$tmp_rows.'\'><div class=\'center\'>'.$tmp_lvl.'</div></td>';
				}

				$tmp_i    = $i;
				$tmp_ssn  = $row['ssn'];
				$tmp_nm   = $row['nm'];
				$tmp_lvl  = $row['lvl_name'];
				$tmp_rows = 1;
			}else{
				$tmp_rows ++;

				$html[$i][0] = '';
				$html[$i][1] = '';
				$html[$i][2] = '';
			}

			$html[$i][3] = '<td><div class=\'left\'>'.$conn->kind_name_svc($row['svc_cd']).'</div></td>';

			if ($row['svc_cd'] == '500' || $row['svc_cd'] == '800'){
				$html[$i][4] = '<td><div class=\'right\'>'.$myF->numberFormat($row['plan_time'],'회').'</div></td>';
				$html[$i][5] = '<td><div class=\'right\'>'.$myF->numberFormat($row['conf_time'],'회').'</div></td>';
				$html[$i][6] = '<td><div class=\'right\' style=\''.($row['conf_time']-$row['plan_time'] < 0 ? 'color:#ff0000;' : '').'\'>'.$myF->numberFormat($row['conf_time']-$row['plan_time'],'회').'</div></td>';
			}else{
				$html[$i][4] = '<td><div class=\'right\'>'.$myF->getMinToHM($row['plan_time']).'</div></td>';
				$html[$i][5] = '<td><div class=\'right\'>'.$myF->getMinToHM($row['conf_time']).'</div></td>';
				$html[$i][6] = '<td><div class=\'right\' style=\''.($row['conf_time']-$row['plan_time'] < 0 ? 'color:#ff0000;' : '').'\'>'.$myF->getMinToHM($row['conf_time']-$row['plan_time']).'</div></td>';
			}

			$html[$i][7] = '<td class=\'last\'><div class=\'center\'></div></td>';
		}

		$html[$tmp_i][0] = '<td rowspan=\''.$tmp_rows.'\'><div class=\'center\'>'.($tmp_seq+1).'</div></td>';
		$html[$tmp_i][1] = '<td rowspan=\''.$tmp_rows.'\'><div class=\'left\'><a href=\'#\' onclick=\'set_detail("'.$ed->en($tmp_ssn).'",document.getElementById("day").value);\'>'.$tmp_nm.'</a></div></td>';
		$html[$tmp_i][2] = '<td rowspan=\''.$tmp_rows.'\'><div class=\'center\'>'.$row['lvl_name'].'</div></td>';

		$conn->row_free();

		$html_cnt = sizeof($html);

		for($i=0; $i<$html_cnt; $i++){
			echo '<tr>';

			if (!empty($html[$i][0])) echo $html[$i][0];
			if (!empty($html[$i][1])) echo $html[$i][1];
			if (!empty($html[$i][2])) echo $html[$i][2];

			echo $html[$i][3];
			echo $html[$i][4];
			echo $html[$i][5];
			echo $html[$i][6];
			echo $html[$i][7];

			echo '</tr>';
		}
	?>
	</tbody>
	<tfoot>
		<tr>
		<?
			if (empty($tmp_seq)){
				echo '<td class=\'center last\' colspan=\'8\'>'.$myF->message('nodata','N').'</td>';
			}else{
				echo '<td class=\'left bottom last\' colspan=\'8\'>수급자 : '.$tmp_seq.'명 / 서비스 : '.$html_cnt.'건</td>';
			}
		?>
		</tr>
	</tfoot>
</table>

<input name="code" type="hidden" value="<?=$code;?>">
<input name="month" type="hidden" value="<?=$month;?>">

<div id="layer_body" style="z-index:0; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:#ffffff; position:absolute; top:0px; left:0px; height:0px; width:0px;"></div>
<div id="layer_list" style="z-index:1; left:0; top:0; position:absolute; color:#000000; text-align:left;"></div>

</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>