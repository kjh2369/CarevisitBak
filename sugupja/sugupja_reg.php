<?
	include("../inc/_db_open.php");
	include("../inc/_http_referer.php");
	include("../inc/_function.php");
	include("../inc/_myFun.php");
	include("../inc/_ed.php");

	$mCode  = $_POST["mCode"] != "" ? $_POST["mCode"] : $_SESSION["userCenterCode"];
	$mKind  = $_POST["mKind"] != "" ? $_POST["mKind"] : $_SESSION["userCenterKind"][0];
	$mJumin = Trim($_POST["mJumin"]) != '' ? $ed->de(Trim($_POST["mJumin"])) : '';
	$mKey   = Trim($_POST["mKey"]);
	$mSeq	= $_POST["mWriteSeq"];

	if ($mJumin == '' and $mKey !=''){
		$sql = "select m03_jumin"
		     . "  from m03sugupja"
			 . " where m03_ccode = '".$mCode
			 . "'  and m03_mkind = '".$mKind
			 . "'  and m03_key   = '".$mKey
			 . "'";
		$mJumin = $conn->get_data($sql);
	}

	if ($mJumin != ''){
		$sql = "select m03_ylvl"
			 . "  from m03sugupja"
			 . " where m03_ccode = '".$mCode
			 . "'  and m03_mkind = '".$mKind
			 . "'  and m03_jumin = '".$mJumin
			 . "'";
		$yLvl = $conn->get_data($sql);
	}else{
		$yLvl = '1';
	}

	if ($mJumin != ''){
		$sql = "select m03_skind"
			 . "  from m03sugupja"
			 . " where m03_ccode = '".$mCode
			 . "'  and m03_mkind = '".$mKind
			 . "'  and m03_jumin = '".$mJumin
			 . "'";
		$sKind = $conn->get_data($sql);
	}else{
		$sKind = '1';
	}

	// 급여한도액
	$sql = 'select m91_kupyeo'
		 . '  from m91maxkupyeo'
		 . ' where m91_code = \''.$yLvl
		 . '\' and replace(left(now(), 10), \'-\', \'\') between m91_sdate and m91_edate';
	$conn->query($sql);
	$row = $conn->fetch();
	$kupyeoMax = $row[0];
	$conn->row_free();

	if ($sKind == '2' || $sKind == '4'){
	}else{
		// 본인부담율
		$sql = 'select m92_bonin_yul'
			 . '  from m92boninyul'
			 . ' where m92_code = \''.$sKind
			 . '\' and replace(left(now(), 10), \'-\', \'\') between m92_sdate and m92_edate';
		$conn->query($sql);
		$row = $conn->fetch();
		$bininYul = $row[0];
		$conn->row_free();

		// 본인부담금
		$kupyeo2 = cutOff($kupyeoMax * $bininYul / 100);

		// 정부지원금
		$kupyeo1 = cutOff($kupyeoMax - $kupyeo2);
	}

	$sql = "select *"
		 . "  from m03sugupja"
		 . " where m03_ccode = '".$mCode
		 . "'  and m03_mkind = '".$mKind
		 . "'  and m03_jumin = '".$mJumin
		 . "'";

	$conn->query($sql);
	$row = $conn->fetch();

	if ($conn->row_count() > 0){
		$editMode = false;
		$bininYul = $row['m03_bonin_yul'];
		$kupyeo1  = $row['m03_kupyeo_1'];
		$kupyeo2  = $row['m03_kupyeo_2'];
	}else{
		$editMode = true;
	}

	if ($row["m03_bonin_yul"] != ''){
		$bininYul = $row["m03_bonin_yul"];
		$kupyeoMax = $row["m03_kupyeo_max"];
		$kupyeo2 = $row['m03_kupyeo_2'];
		$sDate = $row["m03_sdate"];
		$eDate = $row["m03_edate"];
	}else{
		$sDate = date('Ymd', mkTime());
		$eDate = '99999999';
	}

	if ($_POST["gubun"] == "suSearch"){
		$buttonGroup = "<span class='btn_pack m icon'><span class='list'></span><button type='button' onFocus='this.blur();' onClick='_sugupjaList(".$_POST["page"].");'>리스트</button></span> ";
	}

	if ($editMode){
		$buttonGroup .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='showSugupjaLayer();'>등록</button></span> ";
	}else{
		$buttonGroup .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='showSugupjaLayer();'>수정</button></span> ";

		if ($_SESSION["userLevel"] == 'A'){
			$buttonGroup .= "<span class='btn_pack m icon'><span class='delete'></span><button type='button' onFocus='this.blur();' onClick='_sugupjaDelete();'>삭제</button></span>";
		}
	}
