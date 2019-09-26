<?
	include_once('../inc/_header.php');
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/style.css" rel="stylesheet" type="text/css">
<script language='javascript'>
<!--
//엑셀
function excel(){
	var f = document.f;

	f.action = 'sugup_list_excel.php';
	f.submit();
}

function base_all_chk(obj){
	if(obj == true){
		f.chk_all.checked = true;
		f.sugupJumin.checked = true;
		f.manageNo.checked = true;
		f.addr.checked = true;
		f.sugupTel.checked = true;
		f.sugupMobile.checked = true;
	}else{
		f.chk_all.checked = false;
		f.sugupJumin.checked = false;
		f.manageNo.checked = false;
		f.addr.checked = false;
		f.sugupTel.checked = false;
		f.sugupMobile.checked = false;
	}
}

function dtl_all_chk(obj){
	
	if(obj == true){
		f.svcFm.checked = true;				//서비스시작일
		f.svcTo.checked = true;				//서비스종료일
		f.boninYul.checked = true;			//본인부담율
		f.boninGum.checked = true;			//본인부담금
		f.bohojaName.checked = true;		//보호자성명
		f.bohojaRel.checked = true;			//보호자관계
		f.bohojaTel.checked = true;			//보호자연락처
		f.bungName.checked = true;			//병명
		f.otherBungName.checked = true;		//기타병명
		f.level.checked = true;				//등급
		f.kupyeoMax.checked = true;			//급여한도액
		f.mainYoy.checked = true;			//주요양사
		f.partner.checked = true;			//배우자
		f.buYoy.checked = true;				//부요양사
		f.clientGbn.checked = true;			//고객구분
		f.familyGbn.checked = true;			//동거구분
		f.chunguMax.checked = true;			//청구한도액
		f.useService.checked = true;		//이용서비스
		f.useStatGbn.checked = true;		//이용상태(구분)
		f.useStatStop.checked = true;		//이용상태(중지사유)
		f.nintyExceed.checked = true;		//90분초과
		f.bathExceed.checked = true;		//목욕초과
		f.injungNo.checked = true;			//보험(인증번호)
		f.injungDt.checked = true;			//보험(유효기간)
		f.contractType.checked = true;		//계약유형
		f.memo.checked = true;				//메모
		f.memTeam.checked = true;			//담당팀장
		f.billPhone.checked = true;			//현금영수증발행연락처
		
	
	}else {
		f.svcFm.checked = false;			//서비스시작일
		f.svcTo.checked = false;			//서비스종료일
		f.boninYul.checked = false;			//본인부담율
		f.boninGum.checked = false;			//본인부담금
		f.bohojaName.checked = false;		//보호자성명
		f.bohojaRel.checked = false;		//보호자관계
		f.bohojaTel.checked = false;		//보호자연락처
		f.bungName.checked = false;			//병명
		f.otherBungName.checked = false;	//기타병명
		f.level.checked = false;			//등급
		f.kupyeoMax.checked = false;		//급여한도액
		f.mainYoy.checked = false;			//주요양사
		f.partner.checked = false;			//배우자
		f.buYoy.checked = false;			//부요양사
		f.clientGbn.checked = false;		//고객구분
		f.familyGbn.checked = false;			//동거구분
		f.chunguMax.checked = false;		//청구한도액
		f.useService.checked = false;		//이용서비스
		f.useStatGbn.checked = false;		//이용상태(구분)
		f.useStatStop.checked = false;		//이용상태(중지사유)
		f.nintyExceed.checked = false;		//90분초과
		f.bathExceed.checked = false;		//목욕초과
		f.injungNo.checked = false;			//보험(인증번호)
		f.injungDt.checked = false;			//보험(유효기간)
		f.contractType.checked = false;		//계약유형
		f.memo.checked = false;				//메모
		f.memTeam.checked = false;			//담당팀장
		f.billPhone.checked = false;		//현금영수증발행연락처
		
	}
}

