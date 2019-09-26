<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_ed.php");
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	$id = $_POST['id'];
	$page = $_POST["page"];
	$find_type  = $_REQUEST['f_type'];
	$find_text  = $_REQUEST['f_text'];

	$sql = "update counsel
			   set c_count = c_count + 1
			 where c_id	= '$id'";
	$conn->execute($sql);


	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
?>
<script type="text/javascript" src="../js/script.js" ></script>
<script type="text/javascript" src="../js/goodeos.js"></script>
<LINK REL="stylesheet" type="text/css" href="../css/style.css">
<script language='javascript'>
<!--

function list(page){
	var f = document.f;

	f.page.value = page;
	f.f_type.value;
	f.f_text.value;
	f.action = 'visit_quest_list.php';
	f.submit();
}

function update_answer(){
	var id = document.f.id.value;
	var phone = document.f.phone.checked?'Y':'N';
	var mail = document.f.mail.checked?'Y':'N';

	var URL = 'visit_quest_save.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				id:id,
				phone:phone,
				mail:mail
			},
			onSuccess:function (responseHttpObj) {
				//alert(responseHttpObj.responseText);
			}
		}
	);
}

//-->
</script>
<!--

-->
<form name="f" method="post">
<div class="title" style="width:auto; float:left;">비회원문의</div>
<div style="width:auto; float:right; margin-top:8px;">
	<span class="btn_pack m"><button type="button" onclick="list(<?=$page;?>);">이전</button></span>
</div>
<table class="my_table my_border" style="width:100%">
<colgroup>
	<col width="20%">
	<col width="30%">
	<col width="20%">
	<col width="30%">
</colgroup>
<?
	$sql = "select c_id as id"
			 . ",	   c_name as name"
			 . ",	   c_dt as date"
			 . ",	   c_phone as phone"
			 . ",	   c_mail as mail"
			 . ",	   c_content as content"
			 . ",      c_answer_gbn as answergbn"
			 . "  from counsel"
			 . " where c_id = '".$id
			 . "' order by c_dt";

	$counsel = $conn->get_array($sql);

	?>
<tbody>
	<!--<tr>
		<th class="center" colspan="4" style="height:30px; font size:13pt; font-weight:bold;">비회원 상담요청</th>
	</tr>-->
	<tr>
		<th class="left">작성자</th>
		<td style="text-align:left;">&nbsp;&nbsp;<?=$counsel['name']?></td>
		<th class="left">일 자</th>
		<td class="left"><?=$counsel['date'];?></td>
	</tr>
	<tr>
		<th class="left">핸드폰번호</th>
		<td style="text-align:left;">&nbsp;&nbsp;<?=$myF->phoneStyle($counsel['phone'])?></td>
		<th class="left">E-mail 주소</th>
		<td class="last" style="text-align:left;">&nbsp;&nbsp;<?=$counsel['mail']?></td>
	</tr>
	<tr>
		<th class="left">답 변</th>
		<td class="last"style="text-align:left;" colspan="3">
			<input name="phone" type="checkbox" class="checkbox" value="N" onClick="update_answer()"<? if($counsel['answergbn'] == '1' or $counsel['answergbn'] == '3'){echo "checked";}?>>1. 전화
			<input name="mail" type="checkbox" class="checkbox" value="N" onClick="update_answer()"<? if($counsel['answergbn'] == '2' or $counsel['answergbn'] == '3'){echo "checked";}?>>2. 메일
		</td>
	</tr>
	<tr>
		<th colspan="4" class="center last">내 용</th>
	</tr>
	<tr>
		<td colspan="4" class="last"style="height:300px; padding:5px; vertical-align:top;"><?=nl2br($counsel['content']);?></td>
	</tr>
</tbody>
</table>
<!--<input name="id" type="hidden" value="<?=$row['c_id']?>">-->
<input name="id" type="hidden" value="<?=$id?>">
<input name="page"	type="hidden" value="<?=$page;?>">
<input name="f_type" type="hidden" value="<?=$find_type;?>">
<input name="f_text" type="hidden" value="<?=$find_text;?>">


</form>
<div style="width:auto; float:right; margin-top:8px; margin-bottom:8px;">
<span class="btn_pack m"><button type="button" onclick="_delete_visit_quest();">삭제</button></span>
<span class="btn_pack m"><button type="button" onclick="list(<?=$page;?>);">이전</button></span>
</div>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
	include_once('../inc/_db_close.php');
?>
<script>self.focus();</script>