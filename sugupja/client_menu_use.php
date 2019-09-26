<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];
	$svcId = $_POST['svcId'];

	$laSvcList = $conn->kind_list($code, $gHostSvc['voucher']);

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);?>

	<div id="loSvc_<?=$svcCd;?>" value="<?=$svcCd.'_'.$svcId;?>" style="position:; float:left; top:0; left:0; width:427px; background-color:#ffffff; padding:10px; display:;"><?
		$__CURRENT_SVC_ID__ = $svcId;
		$__CURRENT_SVC_CD__ = $svcCd;
		$__CURRENT_SVC_NM__ = $conn->kind_name($laSvcList, $__CURRENT_SVC_ID__, 'id');
		$lbPop = false;
		include('./client_reg_sub.php');?>
	</div><?

	switch($svcCd){
		case '0':?>
			<script type="text/javascript">
				$(document).ready(function(){
					__init_form(document.f);

					_clientSetMgmtData(); //장기요양보험
					_clientSetLimitData(); //청구한도
					_clientSetNurseData(); //가사간병
				});
			</script><?
			break;

		case '1':?>
			<script type="text/javascript">
				$(document).ready(function(){
					__init_form(document.f);

					_clientSetNurseData(); //가사간병
					_clientSetLevelData('21','1');
				});
			</script><?
			break;

		case '2':?>
			<script type="text/javascript">
				$(document).ready(function(){
					__init_form(document.f);

					_clientSetBabyData(); //산모신생아
					_clientSetLevelData('22','2');
				});
			</script><?
			break;

		case '3':?>
			<script type="text/javascript">
				$(document).ready(function(){
					__init_form(document.f);

					_clientSetBabyData(); //산모신생아
					_clientSetLevelData('23','3');
				});
			</script><?
			break;

		case '4':?>
			<script type="text/javascript">
				$(document).ready(function(){
					__init_form(document.f);

					_clientSetLevelData('24','4');
				});
			</script><?
			break;

		default:?>
			<script type="text/javascript">
				$(document).ready(function(){
					__init_form(document.f);
				});
			</script><?
	}

	include_once('../inc/_db_close.php');
?>