<?
	include("../inc/_db_open.php");
	include("../inc/_http_referer.php");
	include("../inc/_function.php");
	include("../inc/_myFun.php");
	include("../inc/_ed.php");

	$menu  = $_POST['mIndex'];
	$tab   = $_POST['mTab'];
	$mCode = ($_POST["mCode"] != "" ? $_POST["mCode"] : $_SESSION["userCenterCode"]);
	$mKind = ($_POST["mKind"] != "" ? $_POST["mKind"] : $_SESSION["userCenterKind"][0]);
	$yKey  = $_POST['key'];


	// 기관의 보험가입정보
	$sql = "select g02_ins_code as code
			,      g02_ins_from_date as fromDate
			,      g02_ins_to_date as toDate
			  from g02inscenter
			 where g02_ccode = '$mCode'
			   and g02_mkind = '$mKind'";
	$ins = $conn->get_array($sql);

	if (is_array($ins)){
		$insName     = $ins[0];
		$insFromDate = $ins[1];
		$insToDate   = $ins[2];
	}else{
		$insName = 0;
	}
	$ins = $conn->get_array($sql);

	$sql = $conn->get_query("02", $_POST["mCode"], $_POST["mKind"], $_POST["key"]);
	$conn->query($sql);
	$row = $conn->fetch();

	$fixPayType = $row['m02_pay_type'] == 'N' ? 'N' : 'Y';

	if ($conn->row_count() > 0){
		$editMode = false;
	}else{
		$editMode = true;
	}

	// 보험가입여부
	if ($row['m02_ins_yn'] == 'Y'){
		$insYN = 'Y';
	}else{
		$insYN = 'N';
	}

	// 보험정보
	if ($insYN == 'Y'){
		$insCode     = $row['m02_ins_code'];
		$insFromDate = $row['m02_ins_from_date'];
		$insToDate   = $row['m02_ins_to_date'];
	}else{
		$sql = "select g03_ins_to_date
				  from g03insapply
				 where g03_ins_code       = '".$ins['code']."'
				   and g03_jumin          = '".$row["m02_yjumin"]."'
				   and g03_ins_from_date >= '".$ins['fromDate']."'
				 order by g03_ins_to_date desc
				 limit 1";
		$tempDate = $conn->get_data($sql);

		$insCode = $ins['code'];

		if (strLen($tempDate) == 8){
			$tempDate = $myF->dateStyle($tempDate);
			$tempDate = $myF->dateAdd('day', 1, $tempDate, 'Ymd');
			$insFromDate = ($ins['fromDate'] > $tempDate ? $ins['fromDate'] : $tempDate);
		}else{
			$insFromDate = ($ins['fromDate'] > $row['m02_yipsail'] ? $ins['fromDate'] : $row['m02_yipsail']);
		}
		$ins[1] = $insFromDate;
		$insToDate = $ins['toDate'];
	}

	$insData = $conn->get_array("select g01_name, g01_use from g01ins where g01_code = '$insCode'");
	$insName = $insData[0];
	$insUse  = $insData[1];

	$conn->row_free();

	if ($_POST["gubun"] == "yoySearch"){
		$buttonGroup = "<span class='btn_pack m icon'><span class='list'></span><button type='button' onFocus='this.blur();' onClick='_centerYoyList(".$_POST["page"].");'>리스트</button></span> ";
	}

	if ($editMode){
		$buttonGroup .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_yoyangsaSave();'>등록</button></span> ";
	}else{
		$buttonGroup .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_yoyangsaSave();'>수정</button></span> ";

		if ($_SESSION["userLevel"] == 'A'){
			$buttonGroup .= "<span class='btn_pack m icon'><span class='delete'></span><button type='button' onFocus='this.blur();' onClick='_yoyangsaDelete();'>삭제</button></span>";
		}
	}
?>
<form name="center" method="post" action="../yoyangsa/yoyangsa_reg_ok.php">

<table style="width:100%;">
	<tr>
		<td class="title">요양보호사/직원 등록</td>
	</tr>
	<?
		$target = "yoyReg";
		include("../main/center_info.php");
	?>
	<tbody id="notBody" style="display:<?=$notBody;?>;">
		<tr>
			<td class="noborder" colspan="2">
				::기관을 선택하여 주십시오.::
			</td>
		</tr>
	</tbody>
<?

