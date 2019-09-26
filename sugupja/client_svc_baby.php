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
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col width="55px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th rowspan="2">서비스구분</th>
			<th>구분</th>
			<td class="left">
				<div id="babyVal" value="" class="clsData" style="float:left; width:auto;"></div>
				<div id="babyTm" value="" class="clsData" style="float:left; width:auto;"></div>
				<div id="babySeq" value="" class="clsData" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td class="left">
				<div style="float:left; width:auto; margin-right:5px;"><span id="babyFrom" value="" class="clsData"></span><span id="babyTo" value="" class="clsData"></span></div>
				<div style="float:left; width:auto;"><?
					if ($view_type != 'read'){?>
						<span class="btn_pack m"><button type="button" onclick="_clientBabyShow('<?=$__CURRENT_SVC_ID__;?>','<?=$__CURRENT_SVC_CD__;?>')">변경</button></span><?
					}?>
				</div>
			</td>
		</tr>
		<tr>
			<th rowspan="2">소득등급</th>
			<th>등급</th>
			<td class="left">
				<div id="babyLvl" value="" class="clsData"></div>
				<div id="babyLvlSeq" value="" class="clsData" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td class="left">
				<div style="float:left; width:auto; margin-right:5px;"><span id="babyLvlFrom" value="" class="clsData"></span><span id="babyLvlTo" value="" class="clsData"></span></div>
				<div style="float:left; width:auto;"><?
					if ($view_type != 'read'){?>
						<span class="btn_pack m"><button type="button" onclick="_clientLvlShow('<?=$__CURRENT_SVC_ID__;?>','<?=$__CURRENT_SVC_CD__;?>')">변경</button></span><?
					}?>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; <?=$view_type == 'read' ? '' : 'border-bottom:none;';?>">
	<colgroup>
		<col width="80px">
		<col width="120px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>총지원금액</th>
			<td class="left"><div id="babyAmt" value="0">0</div></td>
			<th>본인부담금</th>
			<td class="left"><div id="expenseAmt_<?=$__CURRENT_SVC_ID__;?>" value="0">0</div></td>
		</tr>
	</tbody>
</table>


<?
	/*************************

		산모신생아 비급여 항목

	*************************/
	include('./client_reg_sub_baby_addpay.php');
?>