<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_body_header.php");

	/*
	 * mode
	 * 1 : 실적확정취소
	 * 2 : 급여자동계산 취소
	 */

	$init_year = $myF->year();
	$init_date = explode('-', $myF->dateAdd('month', -1, date('Y-m-d', mktime()), 'Y-m-d'));

	$code	= $_SESSION['userCenterCode'];
	$year	= $_REQUEST['year']  != '' ? $_REQUEST['year']  : intval($init_date[0]);
	$month	= $_REQUEST['month'] != '' ? $_REQUEST['month'] : intval($init_date[1]);
	$mode	= $_REQUEST['mode'];

	if ($mode == 1){
		$title = '실적마감취소';
	}else{
		$title = '급여계산취소';
	}

	$cancel_y = $_REQUEST['cancel_y'] != '' ? $_REQUEST['cancel_y'] : intval(date('Y', mktime()));
	$cancel_m = $_REQUEST['cancel_m'] != '' ? $_REQUEST['cancel_m'] : intval(date('m', mktime()));
	$cancel_d = $_REQUEST['cancel_d'] != '' ? $_REQUEST['cancel_d'] : intval(date('d', mktime()));

	$sql = "select cast(right(closing_yymm, 2) as unsigned) as mm
			,      act_bat_conf_flag as conf_yn
			,      act_bat_conf_dt   as conf_dt
			,      act_bat_can_flag  as conf_can

			,      salary_bat_calc_flag as calc_yn
			,      salary_bat_calc_dt   as calc_dt
			,      salary_bat_can_flag  as calc_can
			,      salary_cls_flag      as cls_flag
			  from closing_progress
			 where org_no          = '$code'
			   and closing_yymm like '$year%'";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<12; $i++){
		if ($i < $row_count){
			$row = $conn->select_row($i);

			$closing[$row['mm']]['conf_yn']	 = $row['conf_yn'];
			$closing[$row['mm']]['conf_can'] = $row['conf_can'];
			$closing[$row['mm']]['conf_dt']  = $row['conf_dt'];

			$closing[$row['mm']]['calc_yn']  = $row['calc_yn'];
			$closing[$row['mm']]['calc_dt']  = $row['calc_dt'];
			$closing[$row['mm']]['calc_can'] = $row['calc_can'];

			$closing[$row['mm']]['cls_flag'] = $row['cls_flag'];
		}else{
			$closing[$i+1]['conf_yn']	= 'N';
			$closing[$i+1]['conf_can']	= 'N';
			$closing[$i+1]['conf_dt']	= '';

			$closing[$i+1]['calc_yn']	= 'N';
			$closing[$i+1]['calc_can']	= 'N';
			$closing[$i+1]['calc_dt']	= '';
			$closing[$i+1]['cls_flag']	= 'N';
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
	var mode = f.mode.value;

	if (mode == 1){
		if (!confirm('실적마감을 취소하시겠습니까?')) return;
	}else{
		if (!confirm('급여자동계산을 취소하시겠습니까?')) return;
	}

	//확정취소후 마감취소함
	//var cancel_dt = showModalDialog('result_yn.php?gubun='+mode+'&conf_dt='+conf_dt, window, 'dialogWidth:300px; dialogHeight:120px; dialogHide:no; scroll:no; status:no');
	//if (!checkDate(cancel_dt)) return;
	//f.cancel_dt.value = cancel_dt;

	f.cancel_dt.value = f.cancel_y.value+'-'+f.cancel_m.value+'-'+f.cancel_d.value;
	f.action = 'result_finish_confirm_cancel_ok.php';
	f.submit();
}

window.onload = function(){
	var calc_yn  = document.getElementById('calc_yn').value;
	var conf_can = document.getElementById('conf_can').value;
	var conf_yn  = document.getElementById('conf_yn').value;

	if (calc_yn == 'Y'){
	}else if (conf_can == 'Y' && conf_yn == 'N'){
	}else if (conf_yn == 'N'){
	}else{
		__set_lastday('cancel');
	}
}

-->
</script>

<div class="title title_border"><?=$title;?></div>

<form name="f" method="post">

