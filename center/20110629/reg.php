<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	/*
	 * 기능		: 기관등록 / 수정
	 * 작성자	: 김재용
	 * 일자		: 2011.03.21
	 */

	$is_use_center[2] = true; //바우처 사용여부
	$is_use_center[3] = false; //시설 사용여부

	// 최저시급
	$sql = "select g07_pay_time
			  from g07minpay
			 where g07_year = '".date('Y', mktime())."'";
	$min_hourly = $conn->get_data($sql);

	if ($_SESSION["userLevel"] == "A"){
		$mCode = $_REQUEST["mCode"];
	}else{
		$mCode = $_SESSION["userCenterCode"];
	}

	$find_center_code = $_POST["find_center_code"];
	$find_center_name = $_POST["find_center_name"];
	$page = $_POST["page"];

	$kind = $conn->get_data("select min(m00_mkind) from m00center where m00_mcode = '$mCode' and m00_del_yn = 'N'");
	$sql = "select *
			  from m00center
			 where m00_mcode  = '$mCode'
			   and m00_del_yn = 'N'
			 order by m00_mkind";

	$conn->query($sql);
	$row = $conn->fetch();
	$rowCount = $conn->row_count();

	// 요양기관구분
	$centerGubun[0] = 'N';
	$centerGubun[1] = 'N';
	$centerGubun[2] = 'N';

	// 바우처 구분
	$gubun_1[1] = 'N';
	$gubun_1[2] = 'N';
	$gubun_1[3] = 'N';
	$gubun_1[4] = 'N';

	if ($rowCount > 0){
		$editMode = false;

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if ($row['m00_mkind'] == $kind){
				$cTel		= $myF->phoneStyle($row["m00_ctel"]);	//기관전화번호
				$cPostNo1	= subStr($row["m00_cpostno"], 0, 3);	//우편번호
				$cPostNo2	= subStr($row["m00_cpostno"], 3, 3);	//우편번호
				$cAddr1		= $row["m00_caddr1"];					//주소
				$cAddr2		= $row["m00_caddr2"];					//상세주소
				$mName		= $row["m00_mname"];					//대표자명
				$cCode		= $myF->bizStyle($row["m00_ccode"]);	//사업자번호
				$bankNo		= $row['m00_bank_no'];					//입금계좌번호
				$bankName	= $row['m00_bank_name'];				//입금은행
				$bankDepos	= $row['m00_bank_depos'];				//예금주
				$inwonsu	= number_format($row["m00_inwonsu"]);	//직원수
				$homepage	= $row["m00_homepage"];					//홈페이지
				$kupyeo1	= $row["m00_kupyeo_1"];					//방문요양 서비스 제공여부
				$kupyeo2	= $row["m00_kupyeo_2"];					//방문목욕 서비스 제공여부
				$kupyeo3	= $row["m00_kupyeo_3"];					//방문간호 서비스 제공여부
				$contDate	= $myF->dateStyle($row['m00_cont_date']);	//케어비지트 시작일자

				$sudangRenew	= $row['m00_sudang_renew'];
				$sudangNight	= $row['m00_sudang_night'];
				$sudangHoliday	= $row['m00_sudang_holiday'];
				$sudangMonth	= $row['m00_sudang_month'];

				$day_work_hour      = $row['m00_day_work_hour'];
				$day_hourly         = $row['m00_day_hourly'];
				$law_holiday_yn     = $row['m00_law_holiday_yn'];
				$law_holiday_pay_yn = $row['m00_law_holiday_pay_yn'];

				if ($day_hourly == 0) $day_hourly = $min_hourly;
			}

			$code1[$row['m00_mkind']]	= $row['m00_code1'];	//승인번호
			$cName[$row['m00_mkind']]	= $row["m00_cname"];					//기관명칭

			$mKind[$row['m00_mkind']]	= $row['m00_mkind'];	//요양기관구분
			$cDate[$row['m00_mkind']]	= $myF->dateStyle($row["m00_cdate"]);	//설치신고일자
			$jDate[$row['m00_mkind']]	= $myF->dateStyle($row["m00_jdate"]);	//지정일자

			$muksuYul1[$row['m00_mkind']]	= $row['m00_muksu_yul1'];	//목욕수당배분율(정)
			$muksuYul2[$row['m00_mkind']]	= $row['m00_muksu_yul2'];	//목욕수당배분율(부)

			if ($muksuYul1[$row['m00_mkind']]+$muksuYul2[$row['m00_mkind']] != 100){
				$muksuYul1[$row['m00_mkind']] = 50;
				$muksuYul2[$row['m00_mkind']] = 50;
			}

			$carNo1[$row['m00_mkind']]	= $row['m00_car_no1'];	//차량번호
			$carNo2[$row['m00_mkind']]	= $row['m00_car_no2'];	//차량번호
			$carNo3[$row['m00_mkind']]	= $row['m00_car_no3'];	//차량번호

			$bath_add_yn    = $row['m00_bath_add_yn'];		//목욕할증여부
			$nursing_add_yn = $row['m00_nursing_add_yn'];	//간호할증여부

			// 요양기관구분
			if ($row['m00_mkind'] == '0'){
				$centerGubun[0] = 'Y';
			}else if ($row['m00_mkind'] == '1' || $row['m00_mkind'] == '2' || $row['m00_mkind'] == '3' || $row['m00_mkind'] == '4'){
				$centerGubun[1] = 'Y';
				$gubun_1[$row['m00_mkind']] = 'Y';
			}else if ($row['m00_mkind'] == '5'){
				$centerGubun[2] = 'Y';
			}
		}
	}else{
		$editMode = true;

		$inwonsu = 0;
		$insName = 0;

		$sudangRenew	= 150;
		$sudangNight	= 150;
		$sudangHoliday	= 150;
		$sudangMonth	= 150;

		$muksuYul1[0] = 50;
		$muksuYul2[0] = 50;

		$bath_add_yn	= 'Y';
		$nursing_add_yn	= 'Y';

		$day_work_hour  = '2.5';

		$law_holiday_yn     = 'Y';
		$law_holiday_pay_yn = 'N';

		$day_hourly = $min_hourly;
	}
	$conn->row_free();

	if ($editMode){
		$title = '기관정보등록';
	}else{
		$title = '기관정보수정';
	}

	// 보험사
	$ins = $conn->get_array("select g02_ins_code, g02_ins_from_date, g02_ins_to_date
							   from g02inscenter
							  where g02_ccode = '$mCode'
							    and g02_mkind = '0'");
	if (is_array($ins)){
		$insName     = $ins[0];
		$insFromDate = $ins[1];
		$insToDate   = $ins[2];
	}else{
		$insName = 0;
	}

	// 직원들의 보험가입여부
	if ($insName != 0){
		$sql = "select count(*)
				  from m02yoyangsa
				 where m02_ccode = '$mCode'
				   and m02_mkind = '$kind'
				   and m02_ins_yn = 'Y'";
		if ($conn->get_data($sql) > 0){
			$change_ins_yn = false;
		}else{
			$change_ins_yn = true;
		}
	}else{
		$change_ins_yn = true;
	}

	// 비밀번호
	$pass = $conn->get_data("select m97_pass
							   from m97user
							  where m97_user = '$mCode'");

	$button  = "";
	$button .= "<span class='btn_pack m icon'><span class='list'></span><button type='button' onFocus='this.blur();' onClick='_list_center($page);'>리스트</button></span> ";

	if ($editMode){
		$button .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_save_center();'>등록</button></span> ";
	}else{
		$button .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_save_center();'>수정</button></span> ";

		if ($_SESSION["userLevel"] == "A"){
			$button .= "<span class='btn_pack m icon'><span class='delete'></span><button type='button' onFocus='this.blur();' onClick='_centerDelete();'>삭제</button></span>";
		}
	}
?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script language='javascript'>
<!--
window.onload = function(){
	var use2 = '<?=$is_use_center[2];?>';
	var use3 = '<?=$is_use_center[3];?>';

	var kind_1  = document.getElementById('kind_1').checked;

	if (use2) var kind_2  = document.getElementById('kind_2').checked;
	if (use3) var kind_3  = document.getElementById('kind_3').checked;

	var kupyeo2 = document.getElementById('kupyeo2').checked;
	var kupyeo3 = document.getElementById('kupyeo3').checked;

	if (use2){
		var kind_2_1 = document.getElementById('kind_2_1').checked;
		var kind_2_2 = document.getElementById('kind_2_2').checked;
		var kind_2_3 = document.getElementById('kind_2_3').checked;
		var kind_2_4 = document.getElementById('kind_2_4').checked;
	}

	_show_tbody_layer('tbody1', 'tbody_layer_1', kind_1);

	if (use2) _show_tbody_layer('tbody2', 'tbody_layer_2', kind_2);
	if (use3) _show_tbody_layer('tbody3', 'tbody_layer_3', kind_3);

	_show_tbody_layer('tbody_row_1_1', 'tbody_row_layer_1_1', kupyeo2);
	_show_tbody_layer('tbody_row_1_2', 'tbody_row_layer_1_2', kupyeo2);
	_show_tbody_layer('tbody_row_1_3', 'tbody_row_layer_1_3', kupyeo2);
	_show_tbody_layer('tbody_row_1_4', 'tbody_row_layer_1_4', kupyeo3);

	if (use2){
		_show_tbody_layer('tbody_row_2_1', 'tbody_row_layer_2_1', kind_2_1);
		_show_tbody_layer('tbody_row_2_2', 'tbody_row_layer_2_2', kind_2_1);
		_show_tbody_layer('tbody_row_2_3', 'tbody_row_layer_2_3', kind_2_1);
	}

	if (use3){
		_show_tbody_layer('tbody_row_3_1', 'tbody_row_layer_3_1', kind_2_2);
		_show_tbody_layer('tbody_row_3_2', 'tbody_row_layer_3_2', kind_2_2);
		_show_tbody_layer('tbody_row_3_3', 'tbody_row_layer_3_3', kind_2_2);

		_show_tbody_layer('tbody_row_4_1', 'tbody_row_layer_4_1', kind_2_3);
		_show_tbody_layer('tbody_row_4_2', 'tbody_row_layer_4_2', kind_2_3);
		_show_tbody_layer('tbody_row_4_3', 'tbody_row_layer_4_3', kind_2_3);

		_show_tbody_layer('tbody_row_5_1', 'tbody_row_layer_5_1', kind_2_4);
		_show_tbody_layer('tbody_row_5_2', 'tbody_row_layer_5_2', kind_2_4);
		_show_tbody_layer('tbody_row_5_3', 'tbody_row_layer_5_3', kind_2_4);
	}

	__init_form(document.f);
}

function set_holiday_pay_yn(gubun){
	var holiday_yn     = __get_value(document.getElementsByName(gubun+'_holiday_yn'));
	var holiday_pay_yn = document.getElementsByName(gubun+'_holiday_pay_yn');

	if (holiday_yn == 'Y'){
		holiday_pay_yn[0].disabled = false;
		holiday_pay_yn[1].disabled = false;
	}else{
		holiday_pay_yn[1].checked  = true;
		holiday_pay_yn[0].disabled = true;
		holiday_pay_yn[1].disabled = true;
	}
}
//-->
</script>
<form name="f" method="post">
<div class="title"><?=$title;?></div>
<table class="my_table my_border" >
	<colgroup>
		<col width="400px" span="2">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td style="vertical-align:top;">
				<table class="my_table" style="width:100%; border-bottom:0px;">
					<colgroup>
						<col width="50px">
						<col width="100px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th colspan="2">기관기호</th>
							<td style="border-right:none;">
							<?
								if ($editMode){
								?>	<input name="mCode" type="text" value="<?=$mCode;?>" maxlength="15" class="no_string" style="width:120px;" onFocus="this.select();" onChange="return _exist('mCode');" tag="승인번호를 입력하여 주십시오.">	<?
								}else{
								?>	<span style="height:25px; line-height:25px; margin-left:5px; font-weight:bold;"><?=$mCode;?></span><input name="mCode" type="hidden" value="<?=$mCode;?>"><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">기관전화번호</th>
							<td style="border-right:none;">
								<input name="cTel" type="text" value="<?=$cTel;?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);" tag="기관전화번호를 입력하여 주십시오.">
							</td>
						</tr>
						<tr>
							<th rowspan="3" style="padding:0; text-align:center;">소재</th>
							<th>우편번호</th>
							<td style="border-right:none;">
								<input name="cPostNo1" type="text" value="<?=$cPostNo1;?>" maxlength="3" class="phone" style="width:30px; text-align:center;" onKeyDown="__onlyNumber(this)" onFocus="this.select();"> -
								<input name="cPostNo2" type="text" value="<?=$cPostNo2;?>" maxlength="3" class="phone" style="width:30px; text-align:center; margin-right:0;" onKeyDown="__onlyNumber(this)" onFocus="this.select();">
								<span class="btn_pack small"><button type="button" onClick="__helpAddress(document.f.cPostNo1, document.f.cPostNo2, document.f.cAddr1, document.f.cAddr2);">찾기</button></span>
							</td>
						</tr>
						<tr>
							<th rowspan="2">주소</th>
							<td style="border-right:none;">
								<input name="cAddr1" type="text" value="<?=$cAddr1;?>" maxlength="20" style="width:100%;">
							</td>
						</tr>
						<tr>
							<td style="border-right:none;">
								<input name="cAddr2" type="text" value="<?=$cAddr2;?>" maxlength="30" style="width:100%;">
							</td>
						</tr>
						<tr>
							<th colspan="2">대표자명</th>
							<td style="border-right:none;">
								<input name="mName" type="text" value="<?=$mName;?>" maxlength="10" style="width:70px;" tag="대표자명을 입력하여 주십시오.">
							</td>
						</tr>
						<!--tr>
							<th colspan="2">연락처</th>
							<td style="border-right:none;">
								<input name="mTel" type="text" value="<?=$mTel;?>" maxlength="11" class="phone" style="width:80px;" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);">
							</td>
						</tr-->
						<tr>
							<th colspan="2">사업자등록번호</th>
							<td style="border-right:none;">
								<input name="cCode" type="text" value="<?=$cCode;?>" maxlength="10" class="phone" style="width:80px;" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', ''); this.select();" onBlur="__checkBizID(this);">
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="3">은행<br>계좌</th>
							<th>입금은행</th>
							<td style="border-right:none;">
								<select name="bankName" style="width:auto;">
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
							</td>
						</tr>
						<tr>
							<th>계좌번호</th>
							<td style="border-right:none;">
								<input name="bankNo" type="text" value="<?=$bankNo;?>" maxlength="30" style="width:100%;">
							</td>
						</tr>
						<tr>
							<th>예금주명</th>
							<td style="border-right:none;">
								<input name="bankDepos" type="text" value="<?=$bankDepos;?>" maxlength="30" style="width:100%;">
							</td>
						</tr>
						<tr>
							<th colspan="2">직원수</th>
							<td style="border-right:none;">
								<input name="inwonsu" type="text" value="<?=$inwonsu;?>" maxlength="5" class="number" style="width:50px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}">
							</td>
						</tr>
						<tr>
							<th colspan="2">홈페이지 주소</th>
							<td style="border-right:none;">
								<input name="homepage" type="text" value="<?=$homepage;?>" maxlength="30" style="width:100%;">
							</td>
						</tr>
						<tr>
							<th colspan="2">
								<div class="help_left">계약일자</div>
								<div class="help" onmouseover="_show_help(this, '케어비지트와의 계약일자를 입력하여 주십시오.');" onmouseout="_hidden_help();"></div>
							</th>
							<td style="border-right:none;">
								<input name="contDate" type="text" value="<?=$contDate;?>" maxlength="8" class="date" style="width:70px;" onKeyDown="__onlyNumber(this);"tag="케어비지트 처음 사용일자를 입력하여 주십시오." <? if(!$editMode){?>readonly<?}else{?>onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"<?} ?>>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="3">배상<br>책임<br>보험</th>
							<th>
								<div class="help_left">보험사명</div>
								<div class="help" onmouseover="_show_help(this, '보험사는 모든 직원의 보험이 가입해지가인 상태에서 변경이 가능합니다.');" onmouseout="_hidden_help();"></div>
							</th>
							<td class="<? if(!$change_ins_yn){?>left<?} ?> last">
							<?
								if ($change_ins_yn){
									echo '
										<select name="insName" style="width:auto;" onChange="_insItemList(this.value);">
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
								}else{
									$sql = "select g01_name
											  from g01ins
											 where g01_code = '$insName'";
									echo $conn->get_data($sql);
									echo '<input name="insName" type="hidden" value="'.$insName.'">';
								}
							?>
							</td>
						</tr>
						<tr>
							<th>가입상품명</th>
							<td style="border-right:none;">
								<div id="idInsItem" style="margin-left:5px;"><?=$insName > 0 ? '배상책임보험' : '-';?></div>
							</td>
						</tr>
						<tr>
							<th>보험가입기간</th>
							<td class="<? if(!$change_ins_yn){?>left<?} ?> last">
							<?
								if ($change_ins_yn){?>
									<input name="insFromDate"	type="text" value="<?=$myF->dateStyle($insFromDate);?>" tag="insToDate" maxlength="8" class="date" style="width:70px;" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onChange="_insToDate(this.value, 'insToDate');" onClick="_carlendar(this);"> ~
									<input name="insToDate"		type="text" value="<?=$myF->dateStyle($insToDate);?>"					maxlength="8" class="date" style="width:70px;" style="background:#eeeeee; cursor:defaultl;" onFocus="this.blur();" readOnly><?
								}else{?>
									<?=$myF->dateStyle($insFromDate,'.');?> ~ <?=$myF->dateStyle($insToDate,'.');?>
									<input name="insFromDate" type="hidden" value="<?=$myF->dateStyle($insFromDate);?>">
									<input name="insToDate"	  type="hidden" value="<?=$myF->dateStyle($insToDate);?>"><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">
								<div class="help_left">일근무기준시간/시급</div>
								<div class="help" onmouseover="_show_help(this, '기본급 및 각 수당(법정공휴일, 기관약정휴일이 유급인 경우)을 산출하기 위한 기준시간과 시급을 입력합니다.');" onmouseout="_hidden_help();"></div>
							</th>
							<td class="last">
								<input name="day_work_hour" type="text" value="<?=$day_work_hour;?>" maxlength="3" class="number" style="width:70px;" onKeyDown="__onlyNumber(this,'.');" onFocus="this.select();" tag="일근무기준시간을 입력하여 주십시오."> /
								<input name="day_hourly"    type="text" value="<?=number_format($day_hourly);?>" tag="<?=$min_hourly;?>" maxlength="8" class="number" style="width:70px;" onKeyDown="__onlyNumber(this,'.');" onFocus="this.select();" tag="일근무기준시급을 입력하여 주십시오.">
							</td>
						</tr>
						<tr>
							<th class="head" rowspan="2">법정<br>공휴일</th>
							<th>
								<div class="help_left">인정여부</div>
								<div class="help" onmouseover="_show_help(this, '일요일, 선거일등 법으로 고시된 날(법정공휴일)을 급여처리시 휴일로 간주하여 계산할 지의 여부.');" onmouseout="_hidden_help();"></div>
							</th>
							<td class="last">
								<input name="law_holiday_yn" type="radio" class="radio" value="Y" onclick="set_holiday_pay_yn('law');" <? if($law_holiday_yn == 'Y'){?>checked<?} ?>>유
								<input name="law_holiday_yn" type="radio" class="radio" value="N" onclick="set_holiday_pay_yn('law');" <? if($law_holiday_yn != 'Y'){?>checked<?} ?>>무
							</td>
						</tr>
						<tr>
							<th>
								<div class="help_left">급여여부</div>
								<div class="help" onmouseover="_show_help(this, '법정공휴일을 일근무기준시간으로 일급을 지급할 것인지의 여부.');" onmouseout="_hidden_help();"></div>
							</th>
							<td class="last">
								<input name="law_holiday_pay_yn" type="radio" class="radio" value="Y" <? if($law_holiday_yn != 'Y'){?>disabled="true"<?} if($law_holiday_pay_yn == 'Y'){?>checked<?} ?>>유급
								<input name="law_holiday_pay_yn" type="radio" class="radio" value="N" <? if($law_holiday_yn != 'Y'){?>disabled="true"<?} if($law_holiday_pay_yn != 'Y'){?>checked<?} ?>>무급
							</td>
						</tr>
						<tr>
							<th colspan="2">
								<div class="help_left">기관약정휴일</div>
								<div class="help" onmouseover="_show_help(this, '센터에서 정한 약정휴일이 있으면 등록합니다.');" onmouseout="_hidden_help();"></div>
							</th>
							<td class="last left">
								<span class="btn_pack m"><button id="btn_holiday" type="button" onclick="_show_center_holiday();">등록/수정/삭제</button></span>
							</td>
						</tr>
						<?
							if ($_SESSION['userLevel'] == 'A'){
							?>
								<tr>
									<th class="" colspan="2">비밀번호</th>
									<td class="left last"><?=$pass;?></td>
								</tr>
							<?
							}
						?>
					<tbody>
						<tr>
							<td class="last bottom" colspan="3">
								<p style="text-align:justify; line-height:1.3em; padding:5px;">
									<span>※5월1일(근로자의 날)은 법정공휴일 인정여부와 상관없이 급여처리시</span><br>
									<span style="padding-left:11px;">휴일로 처리됩니다.</span>
								</p>
							</td>
						</tr>
					</tbody>
					</tbody>
					<tbody>
						<tr>
							<td class="last bottom" colspan="3"></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td style="vertical-align:top;">
				<table class="my_table" style="width:100%; border-bottom:0px;">
					<colgroup>
						<col width="35px">
						<col width="115px">
						<col width="65px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th colspan="2">요양기관구분</th>
							<td style="border-right:none;" colspan="3">
								<input name="kind_1" type="checkbox" value="Y" class="checkbox" style="margin-right:0;" <? if($centerGubun[0] == 'Y'){echo 'checked';} ?> onClick="this.checked=true; _show_tbody_layer('tbody1', 'tbody_layer_1', this.checked);">재가
								<?
									if ($is_use_center[2]){?>
										<input name="kind_2" type="checkbox" value="Y" class="checkbox" style="margin-right:0;" <? if($centerGubun[1] == 'Y'){echo 'checked';} ?> onClick="_show_tbody_layer('tbody2', 'tbody_layer_2', this.checked);">바우처<?
									}else{?>
										<input name="kind_2" type="hidden" value="N"><?
									}

									if ($is_use_center[3]){?>
										<input name="kind_3" type="checkbox" value="Y" class="checkbox" style="margin-right:0;" <? if($centerGubun[2] == 'Y'){echo 'checked';} ?> onClick="_show_tbody_layer('tbody3', 'tbody_layer_3', this.checked);">시설<?
									}else{?>
										<input name="kind_3" type="hidden" value="N"><?
									}
								?>
							</td>
						</tr>
					</tbody>
					<tbody id="tbody1">
						<tr>
							<th rowspan="9" style="padding:0; text-align:center;">재</br>가</th>
							<th>승인번호</th>
							<td style="border-right:none;" colspan="3">
								<input name="code0" type="text" value="<?=$code1[0];?>" maxlength="15" class="phonerr" style="width:120px;" onKeyDown="__onlyNumber(this);" onFocus="this.select();" tag="가사간병바우처의 승인번호를 입력하여 주십시오.">
							</td>
						</tr>
						<tr>
							<th>기관명칭</th>
							<td style="border-right:none;" colspan="3">
								<input name="cName0" type="text" value="<?=$cName[0];?>" maxlength="20" style="width:100%;" onFocus="this.select();" tag="기관명을 입력하여 주십시오.">
							</td>
						</tr>
						<tr>
							<th>제공급여종류</th>
							<td style="border-right:none;" colspan="3">
								<input name="kupyeo1" type="checkbox" value="Y" class="checkbox" style="margin-right:0;" <? if($kupyeo1 == 'Y'){echo 'checked';} ?>>방문요양
								<input name="kupyeo2" type="checkbox" value="Y" class="checkbox" style="margin-right:0;" <? if($kupyeo2 == 'Y'){echo 'checked';} ?> onClick="_show_tbody_layer('tbody_row_1_1', 'tbody_row_layer_1_1', this.checked);
																																											 _show_tbody_layer('tbody_row_1_2', 'tbody_row_layer_1_2', this.checked);
																																											 _show_tbody_layer('tbody_row_1_3', 'tbody_row_layer_1_3', this.checked);">방문목욕
								<input name="kupyeo3" type="checkbox" value="Y" class="checkbox" style="margin-right:0;" <? if($kupyeo3 == 'Y'){echo 'checked';} ?> onClick="_show_tbody_layer('tbody_row_1_4', 'tbody_row_layer_1_4', this.checked);">방문간호
							</td>
						</tr>
						<tr>
							<th>설치신고일자</th>
							<td style="border-right:none;" colspan="3">
								<input name="cDate" type="text" value="<?=$cDate[0];?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);">
							</td>
						</tr>
						<tr>
							<th>지정일자</th>
							<td style="border-right:none;" colspan="3">
								<input name="jDate0" type="text" value="<?=$jDate[0];?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);">
							</td>
						</tr>
						<tr>
							<th>
								<div class="help_left">목욕수당 배분율</div>
								<div class="help" onmouseover="_show_help(this, '주담당자와 부담당자에게 주어질 수당의 배분율을 입력합니다. 센터내에 일률적으로 적용할 경우에 입력합니다. (생락시 50:50적용) 목욕수당이 요양보호사 개별로 책정이 될 경우에는 이 값은 무시됩니다.');" onmouseout="_hidden_help();"></div>
							</th>
							<td style="border-right:none;" colspan="2" id="tbody_row_1_1">
								<input name="sudangYul1" type="text" value="<?=number_format($muksuYul1[0]);?>" class="number" style="width:40px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);" onChange="_checkMuksuRate(this, document.f.sudangYul2);">% /
								<input name="sudangYul2" type="text" value="<?=number_format($muksuYul2[0]);?>" class="number" style="width:40px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);" onChange="_checkMuksuRate(this, document.f.sudangYul1);">%
							</td>
							<!--<td>
								<div class="help_left">목욕수당</div>
								<div class="help" onmouseover="_show_help(this, '목욕수당을 클릭하면 기관관리>고정수당관리 화면으로 이동합니다.(목욕수당 입력 가능)');" onmouseout="_hidden_help();"></div>
							</td>-->
						</tr>

						<tr>
							<th>목욕차량번호</th>
							<td style="border-right:none;" colspan="3" id="tbody_row_1_2">
								<input name="carNo1" type="text" value="<?=$carNo1[0];?>" style="width:70px; margin-right:0;" maxlength="10">
								<input name="carNo2" type="text" value="<?=$carNo2[0];?>" style="width:70px; margin-right:0; margin-left:0;" maxlength="10">
								<input name="carNo3" type="text" value="<?=$carNo3[0];?>" style="width:70px; margin-right:0; margin-left:0;" maxlength="10">
							</td>
						</tr>
						<tr>
							<th>
								<div class="help_left">목욕할증적용</div>
								<div class="help" onmouseover="_show_help(this, '방문목욕의 근무시간대가 18시 이후일 경우 시급의 50%를 할증하여 수당에 반영할 것인지의 유무를 선택합니다.');" onmouseout="_hidden_help();"></div>
							</th>
							<td style="border-right:none;" colspan="3" id="tbody_row_1_3">
								<input name="bath_add_yn" type="radio" class="radio" value="Y" <? if ($bath_add_yn == 'Y'){?>checked<?} ?>>유
								<input name="bath_add_yn" type="radio" class="radio" value="N" <? if ($bath_add_yn != 'Y'){?>checked<?} ?>>무
							</td>
						</tr>
						<tr>
							<th>
								<div class="help_left">간호할증적용</div>
								<div class="help" onmouseover="_show_help(this, '방문간호의 근무시간대가 18시 이후일 경우 시급의 50%를 할증하여 수당에 반영할 것인지의 유무를 선택합니다.');" onmouseout="_hidden_help();"></div>
							</th>
							<td style="border-right:none;" colspan="2" id="tbody_row_1_4">
								<input name="nursing_add_yn" type="radio" class="radio" value="Y" <? if ($nursing_add_yn == 'Y'){?>checked<?} ?>>유
								<input name="nursing_add_yn" type="radio" class="radio" value="N" <? if ($nursing_add_yn != 'Y'){?>checked<?} ?>>무
							</td>
							<!--<td>
								<div class="help_left">간호수당</div>
								<div class="help" onmouseover="_show_help(this, '간호수당을 클릭하면 기관관리>고정수당 관리 화면으로 이동합니다.(간호수당 입력가능)');" onmouseout="_hidden_help();"></div>
							</td>-->
						</tr>
					</tbody>
					<?
						if ($is_use_center[2]){?>
							<tbody id="tbody2">
								<tr>
									<th rowspan="16" style="padding:0; text-align:center;">바</br>우</br>처</th>
									<th rowspan="3" class="padding_no"><input name="kind_2_1" type="checkbox" class="checkbox" value="Y" <? if($gubun_1[1] == 'Y'){?>checked<?} ?> onClick="_show_tbody_layer('tbody_row_2_1', 'tbody_row_layer_2_1', this.checked); _show_tbody_layer('tbody_row_2_2', 'tbody_row_layer_2_2', this.checked); _show_tbody_layer('tbody_row_2_3', 'tbody_row_layer_2_3', this.checked);"><span style="margin-left:-5px;">가사간병</span></th>
									<th>승인번호</th>
									<td id="tbody_row_2_1" style="border-right:none;">
										<input name="code1" type="text" value="<?=$code1[1];?>" maxlength="15" class="phonerr" style="width:120px;" onKeyDown="__onlyNumber(this);" onFocus="this.select();" tag="가사간병바우처의 승인번호를 입력하여 주십시오.">
									</td>
								</tr>
								<tr>
									<th>명칭</th>
									<td id="tbody_row_2_2" style="border-right:none;">
										<input name="cName1" type="text" value="<?=$cName[1];?>" style="width:100%;" onFocus="this.select();" tag="가사간병바우처의 명칭을 입력하여 주십시오.">
									</td>
								</tr>
								<tr>
									<th>지정일자</th>
									<td id="tbody_row_2_3" style="border-right:none;">
										<input name="jDate1" type="text" value="<?=$jDate[1];?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);">
									</td>
								</tr>

								<tr>
									<th rowspan="3" class="padding_no"><input name="kind_2_2" type="checkbox" class="checkbox" value="Y" <? if($gubun_1[2] == 'Y'){?>checked<?} ?> onClick="_show_tbody_layer('tbody_row_3_1', 'tbody_row_layer_3_1', this.checked); _show_tbody_layer('tbody_row_3_2', 'tbody_row_layer_3_2', this.checked); _show_tbody_layer('tbody_row_3_3', 'tbody_row_layer_3_3', this.checked);"><span style="margin-left:-5px;">노인돌봄</span></th>
									<th>승인번호</th>
									<td id="tbody_row_3_1" style="border-right:none;">
										<input name="code2" type="text" value="<?=$code1[2];?>" maxlength="15" class="phonerr" style="width:120px;" onKeyDown="__onlyNumber(this);" onFocus="this.select();" tag="노인돌봄바우처의 승인번호를 입력하여 주십시오.">
									</td>
								</tr>
								<tr>
									<th>명칭</th>
									<td id="tbody_row_3_2" style="border-right:none;">
										<input name="cName2" type="text" value="<?=$cName[2];?>" style="width:100%;" onFocus="this.select();" tag="노인돌봄바우처의 명칭을 입력하여 주십시오.">
									</td>
								</tr>
								<tr>
									<th>지정일자</th>
									<td id="tbody_row_3_3" style="border-right:none;">
										<input name="jDate2" type="text" value="<?=$jDate[2];?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);">
									</td>
								</tr>

								<tr>
									<th rowspan="3" class="padding_no"><input name="kind_2_3" type="checkbox" class="checkbox" value="Y" <? if($gubun_1[3] == 'Y'){?>checked<?} ?> onClick="_show_tbody_layer('tbody_row_4_1', 'tbody_row_layer_4_1', this.checked); _show_tbody_layer('tbody_row_4_2', 'tbody_row_layer_4_2', this.checked); _show_tbody_layer('tbody_row_4_3', 'tbody_row_layer_4_3', this.checked);"><span style="margin-left:-5px;">산모신생아</span></th>
									<th>승인번호</th>
									<td id="tbody_row_4_1" style="border-right:none;">
										<input name="code3" type="text" value="<?=$code1[3];?>" maxlength="15" class="phonerr" style="width:120px;" onKeyDown="__onlyNumber(this);" onFocus="this.select();" tag="산모신생아바우처의 승인번호를 입력하여 주십시오.">
									</td>
								</tr>
								<tr>
									<th>명칭</th>
									<td id="tbody_row_4_2" style="border-right:none;">
										<input name="cName3" type="text" value="<?=$cName[3];?>" style="width:100%;" onFocus="this.select();" tag="산모신생아바우처의 명칭을 입력하여 주십시오.">
									</td>
								</tr>
								<tr>
									<th>지정일자</th>
									<td id="tbody_row_4_3" style="border-right:none;">
										<input name="jDate3" type="text" value="<?=$jDate[3];?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);">
									</td>
								</tr>

								<tr>
									<th rowspan="3" class="padding_no"><input name="kind_2_4" type="checkbox" class="checkbox" value="Y" <? if($gubun_1[4] == 'Y'){?>checked<?} ?> onClick="_show_tbody_layer('tbody_row_5_1', 'tbody_row_layer_5_1', this.checked); _show_tbody_layer('tbody_row_5_2', 'tbody_row_layer_5_2', this.checked); _show_tbody_layer('tbody_row_5_3', 'tbody_row_layer_5_3', this.checked);"><span style="margin-left:-5px;">장애인보조</span></th>
									<th>승인번호</th>
									<td id="tbody_row_5_1" style="border-right:none;">
										<input name="code4" type="text" value="<?=$code1[4];?>" maxlength="15" class="phonerr" style="width:120px;" onKeyDown="__onlyNumber(this);" onFocus="this.select();" tag="장애인보조바우처의 승인번호를 입력하여 주십시오.">
									</td>
								</tr>
								<tr>
									<th>명칭</th>
									<td id="tbody_row_5_2" style="border-right:none;">
										<input name="cName4" type="text" value="<?=$cName[4];?>" style="width:100%;" onFocus="this.select();" tag="장애인보조바우처의 명칭을 입력하여 주십시오.">
									</td>
								</tr>
								<tr>
									<th>지정일자</th>
									<td id="tbody_row_5_3" style="border-right:none;">
										<input name="jDate4" type="text" value="<?=$jDate[4];?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);">
									</td>
								</tr>
							</tbody><?
						}

						if ($is_use_center[3]){?>
							<tbody id="tbody3">
								<tr>
									<th rowspan="3" class="center">시</br>설</th>
									<th>승인번호</th>
									<td class="last" colspan="2">
										<input name="code5" type="text" value="<?=$code1[5];?>" maxlength="15" class="phonerr" style="width:120px;" onKeyDown="__onlyNumber(this);" onFocus="this.select();" tag="시설의 승인번호를 입력하여 주십시오.">
									</td>
								</tr>
								<tr>
									<th>명칭</th>
									<td class="last" colspan="2">
										<input name="cName5" type="text" value="<?=$cName[5];?>" style="width:100%;" onFocus="this.select();" tag="시설의 명칭을 입력하여 주십시오.">
									</td>
								</tr>
								<tr>
									<th>지정일자</th>
									<td class="last" colspan="2">
										<input name="jDate5" type="text" value="<?=$jDate[5];?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);">
									</td>
								</tr>
							</tbody><?
						}
					?>
					<tbody>
						<tr>
							<td class="last bottom" colspan="3"></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="other">&nbsp;</td>
		</tr>
	</tbody>
</table>

<input name="sudang_renew"	type="hidden" value="<?=$sudangRenew;?>">
<input name="sudang_night"	type="hidden" value="<?=$sudangNight;?>">
<input name="sudang_holiday"type="hidden" value="<?=$sudangHoliday;?>">
<input name="sudang_month"	type="hidden" value="<?=$sudangMonth;?>">

<input name="page"	type="hidden" value="<?=$page;?>">

<input name="edit_mode" type="hidden" value="<?=$editMode;?>">

<input name="find_center_code" type="hidden" value="<?=$find_center_code;?>">
<input name="find_center_name" type="hidden" value="<?=$find_center_name;?>">

<div style="width:100%; margin:0; padding:0; text-align:right; margin:5px;"><?=$button;?></div>
</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>