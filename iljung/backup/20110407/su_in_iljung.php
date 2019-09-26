<?
	include('../inc/_header.php');

	$mCode = $_GET['mCode'];
	$mKind = $_GET['mKind'];
	$mKey  = $_GET['mKey'];
	$mMode = $_GET['mMode'];

	$flagHistory = ($mMode == "IN" ? "Y" : "N"); // 히스토리 관리 플래그

	$sql = "select m00_kupyeo_1"
		 . ",      m00_kupyeo_2"
		 . ",      m00_kupyeo_3"
		 . ",      m00_muksu_yul1"
		 . ",      m00_muksu_yul2"
		 . ",      m00_cont_date"
		 . "  from m00center"
		 . " where m00_mcode = '".$mCode
		 . "'  and m00_mkind = '".$mKind
		 . "'";
	$conn->query($sql);
	$row = $conn->fetch();
	$svcSubCode[1] = $row['m00_kupyeo_1'];
	$svcSubCode[2] = $row['m00_kupyeo_2'];
	$svcSubCode[3] = $row['m00_kupyeo_3'];
	$sudangYul[1]  = $row['m00_muksu_yul1'];
	$sudangYul[2]  = $row['m00_muksu_yul2'];
	$centerStartDate = subStr($row['m00_cont_date'], 0, 6);
	$conn->row_free();

	if ($svcSubCode[1] == 'Y'){
		$visit[0] = 'true';
		$visit[1] = '#eeeeee';
	}else{
		if ($svcSubCode[2] != 'Y' and $svcSubCode[3] != 'Y'){
			$visit[0] = 'true';
			$visit[1] = '#eeeeee';
		}else{
			$visit[0] = 'false';
			$visit[1] = '#ffffff';
		}
	}

	$sugupjaJumin = $conn->get_sugupja_jumin($mCode, $mKind, $mKey); //수급자 주민번호
	$sugupjaLevel = $conn->get_sugupja_level($mCode, $mKind, $sugupjaJumin); //수급자 등급

	$sql = "select m03_yoyangsa1"
		 . ",     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa1 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa1 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1')"
		 . ",      m03_yoyangsa2"
		 . ",     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa2 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa2 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1')"
		 . ",      m03_yoyangsa3"
		 . ",     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa3 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa3 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1')"
		 . ",      m03_yoyangsa4"
		 . ",     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa4 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa4 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1')"
		 . ",      m03_yoyangsa5"
		 . ",     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa5 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa5 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygupyeo_kind from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa1 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygupyeo_kind from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa2 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygupyeo_kind from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa3 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygupyeo_kind from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa4 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygupyeo_kind from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa5 and m02_ygoyong_stat = '1')"
		 . ",      m03_familycare"
		 . "  from m03sugupja"
		 . " where m03_ccode = '".$mCode
		 . "'  and m03_mkind = '".$mKind
		 . "'  and m03_key   = '".$mKey
		 . "'";

	$conn->query($sql);
	$row = $conn->fetch();
	$yoy1   = $row[0];
	$yoyNm1 = $row[1];
	$yoyTA1 = $row[2];
	$yoy2   = ''; //$row[3];
	$yoyNm2 = ''; //$row[4];
	$yoyTA2 = ''; //$row[5];
	$yoy3   = ''; //$row[6];
	$yoyNm3 = ''; //$row[7];
	$yoyTA3 = ''; //$row[8];
	$yoy4   = ''; //$row[9];
	$yoyNm4 = ''; //$row[10];
	$yoyTA4 = ''; //$row[11];
	$yoy5   = ''; //$row[12];
	$yoyNm5 = ''; //$row[13];
	$yoyTA5 = ''; //$row[14];
	//$chkFamilyCare = ($row['m03_familycare']=='Y'?'checked':'');

	//echo $yoyTA1.'/'.$yoyTA2;

	$yoyKN1 = $row[15];
	$yoyKN2 = $row[16];
	$yoyKN3 = $row[17];
	$yoyKN4 = $row[18];
	$yoyKN5 = $row[19];
	$conn->row_free();

	if ($mMode != 'IN'){
		$mWeek = $_GET['mWeek'];
	}

	if ($mMode == 'IN' || $mMode == 'PATTERN'){
		$sql = "select m02_ygunmu_mon"
			 . ",      m02_ygunmu_tue"
			 . ",      m02_ygunmu_wed"
			 . ",      m02_ygunmu_thu"
			 . ",      m02_ygunmu_fri"
			 . ",      m02_ygunmu_sat"
			 . ",      m02_ygunmu_sun"
			 . ",      m02_ysugang"
			 . "  from m02yoyangsa"
			 . " where m02_ccode  = '".$mCode
			 . "'  and m02_mkind  = '".$mKind
			 . "'  and m02_yjumin = '".$yoy1
			 . "'";
		$conn->query($sql);
		$row = $conn->fetch();
		$weekDay[1] = $row['m02_ygunmu_mon'] != 'Y' ? 'disabled=true' : '';
		$weekDay[2] = $row['m02_ygunmu_tue'] != 'Y' ? 'disabled=true' : '';
		$weekDay[3] = $row['m02_ygunmu_wed'] != 'Y' ? 'disabled=true' : '';
		$weekDay[4] = $row['m02_ygunmu_thu'] != 'Y' ? 'disabled=true' : '';
		$weekDay[5] = $row['m02_ygunmu_fri'] != 'Y' ? 'disabled=true' : '';
		$weekDay[6] = $row['m02_ygunmu_sat'] != 'Y' ? 'disabled=true' : '';
		$weekDay[0] = $row['m02_ygunmu_sun'] != 'Y' ? 'disabled=true' : '';
		$visitSudang = 0;
		$conn->row_free();
	}else{
		$weekDay[1] = 'disabled=true';
		$weekDay[2] = 'disabled=true';
		$weekDay[3] = 'disabled=true';
		$weekDay[4] = 'disabled=true';
		$weekDay[5] = 'disabled=true';
		$weekDay[6] = 'disabled=true';
		$weekDay[0] = 'disabled=true';
		$weekDay[$mWeek] = 'checked onClick="this.checked=true;"';
	}
