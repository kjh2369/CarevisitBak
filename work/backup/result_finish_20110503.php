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

	$sql = "select ifnull(max(cast(substring(closing_yymm, 5, 2) as signed)), 1)
			  from closing_progress
			 where org_no          = '$code'
			   and closing_yymm like '$year%'
			   and del_flag        = 'N'";
	$max_closing_ym = $conn->get_data($sql);
	$max_closing_ym ++;

	$sql = "select closing_yymm
			,      act_cls_dt_from
			,      act_cls_ent_dt
			,      act_bat_conf_flag
			,      salary_cls_dt_from
			,      salary_cls_ent_dt
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
		$data[$i]['pay_dt']  = '';
		$data[$i]['reg_dt']  = '';
	}

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$index = intval(substr($row['closing_yymm'], 4, 2));

			$data[$index]['closing'] = 'Y';
			$data[$index]['act_dt']  = $row['act_cls_dt_from'];
			$data[$index]['pay_dt']  = $row['salary_cls_dt_from'];
			$data[$index]['reg_dt']  = $row['act_cls_ent_dt'];
			$data[$index]['act_yn']  = $row['act_bat_conf_flag'];
		}

		if ($data[$month]['closing'] == 'Y'){
			$reg_date  = $data[$month]['reg_dt'];
			$temp_date_1 = explode('-', $data[$month]['act_dt']);
			$temp_date_2 = explode('-', $data[$month]['pay_dt']);
		}
	}

	if ($data[$month]['closing'] != 'Y'){
		$reg_date  = date('Y-m-d', mktime());
		$temp_date = $myF->dateAdd('month', 1, $year.'-'.$month.'-01', 'Y-m-d');
		$today     = date('Y-m-d', mktime());

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
			alert('마감적용일자(실적)는 마감기준년월보다 커야 합니다. 확인하여 주십시오.');
			return;
		}
	}else{
		var p_dt = f.p_y.value + '-' + f.p_m.value + '-' + f.p_d.value;

		if (s_dt > p_dt){
			alert('마감적용일자(급여)는 마감기준년월보다 커야 합니다. 확인하여 주십시오.');
			return;
		}
	}

	f.action = 'result_finish_save.php?gubun='+gubun;
	f.submit();
}

window.onload = function(){
	set_lastday('<?=$mode == 1 ? "w" : "p";?>');
	//set_lastday('p');
}
-->
</script>

<div class="title title_border">마감처리등록</div>

<form name="f" method="post">

<div style="width:120px; margin:10px 0 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold;">실적 마감</div>
<div style="width:120px; margin:-2px -12px 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold; float:left;">마감 기준 년월</div>
<div style="width:90px; margin:-2px -10px 0 10px; padding:7px 5px 6px 5px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold; float:left;">
	<select name="year" style="width:auto;" onchange="search();">
	<?
		for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
			<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?></option><?
		}
	?>
	</select>년
	<!--select name="s_m" style="width:auto;" onchange="search();">
	<?
		for($i=1; $i<=12; $i++){?>
			<option value="<?=($i < 10 ? '0' : '').$i;?>" <? if($i == $s_m){?>selected<?} ?>><?=$i;?></option><?
		}
	?>
	</select>월-->
