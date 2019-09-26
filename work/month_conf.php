<?
	include("../inc/_header.php");
	
	if ($_GET['manager'] != 'true'){
		include("../inc/_body_header.php");
	}
	
	$mCode = $_SESSION["userCenterCode"];
	$mKind = $_REQUEST["mKind"] != "" ? $_REQUEST["mKind"] : $_SESSION["userCenterKind"][0];
	$mYear = $_REQUEST["mYear"] != "" ? $_REQUEST["mYear"] : date("Y", mkTime());
	
	if ($_GET['manager'] == 'true'){
	?>
		<style>
		body{
			margin-left:10px;
			margin-right:10px;
		}
		</style>
	<?
	}

?>
<script src="../js/work.js" type="text/javascript"></script>
<form name="f" method="post">
<table style="width:100%;">
<tr>
<td class="title" colspan="2">월 실적 등록(수급자)</td>
</tr>
<tr>
	<td style="text-align:left;" class="noborder">
		<select name="mYear" style="width:auto;" onChange="_getSugupjaList(myBody, this.value, '<?=$mGubun;?>', '<?=$mCode;?>', document.suList.myKind.value);">
		<?
			$years = $conn->get_min_max_year('t01iljung', 't01_sugup_date');
			$years[1] = date("Y", mkTime())+(date("m", mkTime())=="12"?1:0);
			for($i=$years[0]; $i<=$years[1]; $i++){
			?>
				<option value="<?=$i;?>"<? if($i == $mYear){echo "selected";}?>><?=$i;?></option>
			<?
			}
		?>
		</select>
		<select name="mKind" style="width:auto;">
		<?
			for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
			?>
				<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $mKind){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
			<?
			}
		?>
		</select>
		<select name="mRate" style="width:auto;">
		<option value="all">전체</option>
		<?
			$sql = "select m92_code"
				 . ",      m92_bonin_yul"
				 . ",      m92_cont"
				 . "  from m92boninyul"
				 . " where date_format(now(), '%Y%m%d') between m92_sdate and m92_edate"
				 . " order by m92_code";
			$conn->query($sql);
			$conn->fetch();
			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				?>
				<option value="<?=$row['m92_code'];?>" <? if ($mRate == $row['m92_code']){echo 'selected';} ?>><?=$row['m92_cont'];?>(<?=$row['m92_bonin_yul'];?>)</option>
				<?
			}

			$conn->row_free();
		?>
		</select>
		<input type="button" value="조회" class="btnSmall2" onClick="getMonthConfList(document.getElementById('myBody'), document.f.mYear.value, '<?=$mCode;?>', document.f.mKind.value, document.f.mRate.value);">
	</td>
</tr>
<tr>
	<td class="noborder">
	<div id="myBody"></div>
	</td>
</tr>
</table>
</form>
<?
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");
?>
<script language='javascript'>
	getMonthConfList(document.getElementById('myBody'), document.f.mYear.value, '<?=$mCode;?>', document.f.mKind.value, document.f.mRate.value);
</script>