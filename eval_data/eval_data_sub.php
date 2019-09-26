<?
	include_once('../inc/_http_uri.php');

	$code = $_SESSION['userCenterCode'];
	
	/*******************************************************

		리포트 대분류

	*******************************************************/
		$report_index = $_REQUEST['report_index'];
		$report_id    = $report->get_report_id($report_index);

		$yymm = $_REQUEST['yymm'];
		$seq  = $_REQUEST['seq'];
		$ssn  = $ed->de($_REQUEST['ssn']);
	/******************************************************/
	//$year = substr($yymm, 0, 4);
	//$month = substr($yymm, 4, 2);
	
	$month = substr($yymm,4);
	$month = $month > 9 ? $month : substr($month,1);
	
	$year  = $_REQUEST['year'];
	$month = $_REQUEST['month'] != '' ?  $_REQUEST['month'] : $month;
	
	$find_report_nm	= $_REQUEST['find_report_nm'];

	$title = '2012평가자료';

?>
<script src="../js/report.js" type="text/javascript"></script>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function find_report(find_report_nm){
	var f = document.f;

	f.target = '_self';
	f.action = 'report.php';
	f.submit();
}

var __BODY__ = 'report_body';
var __REPORT_WIN__ = null;


/*
 * 2012평가 리포트
 */
function _eval_list(report_menu, find_report_nm){
	var URL     = 'eval_data_list.php';
	var param   = {'report_menu':report_menu,'find_report_nm':find_report_nm};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:_response_http
		}
	);

	_report_show_close();
}

/*
 * 2012평가별 리스트
 */
function _report_list_dtl2(report_menu, report_index, code, navi, is_pop){
	if (is_pop == undefined) is_pop = 'N';
	
	var URL     = '../reportMenu/report_list_dtl.php';
	var param   = {'report_menu':report_menu,'report_index':report_index,'code':code,'is_pop':is_pop};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function(responseHttpObj){
				var body = document.getElementById(__BODY__);
					body.innerHTML = responseHttpObj.responseText;

				_set_report_navi(navi);
			}
		}
	);

	_report_show_close();
}


function eval_detail_view(p_target, url){
	var target	= __getObject(p_target);
	var x		= __getObjectLeft(target);
	var y		= __getObjectTop(target);
	
	var body	= __getObject('EVAL_DETAIL');
	
	var URL = '../eval_data/eval_detail_view.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				p_body:p_target,
				'url' : url
			},
			onSuccess:function (responseHttpObj) {
				body.innerHTML = responseHttpObj.responseText;
				
				
				body.style.left = x + 40;
				body.style.top  = y + 20;
				body.style.display = '';
			}
		}
	);
}

function eval_detail_close(){
	document.getElementById("EVAL_DETAIL").style.display = 'none';
}

window.onload = function(){
	try{
		document.getElementById('opener').value = opener.name;
	}catch(e){
	}

	var body = document.getElementById('report_body');


	if ('<?=$report_id;?>' == '' && '<?=$yymm;?>' == '' && '<?=$seq;?>' == ''){
		_eval_list('<?=$report_menu?>','<?=$find_report_nm;?>');
	}else {
		_report_reg('<?=$code;?>','<?=$report_menu;?>','<?=$report_index;?>','<?=$yymm;?>','<?=$seq;?>','<?=$ed->en($ssn);?>');
	}

	self.focus();
}

window.onunload = _report_show_close;

-->
</script>

<form name="f" method="post" target="_self" enctype="multipart/form-data">

<div id="tmp_title">
	<div id="report_navi" class="title" style="width:auto; float:left;"><?=$title?></div>
	<?
		include_once('../reportMenu/report_view_download.php');
	?>
</div>
<div id="report_body"></div>

<input id="reportMenu" name="report_menu" type="hidden" value="<?=$report_menu;?>">
<input id="code" name="code" type="hidden" value="<?=$code;?>">
<input id="yymm" name="yymm" type="hidden" value="<?=$yymm?>">
<input id="year" name="year" type="hidden" value="<?=$year?>">
<input id="month" name="month" type="hidden" value="<?=$month?>">
<input id="Year" name="Year" type="hidden" value="">
<input id="Month" name="Month" type="hidden" value="">
<input id="seq" name="seq" type="hidden" value="">
<input id="ssn" name="ssn" type="hidden" value="">
<input id="opener" name="opener" type="hidden" value="">
<input id="param" name="param" type="hidden" value="">

</form>