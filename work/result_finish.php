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

	$init_year = $myF->year();
	$init_year[1] ++;
	$init_date = explode('-', $myF->dateAdd('month', -1, date('Y-m-d', mktime()), 'Y-m-d'));

	/*
	 * mode
	 * 1 : 실적마감
	 * 2 : 급여마감
	 */

	$code	= $_SESSION['userCenterCode'];
	$year	= $_REQUEST['year']  != '' ? $_REQUEST['year']  : intval($init_date[0]);
	$month	= $_REQUEST['month'] != '' ? $_REQUEST['month'] : intval($init_date[1]);
	$mode	= $_REQUEST['mode'];

	$user_code = $_SESSION['userCode'];
	$today     = date('Y-m-d', mktime());

	// 마감테이블 초기화
	$sql = "select cast(right(closing_yymm, 2) as unsigned) as mm
			  from closing_progress
			 where org_no          = '$code'
			   and closing_yymm like '$year%'";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	if ($row_count < 12){
		$conn->auto_commit_unset();

		for($i=1; $i<=12; $i++){
			$row_yn = 'N';

			for($j=0; $j<$row_count; $j++){
				$row = $conn->select_row($j);

				if ($i == $row['mm']){
					$row_yn = 'Y';
					break;
				}
			}

			if ($row_yn == 'N'){
				$yymm = $year.($i<10?'0':'').$i;
				$sql = "insert into closing_progress (org_no,closing_yymm,create_id,create_dt) values (
						 '$code'
						,'$yymm'
						,'$user_code'
						,'$today')";
				$conn->execute($sql);
			}
		}
	}

	$conn->row_free();

	if ($mode == 1){
		$title = '실적마감처리';
		$caption = '실적 마감 일자 등록';
	}else{
		$title = '급여마감처리';
		$caption = '급여 마감 일자 등록';
	}

	$sql = "select ifnull(max(cast(substring(closing_yymm, 5, 2) as signed)), 1)
			  from closing_progress
			 where org_no          = '$code'
			   and closing_yymm like '$year%'
			   and del_flag        = 'N'";
	$max_closing_ym = $conn->get_data($sql);
	$max_closing_ym ++;

	$sql = "select closing_yymm
			,      act_cls_flag
			,      act_cls_dt_from
			,      act_cls_ent_dt
			,      act_bat_conf_flag
			,      salary_cls_flag
			,      salary_cls_dt_from
			,      salary_cls_ent_dt
			,      salary_bat_calc_flag
			,      salary_bat_calc_dt
			,      salary_bat_can_flag
			  from closing_progress
			 where org_no          = '$code'
			   and closing_yymm like '$year%'
			   and del_flag        = 'N'";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=1; $i<=12; $i++){
		$data[$i]['closing'] = 'N';
		$data[$i]['act_dt']  = '';
		$data[$i]['act_yn']  = 'N';
		$data[$i]['reg_dt']  = '';
		$data[$i]['pay_dt']  = '';
		$data[$i]['pay_reg'] = '';
		$data[$i]['calc_yn'] = '';
		$data[$i]['calc_dt'] = '';
		$data[$i]['calc_can']= 'N';

		$data[$i]['act_cls_yn']    = 'N';
		$data[$i]['salary_cls_yn'] = 'N';
	}

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$index = intval(substr($row['closing_yymm'], 4, 2));

			$data[$index]['closing'] = 'Y';
			$data[$index]['act_dt']  = $row['act_cls_dt_from'];
			$data[$index]['act_yn']  = $row['act_bat_conf_flag'];
			$data[$index]['reg_dt']  = $row['act_cls_ent_dt'];
			$data[$index]['pay_dt']  = $row['salary_cls_dt_from'];
			$data[$index]['pay_reg'] = $row['salary_cls_ent_dt'];
			$data[$index]['calc_yn'] = $row['salary_bat_calc_flag'];
			$data[$index]['calc_dt'] = $row['salary_bat_calc_dt'];
			$data[$index]['calc_can']= $row['salary_bat_can_flag'];

			$data[$index]['act_cls_yn']    = $row['act_cls_flag'];
			$data[$index]['salary_cls_yn'] = $row['salary_cls_flag'];
		}

		if ($mode == 1){
		}else{
			$data[$month]['closing'] = $data[$month]['calc_yn'];
		}

		if ($data[$month]['closing'] == 'Y'){
			$reg_date  = $data[$month]['reg_dt'];
			$temp_date_1 = explode('-', $data[$month]['act_dt']);

			if (strlen($data[$month]['pay_dt']) == 10){
				$temp_date_2 = explode('-', $data[$month]['pay_dt']);
			}else{
				$temp_date_2 = explode('-', date('Y-m-d', mktime()));
			}
		}
	}

	if ($data[$month]['closing'] != 'Y'){
		$reg_date  = date('Y-m-d', mktime());
		$temp_date = $myF->dateAdd('month', 1, $year.'-'.$month.'-01', 'Y-m-d');

		if ($temp_date < $today){
			$temp_date = $today;
		}

		$temp_date_1 = explode('-', $temp_date);
		$temp_date_2 = explode('-', $temp_date);
	}

	$w_y = intval($temp_date_1[0]);
	$w_m = intval($temp_date_1[1]);
	$w_d = intval($temp_date_1[2]);

	$p_y = intval($temp_date_2[0]);
	$p_m = intval($temp_date_2[1]);
	$p_d = intval($temp_date_2[2]);

	$conn->row_free();
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function search(){
	var f = document.f;

	f.action = 'result_finish.php';
	f.submit();
}

