<?
	include("../inc/_header.php");
	include("../inc/_body_header.php");

?>
<script type="text/javascript" src="../js/work.js"></script>
<script language='javaScript'>
//퇴사자이거나 휴직중인 직원 체크

function stat_chk(){

	alert("퇴사자이거나 휴직중인 직원입니다!");
	return;
}

function _showPopup(object, mIndex, code, kind, jumin){
	var Popup   = document.getElementById('idTalkPopup');
	var pageIndex = mIndex;

	_talkPopupData(object, Popup, pageIndex, code,kind, jumin);
}

function _talkPopupData(object, Popup, pageIndex, code, kind, jumin){
	var URL = 'manage_'+pageIndex+'_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				pageIndex:pageIndex,
				code:code,
				kind:kind,
				jumin:jumin
			},
			onSuccess:function (responseHttpObj) {
				Popup.innerHTML = responseHttpObj.responseText;

				Popup.target = object;
				Popup.object = object;
				Popup.style.left = __getObjectLeft(object);
				Popup.style.top  = __getObjectTop(object)+object.offsetHeight-6;
				Popup.style.display = '';
			}
		}
	);
}
// 모달폼
function __modal(param){
	modalWindow = showModalDialog('../inc/_modal.php?param='+param, window, 'dialogWidth:1000px; dialogHeight:700px; dialogHide:yes; scroll:no; status:yes');

	if (modalWindow == 'Y'){
		_centerYoyReg('yoySearch',document.center.curMcode.value, document.center.curMkind.value,'','',document.center.key.value, document.center.searchJname.value, document.center.searchTel.value);
	}
}
</script>

<div id="center_body"></div>
<?
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");

	if($_GET["gubun"] == "reg"){
	?>
		<script>
			_centerYoyReg('reg','<?=$_GET["mCode"];?>','<?=$_GET["mKind"];?>','','','<?=$_GET["mKey"];?>');
		</script>
	<?
	}else if($_GET["gubun"] == "search"){
	?>
		<script>
			_centerYoyList();
		</script>
	<?
	}
?>