window.onload = function(){
	self.focus();
}

//-->
</script>
<?
	$find_center_code	= $_REQUEST['fCode'];
	$find_center_name	= $_REQUEST['fCname'];
	$find_su_name		= $_REQUEST['fName'];
	$find_su_ssn		= $_REQUEST['fSsn'];
	$find_su_phone		= $_REQUEST['fPhone'];
	$find_su_stat		= $_REQUEST['fStat'];
	$find_center_kind   = $_REQUEST['fKind'];
	$order_sst			= $_REQUEST['sst'];
	$order_sod			= $_REQUEST['sod'];
	$order_sfl		    = $_REQUEST['sfl'];
	$find_team          = $_REQUEST['team'];
	
	$chk_all = $_POST['chk_all'] != '' ? $_POST['chk_all'] : 'Y';				//기본정보선택
	$sugupJumin = $_POST['sugupJumin'] != '' ? $_POST['sugupJumin'] : 'Y';		//주민번호
	$manageNo = $_POST['manageNo'] != '' ? $_POST['manageNo'] : 'Y';			//관리번호
	$addr = $_POST['addr'] != '' ? $_POST['addr'] : 'Y';						//주소
	$sugupTel = $_POST['sugupTel'] != '' ? $_POST['sugupTel'] : 'Y';			//일반전화
	$sugupMobile = $_POST['sugupMobile'] != '' ? $_POST['sugupMobile'] : 'Y';	//핸드폰

?>
<div class="title">수급자엑셀 추가항목</div>
<table class="my_table my_border" width="100%" style="margin-top:0;">
	<tr>
		<th style="text-align:right; padding-right:5px;"><span class="btn_pack m"><span class="excel"></span><button name="btnExcel" type="button" onFocus="this.blur();" onClick="excel();">Excel</button></span></th>
	</tr>
</table>
<form name="f" method="post">