<table style="margin:10px;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="my_border_blue my_bold my_bg_blue my_left"><?=$mode == 1 ? '실적 일괄 확정 취소' : '급여 계산 취소';?></td>
		</tr>
		<tr>
			<td class="my_border_blue my_bold my_bg_blue my_left"><?=$mode == 1 ? '확정 기준 년월' : '계산 기준 년월';?></td>
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
		<tr>
			<td class="noborder my_bold" colspan="3">
				<div style="width:auto; text-align:left; margin-top:20px;">
				<?
					if ($mode == 1){
						if ($closing[$month]['calc_yn'] == 'Y'){?>
							<span>※ <?=$year;?>년 <?=$month;?>월은 급여일괄계산이 완료되었습니다.</span><br>
							<span style="padding-left:17px;">실적마감취소를 하시려면 급여계산을 먼저 취소하셔야 합니다.</span><br>
							<span style="padding-left:17px;"><a href="#" onclick="__go_menu('salary','result_finish_confirm_cancel.php?mode=2&year=<?=$year;?>&month=<?=$month;?>');" style="font-weight:bold;">[급여계산취소 바로가기]</a></span><br><?
						}else if ($closing[$month]['conf_can'] == 'Y' && $closing[$month]['conf_yn'] == 'N'){?>
							<span>※ <?=$year;?>년 <?=$month;?>월은 실적마감이 취소되었습니다.</span><br><?
						}else if ($closing[$month]['conf_yn'] == 'N'){?>
							<span>※ <?=$year;?>년 <?=$month;?>월은 실적마감이 실행되지 않았습니다.</span><br><?
						}else{?>
							<span>※ <?=$year;?>년 <?=$month;?>월은 실적마감 취소를 실행하시려면 아래의 버튼을 클릭하여 주십시오.</span><br>
							<div style="text-align:left; margin-bottom:20px;">
								<div class="my_border_blue my_bg_blue" style="width:auto; float:left; padding:3px 10px 3px 10px; margin-right:5px;">
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
									<span class="btn_pack m"><button type="button" onclick="delete_conf('<?=$ed->en($closing[$month]['conf_dt']);?>');" style="font-weight:bold;">실적마감 취소</button></span>
								</div>
							</div><?
						}
					}else{
						if ($closing[$month]['cls_flag'] == 'Y'){?>
							<span>※ <?=$year;?>년 <?=$month;?>월은 급여계산은 마감되었습니다.</span><br><br><?
						}else if ($closing[$month]['calc_can'] == 'Y' && $closing[$month]['calc_yn'] == 'N'){?>
							<span>※ <?=$year;?>년 <?=$month;?>월은 급여계산은 취소되었습니다.</span><br><br><?
						}else if ($closing[$month]['calc_yn'] == 'N'){?>
							<span>※ <?=$year;?>년 <?=$month;?>월은 급여계산은 실행되지 않았습니다.</span><br><br><?
						}else{?>
							<span>※ <?=$year;?>년 <?=$month;?>월은 급여계산취소를 실행하시려면 아래의 버튼을 클릭하여 주십시오.</span><br><br>
							<div style="text-align:left; margin-bottom:20px;">
								<div class="my_border_blue my_bg_blue" style="width:auto; float:left; padding:3px 10px 3px 10px; margin-right:5px;">
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
									<span class="btn_pack m"><button type="button" onclick="delete_conf('<?=$ed->en($closing[$month]['calc_dt']);?>');" style="font-weight:bold;">급여계산 취소</button></span>
								</div>
							</div><?
						}
					}
				?>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<?
	include_once('result_status_sub.php');
?>

<br><br>

<input type="hidden" name="code"      value="<?=$code?>">
<input type="hidden" name="month"     value="<?=$month;?>">
<input type="hidden" name="mode"      value="<?=$mode?>">
<input type="hidden" name="cancel_dt" value="">
<input type="hidden" name="calc_yn"   value="<?=$closing[$month]['calc_yn'];?>">
<input type="hidden" name="conf_can"  value="<?=$closing[$month]['conf_can'];?>">
<input type="hidden" name="conf_yn"   value="<?=$closing[$month]['conf_yn'];?>">

</form>
<?
	unset($data);

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>