<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code  = $_SESSION['userCenterCode'];
	$mode  = $_REQUEST['mode'];

	switch($mode){
		case 'from':
			$title = '보낸쪽지함';
			break;
		case 'to':
			$title = '받은쪽지함';
			break;
	}

	$item_count = 10;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;
?>

<script language='javascript'>
<!--

function list(page){
	document.getElementById('page').value = page;

	var mode = document.getElementById('mode').value;
	var URL = 'note_list_sub.php';
	var params  = {'page':page,'mode':mode};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function (responseHttpObj) {
				body_tbl.innerHTML = responseHttpObj.responseText;
				init();
			}
		}
	);
}

/*
 * 쪽지보기
 */
function msg_show(yymm, seq, fcd){
	var code = document.getElementById('code').value;
	var body = document.getElementById('body_show');
	var mode = document.getElementById('mode').value;
	var img  = document.getElementById('img_'+yymm+'_'+seq+'_'+fcd);

	var URL = 'note_view.php';
	var params  = {'mode':mode,'code':code,'yymm':yymm,'seq':seq,'fcd':fcd};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function (responseHttpObj) {
				body.innerHTML = responseHttpObj.responseText;
				body.style.display = '';
				img.src = '../image/msg2.gif';
			}
		}
	);
}

/*
 * 닫기
 */
function msg_hidden(){
	var body = document.getElementById('body_show');
		body.style.display = 'none';
}

/*
 * 삭제
 */
function msg_delete(yymm, seq, fcd){
	var code = document.getElementById('code').value;
	var mode = document.getElementById('mode').value;
	var page = document.getElementById('page').value;

	if (!confirm('쪽지를 정말로 삭제하시곘습니까?')) return;

	var rst = getHttpRequest('note_delete_ok.php?mode='+mode+'&code='+code+'&yymm='+yymm+'&seq='+seq+'&fcd='+fcd);

	list(page);
}

function init(){
	var show = document.getElementById('body_show');
	var list = document.getElementById('body_list');
	var top  = __getObjectTop(list);
	var left = __getObjectLeft(list);
	var width= list.offsetWidth;

	show.style.top  = top + 20;
	show.style.left = left;
	show.style.width= width - 20;
}

window.onload = function(){
	list(1);
	__init_form(document.f);
}

-->
</script>

<div class="title title_border"><?=$title;?></div>

<form name="f" method="post">

<div id="body_tbl"></div>
<div id="body_show" style="position:absolute; top:0; left:0; margin:10px; border:3px solid #cccccc; background-color:#ffffff; display:none;"></div>

<input name="code" type="hidden" value="<?=$code;?>">
<input name="mode" type="hidden" value="<?=$mode;?>">
<input name="page" type="hidden" value="<?=$page;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>