<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:600px;">
<table class="my_border" style="margin-top:-1px;">
	<colgroup>
		<col width="15%"/>
		<col width="12%"/>
		<col width="10%"/>
		<col width="15%"/>
		<col width="12%"/>
		<col width="10%"/>
	</colgroup>
	<tr>
		<th colspan="6"><span style="font-weight:bold;">기본추가 항목</span><span style="margin-left:191;">기본항목 전체선택:&nbsp<input type="checkbox" class="checkbox" name="chk_all" value="Y" onclick="base_all_chk(this.checked);" <?if($chk_all=='Y'){ echo 'checked'; };?>></span></th>
	</tr>
	<tr>
		<th class="left" colspan="2">성명: </th>
		<td class="center"><input type="hidden" name="sugupName" value="Y"><span class="bold" style="color:#ff0000;">√</span></td>
		<th class="left" rowspan="2">연락처</th>
		<th class="left">일반전화: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="sugupTel" value="Y" <?if($sugupTel=='Y'){ echo 'checked'; };?>></td>	
	</tr>
	<tr>
		<th class="left" colspan="2">주민번호 </th>
		<td class="center"><input type="checkbox" class="checkbox" name="sugupJumin" value="Y" <?if($sugupJumin =='Y'){ echo 'checked'; };?>></td>
		<th class="left">핸드폰: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="sugupMobile" value="Y" <?if($sugupMobile=='Y'){ echo 'checked'; };?>></td>	
	</tr>
	<tr>
		<th class="left" colspan="2">관리번호: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="manageNo" value="Y" <?if($manageNo=='Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">주소: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="addr" value="Y" <?if($addr=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th colspan="6"><span style="font-weight:bold;">상세추가 항목</span><span style="margin-left:191;">상세항목 전체선택:&nbsp<input type="checkbox" class="checkbox" name="chk_all2" value="Y" onclick="dtl_all_chk(this.checked);" <?if($chk_all2=='Y'){ echo 'checked'; };?>></span></th>
	</tr>
	<tr>
		<th class="left" rowspan="2">이용상태</th>
		<th class="left">구분: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="useStatGbn" value="Y" <?if($useStatGbn == 'Y'){ echo 'checked'; };?>></td>
		<th class="center" rowspan="2">서비스</br>계약기간</th>
		<th class="left">시작: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="svcFm" value="Y" <?if($svcFm =='Y'){ echo 'checked'; };?>></td>	
	</tr>
	<tr>
		<th class="left">중지사유: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="useStatStop" value="Y" <?if($useStatStop =='Y'){ echo 'checked'; };?>></td>
		<th class="left">종료:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="svcTo" value="Y" <?if($svcTo=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" colspan="2">이용서비스: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="useService" value="Y" <?if($useService=='Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">등급:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="level" value="Y" <?if($level=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" colspan="2">고객구분:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="clientGbn" value="Y" <?if($clientGbn == 'Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">동거구분:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="familyGbn" value="Y" <?if($familyGbn == 'Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" rowspan="3">보호자</th>
		<th class="left">관계:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="bohojaRel" value="Y" <?if($bohojaRel == 'Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">본인부담율:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="boninYul" value="Y" <?if($boninYul=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left">성명: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="bohojaName" value="Y" <?if($bohojaName=='Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">본인부담금:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="boninGum" value="Y" <?if($boninGum=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left">연락처:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="bohojaTel" value="Y" <?if($bohojaTel=='Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">급여한도:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="kupyeoMax" value="Y" <?if($kupyeoMax=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" rowspan="3">서비스제공자</th>
		<th class="left">주담당:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="mainYoy" value="Y" <?if($mainYoy == 'Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">청구한도:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="chunguMax" value="Y" <?if($chunguMax=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left">배우자:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="partner" value="Y" <?if($partner=='Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">병명:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="bungName" value="Y" <?if($bungName == 'Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left">부담당:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="buYoy" value="Y" <?if($buYoy=='Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">기타병명:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="otherBungName" value="Y" <?if($otherBungName=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" rowspan="2">장기요양보험</th>
		<th class="left" >인정번호:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="injungNo" value="Y" <?if($injungNo == 'Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">목욕초과:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="bathExceed" value="Y" <?if($bathExceed=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" >유효기간:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="injungDt" value="Y" <?if($injungDt=='Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">90분초과:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="nintyExceed" value="Y" <?if($nintyExceed=='Y'){ echo 'checked'; };?>></td>	
	</tr>
	<tr>
		<th class="left" colspan="2">담당팀장</th>
		<td class="center"><input type="checkbox" class="checkbox" name="memTeam" value="Y" <?if($memTeam=='Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">현금영수증발행 연락처</th>
		<td class="center"><input type="checkbox" class="checkbox" name="billPhone" value="Y" <?if($billPhone=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" colspan="2">계약유형</th>
		<td class="center"><input type="checkbox" class="checkbox" name="contractType" value="Y" <?if($contractType=='Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">Memo:</th>
		<td class="center"><input type="checkbox" class="checkbox" name="memo" value="Y" <?if($memo=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<input name="find_center_code" type="hidden" value="<?=$find_center_code?>">
	<input name="find_center_name" type="hidden" value="<?=$find_center_name?>">
	<input name="find_center_kind" type="hidden" value="<?=$find_center_kind?>">
	<input name="find_su_name" type="hidden" value="<?=$find_su_name?>">
	<input name="find_su_ssn" type="hidden" value="<?=$find_su_ssn?>">
	<input name="find_su_phone" type="hidden" value="<?=$find_su_phone?>">
	<input name="find_su_stat" type="hidden" value="<?=$find_su_stat?>">
	<input name="order_sst" type="hidden" value="<?=$order_sst?>">
	<input name="order_sod" type="hidden" value="<?=$order_sod?>">
	<input name="order_sfl" type="hidden" value="<?=$order_sfl?>">
	<input name="find_team" type="hidden" value="<?=$find_team?>">
</table>
</div>
</form>
