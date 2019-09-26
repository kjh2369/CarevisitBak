<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");
	include_once("../inc/_function.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_check_class.php");
	include_once("../inc/_ed.php");

	if ($_SESSION["userLevel"] == "A"){
		$mCode = $_POST["mCode"];
		$mKind = $_POST["mKind"];
	}else{
		$mCode = $_SESSION["userCenterCode"];
		$mKind = $_POST["mKind"] != '' ? $_POST["mKind"] : $_SESSION["userCenterKind"][0];
	}

	$page = $_POST["page"];

	$sql = "select *"
		 . "  from m00center"
		 . " where m00_mcode = '".$mCode
		 . "'  and m00_mkind = '".$mKind
		 . "'";

	$conn->query($sql);
	$row = $conn->fetch();

	if ($conn->row_count() > 0){
		$editMode = false;

		//$mCode = $row["m00_ccode"];
		$mKind			= $row["m00_mkind"];
		$code1			= $row["m00_code1"];
		$cName			= $row["m00_cname"];
		$mName			= $row["m00_mname"];
		$mJumin1		= subStr($row["m00_mjumin"], 0, 6);
		$mJumin2		= subStr($row["m00_mjumin"], 6, 7);
		$cTel			= getPhoneStyle($row["m00_ctel"]);
		$cPostNo		= getPostNoStyle($row["m00_cpostno"]);
		$cAddr1			= $row["m00_caddr1"];
		$cAddr2			= $row["m00_caddr2"];
		$cDate			= getDateStyle($row["m00_cdate"]);
		$jDate			= getDateStyle($row["m00_jdate"]);
		$cCode			= getBizStyle($row["m00_ccode"]);
		$kupyeoGbn		= $row["m00_kupyeo_gbn"];
		$kupyeo1		= $row["m00_kupyeo_1"];
		$kupyeo2		= $row["m00_kupyeo_2"];
		$kupyeo3		= $row["m00_kupyeo_3"];
		$siselGbn		= $row["m00_sisel_gbn"];
		$inwonsu		= number_format($row["m00_inwonsu"]);
		$homepage		= $row["m00_homepage"];
		$closeCond		= $row["m00_close_cond"];
		$carNo1			= $row['m00_car_no1'];
		$carNo2			= $row['m00_car_no2'];
		$carNo3			= $row['m00_car_no3'];
		$carNo4			= $row['m00_car_no4'];
		$carNo5			= $row['m00_car_no5'];
		$muksuYul1		= $row['m00_muksu_yul1'];
		$muksuYul2		= $row['m00_muksu_yul2'];
		$contDate		= getDateStyle($row['m00_cont_date']);
		$bankNo			= $row['m00_bank_no'];
		$bankName		= $row['m00_bank_name'];
		$bankDepos		= $row['m00_bank_depos'];
		$sudangRenew	= $row['m00_sudang_renew'];
		$sudangNight	= $row['m00_sudang_night'];
		$sudangHoliday	= $row['m00_sudang_holiday'];
		$sudangMonth	= $row['m00_sudang_month'];

		//$insName		= $row['m00_ins_code'];
		//$insFromDate	= $row['m00_ins_from_date'];
		//$insToDate	= $row['m00_ins_to_date'];
	}else{
		$editMode = true;

		$inwonsu = 0;
		$insName = 0;

		$sudangRenew	= 150;
		$sudangNight	= 150;
		$sudangHoliday	= 150;
		$sudangMonth	= 150;
	}
	$conn->row_free();

	$sql = "select g02_ins_code, g02_ins_from_date, g02_ins_to_date
			  from g02inscenter
			 where g02_ccode = '".$row['m00_mcode']."'
			   and g02_mkind = '".$row['m00_mkind']."'";
	$ins = $conn->get_array($sql);

	if (is_array($ins)){
		$insName     = $ins[0];
		$insFromDate = $ins[1];
		$insToDate   = $ins[2];
	}else{
		$insName = 0;
	}

	$sql = "select m97_pass"
	     . "  from m97user"
		 . " where m97_user = '".$mCode
		 . "'";
	$pass = $conn->get_data($sql);

	$button = "";
	if ($editMode){
		$button .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_centerSave();'>등록</button></span>";
	}else{
		$button .= "<span class='btn_pack m icon'><span class='list'></span><button type='button' onFocus='this.blur();' onClick='_centerList($page);'>리스트</button></span> ";
		$button .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_centerSave();'>수정</button></span>";

		if ($_SESSION["userLevel"] == "A"){
			$button .= "<span class='btn_pack m icon'><span class='delete'></span><button type='button' onFocus='this.blur();' onClick='_centerDelete();'>삭제</button></span>";
		}
	}
