<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code = $_SESSION['userCenterCode'];
?>

<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function set_csv(){
	var f = document.f;

	if (f.csv.value == ''){
		alert('입력할 TEXT파일을 선택하여 주십시오.');
		return;
	}

	var exp = f.csv.value.split('.');
		exp = exp[exp.length - 1];
		exp = exp.toLowerCase();

	if (exp != 'txt'){
		alert('입력할 TEXT파일을 선택하여 주십시오.');
		return;
	}

	window.open('', 'CSV_WINDOW', 'width=1100, height=600, scrollbars=no, resizable=yes');

	f.submit();
}

-->
</script>

<form name="f" method="post" action="result_csv_upload.php" enctype="multipart/form-data" target="CSV_WINDOW">

<div class="title title_border">실적 등록(TEXT)</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="left last bottom" style="padding:20px 20px 0 20px;">
				<div class="my_border_blue bold" style="padding:10px;">
					<div>* 건보공단에서 다운로드받은 TEXT파일을 업로드하여 주십시오.</div>
					<div style="width:auto; float:left; font-weight:bold;">* TEXT 파일명</div>
					<div style="width:auto; float:left;">
						<input name="csv" type="file" style="width:270px; background-color:#ffffff;">
						<span class="btn_pack m"><button type="button" class="bold" onclick="set_csv();">확인</button></span>
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code" value="<?=$code;?>">

<iframe id="text_help" src="http://www.carevisit.net/help/result/help.html" frameborder="0" width="100%" height="400" scrolling="no" align="center" style="margin:20px; border:2px solid #0e69b0; display:;"></iframe>

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>