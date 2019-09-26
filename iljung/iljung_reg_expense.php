<?
	/**************************************************

		비급여 실비처리

	**************************************************/
	$sql = 'select m03_bipay1 as bipay1
			,      m03_bipay2 as bipay2
			,      m03_bipay3 as bipay3
			,      m03_expense_yn as exp_yn
			,      m03_expense_pay as exp_pay
			  from m03sugupja
			 where m03_ccode = \''.$code.'\'
			   and m03_mkind = \''.$svc_cd.'\'
			   and m03_jumin = \''.$jumin.'\'';

	$bipay_if = $conn->get_array($sql);

	echo '<div id=\'divExpenseBody\' style=\'display:none;\'>';
	echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.';\'>
			<colgroup>
				<col width=\'60px\'>
				<col width=\'80px\'>
				<col width=\'80px\'>
				<col width=\'100px\'>';

	if (($svc_id > 10 && $svc_id < 20) ||
		($svc_id == 24)){
		echo '	<col width=\'100px\'>
				<col width=\'100px\'>';
	}

	echo '		<col width=\'130px\'>
				<col width=\'80px\'>
				<col>
			<colgroup>
			<thead>
				<tr>
					<th class=\'head bold\' colspan=\'9\'>비급여 실비처리구분</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th class=\'head\' rowspan=\'4\'>적용<br>수가</th>
					<td colspan=\'8\'>
						<input id=\'bipayKind1\' name=\'bipay_kind\' type=\'radio\' value=\'1\' class=\'radio\' onclick=\'_current_bipay(this.value);\'><label for=\'bipayKind1\'>공단수가</label>
						<input id=\'bipayKind2\' name=\'bipay_kind\' type=\'radio\' value=\'2\' class=\'radio\' onclick=\'_current_bipay(this.value);\'><label for=\'bipayKind2\'>기관비급여수가</label>
						<input id=\'bipayKind3\' name=\'bipay_kind\' type=\'radio\' value=\'3\' class=\'radio\' onclick=\'_current_bipay(this.value);\' checked><label for=\'bipayKind3\'>고객개별수가</label>
					</td>
				</tr>
				<tr>
					<th class=\'center\' rowspan=\'2\'>공단수가</th>
					<th class=\'center\' rowspan=\'2\'>비급여수가</th>
					<th class=\'center\' colspan=\''.(($svc_id > 10 && $svc_id < 20) || ($svc_id == 24) ? '3' : '1').'\'>고객수가</th>
					<th class=\'center\' colspan=\'3\'>&nbsp;</th>
				</tr>';

	echo '		<tr>';

	if ($svc_id > 10 && $svc_id < 20){
		echo '<th class=\'center\'>방문요양</th>
			  <th class=\'center\'>방문목욕</th>
			  <th class=\'center\'>방문간호</th>';
	}else if ($svc_id == 24){
		echo '<th class=\'center\'>활동지원</th>
			  <th class=\'center\'>방문목욕</th>
			  <th class=\'center\'>방문간호</th>';
	}else{
		echo '<th class=\'center\'>'.$kind_nm.'</th>';
	}

	echo '			<th class=\'center\'>비급여 실비지급여부</th>
					<th class=\'center\'>실비지급금액</th>
					<th class=\'center last\'>비고</th>
				</tr>
				<tr>
					<td class=\'center\'><input name=\'bipay_cost_publid\' type=\'text\' value=\'0\' class=\'number\' style=\'width:100%; background-color:#efefef; cursor:default;\' onfocus=\'this.blur();\' readonly></td>
					<td class=\'center\'><input name=\'bipay_cost_private\' type=\'text\' value=\'0\' class=\'number\' style=\'width:100%; background-color:#efefef; cursor:default;\' onfocus=\'this.blur();\' readonly></td>
					<td class=\'right\'><input name=\'bipay_cost1\' type=\'text\' value=\''.number_format($bipay_if['bipay1']).'\' class=\'number\' style=\'width:'.($svc_id == 23 || $svc_id == 31 ? '63' : '50').'px;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_chk_expense_max(this);\'> / '.($svc_id == 23 || $svc_id == 31 ? '일' : '시간').'</td>';

	if (($svc_id > 10 && $svc_id < 20) ||
		($svc_id == 24)){
		echo '<td class=\'right\'><input name=\'bipay_cost2\' type=\'text\' value=\''.number_format($bipay_if['bipay2']).'\' class=\'number\' style=\'width:50px;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_chk_expense_max(this);\'> / 횟수</td>
			  <td class=\'right\'><input name=\'bipay_cost3\' type=\'text\' value=\''.number_format($bipay_if['bipay3']).'\' class=\'number\' style=\'width:50px;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_chk_expense_max(this);\'> / 횟수</td>';
	}else{
		echo '<input name=\'bipay_cost2\' type=\'hidden\' value=\'0\'>
			  <input name=\'bipay_cost3\' type=\'hidden\' value=\'0\'>';
	}

	echo '			<td class=\'left\'>
						<input name=\'exp_yn\' type=\'radio\' value=\'Y\' class=\'radio\' onclick=\'_set_expense_yn();\' '.($bipay_if['exp_yn'] == 'Y' ? 'checked' : '').'>예
						<input name=\'exp_yn\' type=\'radio\' value=\'N\' class=\'radio\' onclick=\'_set_expense_yn();\' '.($bipay_if['exp_yn'] != 'Y' ? 'checked' : '').'>아니오
					</td>
					<td class=\'\'><input name=\'exp_pay\' type=\'text\' value=\''.number_format($bipay_if['exp_pay']).'\' class=\'number\' style=\'width:80px;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_chk_expense_pay();\'></td>
					<td class=\'left last\'>&nbsp;</td>
				</tr>';

	echo '	<tbody>
		  </table>';

	echo '<input name=\'exp_max_pay\' type=\'hidden\' value=\''.$bipay_if['exp_pay'].'\'>';
	echo '</div>';




	/*
	 * 2011.11.02이전
	echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.';\'>
			<colgroup>
				<col width=\'100px\'>';

	if ($svc_id > 10 && $svc_id < 20){
		echo '	<col width=\'100px\'>
				<col width=\'100px\'>';
	}

	echo '		<col width=\'130px\'>
				<col width=\'80px\'>
				<col>
			<colgroup>
			<thead>
				<tr>
					<th class=\'head bold\' colspan=\'8\'>비급여 실비처리구분</th>
				</tr>
			</thead>
			<tbody>
				<tr>';

	if ($svc_id > 10 && $svc_id < 20){
		echo '<th class=\'center\'>방문요양</th>
			  <th class=\'center\'>방문목욕</th>
			  <th class=\'center\'>방문간호</th>';
	}else{
		echo '<th class=\'center\'>'.$kind_nm.'</th>';
	}

	echo '			<th class=\'center\'>비급여 실비지급여부</th>
					<th class=\'center\'>실비지급금액</th>
					<th class=\'center last\'>비고</th>
				</tr>
				<tr>
					<td class=\'right\'><input name=\'bipay_cost1\' type=\'text\' value=\''.number_format($bipay_if['bipay1']).'\' class=\'number\' style=\'width:50px;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_chk_expense_max(this);\'> / 시간</td>';

	if ($svc_id > 10 && $svc_id < 20){
		echo '<td class=\'right\'><input name=\'bipay_cost2\' type=\'text\' value=\''.number_format($bipay_if['bipay2']).'\' class=\'number\' style=\'width:50px;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_chk_expense_max(this);\'> / 횟수</td>
			  <td class=\'right\'><input name=\'bipay_cost3\' type=\'text\' value=\''.number_format($bipay_if['bipay3']).'\' class=\'number\' style=\'width:50px;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_chk_expense_max(this);\'> / 횟수</td>';
	}else{
		echo '<input name=\'bipay_cost2\' type=\'hidden\' value=\'0\'>
			  <input name=\'bipay_cost3\' type=\'hidden\' value=\'0\'>';
	}

	echo '			<td class=\'left\'>
						<input name=\'exp_yn\' type=\'radio\' value=\'Y\' class=\'radio\' onclick=\'_set_expense_yn();\' '.($bipay_if['exp_yn'] == 'Y' ? 'checked' : '').'>예
						<input name=\'exp_yn\' type=\'radio\' value=\'N\' class=\'radio\' onclick=\'_set_expense_yn();\' '.($bipay_if['exp_yn'] != 'Y' ? 'checked' : '').'>아니오
					</td>
					<td class=\'\'><input name=\'exp_pay\' type=\'text\' value=\''.number_format($bipay_if['exp_pay']).'\' class=\'number\' style=\'width:80px;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_chk_expense_pay();\'></td>
					<td class=\'left last\'>&nbsp;</td>
				</tr>';

	echo '	<tbody>
		  </table>';

	echo '<input name=\'exp_max_pay\' type=\'hidden\' value=\''.$bipay_if['exp_pay'].'\'>';
	*/

	unset($bipay_pay);
?>