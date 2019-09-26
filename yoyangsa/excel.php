<?
	include("../inc/_header.php");
	include("../inc/_body_header.php");

	$mCode = $_POST['mCode'] != '' ? $_POST['mCode'] : $_SESSION['userCenterCode'];
	$mKind = $_POST['mKind'] != '' ? $_POST['mKind'] : $_SESSION['userCenterKind'][0];

?>
<table>
<tr>
	<td class="title">직원정보 일괄등록</td>
</tr>
</table>
<form name="f" method="post" enctype="multipart/form-data">
<table class="view_type1" style="border-bottom:none;">
<tr>
	<th style="width:20%;">요양기관구분</th>
	<td style="width:60%;">
		<select name="mKind" style="width:150px;" onChange="__setYoyIljungList('<?=$mCode;?>',this.value, document.f.mYear.value+document.f.mMonth.value, 'mYoy');">
		<?
			for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
			?>
				<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $_POST["mKind"]){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
			<?
			}
		?>
		</select>
	</td>
	<td style="width:20%; text-align:right;">
		<input type="button" value="샘플다운로드" class="btnM2" onClick="location.href='yoyangsa sample.xls';">
	</td>
</tr>
<tr>
	<th>중복 발생시 겹쳐쓰기</th>
	<td colspan="2">
		<select name="duplicateYN" style="width:auto;">
		<option value="N">아니오</option>
		<option value="Y">예</option>
		</select>
	</td>
</tr>
<tr>
	<th>엑셀파일선택</th>
	<td colspan="2"><input name="excel" type="file" style="width:300px;"></td>
</tr>
<tr>
	<!--td style="text-align:left; border-bottom:none;" colspan="2"> <font color="red">샘플 엑셀시트에서 예제(3번째라인) 다음부터 입력 가능하며, 업데이트 됩니다. 이점 유의해 주십시오.</font></td-->
	<td style="text-align:right; border-bottom:none;" colspan="3">
		<!--input type="button" value="업로드" class="btnM2" onClick="_excelUpload(document.f, document.f.excel, 'excel_upload.php')" style="margin-top:5px;"-->
		<input type="button" value="CSV 업로드" class="btnM2" onClick="_csvUpload(document.f, document.f.excel, 'csv_upload.php')" style="margin-top:5px;">
	</td>
</tr>
</table>
<br>
<p style="font-weight:bold; font-size:15px;">csv 사용자 메뉴얼</p><br>

<p style="font-weight:bold; font-size:12px;"> 1.아래 스크린샷을 보시면 왼쪽상단에 메뉴버튼 클릭 -> 다른이름으로저장 -> 다른 형식을 누릅니다.</p>
<div><img style="width:700px; height:500px;" src="../sugupja/img/excel_manual.gif"></img></div>
<br>
<p style="font-weight:bold; font-size:12px;"> 2. 그럼 아래에 스크린샷과같이 저장창에서 파일형식을 <font color="red">csv(쉼표로분리)</font> 선택하신 후 <font color="red">저장</font>. 그리고 저장파일을 <font color="red">업로드</font></p>
<div><img style="width:700px; height:500px;" src="../sugupja/img/excel_manual2.gif"></img></div>
<input name="mCode" type="hidden" value="<?=$mCode;?>">
</form>
<?
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");
?>