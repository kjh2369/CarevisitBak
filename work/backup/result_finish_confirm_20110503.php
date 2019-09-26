<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_body_header.php");

	$init_year = $myF->year();
	$init_year[1] ++;

	/*
	 * mode
	 * 1 : 실적마감
	 * 2 : 급여마감
	 */

	$code	= $_SESSION['userCenterCode'];
	$year	= $_REQUEST['year']  != '' ? $_REQUEST['year']  : intval(date('Y', mktime()));
	$month	= $_REQUEST['month'] != '' ? $_REQUEST['month'] : intval(date('m', mktime()));
	$mode	= $_REQUEST['mode'];

	if ($mode == 1){
		$title = '수급자실적일괄확정';
	}else{
		$title = '급여일괄계산';
	}

	$sql = "select closing_yymm
			,      act_cls_dt_from
			,      act_bat_conf_flag
			,      act_bat_conf_dt
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
		}

		$conf_date   = explode('-', $data[$month]['conf_dt']);
		$temp_date_1 = $conf_date;

		$calc_date   = explode('-', $data[$month]['calc_dt']);
		$temp_date_2 = $calc_date;
	}

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

<div style="width:120px; margin:10px 0 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold;"><?=$mode == 1 ? '실적 일괄 확정' : '급여 일괄 계산';?></div>
<div style="width:120px; margin:-2px -12px 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold; float:left;">확정 기준 년월</div>
<div style="width:90px; margin:-2px -10px 0 10px; padding:7px 5px 6px 5px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold; float:left;">
	<select name="year" style="width:auto;" onchange="search();">
	<?
		for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
			<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?></option><?
		}
	?>
	</select>년
</div>
<div style="margin:-2px 0 0 -10px; padding:5px; border:2px solid #cccccc; background-color:#fbfbfb;">
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
</div>
<?
	if ($mode == 1){?>
		<!--div style="width:346px; margin:10px 0 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold;">수급자 실적 일괄확정</div-->
		<div style="width:50px; margin:20px -12px 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold; float:left;"><?=$data[$month]['conf_flag'] == 'Y' ? 'Y' : '-';?></div>
		<div style="width:170px; margin:20px -12px 0 3px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold; float:left;">일괄확정 예약일자 저장</div>
		<div style="width:207px; margin:20px 30px 0 10px; padding:7px 5px 6px 5px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold; float:left;">
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
		</div>
		<div style="width:60px; margin:20px -5px 0 10px; padding:6px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold;">
			<span class="btn_pack m"><button type="button" onclick="save(1);" disabled="true">예약일자 저장</button></span>
		</div>

		<div style="text-align:left;">
			<div style="width:60px; margin:20px -5px 0 10px; padding:6px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold;">
				<span class="btn_pack m"><button type="button" onclick="_work_confirm('<?=$code;?>','<?=$year;?>','<?=$month;?>',1);" style="font-weight:bold;" <? if($data[$month]['conf_flag'] == 'Y'){?>disabled="true"<?} ?>>실적일괄확정 수동실행</button></span>
			</div>
		</div>

		<div style="margin:10px; text-align:left; font-weight:bold;">
			<?
				if($data[$month]['conf_flag'] == 'Y'){?>
					<span>※ <?=$year;?>년 <?=$month;?>월의 실적일괄확정은 이미 실행되었습니다.</span><br><br><?
				}
			?>
			<span>※ 수급자 실적, 요양보호사 실적에 대해 공단청구내역을 확정합니다.</span><br>
			<span>※ 수급자 실적에 대한 미처리 또는 오류 부분이 있을 경우에는 수급자 개인별 또는 일괄 취소를 하여 실적 수정 또는 실적 취소를 합니다.</span><br>
			<span style="padding-left:17px;">(마감 적용일자 변경 후 수정 및 취소 가능)</span><br>
		</div><?
	}else{?>
		<!--div style="width:346px; margin:10px 0 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold;">요양보호사 급여 일괄 계산</div-->
		<div style="width:50px; margin:20px -12px 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold; float:left;"><?=$data[$month]['calc_flag'] == 'Y' ? 'Y' : '-';?></div>
		<div style="width:170px; margin:20px -12px 0 3px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold; float:left;">급여계산 예약일자 저장</div>
		<div style="width:207px; margin:20px 30px 0 10px; padding:7px 5px 6px 5px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold; float:left;">
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
		</div>

		<div style="width:60px; margin:20px -5px 0 10px; padding:6px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold;">
			<span class="btn_pack m"><button type="button" onclick="save(2);" disabled="true">예약일자 저장</button></span>
		</div>

		<div style="text-align:left;">
			<div style="width:60px; margin:20px -5px 0 10px; padding:6px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold;">
				<span class="btn_pack m"><button type="button" onclick="_work_confirm('<?=$code;?>','<?=$year;?>','<?=$month;?>',2);" style="font-weight:bold;">급여일괄계산 수동실행</button></span>
			</div>
		</div>

		<div style="margin:10px; text-align:left; font-weight:bold;">
			<span>※ 요양보호사별 급여 내역을 일괄 계산합니다.</span><br><br>
			<span>※ 요양보호사 개인별 계산된 내역은 무시되고 전체 일괄계산합니다.</span><br><br>
		</div><?
	}
?>

<!--div style="padding:10px 0 10px 10px; text-align:center;">
	<span class="btn_pack m"><button type="button" onclick="_work_confirm('<?=$code;?>','<?=$year;?>','<?=$month;?>',1);">수급자 실적확정 테스트</button></span>
	<span class="btn_pack m"><button type="button" onclick="_work_confirm('<?=$code;?>','<?=$year;?>','<?=$month;?>',2);">요양보호사 급여계산 테스트</button></span>
	<span class="btn_pack m"><button type="button" onclick="save();">확정</button></span>
</div-->

<input type="hidden" name="code"	 value="<?=$code;?>">
<input type="hidden" name="month"	 value="<?=$month;?>">
<input type="hidden" name="min_year" value="<?=$init_year[0];?>">
<input type="hidden" name="max_year" value="<?=$init_year[1];?>">

<input type="hidden" name="mode" value="<?=$mode;?>">

<input type="hidden" name="conf_dt" value="<?=$data[$month]['conf_from'];?>">
<input type="hidden" name="calc_dt" value="<?=$data[$month]['calc_from'];?>">

</form>
<?
	unset($data);

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>