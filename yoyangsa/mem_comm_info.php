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

<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
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
	
	//우편번호 검색
	function lfPostCode(postno, addr, addr_dtl){
		var width = 500; //팝업의 너비
		var height = 600; //팝업의 높이
		
				
		new daum.Postcode({
			oncomplete: function(data) {
				
				// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다.
				// 예제를 참고하여 다양한 활용법을 확인해 보세요.
				// 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
				
				// 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
				// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
				var fullRoadAddr = data.roadAddress;
				var extraRoadAddr = '';
				
			
				// 법정동명이 있을 경우 추가한다. (법정리는 제외)
				// 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
				if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
					extraRoadAddr += data.bname;
				}
				// 건물명이 있고, 공동주택일 경우 추가한다.
				if(data.buildingName !== '' && data.apartment === 'Y'){
				   extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
				}
				// 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
				if(extraRoadAddr !== ''){
					extraRoadAddr = ' (' + extraRoadAddr + ')';
				}
				// 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
				if(fullRoadAddr !== ''){
					fullRoadAddr += extraRoadAddr;
				}
				
				

				// 우편번호와 주소 정보를 해당 필드에 넣는다.
				if ($('input:text[name="'+postno+'"]').length > 0){
					$('input:text[name="'+postno+'"]').val(data.zonecode);
				}

				if ($('input:text[name="'+addr+'"]').length > 0){
					$('input:text[name="'+addr+'"]').val(fullRoadAddr);
				}

				document.getElementById(addr_dtl).focus();
			}
		}).open({
			left: (window.screen.width / 2) - (width / 2),
			top: (window.screen.height / 2) - (height / 2)
		});
	}


</script>
<table class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="105px">
		<col width="70px">
		<col width="80px">
		<col width="250px">
		<col width="70px">
		<col width="50px">
		<col width="100px">
		<col width="60px">
		<col width="100px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="center top bottom" style="padding-top:5px;" rowspan="5">
				<div id="pictureView" style="width:90px; height:120px;"><img id="img_picture" src="<?=$mst[$basic_kind]['m02_picture'];?>" style="border:1px solid #000;">
				<input name="mem_picture_back" type="hidden" value="<?=$mst[$basic_kind]['m02_picture'];?>">
			<?
				if ($view_type == 'read'){
				}else{?>
					<div style="width:50px; height:18px; background:url(../image/find_file.gif) no-repeat left 50%;"><input type="file" name="counsel_mem_picture" id="file" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin:0;" onchange="__showLocalImage(this,'pictureView');"></div><?
				}
			?>
			</td>
			<th rowspan="3">개인정보</th>
			<th>주민번호</th>
			<td class="" colspan="2">
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
							<span class="btn_pack m"><button type="button" onClick="find_counsel();">찾기</button></span><?
						}
					}else{?>
						<div class="left"><?=$myF->issNo($jumin);?></div>
						<input id="memJumin" name="ssn" type="hidden" value="<?=$ed->en($jumin);?>"><?
					}
				}
			?>
			</td>
			<th>학력</th>
			<td class="left" ><?=$m_cus_if['edu'];?></td>
			<th>구분</th>
			<td class="left" ><?=$m_cus_if['gbn'];?></td>
			<th>주거</th>
			<td class="left" ><?=$m_cus_if['abode'];?></td>
		</tr>
		<tr>
			<th>성명</th>
			<td class="">
			<?
				$name = !empty($mst[$basic_kind]['m02_yname']) ? $mst[$basic_kind]['m02_yname'] : $mem['mem_nm'];

				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$name.'</div>';
				}else{
					echo '<div style=\'float:left; width:auto;\'><input id=\'memNm\' name=\'counsel_name\' type=\'text\' tabindex=\'1\' value=\''.$name.'\' style=\'width:80px;\' maxlength=\'20\' onkeydown=\'__enterFocus();\'></div>
						  <div style=\'float:left; width:auto;\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'_memFind();\'>직원찾기</button></span></div>';
				}
			?>
			</td>
			<th >연락처</th>
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
			<th>무선</th>
			<td class="" >
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
		</tr>
		<tr>
			<?
				if ($mem_mode != 0){?>
					<th>이력내역</th>
					<td class="left"><span class="btn_pack m"><button type="button" onclick="_memHistory($('#code').val(), $('#memJumin').val());">조회</button></span></td><?
				} 
			?>
			
		</tr>
		<tr>
			
			<th rowspan="2" colspan="2">소&nbsp;&nbsp;&nbsp;재</th>
			<td colspan="8"><?
				if ($mem_mode == 0){
					$postno = !empty($mst[$basic_kind]['m02_ypostno']) ? $mst[$basic_kind]['m02_ypostno'] : $mem['mem_postno'];
				}else{
					$postno = $mst[$basic_kind]['m02_ypostno'];
				}

				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$postno.'</div>';
				}else{?>
					<input id="txtPostNo" name="txtPostNo" type="text" value="<?=$postno;?>" tabindex="21" maxlength="6" class="no_string" style="width:50px; margin-right:0;">
					<span class="btn_pack m"><button onclick="lfPostCode('txtPostNo', 'txtAddr', 'txtAddrDtl');">찾기</button></span><?
				}
				
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
					<input id="txtAddr" name="txtAddr" type="text" value="<?=$addr;?>" tabindex="21" style="width:800px;"><?
				}
				?>
			</td>
			
		</tr>
		<tr>
			<td colspan="8">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$addr_dtl.'</div>';
				}else{?>
					<input id="txtAddrDtl" name="txtAddrDtl" type="text" value="<?=$addr_dtl;?>" tabindex="21" style="width:100%;"><?
				}
			?>
			</td>
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