<?
	/**************************************************
	
		추가 요금 등록
		
		svc_id
		
		11 : 재가요양
		21 ~ 24 : 바우처
		31 ~ 33 : 기타유료
	
	**************************************************/
	
	if ($svc_id == 23 || $svc_id == 31){
		/**************************************************
		
			query
		
		**************************************************/
		$sql = 'select svc_seq
				,      school_not_cnt
				,      school_not_pay
				,      school_cnt
				,      school_pay
				,      family_cnt
				,      family_pay
				,      home_in_yn
				,      home_in_pay
				,      holiday_pay
				  from client_svc_addpay  
				 where org_no   = \''.$code.'\'
				   and svc_kind = \''.$svc_cd.'\'
				   and svc_ssn  = \''.$jumin.'\'
				   and del_flag = \'N\'';
				   
		$addpay_if  = $conn->get_array($sql);
		$addpay_tot = ($addpay_if['school_not_cnt'] * $addpay_if['school_not_pay'])
					+ ($addpay_if['school_cnt'] * $addpay_if['school_pay'])
					+ ($addpay_if['family_cnt'] * $addpay_if['family_pay'])
					+ $addpay_if['home_in_pay']
					+ $addpay_if['holiday_pay'];
		
		echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.';\'>
				<colgroup>
					<col width=\'69px\' span=\'12\'>
					<col>
				<colgroup>
				<thead>
					<tr>
						<th class=\'head bold\' colspan=\'13\'>'.$kind_nm.' 추가 요금 등록</th>
					</tr>
					<tr>
						<th class=\'head\' style=\''.__BORDER_T__.__BORDER_R__.'\' colspan=\'3\'>미취학아동</th>
						<th class=\'head\' style=\''.__BORDER_T__.__BORDER_R__.'\' colspan=\'3\'>취학아동</th>
						<th class=\'head\' style=\''.__BORDER_T__.__BORDER_R__.'\' colspan=\'3\'>동거가족</th>
						<th class=\'head\' style=\''.__BORDER_T__.__BORDER_R__.'\' colspan=\'2\'>입주</th>
						<th class=\'head\' style=\''.__BORDER_T__.__BORDER_R__.'\' rowspan=\'2\'>공/휴일<br>추가요금</th>
						<th class=\'head\' style=\''.__BORDER_T__.__BORDER_R__.'\' rowspan=\'2\'>합계</th>
					</tr>
					<tr>
						<th class=\'head\'>아동수</th>
						<th class=\'head\'>단가</th>
						<th class=\'head\' style=\''.__BORDER_R__.'\'>추가요금</th>
						<th class=\'head\'>아동수</th>
						<th class=\'head\'>단가</th>
						<th class=\'head\' style=\''.__BORDER_R__.'\'>추가요금</th>
						<th class=\'head\'>가족수</th>
						<th class=\'head\'>단가</th>
						<th class=\'head\' style=\''.__BORDER_R__.'\'>추가요금</th>
						<th class=\'head\'>입주여부</th>
						<th class=\'head\' style=\''.__BORDER_R__.'\'>추가요금</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class=\'center\'>
							<input name=\'school_not_cnt\' type=\'text\' value=\''.number_format($addpay_if['school_not_cnt']).'\' class=\'number\' style=\'width:100%;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_set_object_tot("school_not");\'>
						</td>
						<td class=\'center\'>
							<input name=\'school_not_pay\' type=\'text\' value=\''.number_format($addpay_if['school_not_pay']).'\' class=\'number\' style=\'width:100%;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_set_object_tot("school_not");\'>
						</td>
						<td class=\'center\' style=\''.__BORDER_R__.'\'>
							<input name=\'school_not_tot\' type=\'text\' value=\''.number_format($addpay_if['school_not_cnt'] * $addpay_if['school_not_pay']).'\' class=\'number\' style=\'width:100%; background-color:#efefef; cursor:default;\' onfocus=\'this.blur();\' readonly>
						</td>
						<td class=\'center\'>
							<input name=\'school_cnt\' type=\'text\' value=\''.number_format($addpay_if['school_cnt']).'\' class=\'number\' style=\'width:100%;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_set_object_tot("school");\'>
						</td>
						<td class=\'center\'>
							<input name=\'school_pay\' type=\'text\' value=\''.number_format($addpay_if['school_pay']).'\' class=\'number\' style=\'width:100%;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_set_object_tot("school");\'>
						</td>
						<td class=\'center\' style=\''.__BORDER_R__.'\'>
							<input name=\'school_tot\' type=\'text\' value=\''.number_format($addpay_if['school_cnt'] * $addpay_if['school_pay']).'\' class=\'number\' style=\'width:100%; background-color:#efefef; cursor:default;\' onfocus=\'this.blur();\' readonly>
						</td>
						<td class=\'center\'>
							<input name=\'family_cnt\' type=\'text\' value=\''.number_format($addpay_if['family_cnt']).'\' class=\'number\' style=\'width:100%;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_set_object_tot("family");\'>
						</td>
						<td class=\'center\'>
							<input name=\'family_pay\' type=\'text\' value=\''.number_format($addpay_if['family_pay']).'\' class=\'number\' style=\'width:100%;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_set_object_tot("family");\'>
						</td>
						<td class=\'center\' style=\''.__BORDER_R__.'\'>
							<input name=\'family_tot\' type=\'text\' value=\''.number_format($addpay_if['family_cnt'] * $addpay_if['family_pay']).'\' class=\'number\' style=\'width:100%; background-color:#efefef; cursor:default;\' onfocus=\'this.blur();\' readonly>
						</td>
						<td class=\'center\'><div class=\'left\'>
							<input name=\'home_in_yn\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' onclick=\'__object_enabled(document.getElementById("home_in_pay"), this.checked);\' '.($addpay_if['home_in_yn'] == 'Y' ? 'checked' : '').'>입주</div>
						</td>
						<td class=\'center\' style=\''.__BORDER_R__.'\'>
							<input name=\'home_in_pay\' type=\'text\' value=\''.number_format($addpay_if['home_in_pay']).'\' class=\'number\' style=\'width:100%;'.($addpay_if['home_in_yn'] != 'Y' ? 'background-color:#efefef;' : '').'\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_set_addpay_tot();\' '.($addpay_if['home_in_yn'] != 'Y' ? 'disabled=true' : '').'>
						</td>
						<td class=\'center\' style=\''.__BORDER_R__.'\'>
							<input name=\'holiday_pay\' type=\'text\' value=\''.number_format($addpay_if['holiday_pay']).'\' class=\'number\' style=\'width:100%;\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' onchange=\'_set_addpay_tot();\'>
						</td>
						<td class=\'center\'>
							<div id=\'addpay_tot\' class=\'right\'>'.number_format($addpay_tot).'</div>
						</td>
					</tr>
				</tbody>
			  </table>';
			  
		unset($addpay_if);
	}
?>