</div>
<div style="margin:-2px 0 0 -10px; padding:5px; border:2px solid #cccccc; background-color:#fbfbfb;">
<?
	for($i=1; $i<=12; $i++){
		$class = 'my_month ';

		if ($i == intval($month)){
			$class .= 'my_month_y ';
			$color  = 'color:#000000;';
		}else{
			if ($data[$i]['closing'] == 'Y'){
				$class .= 'my_month_1 ';
				$color  = 'color:#000000;';
			}else{
				$class .= 'my_month_2 ';
				$color  = 'color:#c7c7c7;';
			}
		}

		//if ($i <= $max_closing_ym){
			$text = '<a href="#" style="'.$color.'" onclick="set_month('.$i.');">'.$i.'월</a>';
		//}else{
		//	$text = '<span style="'.$color.'">'.$i.'월</span>';
		//}

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
		<!--div style="width:120px; margin:10px 0 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold;">실적 마감</div-->
		<div style="width:120px; margin:20px -12px 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold; float:left;">마감 적용 일자</div>
		<div style="width:207px; margin:20px 0 0 10px; padding:7px 5px 6px 5px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold;">
			<select name="w_y" style="width:auto;" onchange="set_lastday('w');">
			<?
				for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
					<option value="<?=$i;?>" <? if($i == $w_y){?>selected<?} ?>><?=$i;?></option><?
				}
			?>
			</select>년
			<select name="w_m" style="width:auto;" onchange="set_lastday('w');">
			<?
				for($i=1; $i<=12; $i++){?>
					<option value="<?=($i < 10 ? '0' : '').$i;?>" <? if($i == $w_m){?>selected<?} ?>><?=$i;?></option><?
				}
			?>
			</select>월
			<select name="w_d" style="width:auto;">
			<?
				for($i=1; $i<=31; $i++){?>
					<option value="<?=($i < 10 ? '0' : '').$i;?>" <? if($i == $w_d){?>selected<?} ?>><?=$i;?></option><?
				}
			?>
			</select>일
		</div>
		<div style="width:120px; margin:10px -9px 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold; float:left;">마감 등록 일자</div>
		<div style="width:120px; margin:10px 80px 0 0; padding:5px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold; float:left;"><?=$reg_date;?></div>
		<div style="width:80px; margin:10px -5px 0 0; padding:6px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold;">
			<span class="btn_pack m"><button type="button" onclick="save(1);" <? if($data[$month]['act_yn'] == 'Y'){?>disabled="true"<?} ?>>확인</button></span>
		</div>
		<div style="margin:10px; text-align:left; font-weight:bold;">
			<span>※ 실적마감 처리 후 수급자 수급실적을 일괄 확정 하여야 합니다.</span><br><br>
			<span>※ 실적 마감을 하면 마감적용일자부터 마감기준년월의 수급자 및 요양보호사의 실적은 등록, 수정, 취소를 할 수 없습니다.</span><br><br>
			<span>※ 마감 후 실적 등록, 수정, 취소를 하고자 하는 경우에는 마감적용일자를 현재의 일자(시스템일자)보다 크게 변경 하여야 합니다.</span><br>
			<span style="padding-left:17px;">(실적일괄확정, 급여계산을 한 후 실적 수정 또는 취소를 하고자 할 경우에는 실적확정취소 또는 급여계산취소를 한 후 마감적용일자</span><br>
			<span style="padding-left:22px;">변경 가능)</span><br><br>
			<span>※ 수급자 실적확정(공단청구학정) 및 요양보호사 급여 계산 후 마감적용일자를 변경하게 되면 실적 등록, 수정, 취소가 가능하게 되어</span><br>
			<span style="padding-left:19px;">이전에 계산된 청구 금액 및 급여금액이 서로 다를 수 있으니 주의하시기 바랍니다.(재 확정 필요)</span><br><br>
		</div><?
	}else{?>
		<!--div style="width:120px; margin:10px 0 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold;">급여 마감</div-->
		<div style="width:120px; margin:-2px -12px 0 10px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold; float:left;">마감 적용 일자</div>
		<div style="width:207px; margin:-2px 0 0 10px; padding:7px 5px 6px 5px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold; float:left;">
			<select name="p_y" style="width:auto;" onchange="set_lastday('p');">
			<?
				for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
					<option value="<?=$i;?>" <? if($i == $p_y){?>selected<?} ?>><?=$i;?></option><?
				}
			?>
			</select>년
			<select name="p_m" style="width:auto;" onchange="set_lastday('p');">
			<?
				for($i=1; $i<=12; $i++){?>
					<option value="<?=($i < 10 ? '0' : '').$i;?>" <? if($i == $p_m){?>selected<?} ?>><?=$i;?></option><?
				}
			?>
			</select>월
			<select name="p_d" style="width:auto;">
			<?
				for($i=1; $i<=31; $i++){?>
					<option value="<?=($i < 10 ? '0' : '').$i;?>" <? if($i == $p_d){?>selected<?} ?>><?=$i;?></option><?
				}
			?>
			</select>일
		</div>
		<div style="width:120px; margin:-2px -5px 0 -2px; padding:5px; border:2px solid #cccccc; background-color:#efefef; text-align:center; font-weight:bold; float:left;">마감 등록 일자</div>
		<div style="width:120px; margin:-2px -5px 0 -2px; padding:5px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold; float:left;"><?=$reg_date;?></div>
		<div style="width:120px; margin:-2px -5px 0 10px; padding:6px; border:2px solid #cccccc; background-color:#fbfbfb; text-align:center; font-weight:bold;">
			<span class="btn_pack m"><button type="button" onclick="save(2);">저장</button></span>
		</div><?
	}
?>

<input type="hidden" name="mode"	 value="<?=$mode;?>">
<input type="hidden" name="code"	 value="<?=$code;?>">
<input type="hidden" name="month"	 value="<?=$month;?>">
<input type="hidden" name="min_year" value="<?=$init_year[0];?>">
<input type="hidden" name="max_year" value="<?=$init_year[1];?>">

</form>
<?
	unset($data);

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>