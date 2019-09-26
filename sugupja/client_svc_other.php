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
?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; <?=$view_type == 'read' ? '' : 'border-bottom:none;';?>">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
	<?
		if ($__CURRENT_SVC_ID__ == 31){?>
			<tr>
				<th>서비스 구분</th>
				<td><?
				if ($view_type == 'read'){
					switch($client['gbn']){
						case '1':
							$tmp = '단태아';
							break;
						case '2':
							$tmp = '쌍태아';
							break;
						case '3':
							$tmp = '삼태아';
							break;
					}?>
					<div class="left"><?=$tmp;?></div><?
				}else{?>
					<input id="svcGbn_1_<?=$__CURRENT_SVC_ID__;?>" name="svcGbn_<?=$__CURRENT_SVC_ID__;?>" type="radio" value="1" class="radio clsObjData"><label for="svcGbn_1_<?=$__CURRENT_SVC_ID__;?>">단태아</label>
					<input id="svcGbn_2_<?=$__CURRENT_SVC_ID__;?>" name="svcGbn_<?=$__CURRENT_SVC_ID__;?>" type="radio" value="2" class="radio clsObjData"><label for="svcGbn_2_<?=$__CURRENT_SVC_ID__;?>">쌍태아</label>
					<input id="svcGbn_3_<?=$__CURRENT_SVC_ID__;?>" name="svcGbn_<?=$__CURRENT_SVC_ID__;?>" type="radio" value="3" class="radio clsObjData"><label for="svcGbn_3_<?=$__CURRENT_SVC_ID__;?>">삼태아</label><?
				}?>
				</td>
			</tr><?
		}?>
		<tr>
			<th><?=($__CURRENT_SVC_ID__ == 31 ? '서비스금액' : '서비스단가');?></th><?
			if ($view_type == 'read'){?>
				<td><div class="left"><?=number_format($client['amt1']).$str_svc_type;?></div></td><?
			}else{?>
				<td><?
				if ($__CURRENT_SVC_ID__ == 31){?>
					<input id="svcCost_<?=$__CURRENT_SVC_ID__;?>" name="svcCost_<?=$__CURRENT_SVC_ID__;?>" type="text" value="0" class="number clsSvcAmt31 clsObjData" style="width:70px;">원 X
					<input id="svcCnt_<?=$__CURRENT_SVC_ID__;?>" name="svcCnt_<?=$__CURRENT_SVC_ID__;?>" type="text" value="0" class="number clsSvcAmt31 clsObjData" style="width:50px;">일 =
					<span id="svcAmt_<?=$__CURRENT_SVC_ID__;?>" value="0" class="bold" style="color:#0000ff;">0</span>원<?
				}else{?>
					<input id="svcCost_<?=$__CURRENT_SVC_ID__;?>" name="svcCost_<?=$__CURRENT_SVC_ID__;?>" type="text" value="0" class="number clsObjData" style="width:70px;"><?
				}?>
				</td><?
			}?>
		</tr>
	</tbody>
</table>

<?
	if ($__CURRENT_SVC_ID__ == 31){
		include('./client_reg_sub_staff.php');
		include('./client_reg_sub_baby_addpay.php');
	}


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
	function lfInitOther(svcCd, svcId){
		$('.clsSvcAmt31').change(function(){
			lfCalOther(svcCd, svcId);
		});
	}
	function lfCalOther(svcCd, svcId){
		var cost = __str2num($('#svcCost_31').val());
		var cnt  = __str2num($('#svcCnt_31').val());
		var amt  = cost * cnt;

		$('#svcAmt_31').attr('value', amt).text(__num2str(amt));
	}
	function lfLoadOther(svcCd, svcId){
		$.ajax({
			type: 'POST',
			url : './client_breakdown.php',
			data: {
				code   : $('#code').val()
			,	jumin  : $('#jumin').val()
			,	svcCd  : svcCd
			,   seq    : $('#svcSeq_'+svcId).attr('value')
			,	mode   : 99
			},
			beforeSend: function (){
			},
			success: function (result){
				var val = __parseStr(result);

				$('#svcCost_'+svcId).val(__num2str(val['cost']));

				$('#recomNm_'+svcCd).val(val['recomNm']);
				$('#recomTel_'+svcCd).val(__getPhoneNo(val['recomTel']));
				$('#recomAmt_'+svcCd).val(__num2str(val['recomAmt']));

				if (svcId == '31'){
					$('#svcCnt_'+svcId).val(val['cnt']);
					$('input:radio[name="svcGbn_'+svcId+'"]:input[value="'+val['val']+'"]').attr('checked',true);

					lfCalOther(svcCd, svcId);
				}
			},
			error: function (){
			}
		}).responseXML;
	}
</script>
<?
	if ($__CURRENT_SVC_ID__ == '31'){?>
		<script type="text/javascript">
			$(document).ready(function(){
				try{
					lfInitOther('<?=$__CURRENT_SVC_CD__;?>','<?=$__CURRENT_SVC_ID__;?>');
					lfLoadOther('<?=$__CURRENT_SVC_CD__;?>','<?=$__CURRENT_SVC_ID__;?>');
				}catch(e){
				}
			});
		</script><?
	}else{?>
		<script type="text/javascript">
			$(document).ready(function(){
				try{
					lfLoadOther('<?=$__CURRENT_SVC_CD__;?>','<?=$__CURRENT_SVC_ID__;?>');
				}catch(e){
				}
			});
		</script><?
	}
?>