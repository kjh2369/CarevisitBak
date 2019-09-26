<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');

	switch($__SVC_ID__){
		case '0':
			$showDivTag = $gHostSvc['homecare'];
			$svc_title  = '재가요양';
			$depo_title  = '요양';
			break;
		case '1':
			#$showDivTag = $gHostSvc['voucher'];
			$showDivTag = $gHostSvc['nurse'];
			$svc_title  = '바우처[가사간병]';
			$depo_title  = '가사간병';
			break;
		case '2':
			#$showDivTag = $gHostSvc['voucher'];
			$showDivTag = $gHostSvc['old'];
			$svc_title  = '바우처[노인돌봄]';
			$depo_title  = '노인돌봄';
			break;
		case '3':
			#$showDivTag = $gHostSvc['voucher'];
			$showDivTag = $gHostSvc['baby'];
			$svc_title  = '바우처[산모신생아]';
			$depo_title  = '산모신생아';
			break;
		case '4':
			#$showDivTag = $gHostSvc['voucher'];
			$showDivTag = $gHostSvc['dis'];
			$svc_title  = '바우처[장애인활동지원]';
			$depo_title  = '장애인';
			break;
		default:
			return;
	}

	if ($gDomain == 'kacold.net'){
		if ($gHostNm == '' || $gHostNm == 'www'){
			$IsCare = true;
		}else{
			$IsCare = false;
		}
	}else{
		$IsCare = true;
	}

	$IsCare = false;

	if (!$IsCare){
		$showDivTag = '';
	}
