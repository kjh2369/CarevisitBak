<?
	include('../inc/_header.php');
	include('../inc/_page_list.php');
	include('../inc/_body_header.php');
	include('../inc/_myFun.php');
	include('../inc/_ed.php');

	if ($_SESSION["userLevel"] == "A"){
		$mCode = $_POST['mCode'];
	}else{
		$mCode = $_SESSION['userCenterCode'];
	}
	//$mCode = $_POST['mCode'] != '' ? $_POST['mCode'] : $_SESSION['userCenterCode'];
	$mKind = $_POST['mKind']; // != '' ? $_REQUEST['mKind'] : $_SESSION['userCenterKind'][0];
?>
<script src="../js/salary.js" type="text/javascript"></script>
<form name="f" method="post">
<div class="title">직원현황</div>
<table class="my_table my_border">
	<tbody>
		<tr>
			<th style="width:60px">기관분류</th>
			<td class="noborder" style="width:10%; text-align:left;">
				<input name="mCode" type="hidden" value="<?=$mCode;?>">
				<?
				if ($_SESSION["userLevel"] != "A"){
					?>
					<select name="mKind" style="width:auto;">
					<option value="">- 전   체 -</option>
					<?
						include('../inc/_kind_option.php');
					?>
					</select>
					<?
				}else{
					?>
						<input name="mKind" type="hidden" value="">
					<?
				}
				?>
			</td>
			<th style="width:60px">가족보호사</th>
			<td class="noborder" style="width:10%; text-align:left;">
				<select name="familyCare" style="width:auto;">
					<option value="">- 전   체 -</option>
					<option value="Y">유</option>
					<option value="N">무</option>
				</select>
			</td>
			<th style="width:60px">4대보험</th>
			<td class="noborder" style="width:10%; text-align:left;">
				<select name="insurance" style="width:auto;">
					<option value="">- 전   체 -</option>
					<option value="Y">유</option>
					<option value="N">무</option>
				</select>
			</td>
			<th style="width:60px">고용상태</th>
			<td class="noborder" style="width:10%; text-align:left;">
				<select name="employment" style="width:auto;">
					<option value="">- 전   체 -</option>
					<option value="1" selected>활동</option>
					<option value="2">휴직</option>
					<option value="9">퇴직</option>
				</select>
			</td>
			<td class="last" style="width:15%; text-align:left;">
				<span class="btn_pack m icon"><span class="refresh"></span><button name="btnSearch" type="button" onFocus="this.blur();" onClick="_memberStatusList();">검색</button></span>
				<span class="btn_pack m icon"><span class="excel"></span><button name="btnExcel" type="button" onFocus="this.blur();" onClick="alert('준비중입니다.');">엑셀</button></span>
			</td>
		</tr>
	</tbody>
</table>
<div id="myBody" style="margin-top:-1px;"></div>
</form>
<?
	include('../inc/_body_footer.php');
	include('../inc/_footer.php');
?>
<script language="javascript">
	_memberStatusList();
</script>


