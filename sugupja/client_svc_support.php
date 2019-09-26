<?
	#######################################################################
	#
	#

	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	$current_svc_nm = $conn->kind_name($laSvcList, $__CURRENT_SVC_ID__, 'id');
	$body_w = '100%';

	#
	#######################################################################

	include('./client_reg_sub_reason.php');

	//지원 및 자원 연계 정보
	$sql = 'SELECT	care_support
			,		care_resource
			FROM	b02center
			WHERE	b02_center = \''.$code.'\'';

	$tmp = $conn->get_array($sql);

	$careSupportYn	= $tmp['care_support'];
	$careResourceYn	= $tmp['care_resource'];

	Unset($tmp);
?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; <?=$view_type == 'read' ? '' : 'border-bottom:none;';?>">
	<colgroup>
		<col width="80px">
		<col width="55px">
		<col width="90px">
		<col width="40px">
		<col width="90px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>서비스단가</th><?
			if ($view_type == 'read'){?>
				<td colspan="5"><div class="left"><?=number_format($client['amt1']).$str_svc_type;?></div></td><?
			}else{?>
				<td colspan="5">
					<input id="svcCost_<?=$__CURRENT_SVC_ID__;?>" name="svcCost_<?=$__CURRENT_SVC_ID__;?>" type="text" value="0" class="number clsObjData" style="width:70px;">
				</td><?
			}?>
		</tr>
	<tbody id="tbodySupport">
		<tr>
			<th rowspan="3">수급자<br>기관정보</th>
			<th>기관기호</th>
			<td>
				<input id="txtResourceCd" name="txtResourceCd" type="text" value="" class="clsObjData" style="width:100%;">
			</td>
			<th>기관명</th>
			<td>
				<input id="txtResourceNm" name="txtResourceNm" type="text" value="" class="clsObjData" style="width:100%;">
			</td>
			<td>
				<span class="btn_pack find" class="clsObjData" style="margin-left:2px; margin-top:1px;"></span>
			</td>
		</tr>
		<tr>
			<th>인정번호</th>
			<td colspan="5">
				<span class="bold" style="padding-left:5px;">L</span><input id="txtResourceNo" name="txtResourceNo" type="text" value="" class="clsObjData no_string" style="width:70px; margin-left:2px;" maxlength="10">
			</td>
		</tr>
		<tr>
			<th>등급/구분</th>
			<td colspan="5">
				<select id="cboResourceLvl" name="cboResourceLvl" class="clsObjData" style="width:auto;">
					<option value="">-</option>
					<option value="1">1등급</option>
					<option value="2">2등급</option>
					<option value="3">3등급</option>
					<option value="9">일반</option>
				</select> /
				<select id="cboResourceGbn" name="cboResourceGbn" class="clsObjData" style="width:auto;">
					<option value="">-</option>
					<option value="3">기초</option>
					<option value="2">의료</option>
					<option value="4">경감</option>
					<option value="1">일반</option>
				</select>
			</td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th rowspan="3">수급자<br>담당자정보</th>
			<th>성명</th>
			<td colspan="5">
				<input id="txtResourcePicNm" name="txtResourcePicNm" type="text" value="" class="clsObjData" style="width:70px;">
			</td>
		</tr>
		<tr>
			<th>연락처</th>
			<td colspan="5">
				<input id="txtResourceTelno" name="txtResourceTelno" type="text" value="" class="clsObjData phone">
			</td>
		</tr>
	</tbody>
</table>

<?
	/*********************************************************

		추천인

	*********************************************************/
	if ($view_type == 'read'){
	}else{
		if ($debug){
			echo '<table class=\'my_table my_border_blue\' style=\'width:100%; border-top:none;'.($lbTestMode ? '' : 'border-bottom:none;').'\'>
					<colgroup>
						<col width=\'80px\'>
						<col width=\'80px\'>
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class=\' bottom\' rowspan=\'3\'>추천인</th>
							<th class=\'\'>성명</th>
							<td class=\'\'><input id=\'recomNm_'.$__CURRENT_SVC_CD__.'\' name=\'recomNm_'.$__CURRENT_SVC_CD__.'\' type=\'text\' value=\'\' class="clsObjData"></td>
						</tr>
						<tr>
							<th class=\'\'>연락처</th>
							<td class=\'\'><input id=\'recomTel_'.$__CURRENT_SVC_CD__.'\' name=\'recomTel_'.$__CURRENT_SVC_CD__.'\' type=\'text\' value=\'\' class=\'phone clsObjData\'></td>
						</tr>
						<tr>
							<th class=\'bottom\'>금액</th>
							<td class=\'bottom\'><input id=\'recomAmt_'.$__CURRENT_SVC_CD__.'\' name=\'recomAmt_'.$__CURRENT_SVC_CD__.'\' type=\'text\' value=\'\' class=\'number clsObjData\' style=\'width:70px;\'></td>
						</tr>
					</tbody>
				  </table>';
		}
	}
?>
<script type="text/javascript">
	function lfLoadOther(svcCd, svcId){
		$.ajax({
			type: 'POST',
			url : './client_breakdown.php',
			data: {
				code   : $('#code').val()
			,	jumin  : $('#jumin').val()
			,	svcCd  : svcCd
			,   seq    : $('#svcSeq_'+svcId).attr('value')
			,	mode   : 71
			},
			beforeSend: function (){
			},
			success: function (result){
				var val = __parseStr(result);

				$('#svcCost_'+svcId).val(__num2str(val['cost']));
				$('#txtResourceCd').val(val['orgNo']);
				$('#txtResourceNm').val(val['orgNm']);
				$('#txtResourceNo').val(val['no']);
				$('#cboResourceLvl').val(val['lvl']);
				$('#cboResourceGbn').val(val['gbn']);
				$('#txtResourcePicNm').val(val['picnm']);
				$('#txtResourceTelno').val(val['telno']);
			},
			error: function (){
			}
		}).responseXML;
	}
</script>

<script type="text/javascript">
	$(document).ready(function(){
		try{
			lfLoadOther('<?=$__CURRENT_SVC_CD__;?>','<?=$__CURRENT_SVC_ID__;?>');
		}catch(e){
		}

		if ('<?=$careResourceYn;?>' != 'Y'){
			//$('#tbodySupport').hide();
			//$('#txtResourceCd').css('background-color','#efefef').attr('disabled',true);
			//$('#txtResourceNm').css('background-color','#efefef').attr('disabled',true);
			//$('#txtResourceNo').css('background-color','#efefef').attr('disabled',true);
			//$('#cboResourceLvl').css('background-color','#efefef').attr('disabled',true);
			//$('#cboResourceGbn').css('background-color','#efefef').attr('disabled',true);
			//$('#txtResourcePicNm').css('background-color','#efefef').attr('disabled',true);
			//$('#txtResourceTelno').css('background-color','#efefef').attr('disabled',true);

			//$('#txtResourceCd').attr('readonly',true);
			//$('#txtResourceNm').attr('readonly',true);
			//$('#txtResourceNo').attr('readonly',true);
			//$('#cboResourceLvl').attr('readonly',true);
			//$('#cboResourceGbn').attr('readonly',true);
		}
	});
</script>