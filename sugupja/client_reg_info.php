<?
	include_once('../inc/_login.php');

	########################################################################
	#
	# 수급자 기본정보
	#
	########################################################################

	$sql = 'SELECT	bill_phone, real_jumin
			FROM	client_option
			WHERE	org_no	= \''.$code.'\'
			AND		jumin	= \''.$jumin.'\'';
	$cltOption = $conn->get_array($sql);
	
	$billPhone = $cltOption['bill_phone'];
	$realJumin = $ed->de($cltOption['real_jumin']);
	
	$sql = "select m03_jumin as ssn
			,		IFNULL(jumin, m03_jumin) as jumin
			,      m03_name as name
			,      m03_tel as phone
			,      m03_hp as mobile
			,      m03_post_no as postno
			,      m03_juso1 as addr
			,      m03_juso2 as addr_dtl
			,      m03_yboho_name as protect_nm
			,	   m03_yboho_juminno as protect_jumin
			,      m03_yboho_gwange as protect_rel
			,      m03_yboho_phone as protect_tel
			,      m03_yboho_addr as protect_addr
			,      m03_client_no as client_no
			,      m03_memo as memo
			,	   m03_cms_yn as cmsYn
			  from m03sugupja
			  left join mst_jumin
					 on org_no = m03_ccode
					 and code = m03_jumin
					 and gbn = '1'
			 where m03_ccode = '$code'
			   and m03_jumin = '$jumin'
			 order by m03_mkind
			 limit 1";

	$client = $conn->get_array($sql);

	if ($view_type == 'read'){
	}else{?>
		<script type="text/javascript">
		//주민번호 확인
		function check_ssn(obj1, obj2, obj){
			var code = document.getElementById('code');
			var ssn1 = document.getElementById(obj1);
			var ssn2 = document.getElementById(obj2);

			if (ssn1.value.length == 6 && ssn2.value.length == 7){
			}else if (ssn2.value.length == 7){
				$('#name').focus();
				return false;
			}else if (ssn1.value.length == 6){
				ssn2.focus();
				return false;
			}else{
				return false;
			}
			if (!__isSSN(ssn1.value, ssn2.value)){
				//alert('올바른 형식의 주민번호를 입력하여 주십시오.');
				//return false;
			}

			if ('<?=$is_path;?>' == 'counsel'){
				var chkID = '210';
			}else{
				var chkID = '220';
			}

			if ($('#lbTestMode').val() && chkID == '220'){
				var lbFlag = true; //_clientCheckJumin(obj1,obj2);

				if (lbFlag){
					$('#name').focus();
				}

				return lbFlag;
			}

			var rst = getHttpRequest('../inc/_chk_ssn.php?id='+chkID+'&code='+code.value+'&ssn='+ssn1.value+ssn2.value);

			if (rst == 'Y'){
				if ('<?=$is_path;?>' == 'counsel'){
					alert('입력하신 주민번호는 이미등록 주민번호입니다. 확인 후 다시 입력하여 주십시오.');
					ssn1.value = '';
					ssn2.value = '';
					ssn1.focus();

					return false;
				}else{
					//var ssn = getHttpRequest('../inc/_check_class.php?check=ed&cd='+ssn1.value+ssn2.value);
					//find_counsel(ssn);

					alert('입력하신 주민번호는 이미등록 주민번호입니다. 확인 후 다시 입력하여 주십시오.');
					ssn1.value = '';
					ssn2.value = '';
					ssn1.focus();

					return false;
				}
			}
		}

		</script><?
	}
