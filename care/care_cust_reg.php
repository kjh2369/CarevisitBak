<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$date = Date('Y-m-d');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result = 9;

		__init_form(document.f);

		if (opener.cd){
			setTimeout('lfLoad()',200);
		}else{
			setTimeout('lfNew()',200);
		}
	});

	function lfLoad(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':opener.type+'_FIND'
			,	'cd':opener.cd
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var col = __parseStr(data);

				$('#lblCustCd').text(opener.cd);
				$('#txtCustNm').val(col['nm']);
				$('#optGbn'+col['gbn']).attr('checked',true);
				$('#chkKindS').attr('checked',col['kindS'] == 'Y' ? true : false);
				$('#chkKindW').attr('checked',col['kindW'] == 'Y' ? true : false);
				$('#txtDate').val(col['date']);
				$('#txtBizno').val(col['bizno']);
				$('#txtManager').val(col['manager']);
				$('#txtStat').val(col['stat']);
				$('#txtItem').val(col['item']);
				$('#txtPhone').val(__getPhoneNo(col['phone']));
				$('#txtFAX').val(__getPhoneNo(col['fax']));
				$('#txtPostno').val(col['postno']);
				//$('#txtPostno1').val(col['postno'].substring(0,3));
				//$('#txtPostno2').val(col['postno'].substring(3,6));
				$('#txtAddr').val(col['addr']);
				$('#txtAddrDtl').val(col['addrDtl']);
				$('#txtPernm').val(col['pernm']);
				$('#txtPertel').val(__getPhoneNo(col['pertel']));
				$('#optSupport'+col['support']).attr('checked',true);
				$('#optResource'+col['resource']).attr('checked',true);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfNew(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':opener.type+'_NEW'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				$('#lblCustCd').text(data);

				$('#txtCustNm').val('');
				$('#optGbn1').attr('checked',true);
				$('#chkKindS').attr('checked',false);
				$('#chkKindW').attr('checked',false);
				$('#txtDate').val('');
				$('#txtBizno').val('');
				$('#txtManager').val('');
				$('#txtStat').val('');
				$('#txtItem').val('');
				$('#txtPhone').val('');
				$('#txtFAX').val('');
				$('#txtPostno').val('');
				$('#txtAddr').val('');
				$('#txtAddrDtl').val('');
				$('#txtPernm').val('');
				$('#txtPertel').val('');
				$('#optSupportN').attr('checked',true);
				$('#optResourceN').attr('checked',true);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfApply(){
		if (!$('#txtDate').val()){
			alert('등록일자를 입력하여 주십시오.');
			$('#txtDate').focus();
			return;
		}

		if (!$('#txtCustNm').val()){
			alert('거래처명을 입력하여 주십시오.');
			$('#txtCustNm').focus();
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./care_apply.php'
		,	data :{
				'type':opener.type+'_APPLY'
			,	'cd':$('#lblCustCd').text()
			,	'nm':$('#txtCustNm').val()
			,	'gbn':$('input:radio[name="optGbn"]:checked').val()
			,	'date':$('#txtDate').val()
			,	'kindS':$('#chkKindS').prop('checked') ? 'Y' : 'N'
			,	'kindW':$('#chkKindW').prop('checked') ? 'Y' : 'N'
			,	'bizno':$('#txtBizno').val()
			,	'manager':$('#txtManager').val()
			,	'stat':$('#txtStat').val()
			,	'item':$('#txtItem').val()
			,	'phone':$('#txtPhone').val()
			,	'fax':$('#txtFAX').val()
			,	'postno':$('#txtPostno').val()
			,	'addr':$('#txtAddr').val()
			,	'addrDtl':$('#txtAddrDtl').val()
			,	'pernm':$('#txtPernm').val()
			,	'pertel':$('#txtPertel').val()
			,	'support':$('input:radio[name="optSupport"]:checked').val()
			,	'resource':$('input:radio[name="optResource"]:checked').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result == 9){
					alert('처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else if (result){
					alert(result);
				}else{
					opener.result = 1;
					setTimeout('opener.win.lfLoad()',200);
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
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
					.css('left','30')
					.css('top','30')
					.css('width','450px')
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
		$('#txtPostno').val(postno);
		$('#txtAddr').val(lnaddr+'\n'+rnaddr);
		$('#txtAddrDtl').val('');

		$('#ID_LOCAL_POP').hide();
		$('#ID_LOCAL_POP_DATA').html('');
	}
</script>

<form id="f" name="f" method="post">
<div class="title">
	<div>자원 등록 및 변경</div>
</div>
<table class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="90px">
		<col width="150px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>구분</th>
			<td colspan="3">
				<input id="optGbn1" name="optGbn" type="radio" class="radio" value="1"><label for="optGbn1">공공</label>
				<input id="optGbn2" name="optGbn" type="radio" class="radio" value="2"><label for="optGbn2">기업</label>
				<input id="optGbn3" name="optGbn" type="radio" class="radio" value="3"><label for="optGbn3">단체</label>
				<input id="optGbn4" name="optGbn" type="radio" class="radio" value="4"><label for="optGbn4">개인</label>
			</td>
		</tr>
		<tr>
			<th>분류</th>
			<td colspan="3">
				<label><input id="chkKindS" name="chk" type="checkbox" class="checkbox" value="Y">후원자</label>
				<label><input id="chkKindW" name="chk" type="checkbox" class="checkbox" value="Y">봉사자</label>
			</td>
		</tr>
		<tr>
			<th>등록일자</th>
			<td colspan="3">
				<input id="txtDate" name="txt" type="text" value="<?=$date;?>" class="date">
			</td>
		</tr>
		<tr>
			<th>거래처코드</th>
			<td><div id="lblCustCd" class="left bold"></div></td>
			<th>거래처명</th>
			<td><input id="txtCustNm" name="txt" type="text" value="" style="width:100%;"></td>
		</tr>
		<tr>
			<th>사업자번호</th>
			<td><input id="txtBizno" name="txt" type="text" value="" class="phone" alt="biz"></td>
			<th>대표자명</th>
			<td><input id="txtManager" name="txt" type="text" value=""></td>
		</tr>
		<tr>
			<th>업태</th>
			<td><input id="txtStat" name="txt" type="text" value="" style="width:100%;"></td>
			<th>업종</th>
			<td><input id="txtItem" name="txt" type="text" value="" style="width:100%;"></td>
		</tr>
		<tr>
			<th>연락처</th>
			<td><input id="txtPhone" name="txt" type="text" value="" class="phone"></td>
			<th>FAX</th>
			<td><input id="txtFAX" name="txt" type="text" value="" class="phone"></td>
		</tr>
		<tr>
			<th rowspan="3">주소</th>
			<td colspan="3">
				<input id="txtPostno" name="txtPostno" type="text" tabindex="21" maxlength="6" class="no_string" style="width:50px; margin-right:0;">
					<span class="btn_pack m"><button onclick="lfPostno();">찾기</button></span>
				<!--input id="txtPostno1" name="txt" type="text" value="" class="no_string" style="width:30px;">
				<span class="btn_pack small"><button type="button" onClick="__helpAddress(__getObject('txtPostno1'),__getObject('txtPostno2'),__getObject('txtAddr'),__getObject('txtAddrDtl'));">찾기</button></span-->
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<input id="txtAddr" name="txt" type="text" value="" style="width:100%;">
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<input id="txtAddrDtl" name="txt" type="text" value="" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th>담당자</th>
			<td><input id="txtPernm" name="txt" type="text" value=""></td>
			<th>연락처</th>
			<td><input id="txtPertel" name="txt" type="text" value="" class="phone"></td>
		</tr>
		<!--tr>
			<th>재가지원</th>
			<td>
				<input id="optSupportY" name="optSupport" type="radio" class="radio" value="Y"><label for="optSupportY">예</label>
				<input id="optSupportN" name="optSupport" type="radio" class="radio" value="N"><label for="optSupportN">아니오</label>
			</td>
			<th>자원연계</th>
			<td>
				<input id="optResourceY" name="optResource" type="radio" class="radio" value="Y"><label for="optResourceY">예</label>
				<input id="optResourceN" name="optResource" type="radio" class="radio" value="N"><label for="optResourceN">아니오</label>
			</td>
		</tr-->
		<tr>
			<td class="center bottom last" style="padding-top:10px;" colspan="4">
				<span class="btn_pack m"><button type="button" onclick="lfNew();">신규</button></span>
				<span class="btn_pack m"><button type="button" onclick="lfApply();">적용</button></span>
				<span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span><?
				if ($debug){?>
					<span class="btn_pack m"><button onclick="document.f.submit();">reload</button></span><?
				}?>
			</td>
		</tr>
	</tbody>
</table>
</form>

<div id="ID_LOCAL_POP" style="position:absolute; left:0; top:0; width:0; height:0; display:none; z-index:11; background:url('../image/tmp_bg.png'); border:2px solid #4374D9;">
	<div style="position:absolute; text-align:right; width:100%; top:-20px; left:-5px;">
		<a href="#" onclick="$('#ID_LOCAL_POP').hide();"><img src="../image/btn_exit.png"></a>
	</div>
	<div id="ID_LOCAL_POP_DATA" style="position:absolute; width:100%;"></div>
</div>

<?
	include_once('../inc/_footer.php');
?>