<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_mySuga.php');

	$code		= $_SESSION['userCenterCode'];
	$jumin		= $_POST['jumin'];
	$year		= $_POST['year'];
	$month		= $_POST['month'];
	$svcCd		= $_POST['svcCd'];
	$openerId	= $_POST['id'];
	$sr			= $_POST['sr'];
	$para		= $_POST['para'];
	
	
	/*
	if ($_SESSION['userLevel'] == 'C' || $_SESSION['userSmart'] == 'M'){
		$lbEdit	= true;
	}else{
		$lbEdit	= false;
	}
	*/
	$lbEdit	= true;
?>

<script type='text/javascript' src='../iljung/plan.js'></script>
<script type="text/javascript" src="../longcare/longcare.js"></script>
<script type="text/javascript" src="../iljung/iljung.longcare.js"></script>
<script type="text/javascript" src="../iljung/iljung.longcare.result.js"></script>
<script type='text/javascript' src='../js/work.js'></script>
<script type="text/javascript">
	var ltTimer = null;

	$(document).ready(function(){
		lfReady();
		//if ('<?=$debug;?>' == '1') return;
	});

	function lfReady(){
		_planCltLoad();

		if ('<?=$lbEdit;?>' != '1'){
			$('#divSvcBody').hide();
		}else{
			_planSvcLoad();
			_planAssignLoad();
		}

		_planCalBtnLoad();
		_planCalContLoad();
		_planExtraLoad();
		_planBabyAddLoad();
		_planExtraPayLoad();

		if ('<?=$lbEdit;?>' == '1'){
			if ('<?=$svcCd;?>' == '0' && '<?=$IsLongtermCng2016;?>' != '1'){
				_planOptionLoad();
			}

			ltTimer = setInterval('lfChkCnt()',500);
		}

		//if ('<?=$debug;?>' == '1') return;
	}

	function lfChkCnt(){
		if ($('#infoClient').attr('lvlCnt') && $('#infoClient').attr('kindCnt')){
			clearTimeout(ltTimer);
			ltTimer = null;

			if ($('#infoClient').attr('lvlCnt') <= 0){
				alert('현재달의 등록된 수급자등급이 없습니다.\n수급자등급이 없으면 급여한도금액을 확인 할 수 없어 일정등록이 불가능합니다.\n\n수급자등급을 다시 변경 후 일정등록을 이용하여 주십시오.');
				return;
			}

			if ($('#infoClient').attr('kindCnt') <= 0){
				alert('현재달의 등록된 수급자구분이 없습니다.\n수급자구분이 없으면 급여한도금액을 확인 할 수 없어 일정등록이 불가능합니다.\n\n수급자구분을 다시 변경 후 일정등록을 이용하여 주십시오.');
				return;
			}
		}
	}

</script>

<form id="f" name="f" method="post">

<div style="float:left; width:258px; border-top:2px solid #0e69b0;">
	<div id="centerInfo" style="width:auto;" value="<?=$code;?>" value1="<?=$_SESSION['userCenterGiho'];?>" giho="<?=$_SESSION['userCenterGiho'];?>" name="<?=$_SESSION['userCenterName'];?>">
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

<div id="divSvcBody" style="float:left; width:auto; border-top:2px solid #0e69b0; border-left:1px solid #0e69b0;">
	<div class="title title_border">제공서비스</div>
	<div id="svcInfo"></div>
	<div id="sugaInfo"></div>
</div>

<div style="clear:both; width:100%;">
	<div id="assignInfo"></div>
</div>
<div id="planOption" style="display:none; border-bottom:2px solid #0e69b0;"></div>
<div id="calBtn" style="clear:both; width:100%;"></div>
<div id="loMsg" style="display:none;"></div>
<div id="calCont" style="clear:both; width:100%;"  ynLoad="N"></div>
<div id="extraCont" style="position:absolute; width:auto; display:none;"></div>
<div id="babyAddCont" style="position:absolute; width:auto; display:none;"></div>
<div id="extraPayCont" style="position:absolute; width:auto; display:none;"></div>
<div id="patternCont" style="position:absolute; width:auto; display:none;"></div>
<div id="loLoading" style="position:absolute; width:auto; background-color:#ffffff; border:2px solid #cccccc; top:400px; padding:20px; display:none;"></div>
<div id="planInfo" year="<?=$year;?>" month="<?=$month;?>" svcCd="<?=$svcCd;?>" openerId="<?=$openerId;?>" sr="<?=$sr;?>" para="<?=$para;?>"></div>
<div id="document" type="PLAN"></div>
<div id="strDummy" style="display:none;"></div>
</form>

<script type="text/javascript">
$(document).ready(function(){
	$('#loLoading').html(_planLoading($('#loLoading')));
});

window.onunload = function(){
	try{
		var id		= $('#planInfo').attr('openerId');
		var jumin	= $('#clientInfo').attr('value');
		var cnt		= $('.clsCal[duplicate="N"][ynSave="Y"]').length;
		var yymm	= $('#planInfo').attr('year')+$('#planInfo').attr('month');

		opener._planRegResult(id,cnt,jumin,yymm);
	}catch(e){
	}
}
</script>
<?
	include_once('../inc/_footer.php');
?>