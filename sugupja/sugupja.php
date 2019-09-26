<?
	include("../inc/_header.php");
	include("../inc/_body_header.php");
	include('../inc/_ed.php');

	$mJumin = $ed->de($_GET["mJumin"]);
?>
<div id="center_body"></div>
<?
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");

	if($_GET["gubun"] == "reg"){
	?>
		<script>
			_sugupjaReg('reg');
		</script>
	<?
	}else if($_GET["gubun"] == "search"){
	?>
		<script>
			_sugupjaList();
		</script>
	<?
	}else if($_GET['gubun'] == 'sugupjaReg'){
	?>
		<script>
			_sugupjaReg('reg','<?=$_GET["mCode"];?>','<?=$_GET["mKind"];?>','<?=$ed->en($mJumin);?>','','','');
		</script>
	<?
	}
?>
<script type="text/javascript" src="../js/work.js"></script>
<script language='javascript'>
function showSugupjaLayer(){
	if (document.center.editMode.value == 1){
		_sugupjaSave();
	}else{
		if (document.center.yLvl.value == document.center.yLvl.tag &&
			document.center.sKind.value == document.center.sKind.tag &&
			document.center.yoyangsa1.value == document.center.yoyangsa1.tag &&
			document.center.gaeYakFm.value.split('-').join('') == document.center.gaeYakFm.tag &&
			document.center.gaeYakTo.value.split('-').join('') == document.center.gaeYakTo.tag &&
			document.center.sugupStatus.value == document.center.sugupStatus.tag &&
			document.center.boninYul.value == document.center.boninYul.tag){
			_sugupjaSave();
			return;
		}

		document.sugupCheck.startDate.value = document.getElementById('sDate').value;

		sugupLayer1.style.width  = document.body.offsetWidth;

		if (document.body.scrollHeight > document.body.offsetHeight){
			sugupLayer1.style.height = document.body.scrollHeight;
		}else{
			sugupLayer1.style.height = document.body.offsetHeight;
		}

		var tableLeft = (parseInt(__replace(sugupLayer1.style.width, 'px', '')) - parseInt(__replace(sugupTable.style.width, 'px', ''))) / 2+'px';
		var tableTop  = (parseInt(document.body.offsetHeight) - parseInt(__replace(sugupTable.style.height, 'px', ''))) / 2+'px';

		sugupLayer2.style.top     = tableTop;
		sugupLayer2.style.left    = tableLeft;
		sugupLayer2.style.width   = sugupTable.style.width;
		sugupLayer2.style.height  = sugupTable.style.height;
		sugupLayer2.style.display = '';
		sugupTable.style.display  = '';

		document.sugupCheck.startDate.focus();
	}
}

function _showPopup(object, mIndex, code, kind, jumin){
	var Popup   = document.getElementById('idPopup');
	var pageIndex = mIndex;

	_PopupData(object, Popup, pageIndex, code,kind, jumin);
}

function _PopupData(object, Popup, pageIndex, code, kind, jumin){
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

function __modal(param){
	modalWindow = showModalDialog('../inc/_modal.php?param='+param, window, 'dialogWidth:1000px; dialogHeight:700px; dialogHide:yes; scroll:no; status:yes');

	if (modalWindow == 'Y'){
		_sugupjaReg('suSearch',document.center.curMcode.value, document.center.curMkind.value,'', document.center.searchJname.value, document.center.searchTel.value,document.center.mKey.value);
	}
}
function showSugupjaLayerCancel(){
	sugupLayer1.style.width  = 0;
	sugupLayer1.style.height = 0;
	sugupLayer2.style.width  = 0;
	sugupLayer2.style.height = 0;
	sugupTable.style.display = 'none';
}

function showSugupjaSave(){
	document.getElementById('sDate').value = document.sugupCheck.startDate.value;
	_sugupjaSave();
}

// 본인부담 영수증
function _printPaymentsBill(pCode, pKind, pDate, pBoninYul, pKey){
	var width  = 900;
	var height = 700;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('../work/payments_bill_print.php?mCode='+pCode+'&mKind='+pKind+'&mDate='+pDate+'&mBoninYul='+pBoninYul+'&mKey='+pKey, 'POPUP_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

function popupDeposit(mCode, mKind, mKey){
	var width  = 500;
	var height = 400;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

popupAccount = window.open('../account/popup_deposit.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey, 'POPUP_DEPOSIT', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

</script>
<div id="sugupLayer1" style="z-index:0; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; position:absolute; top:0px; left:0px; height:0px; width:0px;"></div>
<div id="sugupLayer2" style="z-index:1; left:0; top:0; position:absolute; color:#000000;">
	<table id="sugupTable" style="width:400px; height:60px; background-color:#ffffff; display:none;">
	<tr>
	<td class="noborder" style="width:10px;"></td>
	<td class="title" style="width:400px;">적용일자 안내</td>
	</tr>
	<tr>
	<td class="noborder" style="width:10px;"></td>
	<td class="title" style="width:400px; padding-top:5px; padding-bottom:5px; line-height:1.2em;">
		장기요양등급, 수급자구분, 계약시작일 및 종료일, 수급현황, 담당요양보호사가 변경된 경우 적용기준일자를 입력하여 주십시오.
	</td>
	</tr>
	<tr>
	<td class="noborder" colspan="2">
		<form name="sugupCheck" method="post">
		<table style="width:100%;">
		<tr>
		<td style="width:20%; border:0px; border-bottom:1px dotted #cccccc; text-align:center; padding-left:15px;">적용 시작일</td>
		<td style="width:30%; border:0px; border-bottom:1px dotted #cccccc; text-align:center;"><input name="startDate" type="text" value="<?=subStr($sDate, 0, 4).'.'.subStr($sDate, 4, 2).'.'.subStr($sDate, 6, 2);?>" tag="<?=$sDate;?>" maxlength="8" class="phone" onFocus="__toNumber(this); this.select();" onBlur="__setDate(this);" onKeyDown="__onlyNumber(this);"></td>
		<td style="width:20%; border:0px; border-bottom:1px dotted #cccccc; text-align:center; padding-left:15px;">적용 종료일</td>
		<td style="width:30%; border:0px; border-bottom:1px dotted #cccccc; text-align:center;" id="endDate">9999.99.99</td>
		</tr>
		<tr>
		<td style="border:0px; padding-left:5px;" colspan="4">
			<a href="#" onClick="showSugupjaSave();"><img src="../image/btn9.gif"></a>
			<a href="#" onClick="showSugupjaLayerCancel();"><img src="../image/btn_cancel.png"></a>
		</td>
		</tr>
		</table>
		</form>
	</td>
	</tr>
	</table>
</div>