?>
<div id="svc_div_<?=$__SVC_ID__;?>" style="width:0; float:left; padding:10px; position:absolute; top:0; left:-10000; display:<?=!$showDivTag ? 'none' : '';?>;">
	<table id="svc_tbl_<?=$__SVC_ID__;?>" class="my_table my_border_blue" style="width:0; position:absolute; top:0; display:; left:-10000;">
		<colgroup>
		<?
			if ($_SESSION['userLevel'] == 'A'){
				$cols = 1;
				echo '<col width="100px">
					  <col>';
			}else{
				$cols = 2;
				echo '<col width="50px">
					  <col width="50px">
					  <col width="230px">
					  <col width="40px">';
			}
		?>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="<?=$cols+2;?>"><?=$svc_title;?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th colspan="<?=$cols;?>">승인번호</th>
				<td colspan="2">
					<input name="code<?=$__SVC_ID__;?>" type="text" value="<?=$code1[$__SVC_ID__];?>" maxlength="15" class="no_string" style="width:80px; margin-right:0;" tag="승인번호를 입력하여 주십시오."><?
					if ($debug){
						if ($__SVC_ID__ == '0'){?>
							/목욕,간호<input name="subCode<?=$__SVC_ID__;?>" type="text" value="<?=$lsSubCD;?>" maxlength="15" class="no_string" style="width:80px; margin-right:0;"><?
						}
					}?>
				</td>
			</tr>
			<tr>
				<th colspan="<?=$cols;?>">기관명칭</th>
				<td colspan="2"><input name="cName<?=$__SVC_ID__;?>" type="text" value='<?=$cName[$__SVC_ID__];?>' maxlength="30" style="width:100%;" onFocus="this.select();" tag="기관명을 입력하여 주십시오."></td>
			</tr>
			<tr>
				<th colspan="<?=$cols;?>">사업개시일자</th>
				<td colspan="2"><input name="jDate<?=$__SVC_ID__;?>" type="text" value="<?=$jDate[$__SVC_ID__];?>" maxlength="8" class="date"></td>
			</tr>
			<?
				// 재가요양인 경우
				if ($__SVC_ID__ == '0'){?>
					<tr>
						<th colspan="<?=$cols;?>">제공급여종류</th>
						<td colspan="2">
							<input id="kupyeo1" name="kupyeo1" type="checkbox" value="Y" class="checkbox" style="margin-right:0;" <? if($kupyeo1 == 'Y'){echo 'checked';} ?>><label for="kupyeo1">방문요양</label>
							<input id="kupyeo2" name="kupyeo2" type="checkbox" value="Y" class="checkbox" style="margin-right:0;" <? if($kupyeo2 == 'Y'){echo 'checked';} ?> onClick="set_care_service(2);"><label for="kupyeo2">방문목욕</label>
							<input id="kupyeo3" name="kupyeo3" type="checkbox" value="Y" class="checkbox" style="margin-right:0;" <? if($kupyeo3 == 'Y'){echo 'checked';} ?> onClick="set_care_service(3);"><label for="kupyeo3">방문간호</label>
						</td>
					</tr>
					<tr>
						<th colspan="<?=$cols;?>">목욕수당 배분율</th>
						<td colspan="2">
							<input name="sudangYul1" type="text" value="<?=number_format($muksuYul1[$__SVC_ID__]);?>" class="number" style="width:40px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);" onChange="_checkMuksuRate(this, document.f.sudangYul2);">% /
							<input name="sudangYul2" type="text" value="<?=number_format($muksuYul2[$__SVC_ID__]);?>" class="number" style="width:40px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);" onChange="_checkMuksuRate(this, document.f.sudangYul1);">%
						</td>
					</tr>
					<tr>
						<th colspan="<?=$cols;?>">목욕차량번호</th>
						<td colspan="2">
							<input name="carNo1" type="text" value="<?=$carNo1[$__SVC_ID__];?>" style="width:70px; margin-right:0;" maxlength="10">
							<input name="carNo2" type="text" value="<?=$carNo2[$__SVC_ID__];?>" style="width:70px; margin-right:0; margin-left:0;" maxlength="10">
							<input name="carNo3" type="text" value="<?=$carNo3[$__SVC_ID__];?>" style="width:70px; margin-right:0; margin-left:0;" maxlength="10">
						</td>
					</tr><?
				}

				if ($_SESSION['userLevel'] != 'A'){?>
					<tr>
						<th rowspan="4" class="center"><?=$depo_title;?></th>
						<th >입금은행</th>
						<td colspan="2"><?
							if ($isBankTrans){?>
								<div class="left bold" style="color:black;">기업은행</div>
								<input name="bankName<?=$__SVC_ID__;?>" type="hidden" value="<?=$gCenterBankCD;?>"><?
							}else{?>
								<select name="bankName<?=$__SVC_ID__;?>" style="width:auto;">
									<option value="">-선택-</option>
								<?
									for($i=0; $i<$bankListCount; $i++){
									?>
										<option value="<?=$bankList[$i]['code'];?>" title="<?=$bankList[$i]['name'];?>" <? if($bankList[$i]['code'] == $bankName[$__SVC_ID__]){?>selected<?} ?>><?=$bankList[$i]['name'];?></option>
									<?
									}
								?>
								</select><?
							}?>
						</td>
					</tr>
					<tr>
						<th >계좌번호</th>
						<td colspan="2"><input name="bankNo<?=$__SVC_ID__;?>" type="text" value="<?=$bankNo[$__SVC_ID__];?>" maxlength="30" style="width:100%;"></td>
					</tr>
					<tr>
						<th >예금주명</th>
						<td colspan="2"><input name="bankDepos<?=$__SVC_ID__;?>" type="text" value='<?=$bankDepos[$__SVC_ID__];?>' maxlength="30" style="width:100%;"></td>
					</tr>
					<tr>
						<th >급여유형</th>
						<td colspan="2"><input name="salaryType<?=$__SVC_ID__;?>" type="text" value='<?=$salaryType[$__SVC_ID__][0];?>' maxlength="30" style="width:100%;"></td>
					</tr>
					<tr id="bathBankName" style="display:none;">
						<th rowspan="4" class="center">목욕</th>
						<th >입금은행</th>
						<td colspan="2">
							<select name="bankName<?=$__SVC_ID__;?>_500" style="width:auto;">
								<option value="">-선택-</option>
							<?
								for($i=0; $i<$bankListCount; $i++){
								?>
									<option value="<?=$bankList[$i]['code'];?>" title="<?=$bankList[$i]['name'];?>" <? if($bankList[$i]['code'] == $bathBankName[$__SVC_ID__]){?>selected<?} ?>><?=$bankList[$i]['name'];?></option>
								<?
								}
							?>
							</select>
						</td>
					</tr>
					<tr id="bathBankNo" style="display:none;">
						<th >계좌번호</th>
						<td colspan="2"><input name="bankNo<?=$__SVC_ID__;?>_500" type="text" value="<?=$bathBankNo[$__SVC_ID__];?>" maxlength="30" style="width:100%;"></td>
					</tr>
					<tr id="bathBankDep" style="display:none;">
						<th >예금주명</th>
						<td colspan="2"><input name="bankDepos<?=$__SVC_ID__;?>_500" type="text" value='<?=$bathBankDepos[$__SVC_ID__];?>' maxlength="30" style="width:100%;"></td>
					</tr>
					<tr id="bathSalaryType" style="display:none;">
						<th >급여유형</th>
						<td colspan="2"><input name="salaryType<?=$__SVC_ID__;?>_500" type="text" value='<?=$salaryType[$__SVC_ID__][1];?>' maxlength="30" style="width:100%;"></td>
					</tr>
					<tr id="nursBankName" style="display:none;">
						<th rowspan="4" class="center">간호</th>
						<th >입금은행</th>
						<td colspan="2">
							<select name="bankName<?=$__SVC_ID__;?>_800" style="width:auto;">
								<option value="">-선택-</option>
							<?
								for($i=0; $i<$bankListCount; $i++){
								?>
									<option value="<?=$bankList[$i]['code'];?>" title="<?=$bankList[$i]['name'];?>" <? if($bankList[$i]['code'] == $nursBankName[$__SVC_ID__]){?>selected<?} ?>><?=$bankList[$i]['name'];?></option>
								<?
								}
							?>
							</select>
						</td>
					</tr>
					<tr id="nursBankNo" style="display:none;">
						<th >계좌번호</th>
						<td colspan="2"><input name="bankNo<?=$__SVC_ID__;?>_800" type="text" value="<?=$nursBankNo[$__SVC_ID__];?>" maxlength="30" style="width:100%;"></td>
					</tr>
					<tr id="nursBankDep" style="display:none;">
						<th >예금주명</th>
						<td colspan="2"><input name="bankDepos<?=$__SVC_ID__;?>_800" type="text" value='<?=$nursBankDepos[$__SVC_ID__];?>' maxlength="30" style="width:100%;"></td>
					</tr>
					<tr id="nursSalaryType" style="display:none;">
						<th >급여유형</th>
						<td colspan="2"><input name="salaryType<?=$__SVC_ID__;?>_800" type="text" value='<?=$salaryType[$__SVC_ID__][2];?>' maxlength="30" style="width:100%;"></td>
					</tr><?

					if ($debug){
						if ($__SVC_ID__ == '3'){
							//산모신생아?>
							<tr>
								<th colspan="<?=$cols;?>">서비스 가격관리<br>(2013년 2월부터)</th>
								<td colspan="2">
									<script type="text/javascript">
										function lfVouBabySugaSet(type){
											var objModal = new Object();
											var url = './vou_baby_suga.php';
											var style = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

											objModal.win = window;
											objModal.type = type;

											window.showModalDialog(url, objModal, style);
										}
									</script>
									<table class="my_table" style="width:100%">
										<colgroup>
											<col width="70px" span="3">
											<col>
										</colgroup>
										<thead>
											<tr>
												<th class="head">단태아</th>
												<th class="head">쌍태아</th>
												<th class="head">삼태아</th>
												<th class="head last">비고</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td class="right bottom" onclick="lfVouBabySugaSet(1);"><a href="#" onclick="return false;">0</a></td>
												<td class="right bottom" onclick="lfVouBabySugaSet(2);"><a href="#" onclick="return false;">0</a></td>
												<td class="right bottom" onclick="lfVouBabySugaSet(3);"><a href="#" onclick="return false;">0</a></td>
												<td class="left bottom last"></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr><?
						}
					}?>
					<tr>
						<th class='center' rowspan='2'>책임<br>보험</th>
						<th>보험사명</th>
						<td colspan='2'>
							<div class="left bold" style="float:left; width:auto;"><span id="lblInsuNm"><?=$laInsuMst[$laInsu[$__SVC_ID__]['code']]['nm'];?></span></div>
							<div class="right" style="float:right; width:auto; margin-top:2px;"><span class="btn_pack m"><button id="btnInsu_<?=$__SVC_ID__;?>" type="button" onclick="lfInsuChange(this);" svcCd="<?=$__SVC_ID__;?>" seq="<?=$laInsu[$__SVC_ID__]['seq'];?>" insuCd="<?=$laInsu[$__SVC_ID__]['code'];?>" from="<?=$laInsu[$__SVC_ID__]['from'];?>" to="<?=$laInsu[$__SVC_ID__]['to'];?>">변경</button></span></div>
						</td>
					</tr>
					<tr>
						<th>가입기간</th>
						<td colspan='2'>
							<div class="left bold" style="float:left; width:auto;"><span id="lblInsuFrom"><?=$myF->dateStyle($laInsu[$__SVC_ID__]['from'],'.');?></span>~<span id="lblInsuTo"><?=$myF->dateStyle($laInsu[$__SVC_ID__]['to'],'.');?></span></div><?
							/**********************************
								재가요양이면서 흥국생명일 때
							***********************************/
							if ($__SVC_ID__ == '0'){

								//보험증권파일경로
								$files = '../insu/pdf/'.$code.'.'.$__SVC_ID__.'.'.$laInsu[$__SVC_ID__]['seq'].'.pdf';

								if(file_exists($files) and is_file($files)){?>
									<div class="right" style="float:right; width:auto;"><span class="btn_pack m"><button id="" type="button" onclick="window.open('<?=$files?>','REPORT','width=700,height=900,scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');">보험증권출력</button></span></div><?
								}else { ?>
									<div class="right" style="float:right; width:auto; margin-top:2px;"><span class="btn_pack m"><button id="" type="button" onclick="alert('보험증권이 등록되어있지 않습니다.');">보험증권출력</button></span></div><?
								}
							}?>
						</td>
					</tr><?
				}
			?>
		</tbody>
	</table>
</div>