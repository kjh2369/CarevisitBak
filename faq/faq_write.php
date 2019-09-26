<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		if (opener.id > 0){
			lfSearch();
		}
	});

	function lfSearch(){
	}
</script>

<div class="title title_border">FAQ 작성</div>
<form id="f" name="f" method="post" enctype="multipart/form-data">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">구분</th>
			<td>
				<select id="cobGbn" name="cbo" style="width:auto;"><?
				$sql = 'SELECT gbn_cd AS cd
						,      gbn_nm AS nm
						  FROM faq_gbn
						 WHERE del_flag = \'N\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);?>
					<option value="<?=$row['cd'];?>"><?=$row['nm'];?></option><?
				}

				$conn->row_free();?>
				</select>
			</td>
		</tr>
		<tr>
			<th class="center">제목</th>
			<td><input id="txtSubject" name="txt" type="text" style="width:100%;"></td>
		</tr>
		<tr>
			<th class="center">작성자</th>
			<td><input id="txtWriter" name="txt" type="text"></td>
		</tr>
		<tr>
			<th class="center">내용</th>
			<td style="height:270px; padding:1px 1px 0 1px;">
				<link href="../editor/css/default.css" rel="stylesheet" type="text/css" />
				<script type="text/javascript" src="../editor/js/HuskyEZCreator.js" charset="utf-8"></script>

				<textarea name="ir1" id="ir1" style="width:100%; height:100%;" tag="내용을 입력하여 주십시오."><?=$content;?></textarea>
				<textarea id="back_content" name="back_content" style="display:none;"></textarea>

				<script>
					var form = document.f;

					var oEditors = [];

					nhn.husky.EZCreator.createInIFrame(oEditors, "ir1", "../editor/SEditorSkin.html", "createSEditorInIFrame", null, false);

					function insertIMG(fname){
						var filepath = form.filepath.value;
						var sHTML = "<img src='" + filepath + "/" + fname + "' style='cursor:hand;' border='0'>";
						oEditors.getById["ir1"].exec("PASTE_HTML", [sHTML]);
					}

					function pasteHTMLDemo(){
						sHTML = "<span style='color:#FF0000'>이미지 등도 이렇게 삽입하면 됩니다.</span>";
						oEditors.getById["ir1"].exec("PASTE_HTML", [sHTML]);
					}

					function showHTML(){
						alert(oEditors.getById["ir1"].getIR());
					}

					function onSubmit(){
						if (form.txtSubject.value == ''){
							alert('제목을 입력하여 주십시오.');
							form.txtSubject.focus();
							return;
						}

						oEditors.getById["ir1"].exec("UPDATE_IR_FIELD", []);

						form.back_content.value = document.getElementById("ir1").value;

						if(form.back_content.value == ""){
							alert("\'내용\'을 입력해 주세요");
							return;
						}

						$.ajax({
							type: 'POST'
						,	url : './faq_write_ok.php'
						,	data: {
								'gbn':$('#cobGbn option:selected').val()
							,	'subject':$('#txtSubject').val()
							,	'content':form.back_content.value
							,	'write':$('#txtWriter').val()
							}
						,	success: function (result){
								if (result == 9){
									alert('등록에러');
									return false;
								}

								alert(result);
							}
						,	error: function (request, status, error){
								alert('[ERROR]'
									 +'\nCODE : ' + request.status
									 +'\nSTAT : ' + status
									 +'\nMESSAGE : ' + request.responseText);
							}
						});
					}
				</script>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="center bottom" colspan="2">
				<span class="btn_pack m"><button type="button" onclick="onSubmit();">저장</button></span>
				<span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span>
			</td>
		</tr>
	</tfoot>
</table>
<input type="hidden" name="filepath" value="upload">
</form>
<?
	include_once('../inc/_footer.php');
?>