<?
	if ($is_path != 'counsel'){
		include_once('../counsel/mem_counsel_head.php');
	}

	if (!isset($mst[$basic_kind]['m02_picture'])){
		$mst[$basic_kind]['m02_picture'] = '../image/no_img_bg.gif';
	}else{
		if (substr($mst[$basic_kind]['m02_picture'],0,strlen('../mem_picture/')) != '../mem_picture/'){
			$mst[$basic_kind]['m02_picture'] = str_replace('../mem_picture/', '', $mst[$basic_kind]['m02_picture']);
			$mst[$basic_kind]['m02_picture'] = '../mem_picture/'.$mst[$basic_kind]['m02_picture'];
		}
	}

	//echo $mst[$basic_kind]['m02_picture'];

	$sql = 'select case mem_edu_lvl when \'1\' then \'중졸이하\'
									when \'3\' then \'고졸\'
									when \'5\' then \'대학중퇴\'
									when \'7\' then \'대졸이상\' else \'-\' end as edu
			,	   case mem_gbn when \'1\' then \'일반\'
								when \'3\' then \'차상위\'
								when \'A\' then \'기초수급자\' else \'-\' end as gbn
			,	   case mem_abode when \'1\' then \'전세\'
								  when \'3\' then \'월세\'
								  when \'5\' then \'자가\' else \'-\' end as abode
			  from counsel_mem
			 where org_no  = \''.$code.'\'
			   and mem_ssn = \''.$jumin.'\'';

	$m_cus_if = $conn->get_array($sql);