if($row['m02_yname']){
?>
<table style="width:100%; margin-top:5px;">
<colGroup>
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
</colGroup>
<tr>
	<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">근로<br>계약서</td>
	<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">개인정보<br>보호동의서</td>
	<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">익월서비스<br>일정표</td>
	<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">상담일지<br>(격월주기)</td>
	<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">직무평가및<br>만족도조사<br>(격월주기)</td>
	<td style="line-height:1.5em; font-weight:bold; background:#eee;" colspan="4">교육</td>
	<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">건강검진<br>(년주기)</td>
</tr>
<tr>
	<td style="font-weight:bold; background:#eee;">신규</td>
	<td style="font-weight:bold; background:#eee;">급여제공</td>
	<td style="font-weight:bold; background:#eee;">업무범위</td>
	<td style="font-weight:bold; background:#eee;">개인정보보호</td>
</tr>
<tr>
	<!-- 근로계약서 -->
	<?
		if($row['m02_ygoyong_kind'] == "1"){
			echo "<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick=\"window.open('../yoyangsa/Report_show_92.php?mCode=$mCode&mKind=$mKind&key=".$row['m02_key']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');\">출력</button></td>";
		}else if ($row['m02_ygoyong_kind'] == "2"){
			echo "<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick=\"window.open('../yoyangsa/Report_show_91.php?mCode=$mCode&mKind=$mKind&key=".$row['m02_key']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');\">출력</button></td>";
		}else {
			echo "<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick=\"window.open('../yoyangsa/Report_show_90.php?mCode=$mCode&mKind=$mKind&key=".$row['m02_key']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');\">출력</button></td>";
		}
	?>
	<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''>미작성</button></span></td>
	<?
		//익월일정확인
		$juminNo = $row["m02_yjumin"];
		$beforeYM = date("Ym",strtotime("+1 month"));
		$sql = "select count(*)
				  from t01iljung
				 where t01_ccode = '$mCode'
				   and t01_mkind = '$mKind'
				   and t01_sugup_date like '$beforeYM%'
				   and '$juminNo' in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
				   and t01_del_yn = 'N'";
		$workCount = $conn->get_data($sql);

		if ($workCount > 0){
		?>
			<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick="serviceCalendarShow('<?=$mCode;?>', '<?=$mKind;?>', '<?=subStr($beforeYM, 0, 4);?>', '<?=subStr($beforeYM, 4, 2);?>', '<?=$ed->en($juminNo);?>', 'y', 'n', 'pdf');">보기</button></span></td>
		<?
		}else{
			if($row["m02_ygoyong_stat"] == "1"){
				echo "<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=\"window.open('../iljung/iljung.php?menuIndex=work&menuSeq=1&manager=true&gubun=reg', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');return false;\">미등록</td>";
			}else {
				echo "<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick='stat_chk();return false;'>-</a></td>";
			}
		}

		/*
		 * 상담일지(격월주기)
		 * - 상담일지 마지막 작성일에서 2달이 지난 시점 이후 작성여부를 판단할것.
		 * select max(date_format(작성일자, 'Y%-m%-d%'))
		 * 작성일자 현 시점에 2달이 지났으면 작성 else 미작성
		 * date_format(작성일자, 'Y%-m%-d%')
		 * $myF->dateAdd('month' 2 작성일자 'Y-m-d')
		 * $myF->dateDiff('d', 작성일자 + 2달 , 오늘) > 0
		 */
		 
		$beforeYMD = date("Ymd", mkTime());
		$sql = "select r260_date"
			 . "  from r260talk"
			 . " where r260_ccode = '".$mCode
			 . "'  and r260_mkind = '".$mKind
			 . "' and r260_yoyangsa like '".$row["m02_yjumin"]
			 . "%'";
		$conn->query($sql);
		$talk = $conn->fetch();

		$date1 = $myF->dateAdd('month', 2, $talk['r260_date'], 'y-m-d');
		$date2 = $myF->dateDiff('d', $date1, $beforeYMD);
	?>
	<?
		if($row["m02_ygoyong_stat"] == "1"){
			if($date2 > 0 or $talk['r260_date'] == ''){
			?>
				<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onclick="_showPopup(this,'1','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["m02_yjumin"]);?>');return false;">미작성</a></td>
			<?
			}else{
			?>
				<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onclick="_showPopup(this,'1','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["m02_yjumin"]);?>');return false;">작성완료</a></td>
			<?
			}
		}else{
		?>
			<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick='stat_chk();return false;'>-</a></td>
		<?
		}

		$sql = "select *"
		 . "  from r270test"
		 . " where r270_ccode = '".$mCode
		 . "'  and r270_mkind = '".$mKind
		 . "' and r270_yoy_code like '".$row["m02_yjumin"]
		 . "%'";
		$conn->query($sql);
		$test = $conn->fetch();

		$beforeYMD2 = date("Ymd", mkTime());

		$date3 = $myF->dateAdd('month', 2, $test['r270_date'], 'y-m-d');
		$date4 = $myF->dateDiff('d', $date3, $beforeYMD2);

	?>
	<?
	if($row["m02_ygoyong_stat"] == "1"){
	?>
		<?
		if($date4 > 0 or $test['r270_date'] == ''){
		?>
			<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onclick="_showPopup(this,'2','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["m02_yjumin"]);?>');return false;">미작성</a></td>
		<?
		}else {
		?>
			<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onclick="_showPopup(this,'2','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["m02_yjumin"]);?>');return false;">작성완료</a></td>
		<?
		}
	}else {
	?>
		<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick='stat_chk()'>-</button></span></td>
	<?
	}
	?>

	<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''>미작성</button></span></td>
	<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''>미작성</button></span></td>
	<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''>미작성</button></span></td>
	<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''>미작성</button></span></td>
	<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''>미작성</button></span></td>
</tr>
</table>
<?
}
?>
<table style="width:100%;>
	<tbody id="yoyBody" style="display:<?=$yoyBody;?>;">
		<tr>
			<td class="subject">요양보호사/직원</td>
			<td class="button" style="vertical-align:bottom;" >
			<?
				echo $buttonGroup;
			?>
			</td>
		</tr>

		<tr>
			<td class="noborder" colspan="2">
				<table class="view_type1">
					<tr>
						<th colspan="4" style="text-align:center;">기본정보</th>
					</tr>
					<tr>
						<th style="width:25%;" scope="row"><span style="color:ff0000; padding-right:3px;">★</span>주민번호</th>
						<td style="width:25%;">
						<?
							if (!$editMode){
							?>
								<div><?=substr($row["m02_yjumin"],0,6)."-".substr($row["m02_yjumin"],6,1);?>******</div>
								<input name="yJumin1" type="hidden" value="<?=substr($row["m02_yjumin"],0,6);?>">
								<input name="yJumin2" type="hidden" value="<?=substr($row["m02_yjumin"],6,13);?>">
							<?
							}else{
							?>
								<input name="yJumin1" type="text" value="" maxlength="6" class="phone" style="width:50px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 6){document.center.yJumin2.focus();}" onChange="_checkSSN('yoy', document.center.yJumin1, document.center.yJumin2, document.center.curMcode, document.center.curMkind);" onFocus="this.select();"> -
								<input name="yJumin2" type="text" value="" maxlength="7" class="phone" style="width:55px;" onKeyDown="__onlyNumber(this);" onkeyUp="if(this.value.length == 7){document.center.yName.focus();}" onChange="_checkSSN('yoy', document.center.yJumin1, document.center.yJumin2, document.center.curMcode, document.center.curMkind);" onFocus="this.select();">
							<?
							}
						?>
						</td>
						<th style="width:25%;" scope="row"><span style="color:ff0000; padding-right:3px;">★</span>성명</th>
						<td style="width:25%;">
							<input name="yName" type="text" value="<?=$row["m02_yname"];?>" maxlength="10" onKeyDown="__enterFocus();" onFocus="this.select();">
						</td>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>핸드폰번호</th>
						<td>
							<input name="yTel" type="text" value="<?=getPhoneStyle($row["m02_ytel"]);?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);" onChange="checkHPno(this);">
						</td>
						<th scope="row"></th>
						<td><input name="yDwnDate" type="hidden" value="<?=$row["m02_ydwndate"];?>" alt="스케줄다운로드일자" maxlength="8" class="phone" onKeyDown="__onlyNumber(this);" onFocus="this.blur();" onBlur="__getDate(this);" readOnly></td>
					</tr>
					<input name="yCode" type="hidden" value="<?=$row["m02_ycode"];?>" alt="요양보호사코드" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getYYSCode(this);">
					<input name="yJimun" type="hidden" value="<?=$row["m02_yjimun"];?>" alt="지문코드" maxlength="12" onKeyDown="__enterFocus();" onFocus="this.blur();" readOnly>
					<tr>
						<th scope="row">바우처업무유무</th>
						<td>
							<select name="sign1" onKeyDown="__enterFocus();">
								<option value="Y"<? if($row["m02_sign1"] == "Y"){echo "selected";}?>>유</option>
								<option value="N"<? if($row["m02_sign1"] == "N"){echo "selected";}?>>무</option>
							</select>
						</td>
						<th scope="row">재가요양업무유무</th>
						<td>
							<select name="sign2" onKeyDown="__enterFocus();">
								<option value="Y"<? if($row["m02_sign2"] == "Y"){echo "selected";}?>>유</option>
								<option value="N"<? if($row["m02_sign2"] == "N"){echo "selected";}?>>무</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">사회서비스관리원 승인유무</th>
						<td>
							<!--
							<select name="yInSign1" onFocus="this.blur();" onChange="this.value='Y';">
								<option value="Y"<? if($row["m02_yinsign1"] == "Y"){echo "selected";}?>>유</option>
								<option value="N"<? if($row["m02_yinsign1"] == "N"){echo "selected";}?>>무</option>
							</select>
							-->
							<?
								if ($row["m02_yinsign1"] == 'Y'){
									$inSing1 = '유';
								}else{
									$inSing1 = '무';
								}
							?>
							<input type="text" value="<?=$inSing1;?>" onFocus="this.blur();" readOnly>
							<input name="yInSign1" type="hidden" value="<?=$row["m02_yinsign1"];?>">
						</td>
						<th scope="row">건강보험관리공단 승인유무</th>
						<td>
							<!--
							<select name="yInSign2" onFocus="this.blur();" onChange="this.value='Y';">
								<option value="Y"<? if($row["m02_yinsign2"] == "Y"){echo "selected";}?>>유</option>
								<option value="N"<? if($row["m02_yinsign2"] == "N"){echo "selected";}?>>무</option>
							</select>
							-->
							<?
								if ($row["m02_yinsign2"] == 'Y'){
									$inSing2 = '유';
								}else{
									$inSing2 = '무';
								}
							?>
							<input type="text" value="<?=$inSing2;?>" onFocus="this.blur();" readOnly>
							<input name="yInSign2" type="hidden" value="<?=$row["m02_yinsign2"];?>">
						</td>
					</tr>
					<tr>
						<th scope="row">사회서비스관리원 승인날짜</th>
						<td><input name="yInDate1" type="text" value="<?=$row["m02_yindate1"];?>" maxlength="8" class="phone" onKeyDown="__onlyNumber(this);" onFocus="this.blur();" onBlur="__getDate(this);" readOnly></td> <!--__replace(this, '-', '');-->
						<th scope="row">건강보험관리공단 승인날짜</th>
						<td><input name="yInDate2" type="text" value="<?=$row["m02_yindate2"];?>" maxlength="8" class="phone" onKeyDown="__onlyNumber(this);" onFocus="this.blur();" onBlur="__getDate(this);" readOnly></td>
					</tr>
					<tr>
						<!--
						<th scope="row">근무형태</th>
						<td>
							<select name="yGunmuKind" onKeyDown="__enterFocus();">
								<option value="1"<? if($row["m02_ygunmu_kind"] == "1"){echo "selected";}?>>시간제</option>
								<option value="2"<? if($row["m02_ygunmu_kind"] == "2"){echo "selected";}?>>계약직</option>
								<option value="3"<? if($row["m02_ygunmu_kind"] == "3"){echo "selected";}?>>전임</option>
								<option value="4"<? if($row["m02_ygunmu_kind"] == "4"){echo "selected";}?>>겸임</option>
							</select>
						</td>
						-->
						<th scope="row">직종</th>
						<td>
							<select name="yJikJong" style="width:100%;" onKeyDown="__enterFocus();">
							<?
								$sql = $conn->get_query("98");
								$conn->query($sql);
								$row2 = $conn->fetch();
								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row2 = $conn->select_row($i);
								?>
									<option value="<?=$row2[0];?>"<? if($row["m02_yjikjong"] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
								<?
								}

								$conn->row_free();
							?>
							</select>
						</td>
						<th scope="row"></th>
						<td></td>
					</tr>
					<tr>
						<th scope="row">자격종류</th>
						<td>
							<select name="yJakukKind" style="width:100%;" onKeyDown="__enterFocus();">
							<?
								$sql = $conn->get_query("99");
								$conn->query($sql);
								$row2 = $conn->fetch();
								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row2 = $conn->select_row($i);
								?>
									<option value="<?=$row2[0];?>"<? if($row["m02_yjakuk_kind"] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
								<?
								}

								$conn->row_free();
							?>
							</select>
						</td>
						<th scope="row">자격증번호</th>
						<td><input name="yJagyukNo" type="text" value="<?=$row["m02_yjagyuk_no"];?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this);" onFocus="this.select();"></td>
					</tr>
					<tr>
						<th scope="row">발급일</th>
						<td><input name="yJakukDate" type="text" value="<?=$row["m02_yjakuk_date"];?>" maxlength="11" class="date" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"></td>
						<th scope="row">간병경력</th>
						<td><input name="yGyeongyuk" type="text" value="<?=$row["m02_ygyeongyuk"];?>" maxlength="4" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}"></td>
					</tr>
					<tr>
						<th scope="row">유선전화번호</th>
						<td colspan="3"><input name="yTel2" type="text" value="<?=$row["m02_ytel2"];?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);"></td>
					</tr>
					<tr>
						<th scope="row">우편번호</th>
						<td>
							<input name="yPostNo1" type="text" value="<?=substr($row["m02_ypostno"],0,3);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();"> -
							<input name="yPostNo2" type="text" value="<?=substr($row["m02_ypostno"],3,6);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();">
							<a href="#" onClick="__helpAddress(document.center.yPostNo1, document.center.yPostNo2, document.center.yJuso1, document.center.yJuso2);">찾기</a>
						</td>
						<th scope="row">주소</th>
						<td>
							<input name="yJuso1" type="text" value="<?=$row["m02_yjuso1"];?>" maxlength="20" style="width:100%;" onKeyDown="__enterFocus();" onFocus="this.select();"><br>
							<input name="yJuso2" type="text" value="<?=$row["m02_yjuso2"];?>" maxlength="20" style="width:100%;" onKeyDown="__enterFocus();" onFocus="this.select();">
						</td>
					</tr>
					<tr>
						<th colspan="4" style="text-align:center;">근무상태</th>
					</tr>
					<tr>
						<th scope="row">고용형태</th>
						<td>
							<select name="yGoyongKind" onKeyDown="__enterFocus();">
								<option value="1"<? if($row["m02_ygoyong_kind"] == "1"){echo "selected";}?>>정규직</option>
								<option value="2"<? if($row["m02_ygoyong_kind"] == "2"){echo "selected";}?>>계약직</option>
								<option value="3"<? if($row["m02_ygoyong_kind"] == "3"){echo "selected";}?>>시간직</option>
								<option value="4"<? if($row["m02_ygoyong_kind"] == "4"){echo "selected";}?>>기타</option>
								<option value="5"<? if($row["m02_ygoyong_kind"] == "5"){echo "selected";}?>>특수근로</option>
							</select>
						</td>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>고용상태</th>
						<td>
							<select name="yGoyongStat" onKeyDown="__enterFocus();" onChange= "_goyongStatChanged();">
								<option value="1"<? if($row["m02_ygoyong_stat"] == "1"){echo "selected";}?>>활동</option>
								<option value="2"<? if($row["m02_ygoyong_stat"] == "2"){echo "selected";}?>>휴직</option>
								<option value="9"<? if($row["m02_ygoyong_stat"] == "9"){echo "selected";}?>>퇴사</option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>입사일자</th>
						<td><input name="yIpsail" type="text" value="<?=$myF->dateStyle($row["m02_yipsail"]);?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"></td>

						<th scope="row">퇴사일자</th>
						<td><input name="yToisail" type="text" value="<?=$myF->dateStyle($row["m02_ytoisail"]);?>" maxlength="8" class="date" tag="<?=$myF->dateStyle($row["m02_ytoisail"]);?>" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"></td>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>근무가능요일</th>
						<td colspan="3">
							<input name="yGunmuMon" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_mon"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">월</font>
							<input name="yGunmuTue" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_tue"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">화</font>
							<input name="yGunmuWed" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_wed"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">수</font>
							<input name="yGunmuThu" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_thu"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">목</font>
							<input name="yGunmuFri" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_fri"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">금</font>
							<input name="yGunmuSat" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_sat"] == "Y"){echo "checked";}?>><font style="font-weight:bold; color:#0000ff;">토</font>
							<input name="yGunmuSun" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($row["m02_ygunmu_sun"] == "Y"){echo "checked";}?>><font style="font-weight:bold; color:#ff0000;">일</font>
						</td>
					</tr>
					<tr>
						<th colspan="4" style="text-align:center;">급여마스터</th>
					</tr>
					<tr>
						<?
							if ($row["m02_ygupyeo_kind"] == '1' || $row["m02_ygupyeo_kind"] == '2'){
								if ($row['m02_pay_type'] != 'Y'){
									$payType = '1';
								}else{
									$payType = '2';
								}
							}else if ($row["m02_ygupyeo_kind"] == '3'){
								$payType = '3';
							}else if ($row["m02_ygupyeo_kind"] == '4'){
								$payType = '4';
							}else{
								$payType = '2';
							}

							switch($payType){
							case '1':
								$gibonKup2 = '0';
								$gibonKup3 = '0';
								$sugaYul = '0';
								break;
							case '2':
								$gibonKup2 = $row["m02_ygibonkup"];
								$gibonKup3 = '0';
								$sugaYul = '0';
								break;
							case '3':
								$gibonKup2 = '0';
								$gibonKup3 = $row["m02_ygibonkup"];
								$sugaYul = '0';
								break;
							case '4':
								$gibonKup2 = '0';
								$gibonKup3 = '0';
								$sugaYul = $row["m02_ysuga_yoyul"];
								break;
							}
						?>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>급여산정방식</th>
						<td>
							<select name="yGupyeoKind" onChange="selectGupyeoType();" onKeyDown="__enterFocus();">
							<?
								$sql = $conn->get_gubun('GYT');
								$conn->query($sql);
								$row2 = $conn->fetch();
								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row2 = $conn->select_row($i);
								?>
									<option value="<?=$row2[0];?>"<? if($row["m02_ygupyeo_kind"] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
								<?
								}

								$conn->row_free();
							?>
							</select><span id="gupyeoType_span" style="display:<? if($payType != '1' && $payType != '2'){echo 'none';} ?>;">(<input name="gupyeoType" type="checkbox" value="Y" class="checkbox" onClick="selectGupyeoType();" <? if ($fixPayType == 'Y'){echo 'checked';} ?>>고정급)</span>
						</td>
						<th id="payTr1" style="display:<? if($payType != '1'){echo 'none';} ?>;" scope="row"><span style="color:ff0000; padding-right:3px;">★</span>시급</th>
						<td id="payTd1" style="display:<? if($payType != '1'){echo 'none';} ?>;">
						<?
							$sql = "select m81_code, m81_name, m02_pay"
								 . "  from m81gubun"
								 . "  left join m02pay"
								 . "    on m02_ccode = '".$_POST["mCode"]
								 . "'  and m02_mkind = '".$_POST["mKind"]
								 . "'  and m02_jumin = '".$row['m02_yjumin']
								 . "'  and m02_gubun = m81_code"
								 . " where m81_gbn   = 'LVL'"
								 . " order by m81_code";
							$conn->query($sql);
							$conn->fetch();
							$row_count = $conn->row_count();

							for($i=0; $i<$row_count; $i++){
								$row2 = $conn->select_row($i);

								$timePay = $row2["m02_pay"];

								if ($timePay == ''){
									$timePay = $gibonKup; //$row["m02_ygibonkup"];
								}

								echo $row2['m81_name'];
								echo '<input name="yGibonKup[]" type="text" value="'.number_format($timePay).'" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==\'\'){this.value=\'0\';}else{__commaSet(this);}"><br>';
								echo '<input name="yGibonKupCode[]" type="hidden" value="'.$row2['m81_code'].'">';
							}

							$conn->row_free();
						?>
						</td>

						<th id="payTr2" style="display:<? if($payType != '2'){echo 'none';} ?>;" scope="row"><span style="color:ff0000; padding-right:3px;">★</span>시급</th>
						<td id="payTd2" style="display:<? if($payType != '2'){echo 'none';} ?>;">
							<input name="yGibonKup2" type="text" value="<?=number_format($gibonKup2);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">
						</td>

						<th id="payTr3" style="display:<? if($payType != '3'){echo 'none';} ?>;" scope="row"><span style="color:ff0000; padding-right:3px;">★</span>기본급</th>
						<td id="payTd3" style="display:<? if($payType != '3'){echo 'none';} ?>;">
							<input name="yGibonKup3" type="text" value="<?=number_format($gibonKup3);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">
						</td>

						<th id="payTr4" style="display:<? if($payType != '4'){echo 'none';} ?>;" scope="row"><span style="color:ff0000; padding-right:3px;">★</span>수가총액비 요율</th>
						<td id="payTd4" style="display:<? if($payType != '4'){echo 'none';} ?>;">
							<input name="ySugaYoyul" type="text" value="<?=$sugaYul;?>" maxlength="3" class="number" onKeyDown="__onlyNumber(this);" onBlur="if(this.value == ''){this.value = '0';}">%
						</td>
					</tr>
					<tr>
						<th scope="row">동거가족케어유무</th>
						<td>
							<select name="yFamCareUmu" onKeyDown="__enterFocus();" onChange="_familyCareInsYN();">
								<option value="Y"<? if($row["m02_yfamcare_umu"] == "Y"){echo "selected";}?>>유</option>
								<option value="N"<? if($row["m02_yfamcare_umu"] != "Y"){echo "selected";}?>>무</option>
							</select>
						</td>
						<th scope="row">친족케어시급</th>
						<td>
							<input name="yFamCarePay" type="text" value="<?=number_format($row['m02_yfamcare_pay'])?>" tag="<?=number_format($row['m02_yfamcare_pay'])?>" maxlength="8" class="number" style="<?=$row["m02_yfamcare_umu"] != 'Y' ? 'background-color:#eee;' : '';?>" onKeyDown="__onlyNumber(this);" onFocus="<?=$row["m02_yfamcare_umu"] == 'Y' ? '__commaUnset(this);' : 'this.blur();';?>" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" <? if($row["m02_yfamcare_umu"] != 'Y'){?>readOnly<?} ?>>
						</td>
					</tr>
					<input name="ySudang" type="hidden" value="0">
					<tr>
						<th scope="row">급여지급계좌번호</th>
						<td><input name="yGyeojaNo" type="text" value="<?=$row["m02_ygyeoja_no"];?>" maxlength="15" class="phone" onKeyDown="__onlyNumber(this, '189 109');" onFocus="this.select();"></td>
						<th scope="row">급여지급 은행명</th>
						<td><input name="yBankName" type="text" value="<?=$row["m02_ybank_name"];?>" maxlength="10" onKeyDown="__enterFocus();" onFocus="this.select();"></td>
					</tr>

					<!-- 급여테이블 수당 항목설정 -->
					<?
						$sql = "select concat(t25_pay_kind1, '_', t25_pay_kind2, '_', t25_pay_code) as code
								,      t25_pay_amount as amount
								  from t25payfix
								 where t25_ccode = '$mCode'
								   and t25_mkind = '$mKind'
								   and t25_yoy_code = '".$row["m02_yjumin"]."'";
						$conn->query($sql);
						$conn->fetch();
						$rowCount = $conn->row_count();

						for($i=0; $i<$rowCount; $i++){
							$myFixPay[$i] = $conn->select_row($i);
						}
						$conn->row_free();

						$sql = "select t20_kind1
								,      t20_kind2
								,      t20_code
								,      t20_name
								  from t20subject
								 where t20_ccode = '$mCode'
								   and t20_fix   = 'Y'
								   and t20_use   = 'Y'
								   and t20_name != ''
								 order by t20_kind1, t20_kind2, t20_code";
						$conn->query($sql);
						$conn->fetch();
						$rowCount = $conn->row_count();
						$tr = false;

						if ($rowCount > 0){
							for($i=0; $i<$rowCount; $i++){
								$pay = $conn->select_row($i);

								if ($i % 2 == 0){
									if ($tr == true){
										echo '</tr>';
									}
									echo '<tr>';
									$tr = true;
								}

								$payString = "";

								if ($pay["t20_kind1"] == "1"){
									if ($pay["t20_kind2"] == "1"){
										$payString = "(과세)";
									}else if ($pay["t20_kind2"] == "2"){
										$payString = "(비과세)";
									}
								}else{
									$payString = "(공제)";
								}

								$myFixAmount = 0;
								for($j=0; $j<sizeOf($myFixPay); $j++){
									if ($myFixPay[$j]['code'] == $pay["t20_kind1"].'_'.$pay["t20_kind2"].'_'.$pay["t20_code"]){
										$myFixAmount = number_format($myFixPay[$j]['amount']);
										break;
									}
								}

								echo '
									<th scope="row">'.$pay["t20_name"].$payString.'</th>
									<td><input name="pay_'.$pay["t20_kind1"].'_'.$pay["t20_kind2"].'_'.$pay["t20_code"].'" type="text" value="'.$myFixAmount.'" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onBlur="if(this.value == \'\'){this.value = \'0\';}"></td>
									 ';
							}
							if ($rowCount % 2 != 0){
								echo '
									<th scope="row"></th>
									<td></td>
									 ';
							}
							echo '</tr>';
						}
						$conn->row_free();
					?>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>4대보험 가입유무</th>
						<td colspan="3">
							<select name="y4BohumUmu" onKeyDown="__enterFocus();" onChange="setInsType();">
								<option value="Y"<? if(($row["m02_y4bohum_umu"]!=null?$row["m02_y4bohum_umu"]:'Y') == "Y"){echo "selected";}?>>유</option>
								<option value="N"<? if(($row["m02_y4bohum_umu"]!=null?$row["m02_y4bohum_umu"]:'Y') == "N"){echo "selected";}?>>무</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">고용보험 가입여부</th>
						<td>
							<select name="yGoBohumUmu" onKeyDown="__enterFocus();" style="display:<? if(($row["m02_y4bohum_umu"]!=null?$row["m02_y4bohum_umu"]:'Y') == 'Y'){echo 'none';} ?>;">
								<option value="Y"<? if($row["m02_ygobohum_umu"] == "Y"){echo "selected";}?>>유</option>
								<option value="N"<? if($row["m02_ygobohum_umu"] == "N"){echo "selected";}?>>무</option>
							</select>
							<div id="bohum1" style="display:<? if(($row["m02_y4bohum_umu"]!=null?$row["m02_y4bohum_umu"]:'Y') != 'Y'){echo 'none';} ?>;">유</div>
						</td>
						<th scope="row">산재보험 가입유무</th>
						<td>
							<select name="ySnBohumUmu" onKeyDown="__enterFocus();" style="display:<? if(($row["m02_y4bohum_umu"]!=null?$row["m02_y4bohum_umu"]:'Y') == 'Y'){echo 'none';} ?>;">
								<option value="Y"<? if($row["m02_ysnbohum_umu"] == "Y"){echo "selected";}?>>유</option>
								<option value="N"<? if($row["m02_ysnbohum_umu"] == "N"){echo "selected";}?>>무</option>
							</select>
							<div id="bohum2" style="display:<? if(($row["m02_y4bohum_umu"]!=null?$row["m02_y4bohum_umu"]:'Y') != 'Y'){echo 'none';} ?>;">유</div>
						</td>
					</tr>
					<tr>
						<th scope="row">건강보험 가입여부</th>
						<td>
							<select name="yGnBohumUmu" onKeyDown="__enterFocus();" style="display:<? if(($row["m02_y4bohum_umu"]!=null?$row["m02_y4bohum_umu"]:'Y') == 'Y'){echo 'none';} ?>;">
								<option value="Y"<? if($row["m02_ygnbohum_umu"] == "Y"){echo "selected";}?>>유</option>
								<option value="N"<? if($row["m02_ygnbohum_umu"] == "N"){echo "selected";}?>>무</option>
							</select>
							<div id="bohum3" style="display:<? if(($row["m02_y4bohum_umu"]!=null?$row["m02_y4bohum_umu"]:'Y') != 'Y'){echo 'none';} ?>;">유</div>
						</td>
						<th scope="row">국민연금 가입유무</th>
						<td>
							<select name="yKmBohumUmu" onKeyDown="__enterFocus();" style="display:<? if(($row["m02_y4bohum_umu"]!=null?$row["m02_y4bohum_umu"]:'Y') == 'Y'){echo 'none';} ?>;">
								<option value="Y"<? if($row["m02_ykmbohum_umu"] == "Y"){echo "selected";}?>>유</option>
								<option value="N"<? if($row["m02_ykmbohum_umu"] == "N"){echo "selected";}?>>무</option>
							</select>
							<div id="bohum4" style="display:<? if(($row["m02_y4bohum_umu"]!=null?$row["m02_y4bohum_umu"]:'Y') != 'Y'){echo 'none';} ?>;">유</div>
						</td>
					</tr>
					<tr>
						<th scope="row">공제대상 가족수(본인포함)</th>
						<td><input name="yGongJeJaNo" type="text" value="<?=$row["m02_ygongjeja_no"];?>" maxlength="2" class="number" onKeyDown="__onlyNumber(this);" onBlur="if(this.value == ''){this.value = '0';}"></td>
						<th scope="row">20세이하 자녀수</th>
						<td><input name="yGongJeJayeNo" type="text" value="<?=$row["m02_ygongjejaye_no"];?>" maxlength="2" class="number" onKeyDown="__onlyNumber(this);" onBlur="if(this.value == ''){this.value = '0';}"></td>
					</tr>
					<tr>
						<th colspan="4" style="text-align:center;">배상책임보험</th>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>현재 적용보험 반영유무</th>
						<td>
						<?
							if ($insCode > 0){
							?>
								<select name="insYN" style="width:auto;" tag="<?=$insYN;?>" onChange="_setInsData();">
									<option value="Y" <?=$insYN == 'Y' ? 'selected' : '';?>>가입</option>
									<option value="N" <?=$insYN != 'Y' ? 'selected' : '';?>>해지</option>
								</select>
							<?
							}else{
							?>
								<input name="insYN" type="hidden" value="N">무
							<?
							}
						?>
						</td>
						<th scope="row">기관보험가입기간</th>
						<td><?=$myF->dateStyle($ins[1]).($insCode > 0 ? '~' : '-').$myF->dateStyle($ins[2]);?></td>
						<input name="centerInsFromDate" type="hidden" value="<?=$myF->dateStyle($ins[1]);?>">
						<input name="centerInsToDate" type="hidden" value="<?=$myF->dateStyle($ins[2]);?>">
					</tr>
					<tr>
						<th scope="row">보험사명</th>
						<td>
							<input name="insCode" type="hidden" value="<?=$insCode;?>">
							<input name="insName" type="hidden" value="<?=$insName;?>">
							<span id="id_insName"><?=($insYN == 'Y' ? $insName : '-');?></span>
						</td>
						<th scope="row">가입상품명(배상책임보험)</th>
						<td>
							<span id="id_insItemName"><?=($insYN == 'Y' ? '배상책임보험' : '-');?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>보험가입기간(가입일)</th>
						<td>
							<input name="insFromDate" type="text" value="<?=$myF->dateStyle($insFromDate);?>" tag="<?=$myF->dateStyle($insFromDate);?>" alt="_checkInsLimitDate" maxlength="11" class="date" style="width:70px; display:<?=$insYN == 'Y' ? 'none' : '';?>;" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);" onChange="_checkInsLimitDate();">
							<span id="id_insFromDate" style="display:<?=$insYN == 'Y' ? 'none' : '';?>;"><?=($insYN == 'Y' ? $myF->dateStyle($insFromDate) : '');?></span>
						</td>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>보험가입기간(해지일)</th>
						<td>
							<input name="insToDate" type="text" value="<?=$myF->dateStyle($insToDate);?>" tag="<?=$myF->dateStyle($insToDate);?>" alt="_checkInsLimitDate" maxlength="11" class="date" style="width:70px; display:<?=$insYN == 'Y' ? 'none' : '';?>;" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);" onChange="_checkInsLimitDate();">
							<span id="id_insToDate" style="display:<?=$insYN == 'Y' ? 'none' : '';?>;"><?=($insYN == 'Y' ? $myF->dateStyle($insToDate) : '');?></span>
						</td>
					</tr>
					<tr>
						<th colspan="4" style="text-align:center;">기타</th>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>국민연금 신고 월급여액</th>
						<td><input name="yKuksinMpay" type="text" value="<?=number_format($row["m02_ykuksin_mpay"]);?>" maxlength="10" class="phone" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);"></td>
						<th scope="row">소득세 원천징수 구분</th>
						<td>
							<select name="ySowonGbn" style="width:100%;" onKeyDown="__enterFocus();">
								<option value="1"<? if($row["m02_ysowon_gbn"] == "1"){echo "selected";}?>>원천징수 안함</option>
								<option value="2"<? if($row["m02_ysowon_gbn"] == "2"){echo "selected";}?>>근로소득세(4대보험가입자)</option>
								<option value="3"<? if($row["m02_ysowon_gbn"] == "3"){echo "selected";}?>>3.3% 공제(주민세포함)</option>
							</select>
						</td>
					</tr>
					<tr>
						<th colspan="4" style="text-align:center;">관리자 선택항목</th>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>직원 여부</th>
						<td colspan="3">
							<select name="jikwonGbn" style="width:auto;" onKeyDown="__enterFocus();">
								<option value="Y"<? if($row["m02_jikwon_gbn"] == "Y"){echo "selected";}?>>요양보호사</option>
								<option value="M"<? if($row["m02_jikwon_gbn"] == "M"){echo "selected";}?>>관리자</option>
								<option value="A"<? if($row["m02_jikwon_gbn"] == "A"){echo "selected";}?>>관리자+요양보호사</option>
							</select>
						</td>
					</tr>
				</table>
				<input name="editMode" type="hidden" value="<?=$editMode;?>">
				<input name="key"   type="hidden" value="<?=$row["m02_key"];?>">
				<input name="insNo" type="hidden" value="<?=$row['m02_ins_no'] != null ? $row['m02_ins_no'] : '';?>">
			</td>
		</tr>
	</tbody>
</table>
</form>
<table style="width:100%;">
<colGroup>
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
</colGroup>
<tr>
	<td class="button" style="vertical-align:bottom;">
	<?
		echo $buttonGroup;
	?>
	</td>
</tr>
</table>
<?
	include("../inc/_db_close.php");
?>

<div id="idTalkPopup" style="z-index:11; left:0; top:0; width:220px; position:absolute; color:#000000; display:none;">
</div>
