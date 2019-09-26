<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');
	
	$code      = $_SESSION['userCenterCode'];
	$kind      = $_REQUEST['kind'];
	$center_nm = $conn->center_name($code, $kind);
	$page      = $_REQUEST['page'];
	$ssn       = $ed->de($_REQUEST['ssn']);
	$client_nm = $conn->client_name($code, $ssn, $kind);
	$year      = $_REQUEST['year'];
	$month     = $_REQUEST['month'];
	$month     = (intval($month) < 10 ? '0' : '').intval($month);

	$sql = "select desire_status as desire_1
			,      desire_content as desire_2
			,      desire_service as desire_3
			,	   desire_subject1 as subject_1
			,	   desire_subject2 as subject_2
			,	   desire_subject3 as subject_3
			  from counsel_client_desire
			 where org_no      = '$code'
			   and desire_ssn  = '$ssn'
			   and desire_yymm = '$year$month'";

	$desire = $conn->get_array($sql);

	if (!$desire)
		$write_mode = 1;
	else
		$write_mode = 2;
?>
<script language='javascript'>
<!--

function list(page){
	var f = document.f;

	f.page.value = page;
	f.action = 'client_desire.php';
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
}

//-->
</script>

<form name="f" method="post">

<div class="title">수급자 욕구상담</div>

<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="130px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td class="left"><?=$code;?></td>
			<th>기관명</th>
			<td class="left last"><?=$center_nm;?></td>
		</tr>
		<tr>
			<th>년월</th>
			<td class="left"><?=$year;?>.<?=$month;?></td>
			<th>수급자</th>
			<td class="left last"><?=$client_nm;?></td>
		</tr>
	</tbody>
</table>

<link href="../editor/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../editor/js/HuskyEZCreator.js" charset="utf-8"></script>