?>
<form name="center" method="post" action="../center/center_reg_ok.php">
<table style="width:100%;">
<tr>
	<td class="title">센터 마스터 등록</td>
	<td class="noborder" style="border-bottom:1px dotted #e5e5e5; text-align:right;"><?=$button;?></td>
</tr>
<tr>
	<td class="noborder" colspan="2">
		<table class="view_type1" style="width:100%;">
		<tr>
			<th style="width:180px;" scope="row">기관기호</th>
			<td style="width:210px;">
			<?
				if ($editMode){
					if ($_SESSION["userLevel"] == "A"){
					?>
						<input name="mCode" type="text" value="<?=$mCode;?>" maxlength="15" style="width:150px;" onChange="_setMkind();">
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
			<th style="width:150px;" scope="row"><span style="color:ff0000; padding-right:3px;">★</span>요양기관 구분</th>
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
			<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>승인번호</th>
			<td><input name="code1" type="text" value="<?=$code1;?>" maxlength="20" style="width:100px;"></td>
			<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>기관명</th>
			<td><input name="cName" type="text" value="<?=$cName;?>" maxlength="20" style="width:100%;"></td>
		</tr>
		<tr>
			<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>대표자명</th>
			<td><input name="mName" type="text" value="<?=$mName;?>" maxlength="10" style="width:70px;"></td>
			<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>대표자 주민번호</th>
			<td>
				<input name="mJumin1" type="text" value="<?=$mJumin1;?>" maxlength="6" class="phone" style="width:50px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 6){document.center.mJumin2.focus();}" onChange="_checkSSN('center', document.center.mJumin1, document.center.mJumin2, document.center.mCode, document.center.mKind);" onFocus="this.select();"> -
				<input name="mJumin2" type="text" value="<?=$mJumin2;?>" maxlength="7" class="phone" style="width:55px;" onKeyDown="__onlyNumber(this);" onkeyUp="if(this.value.length == 7){document.center.cTel.focus();}" onChange="_checkSSN('center', document.center.mJumin1, document.center.mJumin2, document.center.mCode, document.center.mKind);" onFocus="this.select();">
			</td>
		</tr>
		<tr>
			<th scope="row">센터대표전화번호</th>
			<td><input name="cTel" type="text" value="<?=$cTel;?>" maxlength="11" class="phone" style="width:80px;" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);"></td>
			<th scope="row" rowspan="2">주소</th>
			<td rowspan="2">
				<input name="cAddr1" type="text" value="<?=$cAddr1;?>" maxlength="20" style="width:100%;"><br>
				<input name="cAddr2" type="text" value="<?=$cAddr2;?>" maxlength="30" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>우편번호</th>
			<td>
				<input name="cPostNo1" type="text" value="<?=substr($cPostNo,0,3);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();"> -
				<input name="cPostNo2" type="text" value="<?=substr($cPostNo,4,3);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();">
				<a href="#" onClick="__helpAddress(document.center.cPostNo1, document.center.cPostNo2, document.center.cAddr1, document.center.cAddr2);">찾기</a>
			</td>
		</tr>
		<tr>
			<th scope="row">개업일자(설치신고일자)</th>
			<td><input name="cDate" type="text" value="<?=$cDate;?>" maxlength="8" class="phone" style="width:70px;" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"></td>
			<th scope="row">지정일자</th>
			<td><input name="jDate" type="text" value="<?=$jDate;?>" maxlength="8" class="phone" style="width:70px;" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"></td>
		</tr>
		<tr>
			<th scope="row">사업자등록번호</th>
			<td><input name="cCode" type="text" value="<?=$cCode;?>" maxlength="10" class="phone" style="width:80px;" onKeyDown="if(this.value.length < 10){__onlyNumber(this);}else{__checkBizID(this);}" onFocus="__replace(this, '-', ''); this.select();" onBlur="__checkBizID(this);"></td>
			<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>제공급여종류</th>
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
				<input name="inwonsu" type="text" value="<?=$inwonsu;?>" maxlength="5" class="number" style="width:50px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">
			</td>
		</tr>
		<tr>
			<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>목욕수당배분율(정/부)</th>
			<td>
				<input name="sudangYul1" type="text" value="<?=number_format($muksuYul1);?>" class="number" style="width:40px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);" onChange="_checkMuksuRate(this, document.center.sudangYul2);">% /
				<input name="sudangYul2" type="text" value="<?=number_format($muksuYul2);?>" class="number" style="width:40px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);" onChange="_checkMuksuRate(this, document.center.sudangYul1);">%
			</td>
			<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>업무시작일자</th>
			<td><input name="contDate" type="text" value="<?=$contDate;?>" maxlength="8" class="phone" style="width:70px;" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"></td>
		</tr>
		<tr>
			<th scope="row">홈페이지주소</th>
			<td><input name="homepage" type="text" value="<?=$homepage;?>" maxlength="30" style="width:100%;"></td>
			<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>기관상태구분</th>
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
			<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>입금은행/계좌번호</th>
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
			<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>예금주명</th>
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
				<input name="sudangRenew" type="text" value="<?=$sudangRenew;?>" maxlength="3" class="number" style="width:50px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">%
			</td>
			<th scope="row">야간근로수당(시급제)</th>
			<td>
				<input name="sudangNight" type="text" value="<?=$sudangNight;?>" maxlength="3" class="number" style="width:50px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">%
			</td>
		</tr>
		<tr>
			<th scope="row">휴일근로수당(시급제)</th>
			<td>
				<input name="sudangHoliday" type="text" value="<?=$sudangHoliday;?>" maxlength="3" class="number" style="width:50px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">%
			</td>
			<th scope="row">근로수당(월급제)</th>
			<td>
				<input name="sudangMonth" type="text" value="<?=$sudangMonth;?>" maxlength="3" class="number" style="width:50px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">%
			</td>
		</tr>
		<tr>
			<th scope="row" style="text-align:center; padding:0; border-bottom:0;" colspan="4">배상책임보험</th>
		</tr>
		<tr>
			<th scope="row">보험사</th>
			<td>
			<?
				echo '
					<select name="insName" style="width:100%;" onChange="_insItemList(this.value);">
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
			?>
			</td>
			<th scope="row">가입상품명</th>
			<td>
				<div id="idInsItem" style="display:;"><?=$insName > 0 ? '배상책임보험' : '-';?></div>
			</td>
		</tr>
		<tr>
			<th scope="row">보험가입기간(가입일)</th>
			<td>
			<?
				echo '<input name="insFromDate" type="text" value="'.$myF->dateStyle($insFromDate).'" tag="insToDate" maxlength="8" class="date" style="width:70px;" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, \'-\', \'\');" onBlur="__getDate(this);" onChange="_insToDate(this.value, \'insToDate\');" onClick="_carlendar(this);">';
			?>
			</td>
			<th scope="row">보험가입기간(해지일)</th>
			<td>
			<?
				echo '<input name="insToDate" type="text" value="'.$myF->dateStyle($insToDate).'" maxlength="8" class="date" style="width:70px;" style="background:#eeeeee; cursor:defaultl\;" onFocus="this.blur();" readOnly>';
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
<tr>
	<td class="button" colspan="2"><?=$button;?></td>
</tr>
</table>
<div id="centerInMemberLaber" style="z-index:1; left:0; top:0; position:absolute; color:#000000;">
	<table id="centerInMemberTable" style="width:500px; height:500px; background-color:#ffffff; border:2px solid #15519b; display:none;">
	<tr>
	<td class="title" style="width:100%;">요양보호사</td>
	</tr>
	<tr>
		<td class="noborder" style="width:100%;">
		<table class="view_type1" style="width:100%; height:100%; margin:0; padding:0;">
		<colGroup>
		<col width="10%">
		<col width="15%">
		<col width="15%">
		<col width="15%">
		<col width="15%">
		<col width="*">
		</colGroup>
		<tr>
			<th style="height:24px; text-align:center; padding:0;"><input name="insCheckAll" type="checkbox" class="checkbox" onClick="_regInsMemberIsChecks('insCheck[]', this.checked);"></th>
			<th style="height:24px; text-align:center; padding:0;">계약일</th>
			<th style="height:24px; text-align:center; padding:0;">입사일</th>
			<th style="height:24px; text-align:center; padding:0;">퇴사일</th>
			<th style="height:24px; text-align:center; padding:0;">요양보호사</th>
			<th style="height:24px; text-align:center; padding:0;">주민번호</th>
		</tr>
		<tr>
			<td style="border:0; margin:0; padding:0;" colspan="6">
			<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:476px; margin:0; padding:0;">
				<table style="width:105%; margin:0; padding:0;">
				<colGroup>
				<col width="10%">
				<col width="15%">
				<col width="15%">
				<col width="15%">
				<col width="15%">
				<col width="*">
				</colGroup>
				<?
					$sql = "select m02_yjumin, m02_yname, m02_yipsail, m02_ytoisail, m02_ygoyong_stat, m02_ins_yn, m02_ins_code, m02_ins_item
							  from m02yoyangsa
							 where m02_ccode = '$mCode'
							   and m02_mkind = '$mKind'
							 order by m02_ygoyong_stat, m02_yname";
					$conn->query($sql);
					$conn->fetch();
					$rowCount = $conn->row_count();

					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);
						$joinDate = $myF->dateStyle($row["m02_yipsail"]);
						$endDate = $row["m02_ygoyong_stat"] != "1" ? $myF->dateStyle($row["m02_ytoisail"]) : "(근무중)";
						$name = $row["m02_yname"];
						$jumin = $myF->issStyle($row["m02_yjumin"]);
						$edJumin = $ed->en($row["m02_yjumin"]);
						$stat = $row["m02_ygoyong_stat"];

						if ($row["m02_ins_yn"] == "Y" && $row["m02_ins_code"] > 0 && $row["m02_ins_item"] > 0){
							$insChecked = "checked";
						}else{
							$insChecked = "";
						}
						if ($stat == "1" || $insChecked != ""){
							echo "
								<tr>
									<td style='text-align:center;'><input name='insCheck[]' type='checkbox' class='checkbox' value='$i' onClick='_regInsMemberIsCheck(this, $i);' $insChecked></td>
									<td style='text-align:center;'><input name='startDate[]' type='text' value='$joinDate' maxlength='8' class='phone' style='width:70px;' onKeyDown='__onlyNumber(this);' onFocus='__replace(this, \"-\", \"\");' onBlur='__getDate(this);'></td>
									<td style='text-align:center;'>$joinDate</td>
									<td style='text-align:center;'>$endDate</td>
									<td style='text-align:left;'>$name</td>
									<td style='text-align:left;'>$jumin</td>
								</tr>
								<input name='jumin[]' type='hidden' value='$edJumin'>
								<input name='stat[]' type='hidden' value='$stat'>
								 ";
						}
					}
					$conn->row_free();
				?>
				</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="text-align:right; border:0; padding-right:10px;">
			<span class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="_regInsMemberOk();">확인</button></span>
			<span class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="_regInsMemberCancel();">닫기</button></span>
		</td>
	</tr>
	</table>
</div>
</form>
<?
	include_once("../inc/_db_close.php");
?>