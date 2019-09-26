<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_ed.php");
	include_once('../inc/_myFun.php');
	
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
?>
<script src="../js/script.js" type="text/javascript"></script>
<LINK REL="stylesheet" type="text/css" href="../css/style.css">
<script language='javascript'>
<!--
	function _counsel(){
	
	if (document.counsel.c_name.value == ''){
		alert('이름을 입력하여 주십시오.');
		document.counsel.c_name.focus();
		return;
	}

	if (document.counsel.c_phone.value == ''){
		alert('연락처를 입력하여 주십시오.');
		document.f.c_phone.focus();
		return;
	}
	
	if (document.counsel.c_mail.value == ''){
		alert('E-mail 주소를 입력하여 주십시오.');
		document.f.c_mail.focus();
		return;
	}

	if (document.counsel.c_content.value == ''){
		alert('상담내용을 입력하여 주십시오.');
		document.f.c_mail.focus();
		return;
	}
	
	document.counsel.action = 'counsel_ok.php';
	document.counsel.submit();
}
	
//-->
</script>
<?
	$sql = "select c_name"
		 . ",      c_dt"
		 . ",      c_phone"
		 . ",      c_mail"
		 . "  from counsel"
		 . " where c_id = '".$c_id
		 . "'";
	$counsel = $conn->get_array($sql);



	
?>
<!--

-->
<form name="counsel" method="post">
<table class="my_table my_border" style="width:100%">
<colgroup>
	<col width="20%">
	<col width="30%">
	<col width="20%">
	<col width="30%">
</colgroup>
<tbody>
	<tr>
		<th class="center" colspan="4" style="height:30px; font size:13pt; font-weight:bold;">비회원 상담요청</th>	
	</tr>
	<tr>
		<th class="left">이 름</th>
		<td>
			<input name="c_name" type="text"  style="width:100%; ime-mode:active;" value="<?=$counsel['c_name'];?>">
		</td>
		<th class="left">연락처</th>
		<td>
			<input name="c_phone" type="text" value="<?=$counsel['c_phone'];?>" maxlength="11" style="ime-mode:disabled; margin-left:3px;" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);">
		</td>
	</tr>
	<tr>
		<th class="left">E-mail 주소</th>
		<td colspan="3">
			<input name="c_mail" type="text" style="width:100%; ime-mode:inactive;" value="<?=$counsel['c_mail'];?>">
		</td>
	</tr>
	<tr>
		<th class="left">내 용</th>
		<td class="left" colspan="3" style="height:200px; padding-right:6px;">
			<textarea name="c_content" style="width:100%; height:100%; ime-mode:active;"><?=$counsel['c_content'];?></textarea>
		</td>
	</tr>
	<tr>
		<td class="right" colspan="5">
			<span class="btn_pack m icon"><span class="save"></span><button type="button" onFocus="this.blur();" onClick="_counsel();">저장</button></span>
			<span class="btn_pack m icon"><span class="delete"></span><button type="button" onFocus="this.blur();" onClick="window.close();">닫기</button></span>
		</td>
	</tr>
</tbody>
</table>
</form>
<input name="c_id" type="hidden" value="<?=$c_id;?>">
<?
	include_once('../inc/_db_close.php');
?>
<script>self.focus();</script>