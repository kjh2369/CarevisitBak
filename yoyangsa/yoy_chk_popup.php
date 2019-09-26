<?
	include_once('../inc/_header.php');

?>
<script language='javascript'>
<!--
//엑셀
function excel(){
	var f = document.f;

	f.action = 'yoy_list_excel.php';
	f.submit();
}
function base_all_chk(obj){
	if(obj == true){
		f.jumin.checked = true;
		f.addr.checked = true;
		f.tel.checked = true;
		f.mobile.checked = true;
		f.memNo.checked = true;
		f.userID.checked = true;
		f.dept.checked = true;
		f.jobNm.checked = true;
	}else{
		f.jumin.checked = false;
		f.addr.checked = false;
		f.tel.checked = false;
		f.mobile.checked = false;
		f.memNo.checked = false;
		f.userID.checked = false;
		f.dept.checked = false;
		f.jobNm.checked = false;
	}
}

function dtl_all_chk(obj){
	if(obj == true){
		f.tele.checked = true;
		f.rfid_yn.checked = true;
		f.rfid_no.checked = true;
		f.yipsail.checked = true;
		f.toisail.checked = true;
		f.bank_name.checked = true;
		f.bank_account.checked = true;
		f.bohum.checked = true;
		f.jaguk_kind.checked = true;
		f.jaguk_no.checked = true;
		f.jaguk_date.checked = true;
		f.extend.checked = true;
		f.holiday.checked = true;
		f.gikup.checked = true;
		f.general.checked = true;
		f.fam.checked = true;
		f.oldman.checked = true;
		f.housework.checked = true;
		f.puerperd.checked = true;
		f.disability.checked = true;
		f.from_date.checked = true;
		f.to_date.checked = true;
		f.mobile_work.checked = true;
		f.memo.checked = true;
		f.goyong_type.checked = true;
		f.goyong_stat.checked = true;
		f.standard_time.checked = true;
		f.standard_sigup.checked = true;
		f.week.checked = true;
		f.resign_yn.checked = true;
		f.resign_date.checked = true;
		f.familyYn.checked = true;
		f.demantiaYn.checked = true;
	}else {
		f.tele.checked = false;
		f.rfid_yn.checked = false;
		f.rfid_no.checked = false;
		f.yipsail.checked = false;
		f.toisail.checked = false;
		f.bank_name.checked = false;
		f.bank_account.checked = false;
		f.bohum.checked = false;
		f.jaguk_kind.checked = false;
		f.jaguk_no.checked = false;
		f.jaguk_date.checked = false;
		f.extend.checked = false;
		f.holiday.checked = false;
		f.gikup.checked = false;
		f.general.checked = false;
		f.fam.checked = false;
		f.oldman.checked = false;
		f.housework.checked = false;
		f.puerperd.checked = false;
		f.disability.checked = false;
		f.from_date.checked = false;
		f.to_date.checked = false;
		f.mobile_work.checked = false;
		f.memo.checked = false;
		f.goyong_type.checked = false;
		f.goyong_stat.checked = false;
		f.standard_time.checked = false;
		f.standard_sigup.checked = false;
		f.week.checked = false;
		f.resign_yn.checked = false;
		f.resign_date.checked = false;
		f.familyYn.checked = true;
		f.demantiaYn.checked = true;
	}
}

window.onload = function(){
	self.focus();
}
//-->
</script>
<?
	$find_center_name	= $_REQUEST['fCname'];
	$find_yoy_name		= $_REQUEST['fName'];
	$find_yoy_ssn		= $_REQUEST['fSsn'];
	$find_yoy_phone		= $_REQUEST['fPhone'];
	$find_yoy_stat		= $_REQUEST['fStat'];
	$find_dept			= $_REQUEST['fDept'];

	$base_all = $_POST['base_all'] != '' ? $_POST['base_all'] : 'Y';
	$jumin = $_POST['jumin'] != '' ? $_POST['jumin'] : 'Y';
	$manageNo = $_POST['manageNo'] != '' ? $_POST['manageNo'] : 'Y';
	$addr = $_POST['addr'] != '' ? $_POST['addr'] : 'Y';
	$mobile = $_POST['mobile'] != '' ? $_POST['mobile'] : 'Y';
	$tel = $_POST['tel'] != '' ? $_POST['tel'] : 'Y';
	$memNo = $_POST['memNo'] != '' ? $_POST['memNo'] : 'Y';
	$userID = $_POST['userID'] != '' ? $_POST['userID'] : 'Y';
	$dept = $_POST['dept'] != '' ? $_POST['dept'] : 'Y';
	$jobNm = $_POST['jobNm'] != '' ? $_POST['jobNm'] : 'Y';
