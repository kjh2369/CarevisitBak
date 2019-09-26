<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code  = $_SESSION['userCenterCode'];
	$year  = date('Y', mktime());
	$month = intval(date('m', mktime()));
?>

<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function set_data(){
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

	window.open('', 'CSV_WINDOW', 'width=350, height=300, left='+((screen.width - 350) / 2)+', top='+((screen.height - 300) / 2)+', scrollbars=no, resizable=yes');

	f.action = 'nhic_upload.php';
	f.submit();
}

function set_month(month){
	for(var i=1; i<=12; i++){
		var obj = document.getElementById('btnMonth_'+i);

		if (i == month)
			obj.className = 'my_month my_month_y';
		else
			obj.className = 'my_month my_month_1';
	}

	document.getElementById('month').value = month;

	var param   = {'code':document.getElementById('code').value
				  ,'year':document.getElementById('year').value
				  ,'month':document.getElementById('month').value
				  ,'mode':1};
	var URL     = '../nhic/nhic_mstlog_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function(responseHttpObj){
				var body = document.getElementById('my_log');
					body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

function show_apply_list(date, seq){
	var f = document.f;

	f.date.value = date;
	f.seq.value  = seq;

	window.open('', 'CSV_WINDOW', 'width=1100, height=600, left='+((screen.width - 1100) / 2)+', top='+((screen.height - 600) / 2)+', scrollbars=no, resizable=yes');

	f.action = 'nhic_apply.php';
	f.submit();
}

window.onload = function(){
	set_month(document.getElementById('month').value);
}

-->
</script>

<form name="f" method="post" enctype="multipart/form-data" target="CSV_WINDOW">

<div class="title title_border">건보 실적 등록(TEXT)</div>

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
						<span class="btn_pack m"><button type="button" class="bold" onclick="set_data();">Upload</button></span>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="left last bottom" style="padding:20px 20px 0 20px;">
				<div class="my_border_blue bold" style="">
					<table class='my_table' style='width:100%;'>
						<colgroup>
							<col width='70px'>
							<col width='80px'>
							<col>
						</colgroup>
						<tbody>
							<tr>
								<th class='head'>등록관리</th>
								<td class=''>
								<?
									$init_year = $myF->year();

									echo '<select name=\'year\' style=\'width:auto;\'>';

									for($i=$init_year[0]; $i<=$init_year[1]; $i++){
										echo '<option value=\''.$i.'\' '.($i == $year ? 'selected' : '').'>'.$i.'</option>';
									}

									echo '</select>년';
								?>
								</td>
								<td class='' style='padding:1px 0 0 5px;'><? echo $myF->_btn_month($month, 'set_month(', ');');?></td>
							</tr>
						</tbody>
					</table>
					<div id='my_log'></div>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code" value="<?=$code;?>">
<input type='hidden' name='month' value='<?=$month;?>'>
<input type='hidden' name='date' value=''>
<input type='hidden' name='seq' value=''>

<iframe id="text_help" src="../help/result/help.html" frameborder="0" width="100%" height="400" scrolling="no" align="center" style="margin:20px; border:2px solid #0e69b0; display:;"></iframe>

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>