function set_month(month){
	var f = document.f;

	f.month.value = month;

	search();
}

function set_lastday(target){
	var y = document.getElementById(target+'_y');
	var m = document.getElementById(target+'_m');
	var d = document.getElementById(target+'_d');

	var date = y.value + '-' + m.value + '-01';
	var last = getDay(addDate('d', -1, addDate('m', 1, date)));

	for(var i=0; i<d.options.length; i++){
		if (parseInt(d.options[i].text, 10) <= last){
			d.options[i].disabled = false;
		}else{
			d.options[i].disabled = true;
		}
	}

	if (d.selectedIndex > parseInt(last, 10) - 1){
		d.options[parseInt(last, 10) - 1].selected = true;
	}
}

function save(gubun){
	var f    = document.f;
	var s_dt = addDate('d', -1, addDate('m', 1, f.year.value + '-' + f.month.value + '-01'));

	if (gubun == 1){
		var w_dt = f.w_y.value + '-' + f.w_m.value + '-' + f.w_d.value;

		if (s_dt > w_dt){
			alert('실적마감적용일자는 마감기준년월보다 커야 합니다. 확인하여 주십시오.');
			return;
		}
	}else{
		var c_dt = f.pay_dt.value;
		var p_dt = f.p_y.value + '-' + f.p_m.value + '-' + f.p_d.value;

		if (c_dt > p_dt){
			alert('급여마감적용일자는 급여일괄계산일('+c_dt+')보다 커야 합니다. 확인하여 주십시오.');
			return;
		}
	}

	f.action = 'result_finish_save.php?gubun='+gubun;
	f.submit();
}

function cls_execute(){
	var f    = document.f;
	var mode = f.mode.value;

	if (mode == 1){
		var msg = '실적마감을 처리하시겠습니까?';
	}else{
		var msg = '급여마감을 처리하시겠습니까?';
	}

	if (!confirm(msg)) return;

	f.action = 'result_finish_save.php?gubun='+mode;
	f.submit();
}

window.onload = function(){
	//set_lastday('<?=$mode == 1 ? "w" : "p";?>');
	//set_lastday('p');
}
-->
</script>

<div class="title title_border"><?=$title;?></div>

<form name="f" method="post">

<table style="margin:10px;">
	<colgroup>
		<col width="150px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="my_border_blue my_bold my_bg_blue my_left"><?=$caption;?></td>
		</tr>
		<tr>
			<td class="my_border_blue my_bold my_bg_blue my_left">마감 기준 년월</td>
			<td class="my_border_blue my_bold my_center">
				<select name="year" style="width:auto;" onchange="search();">
				<?
					for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
						<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?></option><?
					}
				?>
				</select>년
			</td>
			<td class="my_border_blue my_center">
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

					if ($i != intval($month)){
						$text   = '<a href="#" onclick="set_month('.$i.');">'.$i.'월</a>';
					}else{
						$text   = '<span style="'.$color.' cursor:default;">'.$i.'월</span>';
					}

					if ($i == 12){
						$style = 'float:left;';
					}else{
						$style = 'float:left; margin-right:5px;';
					}?>
					<div class="<?=$class;?>" style="<?=$style;?>"><?=$text;?></div><?
				}
			?>
			</td>
		</tr>
	</tbody>
</table>

