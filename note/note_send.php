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

	$code = $_SESSION['userCenterCode'];
	$mode = 1;
?>

<script language='javascript'>
<!--

function show_member(dept_cd){
	__object_set_value('chk_dept[]', dept_cd);

	var dept   = __object_check('chk_dept[]');
	var mem_tr = document.getElementById('member_'+dept_cd);

	if (dept.checked)
		mem_tr.style.visibility = 'visible';
	else
		mem_tr.style.visibility = 'hidden';
}

/*
 * 리스트 찾기
 */
function find_list(){
	var code      = document.getElementById('code').value;
	var send_type = __object_get_value('send_type');
	var param     = 'code='+code+'&send_type='+send_type;

	var modal = showModalDialog('note_list_find_1.php?'+param, window, 'dialogWidth:400px; dialogHeight:400px; dialogHide:yes; scroll:no; status:yes');

	if (modal == '') return;

	send_type_list(modal);
}

/*
 * 유형별 리스트
 */
function send_type_list(val, remove){
	var btn       = document.getElementById('group_btn');
	var code      = document.getElementById('code').value;
	var send_type = __object_get_value('send_type');

	if (val == undefined)
		val = '';

	if (send_type == 'all')
		btn.style.display = 'none';
	else
		btn.style.display = '';

	if (!remove){
		var obj = document.getElementsByName(send_type+'_cd[]');

		for(var i=0; i<obj.length; i++){
			val += obj[i].value + '//';
		}
	}

	try{
		var URL     = 'note_list_send.php';
		var params  = {'code':code,'send_type':send_type,'val':val};
		var xmlhttp = new Ajax.Request(
			URL, {
				method:'post',
				parameters:params,
				onSuccess:function(responseHttpObj){
					to_list.innerHTML = responseHttpObj.responseText;
				}
			}
		);
	}catch(e){
		__error_show(e);
	}
}

/*
 * 행삭제
 */
function delete_list(tbl, id){
	var tbl = document.getElementById(tbl);
	var row = document.getElementById(id);
	var idx = row.rowIndex;

	tbl.deleteRow(idx);
}

window.onload = function(){
	send_type_list();
	__init_form(document.f);
}

-->
</script>

<div class="title title_border">쪽지보내기</div>

<form name="f" method="post">

<div style="margin-left:10px; margin-top:10px; padding-bottom:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="last">
				<?
					echo '<input name=\'send_type\' type=\'radio\' class=\'radio\' value=\'all\' onclick=\'send_type_list();\' checked>전체';

					if ($_SESSION['userLevel'] == 'A'){
						//echo '<input name=\'send_type\' type=\'radio\' class=\'radio\' value=\'branch\' onclick=\'send_type_list();\'>지사별';
					}else if ($_SESSION['userLevel'] == 'B'){
					}else if ($_SESSION['userLevel'] == 'C'){
					}else if ($_SESSION['userLevel'] == 'P'){
					}else{
						echo '<script>
								alert(\'잘못된 경로로 진입하셨습니다. 메인화면으로 돌아갑니다.\');
								top.location.replace(\'http://'.$_SERVER['HTTP_HOST'].'\');
							  </script>';
					}

					if ($_SESSION['userLevel'] == 'A' ||
						$_SESSION['userLevel'] == 'B'){
						echo '<input name=\'send_type\' type=\'radio\' class=\'radio\' value=\'center\' onclick=\'send_type_list();\'>가맹점별';
					}

					echo '<input name=\'send_type\' type=\'radio\' class=\'radio\' value=\'dept\' onclick=\'send_type_list();\'>부서별';
					echo '<input name=\'send_type\' type=\'radio\' class=\'radio\' value=\'person\' onclick=\'send_type_list();\'>개별';
				?>
				</th>
				<th>
					<div id="group_btn" class="right">
						<a href="#" onclick="find_list();">찾아보기</a>
					</div>
				</th>
			</tr>
		</tbody>
	</table>
</div>

<div id="to_list" style="margin-left:10px; padding-bottom:10px;"></div>

<div style="margin-left:10px; padding-bottom:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="100px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th>제목</th>
				<td><input name="subject" type="text" value="" style="width:100%;"></td>
			</tr>
			<tr>
				<th>내욕</th>
				<td style="height:150px;">
					<link href="../editor/css/default.css" rel="stylesheet" type="text/css" />
					<script type="text/javascript" src="../editor/js/HuskyEZCreator.js" charset="utf-8"></script>

					<textarea name="content" id="content" style="width:100%; height:100%;" tag="내용을 입력하여 주십시오."><?= $notice['content'];?></textarea>
					<textarea id="back_content" name="back_content" style="display:none;"></textarea>

					<script>
						var form = document.f;

						var oEditors = [];

						nhn.husky.EZCreator.createInIFrame(oEditors, "content", "../editor/SEditorSkin.html", "createSEditorInIFrame", null, true);

						function insertIMG(fname){
							var filepath = form.filepath.value;
							var sHTML = "<img src='" + filepath + "/" + fname + "' style='cursor:hand;' border='0'>";
							oEditors.getById["content"].exec("PASTE_HTML", [sHTML]);
							}

						function pasteHTMLDemo(){
							sHTML = "<span style='color:#FF0000'>이미지 등도 이렇게 삽입하면 됩니다.</span>";
							oEditors.getById["content"].exec("PASTE_HTML", [sHTML]);
						}

						function showHTML(){
							alert(oEditors.getById["content"].getIR());
						}

						function onSubmit(){
							var send_type = __object_get_value('send_type');
							var obj = document.getElementsByName(send_type+'_cd[]');

							if (send_type != 'all'){
								if (obj.length == 0){
									alert('쪽지를 보낼 리스트를 작성하여 주십시오.');
									find_list();
									return;
								}
							}

							if (form.subject.value == ''){
								alert('제목을 입력하여 주십시오.');
								form.subject.focus();
								return;
							}

							oEditors.getById["content"].exec("UPDATE_IR_FIELD", []);

							form.back_content.value = document.getElementById("content").value;

							if(form.back_content.value == ""){
								alert("\'내용\'을 입력해 주세요");
								return;
							}

							form.action = 'note_send_ok.php';
							form.submit();
						}
					</script>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="margin-left:10px; padding-bottom:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="head">
					<a href="#" onclick="onSubmit();">보내기</a>
				</th>
			</tr>
		</tbody>
	</table>
</div>

<input name="code" type="hidden" value="<?=$code;?>">
<input name="mode" type="hidden" value="<?=$mode;?>">
<input name="filepath" type="hidden" value="upload">
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>