?>
<table style="width:100%;">
	<form name="center" method="post" action="../sugupja/sugupja_reg_ok.php">
	<tr>
		<td class="title" colspan="2">수급자 등록</td>
	</tr>
	<?
		$target = "suReg";
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
	if($row['m03_name']){
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
				<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">급여<br>계약서</td>
				<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">초기상담<br>기록지<br>(개시전)</td>
				<td style="line-height:1.5em; font-weight:bold; background:#eee;" colspan="3">평가</td>
				<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">익월<br>서비스<br>일정표</td>
				<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">전월<br>본인부담<br>영수증발행</td>
				<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">당월실적<br>확정처리</td>
				<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">미수금<br>현황</td>
				<td style="line-height:1.5em; font-weight:bold; background:#eee;" rowspan="2">만족도<br>조사<br>분기별</td>
			</tr>
			<tr>
				<td style="font-weight:bold; background:#eee;">욕구</td>
				<td style="font-weight:bold; background:#eee;">욕창위험도</td>
				<td style="font-weight:bold; background:#eee;">낙상위험도</td>
			</tr>
			<tr>
			<?
				// 급여계약서
				echo "<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick=\"window.open('../report/report_show_70.php?mCode=$mCode&mKind=$mKind&key=".$row['m03_key']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');\">출력</button></td>";
			?>
				<?
				//초기상담 기록지
				$sql = "select r200_sugup_name"
					 . "  from r200fsttalk"
					 . " where r200_ccode = '".$mCode
					 . "'  and r200_mkind = '".$mKind
					 . "'  and r200_jumin like '".$mJumin
					 . "%'";

				$conn->query($sql);
				$fsttalk = $conn->fetch();

				if($fsttalk['r200_sugup_name'] == ''){
				?>
					<td><span class='btn_pack Y'><button type='button' onFocus='this.blur();' onclick="_showPopup(this,'4','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($mJumin);?>');return false;">미작성</a></td>
				<?
				}else{
					?>
					<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onclick="_showPopup(this,'4','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($mJumin);?>');return false;">작성완료</a></td>
					<?
				}
			?>
			<?
				//욕구평가기록지
				$sql = "select *"
					 . "  from r210nar"
					 . " where r210_ccode = '".$mCode
					 . "'  and r210_mkind = '".$mKind
					 . "'  and r210_sugup_code like '".$mJumin
					 . "%'";

				$conn->query($sql);
				$nar = $conn->fetch();

				if($nar['r210_sugup_name'] == ''){
				?>
					<td><span class='btn_pack Y'><button type='button' onFocus='this.blur();' onclick="_showPopup(this,'1','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($mJumin);?>');return false;">미작성</a></td>
				<?
				}else{
					?>
					<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onclick="_showPopup(this,'1','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($mJumin);?>');return false;">작성완료</a></td>
					<?
				}
			?>
			<?
				//욕창위험도
				$sql = "select *"
					 . "  from r220purat"
					 . " where r220_ccode = '".$mCode
					 . "'  and r220_mkind = '".$mKind
					 . "'  and r220_sugupCode like '".$mJumin
					 . "%'";

				$conn->query($sql);
				$purat = $conn->fetch();

				if($purat['r220_sugupName'] == ''){
				?>
					<td><span class='btn_pack Y'><button type='button' onFocus='this.blur();' onclick="_showPopup(this,'2','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($mJumin);?>');return false;">미작성</a></td>
				<?
				}else{
					?>
					<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onclick="_showPopup(this,'2','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($mJumin);?>');return false;">작성완료</a></td>
					<?
				}
			?>

			<?
				//낙상위험도
				$sql = "select *"
					 . "  from r250risktoll"
					 . " where r250_ccode = '".$mCode
					 . "'  and r250_mkind = '".$mKind
					 . "'  and r250_sugupja_jumin like '".$mJumin
					 . "%'";

				$conn->query($sql);
				$risktoll = $conn->fetch();

				if($risktoll['r250_sugupja_name'] == ''){
				?>
					<td><span class='btn_pack Y'><button type='button' onFocus='this.blur();' onclick="_showPopup(this,'3','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($mJumin);?>');return false;">미작성</a></td>
				<?
				}else{
					?>
					<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onclick="_showPopup(this,'3','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($mJumin);?>');return false;">작성완료</a></td>
					<?
				}
					?>
			<?
			// 익월 서비스 일정표
				$juminNo = $row["m03_jumin"];
				$beforeYM = date("Ym",strtotime("+1 month"));
				$sql = "select count(*)
					  from t01iljung
					 where t01_ccode = '$mCode'
					   and t01_mkind = '$mKind'
					   and t01_sugup_date like '$beforeYM%'
					   and '$juminNo' in (t01_jumin)
					   and t01_del_yn = 'N'";
				$workCount = $conn->get_data($sql);

				if ($workCount > 0){

					?>
					<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick="serviceCalendarShow('<?=$mCode;?>', '<?=$mKind;?>', '<?=subStr($beforeYM, 0, 4);?>', '<?=subStr($beforeYM, 4, 2);?>', '<?=$ed->en($juminNo);?>', 's', 'n', 'pdf');">보기</button></span></td>
					<?
				}else{
					echo "<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=\"window.open('../iljung/iljung.php?menuIndex=work&menuSeq=1&manager=true&gubun=reg', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');return false;\">미작성</td>";
				}
			?>
			<?
			// 전월 본인부담 영수증 발행
			$mYM = date("Ym",strtotime("-1 month"));

			$sql = "select t13_bonin_yul
				      from t13sugupja
				     inner join m03sugupja
					    on m03_ccode = t13_ccode
				       and m03_mkind = t13_mkind
				       and m03_jumin = t13_jumin
				     inner join m81gubun as LVL
					    on m81_gbn  = 'LVL'
				       and m81_code = m03_ylvl
				     where t13_ccode = '$mCode'
				       and t13_mkind = '$mKind'
				       and t13_pay_date = '$mYM'
				       and t13_jumin = '$row[m03_jumin]'
				       and t13_type = '2'";
			$conn->query($sql);
			$bonin = $conn->fetch();
			

			if($bonin['t13_bonin_yul'] > 0){
				echo'<td><span class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="_printPaymentsBill(\''.$mCode.'\', \''.$mKind.'\', \''.$mYM.'\', \''.$bonin['t13_bonin_yul'].'\', \''.$row['m03_key'].'\');" value="" style="width:67px; height:18px; border:0px; no-repeat; cursor:pointer;">발행완료</button></span></td>';
			}else {
				echo"<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''>-</button></span></td>";
			}
		?>
			
			<?
			// 당월실적 확정처리
			$YM = date("Ym",mktime());
		
			$sql = "select count(*)
					  from t13sugupja
					 where t13_ccode = '$mCode'
					   and t13_mkind = '$mKind'
					   and t13_pay_date like '$YM%'
					   and '$juminNo' in (t13_jumin)
					   and t13_type = '2'";
			$confFlag  = $conn->get_data($sql);
			/*
			// 입금여부
			$sql = "select count(*)
					  from t14deposit
					 where t14_ccode = '$mCode'
					   and t14_mkind = '$mKind'
					   and '$juminNo' in (t14_jumin)
					   and t14_pay_date like '$YM%'
					   ''";
			$depositFlag = $conn->get_data($sql);
			*/

			if ($confFlag == 0){
				echo "<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=\"window.open('../work/month_conf_sugupja.php?curYear=".subStr($YM, 0, 4)."&curMonth=".subStr($YM, 4, 2)."&curMcode=$mCode&curMkind=$mKind&curSugupja=".$ed->en($row["m03_jumin"])."&manager!=true', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');return false;\">미확정</button></span></td>";
				
			}else{
				echo "<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick=\"window.open('../work/month_conf_sugupja.php?curYear=".subStr($YM, 0, 4)."&curMonth=".subStr($YM, 4, 2)."&curMcode=$mCode&curMkind=$mKind&curSugupja=".$ed->en($row["m03_jumin"])."&manager!=true', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');return false;\">확정</button></span></td>";
			}
			?>
			
			<?
			// 미수금 현황
			$sql = "select sum(t13_misu_amt - t13_misu_inamt) as misuAmt
			  from t13sugupja
			 inner join m03sugupja
			    on m03_ccode = t13_ccode
			   and m03_mkind = t13_mkind
			   and m03_jumin = t13_jumin
			 inner join m81gubun as LVL
			    on m81_gbn  = 'LVL'
			   and m81_code = m03_ylvl
			 where t13_ccode = '$mCode'
			   and t13_mkind = '$mKind'
			   and t13_jumin = '$row[m03_jumin]'
			   and t13_type = '2'";
			$conn->query($sql);
			$amt = $conn->fetch();

			if($amt['misuAmt'] > 0){
				echo'<td><span class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="popupDeposit(\''.$mCode.'\', \''.$mKind.'\', \''.$row['m03_key'].'\');" value="" style="width:67px; height:18px; border:0px; no-repeat; cursor:pointer;">미수</button></span></td>';
			}else {
				echo"<td><span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick=''>-</button></span></td>";
			}
			?>
	
			<?
			// 만족도 조사(분기별)
			?>
			<td><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''>미작성</button></span></td>
		</tr>
	</table>
	<?
	}