?>
<div class="title">직원엑셀 추가항목</div>
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
		<th colspan="6"><span style="font-weight:bold;">기본추가 항목</span><span style="margin-left:257;">기본항목 전체선택:&nbsp<input type="checkbox" class="checkbox" name="base_all" value="Y" onclick="base_all_chk(this.checked);" <?if($base_all=='Y'){ echo 'checked'; };?>></span></th>
	</tr>
	<tr>
		<th class="left" colspan="2">성명: </th>
		<td class="center"><input type="hidden" name="name" value="Y"><span class=\'bold\' style=\'color:#ff0000;\'>√</span></td>
		<th class="left" colspan="2">주소: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="addr" value="Y" <?if($addr=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" colspan="2">주민번호: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="jumin" value="Y" <?if($jumin=='Y'){ echo 'checked'; };?>></td>
		<th class="left" rowspan="2">연락처</th>
		<th class="left">유선 </th>
		<td class="center"><input type="checkbox" class="checkbox" name="tel" value="Y" <?if($tel=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" colspan="2">사번: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="memNo" value="Y" <?if($memNo == 'Y'){ echo 'checked'; };?>></td>
		<th class="left" >무선 </th>
		<td class="center"><input type="checkbox" class="checkbox" name="mobile" value="Y" <?if($mobile=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" colspan="2">사용자ID: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="userID" value="Y" <?if($userID=='Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">부서: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="dept" value="Y" <?if($dept == 'Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" colspan="2">직무: </th>
		<td class="center"><input type="checkbox" class="checkbox" name="jobNm" value="Y" <?if($jobNm=='Y'){ echo 'checked'; };?>></td>
		<td class="center" colspan="3"></td>
	</tr>
	<tr>
		<th colspan="6"><span style="font-weight:bold;">상세추가 항목</span><span style="margin-left:257;">상세항목 전체선택:&nbsp<input type="checkbox" class="checkbox" name="dtl_all" value="Y" onclick="dtl_all_chk(this.checked);" <?if($dtl_all=='Y'){ echo 'checked'; };?>></span></th>
	</tr>
	<tr>
		<th class="left" rowspan="2">입사/퇴사 일자</th>
		<th class="left" >입사</th>
		<td class="center"><input type="checkbox" class="checkbox" name="yipsail" value="Y" <?if($yipsail=='Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">통신사</th>
		<td class="center"><input type="checkbox" class="checkbox" name="tele" value="Y" <?if($tele=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" >퇴사</th>
		<td class="center"><input type="checkbox" class="checkbox" name="toisail" value="Y" <?if($toisail=='Y'){ echo 'checked'; };?>></td>
		<th class="left" rowspan="2">RFID </th>
		<th class="left" >유.무</th>
		<td class="center"><input type="checkbox" class="checkbox" name="rfid_yn" value="Y" <?if($rfid_yn=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" rowspan="5">고용정보</th>
		<th class="left" >형태</th>
		<td class="center"><input type="checkbox" class="checkbox" name="goyong_type" value="Y" <?if($goyong_type=='Y'){ echo 'checked'; };?>></td>
		<th class="left" >번호</th>
		<td class="center"><input type="checkbox" class="checkbox" name="rfid_no" value="Y" <?if($rfid_no == 'Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" >상태</th>
		<td class="center"><input type="checkbox" class="checkbox" name="goyong_stat" value="Y" <?if($goyong_stat=='Y'){ echo 'checked'; };?>></td>
		<th class="left" rowspan="2">퇴직금중간정산</th>
		<th class="left" >여부</th>
		<td class="center"><input type="checkbox" class="checkbox" name="resign_yn" value="Y" <?if($resign_yn=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" >기준시간</th>
		<td class="center"><input type="checkbox" class="checkbox" name="standard_time" value="Y" <?if($standard_time=='Y'){ echo 'checked'; };?>></td>
		<th class="left" >정산일자</th>
		<td class="center"><input type="checkbox" class="checkbox" name="resign_date" value="Y" <?if($resign_date == 'Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" >기준시급</th>
		<td class="center"><input type="checkbox" class="checkbox" name="standard_sigup" value="Y" <?if($standard_sigup=='Y'){ echo 'checked'; };?>></td>
		<th class="left" rowspan="2">급여지급은행</th>
		<th class="left" >은행명</th>
		<td class="center"><input type="checkbox" class="checkbox" name="bank_name" value="Y" <?if($bank_name=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" >주휴요일</th>
		<td class="center"><input type="checkbox" class="checkbox" name="week" value="Y" <?if($week=='Y'){ echo 'checked'; };?>></td>
		<th class="left" >계좌번호</th>
		<td class="center"><input type="checkbox" class="checkbox" name="bank_account" value="Y" <?if($bank_account == 'Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" colspan="2">4대보험정보</th>
		<td class="center"><input type="checkbox" class="checkbox" name="bohum" value="Y" <?if($bohum =='Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">직급수당 </th>
		<td class="center"><input type="checkbox" class="checkbox" name="gikup" value="Y" <?if($gikup=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" rowspan="6">급여산정방법</th>
		<th class="left" >일반</th>
		<td class="center"><input type="checkbox" class="checkbox" name="general" value="Y" <?if($general=='Y'){ echo 'checked'; };?>></td>
		<th class="left" rowspan="2">특별수당</th>
		<th class="left" >연장</th>
		<td class="center"><input type="checkbox" class="checkbox" name="extend" value="Y" <?if($extend=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" >동거</th>
		<td class="center"><input type="checkbox" class="checkbox" name="fam" value="Y" <?if($fam == 'Y'){ echo 'checked'; };?>></td>
		<th class="left" >휴일</th>
		<td class="center"><input type="checkbox" class="checkbox" name="holiday" value="Y" <?if($holiday == 'Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" >노인</th>
		<td class="center"><input type="checkbox" class="checkbox" name="oldman" value="Y" <?if($oldman == 'Y'){ echo 'checked'; };?>></td>
		<th class="left" rowspan="2">배상책임보험</th>
		<th class="left" >가입일자</th>
		<td class="center"><input type="checkbox" class="checkbox" name="from_date" value="Y" <?if($from_date=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" >가사</th>
		<td class="center"><input type="checkbox" class="checkbox" name="housework" value="Y" <?if($housework == 'Y'){ echo 'checked'; };?>></td>
		<th class="left" >종료일자</th>
		<td class="center"><input type="checkbox" class="checkbox" name="to_date" value="Y" <?if($to_date == 'Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" >산모</th>
		<td class="center"><input type="checkbox" class="checkbox" name="puerperd" value="Y" <?if($puerperd == 'Y'){ echo 'checked'; };?>></td>
		<th class="left" rowspan="3">자격증</th>
		<th class="left" >자격종류</th>
		<td class="center"><input type="checkbox" class="checkbox" name="jaguk_kind" value="Y" <?if($jaguk_kind=='Y'){ echo 'checked'; };?>></td>

	</tr>
	<tr>
		<th class="left" >장애</th>
		<td class="center"><input type="checkbox" class="checkbox" name="disability" value="Y" <?if($disability == 'Y'){ echo 'checked'; };?>></td>
		<th class="left" >자격증번호</th>
		<td class="center"><input type="checkbox" class="checkbox" name="jaguk_no" value="Y" <?if($jaguk_no == 'Y'){ echo 'checked'; };?>></td>

	</tr>
	<tr>
		<th class="left" colspan="2">폰업무</th>
		<td class="center" ><input type="checkbox" class="checkbox" name="mobile_work" value="Y" <?if($mobile_work == 'Y'){ echo 'checked'; };?>></td>
		<th class="left" >발급일자</th>
		<td class="center"><input type="checkbox" class="checkbox" name="jaguk_date" value="Y" <?if($jaguk_date == 'Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" colspan="2">가족케어여부</th>
		<td class="center"><input type="checkbox" class="checkbox" name="familyYn" value="Y" <?if($familyYn == 'Y'){ echo 'checked'; };?>></td>
		<th class="left" colspan="2">치매인지수료여부</th>
		<td class="center"><input type="checkbox" class="checkbox" name="demantiaYn" value="Y" <?if($demantiaYn == 'Y'){ echo 'checked'; };?>></td>
	</tr>
	<tr>
		<th class="left" colspan="2">Memo</th>
		<td class="center"><input type="checkbox" class="checkbox" name="memo" value="Y" <?if($memo=='Y'){ echo 'checked'; };?>></td>
	</tr>
	<input name="find_center_name" type="hidden" value="<?=$find_center_name?>">
	<input name="find_yoy_name" type="hidden" value="<?=$find_yoy_name?>">
	<input name="find_yoy_ssn" type="hidden" value="<?=$find_yoy_ssn?>">
	<input name="find_yoy_phone" type="hidden" value="<?=$find_yoy_phone?>">
	<input name="find_yoy_stat" type="hidden" value="<?=$find_yoy_stat?>">
	<input name="find_dept" type="hidden" value="<?=$find_dept?>">
</table>
</div>
</form>
<?
	include_once('../inc/_footer.php');
?>