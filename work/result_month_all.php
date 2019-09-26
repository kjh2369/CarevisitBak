<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	/**********************************************************

		mode

		1 : 등록
		2 : 취소

	**********************************************************/

	$code = $_SESSION['userCenterCode'];
	$kind = $conn->center_kind($code);
	$mode = $_GET['mode'];

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

	$year  = $_REQUEST['year'] != '' ? $_REQUEST['year'] : date('Y', mktime());
	$month = $_REQUEST['month'] != '' ? $_REQUEST['month'] : date('m', mktime());

	$close_yn = $conn->get_closing_act($code, $year.$month);

	$init_year = $myF->year();

	$k_list = $conn->kind_list($code, $gHostSvc['voucher']);
	$k_cnt  = sizeof($k_list);

	/*********************************************************

		caption

	*********************************************************/
	switch($mode){
		case 1:
			$title = '월 실적 일괄등록';
			$ment1 = '월의 계획데이타를 실적으로 일괄등록하시겠습니까?';
			$ment2 = '실적을 등록할 서비스를 선택하여 주십시오.';
			break;
		case 2:
			$title = '윌 실적 일괄취소';
			$ment1 = '월의 실적을 일괄취소하시곘습니까?';
			$ment2 = '실적을 취소할 서비스를 선택하여 주십시오.';
			break;
		default:
			include_once('../inc/_http_home.php');
			exit;
	}
?>

<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function current_month(code, kind, year, month){
	var f = document.f;

	f.month.value = month;
	f.submit();
}

function result_exec(){
	var f         = document.f;
	var month     = parseInt(f.month.value, 10);
	var kind_list = document.getElementsByName('kind_list[]');
	var kind_chk  = false;

	for(var i=0; i<kind_list.length; i++){
		if (kind_list[i].checked){
			kind_chk = true;
			break;
		}
	}

	if (!kind_chk){
		alert('<?=$ment2;?>');
		return;
	}

	if (!confirm(month + '<?=$ment1;?>')) return;

	f.action = 'result_month_all_ok.php';
	f.submit();
}

function set_check(checked){
	var kind_list = document.getElementsByName('kind_list[]');

	for(var i=0; i<kind_list.length; i++){
		kind_list[i].checked = checked;
	}
}

-->
</script>

<form name="f" method="post">

<div class="title"><?=$title;?></div>

<table class="my_table my_border">
	<colgroup>
		<col width="35px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년도</th>
			<td>
			<?
				echo '<select name=\'year\' style=\'width:auto;\'>';
				for($i=$init_year[0]; $i<=$init_year[1]; $i++){
					echo '<option value=\''.$i.'\' '.($year == $i ? 'selected' : '').'>'.$i.'</option>';
				}
				echo '</select>년';
			?>
			</td>
			<td class="left last">
			<?
				for($i=1; $i<=12; $i++){
					$mon = ($i < 10 ? '0' : '').$i;

					$class = 'my_month ';

					if ($mon == $month){
						$class .= 'my_month_y ';
					}else{
						$class .= 'my_month_2 ';
					}

					$text = '<a href="#" onclick="current_month(\''.$code.'\',\''.$kind.'\',\''.$year.'\',\''.$mon.'\');">'.$i.'월</a>';

					echo '<div class=\''.$class.'\' style=\'float:left; margin-right:2px;\'>'.$text.'</div>';
				}
			?>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="left last bottom" style="padding:20px 20px 0 20px;">
				<div class="my_border_blue bold" style="padding:10px;">
				<?
					if ($close_yn == 'Y'){
						echo '* '.$year.'년 '.intval($month).'월은 실적마감되어 실적을 수정하실 수 없습니다.';
					}else{
						switch($mode){
							case 1:
								echo '* 일정관리에 등록된 계획데이타를 실적으로 일괄등록합니다.<br>';
								echo '<input id=\'flag_1\' name=\'flag[]\' type=\'checkbox\' class=\'checkbox\' value=\'Y\' checked><label for=\'flag_1\'>실적이 등록되어 있으면 수정하지 않습니다.</label>';
								break;
							case 2:
								echo '* 등록된 실적데이타를 일괄취소합니다.<br>';
								echo '<input id=\'flag_2\' name=\'flag[]\' type=\'checkbox\' class=\'checkbox\' value=\'Y\' checked><label for=\'flag_2\'>일괄등록으로 등록된 실적만 취소합니다.</label>';
								break;
						}
					}
				?>
				</div>
			</td>
		</tr>
		<?
			if ($close_yn == 'Y'){
			}else{?>
				<tr>
					<td class="center last bottom" style="padding:10px 10px 0 10px;">
						<div style="width:100%; padding:10px 10px 10px 10px;">
							<table class="my_table my_border_blue" style="width:100%;">
								<colgroup>
									<col width="80px">
									<col>
								</colgroup>
								<thead>
									<th class="head bold" colspan="2">서비스선택</th>
								</thead>
								<tbody>
									<tr>
										<th>전체선택</th>
										<td><input id="check_all" name="check_all" type="checkbox" class="checkbox" onclick="set_check(this.checked);"><label for="check_all">전체</label></td>
									</tr>
								</tbody>
								<tbody>
								<?
									$tr = false;

									for($i=0; $i<$k_cnt; $i++){
										$id = $k_list[$i]['id'];
										$cd = $k_list[$i]['code'];
										$no = $id + (10 - ($id % 10)) - 10;

										if ($tmp_no != $no){
											$tmp_no  = $no;

											if ($tr) echo '</td></tr>';

											$tr = true;

											echo '<tr>';

											if ($cd == '0'){
												echo '<th>장기요양</th>';
											}else if ($cd >= '1' && $cd <= '4'){
												echo '<th>바우처</th>';
											}else if ($cd == '5'){
												echo '<th>시설</th>';
											}else{
												echo '<th>기타유료</th>';
											}

											echo '<td>';
										}

										echo '<input id=\'kind_list_'.$cd.'\' name=\'kind_list[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$cd.'\'><label for=\'kind_list_'.$cd.'\'>'.$k_list[$i]['name'].'</label>';
									}

									echo '</td></tr>';
								?>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td class="center last bottom" style="padding-top:20px;">
						<span class="btn_pack m"><button type="button" class="bold" onclick="result_exec();" <? if ($close_yn == 'Y'){echo 'disabled=true';} ?>><?=intval($month);?>월 실적 전체 <?=$mode == 1 ? '등록' : '취소';?></button></span>
					</td>
				</tr><?
			}
		?>
	</tbody>
</table>

<input type="hidden" name="code"  value="<?=$code;?>">
<input type="hidden" name="kind"  value="<?=$kind;?>">
<input type="hidden" name="month" value="<?=$month;?>">
<input type="hidden" name="mode"  value="<?=$mode;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>