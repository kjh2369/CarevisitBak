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


	//담당관리자
	$sql = 'SELECT	team_cd
			FROM	client_his_team
			WHERE	org_no	 = \''.$code.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		svc_cd	 = \''.$__CURRENT_SVC_CD__.'\'
			AND		from_ym <= \''.$yymm.'\'
			AND		to_ym	>= \''.$yymm.'\'
			AND		del_flag = \'N\'';
	
	$teamCd = $conn->get_data($sql);

	if ($teamCd){
		$sql = 'SELECT	m02_yname
				FROM	m02yoyangsa
				WHERE	m02_ccode = \''.$code.'\'
				/*AND		m02_mkind = \''.$__CURRENT_SVC_CD__.'\'*/
				AND		m02_yjumin= \''.$teamCd.'\'';
		
		$teamNm = $conn->get_data($sql);
		$teamCd = $ed->en($teamCd);
		
	}

?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col width="55px">
		<col>
	</colgroup>
	<tbody><?
		if ($jumin){?>
			<tr>
				<th style="border-bottom:1px solid #0e69b0;" colspan="1">담당팀장</th>
				<td style="border-bottom:1px solid #0e69b0;" colspan="3"><?
					if ($view_type == 'read'){?>
						<div class="left"><?=$teamNm;?></div><?
					}else{?>
						<div id="ID_TEAM" class="left nowrap" style="float:left; width:auto;"><?=$teamNm;?></div>
						<div style="float:left; width:auto; margin-left:5px; margin-top:3px;">
							<span class="btn_pack m"><button onclick="lfTeamPop('<?=$__CURRENT_SVC_CD__;?>');">변경</button></span>
						</div><?
					}?>
				</td>
			</tr><?
		}?>
		<tr>
			<th rowspan="2">서비스시간</th>
			<th>시간</th>
			<td class="left">
				<div id="nusreVal" value="" class="clsData"></div>
				<div id="nusreSeq" value="" class="clsData" style="display:none;">
				<div id="nusreStndDt" value="" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td class="left">
				<div style="float:left; width:auto; margin-right:5px;"><span id="nusreFrom" value="" class="clsData"></span><span id="nusreTo" value="" class="clsData"></span></div>
				<div style="float:left; width:auto;"><?
					if ($view_type != 'read'){?>
						<span class="btn_pack m"><button type="button" onclick="_clientNurseShow('<?=$__CURRENT_SVC_ID__;?>','<?=$__CURRENT_SVC_CD__;?>')">변경</button></span><?
					}?>
				</div>
			</td>
		</tr>
		<tr>
			<th rowspan="2">소득등급</th>
			<th>등급</th>
			<td class="left">
				<div id="nusreLvl" value="" class="clsData"></div>
				<div id="nusreLvlSeq" value="" class="clsData" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td class="left">
				<div style="float:left; width:auto; margin-right:5px;"><span id="nusreLvlFrom" value="" class="clsData"></span><span id="nusreLvlTo" value="" class="clsData"></span></div>
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
			<td class="left"><div id="nurseAmt" value="0">0</div></td>
			<th>본인부담금</th>
			<td class="left"><div id="expenseAmt_<?=$__CURRENT_SVC_ID__;?>" value="0">0</div></td>
		</tr>
	</tbody>
</table>