?>
<table style="width:900px;">
	<tr>
		<td style="width:150px; border-top:0px; font-weight:bold; background-color:#eeeeee;">제공서비스</td>
		<td style="border-top:0px; font-weight:bold; background-color:#eeeeee;" colspan="2">방문시간</td>
		<td style="width:80px; border-top:0px; font-weight:bold; background-color:#eeeeee;" id="svcTitle">소요시간</td>
		<td style="width:220px; border-top:0px; font-weight:bold; background-color:#eeeeee;">예외조건</td>
		<td style="border-top:0px; font-weight:bold; background-color:#eeeeee;" colspan="2">제공요일</td>
	</tr>
	<tr>
		<td style="text-align:center; padding:0px;">
			<input name="svcSubCode" type="radio" value="200" class="radio" <? if($svcSubCode[1] != 'Y'){echo 'disabled=true';} ?> onClick="_setSvc1();" checked>요양
			<input name="svcSubCode" type="radio" value="500" class="radio" <? if($svcSubCode[2] != 'Y'){echo 'disabled=true';} ?> onClick="_setSvc2();">목욕
			<input name="svcSubCode" type="radio" value="800" class="radio" <? if($svcSubCode[3] != 'Y'){echo 'disabled=true';} ?> onClick="_setSvc3();">간호
		</td>
		<td style="width:50px; font-weight:bold; background-color:#eeeeee;">시작</td>
		<td style="width:110px;">
			<input name="ftHour" type="text" value="" maxlength="2" class="number" style="text-align:center; width:30px;<? if ($mMode == 'MODIFY'){echo 'background:#eeeeee;';} ?>" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 2){document.f.ftMin.focus();}" onFocus="<? if ($mMode != 'MODIFY'){echo 'this.select();';}else{echo 'this.blur();';} ?>" <? if ($mMode == 'MODIFY'){echo 'readOnly';} ?> onBlur="_checkTimeH();">시
			<input name="ftMin"  type="text" value="" maxlength="2" class="number" style="text-align:center; width:30px;<? if ($mMode == 'MODIFY'){echo 'background:#eeeeee;';} ?>" onKeyDown="__onlyNumber(this);" onKeyUp="_setEntTimeFocus();" onFocus="<? if ($mMode != 'MODIFY'){echo 'this.select();';}else{echo 'this.blur();';} ?>" <? if ($mMode == 'MODIFY'){echo 'readOnly';} ?> onBlur="_checkTimeM();">분
		</td>
		<td style="">
			<select name="procTime" style="width:90%;" onChange="_setEndTime();">
				<option value="30">30분</option>
				<option value="60">60분</option>
				<option value="90">90분</option>
				<option value="120">120분</option>
				<option value="150">150분</option>
				<option value="180">180분</option>
				<option value="210">210분</option>
				<option value="240">240분</option>
				<option value="0">270분이상</option>
			</select>
		</td>
		<td style="text-align:left; padding-left:3px;" id="carBocy">
			<input name="togeUmu" type="checkbox" value="Y" class="checkbox" onClick="_setNeedTime(); _setIljungSuga();">동거가족
			<input name="bipayUmu" type="checkbox" value="Y" class="checkbox">비급여
			<input name="timeDoub" type="checkbox" value="Y" class="checkbox" style="display:none;"><!--시간중복-->
		</td>
		<td style="" colspan="2">
			<input name="weekDay1" type="checkbox" value="Y" class="checkbox" <?=$weekDay[1];?>>월
			<input name="weekDay2" type="checkbox" value="Y" class="checkbox" <?=$weekDay[2];?>>화
			<input name="weekDay3" type="checkbox" value="Y" class="checkbox" <?=$weekDay[3];?>>수
			<input name="weekDay4" type="checkbox" value="Y" class="checkbox" <?=$weekDay[4];?>>목
			<input name="weekDay5" type="checkbox" value="Y" class="checkbox" <?=$weekDay[5];?>>금
			<input name="weekDay6" type="checkbox" value="Y" class="checkbox" <?=$weekDay[6];?>><font color="#0000ff">토</font>
			<input name="weekDay0" type="checkbox" value="Y" class="checkbox" <?=$weekDay[0];?>><font color="#ff0000">일</font>
		</td>
	</tr>
	<tr>
		<td style="" rowspan="2" id="sugaCont"></td>
		<td style="font-weight:bold; background-color:#eeeeee;">종료</td>
		<td style="">
			<input name="ttHour" type="text" value="" maxlength="2" class="number" style="text-align:center; width:30px; background-color:#eeeeee;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 2){document.f.ttMin.focus();}"  onChange="if(parseInt(this.value) >= 24){this.value = '00';}" onBlur="_setEndTimeM();" readOnly>시
			<input name="ttMin"  type="text" value="" maxlength="2" class="number" style="text-align:center; width:30px; background-color:#eeeeee;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 2){document.f.yoyNm1.focus();}" onChange="if(this.value == ''){this.value = '00';}" onBlur="_setEndTimeM();" readOnly>분
		</td>
		<td style="border-top:0px; font-weight:bold; background-color:#eeeeee;">
			<div style="display:;" id="labelYoy">요양보호사</div>
			<div style="display:none;" id="objSvcSubCD">
				<select name="svcSubCD" style="width:90%;" onChange="_setEndTimeSub();">
					<option value="1">차량입욕</option>
					<option value="2">가정내입욕</option>
				</select>
			</div>
		</td>
		<td style="" colspan="3">
			<table>
				<tr>
					<td class="noborder" style="width:40px;"><img src="../image/btn_find.png" width="20" height="19" onClick="_helpSuYoy('<?=$_GET["mCode"];?>', '<?=$_GET["mKind"];?>', '<?=$_GET["mKey"];?>');" style="cursor:pointer;"></td>
					<td class="noborder" style="text-align:left;">
						<span></span>
						<input name="yoyNm1" type="text" value="<?=$yoyNm1;?>" style="width:70px; background-color:#eeeeee;" onClick="_helpSuYoyP('<?=$_GET["mCode"];?>', '<?=$_GET["mKind"];?>', '<?=$_GET["mKey"];?>', document.f.yoy1, document.f.yoyNm1, document.f.yoyTA1)" readOnly> <a onClick="_yoyNot('1');"><img src="../image/del_btn.png"></a>
						<input name="yoyNm2" type="text" value="<?=$yoyNm2;?>" style="width:70px; background-color:#eeeeee;" onClick="_helpSuYoyP('<?=$_GET["mCode"];?>', '<?=$_GET["mKind"];?>', '<?=$_GET["mKey"];?>', document.f.yoy2, document.f.yoyNm2, document.f.yoyTA2)" readOnly> <a onClick="_yoyNot('2');"><img src="../image/del_btn.png"></a>
						<input name="yoyNm3" type="text" value="<?=$yoyNm3;?>" style="width:70px; background-color:#eeeeee;" onClick="_helpSuYoyP('<?=$_GET["mCode"];?>', '<?=$_GET["mKind"];?>', '<?=$_GET["mKey"];?>', document.f.yoy3, document.f.yoyNm3, document.f.yoyTA3)" readOnly> <a onClick="_yoyNot('3');"><img src="../image/del_btn.png"></a>
						<input name="yoyNm4" type="text" value="<?=$yoyNm4;?>" style="width:70px; background-color:#eeeeee;" onClick="_helpSuYoyP('<?=$_GET["mCode"];?>', '<?=$_GET["mKind"];?>', '<?=$_GET["mKey"];?>', document.f.yoy4, document.f.yoyNm4, document.f.yoyTA4)" readOnly> <a onClick="_yoyNot('4');"><img src="../image/del_btn.png"></a>
						<input name="yoyNm5" type="text" value="<?=$yoyNm5;?>" style="width:70px; background-color:#eeeeee;" onClick="_helpSuYoyP('<?=$_GET["mCode"];?>', '<?=$_GET["mKind"];?>', '<?=$_GET["mKey"];?>', document.f.yoy5, document.f.yoyNm5, document.f.yoyTA5)" readOnly> <a onClick="_yoyNot('5');"><img src="../image/del_btn.png"></a>
						<input name="yoy1" type="hidden" value="<?=$yoy1;?>">
						<input name="yoy2" type="hidden" value="<?=$yoy2;?>">
						<input name="yoy3" type="hidden" value="<?=$yoy3;?>">
						<input name="yoy4" type="hidden" value="<?=$yoy4;?>">
						<input name="yoy5" type="hidden" value="<?=$yoy5;?>">
						<input name="yoyTA1" type="hidden" value="<?=$yoyTA1;?>">
						<input name="yoyTA2" type="hidden" value="<?=$yoyTA2;?>">
						<input name="yoyTA3" type="hidden" value="<?=$yoyTA3;?>">
						<input name="yoyTA4" type="hidden" value="<?=$yoyTA4;?>">
						<input name="yoyTA5" type="hidden" value="<?=$yoyTA5;?>">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
	<td style="" colspan="6" style="text-align:left; padding-left:5px;">
		<input name="visitSudangCheck" type="checkbox" class="checkbox" value="Y" disabled="<?=$visit[0];?>" onClick="checkVisitSugang(this.checked);">방문건별 수당 <input name="visitSudang" type="text" value="<?=number_format($visitSudang);?>" tag="<?=$visitSudang;?>" disabled="<?=$visit[0];?>" class="number" style="background-color:<?=$visit[1];?>;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);">원
		수당비율(<input name="sudangYul1" type="text" value="<?=$sudangYul[1];?>" maxlength="5" tag="<?=$sudangYul[1];?>" disabled="<?=$visit[0];?>" class="number" style="width:40px; background-color:<?=$visit[1];?>;" onKeyDown="__onlyNumber(this, '.');" onFocus="this.select();" onChange="return _setBathRate('1');"> /
		<input name="sudangYul2" type="text" value="<?=$sudangYul[2];?>" maxlength="5" tag="<?=$sudangYul[2];?>" disabled="<?=$visit[0];?>" class="number" style="width:40px; background-color:<?=$visit[1];?>;" onKeyDown="__onlyNumber(this, '.');" onFocus="this.select();" onChange="return _setBathRate('2');">)
		<input name="temp" type="checkbox" class="checkbox" value="Y">시급가산(휴일30%,야간20%)
	</td>
	</tr>
	<tr>
	<td style="font-weight:bold; background-color:#eeeeee;">적용수가</td>
	<td style="" colspan="6">
		<table style="width:100%;">
		<tr>
		<td class="noborder" style="width:80%; text-align:left; padding-left:5px;">
			기준수가 <input name="sPrice" type="text" value="" class="number" style="width:80px; background-color:#eeeeee;" readOnly>원, (
			야간     <input name="ePrice" type="text" value="" class="number" style="width:80px; background-color:#eeeeee;" readOnly>원 +
			심야     <input name="nPrice" type="text" value="" class="number" style="width:80px; background-color:#eeeeee;" readOnly>원) =
			수가계   <input name="tPrice" type="text" value="" class="number" style="width:80px; background-color:#eeeeee;" readOnly>원
		</td>
		<td class="noborder" style="width:20%; text-align:right; padding-right:5px; border-left:1px solid #cccccc;">
			<a href="#" onClick="_setIljungAss();" onFocus="this.blur();"><img src="../image/btn_pattern_list.png"></a>
			<a href="#" onClick="_setAss();" onFocus="this.blur();"><img src="../image/btn_dis.png"></a>
		</td>
		</tr>
		</table>
	</td>
	</tr>
	<input name="sugaCode" type="hidden" value="">
	<input name="sugaName" type="hidden" value="">

	<input name="Egubun" type="hidden" value="">
	<input name="Ngubun" type="hidden" value="">

	<input name="Etime" type="hidden" value="0">
	<input name="Ntime" type="hidden" value="0">

	<input name="mMode" type="hidden" value="<?=$mMode;?>">

	<input name="addDay" type="hidden" value="<?=$_GET['mDay'];?>">
	<input name="addIndex" type="hidden" value="<?=$_GET['mIndex'];?>">
	<input name="addDate" type="hidden" value="<?=$_GET['mDate'];?>">
	<input name="addWeek" type="hidden" value="<?=$_GET['mWeek'];?>">

	<input name="contDate" type="hidden" value="<?=$centerStartDate;?>">
	<input name="oldDate" type="hidden" value="">

	<input name="flagHistory" type="hidden" value="<?=$flagHistory;?>">
