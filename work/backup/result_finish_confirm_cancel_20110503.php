<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_body_header.php");

	$init_year = $myF->year();

	$code	= $_SESSION['userCenterCode'];
	$year	= $_REQUEST['year']  != '' ? $_REQUEST['year']  : intval(date('Y', mktime()));
	$month	= $_REQUEST['month'] != '' ? $_REQUEST['month'] : intval(date('m', mktime()));

	$cancel_y = $_REQUEST['cancel_y'] != '' ? $_REQUEST['cancel_y'] : intval(date('Y', mktime()));
	$cancel_m = $_REQUEST['cancel_m'] != '' ? $_REQUEST['cancel_m'] : intval(date('m', mktime()));
	$cancel_d = $_REQUEST['cancel_d'] != '' ? $_REQUEST['cancel_d'] : intval(date('d', mktime()));

	$sql = "select cast(right(closing_yymm, 2) as unsigned) as mm
			,      act_bat_conf_flag as conf_yn
			,      act_bat_conf_dt as conf_dt
			,      act_bat_can_flag as can_yn
			,      salary_bat_calc_flag as calc_yn
			  from closing_progress
			 where org_no          = '$code'
			   and closing_yymm like '$year%'";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<12; $i++){
		if ($i < $row_count){
			$row = $conn->select_row($i);

			$closing[$row['mm']]['conf']    = $row['conf_yn'];
			$closing[$row['mm']]['can']     = $row['can_yn'];
			$closing[$row['mm']]['calc']    = $row['calc_yn'];
			$closing[$row['mm']]['conf_dt'] = $row['conf_dt'];
		}else{
			$closing[$i+1]['conf']    = 'N';
			$closing[$i+1]['can']     = 'N';
			$closing[$i+1]['calc']    = 'N';
			$closing[$i+1]['conf_dt'] = '';
		}
	}

	$conn->row_free();
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function set_month(month){
	var f = document.f;

	f.month.value = month;
	f.submit();
}

function delete_conf(conf_dt){
	var f = document.f;

	if (!confirm('실적일괄확정을 취소하시겠습니까?')) return;

	var cancel_dt = showModalDialog('result_yn.php?gubun=1&conf_dt='+conf_dt, window, 'dialogWidth:300px; dialogHeight:120px; dialogHide:no; scroll:no; status:no');

	if (!checkDate(cancel_dt)) return;

	f.cancel_dt.value = cancel_dt;
	f.action = 'result_finish_confirm_cancel_ok.php';
	f.submit();
}

window.onload = function(){
	var calc = document.getElementById('calc').value;
	var can  = document.getElementById('can').value;
	var conf = document.getElementById('conf').value;

	if (calc == 'Y'){
	}else if (can == 'Y' && conf == 'N'){
	}else if (conf == 'N'){
	}else{
		__set_lastday('cancel');
	}
}

-->
</script>

<div class="title title_border">실적일괄확정취소</div>

<form name="f" method="post">

<table style="margin:10px;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="my_border_gray my_bold my_bg_gray my_left">실적 일괄 확정 취소</td>
		</tr>
		<tr>
			<td class="my_border_gray my_bold my_bg_gray my_left">확정 기준 년월</td>
			<td class="my_border_gray my_bold my_center">
				<select name="year" style="width:auto;" onchange="search();">
				<?
					for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
						<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?></option><?
					}
				?>
				</select>년
			</td>
			<td class="my_border_gray my_center">
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
		<tr>
			<td class="noborder my_bold" colspan="3">
				<div style="width:auto; text-align:left; margin-top:20px;">
				<?
					if ($closing[$month]['calc'] == 'Y'){?>
						<span>※ <?=$year;?>년 <?=$month;?>월은 급여일괄계산이 완료되었습니다.</span><br>
						<span style="padding-left:17px;">실적일괄확정취소를 하시려면 급여일괄계산을 먼저 취소하셔야 합니다.</span><br><br><?
					}else if ($closing[$month]['can'] == 'Y' && $closing[$month]['conf'] == 'N'){?>
						<span>※ <?=$year;?>년 <?=$month;?>월은 실적일괄확정이 취소되었습니다.</span><br><br><?
					}else if ($closing[$month]['conf'] == 'N'){?>
						<span>※ <?=$year;?>년 <?=$month;?>월은 실적일괄확정이 실행되지 않았습니다.</span><br><br><?
					}else{?>
						<span>※ <?=$year;?>년 <?=$month;?>월은 실적일괄확정 취소를 실행하시려면 아래의 버튼을 클릭하여 주십시오.</span><br><br>
						<div style="text-align:left; margin-bottom:20px;">
							<div class="my_border_gray my_bg_gray" style="width:auto; float:left; padding:3px 10px 3px 10px; margin-right:5px;">
								<select name="cancel_y" style="width:auto;" onchange="__set_lastday('cancel');">
								<?
									for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
										<option value="<?=$i;?>" <? if($i == $cancel_y){?>selected<?} ?>><?=$i;?></option><?
									}
								?>
								</select>년
								<select name="cancel_m" style="width:auto;" onchange="__set_lastday('cancel');">
								<?
									for($i=1; $i<=12; $i++){?>
										<option value="<?=($i < 10 ? '0' : '').$i;?>" <? if($i == $cancel_m){?>selected<?} ?>><?=$i;?></option><?
									}
								?>
								</select>월
								<select name="cancel_d" style="width:auto;">
								<?
									for($i=1; $i<=31; $i++){?>
										<option value="<?=($i < 10 ? '0' : '').$i;?>" <? if($i == $cancel_d){?>selected<?} ?>><?=$i;?></option><?
									}
								?>
								</select>일
							</div>
							<div style="width:auto; padding:5px 10px 3px 10px;">
								<span class="btn_pack m"><button type="button" onclick="delete_conf('<?=$ed->en($closing[$month]['conf_dt']);?>');" style="font-weight:bold;">실적일괄확정 취소</button></span>
							</div>
						</div><?
					}
				?>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code"      value="<?=$code?>">
<input type="hidden" name="month"     value="<?=$month;?>">
<input type="hidden" name="cancel_dt" value="">
<input type="hidden" name="calc"      value="<?=$closing[$month]['calc'];?>">
<input type="hidden" name="can"       value="<?=$closing[$month]['can'];?>">
<input type="hidden" name="conf"      value="<?=$closing[$month]['conf'];?>">

</form>
<?
	unset($data);

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>