?>
<table style="width:100%">
	<tbody id="suBody" style="display:<?=$suBody;?>;">
		<tr>
			<td class="subject">수급자</td>
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
						<th style="padding:0; text-align:center; background-color:#eee;" colspan="4">기본정보</th>
					</tr>
					<tr>
						<th style="width:180px;" scope="row"><span style="color:ff0000; padding-right:3px;">★</span>수급자주민번호</th>
						<td style="width:225px;">
							<?
								if (!$editMode){
								?>
									<div><?=substr($row["m03_jumin"],0,6)."-".substr($row["m03_jumin"],6,1);?>******</div>
									<input name="jumin1" type="hidden" value="<?=substr($row["m03_jumin"],0,6);?>">
									<input name="jumin2" type="hidden" value="<?=substr($row["m03_jumin"],6,13);?>">
									
								<?
								}else{
								?>
									<input name="jumin1" type="text" value="" maxlength="6" class="phone" style="width:50px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 6){document.center.jumin2.focus();}" onChange="_checkSSN('su', document.center.jumin1, document.center.jumin2, document.center.curMcode, document.center.curMkind);" onFocus="this.select();"> -
									<input name="jumin2" type="text" value="" maxlength="7" class="phone" style="width:55px;" onKeyDown="__onlyNumber(this);" onkeyUp="if(this.value.length == 7){document.center.name.focus();}" onChange="_checkSSN('su', document.center.jumin1, document.center.jumin2, document.center.curMcode, document.center.curMkind);" onFocus="this.select();">
								<?
								}
							?>
						</td>
						<th style="width:180px;" scope="row"><span style="color:ff0000; padding-right:3px;">★</span>수급자성명</th>
						<td style="width:225px;">
							<input name="name" type="text" value="<?=$row["m03_name"];?>" maxlength="10" onKeyDown="__enterFocus();" onFocus="this.select();">
						</td>
					</tr>
					<!--
					<tr>
						<th scope="row">서비스항목 구분자</th>
						<td colspan="3">
						<?
							if ($mKind == "0"){
							?>
								<select name="subCD" onKeyDown="__enterFocus();">
								<?
									$sql = $conn->get_gubun("SUB");
									$conn->query($sql);
									$row2 = $conn->fetch();
									$row_count = $conn->row_count();

									for($i=0; $i<$row_count; $i++){
										$row2 = $conn->select_row($i);
									?>
										<option value="<?=$row2[0];?>"<? if($row["m03_subcd"] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
									<?
									}

									$conn->row_free();
								?>
								</select>
							<?
							}else{
							?>
								<select name="subCD" style="width:150px;" onKeyDown="__enterFocus();">
									<option value="<?=$mKind;?>"><?=getKindName($mKind);?></option>
								</select>
							<?
							}
							?>
						</td>
					</tr>
					-->
					<input name="subCD" type="hidden" value="<?=$row["m03_subcd"];?>">
					<tr>
						<th scope="row">자택전화번호</th>
						<td>
							<input name="tel" type="text" value="<?=getPhoneStyle($row["m03_tel"]);?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);">
						</td>
						<th scope="row">휴대폰전화번호</th>
						<td>
							<input name="hp" type="text" value="<?=getPhoneStyle($row["m03_hp"]);?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);">
						</td>
					</tr>
					<tr>
						<th scope="row">우편번호</th>
						<td>
							<input name="postNo1" type="text" value="<?=substr($row["m03_post_no"],0,3);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onKeyUp="if(this.value.length == 3){document.center.postNo2.focus();}" onFocus="this.select();"> -
							<input name="postNo2" type="text" value="<?=substr($row["m03_post_no"],3,6);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onKeyUp="if(this.value.length == 3){document.center.juso1.focus();}" onFocus="this.select();">
							<a href="#" onClick="__helpAddress(document.center.postNo1, document.center.postNo2, document.center.juso1, document.center.juso2);">찾기</a>
						</td>
						<th scope="row">주소</th>
						<td>
							<input name="juso1" type="text" value="<?=$row["m03_juso1"];?>" maxlength="20" style="width:100%;" ime-mode:active; onKeyDown="__enterFocus();" onFocus="this.select();"><br>
							<input name="juso2" type="text" value="<?=$row["m03_juso2"];?>" maxlength="30" style="width:100%;" ime-mode:active; onKeyDown="__enterFocus();" onFocus="this.select();">
						</td>
					</tr>

					<tr>
						<th style="padding:0; text-align:center; background-color:#eee;" colspan="4">기관계약정보</th>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>계약시작일자</th>
						<td>
							<input name="gaeYakFm" type="text" value="<?=getDateStyle($row["m03_gaeyak_fm"]);?>" tag="<?=$row["m03_gaeyak_fm"];?>" maxlength="8" class="phone" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);">
						</td>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>계약종료일자</th>
						<td>
							<input name="gaeYakTo" type="text" value="<?=getDateStyle($row["m03_gaeyak_to"]);?>" tag="<?=$row["m03_gaeyak_to"];?>" maxlength="8" class="phone" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);">
						</td>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>수급현황</th>
						<td>
							<select name="sugupStatus" tag="<?=$row['m03_sugup_status'];?>">
							<?
								$statusList = $definition->SugupjaStatusList();

								for($i=0; $i<sizeOf($statusList); $i++){
									if ($row['m03_sugup_status'] == $statusList[$i]['code']){
										$selected = 'selected';
									}else{
										$selected = '';
									}
									echo '<option value="'.$statusList[$i]['code'].'" '.$selected.'>'.$statusList[$i]['name'].'</option>';
								}
							?>
							</select>
						</td>
						<th scope="row"></th>
						<td></td>
					</tr>

					<tr>
						<th style="padding:0; text-align:center; background-color:#eee;" colspan="4">장애등급정보</th>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>장기요양등급</th>
						<td>
							<select name="yLvl" tag="<?=$row["m03_ylvl"];?>" style="width:100%;" onChange="_setKupyeoMax(document.center.kupyeoMax, this.options[this.selectedIndex].value);" onKeyDown="__enterFocus();">
							<?
								$sql = $conn->get_gubun("LVL");
								$conn->query($sql);
								$row2 = $conn->fetch();
								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row2 = $conn->select_row($i);
								?>
									<option value="<?=$row2[0];?>"<? if(($row["m03_ylvl"]!=null?$row["m03_ylvl"]:'1') == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
								<?
								}

								$conn->row_free();
							?>
							</select>
						</td>
						<th scope="row">급여한도액</th>
						<td>
							<input name="kupyeoMax" type="text" value="<?=number_format($kupyeoMax);?>" maxlength="10" class="number" style="background-color:#eeeeee;" onFocus="document.center.injungNo.focus();" readOnly>
						</td>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>수급자구분</th>
						<td>
							<select name="sKind" tag="<?=$row["m03_skind"];?>" style="width:100%;" onChange="_setBoninYul(document.center.boninYul, this.options[this.selectedIndex].value);" onKeyDown="__enterFocus();">
							<?
								$sql = $conn->get_gubun("STP");
								$conn->query($sql);
								$row2 = $conn->fetch();
								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row2 = $conn->select_row($i);
								?>
									<option value="<?=$row2[0];?>"<? if($row["m03_skind"] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
								<?
								}

								$conn->row_free();
							?>
							</select>
						</td>
						<th scope="row">수급자본인부담율</th>
						<td>
							<input name="boninYul" type="text" value="<?=$bininYul;?>" tag="<?=$bininYul;?>" maxlength="3" style="background-color:<?=$row["m03_skind"] == '2' || $row["m03_skind"] == '4' ? '#ffffff' : '#eeeeee';?>;" class="number" onKeyDown="__onlyNumber(this,'.');" onChange="_setPay();" onFocus="<?=$row["m03_skind"] == '2' || $row["m03_skind"] == '4' ? 'this.select();' : 'document.center.injungNo.focus();';?>" <?=$row["m03_skind"] == '2' || $row["m03_skind"] == '4' ? '' : 'readOnly';?>>
							<span id="idHelpBoninYul" style="font-weight:bold; padding-left:3px;"><a onClick="_showBoninYulHelp();">?</a></span>
						</td>
					</tr>
					<tr>
						<th scope="row">정부지원금</th>
						<td>
							<input name="kupyeo1" type="text" value="<?=number_format($kupyeo1);?>" maxlength="10" class="number" style="background-color:#eeeeee;" onFocus="document.center.injungNo.focus();" readOnly>
						</td>
						<th scope="row">본인부담금</th>
						<td>
							<input name="kupyeo2" type="text" value="<?=number_format($kupyeo2);?>" maxlength="10" class="number" style="background-color:#eeeeee;" onFocus="document.center.injungNo.focus();" readOnly>
						</td>
					</tr>

					<tr>
						<th style="padding:0; text-align:center; background-color:#eee;" colspan="4">장기요양인증정보</th>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>장기요양인증번호</th>
						<td>
							<input name="injungNo" type="text" value="<?=$row["m03_injung_no"];?>" maxlength="14" style="width:150px;" onKeyDown="__enterFocus();" onFocus="__replace(this, '-', '');" onBlur="this.value=__formatString(this.value, '#####-######-###');">
						</td>
						<th scope="row">병명</th>
						<td>
							<select name="byungMung" style="width:100%;" onKeyDown="__enterFocus();">
							<?
								$sql = $conn->get_gubun("DAS");
								$conn->query($sql);
								$row2 = $conn->fetch();
								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row2 = $conn->select_row($i);
								?>
									<option value="<?=$row2[0];?>"<? if($row["m03_byungmung"] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
								<?
								}

								$conn->row_free();
							?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">장기요양인증서<br>유효기간(FROM)</th>
						<td>
							<input name="injungFrom" type="text" value="<?=getDateStyle($row["m03_injung_from"]);?>" maxlength="8" class="phone" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);">
						</td>
						<th scope="row">장기요양인증서<br>유효기간(TO)</th>
						<td>
							<input name="injungTo" type="text" value="<?=getDateStyle($row["m03_injung_to"]);?>" maxlength="8" class="phone" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);">
						</td>
					</tr>
					<tr>
						<th scope="row">보장기관명</th>
						<td>
							<input name="skigwanName" type="text" value="<?=$row["m03_skigwan_name"];?>" maxlength="10" onKeyDown="__enterFocus();" onFocus="this.select();">
						</td>
						<th scope="row">보장기기관기호</th>
						<td>
							<input name="skigwanCode" type="text" value="<?=$row["m03_skigwan_code"];?>" maxlength="7" onKeyDown="__enterFocus();" onFocus="this.select();">
						</td>
					</tr>

					<tr>
						<th style="padding:0; text-align:center; background-color:#eee;" colspan="4">담당요양보호사정보</th>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>담당요양보호사1</th>
						<td>
							<table>
								<tr>
									<td style="border:0px; padding:0px;">
										<input name="yoyangsa1" type="hidden" value="<?=$row["m03_yoyangsa1"];?>" tag="<?=$row["m03_yoyangsa1"];?>">
										<input name="yoyangsa1Nm" type="text" value="<?=$row["m03_yoyangsa1_nm"];?>" style="background-color:#eeeeee;" readOnly>&nbsp;
									</td>
									<td style="border:0px; padding:0px;">
										<input type="button" onClick="__helpYoy('<?=$mCode;?>','<?=$mKind;?>',document.center.yoyangsa1,document.center.yoyangsa1Nm);" value="" style="width:22px; height:21px; border:0px; background:url('../image/btn_find.png') no-repeat; cursor:pointer;">
										<input type="button" onClick="__notYoy(1);" value="" style="width:59px; height:21px; border:0px; background:url('../image/btn11.gif') no-repeat; cursor:pointer;">
									</td>
								</tr>
							</table>
						</td>
						<th scope="row">담당요양보호사2</th>
						<td>
							<table>
								<tr>
									<td style="border:0px; padding:0px;">
										<input name="yoyangsa2" type="hidden" value="<?=$row["m03_yoyangsa2"];?>">
										<input name="yoyangsa2Nm" type="text" value="<?=$row["m03_yoyangsa2_nm"];?>" style="background-color:#eeeeee;" readOnly>&nbsp;
									</td>
									<td style="border:0px; padding:0px;">
										<input type="button" onClick="__helpYoy('<?=$mCode;?>','<?=$mKind;?>',document.center.yoyangsa2,document.center.yoyangsa2Nm);" value="" style="width:22px; height:21px; border:0px; background:url('../image/btn_find.png') no-repeat; cursor:pointer;">
										<input type="button" onClick="__notYoy(2);" value="" style="width:59px; height:21px; border:0px; background:url('../image/btn11.gif') no-repeat; cursor:pointer;">
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<th scope="row">담당요양보호사3</th>
						<td>
							<table>
								<tr>
									<td style="border:0px; padding:0px;">
										<input name="yoyangsa3" type="hidden" value="<?=$row["m03_yoyangsa3"];?>">
										<input name="yoyangsa3Nm" type="text" value="<?=$row["m03_yoyangsa3_nm"];?>" style="background-color:#eeeeee;" readOnly>&nbsp;
									</td>
									<td style="border:0px; padding:0px;">
										<input type="button" onClick="__helpYoy('<?=$mCode;?>','<?=$mKind;?>',document.center.yoyangsa3,document.center.yoyangsa3Nm);" value="" style="width:22px; height:21px; border:0px; background:url('../image/btn_find.png') no-repeat; cursor:pointer;">
										<input type="button" onClick="__notYoy(3);" value="" style="width:59px; height:21px; border:0px; background:url('../image/btn11.gif') no-repeat; cursor:pointer;">
									</td>
								</tr>
							</table>
						</td>
						<th scope="row">담당요양보호사4</th>
						<td>
							<table>
								<tr>
									<td style="border:0px; padding:0px;">
										<input name="yoyangsa4" type="hidden" value="<?=$row["m03_yoyangsa4"];?>">
										<input name="yoyangsa4Nm" type="text" value="<?=$row["m03_yoyangsa4_nm"];?>" style="background-color:#eeeeee;" readOnly>&nbsp;
									</td>
									<td style="border:0px; padding:0px;">
										<input type="button" onClick="__helpYoy('<?=$mCode;?>','<?=$mKind;?>',document.center.yoyangsa4,document.center.yoyangsa4Nm);" value="" style="width:22px; height:21px; border:0px; background:url('../image/btn_find.png') no-repeat; cursor:pointer;">
										<input type="button" onClick="__notYoy(4);" value="" style="width:59px; height:21px; border:0px; background:url('../image/btn11.gif') no-repeat; cursor:pointer;">
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<th scope="row">담당요양보호사5</th>
						<td colspan="3">
							<table>
								<tr>
									<td style="border:0px; padding:0px;">
										<input name="yoyangsa5" type="hidden" value="<?=$row["m03_yoyangsa5"];?>">
										<input name="yoyangsa5Nm" type="text" value="<?=$row["m03_yoyangsa5_nm"];?>" style="background-color:#eeeeee;" readOnly>&nbsp;
									</td>
									<td style="border:0px; padding:0px;">
										<input type="button" onClick="__helpYoy('<?=$mCode;?>','<?=$mKind;?>',document.center.yoyangsa5,document.center.yoyangsa5Nm);" value="" style="width:22px; height:21px; border:0px; background:url('../image/btn_find.png') no-repeat; cursor:pointer;">
										<input type="button" onClick="__notYoy(5);" value="" style="width:59px; height:21px; border:0px; background:url('../image/btn11.gif') no-repeat; cursor:pointer;">
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<tr>
						<th style="padding:0; text-align:center; background-color:#eee;" colspan="4">보호자정보</th>
					</tr>
					<tr>
						<th scope="row">보호자성명</th>
						<td>
							<input name="yBohoName" type="text" value="<?=$row["m03_yboho_name"];?>" maxlength="10" onKeyDown="__enterFocus();" onFocus="this.select();">
						</td>
						<th scope="row">보호자주민번호</th>
						<td>
							<input name="yBohoJuminNo1" type="text" value="<?=substr($row["m03_yboho_juminno"], 0, 6);?>" maxlength="6" class="phone" style="width:50px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 6){document.center.yBohoJuminNo2.focus();}" onFocus="this.select();"> -
							<input name="yBohoJuminNo2" type="password" value="<?=substr($row["m03_yboho_juminno"], 6, 7);?>" maxlength="7" class="phone" style="width:55px;" onKeyDown="__onlyNumber(this);" onkeyUp="if(this.value.length == 7){document.center.yBohoGwange.focus();}" onFocus="this.select();">
						</td>
					</tr>
					<tr>
						<th scope="row">수급자와의 관계</th>
						<td>
							<input name="yBohoGwange" type="text" value="<?=$row["m03_yboho_gwange"];?>" maxlength="10" onKeyDown="__enterFocus();" onFocus="this.select();">
						</td>
						<th scope="row">보호자연락처</th>
						<td>
							<input name="yBohoPhone" type="text" value="<?=getPhoneStyle($row["m03_yboho_phone"]);?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);">
						</td>
					</tr>
					<tr>
						<th scope="row"><span style="color:ff0000; padding-right:3px;">★</span>동거가족케어유무</th>
						<td>
							<select name="familyCare" onKeyDown="__enterFocus();">
								<option value="Y"<? if($row["m03_familycare"] == "Y"){echo "selected";}?>>유</option>
								<option value="N"<? if($row["m03_familycare"] != "Y"){echo "selected";}?>>무</option>
							</select>
						</td>
						<th scope="row"></th>
						<td></td>
					</tr>

					<tr>
						<th style="padding:0; text-align:center; background-color:#eee;" colspan="4"></th>
					</tr>
					<tr>
						<th scope="row">적용 시작일</th>
						<td>
							<!--<input name="sDate" type="text" value="<?=subStr($sDate, 0, 4).'.'.subStr($sDate, 4, 2).'.'.subStr($sDate, 6, 2);?>" tag="<?=$sDate;?>" maxlength="8" class="phone" onFocus="__toNumber(this); this.select();" onBlur="__setDate(this);" onKeyDown="__onlyNumber(this);">-->
							<?=subStr($sDate, 0, 4).'.'.subStr($sDate, 4, 2).'.'.subStr($sDate, 6, 2);?>
							<input name="sDate" type="hidden" value="<?=$sDate;?>" tag="<?=$sDate;?>">
						</td>
						<th scope="row">적용 종료일</th>
						<td>
							<?=subStr($eDate, 0, 4).'.'.subStr($eDate, 4, 2).'.'.subStr($eDate, 6, 2);?>
						</td>
					</tr>
				</table>
				<input name="editMode" type="hidden" value="<?=$editMode;?>">
				<input name="historys" type="hidden" value="Y">
				<input name="mKey"     type="hidden" value="<?=$row['m03_key'];?>">
			</td>
		</tr>
	</tbody>
	</form>
