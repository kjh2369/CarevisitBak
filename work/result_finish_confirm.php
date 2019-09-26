<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_body_header.php");

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
		$title = '수급자실적마감(청구내역작성)';
	}else{
		$title = '급여일괄계산';
	}

	$sql = "select closing_yymm
			,      act_cls_flag
			,      act_cls_dt_from
			,      act_bat_conf_flag
			,      act_bat_conf_dt
			,      salary_cls_flag
			,      salary_cls_dt_from
			,      salary_bat_calc_flag
			,      salary_bat_calc_dt
			  from closing_progress
			 where org_no          = '$code'
			   and closing_yymm like '$year%'
			   and del_flag        = 'N'";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=1; $i<=12; $i++){
		$data[$i]['data_flag'] = 'N';
		$data[$i]['conf_flag'] = 'N';
		$data[$i]['conf_from'] = '';
		$data[$i]['conf_dt']   = '';
		$data[$i]['calc_flag'] = 'N';
		$data[$i]['calc_from'] = '';
		$data[$i]['calc_dt']   = '';

		$data[$i]['act_cls_yn']    = 'N';
		$data[$i]['salary_cls_yn'] = 'N';
	}

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$index = intval(substr($row['closing_yymm'], 4, 2));

			$data[$index]['data_flag'] = 'Y';
			$data[$index]['conf_flag'] = $row['act_bat_conf_flag'];
			$data[$index]['conf_from'] = $row['act_cls_dt_from'];
			$data[$index]['conf_dt']   = $row['act_bat_conf_dt'];
			$data[$index]['calc_flag'] = $row['salary_bat_calc_flag'];
			$data[$index]['calc_from'] = $row['salary_cls_dt_from'];
			$data[$index]['calc_dt']   = $row['salary_bat_calc_dt'];

			$data[$index]['act_cls_yn']    = $row['act_cls_flag'];
			$data[$index]['salary_cls_yn'] = $row['salary_cls_flag'];
		}

		$conf_date   = explode('-', $data[$month]['conf_dt']);
		$temp_date_1 = $conf_date;

		$calc_date   = explode('-', $data[$month]['calc_dt']);
		$temp_date_2 = $calc_date;
	}

	if ($month > $index) $month = $index;

	$temp_date = $myF->dateAdd('month', 1, $year.'-'.$month.'-01', 'Y-m-d');
	$today     = date('Y-m-d', mktime());

	if ($temp_date < $today){
		$temp_date = $today;
	}

	if (sizeof($temp_date_1) < 3) $temp_date_1 = explode('-', $temp_date);
	if (sizeof($temp_date_2) < 3) $temp_date_2 = explode('-', $temp_date);

	$conf_y = intval($temp_date_1[0]);
	$conf_m = intval($temp_date_1[1]);
	$conf_d = intval($temp_date_1[2]);

	$calc_y = intval($temp_date_2[0]);
	$calc_m = intval($temp_date_2[1]);
	$calc_d = intval($temp_date_2[2]);

	$conn->row_free();
?>
<script src="../js/work.js" type="text/javascript"></script>
<script src="../salaryNew/salary.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function search(){
	var f = document.f;

	f.action = '../work/result_finish_confirm.php';
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
		var w_dt = f.conf_y.value + '-' + f.conf_m.value + '-' + f.conf_d.value;
	}else{
		var p_dt = f.calc_y.value + '-' + f.calc_m.value + '-' + f.calc_d.value;
	}

	if (gubun == 1){

		if (s_dt > w_dt){
			alert('수급자 확정일자는 확정기준년월보다 커야 합니다. 확인하여 주십시오.');
			return;
		}
	}else{
		if (s_dt > p_dt){
			alert('요양보호사 급여 계산일자는 확정기준년월보다 커야 합니다. 확인하여 주십시오.');
			return;
		}
	}

	if (gubun == 1){
		if (f.conf_dt.value > w_dt){
			alert('수급자 확정일자는 실적마감일자보다 커야 합니다. 확인하여 주십시오.');
			return;
		}
	}else{
		if (f.calc_dt.value > p_dt){
			alert('요양보호사 급여 계산일자는 급여마감일자보다 커야 합니다. 확인하여 주십시오.');
			return;
		}
	}

	f.action = '../work/result_finish_confirm_save.php?gubun='+gubun;
	f.submit();
}

window.onload = function(){
	set_lastday('<?=$mode == 1 ? "conf" : "calc";?>');
	//set_lastday('calc');
}
-->
</script>

<div class="title title_border"><?=$title;?></div>

<form name="f" method="post">