<table style="width:100%; margin-left:10px; margin-top:20px;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="noborder my_center my_bold">
			<?
				if ($mode == 1){
					if ($data[$month]['act_cls_yn'] == 'Y'){
						$caption = '실적마감 취소';
					}else{
						$caption = '실적마감 처리';
					}
					$flag_yn = $data[$month]['act_cls_yn'];?>
					<span class="btn_pack m"><button type="button" class="my_bold" onclick="cls_execute();" <? if($data[$month]['act_yn'] == 'Y'){?>disabled="true"<?} ?>><?=$caption;?></button></span><?
				}else{
					if ($data[$month]['salary_cls_yn'] == 'Y'){
						$caption = '급여마감 취소';
					}else{
						$caption = '급여마감 처리';
					}
					$flag_yn = $data[$month]['salary_cls_yn'];?>
					<span class="btn_pack m"><button type="button" class="my_bold" onclick="cls_execute();" <? if ($data[$month]['calc_yn'] != 'Y'){?>disabled="true"<?} ?>><?=$caption;?></button></span><?
				}
			?>
			</td>
		</tr>
	</tbody>
</table>

<?
	if ($mode == 1){
		if ($data[$month]['act_yn'] == 'Y'){?>
			<div class="noborder my_left my_bold" style="margin-left:10px;">※ <?=$year;?>년 <?=$month;?>월은 <font color="ff0000">실적일괄확정</font>이 완료되어 <font color="ff0000">실적마감</font>을 취소할 수 없습니다.</div><?
		}
	}else{
		if ($data[$month]['calc_yn'] != 'Y'){?>
			<div class="noborder my_left my_bold" style="margin-left:10px;">※ <?=$year;?>년 <?=$month;?>월 급여마감을 하기 위해서 <font color="ff0000">급여일괄계산</font>이 선행되어야 합니다.</div><?
		}
	}

	include_once('result_status_sub.php');

	if ($mode == 1){?>
		<table style="width:90%; margin-left:10px; margin-top:20px; font-weight:bold;">
			<colgroup>
				<col width="15px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<td class="noborder" style="vertical-align:top; text-align:center;">※</td>
					<td class="noborder" style="vertical-align:top; text-align:left;">실적 마감 처리 후 수급자 수급실적(청구내역작성)을 일괄 확정 하여야 합니다.<br><br></td>
				</tr>
				<tr>
					<td class="noborder" style="vertical-align:top; text-align:center;">※</td>
					<td class="noborder" style="vertical-align:top; text-align:left;">실적 마감 처리 후 <font color="#ff0000">실적등록,수정,취소</font>를 할 수 없습니다.<br><br></td>
				</tr>
				<tr>
					<td class="noborder" style="vertical-align:top; text-align:center;">※</td>
					<td class="noborder" style="vertical-align:top; text-align:left;">마감 후 실적등록,수정,취소를 하고자 하는 경우에는 마감적용일자를 현재일자보다 크게 변경 하여야 합니다.(실적일괄확정, 급여계산을 한 후 실적 수정 또는 취소를 하고자 할 경우에는 실적확정취소 또는 급여계산취소를 한 후 마감적용일자변경 가능)<br><br></td>
				</tr>
				<tr>
					<td class="noborder" style="vertical-align:top; text-align:center;">※</td>
					<td class="noborder" style="vertical-align:top; text-align:left;">수급자 실적확정(공단청구확정) 및 요양보호사 급여 계산 후 마감적용일자를 변경하게 되면 실적 등록, 수정, 취소가 가능하게 되어 이전에 계산된 청구 금액 및 급여금액이 서로 다를 수 있으니 주의하시기 바랍니다.(재 확정 필요)<br><br></td>
				</tr>
			</tbody>
		</table><?
	}else{?>
		<table style="width:90%; margin-left:10px; margin-top:20px; font-weight:bold;">
			<colgroup>
				<col width="15px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<td class="noborder" style="vertical-align:top; text-align:center;">※</td>
					<td class="noborder" style="vertical-align:top; text-align:left;">급여 마감 처리 후 마감적용일부터 <font color="#ff0000">급여조정</font>을 할 수 없습니다.<br><br></td>
				</tr>
			</tbody>
		</table><?
	}
?>

<input type="hidden" name="mode"	 value="<?=$mode;?>">
<input type="hidden" name="flag_yn"	 value="<?=$flag_yn;?>">
<input type="hidden" name="code"	 value="<?=$code;?>">
<input type="hidden" name="month"	 value="<?=$month;?>">
<input type="hidden" name="min_year" value="<?=$init_year[0];?>">
<input type="hidden" name="max_year" value="<?=$init_year[1];?>">

<input type="hidden" name="pay_dt" value="<?=$data[$month]['calc_dt'];?>">

</form>
<?
	unset($data);

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>