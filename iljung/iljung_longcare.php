<?
	include_once '../inc/_header.php';
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$payCtrNo       = $_POST['payCtrNo'];
	$payMm          = $_POST['payMm'];
	$longTermMgmtNo = $_POST['longTermMgmtNo'];
	$serviceKind    = $_POST['serviceKind'];
	$fnc            = $_POST['fnc'];
?>
<script type='text/javascript' src='../js/iljung.reg.js'></script>
<script type='text/javascript'>
<!--

function evtResize(){
	//var h = document.body.clientHeight - $('#iljungPlan').height() - $('#iljungFoot').height();
	//$('#iljungLongcare').height(h);
}

$(document).ready(function(){
	//evtResize();
	_iljungLongcareLoad('iljungLongcare', '<?=$payMm;?>', '<?=$payCtrNo;?>', '<?=$longTermMgmtNo;?>', '<?=$svcKind;?>', '<?=$fnc;?>');
});

$(window).resize(function(){
//	evtResize();
});

-->
</script>
<div id="iljungPlan" style="widht:100%; height:100px;"></div>
<div id="iljungLongcare" style="widht:100%; height:300px;"></div>
<div id="iljungFoot" style="widht:100%; height:50px;">
</div>
<?
	include_once '../inc/_footer.php';
?>