</table>
<div id="txtCarNo" style="width:80px; height:65px; left:499px; top:41px; background-color:#ffffff; border:1px solid #cccccc; position:absolute; display:none;">
	<table>
	<tr>
	<td class="noborder" style="height:32px; border-bottom:1px solid #cccccc; font-weight:bold; background-color:#eeeeee;">차량번호</td>
	</tr>
	<tr>
	<td class="noborder" style="height:30px;">
		<select name="carNo" style="width:74px">
		<?
			$sql = "select m00_car_no1"
				 . ",      m00_car_no2"
				 . ",      m00_car_no3"
				 . ",      m00_car_no4"
				 . ",      m00_car_no5"
				 . "  from m00center"
				 . " where m00_mcode = '".$_GET['mCode']
				 . "'  and m00_mkind = '".$_GET['mKind']
				 . "'";
			$conn->query($sql);
			$row = $conn->fetch();

			if ($row['m00_car_no1'] != '') echo '<option value="'.$row['m00_car_no1'].'">'.$row['m00_car_no1'].'</option>';
			if ($row['m00_car_no2'] != '') echo '<option value="'.$row['m00_car_no2'].'">'.$row['m00_car_no2'].'</option>';
			if ($row['m00_car_no3'] != '') echo '<option value="'.$row['m00_car_no3'].'">'.$row['m00_car_no3'].'</option>';
			if ($row['m00_car_no4'] != '') echo '<option value="'.$row['m00_car_no4'].'">'.$row['m00_car_no4'].'</option>';
			if ($row['m00_car_no5'] != '') echo '<option value="'.$row['m00_car_no5'].'">'.$row['m00_car_no5'].'</option>';

			$conn->row_free();
		?>
		</select>
	</td>
	</tr>
	</table>
</div>
<?
	include('../inc/_footer.php');
?>