<table style="margin:10px;">
	<colgroup>
		<col width="100px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="my_border_blue my_bold my_bg_blue my_left"><?=$mode == 1 ? '실적 일괄 확정' : '급여 일괄 계산';?></td>
		</tr>
		<tr>
			<td class="my_border_blue my_bold my_bg_blue my_left">확정 기준 년월</td>
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
						if ($data[$i]['data_flag'] == 'Y'){
							$class .= 'my_month_1 ';
							$color  = 'color:#666666;';
						}else{
							$class .= 'my_month_2 ';
							$color  = 'color:#c7c7c7;';
						}
					}

					if ($data[$i]['data_flag'] == 'Y'){
						$text   = '<a href="#" style="'.$color.'" onclick="set_month('.$i.');">'.$i.'월</a>';
					}else{
						$text   = '<span style="'.$color.'">'.$i.'월</span>';
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

<table style="margin-left:10px; margin-top:20px;">
	<colgroup>
		<col width="30px">
		<col width="150px">
		<col width="190px">
		<col>
	</colgroup>
	<tbody>
	<?
		if ($mode == 1){?>
			<tr>
				<td class="my_border_blue my_bold my_bg_blue my_center"><?=$data[$month]['conf_flag'] == 'Y' ? 'Y' : '-';?></td>
				<td class="my_border_blue my_bold my_bg_blue my_left">일괄확정 예약일자 저장</td>
				<td class="my_border_blue my_bold my_left">
					<select name="conf_y" style="width:auto;" onchange="set_lastday('conf');">
					<?
						for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
							<option value="<?=$i;?>" <? if($i == $conf_y){?>selected<?} ?>><?=$i;?></option><?
						}
					?>
					</select>년
					<select name="conf_m" style="width:auto;" onchange="set_lastday('conf');">
					<?
						for($i=1; $i<=12; $i++){?>
							<option value="<?=($i < 10 ? '0' : '').$i;?>" <? if($i == $conf_m){?>selected<?} ?>><?=$i;?></option><?
						}
					?>
					</select>월
					<select name="conf_d" style="width:auto;">
					<?
						for($i=1; $i<=31; $i++){?>
							<option value="<?=($i < 10 ? '0' : '').$i;?>" <? if($i == $conf_d){?>selected<?} ?>><?=$i;?></option><?
						}
					?>
					</select>일
				</td>
				<td class="my_border_blue my_center">
					<span class="btn_pack m"><button type="button" onclick="save(1);" style="font-weight:bold;" disabled="true">예약일자 저장</button></span>
				</td>
			</tr>
			<tr>
				<td class="my_border_blue my_center" colspan="4">
					<span class="btn_pack m"><button type="button" onclick="_work_confirm('<?=$code;?>','<?=$year;?>','<?=$month;?>','<?=$mode;?>');" style="font-weight:bold;" <? if($data[$month]['act_cls_yn'] == 'Y' && $data[$month]['conf_flag'] == 'Y'){?>disabled="true"<?} ?>>실적마감 실행</button></span>
				</td>
			</tr><?
		}else{?>
			<tr>
				<td class="my_border_blue my_bold my_bg_blue my_center"><?=$data[$month]['calc_flag'] == 'Y' ? 'Y' : '-';?></td>
				<td class="my_border_blue my_bold my_bg_blue my_left">급여계산 예약일자 저장</td>
				<td class="my_border_blue my_left my_bold">
					<select name="calc_y" style="width:auto;" onchange="set_lastday('calc');">
					<?
						for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
							<option value="<?=$i;?>" <? if($i == $calc_y){?>selected<?} ?>><?=$i;?></option><?
						}
					?>
					</select>년
					<select name="calc_m" style="width:auto;" onchange="set_lastday('calc');">
					<?
						for($i=1; $i<=12; $i++){?>
							<option value="<?=($i < 10 ? '0' : '').$i;?>" <? if($i == $calc_m){?>selected<?} ?>><?=$i;?></option><?
						}
					?>
					</select>월
					<select name="calc_d" style="width:auto;">
					<?
						for($i=1; $i<=31; $i++){?>
							<option value="<?=($i < 10 ? '0' : '').$i;?>" <? if($i == $calc_d){?>selected<?} ?>><?=$i;?></option><?
						}
					?>
					</select>일
				</td>
				<td class="my_border_blue my_center">
					<span class="btn_pack m"><button type="button" onclick="save(2);" style="font-weight:bold;" disabled="true">예약일자 저장</button></span>
				</td>
			</tr>
			<tr>
				<td class="my_border_blue my_center" colspan="4">
				<?
					if ($code == '1234'){
						?>
						<span class="btn_pack m"><button type="button" onclick="_work_confirm('<?=$code;?>','<?=$year;?>','<?=$month;?>','<?=$mode;?>');" style="font-weight:bold;" <? if($data[$month]['salary_cls_yn'] == 'Y' || $data[$month]['calc_flag'] == 'Y'){?>disabled="true"<?} ?>>급여일괄계산 실행</button></span><?
					}else{?>
						<span class="btn_pack m"><button type="button" onclick="_work_confirm('<?=$code;?>','<?=$year;?>','<?=$month;?>','<?=$mode;?>');" style="font-weight:bold;" <? if($data[$month]['salary_cls_yn'] == 'Y' || $data[$month]['calc_flag'] == 'Y'){?>disabled="true"<?} ?>>급여일괄계산 실행</button></span><?
					}
				?>
				</td>
			</tr><?
		}
	?>
	</tbody>