?>
<script type="text/javascript">
	$(document).ready(function(){
		//setTimeout('lfSvcLTC()',10);
	});

	function lfSvcLTCReg(){
		var loginYn = __longcareLoginYn();

		if (!loginYn){
			alert('건보로그인을 하여 주십시오.');
			return false;
		}

		var laSvcKind = {'200':'001','500':'002','800':'003'};
		var rst = '';

		for(var i in laSvcKind){
			rst = lfChkLTCReg(laSvcKind[i]);

			if (rst['name']){
				break;
			}
		}

		if (rst['name']){
			alert('"'+rst['name']+'" 요양보호사를 검색하였습니다.');
			$('#memNm').val(rst['name']);
			$('#workDt').val(rst['wrkDt']);
		}else{
			alert('해당주민번호로 건보공단의 요양보호사정보를 찾을 수 없습니다.');
		}
	}

	function lfSvcLTC(){
		if ($('#memMode').val() == '0') return;

		if ($('#mem_request').css('display') != 'none'){
			$('#lblMsg1').hide();
			return false;
		}

		var loginYn = __longcareLoginYn();

		if (!loginYn){
			$('#memLCRequest').hide();
			$('#memLCLayer').css('left',$('#memLCLayer').parent().offset().left+1)
							.css('top',$('#memLCLayer').parent().offset().top)
							.css('width',$('#memLCLayer').parent().width()-1)
							.css('height',$('#memLCLayer').parent().height()-1).show();
			return false;
		}

		var laSvcKind = {'200':'001','500':'002','800':'003'};

		for(var i in laSvcKind){
			try{
				var rst = lfChkLTCReg(laSvcKind[i]);

				if (rst['name']){
					$('#lblLCName').text(rst['name']);
					$('#lblLC_'+i).css('font-weight','bold').css('color','blue').text('Y');
				}else{
					$('#lblLC_'+i).css('font-weight','normal').css('color','black').text('N');
				}
			}catch(e){
			}
		}

		$('#memLCLayer').hide();
		$('#memLCRequest').css('left',$('#memLCLayer').parent().offset().left+1)
						  .css('top',$('#memLCLayer').parent().offset().top).show();
	}

	function lfChkLTCReg(asSvcKind){
		var loginYn = __longcareLoginYn();

		if (!loginYn){
			alert('건보로그인을 하여 주십시오.');
			return false;
		}

		if ($('#memMode').val() == '0'){
			var jumin = $('#ssn1').val()+'-'+$('#ssn2').val();
		}else{
			var jumin = getHttpRequest('../inc/_ed_code.php?type=2&value='+$('#memJumin').val());
				jumin = jumin.substring(0,6)+'-'+jumin.substring(6,13);
		}

		if (jumin.length != 14){
			return '';
		}

		var date  = new Date();
		var year  = date.getFullYear();
		var month = date.getMonth()+1;
			month = (month < 10 ? '0' : '')+month;
		var rst;

		$.ajax({
			type : 'POST',
			async: false,
			url  : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=YR',
			data : {
				'serviceKind' : asSvcKind
			,	'payMm'		  : year+month
			,	'fnc'		  : 'care'
			},
			success: function (data){
				var tmpNo = '';
				var tmpNm = '';
				var wrkDt = '';

				if ($('td:contains("'+jumin+'")', data).length > 0){
					$('td:contains("'+jumin+'")', data).each(function(){
						tmpNo = $(this).attr('id').replace('careJuminNo','').replace('qlfNo','');
						tmpNm = $('#careNm'+tmpNo, data).text();
						wrkDt = $('#jobFrDt'+tmpNo, data).text().split('.').join('-');

						return false;
					});
				}

				rst = {
					'name':tmpNm
				,	'wrkDt':wrkDt
				};
			},
			error: function (request, status, error){
				//alert('[ERROR No.03]'
				//	 +'\nCODE : ' + request.status
				//	 +'\nSTAT : ' + status
				//	 +'\nMESSAGE : ' + request.responseText);
			}
		});

		return rst;
	}

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
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="105px">
		<col width="70px">
		<col width="80px">
		<col width="180px">
		<col width="50px">
		<col width="50px">
		<col width="100px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="center top bottom" style="padding-top:5px;" rowspan="5">
				<div id="pictureView" style="width:90px; height:120px;"><img id="img_picture" src="<?=$mst[$basic_kind]['m02_picture'];?>" style="border:1px solid #000;">
				<input name="mem_picture_back" type="hidden" value="<?=$mst[$basic_kind]['m02_picture'];?>">
			</td>
			<th rowspan="3">개인정보</th>
			<th>주민번호</th>
			<td class="">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$myF->issStyle($jumin).'</div>';
				}else{
					if ($counsel_mode == 1){
						if ($gDomain == 'kacold.net'){?>
							<input id="ssn1" name="ssn1" type="text" tabindex="1" value="" maxlength="6" class="no_string" style="width:50px;" onkeydown="__onlyNumber(this);"> -
							<input id="ssn2" name="ssn2" type="text" tabindex="1" value="" maxlength="7" class="no_string" style="width:55px;" onkeydown="__onlyNumber(this);"><?
						}else{?>
							<input id="ssn1" name="ssn1" type="text" tabindex="1" value="" maxlength="6" class="no_string" style="width:50px;" onkeydown="__onlyNumber(this);" onkeyup="_memCheckJumin('ssn1','ssn2');"> -
							<input id="ssn2" name="ssn2" type="text" tabindex="1" value="" maxlength="7" class="no_string" style="width:55px;" onkeydown="__onlyNumber(this);" onkeyup="_memCheckJumin('ssn1','ssn2');"><?
						}

						if ($is_path != 'counsel'){?>
							<span class="btn_pack small"><button type="button" onClick="find_counsel();">찾기</button></span><?
						}
					}else{?>
						<div class="left"><?=$myF->issNo($jumin);?></div>
						<input id="memJumin" name="ssn" type="hidden" value="<?=$ed->en($jumin);?>"><?
					}
				}
			?>
			</td>
			<th>학력</th>
			<td class="left" colspan="2"><?=$m_cus_if['edu'];?></td>
			<td class="center top last" rowspan="3" colspan="2"><?
				if ($mem_mode == 1){?>
					<!--span id="lblMsg1"><a href="#" onclick="setTimeout('lfSvcLTC()',10); return false;">조회</a></span-->
					<div id="memLCRequest" style="position:absolute; display:none; z-index:10; margin-left:-1px; margin-top:-1px; background-color:#ffffff;">
						<table class="my_table my_border_blue" style="width:100%;">
							<colgroup>
								<col span="3">
							</colgroup>
							<tbody>
								<tr>
									<th class="center">성명</th>
									<td class="left last" colspan="2"><span id="lblLCName"></span></td>
								</tr>
								<tr>
									<th class="center">요양</th>
									<th class="center">목욕</th>
									<th class="center last">간호</th>
								</tr>
								<tr>
									<td class="center bottom"><span id="lblLC_200"></span></td>
									<td class="center bottom"><span id="lblLC_500"></span></td>
									<td class="center bottom last"><span id="lblLC_800"></span></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div id="memLCLayer" style="position:absolute; text-align:center; display:none; z-index:11; left:0; top:0; border:2px solid#cccccc; background-color:#ffffff;">
						건보공단 로그인 후 새로고침을 클릭하여 주십시오.<br>
						<a href="#" onclick="setTimeout('lfSvcLTC()',10);">새로고침</a>
					</div><?
				}?>
				<div id="mem_request" class="left" style="display:none; padding-top:5px;">&nbsp;</div>
			</td>
		</tr>
		<tr>
			<th>성명</th>
			<td class="">
			<?
				$name = !empty($mst[$basic_kind]['m02_yname']) ? $mst[$basic_kind]['m02_yname'] : $mem['mem_nm'];

				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$name.'</div>';
				}else{
					echo '<div style=\'float:left; width:auto;\'><input id=\'memNm\' name=\'counsel_name\' type=\'text\' tabindex=\'1\' value=\''.$name.'\' style=\'width:80px; height:19px; margin-top:0; margin-bottom:0;\' maxlength=\'20\' onkeydown=\'__enterFocus();\'></div>
						  <div style=\'float:left; width:auto;\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'_memFind();\'>직원찾기</button></span></div>';
				}
			?>
			</td>
			<th>구분</th>
			<td class="left" colspan="2"><?=$m_cus_if['gbn'];?></td>
		</tr>
		<tr>
			<?
				if ($mem_mode != 0){?>
					<th>이력내역</th>
					<td class="left"><span class="btn_pack small"><button type="button" onclick="_memHistory($('#code').val(), $('#memJumin').val());">조회</button></span></td><?
				}else{?>
					<th>건보공단</th>
					<td class="left"><span class="btn_pack m"><button type="button" onclick="lfSvcLTCReg();">요양보호사 찾기</button></span></td><?
				}
			?>
			<th>주거</th>
			<td class="left" colspan="2"><?=$m_cus_if['abode'];?></td>
		</tr>
		<tr>
			<th rowspan="3">소재</th>
			<th>우편번호</th>
			<td><?
				if ($mem_mode == 0){
					$postno = !empty($mst[$basic_kind]['m02_ypostno']) ? $mst[$basic_kind]['m02_ypostno'] : $mem['mem_postno'];
				}else{
					$postno = $mst[$basic_kind]['m02_ypostno'];
				}

				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$postno.'</div>';
				}else{?>
					<input id="txtPostNo" name="txtPostNo" type="text" value="<?=$postno;?>" tabindex="21" maxlength="6" class="no_string" style="width:50px; margin-right:0;">
					<span class="btn_pack m"><button onclick="lfPostno();">찾기</button></span><?
				}?>
			</td>
			<th rowspan="3">연락처</th>
			<th>유선</th>
			<td class="">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$myF->phoneStyle($mst[$basic_kind]['m02_ytel2']).'</div>';
				}else{?>
					<input id="phone" name="mem_phone" type="text" tabindex="11" value="<?=$myF->phoneStyle($mst[$basic_kind]['m02_ytel2']);?>" maxlength="11" class="phone" onkeydown="__onlyNumber(this);"><?
				}
			?>
			</td>
			<th>RFID</th>
			<td class="last"><?
				if (!$mst[$basic_kind]['m02_rfid_yn']) $mst[$basic_kind]['m02_rfid_yn'] = 'Y';?>
				<input id="rfidY" name="rfid_yn" type="radio" class="radio" value="Y" <? if($mst[$basic_kind]['m02_rfid_yn'] == 'Y'){echo 'checked';} ?>><label for="rfidY">유</label>
				<input id="rfidN" name="rfid_yn" type="radio" class="radio" value="N" <? if($mst[$basic_kind]['m02_rfid_yn'] != 'Y'){echo 'checked';} ?>><label for="rfidN">무</label>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<?
				if ($mem_mode == 0){
					if (!empty($mst[$basic_kind]['m02_yjuso1'])){
						$addr     = $mst[$basic_kind]['m02_yjuso1'];
						$addr_dtl = $mst[$basic_kind]['m02_yjuso2'];
					}else{
						$addr     = $mem['mem_addr'];
						$addr_dtl = $mem['mem_addr_dtl'];
					}
				}else{
					$addr     = $mst[$basic_kind]['m02_yjuso1'];
					$addr_dtl = $mst[$basic_kind]['m02_yjuso2'];
				}

				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$addr.'</div>';
				}else{?>
					<textarea id="txtAddr" name="txtAddr" tabindex="21" style="width:100%; height:19px; margin-top:0; margin-bottom:0;"><?=$addr;?></textarea><?
				}
			?>
			</td>
			<th>무선</th>
			<td class="">
			<?
				if ($mem_mode == 0){
					$mobile = !empty($mst[$basic_kind]['m02_ytel']) ? $mst[$basic_kind]['m02_ytel'] : $mem['mem_mobile'];
				}else{
					$mobile = $mst[$basic_kind]['m02_ytel'];
				}

				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$myF->phoneStyle($mobile).'</div>';
				}else{?>
					<input id="mobile" name="mem_mobile" type="text" tabindex="11" value="<?=$myF->phoneStyle($mobile);?>" maxlength="11" class="phone" onkeydown="__onlyNumber(this);"><?
				}
			?>
			</td>
			<th>통신사</th>
			<td class="last">
				<select id="mobileKind" name="mobile_kind" style="width:auto;">
					<option value="">--</option>
					<option value="1" <? if($mst[$basic_kind]['m02_mobile_kind'] == '1'){echo 'selected';} ?>>SKT</option>
					<option value="2" <? if($mst[$basic_kind]['m02_mobile_kind'] == '2'){echo 'selected';} ?>>KT</option>
					<option value="3" <? if($mst[$basic_kind]['m02_mobile_kind'] == '3'){echo 'selected';} ?>>LG U+</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="center top" rowspan="1">
			<?
				if ($view_type == 'read'){
				}else{?>
					<div style="width:50px; height:18px; background:url(../image/find_file.gif) no-repeat left 50%;"><input type="file" name="counsel_mem_picture" id="file" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin:0;" onchange="__showLocalImage(this,'pictureView');"></div><?
				}
			?>
			</td>
			<td colspan="2">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$addr_dtl.'</div>';
				}else{?>
					<input id="txtAddrDtl" name="txtAddrDtl" type="text" value="<?=$addr_dtl;?>" tabindex="21" style="width:100%;"><?
				}
			?>
			</td>
			<th>e-mail</th>
			<td class="">
			<?
				if ($mem_mode == 0){
					$email = !empty($mst[$basic_kind]['m02_email']) ? $mst[$basic_kind]['m02_email'] : $mem['mem_email'];
				}else{
					$email = $mst[$basic_kind]['m02_email'];
				}

				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$email.'</div>';
				}else{?>
					<input id="email" name="mem_email" type="text" tabindex="11" value="<?=$email;?>" style="width:100%;"><?
				}
			?>
			</td>
			<th>모델명</th>
			<td class="last"><input id="modelNo" name="modelNo" type="text" tabindex="11" value="<?=$mst[$basic_kind]['m02_model_no'];?>" style="width:100%;" maxlength="20"></td>
		</tr>
	</tbody>
</table>

<div id="ID_LOCAL_POP" style="position:absolute; left:0; top:0; width:0; height:0; display:none; z-index:11; background:url('../image/tmp_bg.png'); border:2px solid #4374D9;">
	<div style="position:absolute; text-align:right; width:100%; top:-20px; left:-5px;">
		<a href="#" onclick="$('#ID_LOCAL_POP').hide();"><img src="../image/btn_exit.png"></a>
	</div>
	<div id="ID_LOCAL_POP_DATA" style="position:absolute; width:100%;"></div>
</div>
<?
	unset($m_cus_if);

	/**************************************************

		환경변수

	**************************************************/
	echo '<input name=\'mem_counsel_gbn\' type=\'hidden\' value=\'\'>';
?>