<script>
	var form = document.f;
	var oEditors = [];

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

	function save(){
		oEditors.getById["desire_1"].exec("UPDATE_IR_FIELD", []);
		oEditors.getById["desire_2"].exec("UPDATE_IR_FIELD", []);
		oEditors.getById["desire_3"].exec("UPDATE_IR_FIELD", []);

		form.back_desire_1.value = document.getElementById("desire_1").value;
		form.back_desire_2.value = document.getElementById("desire_2").value;
		form.back_desire_3.value = document.getElementById("desire_3").value;

		form.action = 'client_desire_save.php';
		form.submit();
	}

	function del(){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./client_desire_delete.php'
		,	data :{
				'ssn':'<?=$ed->en($ssn);?>'
			,	'yymm':'<?=$year.$month;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					list(<?=$page;?>);
				}else if (result == 9){
					alert('처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<textarea id="back_desire_1" name="back_desire_1" style="display:none;"></textarea>
<textarea id="back_desire_2" name="back_desire_2" style="display:none;"></textarea>
<textarea id="back_desire_3" name="back_desire_3" style="display:none;"></textarea>

<table class="my_table" style="width:100%;"><?
if ($code == '31141000005' || //어르신을 편안하게 돌보는 사람
	$code == '31141000159' ){ //여민복지협동조합
	?>
	<colgroup>
		<col width="5%">
		<col width="28%">
		<col width="5%">
		<col width="28%">
		<col width="5%">
		<col width="28%">
	</colgroup>
	<thead>
		<tr>
			<th class="head" colspan="2">표준장기요양이용계획</th>
			<th class="head" colspan="2">현상/욕구평가</th>
			<th class="head last" colspan="2">급여제공계획</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="head">목표</th>
			<td ><textarea name="desire_subject1" id="desire_subject1" style="width:100%; height:40px; overflow-y:hidden;" onkeypress=''><?=$desire['subject_1'];?></textarea></td>
			<th class="head">욕구</th>
			<td><textarea name="desire_subject2" id="desire_subject2" style="width:100%; height:40px; overflow-y:hidden;"><?=$desire['subject_2'];?></textarea></td>
			<th class="head">목표</th>
			<td><textarea name="desire_subject3" id="desire_subject3" style="width:100%; height:40px; overflow-y:hidden;"><?=$desire['subject_3'];?></textarea></td>
		</tr>
		<tr>
			<td class="center top" style="padding:1px 1px 0 1px;" colspan="2">
				<textarea name="desire_2" id="desire_2" style="width:100%; height:150px; "><?=stripslashes($desire['desire_2']);?></textarea>
				<script>nhn.husky.EZCreator.createInIFrame(oEditors, "desire_2", "../editor/SimpleSkin.html", "createSEditorInIFrame", null);</script>
			</td>
			<td class="center top" style="padding:1px 1px 0 1px;" colspan="2">
				<textarea name="desire_1" id="desire_1" style="width:100%; height:150px;"><?=stripslashes($desire['desire_1']);?></textarea>
				<script>nhn.husky.EZCreator.createInIFrame(oEditors, "desire_1", "../editor/SimpleSkin.html", "createSEditorInIFrame", null);</script>
			</td>
			<td class="center top last" style="padding:1px 1px 0 1px;" colspan="2">
				<textarea name="desire_3" id="desire_3" style="width:100%; height:150px;"><?=stripslashes($desire['desire_3']);?></textarea>
				<script>nhn.husky.EZCreator.createInIFrame(oEditors, "desire_3", "../editor/SimpleSkin.html", "createSEditorInIFrame", null);</script>
			</td>
		</tr>
		<!--tr>
			<th class="head">비용</th>
			<td class="left last" colspan="5"><input name="desire_text" value="<?=$desire['text']?>" style="width:120px;" /></td>
		</tr-->
	</tbody><?
}else { ?>
	<colgroup>
		<col width="33%" span="3">
	</colgroup>
	<thead>
		<tr>
			<th class="head">수급자 현상태/욕구평가</th>
			<th class="head">표준장기요양 필요내용</th>
			<th class="head last">요양보호사 서비스내용</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top" style="padding:1px 1px 0 1px;">
				<textarea name="desire_1" id="desire_1" style="width:100%; height:150px;"><?=stripslashes($desire['desire_1']);?></textarea>
				<script>nhn.husky.EZCreator.createInIFrame(oEditors, "desire_1", "../editor/SimpleSkin.html", "createSEditorInIFrame", null);</script>
			</td>
			<td class="center top" style="padding:1px 1px 0 1px;">
				<textarea name="desire_2" id="desire_2" style="width:100%; height:150px;"><?=stripslashes($desire['desire_2']);?></textarea>
				<script>nhn.husky.EZCreator.createInIFrame(oEditors, "desire_2", "../editor/SimpleSkin.html", "createSEditorInIFrame", null);</script>
			</td>
			<td class="center top last" style="padding:1px 1px 0 1px;">
				<textarea name="desire_3" id="desire_3" style="width:100%; height:150px;"><?=stripslashes($desire['desire_3']);?></textarea>
				<script>nhn.husky.EZCreator.createInIFrame(oEditors, "desire_3", "../editor/SimpleSkin.html", "createSEditorInIFrame", null);</script>
			</td>
		</tr>
	</tbody><?
} ?>
	<tbody>
		<tr>
			<td class="right last" colspan="6">
				<a href="#" onclick="list(<?=$page;?>);">리스트</a> |
				<a href="#" onclick="save();">저장</a> |
				<a href="#" onclick="del();">삭제</a>
			</td>
		</tr>
	</tbody>
</table>

<input name="code" type="hidden" value="<?=$code;?>">
<input name="kind" type="hidden" value="<?=$kind;?>">
<input name="page" type="hidden" value="<?=$page;?>">
<input name="ssn" type="hidden" value="<?=$ed->en($ssn);?>">
<input name="year" type="hidden" value="<?=$year;?>">
<input name="month" type="hidden" value="<?=$month;?>">
<input name="write_mode" type="hidden" value="<?=$write_mode;?>">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>