</table>

<?
	if ($mode == 1){
		if ($data[$month]['calc_flag'] == 'Y'){?>
			<div class="noborder my_left my_bold" style="margin-left:10px;">※ <?=$year;?>년 <?=$month;?>월은 <font color="ff0000">급여계산</font>이 완료되어 <font color="ff0000">실적마감을</font> 실행할 수 없습니다.</div><?
		}else if ($data[$month]['conf_flag'] == 'Y'){?>
			<div class="noborder my_left my_bold" style="margin-left:10px;">※ <?=$year;?>년 <?=$month;?>월은 <font color="ff0000">실적마감</font>이 이미 완료되었습니다.</div><?
		}
	}else{
		if ($data[$month]['calc_flag'] == 'Y'){?>
			<div class="noborder my_left my_bold" style="margin-left:10px;">※ <?=$year;?>년 <?=$month;?>월은 <font color="ff0000">급여계산</font>이 이미 완료되었습니다.</div><?
		}else if ($data[$month]['conf_flag'] == 'N'){?>
			<div class="noborder my_left my_bold" style="margin-left:10px;">※ <?=$year;?>년 <?=$month;?>월의 <font color="ff0000">실적마감</font>이 선행되어야합니다.</div>
			<div class="noborder my_left my_bold" style="margin-left:10px;"><span style="padding-left:17px;"><a href="#" onclick="__go_menu('work','result_finish_confirm.php?mode=1&year=<?=$year;?>&month=<?=$month;?>');" style="font-weight:bold;">[실적마감 바로가기]</a></span></div><?
		}
	}

	include_once('result_status_sub.php');
?>

<table style="width:100%; margin-left:10px; margin-top:20px; font-weight:bold;">
	<colgroup>
		<col width="15px">
		<col>
	</colgroup>
	<tbody>
	<?
		if ($mode == 1){?>
			<tr>
				<td class="noborder" style="vertical-align:top; text-align:center;">※</td>
				<td class="noborder" style="vertical-align:top; text-align:left;">수급자 실적, 요양보호사 실적에 대해 공단청구내역을 확정합니다.<br><br></td>
			</tr>
			<tr>
				<td class="noborder" style="vertical-align:top; text-align:center;">※</td>
				<td class="noborder" style="vertical-align:top; text-align:left;">수급자 실적에 대한 미처리 또는 오류 부분이 있을 경우에는 수급자 개인별 또는 일괄 취소를 하여 실적 수정 또는 실적 취소를 합니다.<br>(마감 적용일자 변경 후 수정 및 취소 가능)<br><br></td>
			</tr><?
		}else{?>
			<tr>
				<td class="noborder" style="vertical-align:top; text-align:center;">※</td>
				<td class="noborder" style="vertical-align:top; text-align:left;">요양보호사별 급여 내역을 일괄 계산합니다.<br><br></td>
			</tr>
			<tr>
				<td class="noborder" style="vertical-align:top; text-align:center;">※</td>
				<td class="noborder" style="vertical-align:top; text-align:left;">요양보호사 개인별 계산된 내역은 무시되고 전체 일괄계산합니다.<br><br></td>
			</tr><?
		}?>
	</tbody>
</table>

<input type="hidden" id="code" name="code"	 value="<?=$code;?>">
<input type="hidden" name="month"	 value="<?=$month;?>">
<input type="hidden" name="min_year" value="<?=$init_year[0];?>">
<input type="hidden" name="max_year" value="<?=$init_year[1];?>">

<input type="hidden" name="mode" value="<?=$mode;?>">

<input type="hidden" name="conf_dt" value="<?=$data[$month]['conf_from'];?>">
<input type="hidden" name="calc_dt" value="<?=$data[$month]['calc_from'];?>">

<input type="hidden" name="calc_flag" value="<?=$data[$month]['calc_flag'];?>">
<input type="hidden" name="conf_flag" value="<?=$data[$month]['conf_flag'];?>">

<input type="hidden" name="act_cls_yn"    value="<?=$data[$month]['act_cls_yn'];?>">
<input type="hidden" name="salary_cls_yn" value="<?=$data[$month]['salary_cls_yn'];?>">

</form>
<?
	unset($data);

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>