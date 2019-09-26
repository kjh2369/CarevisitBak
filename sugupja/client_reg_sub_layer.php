<?
	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	##################################################################
	#
	# 이용서비스 변경시 변경 내역에 남길 적용일자를 받는다.
	#
	##################################################################

	$sql = "select m03_sdate
			,      m03_edate
			  from m03sugupja
			 where m03_ccode  = '$code'
			   and m03_mkind  = '$__CURRENT_SVC_CD__'
			   and m03_jumin  = '$jumin'
			   and m03_del_yn = 'N'";

	$stnd_dt  = $conn->get_data($sql);

	switch($__CURRENT_SVC_ID__){
		case 11:
			$txt      = '수급상태, 계약기간, 담당요양보호사, 장기요양등급, 수급자구분, 청구한도금액, 본인부담율이 변경된 경우 적용기준일자를 입력하여 주십시오.';
			break;
		case 21:
			$txt      = '이용상태, 계약기간, 서비스시간, 소득등급이 변경된 경우 적용기준일자를 입력하여 주십시오.';
			break;
		case 22:
			$txt      = '이용상태, 계약기간, 서비스구분, 서비스시간, 소득등급, 이월시간이 변경된 경우 적용기준일자를 입력하여 주십시오.';
			break;
		case 23:
			$txt      = '이용상태, 계약기간, 서비스구분, 서비스시간, 소득등급이 변경된 경우 적용기준일자를 입력하여 주십시오.';
			break;
		case 24:
			$txt      = '이용상태, 계약기간, 나이등급, 장애인정등급, 특례구분, 소득등급, 이월시간이 변경된 경우 적용기준일자를 입력하여 주십시오.';
			break;
		default:
			$txt      = '이용상태, 계약기간, 서비스단가, 서비스시간이 변경된 경우 적용기준일자를 입력하여 주십시오.';
	}

	// 최초 일정등록일
	$sql = "select ifnull(min(t01_sugup_date), '99991231')
			,      ifnull(max(t01_sugup_date), '00000101')
			  from t01iljung
			 where t01_ccode  = '$code'
			   and t01_mkind  = '$__CURRENT_SVC_CD__'
			   and t01_jumin  = '$jumin'
			   and t01_del_yn = 'N'";
	$tmp = $conn->get_array($sql);

	$first_dt = $tmp[0];
	$last_dt  = $tmp[1];
?>
<div id="layer_body_<?=$__CURRENT_SVC_ID__;?>" style="z-index:1000; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:#cccccc; position:absolute; height:100px; width:100px;"></div>
<div id="layer_cont_<?=$__CURRENT_SVC_ID__;?>" style="z-index:1001; position:absolute; border:2px solid #ff0000; text-align:center; padding-top:30px;">
	<table style="width:80%; border:2px solid #666666;">
		<colgroup>
			<col width="25%" span="4">
		</colgroup>
		<tbody>
			<tr>
				<td style="background-color:#eeeeee; font-weight:bold;" colspan="4">
					<div style="width:50%; float:left; text-align:left; padding-left:5px;">적용일자안내</div>
					<div style="width:100%; text-align:right; padding-right:5px;"><a id="<?=$__CURRENT_SVC_ID__;?>_layerClose" href="#" onclick="hidden_layer('<?=$__CURRENT_SVC_ID__;?>'); return false;" style="font-weight:bold; font-size:18px;">X</a></div>
				</td>
			</tr>
			<tr>
				<td style="background-color:#ffffff;" colspan="4"><p style="text-align:justify; padding:5px; line-height:1.2em; font-weight:bold;"><?=$txt;?></p></td>
			</tr>
			<tr>
				<td style="background-color:#eeeeee;">적용기준일</td>
				<td style="background-color:#ffffff; font-weight:bold;"><?=$myF->dateStyle($stnd_dt);?></td>
				<td style="background-color:#eeeeee;">적용시작일</td>
				<td style="background-color:#ffffff;"><input name="<?=$__CURRENT_SVC_ID__;?>_startDt" type="text" value="<?=$myF->dateStyle($stnd_dt);?>" tag="<?=$stnd_dt;?>" maxlength="8" class="date"></td>
			</tr>
			<tr>
				<td style="background-color:#ffffff;" colspan="4">
					<div id="<?=$__CURRENT_SVC_ID__;?>_layerBtn">
						<a href="#" onclick="client_save_layer('<?=$__CURRENT_SVC_ID__;?>'); return false;">저장</a> |
						<a href="#" onclick="hidden_layer('<?=$__CURRENT_SVC_ID__;?>'); return false;">취소</a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<input name="<?=$__CURRENT_SVC_ID__;?>_historyYn" type="hidden" value="Y">
<input name="<?=$__CURRENT_SVC_ID__;?>_startDtate" type="hidden" value="<?=$stnd_dt;?>" tag="<?=$stnd_dt;?>">
<input name="<?=$__CURRENT_SVC_ID__;?>_memChangeDt" type="hidden" value="<?=$stnd_dt;?>">
<input id="<?=$__CURRENT_SVC_ID__;?>_firstDt" name="<?=$__CURRENT_SVC_ID__;?>_firstDt" type="hidden" value="<?=$first_dt;?>">
<input id="<?=$__CURRENT_SVC_ID__;?>_lastDt" name="<?=$__CURRENT_SVC_ID__;?>_lastDt" type="hidden" value="<?=$last_dt;?>">