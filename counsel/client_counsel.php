<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$h_ref = $_COOKIE['__left_menu__'];

	$code = $_SESSION['userCenterCode'];
	$kind = $conn->center_kind($code);
	$name = $conn->center_name($code, $kind);

	switch($h_ref){
		case 'report':
			$title = '고객관리 > 초기서식 > 초기상담(욕구사정) 기록지';
			break;

		default:
			$title = '초기상담(욕구사정) 기록지(고객)';
	}

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;
?>
<script src="../js/report.js" type="text/javascript"></script>
<script language='javascript'>
<!--
var f = null;

function counsel_list(page){
	f.page.value = page;
	f.submit();
}

function counsel_find(){
	f.submit();
}

function counsel_reg(dt, seq){
	f.counsel_dt.value = dt;
	f.counsel_seq.value = seq;
	f.action = 'client_counsel_reg.php';
	f.submit();
}

function counsel_delete(code, dt, seq){
	if (!confirm('선택하신 초기상담기록지를 삭제하시겠습니까?')) return;

	var URL  = 'client_counsel_delete.php';
	var para = {'code':code,'dt':dt,'seq':seq};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:para,
			onSuccess:function (responseHttpObj) {
				if (responseHttpObj.responseText == 'OK'){
					counsel_list(f.page.value);
				}
			}
		}
	);
}

function counsel_print(code, dt, seq, gbn){
	//if('<?=$debug;?>'=='1'){
		var URL = 'client_counsel_pdf_test.php?code='+code+'&dt='+dt+'&seq='+seq+'&gbn='+gbn;
	//}else {
	//	var URL = 'client_counsel_print.php?code='+code+'&dt='+dt+'&seq='+seq+'&gbn='+gbn;
	//}
	var popup = window.open(URL,'REPORT','width=700,height=900,scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');
}

function lfExcel(){
	var parm = new Array();
		parm = {

		};

	var form = document.createElement('form');
	var objs;
	for(var key in parm){
		objs = document.createElement('input');
		objs.setAttribute('type', 'hidden');
		objs.setAttribute('name', key);
		objs.setAttribute('value', parm[key]);

		form.appendChild(objs);
	}

	form.setAttribute('method', 'post');
	form.setAttribute('action', './client_counsel_excel.php');

	document.body.appendChild(form);

	form.submit();
}

window.onload = function(){
	f = document.f;

	__init_form(f);
}

//-->
</script>

<form name="f" method="post">

<div class="title"><?=$title;?></div>
<?
	include_once('client_counsel_list.php');
?>
<input name="code" type="hidden" value="<?=$code;?>">
<input name="kind" type="hidden" value="<?=$kind;?>">
<input name="page" type="hidden" value="<?=$page;?>">
<input name="counsel_dt" type="hidden" value="">
<input name="counsel_seq" type="hidden" value="">
<input name="h_ref" type="hidden" value="<?=$h_ref;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>