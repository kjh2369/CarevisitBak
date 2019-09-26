<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$svcCd = $_POST['svcCd'];
?>

<script type='text/javascript' src='./plan.js'></script>
<script type="text/javascript" src="./iljung.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		_planCltLoad();
		_planAssignLoad();
		_planCalBtnLoad();
		_planCalContLoad();
	});
</script>

<form id="f" name="f" method="post">
<div style="float:left; width:258px; border-top:2px solid #0e69b0;">
	<div id="centerInfo" style="width:auto;" value="<?=$code;?>" value1="<?=$_SESSION['userCenterGiho'];?>">
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="60px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<td class="bottom last" colspan="2"><div class="title title_border">기관 및 고객정보</div></td>
				</tr>
				<tr>
					<th class="center">기관명</th>
					<td class="center last"><div class="left nowrap" style="width:193px;"><?=$_SESSION['userCenterName'];?>(<?=$_SESSION['userCenterGiho'];?>)</div></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="clientInfo" value="<?=$jumin;?>"></div>
</div>

<div style="float:left; width:auto; border-top:2px solid #0e69b0; border-left:1px solid #0e69b0;">
	<div class="title title_border">실적조회내역</div>
	<div id="assignInfo"></div>
</div>
<div id="loMsg" style="display:none;"></div>
<div id="calBtn" style="clear:both; width:auto;"></div>
<div style="float:left; width:100%;">
	<div id="calCont" style="clear:both; width:100%;"  ynLoad="N"></div>
</div>
<div id="loLoading" style="position:absolute; width:auto; background-color:#ffffff; border:2px solid #cccccc; top:400px; padding:20px; display:none;"></div>

<div id="planInfo" year="<?=$year;?>" month="<?=$month;?>" svcCd="<?=$svcCd;?>" openerId="<?=$openerId;?>"></div>
<div id="document" type="CONF"></div>
</form>

<script type="text/javascript">
$(document).ready(function(){
	$('#loLoading').html(_planLoading($('#loLoading')));
});
</script>
<?
	include_once('../inc/_footer.php');
?>