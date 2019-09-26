<?
	include_once('../inc/_login.php');

	if ($myF->_self() == 'mem_reg'){
		$today = Date('Ymd');
		$sql = 'SELECT	*
				FROM	mem_insu
				WHERE	org_no	 = \''.$code.'\'
				AND		jumin	 = \''.$jumin.'\'
				AND		from_dt <= \''.$today.'\'
				AND		to_dt	>= \''.$today.'\'';

		$tmp = $conn->get_array($sql);

		if (!$tmp){
			$sql = 'SELECT	*
					FROM	mem_insu
					WHERE	org_no	 = \''.$code.'\'
					AND		jumin	 = \''.$jumin.'\'
					ORDER	BY seq DESC
					LIMIT	1';

			$tmp = $conn->get_array($sql);
		}
	}?>

<table class="my_table my_border_blue">
	<colgroup>
		<col width="60px">
		<col width="30px">
		<col width="60px">
		<col width="30px">
	</colgroup>
	<thead>
		<tr>
			<th class="head bold" colspan="4">급여공통항목</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>국민연금</th>
			<td class="center"><span id="lblInsuAnnuityYn"><?=$tmp['annuity_yn'];?></span></td>
			<th>건강보험</th>
			<td class="center"><span id="lblInsuHealthYn"><?=$tmp['health_yn'];?></span></td>
		</tr>
		<tr>
			<th>고용보험</th>
			<td class="center"><span id="lblInsuEmployYn"><?=$tmp['employ_yn']=='O'? '사' : $tmp['employ_yn'];?></span></td>
			<th>산재보험</th>
			<td class="center"><span id="lblInsuSanjeYn"><?=$tmp['sanje_yn'];?></span></td>
		</tr>
		<tr>
			<th>원천징수</th>
			<td class="left" colspan="3"><span id="lblInsuPAYEYn"><?=$tmp['paye_yn'];?></span></td>
		</tr>
		<tr>
			<th>적용일</th>
			<td class="left" colspan="2"><span id="lblInsuFrom"><?=$myF->dateStyle($tmp['from_dt'],'.');?></span></td>
			<td class="center" rowspan="2"><?
				if ($myF->_self() == 'mem_reg'){?>
					<button type="button" onclick="_memInsuHis($(':input[name=\'ssn\']').attr('value'));" style="width:25px; line-height:1.3em; padding-top:3px;">변<br>경</button><?
				}else{?>
					<button type="button" onclick="_memInsuHis($('#jumin').attr('value'));" style="width:25px; line-height:1.3em; padding-top:3px;">변<br>경</button><?
				}?>
			</td>
		</tr>
		<tr>
			<th>종료일</th>
			<td class="left" colspan="2"><span id="lblInsuTo"><?=$myF->dateStyle($tmp['to_dt'],'.');?></span></td>
		</tr>
	</tbody>
</table><?

Unset($tmp);

if ($myF->_self() == 'mem_reg'){
	$yymm = Date('Ym');
	$sql = 'SELECT	yymm
			,		monthly
			FROM	mem_insu_monthly
			WHERE	org_no	 = \''.$code.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		yymm	<= \''.$yymm.'\'
			ORDER	BY yymm DESC
			LIMIT	1';

	$tmp = $conn->get_array($sql);


	if (!$tmp){
		$sql = 'SELECT	yymm
				,		monthly
				FROM	mem_insu_monthly
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				ORDER	BY yymm DESC
				LIMIT	1';

		$tmp = $conn->get_array($sql);
	}
}

if ($_SESSION['userLevel'] == 'C' || $code == '31138000044'){
?>
<table class="my_table my_border_blue" style="margin-top:10px;">
	<colgroup>
		<col width="60px">
		<col width="60px">
		<col width="55px">
	</colgroup>
	<thead>
		<tr>
			<th class="head bold" colspan="4">급여보수신고급여</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>금액</th>
			<td class="left"><span id="lblInsuMonthly"><?=Number_Format($tmp['monthly']);?></span></td>
			<td class="left" rowspan="2"><?
				if ($myF->_self() == 'mem_reg'){?>
					<span class="btn_pack m"><button type="button" onclick="_memInsuHisMonthly($(':input[name=\'ssn\']').attr('value'));">변경</button></span><?
				}else{?>
					<span class="btn_pack m"><button type="button" onclick="_memInsuHisMonthly($('#jumin').attr('value'));">변경</button></span><?
				}?>
			</td>
		</tr>
		<tr>
			<th>기준년월</th>
			<td class="left"><span id="lblInsuYYMM"><?=SubStr($tmp['yymm'],0,4).'.'.SubStr($tmp['yymm'],4);?></span></td>
		</tr>
	</tbody>
</table><?

}

Unset($tmp);
?>