?>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="70px">
		<col width="100px">
		<col width="100px">
		<col width="70px">
		<col width="70px">
		<col width="120px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th rowspan="3">기본정보</th>
			<th>생년월일</th>
			<td colspan="2">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$myF->issStyle($client['jumin']).'</div>';
				}else{
					if ($write_mode == 1){
						//if ($debug){
							echo '<input id=\'jumin1\' name=\'jumin1\' type=\'text\' value=\'\' maxlength=\'6\' tabindex="1" class=\'no_string\' style=\'width:50px;\'>';
							echo '<label><input id="gender1" name="gender" type="radio" value="1" class="radio">남</label>';
							echo '<label><input id="gender2" name="gender" type="radio" value="2" class="radio">여</label>';
							echo '<input id=\'jumin\' name=\'jumin\' type=\'hidden\' value=\''.$ed->en($jumin).'\'>';
							//echo '<span class=\'btn_pack small\' style="margin-left:5px; margin-top:1px;"><button type=\'button\' onClick=\'find_counsel();\'>찾기</button></span>';
						//}else{
						//	echo '<input id=\'jumin1\' name=\'jumin1\' type=\'text\' value=\'\' maxlength=\'6\' tabindex="1" class=\'no_string\' style=\'width:50px;\' onkeyup=\'check_ssn("jumin1","jumin2",this);\'> - ';
						//	echo '<input id=\'jumin2\' name=\'jumin2\' type=\'text\' value=\'\' maxlength=\'7\' tabindex="2" class=\'no_string\' style=\'width:55px;\' onkeyup=\'check_ssn("jumin1","jumin2",this);\'>';
						//	echo '<span class=\'btn_pack small\'><button type=\'button\' onClick=\'find_counsel();\'>찾기</button></span>';
						//}
					}else{
						echo '<input id=\'jumin\' name=\'jumin\' type=\'hidden\' value=\''.$ed->en($jumin).'\'>';
						echo '<div class=\'left\'><a href=\'#\' onclick=\'show_ssn();\'>'.$myF->issNo($jumin).'</a></div>';
					}
				}
			?>
			</td><?
			if ($write_mode == 1){?>
				<th><label><input id="chkAppNo" type="checkbox" class="checkbox" style="margin-left:0;" checked>인정번호</label></th>
				<td class="left" colspan="2">
					<span class="bold">L</span>
					<input id="cnfAppNo" type="text" class="no_string" onchange="$(this).attr('chkYn', 'N');" style="margin-left:0; margin-right:0;" maxlength="10" chkYn="N">
					<span class="btn_pack small"><button id="btnAppnoCnf" onclick="lfCnfAppNo();">확인</button></span>
					<script type="text/javascript">
						$('#jumin1').unbind('focus').bind('focus', function(){
							if ($('#cnfAppNo').attr('chkYn') != 'Y' && $('#chkAppNo').attr('checked')){
								alert('인정번호를 입력하여 주십시오.');
								$('#cnfAppNo').focus();
								return;
							}

							if($('#chkAppNo').attr('checked') && $('#cnfAppNo').attr('value').length < 10){
								alert('인정번호를 10자리로 입력하여 주십시오.');
								$('#cnfAppNo').focus();
								return;
							}

						});

						$('#chkAppNo').unbind('click').bind('click', function(){
							if ($(this).attr('checked')){
								$('#cnfAppNo, #btnAppnoCnf, #loLink_0').attr('disabled', false);
							}else{
								$('#cnfAppNo, #btnAppnoCnf, #loLink_0').attr('disabled', true);
							}
						});

						function lfCnfAppNo(){
							$.ajax({
								type:'POST',
								url:'./check_appno.php',
								data:{
									'appno':$('#cnfAppNo').val()
								},
								beforeSend: function (){
									$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
								},
								success:function(result){
									if (result == 'Y'){
										$('#cnfAppNo').attr('chkYn', 'Y');
										$('#jumin1').focus();
										$('#mgmtNo').attr('value', 'L'+$('#cnfAppNo').val()).text('L'+$('#cnfAppNo').val());
									}else{
										var v = __parseVal(result);

										if (confirm(v['name']+'님의 정보가 있습니다. 이동하시겠습니까?')){
											var f = document.f;

											f.code.value = '<?=$orgNo;?>';
											f.kind.value = '0';
											f.jumin.value = v['jumin'];
											f.action = 'client_new.php';
											f.submit();
										}else{
											$('#cnfAppNo').attr('chkYn', 'N').val('');
											$('#mgmtNo').attr('value', '').text('');
										}
									}
									$('#tempLodingBar').remove();
								},
								error: function (request, status, error){
									$('#tempLodingBar').remove();

									alert('[ERROR]'
										 +'\nCODE : ' + request.status
										 +'\nSTAT : ' + status
										 +'\nMESSAGE : ' + request.responseText);
								}
							}).responseXML;
						}
					</script>
				</td><?
			}else{?>
				<td colspan="3"></td><?
			}?>
			<th>관리번호</th>
			<td class="last">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['client_no'].'</div>';
				}else{
					
					if($code == '31138000044'){
						echo '<input id=\'clientNo\' name=\'client_no\' type=\'text\' value=\''.$client['client_no'].'\' maxlength=\'15\' >';
					}else {
						echo '<input id=\'clientNo\' name=\'client_no\' type=\'text\' value=\''.$client['client_no'].'\' maxlength=\'15\' onchange=\'return chk_clientno(this);\'>';
					}
				}
			?>
			</td>
		</tr>
		<tr>
			<th>성명</th>
			<td class="last">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['name'].'</div>';
				}else{
					echo '<input id=\'name\' name=\'name\' type=\'text\' value=\''.$client['name'].'\' style=\'height:19px; margin-top:0; margin-bottom:0;\' tabindex="3" tag=\'수급자성명을 입력하여 주십시오.\'>';
				}
			?>

			</td>
			<td><?
				if ($view_type != 'read'){?>
					<span class="btn_pack small"><button type="button" onclick="_clientFind();">찾기</button></span><?
					if ($write_mode == '1'){?>
						<!--span class="btn_pack small"><button type="button" onclick="setTimeout('lfSvcLTCReg()',10);">건보</button></span--><?
					}
				}?>
			</td>
			<th>연락처</th>
			<th>유선</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$myF->phoneStyle($client['phone']).'</div>';
				}else{
					echo '<input id=\'phone\' name=\'phone\' type=\'text\' value=\''.$myF->phoneStyle($client['phone']).'\' class=\'phone\' maxlength=\'11\'>';
				}
			?>
			</td>
			<th>무선</th>
			<td class="last">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$myF->phoneStyle($client['mobile']).'</div>';
				}else{
					echo '<input id=\'mobile\' name=\'mobile\' type=\'text\' value=\''.$myF->phoneStyle($client['mobile']).'\' class=\'phone\' maxlength=\'11\'>';
				}
			?>
			</td>
		</tr>
		<tr><?
			if($gDomain == 'dolvoin.net' || $code == '1234' || $code == '34874000062' || $code == '34213000048' || $code == '31147000373' || $code == '34121000189' || $code == '32817000343' || $code == '34717000120'){ 
				if($write_mode == 2){ ?>
					<th>주민번호</th>
					<td colspan="2">
						<input id="realJumin" name="realJumin" type="text" value="<?=$realJumin;?>" class="jumin_no" />
					</td><?
				}
			}else { ?>
				<td colspan="3" ></td><?
			} ?>
			<th class="left" colspan="2">현금영수증발행연락처</th>
			<td class="left" ><input name="txtBillPhone" type="text" value="<?=$myF->phoneStyle($billPhone);?>" class="phone"></td>
			<th class="left" >본인부담금</br>CMS 여부</th>
			<td class="left" >
				<input type="radio" class="radio" name="optCmsYn" id="optCmsYn_1" value="Y" <?= $client['cmsYn'] == 'Y' ? 'checked' : ''; ?> >예
				<input type="radio" class="radio" name="optCmsYn" id="optCmsYn_2" value="N" <?= $client['cmsYn'] == 'N' ? 'checked' : ''; ?> >아니오
			</td>
		</tr>
		<tr>
			<th rowspan="3">소재</th>
			<th>우편번호</th>
			<td colspan="2">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.substr($client['postno'],0,3).'-'.substr($client['postno'],3,3).'</div>';
				}else{?>
					<script type="text/javascript">
						function lfPostno(){
							$.ajax({
								type:'POST',
								url:'../find/postno.php',
								data:{
									'rstFun':'lfPostnoRst'
								},
								beforeSend: function (){
									$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
								},
								success:function(html){
									$('#ID_LOCAL_POP_DATA').html(html);
									$('#ID_LOCAL_POP')
										.css('left','300px')
										.css('top','200px')
										.css('width','600px')
										.css('height','500px')
										.show();
									$('#tempLodingBar').remove();
								},
								error: function (request, status, error){
									$('#tempLodingBar').remove();

									alert('[ERROR]'
										 +'\nCODE : ' + request.status
										 +'\nSTAT : ' + status
										 +'\nMESSAGE : ' + request.responseText);
								}
							}).responseXML;
						}

						function lfPostnoRst(gbn, postno, lnaddr, rnaddr){
							$('#txtPostNo').val(postno);
							$('#txtAddr').val(lnaddr+'\n'+rnaddr);
							$('#txtAddrDtl').val('');

							$('#ID_LOCAL_POP').hide();
							$('#ID_LOCAL_POP_DATA').html('');
						}
					</script>
					<input id="txtPostNo" name="txtPostNo" type="text" value="<?=$client['postno'];?>" tabindex="21" maxlength="6" class="no_string" style="width:50px; margin-right:0;">
					<span class="btn_pack m"><button onclick="lfPostno();">찾기</button></span><?
					//echo '<input id=\'postNo1\' name=\'postno1\' type=\'text\' value=\''.substr($client['postno'],0,3).'\' maxlength=\'3\' class=\'no_string\' style=\'width:30px;\'> - ';
					//echo '<input id=\'postNo2\' name=\'postno2\' type=\'text\' value=\''.substr($client['postno'],3,3).'\' maxlength=\'3\' class=\'no_string\' style=\'width:30px;\'>';
					//echo '<span class=\'btn_pack small\'><button type=\'button\' onClick=\'__helpAddress(document.f.postno1, document.f.postno2, document.f.addr, document.f.addr_dtl);\'>찾기</button></span>';
				}
			?>
			</td>
			<th class="left" rowspan="3" >메모</th>
			<td class="last" colspan="5" rowspan="3">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\' style=\'overflow-x:hidden; overflow-y:scroll; width:100%; height:100%;\'>'.nl2br(stripslashes($client['memo'])).'</div>';
				}else{
					echo '<div style=\'width:100%; height:100%;\'><textarea id=\'memo\' name=\'memo\' style=\'width:100%; height:100%; margin:0; border:none;\'>'.stripslashes($client['memo']).'</textarea></div>';
				}
			?>
			</td>
		</tr>
		<tr>
			<td colspan="3">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['addr'].'</div>';
				}else{?>
					<textarea id="txtAddr" name="txtAddr" tabindex="21" style="width:100%; height:19px; margin-top:0; margin-bottom:0;"><?=$client['addr'];?></textarea><?
					//echo '<input id=\'addr\' name=\'addr\' type=\'text\' value=\''.$client['addr'].'\' style=\'width:100%;\'>';
				}
			?>
			</td>
			
		</tr>
		<tr>
			<td colspan="3">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['addr_dtl'].'</div>';
				}else{?>
					<input id="txtAddrDtl" name="txtAddrDtl" type="text" value="<?=$client['addr_dtl'];?>" tabindex="21" style="width:100%;"><?
					//echo '<input id=\'addrDtl\' name=\'addr_dtl\' type=\'text\' value=\''.$client['addr_dtl'].'\' style=\'width:100%;\'>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th rowspan="2">보호자</th>
			<th>성명</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['protect_nm'].'</div>';
				}else{
					echo '<input id=\'protectNm\' name=\'protect_nm\' type=\'text\' value=\''.$client['protect_nm'].'\'>';
				}
			?>
			</td>
			<th>관계</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['protect_rel'].'</div>';
				}else{
					echo '<input id=\'protectRel\' name=\'protect_rel\' type=\'text\' value=\''.$client['protect_rel'].'\'>';
				}
			?>
			</td>

			<th>연락처</th>
			<td class="last" colspan="3">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$myF->phoneStyle($client['protect_tel']).'</div>';
				}else{
					echo '<input id=\'protectTel\' name=\'protect_tel\' type=\'text\' value=\''.$myF->phoneStyle($client['protect_tel']).'\' class=\'phone\' maxlength=\'11\'>';
				}
			?>
			</td>
		</tr>
		<tr>
			<th>주민7자리</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$myF->issSsn7($client['protect_jumin']).'</div>';
				}else{ ?>
					<input id="protectJumin" name="protect_jumin" type="text" value="<?=$myF->issSsn7($client['protect_jumin']);?>"  class="jumin_no" alt="ssn" /><?
				}
			?>
			</td>
			<th>주소</th>
			<td class="last" colspan="5">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$client['protect_addr'].'</div>';
				}else{
					echo '<input id=\'protectAddr\' name=\'protect_addr\' type=\'text\' value=\''.$client['protect_addr'].'\' style="width:100%;">';
				}
			?>
			</td>
		</tr>
	</tbody>
</table>
<?
	unset($client);
?>

<div id="ID_LOCAL_POP" style="position:absolute; left:0; top:0; width:0; height:0; display:none; z-index:11; background:url('../image/tmp_bg.png'); border:2px solid #4374D9;">
	<div style="position:absolute; text-align:right; width:100%; top:-20px; left:-5px;">
		<a href="#" onclick="$('#ID_LOCAL_POP').hide();"><img src="../image/btn_exit.png"></a>
	</div>
	<div id="ID_LOCAL_POP_DATA" style="position:absolute; width:100%;"></div>
</div>