<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code	= $_SESSION['userCenterCode'];
	$kind	= $_SESSION['userCenterKind'][0];

	$find_name		= $_POST['find_name'];
	$find_unpaid	= $_POST['find_unpaid'];
	$init_year		= $myF->year();

	if (!$find_unpaid) $find_unpaid = '2';
?>

<script src="../js/account.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function list(){
	var f = document.f;

	f.submit();
}

function reg(jumin){
	var f = document.f;

	f.jumin.value = jumin;
	f.action = 'unpaid_reg.php';
	f.submit();
}

function detail(jumin){
	var f = document.f;

	f.jumin.value = jumin;
	f.action = 'unpaid_detail_list.php';
	f.submit();
}

function excel(){
	var f = document.f;

	f.action = 'unpaid_list_excel.php';
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
}

-->
</script>

<form name="f" method="post">

<div class="title">미수금 입금처리</div>

<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="80px">
		<col width="40px">
		<col width="80px">
		<col span="2">
	</colgroup>
	<tbody>
		<tr>
			<th class="head">수급자명</th>
			<td>
				<input name="find_name" type="text" value="<?=$find_name;?>">
			</td>
			<th class="head">구분</th>
			<td>
				<select name="find_unpaid" style="width:auto;">
					<option value="1" <? if($find_unpaid == '1'){?>selected<?} ?>>전체 출력</option>
					<option value="2" <? if($find_unpaid == '2'){?>selected<?} ?>>미수금있는 사람만 출력</option>
				</select>
			</td>
			<td class="left last">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="list();">조회</button></span>
			</td>
			<td class="right last">
				<span class="btn_pack m icon"><span class="excel"></span><button type="button" onclick="excel();">엑셀</button></span>
			</td>
		</tr>
	</tbody>
</table>

<?
	include_once('unpaid_list_sub.php');
?>

<input type="hidden" name="code"   value="<?=$code;?>">
<input type="hidden" name="kind"   value="<?=$kind;?>">
<input type="hidden" name="jumin"  value="">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>