</table>
<!--
<table>
	<tr>
		<td class="button" style="height:40px;">
		<?
			if ($_POST["gubun"] == "suSearch"){
			?>
				<a href="javascript:_sugupjaList(<?=$_POST["page"];?>);"><img src="../image/btn_list.png"></a>
			<?
			}
		?>
		</td>
	</tr>
</table>
-->
<?
	$conn->row_free();
?>
<table style="width:100%;">
<tr>
	<td class="subject">수급자 히스토리</td>
</tr>
<tr>
	<td class="noborder">
		<table class="view_type1">
		<tr>
			<th style="width:10%; padding:0; text-align:center;">적용시작일</th>
			<th style="width:10%; padding:0; text-align:center;">적용종료일</th>
			<th style="width:5%; padding:0; text-align:center;">등급</th>
			<th style="width:18%; padding:0; text-align:center;">수급자구분</th>
			<th style="width:12%; padding:0; text-align:center;">수급현황</th>
			<th style="width:10%; padding:0; text-align:center;">계약시작일</th>
			<th style="width:10%; padding:0; text-align:center;">계약종료일</th>
			<th style="width:25%; padding:0; text-align:center;">담당요양사변경사항</th>
		</tr>
		<?
			$sql = "select m31_sdate
					,      m31_edate
					,      LVL.m81_name as levelName
					,      STP.m81_name as kindName
					,      m31_bonin_yul
					,      m31_status
					,      m31_gaeyak_fm
					,      m31_gaeyak_to
					,      m32_b_date
					,      m32_a_date
					,      m32_b_name
					,      m32_a_name
					  from m31sugupja
					  left join m32jikwon
					    on m32_ccode = m31_ccode
					   and m32_mkind = m31_mkind
					   and m32_jumin = m31_jumin
					   and m32_b_date = m31_sdate
					 inner join m81gubun as LVL
					    on LVL.m81_gbn = 'LVL'
					   and LVL.m81_code = m31_level
					 inner join m81gubun as STP
					    on STP.m81_gbn = 'STP'
					   and STP.m81_code = m31_kind
					 where m31_ccode = '$mCode'
					   and m31_mkind = '$mKind'
					   and m31_jumin = '$mJumin'
					 order by m31_sdate, m31_edate";
			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();

			if ($rowCount > 0){
				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					echo '<tr>';
					echo '<td style="text-align:center;">'.$myF->dateStyle($row['m31_sdate']).'</td>';
					echo '<td style="text-align:center;">'.$myF->dateStyle($row['m31_edate']).'</td>';
					echo '<td style="text-align:center;">'.$row['levelName'].'</td>';
					echo '<td style="text-align:left;">'.$row['kindName'].'('.$row['m31_bonin_yul'].')'.'</td>';
					echo '<td style="text-align:left;">'.$definition->SugupjaStatusGbn($row['m31_status']).'</td>';
					echo '<td style="text-align:center;">'.$myF->dateStyle($row['m31_gaeyak_fm']).'</td>';
					echo '<td style="text-align:center;">'.$myF->dateStyle($row['m31_gaeyak_to']).'</td>';
					echo '<td style="text-align:left;">'.($row['m32_b_name'].$row['m32_a_name'] != '' ? $row['m32_b_name'].'→'.$row['m32_a_name'] : '').'</td>';
					echo '</tr>';
				}
			}else{
				echo '<tr><td style="text-align:center;" colspan="8">::검색된 데이타가 없습니다.::</td></tr>';
			}
			$conn->row_free();
		?>
		</table>
	</td>
</tr>
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
<div id="helpBoninYul" style="z-index:11; left:0; top:0; width:300px; position:absolute; color:#000000; display:none;">
	<table style="width:300px; height:60px; background-color:#eeeeee; border:2px solid #ccc;">
	<tr>
		<td>수급자구분이 "의료급여수급권자"와 "본인부담겸강대상"인 경우 "수급자본인부담율"을 변경할 수 있습니다.</td>
	</tr>
	</table>
	<div style="margin-top:3px; text-align:right;">
		<span class="btn_pack m icon"><span class="check"></span><button type="button" onFocus="this.blur();" onClick="helpBoninYul.style.display='none';">확인</button></span>
	</div>
</div>
<div id="idPopup" style="z-index:11; left:0; top:0; width:220px; position:absolute; color:#000000; display:none;">
</div>
