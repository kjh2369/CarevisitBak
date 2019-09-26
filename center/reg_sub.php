<tr>
	<td class="title">센터 마스터 등록</td>
</tr>
<tr>
	<td class="button">
	<!--<a onClick="_centerSearch();"><img src="../image/btn8.gif"></a>-->
	<?=$button;?>
	</td>
</tr>
<tr>
	<td class="noborder">
		<table class="view_type1">
		<tr>
			<th style="width:180px;" scope="row">기관기호</th>
			<td style="width:210px;">
			<?
				if ($editMode){
					if ($_SESSION["userLevel"] == "A"){
					?>
						<input name="mCode" type="text" maxlength="15" style="width:150px;" onChange="_setMkind();">
					<?
					}else{
					?>
						<?=$_SESSION["userCenterCode"];?><input name="mCode" type="hidden" value="<?=$_SESSION["userCenterCode"];?>">
					<?
					}
				}else{
				?>
					<?=$mCode;?><input name="mCode" type="hidden" value="<?=$mCode;?>">
				<?
				}
			?>
			</td>
			<th style="width:150px;" scope="row">요양기관 구분</th>
			<td style="width:270px;">
			<?
				switch($mKind){
					case "0":
						$mKindName = "재가요양기관";
						break;
					case "1":
						$mKindName = "가사간병(바우처)";
						break;
					case "2":
						$mKindName = "노인돌봄(바우처)";
						break;
					case "3":
						$mKindName = "산모신생아(바우처)";
						break;
					case "4":
						$mKindName = "장애인 활동보조(바우처)";
						break;
				}

				if ($editMode or $_POST["gubun"] == "reg"){
				?>
					<select name="mKind" style="width:100%;" onChange="_setMkind();">
						<option value="0"<? if($mKind == "0"){echo "selected";}?>>재가요양기관</option>
						<option value="1"<? if($mKind == "1"){echo "selected";}?>>가사간병(바우처)</option>
						<option value="2"<? if($mKind == "2"){echo "selected";}?>>노인돌봄(바우처)</option>
						<option value="3"<? if($mKind == "3"){echo "selected";}?>>산모신생아(바우처)</option>
						<option value="4"<? if($mKind == "4"){echo "selected";}?>>장애인 활동보조(바우처)</option>
					</select>
				<?
				}else{
				?>
					<?=$mKindName;?><input name="mKind" type="hidden" value="<?=$mKind;?>">
				<?
				}
			?>
			</td>
		</tr>
		<tr>
			<th scope="row">승인번호</th>
			<td><input name="code1" type="text" value="<?=$code1;?>" maxlength="20" style="width:150px;"></td>
			<th scope="row">기관명</th>
			<td><input name="cName" type="text" value="<?=$cName;?>" maxlength="20" style="width:100%;"></td>
		</tr>
		<tr>
			<th scope="row">대표자명</th>
			<td><input name="mName" type="text" value="<?=$mName;?>" maxlength="10"></td>
			<th scope="row">대표자 주민번호</th>
			<td>
				<input name="mJumin1" type="text" value="<?=$mJumin1;?>" maxlength="6" class="phone" style="width:50px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 6){document.center.mJumin2.focus();}" onChange="_checkSSN('center', document.center.mJumin1, document.center.mJumin2, document.center.mCode, document.center.mKind);" onFocus="this.select();"> -
				<input name="mJumin2" type="text" value="<?=$mJumin2;?>" maxlength="7" class="phone" style="width:55px;" onKeyDown="__onlyNumber(this);" onkeyUp="if(this.value.length == 7){document.center.cTel.focus();}" onChange="_checkSSN('center', document.center.mJumin1, document.center.mJumin2, document.center.mCode, document.center.mKind);" onFocus="this.select();">
			</td>
		</tr>
		<tr>
			<th scope="row">센터대표전화번호</th>
			<td><input name="cTel" type="text" value="<?=$cTel;?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);"></td>
			<th scope="row" rowspan="2">주소</th>
			<td rowspan="2">
				<input name="cAddr1" type="text" value="<?=$cAddr1;?>" maxlength="20" style="width:100%;"><br>
				<input name="cAddr2" type="text" value="<?=$cAddr2;?>" maxlength="30" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th scope="row">우편번호</th>
			<td>
				<input name="cPostNo1" type="text" value="<?=substr($cPostNo,0,3);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();"> -
				<input name="cPostNo2" type="text" value="<?=substr($cPostNo,4,3);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();">
				<a href="#" onClick="__helpAddress(document.center.cPostNo1, document.center.cPostNo2, document.center.cAddr1, document.center.cAddr2);">찾기</a>
			</td>
		</tr>
		<tr>
			<th scope="row">개업일자(설치신고일자)</th>
			<td><input name="cDate" type="text" value="<?=$cDate;?>" maxlength="8" class="phone" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);"></td>
			<th scope="row">사업개시일자</th>
			<td><input name="jDate" type="text" value="<?=$jDate;?>" maxlength="8" class="phone" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);"></td>
		</tr>
		<tr>
			<th scope="row">사업자등록번호</th>
			<td><input name="cCode" type="text" value="<?=$cCode;?>" maxlength="10" class="phone" onKeyDown="if(this.value.length < 10){__onlyNumber(this);}else{__checkBizID(this);}" onFocus="__replace(this, '-', ''); this.select();" onBlur="__checkBizID(this);"></td>
			<th scope="row">제공급여종류</th>
			<td id="kupyeoGbn">
			<?
				if ($mKind == "0"){
				?>
					<input name="kupyeoGbn" type="hidden" value="N">
					<input name="kupyeo1" type="checkbox" value="Y" class="checkbox"<? if($kupyeo1 == "Y"){echo "checked";}?>>방문요양
					<input name="kupyeo2" type="checkbox" value="Y" class="checkbox"<? if($kupyeo2 == "Y"){echo "checked";}?>>방문목욕
					<input name="kupyeo3" type="checkbox" value="Y" class="checkbox"<? if($kupyeo3 == "Y"){echo "checked";}?>>방문간호
				<?
				}else{
				?>
					<input name="kupyeoGbn" type="checkbox" value="Y" class="checkbox"<? if($kupyeoGbn == "Y"){echo "checked";}?>><?=$mKindName;?>
					<input name="kupyeo1" type="hidden" value="N">
					<input name="kupyeo2" type="hidden" value="N">
					<input name="kupyeo3" type="hidden" value="N">
				<?
				}
			?>
			</td>
		</tr>
		<tr>
			<th scope="row">시설구분</th>
			<td>
				<select name="siselGbn" style="width:100%;">
					<option value="1">재가장기요양기관</option>
				</select>
			</td>
			<th scope="row">직원수</th>
			<td>
				<input name="inwonsu" type="text" value="<?=$inwonsu;?>" maxlength="5" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">
			</td>
		</tr>
		<tr>
			<th scope="row">목욕수당배분율(정/부)</th>
			<td>
				<input name="sudangYul1" type="text" value="<?=number_format($muksuYul1);?>" class="number" style="width:80px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);" onChange="_checkMuksuRate(this, document.center.sudangYul2);">% /
				<input name="sudangYul2" type="text" value="<?=number_format($muksuYul2);?>" class="number" style="width:80px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);" onChange="_checkMuksuRate(this, document.center.sudangYul1);">%
			</td>
			<th scope="row">업무시작일자</th>
			<td><input name="contDate" type="text" value="<?=$contDate;?>" maxlength="8" class="phone" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);"></td>
		</tr>
		<tr>
			<th scope="row">홈페이지주소</th>
			<td><input name="homepage" type="text" value="<?=$homepage;?>" maxlength="30" style="width:100%;"></td>
			<th scope="row">센터상태구분</th>
			<td>
				<select name="closeCond" style="width:100%;">
					<option value="0">운영중</option>
					<option value="1">휴업중</option>
					<option value="8">파업중</option>
					<option value="9">폐업</option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">입금은행/계화번호</th>
			<td>
				<select name="bankName" style="width:40%;">
				<?
					$bankList= $definition->GetBankList();
					$bankListCount = sizeOf($bankList);

					for($i=0; $i<$bankListCount; $i++){
					?>
						<option value="<?=$bankList[$i]['code'];?>" title="<?=$bankList[$i]['name'];?>" <? if($bankName == $bankList[$i]['code']){?>selected<?} ?>><?=$bankList[$i]['name'];?></option>
					<?
					}
				?>
				</select>
				<input name="bankNo" type="text" value="<?=$bankNo;?>" maxlength="30" style="width:58%;">
			</td>
			<th scope="row">예금주명</th>
			<td><input name="bankDepos" type="text" value="<?=$bankDepos;?>" maxlength="30" style="width:100%;"></td>
		</tr>
		<tr>
			<th scope="row">차량번호</th>
			<td colspan="3">
				<input name="carNo1" type="text" value="<?=$carNo1;?>" maxlength="10">
				<input name="carNo2" type="text" value="<?=$carNo2;?>" maxlength="10">
				<input name="carNo3" type="text" value="<?=$carNo3;?>" maxlength="10">
				<input name="carNo4" type="text" value="<?=$carNo4;?>" maxlength="10">
				<input name="carNo5" type="text" value="<?=$carNo5;?>" maxlength="10">
			</td>
		</tr>
		<tr>
			<th scope="row" style="text-align:center; padding:0;" colspan="4">근로수당</th>
		</tr>
		<tr>
			<th scope="row">연장근로수당(시급제)</th>
			<td>
				<input name="sudangRenew" type="text" value="<?=$sudangRenew;?>" maxlength="3" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">%
			</td>
			<th scope="row">야간근로수당(시급제)</th>
			<td>
				<input name="sudangNight" type="text" value="<?=$sudangNight;?>" maxlength="3" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">%
			</td>
		</tr>
		<tr>
			<th scope="row">휴일근로수당(시급제)</th>
			<td>
				<input name="sudangHoliday" type="text" value="<?=$sudangHoliday;?>" maxlength="3" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">%
			</td>
			<th scope="row">근로수당(월급제)</th>
			<td>
				<input name="sudangMonth" type="text" value="<?=$sudangMonth;?>" maxlength="3" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">%
			</td>
		</tr>
		<tr>
			<th scope="row" style="text-align:center; padding:0; border-bottom:0;" colspan="4">배상책임보험</th>
		</tr>
		<tr>
			<th scope="row">보험사</th>
			<td>
			<?
			//	if (($insName > 0 && $insItem > 0) || ($insUseYN == "N")){
			//		$insNM = $conn->get_data("select g01_name from g01ins where g01_code = '$insName'");
			//		echo $insNM;
			//		echo "<input name='insName' type='hidden' value='$insName'>";
			//	}else{
					echo '
						<select name="insName" style="width:100%;" onChange="_insItemList(this.options[selectedIndex].tag, insItem, this.value, \'insMemberCount\', \'insMemberPrice\', \'insAmount\');">
						<option value=""> - 선택하여 주십시오. - </option>
						 ';
					$sql = "select g01_code as code, g01_name as name, g01_use as useYN
							  from g01ins";
					$conn->query($sql);
					$conn->fetch();
					$rowCount = $conn->row_count();

					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);
						$code = $row["code"];
						$name = $row["name"];
						$useYN = $row["useYN"];

						if ($insCode != "") $insCode = $code;

						echo "<option value='$code' ".($insName == $code ? "selected" : "")." tag='$useYN'>$name</option>";
					}
					$conn->row_free();

					echo '</select>';
			//	}
			?>
			</td>
			<th scope="row">가입상품명</th>
			<td>
			<?
			//	if (($insName > 0 && $insItem > 0) || ($insUseYN == "N")){
			//		$insNM = $conn->get_data("select g02_name from g02insitem where g02_code = '$insName' and g02_item = '$insItem'");
			//		echo $insNM;
			//		echo "<input name='insItem' type='hidden' value='$insItem'>";
			//	}else{
				if ($insUseYN == "N"){
					echo "배상책임보험";;
					echo "<input name='insItem' type='hidden' value='$insItem'>";
				}else{
					echo '
						<div id="idInsItem" style="display:;">
						<select name="insItem" style="width:100%;" onChange="_insMemberPrice(\'insName\', \'insItem\', \'insMemberCount\', \'insMemberPrice\', \'insAmount\');">
						<option value=""> - 선택하여 주십시오. - </option>
						 ';
					if ($insName != null) $insCode = $insName;

					$sql = "select distinct g02_item as code, g02_name as name
							  from g02insitem
							 where g02_code = '$insCode'";
					$conn->query($sql);
					$conn->fetch();
					$rowCount = $conn->row_count();

					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);
						$code = $row["code"];
						$name = $row["name"];

						echo "<option value='$code' ".($insItem == $code ? "selected" : "").">$name</option>";
					}
					$conn->row_free();

					echo '
						</select>
						</div>
						<div id="idInsItemTemp" style="display:none;">배상책임보험</div>
						 ';
				}
			//	}
			?>
			</td>
		</tr>
		<tbody id="idTbodyIns" style="display:<?=$insUseYN == "N" ? "none" : ""?>;">
		<tr>
			<th scope="row">가입인원수/보험료단가</th>
			<td>
			<?
			//	if ($insName > 0 && $insItem > 0){
			//		echo $insMember." / ".number_format($insPrice);
			//		echo '<input name="insMemberCount" type="hidden" value="'.$insMember.'">';
			//		echo '<input name="insMemberPrice" type="hidden" value="'.$insPrice.'">';
			//	}else{
					echo '
						<input name="insMemberCount" type="text" value="'.$insMember.'" maxlength="3" class="number" style="width:50px;" onFocus="if(document.center.insName.value==\'\'){alert(\'보험사를 먼저 선택하여 주십시오.\'); this.blur(); document.center.insName.focus(); return;} if(document.center.insItem.value==\'\'){alert(\'상품을 먼저 선택하여 주십시오.\'); this.blur(); document.center.insItem.focus(); return}" onKeyDown="__onlyNumber(this);" onKeyUp="_insMemberPrice(\'insName\', \'insItem\', \'insMemberCount\', \'insMemberPrice\', \'insAmount\');" onFocus="this.select();"> /
						<input name="insMemberPrice" type="text" value="'.number_format($insPrice).'" maxlength="9" class="number" style="width:100px; background:#eeeeee; cursor:defaultl\;" onFocus="this.blur();" readOnly>
						<span class="btn_pack small"><button type="button" onFocus="this.blur();" onClick="if(document.center.insName.value==\'\'){alert(\'보험사를 먼저 선택하여 주십시오.\'); this.blur(); document.center.insName.focus(); return;} if(document.center.insItem.value==\'\'){alert(\'상품을 먼저 선택하여 주십시오.\'); this.blur(); document.center.insItem.focus(); return} _regInsMember();">...</button></span>
						 ';
			//	}
			?>
			</td>
			<th scope="row">납입보험료액</th>
			<td>
			<?
			//	if ($insName > 0 && $insItem > 0){
			//		echo number_format($insAmount);
			//		echo '<input name="insAmount" type="hidden" value="'.number_format($insAmount).'">';
			//	}else{
					echo '<input name="insAmount" type="text" value="'.number_format($insAmount).'" maxlength="9" class="number" style="width:100px; background:#eeeeee; cursor:defaultl;" onFocus="this.blur();" readOnly>';
			//	}
			?>
			</td>
		</tr>
		</tbody>
		<tr>
			<th scope="row">보험가입기간(FROM)</th>
			<td>
			<?
			//	if ($insName > 0 && $insItem > 0){
			//		echo $myF->dateStyle($insFromDate);
			//		echo '<input name="insFromDate" type="hidden" value="'.$insFromDate.'">';
			//	}else{
					echo '<input name="insFromDate" type="text" value="'.$myF->dateStyle($insFromDate).'" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, \'-\', \'\');" onBlur="__getDate(this);" onChange="_insToDate(this.value, \'insToDate\');">';
			//	}
			?>
			</td>
			<th scope="row">보험가입기간(TO)</th>
			<td>
			<?
			//	if ($insName > 0 && $insItem > 0){
			//		echo $myF->dateStyle($insToDate);
			//		echo '<input name="insToDate" type="hidden" value="'.$insToDate.'">';
			//	}else{
					echo '<input name="insToDate" type="text" value="'.$myF->dateStyle($insToDate).'" maxlength="8" class="date" style="background:#eeeeee; cursor:defaultl\;" onFocus="this.blur();" readOnly>';
			//	}
			?>
			</td>
		</tr>
		<?
			if ($_SESSION['userLevel'] == 'A'){
			?>
				<tr>
				<th scope="row">비밀번호</th>
				<td colspan="3">
					<input name="password" type="text" value="<?=$pass;?>" maxlength="15">
				</td>
				</tr>
			<?
			}
		?>
		</table>
		<input name="editMode" type="hidden" value="<?=$editMode;?>">
		<input name="gubun" type="hidden" value="<?=$_POST["gubun"];?>">
		<input name="searchMcode" type="hidden" value="<?=$_POST["searchMcode"];?>">
		<input name="searchMkind" type="hidden" value="<?=$_POST["searchMkind"];?>">
		<input name="searchCode1" type="hidden" value="<?=$_POST["searchCode1"];?>">
		<input name="searchCname" type="hidden" value="<?=$_POST["searchCname"];?>">
	</td>
</tr>